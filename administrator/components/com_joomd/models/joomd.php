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
 
jimport( 'joomla.application.component.model' );


class JoomdModelJoomd extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();

	}
	
	function getIcons()
	{
		
		$items = Joomd::getApps();
		
		$data = array();
		
		for($i=0;$i<count($items);$i++)	{
			
			if(is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php');
				
				$class = "JoomdApp".ucfirst($items[$i]->name);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'icon_display'))	{
						
						$items[$i]->iconhtml = $class->icon_display();
						$data[] = $items[$i];
						
					}
				
				}
				
			}
			
		}
		
		return $data;
	
	}
	
	function getPanel()
	{
		
		$items = Joomd::getApps();
		
		$data = array();
		
		$item = new stdClass();
		
		$item = $this->getStates();
		
		array_push($data, $item);
		
		for($i=0;$i<count($items);$i++)	{
			
			if(is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php');
				
				$class = "JoomdApp".ucfirst($items[$i]->name);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'panel_display'))	{
						
						$items[$i]->panelhtml = $class->panel_display();
						$data[] = $items[$i];
	
					}
				
				}
				
			}
			
		}
		
		return $data;
		
	}
	
	function getStates()
	{
		
		$doc = JFactory::getDocument();
		
		$tabs = Joomdui::getTabs();
		
		$item = new stdClass();
		
		$item->label = JText::_('STATIS');
		
		$query = 'select count( * ) from #__joomd_types';
		$this->_db->setQuery( $query );
		$types = $this->_db->loadResult();
		
		$query = 'select count( * ) from #__joomd_category';
		$this->_db->setQuery( $query );
		$cats = $this->_db->loadResult();
		
		$query = 'select count( * ) from #__joomd_item';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadResult();
		
		$query = 'select count( * ) from #__joomd_field';
		$this->_db->setQuery( $query );
		$fields = $this->_db->loadResult();
		
		$js = '
		
		function drawstateschart()	{
			
			var statesitems = [
				["'.JText::_('ENTITY').'", "'.JText::_('COUNT').'"],
				["'.JText::_('TYPE').'", '.$types.'],
				["'.JText::_('CATEGORY').'", '.$cats.'],
				["'.JText::_('ITEMS').'", '.$items.'],
				["'.JText::_('FIELDS').'", '.$fields.']
		
			];
		
			var statesdata = google.visualization.arrayToDataTable(statesitems);
					
			var statesoptions = {
			  title: "'.JText::_('ENTITY_STATES').'",
			  backgroundColor:"#eee",
			  width:$jd(".jd_cp_block").width()-43
			};
	
			var stateschart = new google.visualization.PieChart(document.getElementById("states_chart_div"));
			stateschart.draw(statesdata, statesoptions);
		
		}
		
		if(typeof google !== "undefined")
			google.setOnLoadCallback(drawstateschart);
			
		$jd(function() {
			$jd( "#cp_states_tab" ).tabs({
				event: "click",
				selected: 1,
				fx: { opacity: "toggle" },
				collapsible: true
			});
		});
		
		';
				
		$doc->addScriptDeclaration($js);
		
		$item->panelhtml = '<div id="cp_states_tab">';
		
		$item->panelhtml .= '<ul>
			<li><a href="#cp_states_tab1">'.JText::_('STATIS').'</a></li>
			<li><a href="#cp_states_tab2">'.JText::_('CHART').'</a></li>
		</ul>';
		$item->panelhtml .= '<div id="cp_states_tab1">';
		
		$item->panelhtml .= '<div class="adminlist class_panel_b">';
		
		$item->panelhtml .= ' <div class="tr_bo_header"><div class="class_panel_h">'.JText::_('ENTITY').'</div><div class="class_panel_hd">'.JText::_('COUNT').'</div><div class="clr"></div></div>';
		
		$item->panelhtml .= '<div class="cont_par_box"><div class="left_hit_box">'.JText::_('TYPE').'</div><div class="right_hit_box">'.$types.'</div><div class="clr"></div></div>';
		$item->panelhtml .= '<div class="cont_par_box"><div class="left_hit_box">'.JText::_('CATEGORY').'</div><div class="right_hit_box">'.$cats.'</div><div class="clr"></div></div>';
		$item->panelhtml .= '<div class="cont_par_box"><div class="left_hit_box">'.JText::_('ITEMS').'</div><div class="right_hit_box">'.$items.'</div><div class="clr"></div></div>';
		$item->panelhtml .= '<div class="cont_par_box"><div class="left_hit_box">'.JText::_('FIELDS').'</div><div class="right_hit_box">'.$fields.'</div><div class="clr"></div></div>';
		
		$item->panelhtml .= '</div>';
		
		$item->panelhtml .= '</div>';
		
		$item->panelhtml .= '<div id="cp_states_tab2"><div id="states_chart_div"></div></div>';
		
		$item->panelhtml .= '</div>';
		
				
		return $item;
		
		
	}
	
	function getEmails()
	{
		
		$query = 'select id from #__joomd_field where type = 8';
		$this->_db->setQuery( $query );
		$fields = (array)$this->_db->loadResultArray();
		
		$emails = array();
		
		foreach($fields as $field)	{
		
			$query = 'select typeid from #__joomd_tnf where fieldid = '.$field;
			$this->_db->setQuery( $query );
			$types = (array)$this->_db->loadResultArray();
			
			foreach($types as $type)	{

				$field = 'field_'.$field;
				$table = $this->_db->getPrefix().'joomd_type'.$type;
				
				$query = 'show tables like '.$this->_db->Quote($table);
				$this->_db->setQuery( $query );
				$exists = $this->_db->loadObject();
				
				if(!empty($exists))	{
					
					$query = 'show fields from '.$table.' LIKE '.$this->_db->Quote($field);
					$this->_db->setQuery( $query );
					$exists = $this->_db->loadObject();
					
					if(!empty($exists))	{
						
						$query = 'select '.$field.' from '.$table.' where '.$field.' <> ""';
						$this->_db->setQuery( $query );
						$e = $this->_db->loadResultArray();
						
						$emails += $e;
						
					}
					
				}
								
		
			}		
		
		}
		
		return array_unique($emails);
		
	}
	
	function gethitChart()
	{
		
		$ar = JRequest::getVar('ar', 'day');
		
		switch($ar)
		{
			
			case 'day':
			$func = 'date_format(hit_date, "%e, %b") as func';
			break;
			
			case 'week':
			$func = 'date_format(hit_date, "%U, %Y") as func';
			break;
			
			case 'month':
			$func = 'date_format(hit_date, "%b, %Y") as func';
			break;
			
			case 'year':
			$func = 'year(hit_date) as func';
			break;
			
		}
		
		$query = 'SELECT '.$func.' FROM #__joomd_user_item where hit_date <> "0000-00-00 00:00:00" group by func order by hit_date desc limit 12';
		$this->_db->setQuery( $query );
		$ids = $this->_db->loadResultArray();
		
		$query = 'SELECT '.$func.', sum(hits) as hits FROM #__joomd_user_item where hit_date <> "0000-00-00 00:00:00" group by func having func in ("'.implode('", "', $ids).'") order by hit_date asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadRowList();
				echo $this->_db->getErrorMsg();
		$json = '{"result":"success", "items":[';
											   
		$arr = array('["'.JText::_(strtoupper($ar)).'"', '"'.JText::_('HITS').'"]');
		
		foreach($items as $item)
			array_push($arr, '["'.$item[0].'"', $item[1].']');
														   
		$json .= implode(',', $arr);
		
		$json .= ']}';
		
		return $json;
		
	}

}

?>