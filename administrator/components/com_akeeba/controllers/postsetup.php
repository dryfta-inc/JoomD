<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 3.3.b1
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

class AkeebaControllerPostsetup extends JController
{
	public function  __construct($config = array()) {
		parent::__construct($config);
		if(AKEEBA_JVERSION=='16')
		{
			// Access check, Joomla! 1.6 style.
			$user = JFactory::getUser();
			if (!$user->authorise('akeeba.configure', 'com_akeeba')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				$this->redirect();
			}
		} else {
			// Custom ACL for Joomla! 1.5
			$aclModel = JModel::getInstance('Acl','AkeebaModel');
			if(!$aclModel->authorizeUser('configure')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('Access Forbidden'));
				$this->redirect();
			}
		}
	}
	
	/**
	 * Displays the editor page
	 *
	 */
	public function display()
	{
		parent::display();
	}
	
	public function save()
	{
		$enableSRP = JRequest::getBool('srp', 0);
		$enableAutoupdate = JRequest::getBool('autoupdate', 0);
		$runConfwiz = JRequest::getBool('confwiz', 0);
		
		$db = JFactory::getDBO();
		
		if($enableSRP) {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = "UPDATE #__extensions SET enabled=1 WHERE element='srp' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=1 WHERE element='srp' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		} else {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = "UPDATE #__extensions SET enabled=0 WHERE element='srp' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=0 WHERE element='srp' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		}
		
		if($enableAutoupdate) {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = "UPDATE #__extensions SET enabled=1 WHERE element='oneclickaction' AND folder='system'";
				$db->setQuery($query);
				$db->query();
				
				$query = "UPDATE #__extensions SET enabled=1 WHERE element='akeebaupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=1 WHERE element='oneclickaction' AND folder='system'";
				$db->setQuery($query);
				$db->query();
				
				$query = "UPDATE #__plugins SET published=1 WHERE element='akeebaupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		} else {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = "UPDATE #__extensions SET enabled=0 WHERE element='oneclickaction' AND folder='system'";
				$db->setQuery($query);
				$db->query();
				
				$query = "UPDATE #__extensions SET enabled=0 WHERE element='akeebaupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=0 WHERE element='oneclickaction' AND folder='system'";
				$db->setQuery($query);
				$db->query();
				
				$query = "UPDATE #__plugins SET published=0 WHERE element='akeebaupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		}
		
		// Update last version check. DO NOT USE JCOMPONENTHELPER!
		if( version_compare(JVERSION,'1.6.0','ge') ) {
			$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__extensions').
				' WHERE '.$db->nameQuote('type').' = '.$db->Quote('component').' AND '.
				$db->nameQuote('element').' = '.$db->Quote('com_akeeba');
			$db->setQuery($sql);
		} else {
			$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
				' WHERE '.$db->nameQuote('option').' = '.$db->Quote('com_akeeba').
				" AND `parent` = 0 AND `menuid` = 0";
			$db->setQuery($sql);
		}
		$rawparams = $db->loadResult();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$params = new JRegistry();
			$params->loadJSON($rawparams);
		} else {
			$params = new JParameter($rawparams);
		}
		$params->setValue('lastversion', AKEEBA_VERSION);
		if( version_compare(JVERSION,'1.6.0','ge') )
		{
			// Joomla! 1.6
			$data = $params->toString('JSON');
			$sql = 'UPDATE `#__extensions` SET `params` = '.$db->Quote($data).' WHERE '.
				"`element` = ".$db->Quote('com_akeeba')." AND `type` = 'component'";
		}
		else
		{
			// Joomla! 1.5
			$data = $params->toString('INI');
			$sql = 'UPDATE `#__components` SET `params` = '.$db->Quote($data).' WHERE '.
				"`option` = ".$db->Quote('com_akeeba')." AND `parent` = 0 AND `menuid` = 0";
		}
		$db->setQuery($sql);
		$db->query();
		
		// Even better, create the "akeeba.lastversion.php" file with this information
		$fileData = "<"."?php\ndefined('_JEXEC') or die();\ndefine('AKEEBA_LASTVERSIONCHECK','".
			AKEEBA_VERSION."');";
		jimport('joomla.filesystem.file');
		$fileName = JPATH_COMPONENT_ADMINISTRATOR.'/akeeba.lastversion.php';
		JFile::write($fileName, $fileData);
		
		// Force reload the Live Update information
		$dummy = LiveUpdate::getUpdateInformation(true);
		
		// Run the configuration wizard if requested
		if($runConfwiz) {
			$url = 'index.php?option=com_akeeba&view=confwiz';
		} else {
			$url = 'index.php?option=com_akeeba&view=cpanel';
		}
		
		$app = JFactory::getApplication();
		$app->redirect($url);
	}
}