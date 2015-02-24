<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Logic: Site setup
 */

defined('_ABI') or die('Direct access is not allowed');

require_once(JPATH_INSTALLATION.'/includes/configuration.php');
require_once(JPATH_INSTALLATION.'/includes/db.php');

global $view, $auto_data, $configuration; // Import global variables

function autoGetVariable($key, $default = null, $isBoolean = false)
{
	global $auto_data, $configuration;

	if(isset($auto_data[$key]))
	{
		return $auto_data[$key];
	}
	else
	{
		if(is_null($default))
		{
			$default = $configuration->get($key);
		}
		if($isBoolean)
		{
			if(!is_numeric($default)) $default = ($default == 'on') || ($default == '1') ? 1 : 0;
		}
		return $default;
	}
}

// Load global Output and Storage objects
$output =& ABIOutput::getInstance();
$storage =& ABIStorage::getInstance();

$valid = $storage->get('installerflag', '0');
if(!$valid) {
	$output->setError(ABIText::_('ERROR_STORAGE_NOT_WORKING').'<br/><a href="https://www.akeebabackup.com/documentation/troubleshooter/abisession.html" target="_blank">'.ABIText::_('GENERIC_HELPME_CLICKHERE').'</a>');
}

// Load configuration
$configuration =& ABIConfiguration::getInstance();

// Load any automation information
$automation =& ABIAutomation::getInstance();
$hasAutomation = $automation->hasAutomation();
if($hasAutomation)
{
	// Get the data from the [abi] section
	$auto_data = $automation->getSection('abi');
	// Compatibility with older releases: use the [jpi4] section
	if(empty($auto_data)) $auto_data = $automation->getSection('jpi4');
}
else
{
	$auto_data = array();
}

// Get FTP settings
$ftp = array(
	'ftp_enable'	=> autoGetVariable('ftp_enable',null,true),
	'ftp_host'		=> autoGetVariable('ftp_host'),
	'ftp_port'		=> autoGetVariable('ftp_port'),
	'ftp_user'		=> autoGetVariable('ftp_user'),
	'ftp_pass'		=> autoGetVariable('ftp_pass'),
	'ftp_root'		=> autoGetVariable('ftp_root'),
);
$view['ftp'] =& $ftp;

// Site parameters
$site = array(
	'sitename'		=> autoGetVariable('sitename'),
	'mailfrom'		=> autoGetVariable('mailfrom'),
	'fromname'		=> autoGetVariable('fromname'),
	'live_site'		=> autoGetVariable('live_site',''),
	'jversion'		=> $configuration->joomlaVersion(),
	'cookie_domain'	=> autoGetVariable('cookie_domain',''),
	'cookie_path'	=> autoGetVariable('cookie_path','')
);
$view['site'] =& $site;

// Super administrator
// We begin by getting a list of super administrators
$databases = $storage->get('databases'); // Get all db definitions
$dbkeys = array_keys($databases); // Get db def's keys
$firstkey = array_shift($dbkeys);
$d = $databases[$firstkey]; // Get the first db def and connect to it
$db =& ABIDatabase::getInstance($d['dbtype'], $d['dbhost'], $d['dbuser'], $d['dbpass'],
	$d['dbname'], $d['prefix']);
unset($d); unset($databases);

// Detect Joomla! 1.6 based on whether the #__extensions table exists
$db->reset();
$sql = 'SELECT COUNT(*) FROM `#__extensions`';
$db->getAssocArray($sql);
$joomla15 = ($db->errno != 0);
$db->reset();

// Target specific tables based on the Joomla! version
if($joomla15) {
	$sql = 'SELECT `id`, `username`, `email` FROM `#__users` WHERE `usertype`="Super Administrator"';
} else {
	$sql = 'SELECT `id`, `username`, `email` FROM `#__users` AS u LEFT OUTER JOIN `#__user_usergroup_map` AS m ON m.`user_id` = u.`id` '.
		'WHERE (m.group_id = 8) OR (u.`id` <= 42)';
}
$db->setQuery($sql, false);
$sa = $db->getAssocArray();
$view['sa'] =& $sa;
$view['saselected'] = $sa[0]['id'];
$view['saemail'] = $sa[0]['email'];
$view['sapass1'] = '';
$view['sapass2'] = '';

// Override Super Administrator settings from the automation data
if($hasAutomation)
{
	$sauser = autoGetVariable('sauser',-1);
	if($sauser > 0)
	{
		// Construct a new Super Users array
		$newsa = array();
		foreach($sa as $sarecord)
		{
			if($sarecord['id'] == $sauser)
			{
				$sarecord['email'] = autoGetVariable('saemail',$sarecord['email']);
				$view['saemail'] = $sarecord['email'];
				$view['sapass1'] = autoGetVariable('sapass1','');
				$view['sapass2'] = autoGetVariable('sapass1','');
			}
			$newsa[] = $sarecord;
		}
		$sa = $newsa;
		$view['sa'] =& $sa;
	}
}

// Directories
$tmpDir = $configuration->get('tmp_path', JPATH_SITE.'/tmp');
if(!@is_dir($tmpDir)) $tmpDir = JPATH_SITE.'/tmp';
if(!@is_writable($tmpDir)) $tmpDir = JPATH_SITE.'/tmp';
$logDir = $configuration->get('log_path', JPATH_SITE.'/logs');
if(!@is_dir($logDir)) $logDir = JPATH_SITE.'/logs';
if(!@is_writable($logDir)) $logDir = JPATH_SITE.'/logs';
$dirs = array(
	'tmp_path'	=> autoGetVariable('tmp_path', $tmpDir),
	'log_path'	=> autoGetVariable('log_path', $logDir)
);
$view['dirs'] =& $dirs;

// Process any directory variables
if($hasAutomation)
{
	if( strpos($dirs['tmp_path'], '$SITEROOT') === 0 )
	{
		$dirs['tmp_path'] = JPATH_SITE.substr($dirs['tmp_path'],9);
	}
	if( strpos($dirs['log_path'], '$SITEROOT') === 0 )
	{
		$dirs['log_path'] = JPATH_SITE.substr($dirs['log_path'],9);
	}
}

// Set automation JavaScript
if($hasAutomation)
{
	$output->setAutomation('submitForm(\'finish\');');
}

$output->setButtons("submitForm('dbprev')","submitForm('finish')");
$output->setActiveStep('setup');
$storage->set('step', 'setup');