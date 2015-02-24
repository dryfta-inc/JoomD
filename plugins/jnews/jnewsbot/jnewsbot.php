<?php
defined('_JEXEC') OR die('Access Denied!');
### Copyright (C) 2006-2012 Joobi Limited. All rights reserved.
### http://www.gnu.org/copyleft/gpl.html GNU/GPL


if ( strtolower( substr( JPATH_ROOT, strlen(JPATH_ROOT)-13 ) ) =='administrator' ) {
	$adminPath = strtolower( substr( JPATH_ROOT, strlen(JPATH_ROOT)-13 ) );
} else {
	$adminPath = JPATH_ROOT;
}

jimport('joomla.filesystem.file');
$mainAdminPathDefined = $adminPath . DS.'components'.DS.'com_jnews'.DS.'defines.php';
if ( JFile::exists( $mainAdminPathDefined ) ) {
	require_once( $mainAdminPathDefined );
	JApplication::registerEvent( 'jnewsbot_editabs', 'jnewsbot_content_editab' );
	JApplication::registerEvent( 'jnewsbot_transformall', 'jnewsbot_content_transformall' );
	JApplication::registerEvent( 'jnewsbot_transformall', 'jnewsbot_jcalpro_transformall' );
	
	//register the viewonline tag as an event to be executed
	JApplication::registerEvent( 'jnewsbot_transformall', 'jnewsbot_viewonline_transformall' );
	JApplication::registerEvent('jnewsbot_transformfinal', 'jnewsbot_class_transformfinal');
}


JHTML::_( 'behavior.mootools' );

