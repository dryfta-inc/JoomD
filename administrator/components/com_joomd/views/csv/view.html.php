<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD CSV Application
# ------------------------------------------------------------------------
# author    Noorullah Kalim - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 

class JoomdViewCsv extends JView
{    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_joomd/assets/css/csv.css');
		
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$toolbar->title( JText::_( 'CSV_MANAGER' ), 'csv' );
		
		$type = $this->get('type');
	    $this->assignRef('type', $type);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
					
		parent::display($tpl);
        
    }
  
  
}
