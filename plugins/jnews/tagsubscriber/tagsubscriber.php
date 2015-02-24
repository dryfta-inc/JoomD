<?php
defined('_JEXEC') OR die('Access Denied!');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

JApplication::registerEvent( 'jnewsbot_tagsubscriber', 'tagsubscriber' );

/**
 * <p>Function to insert a subscription tag<p>

 */
function tagsubscriber(){
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
				<tr class="row0" onclick="insertjnewstag('{tag:name}')">
					<td>
						<strong><?php echo '{tag:name}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TAG_NAME_DESC ?>
					</td>
				</tr>
				<tr class="row1" onclick="insertjnewstag('{tag:firstname}')">
					<td>
						<strong><?php echo '{tag:firstname}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TAG_FNAME_DESC ?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('{tag:email}')">
					<td>
						<strong><?php echo '{tag:email}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TAG_EMAIL_DESC ?>
					</td>
				</tr>
				<tr class="row1" onclick="insertjnewstag('{tag:issuenb}')">
					<td>
						<strong><?php echo '{tag:issuenb}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TAG_ISSUENB_DESC ?>
					</td>
				</tr>
				<tr class="row1" onclick="insertjnewstag('{tag:title}')">
					<td>
						<strong><?php echo '{tag:title}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_TITLE ?>
					</td>
				</tr>
				<?php if($GLOBALS[JNEWS.'level'] > 2){
				if($GLOBALS[JNEWS.'show_column1']==1){?>
				<tr class="row0" onclick="insertjnewstag('{tag:profile nb=1}')">
					<td>
						<strong><?php echo '{tag:profile nb=1}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_COLUMN_DESC . '1' ?>
					</td>
				</tr>
				<?php
				}
				if($GLOBALS[JNEWS.'show_column2']==1){
				?>
				<tr class="row1" onclick="insertjnewstag('{tag:profile nb=2}')">
					<td>
						<strong><?php echo '{tag:profile nb=2}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_COLUMN_DESC . '2' ?>
					</td>
				</tr>
				<?php
				}
				if($GLOBALS[JNEWS.'show_column3']==1){
				?>
				<tr class="row0" onclick="insertjnewstag('{tag:profile nb=3}')">
					<td>
						<strong><?php echo '{tag:profile nb=3}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_COLUMN_DESC . '3' ?>
					</td>
				</tr>
				<?php
				}
				if($GLOBALS[JNEWS.'show_column4']==1){
				?>
				<tr class="row1" onclick="insertjnewstag('{tag:profile nb=4}')">
					<td>
						<strong><?php echo '{tag:profile nb=4}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_COLUMN_DESC . '4' ?>
					</td>
				</tr>
				<?php
				}
				if($GLOBALS[JNEWS.'show_column5']==1){
				?>
				<tr class="row0" onclick="insertjnewstag('{tag:profile nb=5}')">
					<td>
						<strong><?php echo '{tag:profile nb=5}'; ?></strong>
					</td>
					<td>
						<?php echo _JNEWS_COLUMN_DESC . '5' ?>
					</td>
				</tr>
			<?php }
				}
				 ?>
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
