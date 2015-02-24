<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: view.html.php 691 2011-06-02 19:59:30Z nikosdion $
 * @since 3.2.1
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class AkeebaViewAcl extends JView
{
	function display()
	{
		// Set the toolbar title
		JToolBarHelper::title(JText::_('AKEEBA').':: <small>'.JText::_('AKEEBA_ACL_TITLE').'</small>','akeeba');

		// Add some buttons
		JToolBarHelper::back('AKEEBA_CONTROLPANEL', 'index.php?option='.JRequest::getCmd('option'));
		JToolBarHelper::spacer();

		// Add references to CSS and JS files
		AkeebaHelperIncludes::includeMedia(false);

		// Add live help
		AkeebaHelperIncludes::addHelp();
		
		// Get the users from manager and above
		$model = JModel::getInstance('Acl','AkeebaModel');
		$list =& $model->getUserList();
		$this->assignRef('userlist', $list);

		parent::display(JRequest::getCmd('tpl',null));
	}
}