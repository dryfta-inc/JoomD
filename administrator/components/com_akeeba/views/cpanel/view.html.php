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
 * Akeeba Backup Control Panel view class
 *
 */
class AkeebaViewCpanel extends JView
{
	function display()
	{
		$selfhealModel = JModel::getInstance('Selfheal','AkeebaModel');
		$schemaok = $selfhealModel->healSchema();
		$this->assign('schemaok', $schemaok);		
		
		$registry =& AEFactory::getConfiguration();
		// Set the toolbar title; add a help button
		JToolBarHelper::title(JText::_('AKEEBA').':: <small>'.JText::_('AKEEBA_CONTROLPANEL').'</small>','akeeba');
		//JToolBarHelper::preferences('com_akeeba', '500', '660');

		if($schemaok) {
			// Add submenus (those nifty text links below the toolbar!)
			// -- Configuration
			$link = JURI::base().'index.php?option='.JRequest::getCmd('option').'&view=config';
			JSubMenuHelper::addEntry(JText::_('CONFIGURATION'), $link);

			// -- Backup Now
			$link = JURI::base().'index.php?option='.JRequest::getCmd('option').'&view=backup';
			JSubMenuHelper::addEntry(JText::_('BACKUP'), $link);
			// -- Administer Backup Files
			$link = JURI::base().'index.php?option='.JRequest::getCmd('option').'&view=buadmin';
			JSubMenuHelper::addEntry(JText::_('BUADMIN'), $link);
			// -- View log
			$link = JURI::base().'index.php?option='.JRequest::getCmd('option').'&view=log';
			JSubMenuHelper::addEntry(JText::_('VIEWLOG'), $link);

			// Load the helper classes
			$this->loadHelper('utils');
			$this->loadHelper('status');
			$statusHelper = AkeebaHelperStatus::getInstance();

			// Load the model
			if(!class_exists('AkeebaModelStatistics')) JLoader::import('models.statistics', JPATH_COMPONENT_ADMINISTRATOR);
			$model =& $this->getModel();
			$statmodel = new AkeebaModelStatistics();

			$this->assign('icondefs', $model->getIconDefinitions()); // Icon definitions
			$this->assign('profileid', $model->getProfileID()); // Active profile ID
			$this->assign('profilelist', $model->getProfilesList()); // List of available profiles
			$this->assign('statuscell', $statusHelper->getStatusCell() ); // Backup status
			$this->assign('detailscell', $statusHelper->getQuirksCell() ); // Details (warnings)
			$this->assign('statscell', $statmodel->getLatestBackupDetails() );

			$this->assign('fixedpermissions', $model->fixMediaPermissions() ); // Fix media/com_akeeba permissions
			
			// Add live help
			AkeebaHelperIncludes::addHelp();
		}
		
		// Add references to CSS and JS files
		AkeebaHelperIncludes::includeMedia(false);

		parent::display();
	}
}