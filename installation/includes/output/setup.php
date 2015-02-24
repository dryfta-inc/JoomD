<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Output: Database restoration setup
 */

defined('_ABI') or die('Direct access is not allowed');

global $view;
extract($view);

?>

<script type="text/javascript">//<![CDATA[
	var sa_emails = new Array();
	<?php foreach($sa as $def): ?>
	sa_emails["<?php echo $def['id'] ?>"] = "<?php echo $def['email']?>";
	<?php endforeach; ?>
	$(function() {
		// Hijack the next button to perform pre-submission validation and
		// setup our lovely dialog
		hijackSetupNext();
		// Hook up combo box change with email field
		$('#sauser').change(function(){
			$('#saemail').val( sa_emails[$('#sauser').val()] );
			$('#sapass1').val('');
			$('#sapass2').val('');
		});
		$('#btnFindFTPRoot').click(btnFindFTPRootClick); // Find FTP Root click handler
		$('#btnFTPCheck').click(btnFTPCheckClick); // FTP Check click handler
		$('#live_site').blur(checkLiveSite);
	});

	var errorstrings = Array();
	errorstrings['sitename'] = '<?php echo addslashes(ABIText::_('VERR_SITENAME')) ?>';
	errorstrings['siteemail'] = '<?php echo addslashes(ABIText::_('VERR_EMAIL')) ?>';
	errorstrings['fromname'] = '<?php echo addslashes(ABIText::_('VERR_EMAILFROM')) ?>';
	errorstrings['sapass'] = '<?php echo addslashes(ABIText::_('VERR_SAPASS')) ?>';
	errorstrings['saemail'] = '<?php echo addslashes(ABIText::_('VERR_SAEMAIL')) ?>';

	abi_current_tmp = '<?php echo addslashes($dirs['tmp_path']) ?>';
	abi_current_log = '<?php echo addslashes($dirs['log_path']) ?>';
	abi_default_tmp = '<?php echo addslashes(JPATH_SITE.'/tmp') ?>';
	abi_default_log = '<?php echo addslashes(JPATH_SITE.'/log') ?>';
//]]></script>

<div id="dialog" title="<?php echo ABIText::_('ERROR_DIALOG_LABEL') ?>">
	<p id="progresstext"></p>
</div>

<div id="okdialog" title="<?php echo ABIText::_('DIALOG_OK') ?>">
	<p><?php echo ABIText::_('FTP_CONNECTION_ESTABLISHED') ?></p>
</div>

<h2><?php echo ABIText::_('TITLE_SITESETUP') ?></h2>

<div id="accordion">

<div id="helpme">
	<?php echo ABIText::_('GENERIC_HELPME_WONDERING') ?> <a href="https://www.akeebabackup.com/documentation/quick-start-guide/abi-site-information.html" target="_blank"><?php echo ABIText::_('GENERIC_HELPME_CLICKHERE') ?></a>
</div>
	
