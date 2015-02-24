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
 

class JoomdViewCss extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$layout 		= JRequest::getCmd('layout', '');
		
		$config = Joomd::getConfig();
		
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$abase = JRequest::getInt('abase', 0);
		$this->assignRef('abase', $abase);
		
		$toolbar->title( JText::_( 'CSS' ), 'css' );
		$toolbar->button('custom', JText::_('SAVE'), 'save', 'save');
		$toolbar->help('help');
		
		jimport('joomla.filesystem.file');
		
		$file = JPATH_SITE.DS.'components/com_joomd/templates/'.$config->theme.'/css/style.css';
		
		if(jfile::exists($file))	{
			
			$content = jfile::read($file);
			
		}
		else
			$content = '';
			
		$this->assignRef('content', $content);

		parent::display($tpl);
		
        
    }
  
  
}
