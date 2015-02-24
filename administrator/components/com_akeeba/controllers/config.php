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
 * The Configuration Editor controller class
 *
 */
class AkeebaControllerConfig extends JController
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

	/**
	 * Handle the apply task which saves settings and shows the editor again
	 *
	 */
	public function apply()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
		}
		
		// Get the var array from the request
		$var = JRequest::getVar('var', array(), 'default', 'array');
		// Make it into Akeeba Engine array format
		$data = array();
		foreach($var as $key => $value)
		{
			$data[$key] = $value;
		}
		// Forbid stupidly selecting the site's root as the output or temporary directory
		if( array_key_exists('akeeba.basic.output_directory', $data) )
		{
			$folder = $data['akeeba.basic.output_directory'];
			$folder = AEUtilFilesystem::translateStockDirs( $folder, true, true );

			$check = AEUtilFilesystem::translateStockDirs( '[SITEROOT]', true, true );

			if($check == $folder)
			{
				JError::raiseWarning(503, JText::_('CONFIG_OUTDIR_ROOT'));
				$data['akeeba.basic.output_directory'] = '[DEFAULT_OUTPUT]';
			}
		}

		// Merge it
		$config =& AEFactory::getConfiguration();
		$config->mergeArray($data, false, false);
		// Save configuration
		AEPlatform::getInstance()->save_configuration();

		$this->setRedirect(JURI::base().'index.php?option='.JRequest::getCmd('option').'&view=config', JText::_('CONFIG_SAVE_OK'));
	}

	/**
	 * Handle the save task which saves settings and returns to the cpanel
	 *
	 */
	public function save()
	{
		$this->apply();
		$this->setRedirect(JURI::base().'index.php?option='.JRequest::getCmd('option'), JText::_('CONFIG_SAVE_OK'));
	}

	/**
	 * Handle the cancel task which doesn't save anything and returns to the cpanel
	 *
	 */
	public function cancel()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
		}
		
		$this->setRedirect(JURI::base().'index.php?option='.JRequest::getCmd('option'));
	}
	
	/**
	 * Tests the validity of the FTP connection details
	 */
	public function testftp()
	{
		$model = $this->getModel('Config','AkeebaModel');
		$model->setState('host', JRequest::getVar('host'));
		$model->setState('port', JRequest::getVar('port'));
		$model->setState('user', JRequest::getVar('user'));
		$model->setState('pass', JRequest::getVar('pass'));
		$model->setState('initdir', JRequest::getVar('initdir'));
		$model->setState('usessl', JRequest::getVar('usessl') == 'true');
		$model->setState('passive', JRequest::getVar('passive') == 'true');
		
		@ob_end_clean();
		echo '###'.json_encode( $model->testFTP() ).'###';
		flush();
		JFactory::getApplication()->close();
	}
	
	/**
	 * Tests the validity of the SFTP connection details
	 */
	public function testsftp()
	{
		$model = $this->getModel('Config','AkeebaModel');
		$model->setState('host', JRequest::getVar('host'));
		$model->setState('port', JRequest::getVar('port'));
		$model->setState('user', JRequest::getVar('user'));
		$model->setState('pass', JRequest::getVar('pass'));
		$model->setState('initdir', JRequest::getVar('initdir'));
		
		@ob_end_clean();
		echo '###'.json_encode( $model->testSFTP() ).'###';
		flush();
		JFactory::getApplication()->close();
	}
}