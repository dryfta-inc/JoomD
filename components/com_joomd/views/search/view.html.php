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
 
 
class JoomdViewSearch extends JView
{
    function display($tpl = null)
    {
		$mainframe =  JFactory::getApplication();
		
		$document	=  JFactory::getDocument();
		$params =  $mainframe->getParams();
		
		$menus		=  $mainframe->getMenu();
		$menu    	= $menus->getActive();
		
		$layout = JRequest::getCmd('layout', '');
		
		$type = Joomd::getType();
		$this->assignRef('type', $type);
		
		$config =  Joomd::getConfig($type->app);
		
		$this->assignRef('config', $config);
		
		$user =  $this->get('User');
		$this->assignRef('user', $user);
		
		$field = new JoomdAppField();
		$this->assignRef('field', $field);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$cparams =  $this->get('Params');
		$this->assignRef('cparams', $cparams);
		
		if($layout == "search")	{
			
			$pagetitle = JText::_('SEARCHRES');

			$items =  $this->get('Items');
			
			$fields =  $field->getFields(array('cats'=>$cparams->cats, 'published'=>1, 'list'=>true));		
			
			Joomdui::createlist($cparams, array('formname'=>'listform', 'list'=>'.itemlist', 'type'=>'GET', 'sortable'=>false));
			
			$this->assignRef('items', $items);
			$this->assignRef('fields', $fields);
		
		}
		
		else	{
		
			$pagetitle = JText::_('SEARCH');
		/*	
			if(!$cparams->typeid)	{
				$types =  $this->get('Types');
				$this->assignRef( 'types', $types );
			}
		*/	
			$cats =  $this->get('Cats');
			
			$fields =  $field->getFields(array('published'=>1, 'search'=>1));
			
			$this->assignRef('fields', $fields);
			
			$this->assignRef( 'cats', $cats );
		
		}
		
		if (is_object($menu)) {
				
			if ($params->get( 'page_title')) {
				$pagetitle = $params->get( 'page_title', $pagetitle);
			}
		}
		
		$params->set('page_title', $pagetitle);
		
		$document->setTitle( $params->def('page_title') );
		$this->assignRef( 'document', $document );
		
		$this->assignRef('params', $params);
		
		$theme = Joomd::get('Theme');
		$this->addTemplatePath('components/com_joomd/templates/'.$theme.'/search');
		
		parent::display($tpl);

    }
}
