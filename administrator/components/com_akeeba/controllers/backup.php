<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: backup.php 681 2011-06-01 08:50:04Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Backup controller class
 *
 */
class AkeebaControllerBackup extends JController
{
	public function  __construct($config = array()) {
		parent::__construct($config);
		if(AKEEBA_JVERSION=='16')
		{
			// Access check, Joomla! 1.6 style.
			$user = JFactory::getUser();
			if (!$user->authorise('akeeba.backup', 'com_akeeba')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				$this->redirect();
			}
		} else {
			// Custom ACL for Joomla! 1.5
			$aclModel = JModel::getInstance('Acl','AkeebaModel');
			if(!$aclModel->authorizeUser('backup')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('Access Forbidden'));
				$this->redirect();
			}
		}
	}

	/**
	 * Default task; shows the initial page where the user selects a profile
	 * and enters description and comment
	 *
	 */
	public function display()
	{
		$newProfile = JRequest::getInt('profileid', -10);
		if(is_numeric($newProfile) && ($newProfile > 0))
		{
			// CSRF prevention
			if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
				JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
			}
			
			$session =& JFactory::getSession();
			$session->set('profile', $newProfile, 'akeeba');
		}

		// Deactivate the menus
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	public function ajax()
	{
		$model = $this->getModel('Backup','AkeebaModel');

		$model->setState('profile',		JRequest::getInt('profileid', -10));
		$model->setState('ajax',		JRequest::getCmd('ajax', ''));
		$model->setState('description',	JRequest::getString('description','','default', null));
		$model->setState('comment',		JRequest::getString('comment','','default', 4));
		$model->setState('jpskey',		JRequest::getVar('jpskey',''));
		
		// System Restore Point backup state variables
		$model->setState('tag',			JRequest::getCmd('tag','backend'));
		$model->setState('type',		strtolower(JRequest::getCmd('type','')));
		$model->setState('name',		strtolower(JRequest::getCmd('name','')));
		$model->setState('group',		strtolower(JRequest::getCmd('group','')));
		$model->setState('customdirs',	JRequest::getVar('customdirs',array(),'default','array',2));
		$model->setState('customfiles',	JRequest::getVar('customfiles',array(),'default','array',2));
		$model->setState('extraprefixes',JRequest::getVar('extraprefixes',array(),'default','array',2));
		$model->setState('customtables',JRequest::getVar('customtables',array(),'default','array',2));
		$model->setState('skiptables',	JRequest::getVar('skiptables',array(),'default','array',2));
		$model->setState('langfiles',	JRequest::getVar('langfiles',array(),'default','array',2));
		$model->setState('xmlname',		JRequest::getString('xmlname',''));
		
		define('AKEEBA_BACKUP_ORIGIN', JRequest::getCmd('tag','backend'));
		
		$ret_array = $model->runBackup();

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($ret_array) . '###';
		flush();
		JFactory::getApplication()->close();
	}
}