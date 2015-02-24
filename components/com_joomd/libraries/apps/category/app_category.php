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

class JoomdAppCategory extends JoomdApp	{
	
	
	function __construct()
	{
				
		$app = JFactory::getApplication();
		
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
		
		$lang->load('app_category', JDPATH_BASE);
		
		$loaded = true;
		
		return true;
		
	}
	
	function add_submenu()
	{
		$view = JRequest::getCmd('view', '');
		
		$active = $view == 'category';
	
		JSubMenuHelper::addEntry( '<span class="hasTip" title="'.JText::_('SUBMENU_CATEGORY_DESCR').'">'.JText::_('CATEGORY').'</span>' , 'index.php?option=com_joomd&view=category' , $active );
		
	}
	
	//Displays the icon in Control Panel
	function icon_display()
	{
		
		$html = '<div class="icon" title="'.JText::_('SUBMENU_CATEGORY_DESCR').'"><a href="index.php?option=com_joomd&view=category&task=add&cid[]=0" id="thiscat"><img src="components/com_joomd/assets/images/icon-48-category-add.png" alt="Category" /><span>'. JText::_('ADDACAT').'</span></a></div>';
		
		return $html;
		
	}
	
	//Displays the panel in Control Panel
	function panel_display()
	{
		$doc = JFactory::getDocument();
				
		$query = 'select c.id, c.name as name, sum(i.hits) as hits from #__joomd_category as c join #__joomd_item_cat as ic on c.id=ic.catid join #__joomd_item as i on ic.itemid=i.id group by c.id order by hits desc limit 4';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		$js = '
		
		function drawcatchart()	{
			
			var catitems = [["'.JText::_('CATEGORY').'", "'.JText::_('HITS').'"]';
		
			foreach($items as $item)
				$js .= ', ["'.$item->name.'", '.$item->hits.']';
			
			$js .= '];
		
			var catdata = google.visualization.arrayToDataTable(catitems);
					
			var catoptions = {
			  title: "'.JText::_('ITEM_HITS').'",
			  backgroundColor:"#eee",
			  width:$jd(".jd_cp_block").width()-43
			};
	
			var catchart = new google.visualization.PieChart(document.getElementById("cat_chart_div"));
			catchart.draw(catdata, catoptions);
		
		}
		
		if(typeof google !== "undefined")
			google.setOnLoadCallback(drawcatchart);
			
		$jd(function() {
			$jd( "#cp_cat_tab" ).tabs({
				event: "click",
				selected: 1,
				fx: { opacity: "toggle" },
				collapsible: true
			});
		});	
		
		';
				
		$doc->addScriptDeclaration($js);
		
		$html = '<div id="cp_cat_tab">';
		
		$html .= '<ul>
			<li><a href="#cp_cat_tab1">'.JText::_('ITEMS').'</a></li>
			<li><a href="#cp_cat_tab2">'.JText::_('CHART').'</a></li>
		</ul>';
		$html .= '<div id="cp_cat_tab1">';
		
		$html .= '<div class="adminlist class_panel_b">';
		
		$html .= '<div class="tr_bo_header"><div class="class_panel_h">'.JText::_('NAME').'</div><div class="class_panel_hd">'.JText::_('HITS').'</div><div class="clr"></div></div>';
		
		for($i=0;$i<count($items);$i++)	{
			
			$html .= '<div class="cont_par_box"><div class="left_hit_box" ><a href="index.php?option=com_joomd&view=category&layout=form&cid[]='.$items[$i]->id.'">'.$items[$i]->name.'</a></div><div class="right_hit_box"><a href="index.php?option=com_joomd&view=item&filter_cat='.$items[$i]->id.'">'.$items[$i]->hits.'</a></div><div class="clr"></div></div>';
			
		}
		
		$html .= '</div>';
		
		$html .= '</div>';
		
		$html .= '<div id="cp_cat_tab2"><div id="cat_chart_div"></div></div>';
		
		$html .= '</div>';
		
		return $html;
		
	}
	
	function onBeforeStore()
	{
		
		$cats = JRequest::getVar('catid', array(), 'post', 'array');
		
		if(!count($cats))	{
			throw new Exception(JText::_('PLSSELATLOCAT'));
		}
		
		return true;
		
	}
	
	function onAfterStore($row)
	{
		
		$cats = JRequest::getVar('catid', array(), 'post', 'array');
		
		JArrayHelper::toInteger( $cats );
		
		$query = 'delete from #__joomd_item_cat where itemid = '.$row->id;
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			throw new Exception($this->_db->getErrorMsg());
		}
		
		foreach($cats as $cat)	{
		
			$query = 'insert into #__joomd_item_cat values('.$cat.', '.$row->id.')';
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				throw new Exception($this->_db->getErrorMsg());
			}
			
		}
					
		return true;
		
	}
	
	
}