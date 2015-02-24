<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
 
class JoomdViewJoomd extends JView
{
    function display($tpl = null)
    {
		$mainframe =  JFactory::getApplication();
		
		$document	=  JFactory::getDocument();
		$params =  $mainframe->getParams();
		
		$menus		=  $mainframe->getMenu();
		$menu    	= $menus->getActive();
		
		if (!$params->get( 'page_title')) {
			$params->set('page_title',	JText::_('FRONTPAGE'));
		}
		
		$abase = JRequest::getInt('abase', 0);
		
		$config = Joomd::getConfig();
		
		$this->blocks = $this->get('Blocks');
		
		$document->setTitle( $params->def('page_title') );
		
		$this->assignRef('params', $params);
		
		$this->addTemplatePath('components/com_joomd/templates/'.$config->theme.'/joomd');
		
		parent::display($tpl);

    }
}
