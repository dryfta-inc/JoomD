<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 2, or later
 * @version $Id$
 * @since 1.3
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
if(!version_compare($version, '5.0.0', '>='))
{
	return JError::raise(E_ERROR, 500, 'PHP 4 is not supported by Akeeba Backup');
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

if(!version_compare( JVERSION, '1.6.0', 'ge' )) {
	define('AKEEBA_JVERSION','15');
} else {
	define('AKEEBA_JVERSION','16');
}

if(!defined('AKEEBAENGINE'))
{
	define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
	define('AKEEBAROOT', JPATH_ROOT.'/administrator/components/com_akeeba/akeeba'); 
	define('AKEEBAPLATFORM', 'joomla15'); // So that platform-specific stuff can get done!
}

if(!defined('JPATH_COMPONENT_ADMINISTRATOR'))
{
	define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR.'/components/com_akeeba' );
}

require_once(JPATH_SITE.'/administrator/components/com_akeeba/version.php');

// Apply profile selection, if any
$profile = JRequest::getInt('profile',1);
if(!is_numeric($profile)) $profile = 1;
$session =& JFactory::getSession();
$session->set('profile', $profile, 'akeeba');
JRequest::setVar('profile', $profile);

// Get the view and controller from the request, or set to default if they weren't set
JRequest::setVar('view', JRequest::getCmd('view','backup'));
JRequest::setVar('c', JRequest::getCmd('view')); // Black magic: Get controller based on the selected view

// Black Magic II: merge the default translation with the current translation
$jlang =& JFactory::getLanguage();
$jlang->load('com_akeeba', JPATH_SITE, 'en-GB', true);
$jlang->load('com_akeeba', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_akeeba', JPATH_SITE, null, true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, null, true);

// Preload the factory
jimport('joomla.filesystem.file');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/akeeba/factory.php';

// Load the utils helper library
AEPlatform::getInstance()->load_version_defines();

// Load the appropriate controller
$c = JRequest::getCmd('c','cpanel');
$path = JPATH_COMPONENT.'/controllers/'.$c.'.php';
jimport('joomla.filesystem.file');
if(JFile::exists($path))
{
	// The requested controller exists and there you load it...
	require_once($path);
}
else
{
	// Hmm... an invalid controller was passed
	JError::raiseError('500',JText::_('Unknown controller'));
}

// Instanciate and execute the controller
jimport('joomla.utilities.string');
$c = 'AkeebaController'.ucfirst($c);
$controller = new $c();
$controller->execute(JRequest::getCmd('task','display'));

// Redirect
$controller->redirect();