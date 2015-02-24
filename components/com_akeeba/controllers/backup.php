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

defined('AKEEBA_BACKUP_ORIGIN') or define('AKEEBA_BACKUP_ORIGIN','frontend');

// Load framework base classes
jimport('joomla.application.component.controller');

class AkeebaControllerBackup extends JController
{
	public function display()
	{
		// Check permissions
		$this->_checkPermissions();
		// Set the profile
		$this->_setProfile();

		// Start the backup
		jimport('joomla.utilities.date');
		AECoreKettenrad::reset();
		$memory_filename = AEUtilTempvars::get_storage_filename(AKEEBA_BACKUP_ORIGIN);
		@unlink($memory_filename);

		$kettenrad =& AECoreKettenrad::load(AKEEBA_BACKUP_ORIGIN);
		$user =& JFactory::getUser();
		$userTZ = $user->getParam('timezone',0);
		$dateNow = new JDate();
		$dateNow->setOffset($userTZ);
		if( AKEEBA_JVERSION == '16' ) {
			$description = JText::_('BACKUP_DEFAULT_DESCRIPTION').' '.$dateNow->format(JText::_('DATE_FORMAT_LC2'), true);
		} else {
			$description = JText::_('BACKUP_DEFAULT_DESCRIPTION').' '.$dateNow->toFormat(JText::_('DATE_FORMAT_LC2'));
		}
		$options = array(
			'description'	=> $description,
			'comment'		=> ''
		);
		$kettenrad->setup($options);
		$array = $kettenrad->tick();
		$array = $kettenrad->tick();
		AECoreKettenrad::save(AKEEBA_BACKUP_ORIGIN);
		
		if($array['Error'] != '')
		{
			// An error occured
			die('500 ERROR -- '.$array['Error']);
		}
		else
		{
			$noredirect = JRequest::getInt('noredirect', 0);
			if($noredirect != 0)
			{
				@ob_end_clean();
				echo "301 More work required";
				flush();
				JFactory::getApplication()->close();
			}
			else
			{
				$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=backup&task=step&key='.JRequest::getVar('key').'&profile='.JRequest::getInt('profile',1));
			}
		}
	}

	public function step()
	{
		// Check permissions
		$this->_checkPermissions();
		// Set the profile
		$this->_setProfile();

		$kettenrad =& AECoreKettenrad::load(AKEEBA_BACKUP_ORIGIN);
		$array = $kettenrad->tick();
		AECoreKettenrad::save(AKEEBA_BACKUP_ORIGIN);

		if($array['Error'] != '')
		{
			@ob_end_clean();
			echo '500 ERROR -- '.$array['Error'];
			flush();
			JFactory::getApplication()->close();
		}
		elseif($array['HasRun'] == false)
		{
			// All done
			AEFactory::nuke();
			AEUtilTempvars::reset();
			@ob_end_clean();
			echo '200 OK';
			flush();
			JFactory::getApplication()->close();
		}
		else
		{
			$noredirect = JRequest::getInt('noredirect', 0);
			if($noredirect != 0)
			{
				@ob_end_clean();
				echo "301 More work required";
				flush();
				JFactory::getApplication()->close();
			}
			else
			{
				$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=backup&task=step&key='.JRequest::getVar('key').'&profile='.JRequest::getInt('profile',1));
			}
		}
	}
	/**
	 * Check that the user has sufficient permissions, or die in error
	 *
	 */
	private function _checkPermissions()
	{
		// Is frontend backup enabled?
		$febEnabled = AEPlatform::getInstance()->get_platform_configuration_option('frontend_enable', 0) != 0; 
		if(!$febEnabled)
		{
			@ob_end_clean();
			echo '403 '.JText::_('ERROR_NOT_ENABLED');
			flush();
			JFactory::getApplication()->close();
		}

		// Is the key good?
		$key = JRequest::getVar('key');
		$validKey=AEPlatform::getInstance()->get_platform_configuration_option('frontend_secret_word','');
		$validKeyTrim = trim($validKey);
		if( ($key != $validKey) || (empty($validKeyTrim)) )
		{
			@ob_end_clean();
			echo '403 '.JText::_('ERROR_INVALID_KEY');
			flush();
			JFactory::getApplication()->close();
		}
	}

	private function _setProfile()
	{
		// Set profile
		$profile = JRequest::getInt('profile',1);
		if(!is_numeric($profile)) $profile = 1;
		$session =& JFactory::getSession();
		$session->set('profile', $profile, 'akeeba');

		AEPlatform::getInstance()->load_configuration($profile);
	}
}