function jnewsbot_content_editab($forms) {
	$siteContent = new siteContent();
//	$limit = -1;
	$limit = 5;
	
	$limittotal = $siteContent->countSiteCount();
	$setLimit = jnews::setLimitPagination($limittotal);
	
	$action = JRequest::getVar('act');
	$task = JRequest::getVar('task');
	$contentsearch = JRequest::getVar('contentsearch', '' );
	echo $forms['main'];
	$hidden = '<input type="hidden" name="option" value="'.JNEWS_OPTION.'" />';
	$hidden .= '<input type="hidden" name="limit" value="'.$limit.'" />';
	$toSearch = null;
	$toSearch->forms = '';
	$toSearch->hidden = $hidden;
	$toSearch->listsearch = $contentsearch;
	$toSearch->id = 'contentsearch';
	$app =& JFactory::getApplication();
	
	$setSort = null;
	$setSort->orderValue = $app->getUserStateFromRequest( JNEWS_OPTION.'.content.filter_order', 'filter_order', 'a.id','cmd' );
	$setSort->orderDir	= $app->getUserStateFromRequest( JNEWS_OPTION.'.content.filter_order_Dir', 'filter_order_Dir', 'desc',	'word' );
	
	$contentItems = jnewsbot_content_getitems($contentsearch, $setLimit, $setSort);
 	ob_start();
	$js = "function setContentTag(id, url, changeType){
 		 		var form = document.adminForm;
				if(!form){
					form = document.mosForm;
				}
 	 			if(id!=null){
					for (i=0;i<form.content_type.length;i++) {
						if (form.content_type[i].checked) {
							var content_type = form.content_type[i].value;
						}
					}";
//   if ( @include_once( JNEWSPATH_ADMIN . 'social' .DS. 'class.social.php' ) ) {
	if((!$GLOBALS[JNEWS.'use_tags'] AND class_exists('jNews_Social')) OR (!$GLOBALS[JNEWS.'use_tags'] AND $GLOBALS[JNEWS.'level'] > 2) )	{
		$js .="
					if(changeType!=null)
						id = document.getElementById('insertbot').value;

					template = window.top.document.getElementById('template_id');
					templateid = template.value;

					var tag =  id;
					if(id != 0)
						getContent(id, content_type, url,templateid);

					form.contenttag.value = tag;
					}else{
					var tag = form.contentreplace.value;";
				if( version_compare(JVERSION,'1.6.0','<') ){//1.5
					$js .= " if(window.top.insertTag(tag)){window.top.document.getElementById('sbox-window').close();}";
				}else{
					$js .=  " if(window.top.insertTag(tag)) window.parent.SqueezeBox.close();";
				}
		$js .= "}
 			}";

	}else{
		$js .="
			if(changeType==null)
				form.botID.value= id;

			var tag = '{contentitem:' + form.botID.value + '|' + content_type + '}';

			form.contenttag.value = tag;
			}else{

					var tag = form.contenttag.value;";
				if( version_compare(JVERSION,'1.6.0','<') ){//1.5
					$js .= " if(window.top.insertTag(tag)){window.top.document.getElementById('sbox-window').close();}";
				}else{
					$js .=  " if(window.top.insertTag(tag)) window.parent.SqueezeBox.close();";
				}
		$js .= "}
 			}";
	}

	if( version_compare(JVERSION,'1.6.0','<') ){//1.5
	$js .="

 			function getContent(id, content_type, url, templateid){
 				var ajax = new Ajax(url,
 					{data: 'artId='+id+'&content_type='+content_type+'&templateid='+templateid,
 					method: 'POST',
 					onComplete : function(result){insertContent(result, id); }
 					}
 				);
 				ajax.request();
 			}";
	}else{
		$js .="
 			function getContent(id, content_type, url,templateid){
				var ajax = new Request({
				url : url,
				data: 'artId='+id+'&content_type='+content_type+'&templateid='+templateid,
				method: 'POST',
				onComplete : function(result){insertContent(result, id); }
				});
				ajax.send();
 			}";
	}


 	$js .=		"
 			function insertContent(html, id){
				var form = document.adminForm;
				if(!form){
					form = document.mosForm;
				}

				var root = document.createElement('div');

				root.innerHTML = html;
				var body = document.getElementsByTagName('body')[0].appendChild(root);

				root.setAttribute(\"style\", \"width:150px; display:none\");
				var element = document.getElementById('artcontent_'+id);

				form.contentreplace.value = element.innerHTML;

				document.getElementsByTagName('body')[0].removeChild(root);
			}
			";

		$url = jNews_Tools::completeLink( 'option='.JNEWS_OPTION.'&act=mailing&task=articleContent', false, false, true );

$doc =& JFactory::getDocument();
$doc->addScriptDeclaration($js);

?>

<style type="text/css">
	table.smartcontent {
		border: 1px solid #D5D5D5;
		background-color: #F6F6F6;
		width: 100%;
		margin-bottom: 10px;
		-moz-border-radius:3px;
 		-webkit-border-radius:3px;
 		padding: 5px;
 	}
 	table.smartcontent td.key {
 		background-color: #f6f6f6;
		text-align: left;
		width: 140px;
		color: #666;
		font-weight: bold;
		border-bottom: 1px solid #e9e9e9;
		border-right: 1px solid #e9e9e9;
 	}
 </style>
<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
<div class="m">


<!--<form name="adminForm" method="post" action="index.php?option=<?php echo JNEWS_OPTION; ?>&tmpl=component">-->
<table class="smartcontent" width="100%"">
	<tr>
		<td width="185" class="key">
			<span class="editlinktip">
			<?php
				$tip = _JNEWS_AUTONEWS_TYPE_TIPS;
				$title = _JNEWS_AUTONEWS_TYPE;
				echo jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 );
			?>
			</span>
		</td>
		<td style="vertical-align: top;">
				<span class="editlinktip">
			    <?php
				$tip =  _JNEWS_TITLE_ONLY_TIPS ;
            	$title = _JNEWS_TITLE_ONLY;
			   	$title_only = "<span class=\"editlinktip\">" . jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 ) . "</span>";

				$tip = _JNEWS_INTRO_ONLY_TIPS;
				$title =  _JNEWS_INTRO_ONLY;
				$intro_only = "<span class=\"editlinktip\">" . jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 ) . "</span>";

           		$tip =  _JNEWS_FULL_ARTICLE_TIPS;
				$title =  _JNEWS_FULL_ARTICLE ;
				$full_article = "<span class=\"editlinktip\">" . jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 ) . "</span>";
				?>
				</span>
				<span class="editlinktip">
               	<input id="content_type" type="radio" name="content_type" value="0" checked="checked" onclick="setContentTag(1,'<?php echo $url?>', 0);"/><?php echo $full_article; ?>
                <input id="content_type" type="radio" name="content_type" value="1" onclick="setContentTag(1, '<?php echo $url?>', 1);"/><?php echo $intro_only; ?>
                <input id="content_type" type="radio" name="content_type" value="2" onclick="setContentTag(1, '<?php echo $url?>', 2);"/><?php echo $title_only; ?>
                </span>

		</td>
		<td rowspan="2">
			<input onclick="setContentTag(null,'<?php echo $url; ?>')" class="inserttag" type="button" label="Insert Content" name="Insert Content" value="Insert Content"/>
		</td>
	</tr>
	 <tr>
		<td width="185" class="key">
			<span class="editlinktip">
				<?php
				$tip =  _JNEWS_CONTENT_TIP ;
            	$title = _JNEWS_CONTENT_ID ;
			   	echo jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 );
				?>
			</span>
		</td>
		<td style="vertical-align: top;">
				<!--  <input type="text" onchange="setCaptionTags();" size="60px" name="jnewstagcaption"> -->
				<input id="insertbot" type="text" size="20px" name="contenttag" value="0"/>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input id="insertbot" type="hidden" size="60px" name="contentreplace" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input id="botID" size="10px" name="botID" value="0" type="hidden"/>
		</td>
	</tr>
