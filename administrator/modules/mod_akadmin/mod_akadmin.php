<?php
/**
 * @package AkeebaBackup
 * @subpackage BackupIconModule
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 2.2
 * @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Check for PHP4
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.2.7', '>=')) return;

// Make sure Akeeba Backup is installed
if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_akeeba')) {
	return;
}

// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if(function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set') && !version_compare(JVERSION,'1.6','ge')) {
	if(function_exists('error_reporting')) {
		$oldLevel = error_reporting(0);
	}
	$serverTimezone = @date_default_timezone_get();
	if(empty($serverTimezone) || !is_string($serverTimezone)) $serverTimezone = 'UTC';
	if(function_exists('error_reporting')) {
		error_reporting($oldLevel);
	}
	@date_default_timezone_set( $serverTimezone);
}

/*
 * Hopefuly, if we are still here, the site is running on at least PHP5. This means that
 * including the Akeeba Backup factory class will not throw a White Screen of Death, locking
 * the administrator out of the back-end.
 */

// Make sure Akeeba Backup is installed, or quit
$akeeba_installed = @file_exists(JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php');
if(!$akeeba_installed) return;

// Make sure Akeeba Backup is enabled
jimport('joomla.application.component.helper');
if (!JComponentHelper::isEnabled('com_akeeba', true))
{
	//JError::raiseError('E_JPNOTENABLED', JText('AKEEBA_NOT_ENABLED'));
	return;
}

// Joomla! 1.6 or later - check ACLs (and not display when the site is bricked,
// hopefully resulting in no stupid emails from users who think that somehow
// Akeeba Backup crashed their site). It also not displays the button to people
// who are not authorised to take backups - which makes perfect sense!
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$user = JFactory::getUser();
	if (!$user->authorise('akeeba.backup', 'com_akeeba')) {
		return;
	}
}

// Load custom CSS
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/mod_akadmin/css/mod_akadmin.css');

// Load the language files
$jlang =& JFactory::getLanguage();
$jlang->load('mod_akadmin', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('mod_akadmin', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('mod_akadmin', JPATH_ADMINISTRATOR, null, true);

// Initialize defaults
$image = "akeeba-48.png";
$label = JText::_('LBL_AKEEBA');

if( $params->get('enablewarning', 0) == 0 )
{
	// Process warnings
	$warning = false;

	// Load necessary files
	if(!version_compare( JVERSION, '1.6.0', 'ge' )) {
		define('AKEEBA_JVERSION','15');
	} else {
		define('AKEEBA_JVERSION','16');
	}
	if(!defined('AKEEBAENGINE')) {
		define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
		define('AKEEBAPLATFORM', 'joomla15'); // So that platform-specific stuff can get done!
	}
	require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php';
	$registry =& AEFactory::getConfiguration();
	AEPlatform::getInstance()->load_configuration();
	
	// Get latest non-SRP backup ID
	$filters = array(
		array(
			'field'			=> 'tag',
			'operand'		=> '<>',
			'value'			=> 'restorepoint'
		)
	);
	$ordering = array(
		'by'		=> 'backupstart',
		'order'		=> 'DESC'
	);
	require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/models/statistics.php';
	$model = new AkeebaModelStatistics();
	$list =& $model->getStatisticsListWithMeta(false, $filters, $ordering);
	
	if(!empty($list)) {
		$record = (object)array_shift($list);
	} else {
		$record = null;
	}
	
	// Process "failed backup" warnings, if specified
	if( $params->get('warnfailed', 0) == 0 )
	{
		if(!is_null($record))
		{
			$warning = (($record->status == 'fail') || ($record->status == 'run'));
		}
	}

	// Process "stale backup" warnings, if specified
	if(is_null($record))
	{
		$warning = true;
	}
	else
	{
		$maxperiod = $params->get('maxbackupperiod', 24);
		jimport('joomla.utilities.date');
		$lastBackupRaw = $record->backupstart;
		$lastBackupObject = new JDate($lastBackupRaw);
		$lastBackup = $lastBackupObject->toUnix(false);
		$maxBackup = time() - $maxperiod * 3600;
		if(!$warning) $warning = ($lastBackup < $maxBackup);
	}

	if($warning)
	{
		$image = 'akeeba-warning-48.png';
		$label = JText::_('LBL_BACKUPREQUIRED');
	}
}

// Load the Akeeba Backup configuration and check user access permission
if(!defined('AKEEBAENGINE'))
{
	define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
	define('AKEEBAPLATFORM', 'joomla15'); // So that platform-specific stuff can get done!
}
require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php';
$registry =& AEFactory::getConfiguration();
$user =& JFactory::getUser();
$showModule = true;
unset($registry);

// Administrator access allowed
if( version_compare( JVERSION, '1.6.0', 'ge' ) )
{
	// Joomla! 1.6
	$extraclass = 'icon16';
}
else
{
	// Joomla! 1.5
	$gid = $user->gid;
	if( ($gid != 25) && ($gid != 24) )
	{
		$showModule = false;
	}

	$extraclass = 'icon15';
}

unset($user);

if($showModule):
	
if(version_compare(JVERSION, '1.6.0', 'ge')):?>
<div class="icon-wrapper" id="akadminicon">
	<div class="akcpanel">
		<div class="icon-wrapper">
			<div class="icon <?php echo $extraclass ?>">
				<a href="index.php?option=com_akeeba&view=backup">
					<img src="components/com_akeeba/assets/images/<?php echo $image ?>" />
					<span><?php echo $label; ?></span>
				</a>
			</div>
		</div>
	</div>
</div>
<script lang="text/javascript">
	var akeebabackupIcon = $('akadminicon');
	try {
		var akeebabackupIconParent = $('akadminicon').getParent().getParent();
		if(akeebabackupIconParent.attributes.class.textContent == 'panel') {
			akeebabackupIconParent.setStyle('display','none');
		}
	} catch(e) {	
	}
<?php if(version_compare(JVERSION, '2.5.0', 'lt')): ?>
	try {
		$('cpanel').grab(akeebabackupIcon);
	} catch(e) {	
	}
<?php else: ?>
	try {
		$$('div.cpanel')[0].grab(akeebabackupIcon)
	} catch(e) {
	}
<?php endif; ?>
</script>
<?php else: ?>
<div class="akcpanel">
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon <?php echo $extraclass ?>">
			<a href="index.php?option=com_akeeba&view=backup">
				<img src="components/com_akeeba/assets/images/<?php echo $image ?>" />
				<span><?php echo $label; ?></span>
			</a>
		</div>
	</div>
</div>
<?php endif; ?>
<?php endif; ?>