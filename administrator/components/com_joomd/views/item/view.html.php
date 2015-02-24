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


class JoomdViewItem extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$layout 		= JRequest::getCmd('layout', '');
		
		$document =  JFactory::getDocument();
		
		$this->toolbar = Joomd::getToolbar();
		
		$this->multiselect = Joomdui::getMultiselect();
		
		$this->type = Joomd::getType();
		
		$this->cats =  $this->get('Cats');
		
		$this->field = new JoomdAppField();
		
		$this->abase = JRequest::getInt('abase', 0);
		
		$this->params =  $this->get('Params');
		
		if(JRequest::getCmd('layout', '') == 'form')	{
        	
			$this->item =  $this->get('Item');
			$isNew		= ($this->item->id < 1);
						
			joomdui::createform();
			
			$this->pane = Joomdui::getAccordion();
			
			$this->lists['access'] = JHtml::_('access.assetgrouplist', 'access', $this->item->access);
			
			if($isNew)	{
				$this->toolbarTitle = $this->toolbar->evtitle( JText::_( 'NEWITEM' ), 'item' );
			}
			else	{
				$this->toolbarTitle = $this->toolbar->evtitle( JText::_( 'EDITITEM' ), 'item' );
				$this->order_list =  $this->get('Order_list');
			}
			
			if(!$this->abase)
				$this->toolbar->cancel();
			else
				$this->toolbar->apply();
				
			$this->toolbar->save();
			$this->toolbar->help('help');
			
		}
		
		else	{
		
			$this->toolbar->title( $this->type->name. ' ' .JText::_( 'MANAGER' ), 'item' );
			$this->toolbar->publish();
			$this->toolbar->unpublish();
			$this->toolbar->add();
        	$this->toolbar->delete();
			$this->toolbar->help('help');
			
			$this->items = $this->get('Items');
			
			$this->firstfield =  $this->field->get_firstfield();
			
			joomdui::createlist($this->params, array('order'=>"i.ordering", 'reorder'=>true));
			
			//language filter start
			$this->lists['language'] = '<select id="filter_language" class="inputbox" onchange="filterlist(this);" size="1" name="filter_language">';
			$this->lists['language'] .= '<option value="">'.JText::_( 'SELECT_LANGUAGE' ).'</option>';
			$this->lists['language'] .= JHTML::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' );
			$this->lists['language'] .= '</select>';
			//language filter end
			
			$this->lists['cat'] = '<select name="filter_cat" id="filter_cat" class="inputbox" onchange="filterlist(this);"><option value="">'.JText::_( 'SELCATE' ).'</option>';
						
			for($i=0;$i<count($this->cats);$i++)	{
				$this->lists['cat'] .= '<option value="'.$this->cats[$i]->id.'"';
				if($this->cats[$i]->id==$this->params->filter_cat)
					$this->lists['cat'] .= ' selected="selected"';
				$this->lists['cat'] .= '>'.$this->cats[$i]->treename.'</option>';
			}
			
			$this->lists['cat'] .= '</select>';
			
			$this->lists['state']	= JHTML::_('jdgrid.state',  $this->params->filter_state );
			$this->lists['search']= JHTML::_('jdgrid.search',  $this->params->filter_search );
			
			// Table ordering.
			$this->lists['order_Dir'] = $this->params->filter_order_Dir;
			$this->lists['order']     = $this->params->filter_order;
			
		}
				
		parent::display($tpl);
		
        
    }
  
  
}