</table>
<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
<div class="m">
<?php
echo jnews::setTop( $toSearch, null); 
?>
<table class="joobilist" cellpadding="0" cellspacing="0">
		<tbody>
			<thead>
				<tr>
					<th class="title">
						<?php //echo _JNEWS_TAGPICKLIST_TITLE; 
						echo JHTML::_('grid.sort', _JNEWS_TAGPICKLIST_TITLE, 'a.title', $setSort->orderDir,$setSort->orderValue);
						?>
					</th>
					<th width="80px" class="title">
						<?php //echo _JNEWS_TAG_ARTICLESECTION; 
						if(version_compare(JVERSION,'1.6.0','<')){ //j15
							echo JHTML::_('grid.sort', _JNEWS_TAG_ARTICLESECTION, 'b.title', $setSort->orderDir,$setSort->orderValue);	
						}else{
							echo JHTML::_('grid.sort', _JNEWS_TAG_ARTICLESECTION, 'c.title', $setSort->orderDir,$setSort->orderValue);	
						}
						?>
					</th>
					<th width="80px" class="title">
						<?php //echo _JNEWS_TAG_ARTICLECATEGORY; 
						echo JHTML::_('grid.sort', _JNEWS_TAG_ARTICLECATEGORY, 'c.title', $setSort->orderDir,$setSort->orderValue);	
						?>
					</th>
					<th width="30px" class="title">
						<?php
						echo JHTML::_('grid.sort', 'ID', 'a.id', $setSort->orderDir,$setSort->orderValue);	
						?>
					</th>
				</tr>
			</thead>
			<?php

				if(sizeof($contentItems) > 0){
					$k = 0;
					foreach($contentItems as $contentItem){
						if (empty($contentItem->section)) $contentItem->section = JText::_('Uncategorised');
						if (empty($contentItem->category)) $contentItem->category = JText::_('Uncategorised');
						echo '<tr style="cursor:pointer" class="row'.$k.'" onclick="setContentTag(\''.$contentItem->id.'\',\''.$url.'\');" ><td>'.$contentItem->title.'</td><td nowrap="nowrap" align="center">'.$contentItem->section.'</td><td nowrap="nowrap" align="center">'.$contentItem->category.'</td><td nowrap="nowrap" align="center">'.$contentItem->id.'</td></tr>';
						//echo '<tr style="cursor:pointer" class="row'.$k.'" onclick="setContentTag(\''.$contentItem->id.'\',\''.$url.'\');" ><td>'.$contentItem->title.'</td><td nowrap="nowrap" align="center">'.$contentItem->section.'</td><td nowrap="nowrap" align="center">'.$contentItem->category.'</td><td nowrap="nowrap" align="center">'.$contentItem->id.'</td></tr>';

						$k = 1-$k;
					}
				}
			?>
		</tbody>
</table>
<?php 
	echo jnews::setPaginationBot($setLimit, 'margin:auto;');
