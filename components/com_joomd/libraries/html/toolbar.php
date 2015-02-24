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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if(!class_exists('JToolBar'))
	require_once(JPATH_SITE.'/libraries/joomla/html/toolbar.php');

class JoomdToolbar extends JToolBar
{

	function __construct()
	{
		$this->bar =  JToolBar::getInstance('toolbar');
		
		$path = JPATH_SITE.DS.'components/com_joomd/libraries/html/button';
		
		$this->bar->addButtonPath($path);
	
	}
	
	function title($title, $icon = 'generic.png')
	{
		$mainframe =  JFactory::getApplication();

		//strip the extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		$html  = "<div class=\"pagetitle icon-48-$icon\">\n";
		$html .= "<h2>$title</h2>\n";
		$html .= "</div>\n";

		$mainframe->set('JComponentTitle', $html);
	}
	
	function evtitle($title, $icon = 'generic.png')
	{
		
		$mainframe =  JFactory::getApplication();
		
		//strip the extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		$html  = "<div class=\"pagetitle icon-48-$icon\">\n";
		$html .= "<h2>$title</h2>\n";
		$html .= "</div>\n";
		
		$mainframe->set('JComponentTitle', $html);
		
		return $html;
	}
	
	function render()
	{
		
		$this->bar =  JToolBar::getInstance('toolbar');
		
		return $this->bar->render();
		
	}
	
	function button($type = 'Standard', $alt = '', $task = '', $icon = '', $listSelect = false)
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', $type, $alt, $task, $icon, $listSelect);
	
	}
	
	function cancel($alt = 'CLOSE', $icon = 'cancel')
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'cancel', JText::_($alt), $icon);
	
	}
	
	function delete($alt = 'DELETE', $task = 'delete', $icon = 'delete', $listSelect = true)
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'delete', JText::_($alt), $task, $icon, $listSelect);
	
	}
	
	function add($alt = 'ADD', $task = 'add', $icon = 'add', $listSelect = false)
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'dialog', JText::_($alt), $task, $icon, $listSelect );
	
	}
	
	function edit($alt = 'EDIT', $task = 'edit', $icon = 'edit', $listSelect = true)
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'dialog', JText::_($alt), $task, $icon, $listSelect);
	
	}
	
	function publish($alt = 'PUBLISH', $task = 'publish', $icon = 'publish')
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'state', JText::_($alt), $task, $icon, true);
	
	}
	
	function unpublish($alt = 'UNPUBLISH', $task = 'unpublish', $icon = 'unpublish')
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'state', JText::_($alt), $task, $icon, true);
	
	}
	
	function save($alt = 'SAVE', $task = 'save', $icon = 'save')
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'save', JText::_($alt), $task, $icon);
	
	}
	
	function apply($alt = 'APPLY', $task = 'apply', $icon = 'apply')
	{
	
		$this->bar->appendButton( 'Joomdtoolbar', 'save', JText::_($alt), $task, $icon);
	
	}
	
	function help($alt = 'HELP', $ref = 'javascript:void(0);', $icon = 'help')
	{
		$bar =  JToolBar::getInstance('toolbar');
		// Add a help button
		$bar->appendButton( 'Joomdtoolbar', 'help', JText::_($alt), 'help', $icon, $ref );
	}
	
	function url($alt = 'GO', $ref = 'javascript:void(0);', $icon = 'go', $target='_self')
	{
		$bar =  JToolBar::getInstance('toolbar');
		// Add a help button
		$bar->appendButton( 'Joomdtoolbar', 'url', JText::_($alt), $ref, $icon, $target );
	}
	
	function dopin($alt = 'GO', $task = 'go', $icon = 'go', $list=true)
	{
		$bar =  JToolBar::getInstance('toolbar');
		// Add a help button
		$bar->appendButton( 'Joomdtoolbar', 'dopin', JText::_($alt), $task, $icon, $list );
	}
	
}

?>