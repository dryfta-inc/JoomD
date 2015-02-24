<?php
/*------------------------------------------------------------------------
# app_subscription - JoomD Application
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport( 'joomla.application.component.view' );
 

class joomdViewPackages extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$layout 		= JRequest::getCmd('layout', '');
		
		$toolbar = joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$multiselect = JoomDui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$config = JoomD::getConfig();
		$this->assignRef('config', $config);
		
		$abase = JRequest::getInt('abase', 0);
		$this->assignRef( 'abase', $abase );
		
		if(JRequest::getCmd('layout', '') == 'form')	{
        	
			$item =  $this->get('Item');
			$isNew		= ($item->id < 1);
			
			$this->assignRef( 'item', $item );
			
			$this->types = $this->get('Types');
			$this->cats = $this->get('Cats');
			
			joomdui::createform();
			
			if($isNew)	{
				$this->toolbarTitle = $toolbar->evtitle( JText::_( 'NEW_PACK' ), 'package' );
			}
			else	{
				$this->toolbarTitle = $toolbar->evtitle( JText::_( 'EDIT_PACK' ), 'package' );
				$order_list =  $this->get('Order_list');
				$this->assignRef( 'order_list', $order_list );
			}
		
			if($abase)
				$toolbar->apply();
			else
				$toolbar->cancel();
				
			$toolbar->save();
			$toolbar->help('help');
			
		}
		
		else	{
			
			$toolbar->title( JText::_( 'SUB_PACKS' ), 'package' );
			$toolbar->publish();
			$toolbar->unpublish();
			$toolbar->add();
        	$toolbar->delete();
			$toolbar->help('help');
			
			$items = $this->get('Items');
			$this->assignRef( 'items', $items );
			
			$params =  $this->get('Params');
			
			joomdui::createlist($params, array('order'=>"i.ordering", 'reorder'=>true));
			
			//language filter start
			$lists['language'] = '<select id="filter_language" class="inputbox" onchange="filterlist(this);" size="1" name="filter_language">';
			$lists['language'] .= '<option value="">'.JText::_( 'SELECT_LANGUAGE' ).'</option>';
			$lists['language'] .= JHTML::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' );
			$lists['language'] .= '</select>';
			//language filter end
			
			$lists['state']	= JHTML::_('jdgrid.state',  $params->filter_state );
			$lists['search']= JHTML::_('jdgrid.search',  $params->filter_search );
			
			// Table ordering.
			$lists['order_Dir'] = $params->filter_order_Dir;
			$lists['order']     = $params->filter_order;
			
			$this->assignRef( 'params', $params );
			$this->assignRef( 'lists', $lists );
			
		
		}
		
		parent::display($tpl);
		
        
    }
  
  
}