?>
<input type="hidden" value="<?php echo JNEWS_OPTION; ?>" name="option"/>
	<input type="hidden" value="<?php echo $action; ?>" name="act"/>
	<input type="hidden" value="<?php echo $task; ?>" name="task"/>
	<input type="hidden" value="<?php echo $setSort->orderValue; ?>" name="filter_order"/>
	<input type="hidden" value="<?php echo $setSort->orderDir; ?>" name="filter_order_Dir"/>
</form>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
<?php
	 $return = ob_get_contents();
	 ob_end_clean();
	 return array( _JNEWS_CONTENT_ITEM, $return );
 }

function jnewsbot_content_getitems($contentsearch='', $setLimit=null, $setSort=null ) {

	if ( $setLimit->end < 5 ) $setLimit->end = 5;

	jimport( 'joomla.html.parameter' );
		$db=&JFactory::getDBO();
		//get the parameters from the plugin
		$plugin =& JPluginHelper::getPlugin('jnews', 'jnewsbot');
		$params = new JParameter( $plugin->params );
		//get the limit parameter
		$content_limit = $params->get('content_limit','5000');
		$displaycontent = $params->get('displaycontent','1');
		
		if(version_compare(JVERSION,'1.6.0','<')){ //j15
			$query = "SELECT a.id as id, a.title as title, b.title as section, c.title as category FROM #__content as a LEFT JOIN #__sections as b ON a.sectionid = b.id LEFT JOIN #__categories AS c ON a.catid = c.id";
			
			if (!empty($contentsearch)){
				$query .= ' WHERE a.title LIKE  \'%' . $contentsearch . '%\' AND ';
			}else{
				$query .= ' WHERE ';
			}
			
//			$query .= " WHERE ";
			if( !$displaycontent ) $query .= "a.created > '".date('Y-m-d H:i:s',time()-30240000)."' AND ";
			$query .= "  a.state=1";
//			$query .= " ORDER BY ";	
//			switch( $params->get('content_order','0') ) {
//				case '1': 
//					$query .= " a.sectionid, a.catid ASC ";	
//					break;
//				case '2': 
//					$query .= " a.created DESC ";	
//					break;
//				case '3': 
//					$query .= " a.title ASC ";	
//					break;
//				case '0':
//				default: 
//					$query .= " a.id DESC ";	
////					$query .= " a.sectionid, a.catid, a.created DESC ";	
//					break;
//			}
			
			if( !empty($setSort) ) {
				$query .= " ORDER BY $setSort->orderValue $setSort->orderDir";
			}else{
				$query .= " ORDER BY ";	
				switch( $params->get('content_order','0') ) {
					case '1': 
						$query .= " a.sectionid, a.catid ASC ";	
						break;
					case '2': 
						$query .= " a.created DESC ";	
						break;
					case '3': 
						$query .= " a.title ASC ";	
						break;
					case '0':
					default: 
						$query .= " a.id DESC ";	
	//					$query .= " a.sectionid, a.catid, a.created DESC ";	
						break;
				}
			}
			
			
			//$query .= " LIMIT ".$content_limit;
			if (!is_null($setLimit) && $setLimit->start != -1 && $setLimit->end) $query .= ' LIMIT ' . $setLimit->start . ', ' . $setLimit->end;	
			
		}else{ //j16
			
			$query = "SELECT a.id as id, a.title as title, c.title as category FROM #__content as a LEFT JOIN #__categories AS c ON a.catid = c.id";
			
			if (!empty($contentsearch)){
				$query .= ' WHERE a.title LIKE  \'%' . $contentsearch . '%\' AND ';
			}else{
				$query .= ' WHERE ';
			}
			
//			$query .= " WHERE c.extension = 'com_content' AND ";
			$query .= "  c.extension = 'com_content' AND ";
			if( !$displaycontent ) $query .= " a.created > '".date('Y-m-d H:i:s',time()-30240000)."' AND ";
			$query .= " a.state=1";
//			switch( $params->get('content_order','0') ) {
//				case '1': 
//					$query .= " a.catid ASC, c.lft ASC ";	
//					break;
//				case '2': 
//					$query .= " a.created DESC, c.lft ASC ";	
//					break;
//				case '3': 
//				case '0':
//				default: 
//					$query .= " a.id DESC ";	
////					$query .= " a.title ASC, c.lft ASC ";	
//					break;
//			}
			
			if( !empty($setSort) ) {
				$query .= " ORDER BY $setSort->orderValue $setSort->orderDir";
			}else{
				$query .= " ORDER BY ";	
				switch( $params->get('content_order','0') ) {
					case '1': 
						$query .= " a.catid ASC, c.lft ASC ";	
						break;
					case '2': 
						$query .= " a.created DESC, c.lft ASC ";	
						break;
					case '3': 
					case '0':
					default: 
						$query .= " a.id DESC ";	
	//					$query .= " a.title ASC, c.lft ASC ";	
						break;
				}
			}
			
			//$query .= " LIMIT ".$content_limit;	
			if (!is_null($setLimit) && $setLimit->start != -1 && $setLimit->end) $query .= ' LIMIT ' . $setLimit->start . ', ' . $setLimit->end;
		}

	$db->setQuery($query);
	$contentitems = $db->loadObjectList();
	return $contentitems;
	 
}


 function jnewsbot_content_transformall($html, $text, $subject, $queueInfo=null ) {
	 global $mainframe;

	 $content_items = array();
	 $contentItemsNew = array();
	 $contentItemsOld = array();
	 preg_match_all('/\{contentitem:(.{1,8})\|(.{1})\|(.{1})}/', $html, $contentItemsNew, PREG_SET_ORDER );
	 preg_match_all('/\{contentitem:(.{1,8})\|(.{1})}/', $html, $contentItemsOld, PREG_SET_ORDER );
	 $content_items = jnewsbot_class_transform_mergeArray( $contentItemsNew, $contentItemsOld );

	 foreach ($content_items as $content_item) {

		if( version_compare(JVERSION,'1.6.0','<') ) $Itemid = $mainframe->getItemId($content_item[1]);

		if(empty($Itemid)){
		 	$Itemid = $GLOBALS[JNEWS.'itemidAca'];
		}
		 
		$replacement = jnewsbot_content_getitem($content_item[1]);
		if ($GLOBALS[JNEWS.'show_author'] == 1){
		 	$author = '<br />'.$replacement->created_by_alias;
	 	} else {
	 		$author = '';
	 	}

		if ($content_item[2] == 0) {
			 $html = str_replace($content_item[0], '<div class="aca_content"><span class="aca_title">' . $replacement->title . '</span>' . "\r\n" . $author .'<br />' . $replacement->introtext . '<br />' . "\r\n" . $replacement->fulltext . "\r\n".'</div>', $html);
		 } else {

			$link = 'option=com_content&view=article&id='.$content_item[1].'&Itemid='.$Itemid;
			$link = jNews_Tools::completeLink($link,false,$GLOBALS[JNEWS.'use_sef']);

             if ($content_item[2] == 1) {
             	if(empty($replacement->fulltext) && !empty($GLOBALS[JNEWS.'word_wrap'])){
             		//Limit the number of words
             		if( strlen( $replacement->introtext ) > $GLOBALS[JNEWS.'word_wrap'] ) {
						static $requiredOnce = true;
             			if ( $requiredOnce ) {
             				require_once( JNEWSPATH_CLASS . 'content.php' );
             				$requiredOnce = false;
             			}
						$convertContent = new jNews_Content;
						$fulltext = $convertContent->HTMLtoText( $replacement->introtext, false, false );                   			
             			
//             			$fulltext = strip_tags($replacement->introtext,'<br><img>');
             			if(strlen($fulltext) > $GLOBALS[JNEWS.'word_wrap']){
	             			//We make sure we won't cut any html tag :
//	             			$open = 0;
//	             			$limitText = strlen($fulltext) - 1;
//	             			for($i=0;$i<strlen($fulltext);$i++){
//	             				if($replacement->introtext[$i] == '<'){ $open++; continue;}
//	             				if($replacement->introtext[$i] == '>'){$open--; continue;}
//	             				if($replacement->introtext[$i] == " " AND $i>$GLOBALS[JNEWS.'word_wrap'] AND $open == 0){
//	             					$limitText = $i-1;
//	             					break;
//	             				}
//	             			}
	             			$replacement->introtext = substr($fulltext , 0, $GLOBALS[JNEWS.'word_wrap']-3 ).'...';
             				
             			} else {
             				$replacement->introtext = $fulltext.'...';
             			}
             		}
             	}
			    $html = str_replace($content_item[0], '<div class="aca_content"><span class="aca_title">' . $replacement->title . '</span>' . "\r\n" . $author . '<br />' . $replacement->introtext . '<br />' . "\r\n" . '<a href="' . $link . '"><span class="aca_readmore">' . _JNEWS_READMORE . '</span></a>' . "\r\n".'</div>', $html);
             }
             else {
			    $html = str_replace($content_item[0], '<a href="' . $link . '"><span class="aca_title">' . $replacement->title . '</span></a>', $html);
             }
        }

		 $images = jnewsbot_content_getimage($replacement->images);
		 foreach($images as $image) {
			 $image_string = '<img src="' . JNEWS_JPATH_LIVE_NO_HTTPS . '/images/stories/' . $image['image'] . '" align="' . $image['align'] . '" alt="' . $image['alttext'] . '" border="' . $image['border'] . '" />';
			 $html = preg_replace('/{mosimage}/', $image_string, $html, 1);
		 }
	 }
	 
	 $content_items = array();
	 $contentItemsNew = array();
	 $contentItemsOld = array();
	 preg_match_all('/\{contentitem:(.{1,8})\|(.{1})\|(.{1})}/', $text, $contentItemsNew, PREG_SET_ORDER );
	 preg_match_all('/\{contentitem:(.{1,8})\|(.{1})}/', $text, $contentItemsOld, PREG_SET_ORDER );
	 $content_items = jnewsbot_class_transform_mergeArray( $contentItemsNew, $contentItemsOld );

	 foreach ($content_items as $content_item) {

		  if( version_compare(JVERSION,'1.6.0','<') ) $Itemid = $mainframe->getItemId($content_item[1]);

		 if(empty($Itemid)){
		 	$Itemid = $GLOBALS[JNEWS.'itemidAca'];
		 }
		 $replacement = jnewsbot_content_getitem($content_item[1]);
 		if ($GLOBALS[JNEWS.'show_author'] == 1){
		 	$author = "\r\n".$replacement->created_by_alias;
	 	}
	 	else{
	 		$author = '';
	 	}

		 $replacement->title ="<b>". strtoupper(jNews_ProcessMail::htmlToText($replacement->title)) ."</b>";
		 $replacement->introtext = jNews_ProcessMail::htmlToText($replacement->introtext);
		 $replacement->fulltext = jNews_ProcessMail::htmlToText($replacement->fulltext);
		 if ($content_item[2] == 0) {
			 $text = str_replace($content_item[0], $replacement->title . $author . "\r\n" . $replacement->introtext . "\r\n" . $replacement->fulltext . "\r\n", $text);
		 } else {

		 	$link = 'option=com_content&view=article&id=' . $content_item[1].'&Itemid='.$Itemid ;
		 	$link = jNews_Tools::completeLink($link,false,$GLOBALS[JNEWS.'use_sef']);

             if ($content_item[2] == 1) {
				if(empty($replacement->fulltext) AND !empty($GLOBALS[JNEWS.'word_wrap'])){
             		if(strlen($replacement->introtext) > $GLOBALS[JNEWS.'word_wrap']){
             			$replacement->introtext = substr(strip_tags($replacement->introtext),0,$GLOBALS[JNEWS.'word_wrap']).'...';
             		}
             	}
			    $text = str_replace($content_item[0], $replacement->title . $author . "\r\n" . $replacement->introtext . "\r\n" . '* ' . _JNEWS_READMORE . ' ( '. $link . ' )' . "\r\n", $text);
             }
             else {
			    $text = str_replace($content_item[0], $replacement->title . ' ( ' . $link . ' )', $text);
             }
         }
		 $text = str_replace('{mosimage}', '', $text);
	 }

	 $html = str_replace('{mospagebreak}', '<div style="clear: both;" ></div>', $html);
	 $text = str_replace('{mospagebreak}', "\r\n \r\n", $text);

 }

 function jnewsbot_content_getitem($id) {
	
 	$db=&JFactory::getDBO();
	if($id ==0) return '';

	$query = "SELECT a.title as title, a.sectionid as sectionid, a.catid as catid, a.introtext as introtext, b.name as name, a.created_by_alias as created_by_alias, a.fulltext as `fulltext`, a.images as images FROM #__content as a LEFT JOIN #__users as b ON a.created_by = b.id WHERE a.id = $id";
	$db->setQuery($query);
	$content_item = $db->loadObject();

	if($content_item->created_by_alias == ''){$content_item->created_by_alias = $content_item->name;}

	if(get_magic_quotes_runtime()) {
		$content_item->title ="<b>". stripslashes($content_item->title)."</b>";
		$content_item->introtext = stripslashes($content_item->introtext);
		$content_item->fulltext = stripslashes($content_item->fulltext);
		$content_item->images = stripslashes($content_item->images);
		$content_item->created_by_alias = stripslashes($content_item->created_by_alias);
	}

	return $content_item;
 }

 function jnewsbot_content_getimage($images) {

	$first = @explode("\n",$images);

	for($i=0, $n=count($first); $i < $n; $i++) {
		$second = explode('|',$first[$i] . '|||');
		$third[$i]['image'] = $second[0];
		$third[$i]['align'] = $second[1];
		$third[$i]['alttext'] = $second[2];
		$third[$i]['border'] = $second[3];
	}
	return $third;
 }

 function jnewsbot_jcalpro_transformall($html, $text, $subject, $queueInfo=null ) {

	$db=&JFactory::getDBO();

	$Itemid = $GLOBALS[JNEWS.'itemidAca'];

 	preg_match_all('#{jcalevent:.{7,15}}#', $html.$text, $tags);
 	$replace = array();
 	$replacebyHTML = array();
 	$replacebyText = array();
 	if(!empty($tags[0])){
 		foreach ($tags[0] as $tag){
			$isolate = explode(':',$tag);
			if(count($isolate)!=2) continue;
			$parameters = explode('|',$isolate[1]);
			if(count($parameters)!=4) continue;
			if(!empty($replace[$tag])) continue;
			$replace[$tag] = $tag;
			$query = 'SELECT `title`, `description`, `end_date`, `start_date`, `extid` from #__jcalpro2_events where `extid` = '.intval($parameters[0]);
			$db->setQuery($query);
			$event = $db->loadObject();
			$db->loadObject($event);

			if(empty($event->extid)){
				$replacebyHTML[$tag] = '';
				$replacebyText[$tag] = '';
				continue;
			}

			if(get_magic_quotes_runtime()) {
				$event->title = stripslashes($event->title);
				$event->description = stripslashes($event->description);
			}

			$eventhtml = '';
			if($parameters[2]){
				$eventhtml .= '<div class="aca_jcalcontent">';
			}
			$eventhtml .=  '<span class="aca_jcaltitle">' . $event->title . '</span>';
			$eventtext = strtoupper(jNews_ProcessMail::htmlToText($event->title));

			if($parameters[1]){
				$start_date_array = (explode('-',$event->start_date));
				$start_time_array = (explode(':',substr($event->start_date,10,15)));
				$date = strftime(JText::_('DATE_FORMAT_LC'), mktime($start_time_array[0], $start_time_array[1], 0, $start_date_array[1], $start_date_array[2], $start_date_array[0]));
				$eventhtml.= '<br/>'.$date;
				$eventtext.= "\r\n".$date;
			}
			if($parameters[2]){
				$eventhtml.= '<br/>'.$event->description;
				$eventtext.= "\r\n".jNews_ProcessMail::htmlToText($event->description);
			}
			if($parameters[3]){
				$link = 'option=com_jcalpro&extmode=view&extid='.$event->extid.'&Itemid='.$Itemid ;
				$link = jNews_Tools::completeLink($link,false,$GLOBALS[JNEWS.'use_sef']);

				$eventhtml.= '<br/><a href="' . $link . '"><span class="aca_readmore">' . _JNEWS_READMORE . '</span></a>';
				$eventtext.= "\r\n".' * ' . _JNEWS_READMORE . ' ( '. $link . ' )';
			}

			if($parameters[2]){
				$eventhtml .= '</div>';
			}

			$replacebyHTML[$tag] = $eventhtml;
			$replacebyText[$tag] = $eventtext;
 		}
 	}
 	$html = str_replace($replace,$replacebyHTML,$html);
	$text = str_replace($replace,$replacebyText,$text);
 }

