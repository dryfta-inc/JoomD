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
jimport('joomla.application.component.view');

/**
 * Akeeba Backup Configuration view class
 *
 */
class AkeebaViewConfig extends JView
{
	function display()
	{
		// Toolbar buttons
		JToolBarHelper::title(JText::_('AKEEBA').':: <small>'.JText::_('CONFIGURATION').'</small>','akeeba');
		JToolBarHelper::preferences('com_akeeba', '500', '660');
		JToolBarHelper::spacer();
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		
		// Add references to scripts and CSS
		AkeebaHelperIncludes::includeMedia(false);
		$media_folder = JURI::base().'../media/com_akeeba/';

		// Get a JSON representation of GUI data
		$json = AkeebaHelperEscape::escapeJS(AEUtilInihelper::getJsonGuiDefinition(),'"\\');
		$this->assignRef( 'json', $json );

		// Get profile ID
		$profileid = AEPlatform::getInstance()->get_active_profile();
		$this->assign('profileid', $profileid);

		// Get profile name
		if(!class_exists('AkeebaModelProfiles')) JLoader::import('models.profiles', JPATH_COMPONENT_ADMINISTRATOR);
		$model = new AkeebaModelProfiles();
		$model->setId($profileid);
		$profile_data = $model->getProfile();
		$this->assign('profilename', $profile_data->description);

		// Get the root URI for media files
		$this->assign( 'mediadir', AkeebaHelperEscape::escapeJS($media_folder.'theme/') );
		
		// Are the settings secured?
		if( AEPlatform::getInstance()->get_platform_configuration_option('useencryption', -1) == 0 ) {
			$this->assign('securesettings', -1);
		} elseif( !AEUtilSecuresettings::supportsEncryption() ) {
			$this->assign('securesettings', 0);
		} else {
			jimport('joomla.filesystem.file');
			$filename = JPATH_COMPONENT_ADMINISTRATOR.'/akeeba/serverkey.php';
			if(JFile::exists($filename)) {
				$this->assign('securesettings', 1);
			} else {
				$this->assign('securesettings', 0);
			}
		}
		
		// Add live help
		AkeebaHelperIncludes::addHelp();

		parent::display();
	}
}