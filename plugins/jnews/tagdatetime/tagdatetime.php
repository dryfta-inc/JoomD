<?php
defined('_JEXEC') OR die('Access Denied!');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

JApplication::registerEvent( 'jnewsbot_tagdatetime', 'tagdatetime' );
JApplication::registerEvent( 'jnewsbot_transformall','tagdatetime_transform');

/**
 * <p>Function to insert a date tag<p>

 */
function tagdatetime(){
		$js = 'function insertjnewstag(tag){';
	if( version_compare(JVERSION,'1.6.0','<') ){//1.5
		$js .= ' if(window.top.insertTag(tag)){window.top.document.getElementById(\'sbox-window\').close();}';
	}else{
		$js .= ' if(window.top.insertTag(tag)) window.parent.SqueezeBox.close();';
	}
		$js .= '}';
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	?>
<style type="text/css">
	table.joobilist tr:hover {
		cursor: pointer;
	}
</style>
<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
	<table class="joobilist">
			<tbody>
				<thead>
					<tr>
						<th class="title"><center><?php echo _JNEWS_MAILING_TAG; ?></center></th>
						<th class="title"><center><?php echo _JNEWS_TEMPLATE_DESC; ?></center></th>
					</tr>
				</thead>
				<tr class="row0" onclick="insertjnewstag('{tag:date}')">
					<td>
						<strong><?php echo '{tag:date}'; ?></strong>
					</td>
					<td>
						<?php
						$date = JHTML::_('date',jnews::getNow(), JText::_('DATE_FORMAT_LC'), JNEWS_TIME_OFFSET);
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('tag:date format=1')">
					<td>
						<strong><?php echo '{tag:date format=1}'; ?></strong>
					</td>
					<td>
						<?php
						$date = JHTML::_('date',jnews::getNow(), JText::_('DATE_FORMAT_LC1'), JNEWS_TIME_OFFSET);
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('tag:date format=2')">
					<td>
						<strong><?php echo '{tag:date format=2}'; ?></strong>
					</td>
					<td>
						<?php
						$date = JHTML::_('date',jnews::getNow(), JText::_('DATE_FORMAT_LC2'), JNEWS_TIME_OFFSET);
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('tag:date format=3')">
					<td>
						<strong><?php echo '{tag:date format=3}'; ?></strong>
					</td>
					<td>
						<?php
						$date = JHTML::_('date',jnews::getNow(), JText::_('DATE_FORMAT_LC3'), JNEWS_TIME_OFFSET);
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('tag:date format=4')">
					<td>
						<strong><?php echo '{tag:date format=4}'; ?></strong>
					</td>
					<td>
						<?php
						$date = JHTML::_('date',jnews::getNow(), JText::_('DATE_FORMAT_LC4'), JNEWS_TIME_OFFSET);
						echo $date;
						?>
					</td>
				</tr>
			</tbody>
		</table>

	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
<?php
}

function tagdatetime_transform($html, $text, $subject, $queueInfo=null ){

	//we store the replacement of the date/time tag
	$replace = array();

//	$pattern = '#{date:?([^:]*)}#Ui';
	//sample tag = {tag:date format=1}
	$pattern = '#{tag:date ?(.*)}#Ui';

	preg_match_all($pattern, $html.$text.$subject, $tags);

	if(!empty($tags[0])){

		foreach ($tags[0] as $key => $tag){
			$format = (!empty($tags[1][$key])) ? substr($tags[1][$key], 8 ) : '';
			$replace[$tag] = JHTML::_('date',jnews::getNow(), JText::_('DATE_FORMAT_LC'.$format ), JNEWS_TIME_OFFSET);
		}

	}
	$html = str_replace(array_keys($replace),$replace,$html);
	$text = str_replace(array_keys($replace),$replace,$text);
	$subject = str_replace(array_keys($replace),$replace,$subject);

}