// This function adds the viewonline tag
//	It will take the text inside the tag and create a link on it to direct the user to the online version of the newsletter
 function jnewsbot_viewonline_transformall($html, $text, $subject, $queueInfo=null ) {

	$db=&JFactory::getDBO();
	$Itemid = $GLOBALS[JNEWS.'itemidAca'];
	$viewonlinehtml='';
	$viewonlinetext='';

	// catches all the viewonline tags on the newsletter html and text body
// 	preg_match_all('#{tag:viewonline.{3,}#', $html.$text, $tags);
	preg_match_all('#\{tag:viewonline.{3,}?\}#', $html, $tags);
 	$replace = array();
 	$replacebyHTML = array();

 	$replacebyText = array();
 	if(!empty($tags[0])){

 		foreach ($tags[0] as $tag){

//			$isolate = explode(':',$tag);
			$isolate = explode('tag:viewonline name=',$tag);
			$details = explode('}',$isolate[1]);

			if(!empty($replace[$tag])) continue;
			$replace[$tag] = $tag;
			$Itemid = $GLOBALS[JNEWS.'itemidAca'];
			$mailingId = JRequest::getInt('mailingid', 0, 'request');

			$listId = JRequest::getInt('listid', 0, 'request');

			//create the link
			$link = jNews_Tools::completeLink( 'option='.JNEWS_OPTION.'&act=mailing&task=view&mailingid='.$mailingId . '&format=raw' , false, $GLOBALS[JNEWS.'use_sef'] );

			$viewonlinehtml.= '<a href="' . $link . '"><span class="aca_online">'.$details[0].'</span></a>';
			$viewonlinetext.= "".' * '.$details[0].' ( '. $link . ' )';

			$replacebyHTML[$tag] = $viewonlinehtml;
			$replacebyText[$tag] = $viewonlinetext;

 		}
 	}
	//replace the tag with the exact link
 	$html = str_replace($replace,$replacebyHTML,$html);
	$text = str_replace($replace,$replacebyText,$text);

 }

 function jnewsbot_class_transformfinal($html, $text,$params = null) {

	$db=&JFactory::getDBO();

	 $replace = array();
	 $replaceby = array();
	 $i = 0;
	 if(!empty($params)){
		 foreach($params as $class => $style){
			if(preg_match('#class_#',$class) AND !empty($style)){
				$class = str_replace('class_','',$class);
				$replace[$i] = 'class="'.$class.'"';
				$replaceby[$i] = 'style="'.$style.'"';
				$i++;
			}
		}
	}

	$html = str_replace($replace,$replaceby,$html);
 }
 
