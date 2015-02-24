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

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Control Panel controller class
 *
 */
class AkeebaControllerCpanel extends JController
{
	public function  __construct($config = array()) {
		parent::__construct($config);
		if(AKEEBA_JVERSION=='16')
		{
			// Access check, Joomla! 1.6 style.
			$user = JFactory::getUser();
			if (!$user->authorise('core.manage', 'com_akeeba')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				$this->redirect();
			}
		}
	}

	/**
	 * Displays the Control Panel (main page)
	 * Accessible at index.php?option=com_akeeba
	 *
	 */
	public function display()
	{
		$registry =& AEFactory::getConfiguration();

		// Invalidate stale backups
		AECoreKettenrad::reset( array('global'=>true,'log'=>false,'maxrun' => 180) );

		// Just in case the reset() loaded a stale configuration...
		AEPlatform::getInstance()->load_configuration();
		
		// Let's make sure the temporary and output directories are set correctly and writable...
		$wizmodel = JModel::getInstance('Confwiz','AkeebaModel');
		$wizmodel->autofixDirectories();
		
		// Check if we need to toggle the settings encryption feature
		$model = JModel::getInstance('Cpanel','AkeebaModel');
		$model->checkSettingsEncryption();
		// Update the magic component parameters
		$model->updateMagicParameters();
		$model->applyJoomlaExtensionUpdateChanges();
		
		// Check the last installed version
		$versionLast = null;
		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/akeeba.lastversion.php')) {
			include_once JPATH_COMPONENT_ADMINISTRATOR.'/akeeba.lastversion.php';
			if(defined('AKEEBA_LASTVERSIONCHECK')) $versionLast = AKEEBA_LASTVERSIONCHECK;
		}
		if(is_null($versionLast)) {
			$component =& JComponentHelper::getComponent( 'com_akeeba' );
			if(is_object($component->params) && ($component->params instanceof JRegistry)) {
				$params = $component->params;
			} else {
				$params = new JParameter($component->params);
			}
			$versionLast = $params->get('lastversion','');
		}
		if(version_compare(AKEEBA_VERSION, $versionLast, 'ne') || empty($versionLast)) {
			$this->setRedirect('index.php?option=com_akeeba&view=postsetup');
			return;
		}

		// Display the panel
		parent::display();
	}

	public function switchprofile()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
		}
		
		$newProfile = JRequest::getInt('profileid', -10);

		if(!is_numeric($newProfile) || ($newProfile <= 0))
		{
			$this->setRedirect(JURI::base().'index.php?option='.JRequest::getCmd('option'), JText::_('PANEL_PROFILE_SWITCH_ERROR'), 'error' );
			return;
		}

		$session =& JFactory::getSession();
		$session->set('profile', $newProfile, 'akeeba');
		$this->setRedirect(JURI::base().'index.php?option='.JRequest::getCmd('option'), JText::_('PANEL_PROFILE_SWITCH_OK'));
	}


}