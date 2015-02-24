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
 

class JoomdViewJoomd extends JView
{
    
    function display($tpl = null)
    {
		
		$document =  JFactory::getDocument();
		
		$document->addScript('https://www.google.com/jsapi');
		
		$document->addScriptDeclaration('if(typeof google !== "undefined") google.load("visualization", "1", {packages:["corechart"]});');
				
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$toolbar->title( '&nbsp;', 'joomd' );
		$toolbar->url(JText::_('SYNCEMAIL'), 'index.php?option=com_joomd&task=syncemail', 'syncemail');
		$toolbar->help(JText::_('HELP'));
		
		$icons = $this->get('Icons');
		$this->assignRef('icons', $icons);
		
		$accordion = Joomdui::getAccordion();
		$this->assignRef('accordion', $accordion);
		
		$panel = $this->get('Panel');
		$this->assignRef('panel', $panel);
		
		parent::display($tpl);
		        
    }
  
  
}
