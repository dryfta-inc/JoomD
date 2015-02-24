<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: acl.php 681 2011-06-01 08:50:04Z nikosdion $
 * @since 3.2.1
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

class AkeebaControllerAcl extends JController
{
	public function  __construct($config = array()) {
		parent::__construct($config);
		if(AKEEBA_JVERSION!='16')
		{
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
		parent::display();
	}
	
	public function toggle()
	{
		$userID = JRequest::getInt('id', 0);
		$axo = JRequest::getCmd('axo','');
		
		$canDo = true;
		if(empty($userID) || empty($axo)) {
			$canDo = false;
		} else {
			$user = JFactory::getUser($userID);
			if(($user->gid < 23) || ($user->gid > 25)) $canDo = false;
		}
		
		if(!in_array($axo,array('backup','configure','download'))) {
			$canDo = false;
		}
		
		if(!$canDo) {
			$this->setRedirect('index.php?option=com_akeeba&view=acl');
			return JError::raiseWarning(403, 'Invalid parameters');
			$this->redirect();
		}
		
		$model = JModel::getInstance('Acl','AkeebaModel');
		$permissions = array();
		$permissions['backup'] = $model->authorizeUser('backup',$userID) ? 1 : 0;
		$permissions['download'] = $model->authorizeUser('download',$userID) ? 1 : 0;
		$permissions['configure'] = $model->authorizeUser('configure',$userID) ? 1 : 0;
		
		$permissions[$axo] = $permissions[$axo] ? 0 : 1;

		$p = json_encode($permissions);
		
		$db = JFactory::getDBO();
		$sql = 'REPLACE INTO `#__ak_acl` VALUES('.$db->Quote($userID).','.$db->Quote($p).')';
		$db->setQuery($sql);
		$db->query();
		
		$this->setRedirect('index.php?option=com_akeeba&view=acl');
		$this->redirect();
	}
}