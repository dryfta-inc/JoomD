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
 

class joomdViewOrders extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$layout 		= JRequest::getCmd('layout', '');
		
		$toolbar = joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$config = Joomd::getConfig();
		$this->assignRef('config', $config);
		
		$abase = JRequest::getInt('abase', 0);
		$this->assignRef( 'abase', $abase );
				
		if(JRequest::getCmd('layout', '') == 'form')	{
        	
			$item =  $this->get('Item');
			
			$this->assignRef( 'item', $item );
						
			$this->toolbarTitle = $toolbar->evtitle( JText::_( 'ORDERMANAGERHTML' ), 'orders' );
			$order_list =  $this->get('Order_list');
			$this->assignRef( 'order_list', $order_list );
			
			if(!$this->abase)
				$toolbar->cancel();
				
			$toolbar->help('help');
			
		}
		
		else	{
		
			$toolbar->title( JText::_( 'ORDERS' ), 'orders' );
			$toolbar->help('help');
			
			$items = $this->get('Items');
			$this->assignRef( 'items', $items );
		
					
			$params =  $this->get('Params');
			
			joomdui::createlist($params, array('sortable'=>false, 'dialog'=>false, 'access'=>false));
			
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
