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


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 

class JoomdViewConfig extends JView
{
    
    function display($tpl = null)
    {
		
		$document =  JFactory::getDocument();
				
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$toolbar->title( JText::_( 'JOOMDCONFIG' ), 'config' );
		
		$toolbar->cancel();
		$toolbar->button('custom', JText::_('SAVE'), 'save', 'save');
		$toolbar->help(JText::_('HELP'));
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$tabs = Joomdui::getTabs();
		$this->assignRef('tabs', $tabs);
		
		$themes = $this->get('Themes');
		$this->assignRef('themes', $themes);
		
		$config = $this->get('Config');
		$this->assignRef('config', $config);
		
		$panels = $this->get('Panels');
		$this->assignRef('panels', $panels);
		
		parent::display($tpl);
		        
    }
  
  
}
