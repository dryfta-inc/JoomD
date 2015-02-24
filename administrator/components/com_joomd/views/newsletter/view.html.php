<?php
 
/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/index.php?option=com_ccboard&view=forumlist&Itemid=63
-----------------------------------------------------------------------*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport( 'joomla.application.component.view' );
 

class JoomdViewNewsletter extends JView
{
    
    function display($tpl = null)
    {
		
		$mainframe =  JFactory::getApplication();
		$document =  JFactory::getDocument();
			
		$document->addStyleSheet('components/com_joomd/assets/css/newsletter.css');
		
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$tabs = Joomdui::getTabs();
		$this->assignRef('tabs', $tabs);
		
		$abase = JRequest::getInt('abase', 0);
		$this->assignRef('abase', $abase);
		
		$types =  $this->get('Types');
		$this->assignRef('types', $types);
		
		$cats =  $this->get('Cats');
		$this->assignRef('cats', $cats);
		
		$toolbar->title( JText::_( 'NEWSLETTER' ), 'newsletter' );
		$toolbar->help('help');
		
		$items = $this->get('Items');
		$this->assignRef( 'items', $items );
		
		$params =  $this->get('Params');
		
		joomdui::createlist($params);
		
		$type[] = JHTML::_('select.option',  '', JText::_( 'SELECTTYPE' ) );
					
		for($i=0;$i<count($types);$i++)
			$type[] = JHTML::_('select.option',  $types[$i]->id, $types[$i]->name );
		
		$lists['type'] = JHTML::_('select.genericlist',   $type, 'filter_type', 'class="inputbox" size="1" onchange="filterlist(this);get_pitems();"', 'value', 'text', $params->filter_type );
		
		$cat[] = JHTML::_('select.option',  '', JText::_( 'SELCAAT' ) );
					
		for($i=0;$i<count($cats);$i++)	{
			$cat[] = JHTML::_('select.option',  $cats[$i]->id, $cats[$i]->name );
		}
		
		$lists['cat'] = JHTML::_('select.genericlist',   $cat, 'filter_cat', 'class="inputbox" size="1" onchange="filterlist(this);"', 'value', 'text', $params->filter_cat );
		
		$lists['search']= JHTML::_('jdgrid.search',  $params->filter_search );
		
		// Table ordering.
		$lists['order_Dir'] = $params->filter_order_Dir;
		$lists['order']     = $params->filter_order;
		
		$this->assignRef( 'params', $params );
		
		$this->assignRef( 'lists', $lists );
		
		parent::display($tpl);
		
        
    }
  
  
}
