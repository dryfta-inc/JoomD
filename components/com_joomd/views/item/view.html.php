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
 
 
class JoomdViewItem extends JView
{
    function display($tpl = null)
    {
		$mainframe =  JFactory::getApplication();
		
		$document	=  JFactory::getDocument();
		$params =  $mainframe->getParams();
		
		$this->jdapp = Joomd::getApp();
		
		$menus		=  $mainframe->getMenu();
		$menu    	= $menus->getActive();
		
		$layout = JRequest::getCmd('layout', '');
		
		$config =  Joomd::getConfig('item');
				
		$this->assignRef('config', $config);
		
		$type = Joomd::getType();
		$this->assignRef('type', $type);
		
		$cparams =  $this->get('Params');
		$this->assignRef('cparams', $cparams);
		
		$user =  $this->get('User');
		$this->assignRef('user', $user);
		
		$category =  $this->get('Category');
		$this->assignRef('category', $category);
		
		$field = new JoomdAppField();
		$this->assignRef('field', $field);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
				
		if($layout == "detail")	{
			
			$item =  $this->get('Item');
			
			if ($item->metadata->get('meta_desc'))
			{
				$this->document->setDescription($item->metadata->get('meta_desc'));
			}
			elseif (!$item->metadata->get('meta_desc') && $params->get('menu-meta_description'))
			{
				$document->setDescription($params->get('menu-meta_description'));
			}
	
			if ($item->metadata->get('meta_key'))
			{
				$document->setMetadata('keywords', $item->metadata->get('meta_key'));
			}
			elseif (!$item->metadata->get('meta_key') && $params->get('menu-meta_keywords'))
			{
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}
	
			if ($item->metadata->get('robots'))
			{
				$document->setMetadata('robots', $item->metadata->get('robots'));
			}
	
			if ($item->metadata->get('author'))
			{
				$document->setMetaData('author', $item->metadata->get('author'));
			}
			
			$fields =  $field->getFields(array('itemid'=>$item->id, 'published'=>1, 'detail'=>true, 'access'=>true));
		
			$this->assignRef('fields', $fields);
			
			$firstfield = $field->get_firstfield(array('published'=>1));
			
			$value = $field->getfieldvalue($item->id, $firstfield->id);
			
			if($firstfield->type == 1 and !empty($value))	{
				$params->set('page_title', $value);
			}
			
			$this->assignRef('item', $item);
			$this->assignRef('fields', $fields);
		
		}
		
		else	{
			
			Joomdui::createlist($cparams, array('formname'=>'listform', 'list'=>'.itemlist', 'sortable'=>false));
						
			$items =  $this->get('Items');
			
			$category =  $this->get('Category');
			
			$pagetitle = JText::_('ITEMS');
			
			if(!empty($category))	{
				
				$pagetitle = $category->name;
				
				if (!is_object($menu)) {
					$pathway 	=  $mainframe->getPathWay();
					$pathway->addItem( $this->escape($category->name));
				}
				
			}
			elseif($cparams->userid)	{
				$u = JFactory::getUser($cparams->userid);
				$pagetitle .= ' - '.$u->name;
				
				if (!is_object($menu)) {
					$pathway 	=  $mainframe->getPathWay();
					$pathway->addItem( $this->escape($pagetitle));
				}
			}
			elseif (is_object($menu)) {
				
				if ($params->get( 'page_title')) {
					$pagetitle = $params->get( 'page_title', $pagetitle);
				}
			}
			
			$params->set('page_title', $pagetitle);
			
			$fields =  $field->getFields(array('cats'=>$cparams->catid, 'published'=>1, 'list'=>true));
			$this->assignRef('fields', $fields);
			 
			$this->assignRef( 'items', $items );
		
		}
		
		$document->setTitle( $params->def('page_title') );
		$this->assignRef( 'document', $document );
		
		$this->assignRef( 'params', $params );
		
		$theme = Joomd::get('Theme');
		$this->addTemplatePath('components/com_joomd/templates/'.$theme.'/item');
		
		parent::display($tpl);

    }
}