<h3><?php echo ABIText::_('LABEL_SITEPARAMS') ?></h3>
<div class="categoryitems">
<table>
	<thead>
		<tr>
			<th><?php echo ABIText::_('ITEM'); ?></th>
			<th><?php echo ABIText::_('VALUE'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo ABIText::_('SITENAME') ?></td>
			<td><input type="text" id="sitename" name="sitename" value="<?php echo $site['sitename'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('SITEEMAIL') ?></td>
			<td><input type="text" id="mailfrom" name="mailfrom" value="<?php echo $site['mailfrom'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('SITEEMAILFROM') ?></td>
			<td><input type="text" id="fromname" name="fromname" value="<?php echo $site['fromname'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('SITELIVESITE') ?></td>
			<td><input type="text" id="live_site" name="live_site" value="<?php echo $site['live_site'] ?>" size="30" /></td>
		</tr>
		<?php if($site['jversion']): ?>
		<tr>
			<td><?php echo ABIText::_('COOKIEDOMAIN') ?></td>
			<td><input type="text" id="cookie_domain" name="cookie_domain" value="<?php echo $site['cookie_domain'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('COOKIEPATH') ?></td>
			<td><input type="text" id="cookie_path" name="cookie_path" value="<?php echo $site['cookie_path'] ?>" size="30" /></td>
		</tr>
		<?php endif; ?>
		<tr>
			<td><?php echo ABIText::_('OVERRIDEPATHS') ?></td>
			<td>
				<label for="overridepaths"><?php echo ABIText::_('OVERRIDEPATHSCHECKTEXT') ?></label>
				<input type="checkbox" id="overridepaths" name="overridepaths" onchange="onOverridePaths();" />
			</td>
		</tr>		
	</tbody>
</table>
</div>

<h3><?php echo ABIText::_('LABEL_FTP') ?></h3>
<div class="categoryitems">
<table>
	<thead>
		<tr>
			<th><?php echo ABIText::_('ITEM'); ?></th>
			<th><?php echo ABIText::_('VALUE'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo ABIText::_('USEFTP') ?></td>
			<td><input type="checkbox" id="ftp_enable" name="ftp_enable" <?php echo $ftp['ftp_enable'] == 1 ? 'checked="checked"' : '' ?> /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('FTPHOST') ?></td>
			<td><input type="text" id="ftp_host" name="ftp_host" value="<?php echo $ftp['ftp_host'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('FTPPORT') ?></td>
			<td><input type="text" id="ftp_port" name="ftp_port" value="<?php echo $ftp['ftp_port'] ?>" size="5" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('FTPUSER') ?></td>
			<td><input type="text" id="ftp_user" name="ftp_user" value="<?php echo $ftp['ftp_user'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('FTPPASS') ?></td>
			<td><input type="password" id="ftp_pass" name="ftp_pass" value="<?php echo $ftp['ftp_pass'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('FTPDIR') ?></td>
			<td><input type="text" id="ftp_root" name="ftp_root" value="<?php echo $ftp['ftp_root'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="button" id="btnFindFTPRoot" value="<?php echo ABIText::_('FTPAUTO') ?>" />
				<input type="button" id="btnFTPCheck" value="<?php echo ABIText::_('FTPCHECK') ?>" />
			</td>
		</tr>
	</tbody>
</table>
</div>

<h3><?php echo ABIText::_('LABEL_SUPERADMIN') ?></h3>
<div class="categoryitems">
<table>
	<thead>
		<tr>
			<th><?php echo ABIText::_('ITEM'); ?></th>
			<th><?php echo ABIText::_('VALUE'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo ABIText::_('SAUSERNAME') ?></td>
			<td>
				<select name="sauser" id="sauser">
				<?php foreach($sa as $def): ?>
					<?php $selected = ($def['id'] == $saselected) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo $def['id'] ?>" <?php echo $selected?>><?php echo $def['username']?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('SAPASS1') ?></td>
			<td><input type="password" name="sapass1" id="sapass1" value="<?php echo $sapass1 ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('SAPASS2') ?></td>
			<td><input type="password" name="sapass2" id="sapass2" value="<?php echo $sapass2 ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('SAEMAIL') ?></td>
			<td><input type="text" name="saemail" id="saemail" value="<?php echo $saemail ?>" size="30" /></td>
		</tr>
	</tbody>
</table>
</div>

<h3><?php echo ABIText::_('LABEL_FINETUNING') ?></h3>
<div class="categoryitems">
<table>
	<thead>
		<tr>
			<th><?php echo ABIText::_('ITEM'); ?></th>
			<th><?php echo ABIText::_('VALUE'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo ABIText::_('SITE_ABSOLUTE') ?></td>
			<td><tt><?php echo htmlentities(JPATH_SITE) ?></tt></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('TMP_DIR') ?></td>
			<td><input type="text" name="tmp_path" id="tmp_path" value="<?php echo $dirs['tmp_path'] ?>" size="30" /></td>
		</tr>
		<tr>
			<td><?php echo ABIText::_('LOG_DIR') ?></td>
			<td><input type="text" name="log_path" id="log_path" value="<?php echo $dirs['log_path'] ?>" size="30" /></td>
		</tr>
	</tbody>
</table>
</div>

</div>