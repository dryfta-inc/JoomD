<?php
defined('_JEXEC') OR die('Access Denied!');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

JApplication::registerEvent( 'jnewsbot_tagsite', 'tagsite' );
JApplication::registerEvent( 'jnewsbot_transformall','tagsite_transform');

/**
 * <p>Function to insert a module tag<p>
 * @param string $forms - the start html form
 * @param object $params - the plugin parameters

 */
function tagsite($forms, $params = null){
		$limit = -1;
		$action = JRequest::getVar('act');
		$task = JRequest::getVar('task');
		echo $forms['main'];
		$customlink = trim($params->get('customlink'));
		$customlinkA = array();
		if (!empty($customlink)) $customlinkA = explode(',', $customlink);
		$countParams = count($customlinkA);
	?>
<style type="text/css">
	table.sitetaginput {
		border: 1px solid #D5D5D5;
		background-color: #F6F6F6;
		width: 100%;
		margin-bottom: 10px;
		-moz-border-radius:3px;
 		-webkit-border-radius:3px;
 		padding: 5px;
 	}
 	table.sitetaginput td.key {
 		background-color: #f6f6f6;
		text-align: left;
		width: 140px;
		color: #666;
		font-weight: bold;
		border-bottom: 1px solid #e9e9e9;
		border-right: 1px solid #e9e9e9;
 	}
	div.sitetaglist {
		border-spacing:1px;
		border:3px double #D5D5D5;
		padding: 5px;
		margin-bottom: 5px;
		cursor: pointer;
	}
 	div.k0 {
		background-color:#f4f4ff;
		color:#666;
	}
	div.k0:hover, div.k1:hover{
		background-color:#CFF;
		color:#666;
	}
	div.k1 {
		background-color:#FFF;
		color:#666;
	}
	div.k1:hover, div.k1:hover{
		background-color:#CFF;
		color:#666;
	}

	div.selectedtag {
		background-color:#87c331;
		color:#FFF;
		font-weight:bold;
	}

	table.sitetaginput tr.inputhide {
		display: none;
	}

	table.sitetaginput tr.inputunhide {
		display: table-row;
	}
 </style>
<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
<div class="m">
	<?php
		$js = 'function insertjnewstag(tag){';
	if( version_compare(JVERSION,'1.6.0','<') ){//1.5
		$js .= ' if(window.top.insertTag(tag)){window.top.document.getElementById(\'sbox-window\').close();}';
	}else{
		$js .= ' if(window.top.insertTag(tag)) window.parent.SqueezeBox.close();';
	}
		$js .= '}';
		$script = 'function setCaption(newtag){window.document.getElementById(\'jnewstag\').value = newtag}';
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	?>
	<script language="javascript" type="text/javascript">
	 function jNewsTagInsert(tag, id) {
		var form = document.adminForm;
		if(!form){
			form = document.mosForm;
		}
		form.jnewstag.value = tag;
		form.jnewsselecttag.value = id;
		document.getElementById('tagvisibility').className = 'inputunhide';
		document.getElementById(id).className = 'selectedtag sitetaglist';
	 	switch(id){
	 		case 'divviewonline':
	 			form.jnewstagcaption.value = '<?php echo _JNEWS_TAG_VIEWONLINE;?>';
		 		document.getElementById('divfwdtofriend').className = 'k1 sitetaglist';
				<?php for ($a = 0; $a <= count($customlinkA); $a++){ ?>
				document.getElementById('div<?php echo 'site'.$a;?>').className = 'k<?php $x=$a % 2; echo $x;?> sitetaglist';
				<?php
				 }
				 	?>
		 		break;
		 	case 'divfwdtofriend':
		 		form.jnewstagcaption.value = '<?php echo _JNEWS_TAG_FWDTOFRIEND;?>';
				document.getElementById('divviewonline').className = 'k1 sitetaglist';
				<?php for ($b = 0; $b <= $countParams; $b++){ ?>
				document.getElementById('div<?php echo 'site'.$b;?>').className = 'k<?php $x=$b % 2; echo $x;?> sitetaglist';
				<?php
				}
				?>
		 		break;
		 	<?php
		 		for ($c = 0; $c <= $countParams; $c++){
		 	?>
			case 'div<?php echo 'site'.$c;?>':
				form.jnewstagcaption.value = '<?php
				if(isset($customlinkA[$c])){
				if (substr_count($customlinkA[$c], '|') == 1){
				$linkCaption = explode('|', $customlinkA[$c]);
				echo $linkCaption[0];
				}
				}
				?>';
				document.getElementById('divviewonline').className = 'k0 sitetaglist';
				document.getElementById('divfwdtofriend').className = 'k1 sitetaglist';
				<?php
						$tagA = $customlinkA;
					for ($ci = 0; $ci <= count($tagA); $ci++){
						if ($c != $ci){
				?>
				document.getElementById('divsite<?php echo $ci;?>').className = 'k<?php $x=$ci % 2; echo $x;?> sitetaglist';
				<?php
						}
						}
				?>
				break;
			<?php
				}
			?>
		 	default:
		 		document.getElementById('divviewonline').className = 'k0 sitetaglist';
				document.getElementById('divfwdtofriend').className = 'k1 sitetaglist';
				<?php for ($d = 0; $d <= $countParams; $d++){ ?>
				document.getElementById('divsite<?php echo $d;?>').className = 'k<?php $x=$d % 2; echo $x;?> sitetaglist';
				<?php
				}
				?>
	 	}
	}
	function setCaptionTags(){
		var form = document.adminForm;
		if(!form){
			form = document.mosForm;
		}
		var selectedtag = form.jnewsselecttag.value;
		if (selectedtag == 'divviewonline'){
			form.jnewstag.value = '{tag:viewonline name='+form.jnewstagcaption.value+'}';
		}
		if (selectedtag == 'divfwdtofriend'){
			form.jnewstag.value = '{tag:fwdtofriend name='+form.jnewstagcaption.value+'}';
		}
		<?php
			for ($e = 0; $e <= $countParams; $e++){
				if(isset($customlinkA[$e])){
				if (substr_count($customlinkA[$e], '|') == 1){
					$tagCaption = explode('|', $customlinkA[$e]);
		?>
			if (selectedtag == 'divsite<?php echo $e;?>'){
				form.jnewstag.value = '{tagsite:'+form.jnewstagcaption.value+'|<?php echo $tagCaption[1];?>}';
			}
		<?php
			}
			}
		}
		?>

	}
	</script>
	<table class="sitetaginput" width="100%"">
			<tbody>
				<tr id="tagvisibility" class="inputhide">
					<td width="80" class="key">
						<span class="editlinktip">
						<?php
							$tip = _JNEWS_TAGSITE_TAGTIP;
							$title = _JNEWS_MAILING_TAG;
							echo jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 );
						?>
						</span>
					</td>
					<td>
						<input type="text" name="jnewstag" class="inputbox" size="60px">
						<input type="button" value="Insert Tag" onclick="insertjnewstag(form.jnewstag.value);" class="joobibutton">
					</td>
				</tr>
				<tr style="display:visible;">
					<td width="80" class="key">
						<span class="editlinktip">
						<?php
							$tip = _JNEWS_TAGSITE_CAPTIONTIP;
							$title = _JNEWS_TAGSITE_CAPTION;
							echo jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 );
						?>
						</span>
					</td>
					<td>
						<input type="text" onchange="setCaptionTags();" size="60px" name="jnewstagcaption">
						<input type="hidden" size="60px" name="jnewsselecttag">
					</td>
				</tr>
			</tbody>
		</table>

	<div id="divviewonline" class="k0 sitetaglist" onclick="jNewsTagInsert('{tag:viewonline name=<?php echo _JNEWS_TAG_VIEWONLINE;?>}', 'divviewonline');">Insert a "VIEW IT ONLINE" link in your e-mail</div>
	<div id="divfwdtofriend" class="k1 sitetaglist" onclick="jNewsTagInsert('{tag:fwdtofriend name=<?php echo _JNEWS_TAG_FWDTOFRIEND;?>}', 'divfwdtofriend');">Insert a "FORWARD TO A FRIEND" link in your e-mail</div>
	<?php

		$k = 0;
		foreach ($customlinkA as $customlinkOne){
			//check if | exist
			if (substr_count($customlinkOne, '|') == 1){
			$customlinkOneA = explode('|', $customlinkOne);
	?>
		<div id="divsite<?php echo $k;?>" class="k<?php echo $k%2;?> sitetaglist" onclick="jNewsTagInsert('{tagsite:<?php echo $customlinkOneA[0];?>|<?php echo $customlinkOneA[1];?>}', 'divsite<?php echo $k;?>');">Insert "<?php echo $customlinkOneA[0]?>" link in your e-mail</div>
	<?php
	$k++;
			}
	}

	?>

	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
	<input type="hidden" value="<?php echo JNEWS_OPTION; ?>" name="option"/>
	<input type="hidden" value="<?php echo $action; ?>" name="act"/>
	<input type="hidden" value="<?php echo $task; ?>" name="task"/>
  	</form>
