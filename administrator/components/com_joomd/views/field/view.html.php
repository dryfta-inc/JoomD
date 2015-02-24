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


class JoomdViewField extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$layout 		= JRequest::getCmd('layout', '');
		
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$types =  $this->get('Types');
		$this->assignRef('types', $types);
		
		$cats =  $this->get('Cats');
		$this->assignRef('cats', $cats);
		
		$fieldtypes =  $this->get('Fieldtypes');
		$this->assignRef('fieldtypes', $fieldtypes);
		
		$abase = JRequest::getInt('abase', 0);
		$this->assignRef('abase', $abase);
		
		if(JRequest::getCmd('layout', '') == 'form')	{
        	
			$item =  $this->get('Item');
			$isNew		= ($item->id < 1);
			
			$this->assignRef( 'item', $item );
			
			joomdui::createform();
				
			$lists['access'] = JHtml::_('access.assetgrouplist', 'access', $item->access);
			
			if($isNew)	{
				$this->toolbarTitle = $toolbar->evtitle( JText::_( 'NFIELD' ), 'field.png' );
			}
			else	{
				$this->toolbarTitle = $toolbar->evtitle( JText::_( 'EFIELD' ), 'field.png' );
				$order_list =  $this->get('Order_list');
				$this->assignRef( 'order_list', $order_list );
			}
			
			if(!$abase)
				$toolbar->cancel();
			else
				$toolbar->apply();
				
			$toolbar->save();
			$toolbar->help('help');
			
		}
		
		else	{
		
			$toolbar->title( JText::_( 'Field Manager' ), 'field.png' );
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
			
			$type[] = JHTML::_('select.option',  '', JText::_( 'SELECTTYPE' ) );
						
			for($i=0;$i<count($types);$i++)
				$type[] = JHTML::_('select.option',  $types[$i]->id, $types[$i]->name );
			
			$lists['type'] = JHTML::_('select.genericlist',   $type, 'filter_type', 'class="inputbox" size="1" onchange="filterlist(this);"', 'value', 'text', $params->filter_type );
			
			$lists['cat'] = '<select name="filter_cat" id="filter_cat" class="inputbox" onchange="filterlist(this);"><option value="">'.JText::_( 'SELCAAT' ).'</option>';
						
			for($i=0;$i<count($cats);$i++)	{
				$lists['cat'] .= '<option value="'.$cats[$i]->id.'"';
				if($cats[$i]->id==$params->filter_cat)
					$lists['cat'] .= ' selected="selected"';
				$lists['cat'] .= '>'.$cats[$i]->treename.'</option>';
			}
			
			$lists['cat'] .= '</select>';
			
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
