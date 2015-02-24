<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
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
if(!version_compare($version, '5.2.0', '>='))
{
	return JError::raise(E_ERROR, 500, 'PHP 4.x, 5.0 and 5.1 is no longer supported by Akeeba Backup.','The version of PHP used on your site is obsolete and contains known security vulenrabilities. Moreover, it is missing features required by Akeeba Backup to work properly or at all. Please ask your host to upgrade your server to the latest PHP 5.2 or 5.3 release. Thank you!');
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

// Joomla! 1.6 detection
jimport('joomla.filesystem.file');
if(!version_compare( JVERSION, '1.6.0', 'ge' )) {
	define('AKEEBA_JVERSION','15');
} else {
	define('AKEEBA_JVERSION','16');
}

if(!defined('AKEEBAENGINE')) {
	define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
	define('AKEEBAROOT', dirname(__FILE__).'/akeeba'); 
	define('AKEEBAPLATFORM', 'joomla15'); // So that platform-specific stuff can get done!
}

// Setup Akeeba's ACLs, honoring laxed permissions in component's parameters, if set
if(AKEEBA_JVERSION == '15')
{
	$component =& JComponentHelper::getComponent( 'com_akeeba' );
	$params = new JParameter($component->params);
	$acl =& JFactory::getACL();
	if(method_exists($acl, 'addACL'))
	{
		$min_acl = $params->get('minimum_acl_group','super administrator');
		$acl->addACL('com_akeeba', 'manage', 'users', 'super administrator' );
		switch($min_acl)
		{
			case 'administrator':
				$acl->addACL('com_akeeba', 'manage', 'users', 'administrator' );
				break;

			case 'manager':
				$acl->addACL('com_akeeba', 'manage', 'users', 'administrator' );
				$acl->addACL('com_akeeba', 'manage', 'users', 'manager' );
				break;
		}
	}
}
else
{
	// Access check, Joomla! 1.6 style.
	$user = JFactory::getUser();
	if (!$user->authorise('core.manage', 'com_akeeba')) {
		return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}
// Make sure we have a profile set throughout the component's lifetime
$session =& JFactory::getSession();
$profile_id = $session->get('profile', null, 'akeeba');
if(is_null($profile_id))
{
	// No profile is set in the session; use default profile
	$session->set('profile', 1, 'akeeba');
}

// Get the view and controller from the request, or set to default if they weren't set
JRequest::setVar('view', JRequest::getCmd('view','cpanel'));
JRequest::setVar('c', JRequest::getCmd('view','cpanel')); // Black magic: Get controller based on the selected view

// Load the factory
require_once JPATH_COMPONENT_ADMINISTRATOR.'/akeeba/factory.php';
// Load the Akeeba Backup configuration and check user access permission
$registry =& AEFactory::getConfiguration();
AEPlatform::getInstance()->load_configuration();
unset($registry);

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/includes.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/escape.php';

// Merge the default translation with the current translation
$jlang =& JFactory::getLanguage();
// Front-end translation
$jlang->load('com_akeeba', JPATH_SITE, 'en-GB', true);
$jlang->load('com_akeeba', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_akeeba', JPATH_SITE, null, true);
// Back-end translation
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, null, true);

// Load the utils helper library
AEPlatform::getInstance()->load_version_defines();

// Create a versioning tag for our static files
$staticFilesVersioningTag = md5(AKEEBA_VERSION.AKEEBA_DATE.AKEEBA_JVERSION);
define('AKEEBAMEDIATAG', $staticFilesVersioningTag);

// If JSON functions don't exist, load our compatibility layer
if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
{
	require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/jsonlib.php';
}

// Handle Live Update requests
require_once JPATH_COMPONENT_ADMINISTRATOR.'/liveupdate/liveupdate.php';
if(JRequest::getCmd('view','') == 'liveupdate') {
	LiveUpdate::handleRequest();
	return;
}

// Load the appropriate controller
$c = JRequest::getCmd('c','cpanel');
$path = JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.$c.'.php';
$alt_path = JPATH_COMPONENT_ADMINISTRATOR.'/plugins/controllers/'.$c.'.php';
if(JFile::exists($alt_path))
{
	// The requested controller exists and there you load it...
	require_once($alt_path);
}
elseif(JFile::exists($path))
{
	require_once($path);
}
else
{
	// Hmm... an invalid controller was passed
	JError::raiseError('500',JText::_('Unknown controller').' '.$c);
}

// Instanciate and execute the controller
jimport('joomla.utilities.string');
$c = 'AkeebaController'.ucfirst($c);
$controller = new $c();
if(AKEEBA_JVERSION=='15')
{
	$controller->setAccessControl('com_akeeba','manage'); // Enforce Joomla!'s ACL
}
$controller->execute(JRequest::getCmd('task','display'));

// Redirect
$controller->redirect();