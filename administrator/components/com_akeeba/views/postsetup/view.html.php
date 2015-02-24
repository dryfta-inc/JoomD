<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 3.3.b1
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * MVC View for Profiles management
 *
 */
class AkeebaViewPostsetup extends JView
{
	public function display($tpl = null)
	{
		JToolBarHelper::title(JText::_('AKEEBA').': <small>'.JText::_('AKEEBA_POSTSETUP').'</small>','akeeba');
		
		// Add a spacer, a help button and show the template
		JToolBarHelper::spacer();
		
		$this->_setSRPStatus();
		$this->_setAutoupdateStatus();
		$this->_setConfWizStatus();

		AkeebaHelperIncludes::includeMedia(false);

		parent::display($tpl);
	}
	
	private function _setAutoupdateStatus()
	{
		if($this->_setConfWizStatus()) {
			$this->assign('enableautoupdate', true);
			return;
		}
		
		$db = JFactory::getDBO();
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$db->setQuery("SELECT `enabled` FROM `#__extensions` WHERE element='oneclickaction' AND folder='system'");
		} else {
			$db->setQuery("SELECT `published` FROM `#__plugins` WHERE element='oneclickaction' AND folder='system'");
		}
		$enabledOCA = $db->loadResult();
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$db->setQuery("SELECT `enabled` FROM `#__extensions` WHERE element='akeebaupdatecheck' AND folder='system'");
		} else {
			$db->setQuery("SELECT `published` FROM `#__plugins` WHERE element='akeebaupdatecheck' AND folder='system'");
		}
		$enabledAUC = $db->loadResult();
		
		if(!AKEEBA_PRO) {
			$enabledAUC = false;
			$enabledOCA = false;
		}
		
		$this->assign('enableautoupdate', $enabledAUC && $enabledOCA);
	}
	
	private function _setSRPStatus()
	{
		if($this->_setConfWizStatus()) {
			$this->assign('enablesrp', true);
			return;
		}
		
		$db = JFactory::getDBO();
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$db->setQuery("SELECT `enabled` FROM `#__extensions` WHERE element='srp' AND folder='system'");
		} else {
			$db->setQuery("SELECT `published` FROM `#__plugins` WHERE element='srp' AND folder='system'");
		}
		$enableSRP = $db->loadResult();
		
		if(!AKEEBA_PRO) {
			$enableSRP = false;
		}
		
		$this->assign('enablesrp', $enableSRP ? true : false);	
	}
	
	private function _setConfWizStatus()
	{
		static $enableconfwiz;
		
		if(empty($enableconfwiz)) {
			$component =& JComponentHelper::getComponent( 'com_akeeba' );
			if(is_object($component->params) && ($component->params instanceof JRegistry)) {
				$params = $component->params;
			} else {
				$params = new JParameter($component->params);
			}
			$lv = $params->get( 'lastversion', '' );
			
			$enableconfwiz = empty($lv);
		}
		
		$this->assign('enableconfwiz', $enableconfwiz);
		return $enableconfwiz;
	}
}