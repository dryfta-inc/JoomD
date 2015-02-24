<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: ftpbrowser.php 514 2011-03-22 13:33:12Z nikosdion $
 * @since 2.2
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * Folder bowser controller
 *
 */
class AkeebaControllerFtpbrowser extends JController
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

	public function display()
	{
		$model = $this->getModel('Ftpbrowser','AkeebaModel');

		// Grab the data and push them to the model
		$model->host = JRequest::getString('host','');
		$model->port = JRequest::getInt('port',21);
		$model->passive = JRequest::getInt('passive',1);
		$model->ssl = JRequest::getInt('ssl',0);
		$model->username = JRequest::getVar('username','');
		$model->password = JRequest::getVar('password','');
		$model->directory = JRequest::getVar('directory', '');

		$ret = $model->doBrowse();

		@ob_end_clean();
		echo '###'.json_encode($ret).'###';
		flush();
		JFactory::getApplication()->close();
	}
}