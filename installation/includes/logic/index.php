<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Logic: The first page to load
 */

defined('_ABI') or die('Direct access is not allowed');

global $view; // Import global $view variable

require_once(JPATH_INSTALLATION.'/includes/configuration.php'); // Configuration class

// Begin by grabbing and caching the configuration (creating an instance automatically does that!)
$configuration =& ABIConfiguration::getInstance();

// Check requirements
$phpOptions = array();
$phpRecommended = array();

if(version_compare($configuration->joomlaVersion(), '1.6.0', 'ge')) {
	$phpOptions[] = array (
		'label' => ABIText::_('PHP_VERSION').' >= 5.2.7',
		'state' => version_compare(phpversion(), '5.2.7', 'ge')
	);
} else {
	$phpOptions[] = array (
		'label' => ABIText::_('PHP_VERSION').' >= 5.1.6',
		'state' => version_compare(phpversion(), '5.1.6', 'ge')
	);
}
$phpOptions[] = array (
	'label' => '- '.ABIText::_('ZLIB_SUPPORT'),
	'state' => extension_loaded('zlib')
);
$phpOptions[] = array (
	'label' => '- '.ABIText::_('XML_SUPPORT'),
	'state' => extension_loaded('xml')
);
$phpOptions[] = array (
	'label' => '- '.ABIText::_('MYSQL_SUPPORT'),
	'state' => (function_exists('mysql_connect') || function_exists('mysqli_connect'))
);
if (extension_loaded( 'mbstring' )) {
	$mbDefLang = strtolower( ini_get( 'mbstring.language' ) ) == 'neutral';
	$mbOvl = ini_get('mbstring.func_overload') != 0;
	$phpOptions[] = array (
		'label' => ABIText::_( 'MB_DEFAULT_LANGUAGE' ),
		'state' => $mbDefLang
	);
	$phpOptions[] = array (
		'label' => ABIText::_('MB_OVERLOAD'),
		'state' => !$mbOvl
	);
}
$cW = (@ file_exists('../configuration.php') && @is_writable('../configuration.php')) || @is_writable('../');
$phpOptions[] = array (
	'label' => 'configuration.php '.ABIText::_('WRITABLE'),
	'state' => $cW ? 'Yes' : 'No',
	'notice' => $cW ? '' : ABIText::_('NOTICEYOUCANSTILLRESTORE'),
	'optional' => true
);
$lists['phpOptions'] = & $phpOptions;

// Process requirements
$requirementsMet = true;
foreach($phpOptions as $option)
{
	if(!isset($option['optional']))
	{
		$requirementsMet = $requirementsMet && $option['state'];
	}
}
$lists['requirementsMet'] = $requirementsMet;

// Check recommendations
$phpRecommended = array (
array (
ABIText::_('SAFE_MODE'),
	'safe_mode',
	false
	),
	array (
	ABIText::_('DISPLAY_ERRORS'),
	'display_errors',
	false
	),
	array (
	ABIText::_('FILE_UPLOADS'),
	'file_uploads',
	true
	),
	array (
	ABIText::_('MAGIC_QUOTES_RUNTIME'),
	'magic_quotes_runtime',
	false
	),
	array (
	ABIText::_('REGISTER_GLOBALS'),
	'register_globals',
	false
	),
	array (
	ABIText::_('OUTPUT_BUFFERING'),
	'output_buffering',
	false
	),
	array (
	ABIText::_('SESSION_AUTO_START'),
	'session.auto_start',
	false
	)
);

foreach ($phpRecommended as $setting)
{
	$lists['phpSettings'][] = array (
		'label' => $setting[0],
		'setting' => $setting[2],
		'actual' => ini_get($setting[1]) == '1',
		'state' => (ini_get($setting[1]) == 1) == $setting[2]
	);
}

$abspath = realpath(JPATH_SITE);
if(empty($abspath)) {
	$abspath = ''; // The server reports screwed up directories; this setting will work on the restored site...
} else {
	$abspath .= DIRECTORY_SEPARATOR;
}

// Check writable directories - Temporary
$tmpDir = $configuration->get('tmp_path', JPATH_SITE.'/tmp');
if(!@is_dir($tmpDir)) $tmpDir = $abspath.'tmp';
$directories[] = array(
	'label'		=> ABIText::_('TMP_DIR'),
	'directory'	=> $tmpDir,
	'writable'	=> @is_writable($tmpDir)
);

// Check writable directories - Log
$logDir = $configuration->get('log_path', JPATH_SITE.'/logs');
if(!@is_dir($logDir)) $logDir = $abspath.'logs';
$directories[] = array(
	'label'		=> ABIText::_('LOG_DIR'),
	'directory'	=> $logDir,
	'writable'	=> @is_writable($logDir)
);

// Check writable directories - Cache
$cacheDir = $abspath.'cache';
$directories[] = array(
	'label'		=> ABIText::_('CACHE_DIR'),
	'directory'	=> $cacheDir,
	'writable'	=> @is_writable($cacheDir)
);

$lists['directories'] = $directories;

// Pass the results to the global $view array
$storage =& ABIStorage::getInstance();
$output =& ABIOutput::getInstance();
$view = $lists;

// Is the storage working? If not, requirements are not met and we show an error
// message.
$view['isStorageWorking'] = $storage->isStorageWorking();
if(!$view['isStorageWorking']) {
	$requirementsMet = false;
	$output->setError(ABIText::_('ERROR_STORAGE_NOT_WORKING').'<br/><a href="https://www.akeebabackup.com/documentation/troubleshooter/abisession.html" target="_blank">'.ABIText::_('GENERIC_HELPME_CLICKHERE').'</a>');
}

// Pass on the automation information
$automation =& ABIAutomation::getInstance();

// Set some output parameters - The previous/next buttons
if($requirementsMet)
{
	$output->setButtons(null,"submitForm('dbnext')");
	if($automation->hasAutomation())
	{
		$output->setAutomation('submitForm("dbnext");');
	}
}
else
{
	$output->setButtons(null,"submitForm('index')");
}

// Clear the storage and set last shown step to 'index' (this page)
$storage->reset();
$storage->set('step','index');
$storage->set('installerflag','1');