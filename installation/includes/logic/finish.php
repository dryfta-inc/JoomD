<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Logic: Finish up
 */

defined('_ABI') or die('Direct access is not allowed');

require_once(JPATH_INSTALLATION.'/includes/db.php');
require_once(JPATH_INSTALLATION.'/includes/ftp.php');
require_once(JPATH_INSTALLATION.'/includes/configuration.php');

global $view;

/**
 * Changes the email and password of the administrator user
 * @return bool True on success, false otherwise
 */
function changeAdminUser()
{
	// Get the request parameters
	$uid = getParam('sauser', 62);
	$password = getParam('sapass1', '');
	$password_confirm = getParam('sapass2', '');
	$email = getParam('saemail', '');

	// Bail out 1 - passwords don't match
	if( $password != $password_confirm ) return;

	// Bail out 2 - password empty
	if( empty($password) ) return;
	
	// Get a connection to the main site database
	$storage =& ABIStorage::getInstance();
	$databases = $storage->get('databases');
	$dbkeys = array_keys($databases);
	$firstkey = array_shift($dbkeys);
	$d = $databases[$firstkey];
	$db =& ABIDatabase::getInstance($d['dbtype'], $d['dbhost'], $d['dbuser'], $d['dbpass'],
		$d['dbname'], $d['prefix']);
	$db->connect(); // Required to initiate the actual connection to the db server!
	unset($d); unset($databases);

	// Generate encrypted password string
	$salt = genRandomPassword(32);
	$crypt = md5($password.$salt);
	$cryptpass = $crypt.':'.$salt;

	// Update database
	$query = 'UPDATE `#__users` SET `password` = "'.$db->escape($cryptpass).
		'", `email` = "'.$db->escape($email).'" WHERE `id` = "'.(int)$uid.'"';
	$res = $db->query($query);

	return $res;
}

function genRandomPassword($length = 8)
{
	$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$len = strlen($salt);
	$makepass = '';

	$stat = @stat(__FILE__);
	if(empty($stat) || !is_array($stat)) $stat = array(php_uname());

	mt_srand(crc32(microtime() . implode('|', $stat)));

	for ($i = 0; $i < $length; $i ++) {
		$makepass .= $salt[mt_rand(0, $len -1)];
	}

	return $makepass;
}

$storage =& ABIStorage::getInstance();
$valid = $storage->get('installerflag', '0');
if(!$valid) {
	$output->setError(ABIText::_('ERROR_STORAGE_NOT_WORKING').'<br/><a href="https://www.akeebabackup.com/documentation/troubleshooter/abisession.html" target="_blank">'.ABIText::_('GENERIC_HELPME_CLICKHERE').'</a>');
}

// 1. Change the Super Administrator settings
changeAdminUser();

// 2. Update the configuration
$configuration =& ABIConfiguration::getInstance();
// FTP Settings
$configuration->set('ftp_enable', in_array(getParam('ftp_enable'), array('checked','on','true')) ? '1' : '0' );
$configuration->set('ftp_host', getParam('ftp_host'));
$configuration->set('ftp_port', getParam('ftp_port'));
$configuration->set('ftp_user', getParam('ftp_user'));
$configuration->set('ftp_pass', getParam('ftp_pass'));
$configuration->set('ftp_root', getParam('ftp_root'));
// Site Parameters
$configuration->set('sitename', getParam('sitename'));
$configuration->set('mailfrom', getParam('mailfrom'));
$configuration->set('fromname', getParam('fromname'));
// Fine tuning values
$configuration->set('tmp_path', getParam('tmp_path'));
$configuration->set('log_path', getParam('log_path'));
$configuration->set('live_site', getParam('live_site',''));
$configuration->set('cookie_domain', getParam('cookie_domain',''));
$configuration->set('cookie_path', getParam('cookie_path',''));
// Create a new, random, secret word
$configuration->set('secret', genRandomPassword(16));

// 3. Check if we can write to the configuration
$confdata = $configuration->getConfiguration();
$view['confwritten'] = false;
// First try with PHP
if(@is_writable(JPATH_SITE.'/configuration.php'))
{
	// It's writable, so let's write it straight away using PHP only
	if(function_exists('file_put_contents'))
	{
		// We can do this easily w/ file_put_contents
		if(@file_put_contents(JPATH_SITE.'/configuration.php', $confdata) !== false)
		{
			$view['confwritten'] = true;
		}
	}
	else
	{
		// Crap... the long way around...
		$file = @fopen(JPATH_SITE.'/configuration.php', 'w');
		if($file !== false)
		{
			@fwrite($file, $confdata);
		}
		fclose($file);
		$view['confwritten'] = true;
	}
}
// If this wasn't possible, try FTP
if(($configuration->get('ftp_enable') == 1) && !$view['confwritten'])
{
	// At least we have FTP. Let's try this one out
	$ftp =& ABIFtp::getInstance(getParam('ftp_host'),getParam('ftp_port'),getParam('ftp_user'),getParam('ftp_pass'),getParam('ftp_root'));
	if($ftp->connect(false))
	{
		if($ftp->write('configuration.php', $confdata) === true)
		{
			$view['confwritten'] = true;
		}
	}
}

// 4. Gotta pass on the configuration data if we couldn't write it to the configuration.php
if(!$view['confwritten'])
{
	$view['confdata'] =& $confdata;
}

// 5. Update buttons etc.
$output =& ABIOutput::getInstance();
$storage =& ABIStorage::getInstance();
$output->setButtons("submitForm('setup')",null);
$output->setActiveStep('finish');
$storage->set('step', 'finish');

$automation =& ABIAutomation::getInstance();
if($automation->hasAutomation() && $view['confwritten'])
{
	$output->setAutomation("opener.onRunCleanupClick(); window.close();");
}