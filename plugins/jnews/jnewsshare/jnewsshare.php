<?php 
defined('_JEXEC') OR die('...Direct Access to this location is not allowed...');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

/**
* @copyright Copyright (C) 2009 Joobi Limited All rights reserved.
* @license This file is released under the GPL license (http://www.gnu.org/licenses )
* @link http://www.joobi.co
*/

JApplication::registerEvent('jnewsbot_share_editabs', 'jnewsbot_share_editab');
//JApplication::registerEvent('jnewsbot_share_media', 'jnewsbot_share_replaceMedia');
JApplication::registerEvent('jnewsbot_transformall', 'jnewsbot_share_replaceMedia');

function jnewsbot_share_editab() {
	ob_start();

	$js = "function setShareTag(changed){
		var form = document.adminForm;
		if(!form){
			form = document.mosForm;
		}
		if(changed!=null){
			var tag = '{tag:share name=';
			var media = '';
			var facebook = 0;
			var linkedin = 0;
			var twitter = 0;
		
			for (i=0;i<form.facebook.length;i++) {
				if (form.facebook[i].checked) {
					facebook = form.facebook[i].value;
				}
			}
			
			for (i=0;i<form.linkedin.length;i++) {
				if (form.linkedin[i].checked) {
					linkedin = form.linkedin[i].value;
				}
			}
			
			for (i=0;i<form.twitter.length;i++) {
				if (form.twitter[i].checked) {
					twitter = form.twitter[i].value;
				}
			}
			
			if(facebook == 1) media = media + 'facebook';
			
			if((facebook==1 && linkedin==1)) media = media + ','; 
			
			if(linkedin == 1) media = media + 'linkedin';	
			
			if((facebook==1 || linkedin==1) && twitter == 1) media = media + ','; 
			
			if(twitter == 1) media = media + 'twitter';	
			
			if(media == '') tag = media;
			else tag = tag + media + '}';
			
			
			form.sharetag.value= tag;
			
		}else{
			var sharetag = form.sharetag.value;";
	if( version_compare(JVERSION,'1.6.0','<') ){//1.5
		$js .= " if(window.top.insertTag(sharetag)){window.top.document.getElementById('sbox-window').close();}";
	}else{
		$js .= " if(window.top.insertTag(sharetag)) window.parent.SqueezeBox.close();";
	}
		$js .= "}
	}";
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
<form name="adminForm" method="post" action="index.php">
<div id="element-box">
<div class="t">
<div class="t">
<div class="t"></div>
</div>
</div>
<div class="m">
<table class="smartcontent" width="100%">

<tr>
	<td width="185" class="key">
		<span class="editlinktip">
			<?php
				$tip = 'Select yes to add Facebook tag)';
				$title = 'Facebook';
				echo jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 );
			?>
		</span>
	</td>
	<td style="vertical-align: top;">
		<input type="radio" name="facebook" value="1" checked="checked" onclick="setShareTag(1)"/><?php echo 'Yes'; ?>
        <input type="radio" name="facebook" value="0" onclick="setShareTag(1)"/><?php echo 'No'; ?>
	</td>
</tr>

<tr>
	<td width="185" class="key">
		<span class="editlinktip">
			<?php
				$tip = 'Select yes to add LinkedIn tag)';
				$title = 'LinkedIn';
				echo jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 );
			?>
		</span>
	</td>
	<td style="vertical-align: top;">
		<input type="radio" name="linkedin" value="1" checked="checked" onclick="setShareTag(1)"/><?php echo 'Yes'; ?>
        <input type="radio" name="linkedin" value="0" onclick="setShareTag(1)"/><?php echo 'No'; ?>
	</td>
</tr>

<tr>
	<td width="185" class="key">
		<span class="editlinktip">
			<?php
				$tip = 'Select yes to add Twitter tag)';
				$title = 'Twitter';
				echo jNews_Tools::toolTip( $tip, '', 280, 'tooltip.png', $title, '', 0 );
			?>
		</span>
	</td>
	<td style="vertical-align: top;">
		<input type="radio" name="twitter" value="1" checked="checked" onclick="setShareTag(1)"/><?php echo 'Yes'; ?>
        <input type="radio" name="twitter" value="0" onclick="setShareTag(1)"/><?php echo 'No'; ?>
	</td>
</tr>

<table class="smartcontent" width="100%">
<tr>
	<td width="185" class="key">
		<span class="editlinktip">
			Tag
		</span>
	</td>
	<td style="vertical-align: top;">
		<input type="text" size="70px" name="sharetag" readonly="true" value="{tag:share name=facebook,linkedin,twitter}">
	</td>
	<td rowspan="2">
		<input onclick="setShareTag(null)" class="sharetag" type="button" label="Insert Tag" name="Insert Tag" value="Insert Tag"/>
	</td>
</tr>
</table>
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
	return array(_JNEWS_TAGMENU_MEDIA_SHARE, $return);
}

//function to replace the social share tag  with it's value
//return $html-content of the newsletter with the replaced value
function jnewsbot_share_replaceMedia( $html, $text) {
	preg_match_all('#\{tag:share.{3,}?\}#', $html, $tags);
	$replace = array();
 	$replacebyHTML = array();
 	$replacebyText = array();
 	
 	if(!empty($tags[0])){
 		if(@include_once( JNEWSPATH_CLASS . 'socialshare.php')){
 			if(class_exists('jNews_SocialShare')){
 				foreach($tags[0] as $tag){
 					$social = explode('tag:share name=', $tag);
 					$socialTags = explode('}',$social[1]);
					$mediaShare = explode(',',$socialTags[0]);

					if(!empty($replace[$tag])) continue;
					$replace[$tag] = $tag;
					
					$mailingId = JRequest::getInt('mailingid', 0, 'request');

					$mediaShareHTML = jNews_SocialShare::mediaShare($mediaShare, true, $mailingId); 
			
					$replacebyHTML[$tag] = $mediaShareHTML;
					//$replacebyText[$tag] = $viewonlinetext;
 				}
 			}
 		}
 	}
 	$html = str_replace($replace,$replacebyHTML,$html);
 	$text = str_replace($replace,$replacebyText,$text);
 	
	//$html = html_entity_decode( $html );
	//$text = html_entity_decode( $text );	
	//$html = str_replace('{mospagebreak}', '<div style="clear: both;" ></div>', $html);
	//$text = str_replace('{mospagebreak}', "\r\n \r\n", $text);
}

