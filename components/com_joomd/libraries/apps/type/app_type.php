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

require_once(JPATH_SITE.'/components/com_joomd/libraries/app.php');

class JoomdAppType extends JoomdApp	{
	
	
	function __construct()
	{
		
		parent::__construct();
		
		$this->initialize();
		
	}
	
	function initialize()
	{
		
		static $init = false;
		
		if($init)
			return;
				
		$this->loadLanguage();
				
		$init = true;
		
	}
	
	function loadLanguage()
	{
		
		static $loaded = false;
		
		if($loaded)
			return true;
		
		$lang = JFactory::getLanguage();
		
		$lang->load('app_type', JDPATH_BASE);
		
		$loaded = true;
		
		return true;
		
	}
	
	//Displays submenu in Back end
	function add_submenu()
	{
		$view = JRequest::getCmd('view', '');
		
		$active = $view == 'type';
		
		JSubMenuHelper::addEntry( '<span class="hasTip" title="'.JText::_('SUBMENU_TYPES_DESCR').'">'.JText::_('TYPES').'</span>' , 'index.php?option=com_joomd&view=type' , $active );
		
		$typeid = JRequest::getInt('typeid', 0);
		
		$query = 'select i.*, a.name as app from #__joomd_types as i join #__joomd_apps as a on i.appid=a.id where i.published = 1 order by i.ordering asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		foreach($items as $item)	{
			
			$active = ($view==$item->app && $item->id==$typeid);
			
			JSubMenuHelper::addEntry( '<span class="hasTip" title="'.$item->descr.'">'.$item->name.'</span>' , 'index.php?option=com_joomd&view='.$item->app.'&typeid='.$item->id , $active );
		}
		
	}
	
	//Displays the icon in Control Panel
	function icon_display()
	{
		
		$html = '<div class="icon" title="'.JText::_('SUBMENU_TYPES_DESCR').'"><a href="index.php?option=com_joomd&view=type&task=add&cid[]=0" id="thistype"><img src="components/com_joomd/assets/images/icon-48-type-add.png" alt="Type" /><span>'. JText::_('ADD_A_TYPE').'</span></a></div>';
		
		return $html;
		
	}
	
	//Displays the panel in Control Panel
	function panel_display()
	{
		$doc = JFactory::getDocument();
				
		$query = 'select t.id, t.name as name, sum(i.hits) as hits from #__joomd_types as t join #__joomd_item as i on t.id=i.typeid group by t.id order by hits desc limit 4';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		$js = '
		
		function drawtypechart()	{
			
			var typeitems = [["'.JText::_('TYPE').'", "'.JText::_('HITS').'"]';
		
			foreach($items as $item)
				$js .= ', ["'.$item->name.'", '.$item->hits.']';
			
			$js .= '];
		
			var typedata = google.visualization.arrayToDataTable(typeitems);
					
			var typeoptions = {
			  title: "'.JText::_('ITEM_HITS').'",
			  backgroundColor:"#eee",
			  width:$jd(".jd_cp_block").width()-43
			};
	
			var typechart = new google.visualization.PieChart(document.getElementById("type_chart_div"));
			typechart.draw(typedata, typeoptions);
		
		}
		
		if(typeof google !== "undefined")
			google.setOnLoadCallback(drawtypechart);
			
		$jd(function() {
			$jd( "#cp_type_tab" ).tabs({
				event: "click",
				selected: 1,
				fx: { opacity: "toggle" },
				collapsible: true
			});
			
		});
			
		';
				
		$doc->addScriptDeclaration($js);
		
		$html = '<div id="cp_type_tab">';
		
		$html .= '<ul>
			<li><a href="#cp_type_tab1">'.JText::_('ITEMS').'</a></li>
			<li><a href="#cp_type_tab2">'.JText::_('CHART').'</a></li>
		</ul>';
		$html .= '<div id="cp_type_tab1">';
		
		$html .= '<div class="adminlist class_panel_b">';
		
		$html .= '<div class="tr_bo_header"><div class="class_panel_h">'.JText::_('NAME').'</div><div class="class_panel_hd">'.JText::_('HITS').'</div><div class="clr"></div></div>';
		
		for($i=0;$i<count($items);$i++)	{
			
			$html .= '<div class="cont_par_box"><div class="left_hit_box" ><a href="index.php?option=com_joomd&view=type&layout=form&cid[]='.$items[$i]->id.'">'.$items[$i]->name.'</a></div><div class="right_hit_box"><a href="index.php?option=com_joomd&view=item&typeid='.$items[$i]->id.'">'.$items[$i]->hits.'</a></div><div class="clr"></div></div>';
			
		}
		
		$html .= '</div>';
		
		$html .= '</div>';
		
		$html .= '<div id="cp_type_tab2"><div id="type_chart_div"></div></div>';
		
		$html .= '</div>';
		
		return $html;
		
	}
	
	function getTheme()
	{
				
		$typeid = JRequest::getInt('typeid', 0);
		
		$query = 'select config from #__joomd_types where id = '.$typeid.' limit 1';
		$this->_db->setQuery( $query );
		$config = $this->_db->loadResult();
		
		$registry = new JRegistry;
		$registry->loadString($config);
		$config = $registry;
		
		$query = 'select name from #__joomd_templates where id = '.(int)$config->get('template');
		$this->_db->setQuery( $query );
		$theme = $this->_db->loadResult();
		
		return $theme;
		
	}

	
}