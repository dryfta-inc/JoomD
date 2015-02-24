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
 * MVC View for Log
 *
 */
class AkeebaViewLog extends JView
{
	public function display($tpl = null)
	{
		// Add toolbar buttons
		JToolBarHelper::title(JText::_('AKEEBA').': <small>'.JText::_('VIEWLOG').'</small>','akeeba');
		JToolBarHelper::back('AKEEBA_CONTROLPANEL', 'index.php?option='.JRequest::getCmd('option'));
		JToolBarHelper::spacer();
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'../media/com_akeeba/theme/akeebaui.css?'.AKEEBAMEDIATAG);

		// Add live help
		AkeebaHelperIncludes::addHelp();

		// Get a list of log names
		if(!class_exists('AkeebaModelLog')) JLoader::import('models.log', JPATH_COMPONENT_ADMINISTRATOR);
		$model = new AkeebaModelLog();
		$this->assign('logs', $model->getLogList());

		$tag = JRequest::getCmd('tag',null);
		if(empty($tag)) $tag = null;
		$this->assign('tag', $tag);

		// Get profile ID
		$profileid = AEPlatform::getInstance()->get_active_profile();
		$this->assign('profileid', $profileid);

		// Get profile name
		if(!class_exists('AkeebaModelProfiles')) JLoader::import('models.profiles', JPATH_COMPONENT_ADMINISTRATOR);
		$model = new AkeebaModelProfiles();
		$model->setId($profileid);
		$profile_data = $model->getProfile();
		$this->assign('profilename', $profile_data->description);

		AkeebaHelperIncludes::includeMedia(false);

		parent::display($tpl);
	}
}