function jnewsbot_class_transform_mergeArray( $contentItemsNew, $contentItemsOld ) {

	if ( empty($contentItemsNew) ) {
		return ( empty($contentItemsOld) ? array() : $contentItemsOld );
	} else {
		if ( empty($contentItemsOld) ) {
			return $contentItemsNew;
		} else {
			$newArray = $contentItemsNew;
			$allTagA = array();
			foreach( $contentItemsNew as $ONEcontentItemsNew ) $allTagA[] = $ONEcontentItemsNew[0];
				
			foreach( $contentItemsOld as $ONEcontentItemsOld ) {
				if ( !in_array( $ONEcontentItemsOld[0], $allTagA ) ) $newArray[] = $ONEcontentItemsOld;
			}
			
			return $newArray;
		}
	}
	
}
 
 
class siteContent{
 	function countSiteCount(){
 		$db=&JFactory::getDBO();
 		if(version_compare(JVERSION,'1.6.0','<')){ //j15
 			$query = 'SELECT count(a.`id`) FROM `#__content`AS a WHERE a.`state`=1';
 			$db->setQuery($query);
 			$db->query();
 			$count = $db->loadResult();
 		}else{
 			$query = 'SELECT count(a.`id`) FROM `#__content` as a LEFT JOIN #__categories AS c ON a.catid = c.id WHERE c.extension = "com_content" AND a.`state`=1';
 			$db->setQuery($query);
 			$db->query();
 			$count = $db->loadResult();
 		}
 		return $count;
 	}
 }