<?php

}

function tagsite_transform($html, $text, $subject, $queueInfo=null ) {
	$sitetaghtml='';
	$sitetagtext='';

 	//catches all the sitelink tags on the newsletter html and text body
	$replaceHtml = array();
	$replaceText = array();
	$replacebyHTML = array();
	$replacebyText = array();

	preg_match_all('#{tagsite:.{3,}#', $html.$text, $tags);
	if(!empty($tags[0])){
		foreach ($tags[0] as $tag){
	 		if(stripos($tag, ':') == strrpos($tag, ':')){
				$isolate = explode(':',$tag);
	 		}else{
				$isolate=explode('tagsite:',$tag);
	 		}
			$details = explode('}',$isolate[1]);
			if(!empty($replace[$tag])) continue;
			$replaceHtml[$tag] = $tag;
			$site=explode('|',$details[0]);
	        foreach($site as $key => $value){
	    	   	$url='';
				if($key%2 != 0){
					$parsedUrl = parse_url($value);
					if ( !isset($parsedUrl['scheme'])) {
						$url = 'http://'.$value;
					}else $url = $value;
                }else{
	               	$name=$value;
	               }
			}

			$sitetaghtml = "<br/><a href=\"$url\">$name</a>";
			$replacebyHTML[$tag] = $sitetaghtml;
			$sitetagtext ="\r\n".' * '.$name.' ( '. $url. ' )';
			$replacebyText[$tag] = $sitetagtext;
		}
	 }

	 $html = str_replace($replaceHtml,$replacebyHTML,$html);
	 $text = str_replace($replaceText,$replacebyText,$text);
}