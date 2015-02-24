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
 
 
class JoomdViewCategory extends JView
{
    function display($tpl = null)
    {
		$mainframe =  JFactory::getApplication();
		
		$document	=  JFactory::getDocument();
		$params =  $mainframe->getParams();
		
		$menus		=  $mainframe->getMenu();
		$menu    	= $menus->getActive();
		
		$layout = JRequest::getCmd('layout', '');
		$abase = JRequest::getInt('abase', 0);
		
		$config = Joomd::getConfig();
		$this->assignRef('config', $config);
		
		$type = Joomd::getType();
		$this->assignRef('type', $type);
		
		$parent = $this->get('Parent');
		$this->assignRef('parent', $parent);
		
		$cparams =  $this->get('Params');
		$this->assignRef('cparams', $cparams);
		
		Joomdui::createlist($cparams, array('formname'=>'listform', 'list'=>'.catlist', 'sortable'=>false));		
		
		if (is_object($menu)) {
			
			if (!$params->get( 'page_title')) {
				if(empty($parent))	{
					$params->set('page_title',	JText::_('CATEGORIES'));
				}
				else	{
					$params->set('page_title',	$this->escape($parent->name));
				}
			}
			
			
		}
		else {
			
			$pathway 	=  $mainframe->getPathWay();
			
			if(empty($parent))	{
				$pathway->addItem( JText::_('CATEGORIES'));
				$params->set('page_title', JText::_('JOOMDCATE'));
			}
			else	{
				$pathway->addItem( JText::_('CATEGORIES'), JRoute::_('index.php?option=com_joomd&view=category'));
				$pathway->addItem( $this->escape($parent->name));
				
				$params->set('page_title', $parent->name);
				
			}
			
		}
		
		$items =  $this->get('Items');
		
		$document->setTitle( $params->def('page_title') );
		
		$this->assignRef( 'items', $items );
		$this->assignRef('params', $params);
				
		$theme = Joomd::get('Theme');
		$this->addTemplatePath('components/com_joomd/templates/'.$theme.'/category');
		
		parent::display($tpl);

    }
}
