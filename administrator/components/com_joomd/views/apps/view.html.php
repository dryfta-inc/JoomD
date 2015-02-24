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
 

class JoomdViewApps extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$layout 		= JRequest::getCmd('layout', '');
		
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$abase = JRequest::getInt('abase', 0);
		$this->assignRef('abase', $abase);
		
		if(JRequest::getCmd('layout', '') == 'form')	{
        	
			$item =  $this->get('Item');
			$isNew		= ($item->id < 1);
			
			$this->assignRef( 'item', $item );
			
			joomdui::createform();
			
			if($isNew)	{
				$this->toolbarTitle = $toolbar->evtitle( JText::_( 'NEW_APP' ), 'apps' );
			}
			else	{
				$this->toolbarTitle = $toolbar->evtitle( JText::_( 'EDIT_APP' ), 'apps' );
				$order_list =  $this->get('Order_list');
				$this->assignRef( 'order_list', $order_list );
			}
			
			if(!$abase)
				$toolbar->cancel();
			else
				$toolbar->apply();
			
			$toolbar->save();
			if($item->id)
				$toolbar->save('UNINSTALL', 'delete', 'delete');
			$toolbar->help('help');
			
		}
		
		else	{
		
			$toolbar->title( JText::_( 'APP_MANAGER' ), 'apps' );
			/*$toolbar->publish();
			$toolbar->unpublish();*/
			$toolbar->add('INSTALL');
        	//$toolbar->delete('Uninstall');
			$toolbar->help('help');
			
			$items = $this->get('Items');
			$this->assignRef( 'items', $items );
			
			$params =  $this->get('Params');
			
			joomdui::createlist($params, array('order'=>"ordering", 'reorder'=>true));
			
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
