<?php
defined('_JEXEC') OR die('Access Denied!');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

JApplication::registerEvent( 'jnewsbot_tagsubscription', 'tagsubscription' );

/**
 * <p>Function to insert a subscription tag<p>

 */
function tagsubscription(){
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
				<tr class="row0" onclick="insertjnewstag('{tag:subscriptions}')">
					<td>
						<strong><?php echo '{tag:subscriptions}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TAG_SUBSCRIPTION_DESC ?>
					</td>
				</tr>
				<tr class="row1" onclick="insertjnewstag('{tag:unsubscribe}')">
					<td>
						<strong><?php echo '{tag:unsubscribe}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TAG_UNSUBSCRIBE_DESC ?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('{tag:confirm}')">
					<td>
						<strong><?php echo '{tag:confirm}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TAG_CONFIRM_DESC ?>
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
