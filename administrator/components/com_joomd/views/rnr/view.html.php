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
 

class JoomdViewRnr extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$document =  JFactory::getDocument();
		$layout 		= JRequest::getCmd('layout', '');
		
		$document->addStyleSheet(JURI::root().'components/com_joomd/assets/css/rnr.css');
		
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$abase = JRequest::getInt('abase', 0);
		$this->assignRef('abase', $abase);
		
		$types =  $this->get('Types');
		$this->assignRef('types', $types);
		
		$pitems =  $this->get('Pluginitems');
		$this->assignRef('pitems', $pitems);
		
		$users = $this->get('Users');
		$this->assignRef('users', $users);
		
		if(JRequest::getCmd('layout', '') == 'form')	{
        	
			$item =  $this->get('Item');
			$isNew		= ($item->id < 1);
			
			$this->assignRef( 'item', $item );
			
			joomdui::createform();
						
			$this->assignRef('toolbarTitle', $toolbar->evtitle( JText::_( 'EDITREVIEW' ), 'reviews' ));
			
			if(!$abase)
				$toolbar->cancel();
			else
				$toolbar->apply();
				
			$toolbar->save();
			$toolbar->help('help');
			
		}
		
		else	{
		
			$toolbar->title( JText::_( 'REVIEWS' ), 'reviews' );
			$toolbar->publish();
			$toolbar->unpublish();
        	$toolbar->delete();
			$toolbar->help('help');
			
			$items = $this->get('Items');
			$this->assignRef( 'items', $items );
			
			$params =  $this->get('Params');
			
			joomdui::createlist($params, array('order'=>"i.ordering", 'reorder'=>true));
			
			$user[] = JHTML::_('select.option',  '', JText::_( 'SELECTUSER' ) );
						
			for($i=0;$i<count($users);$i++)
				$user[] = JHTML::_('select.option',  $users[$i]->id, $users[$i]->name );
			
			$lists['user'] = JHTML::_('select.genericlist',   $user, 'filter_user', 'class="inputbox" size="1" onchange="filterlist(this);"', 'value', 'text', $params->filter_user );
			
			$type[] = JHTML::_('select.option',  '', JText::_( 'SELECTTYPE' ) );
						
			for($i=0;$i<count($types);$i++)
				$type[] = JHTML::_('select.option',  $types[$i]->id, $types[$i]->name );
			
			$lists['type'] = JHTML::_('select.genericlist',   $type, 'filter_type', 'class="inputbox" size="1" onchange="filterlist(this);get_pitems();"', 'value', 'text', $params->filter_type );
			
			$pitem[] = JHTML::_('select.option',  '', JText::_( 'SELECTITEM' ) );
						
			for($i=0;$i<count($pitems);$i++)	{
				$pitem[] = JHTML::_('select.option',  $pitems[$i]->id, $pitems[$i]->title );
			}
			
			$lists['pitem'] = JHTML::_('select.genericlist',   $pitem, 'filter_item', 'class="inputbox" size="1" onchange="filterlist(this);"', 'value', 'text', $params->filter_item );
			
			$lists['state']	= JHTML::_('jdgrid.state',  $params->filter_state );
			$lists['search']= JHTML::_('jdgrid.search',  $params->filter_search );
			
			// Table ordering.
			$lists['order_Dir'] = $params->filter_order_Dir;
			$lists['order']     = $params->filter_order;
			
			$this->assignRef( 'params', $params );
		
		}
		
		$this->assignRef( 'lists', $lists );
		
		parent::display($tpl);
		
        
    }
  
  
}
