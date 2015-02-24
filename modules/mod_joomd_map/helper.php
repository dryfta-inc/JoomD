<?php

/*------------------------------------------------------------------------
# mod_joomd_map - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomd_mapHelper
{
	
	static function loadmarker($params)
	{
		$obj = new stdClass();
				
		$obj->data = modJoomd_mapHelper::getItems($params);
				
		$obj->result = 'success';
		
		return $obj;
		
	}
	
	static function getItems($params)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		$now = $date->toMySQL();
		
		$typeid		= (int)$params->get('typeid', 1);
		$cats 		= (array)$params->get('cats', array());
		$featured 	= (int)$params->get('featured', 0);
		$fieldid 	= (int)$params->get('fieldid', 0);
		$fs		 	= (array)$params->get('fields', array());
		$limit 		= (int)$params->get('limit', 50);
		
		$latlngbound = JRequest::getVar('latlngbound', '');
		$cords = explode(',', $latlngbound);
		
		if(!class_exists('Joomd'))
			require_once(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'core.php');
						
		$type = Joomd::getType($typeid);
		
		if(!class_exists('JoomdAppField'))
			require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$_field = new JoomdAppField();
		$_field->setType($typeid);
		
		$where = array();
		
		$where[] = 'i.published = 1';
		
		$where[] = 'i.typeid = '.$typeid;
		
		$where[] = 'i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$where[] = 'i.publish_up <= '.$db->Quote($now);
				
		$where[] = '( i.publish_down >= '.$db->Quote($now).' or i.publish_down = "0000-00-00 00:00:00" )';
		
		if(count($cats) > 0)	{
			$query = 'select itemid from #__joomd_'.$type->app.'_cat where catid in ('.implode(',', $cats).')';
			$db->setQuery( $query );
			$ids = (array)$db->loadResultArray();
			
			$where[] = count($ids)?'i.id in ('.implode(',', $ids).')':'i.id=0';
			
		}		
		
		if($featured)
			$where[] = 'i.featured = 1';
		
		$min_lat = $cords[0];
		$max_lat = $cords[1];
		$min_lng = $cords[2];
		$max_lng = $cords[3];
		
		$where[] = 'a.lat between '.$min_lat.' and '.$max_lat;
		$where[] = 'a.lng between '.$min_lng.' and '.$max_lng;
		$where[] = 'a.address <> ""';
		
		$field = $_field->getField($fieldid, array('published'=>1));
		
		//$where[] = 'field_'.$field->id.' regexp '.$db->Quote( '^{"address":"(.+)?","lat":"(.+)?","lng":"(.+)?","zoom":"[0-9]{1,2}"}$', false );
		
		$query = 'select i.id, i.typeid, i.created, i.created_by, i.hits, a.lat, a.lng, a.address, a.zoom from #__joomd_'.$type->app.' as i join #__joomd_type'.$type->id.' as t on i.id=t.itemid join #__joomd_field_address as a on t.field_'.$field->id.'=a.id';			

		
		$query .= ' where '.implode(' and ', $where);
		
		if($limit)
			$query .= ' limit '.$limit;
		
		
		$db->setQuery( $query );
		$items = (array)$db->loadObjectList();
				
		for($i=0;$i<count($items);$i++)	{
			
			$address = $_field->getfieldvalue($items[$i]->id, $field->id);
			
			if($field->type==1)	{
				$file = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=true');
				
				$json = json_decode($file);
				
				$items[$i]->lat = $json->geometry->location->lat;
				$items[$i]->lng = $json->geometry->location->lng;
				$items[$i]->address = $address;
				
			}
			
			$items[$i]->html = '';
			
			$fields = $_field->getFields(array('itemid'=>$items[$i]->id, 'ids'=>$fs));
			
			if(count($fields) < 1)
				$fields = array($_field->get_firstfield(array('published'=>1)));
			
			$n=0;
			for($j=0;$j<count($fields);$j++)	{
				
				$value = $_field->getfieldvalue($items[$i]->id, $fields[$j]->id);
				
				if(!empty($value))	{
					
					$items[$i]->html .= '<div class="field_row">';
					
					if($fields[$j]->showtitle or $fields[$j]->showicon)	{
						$items[$i]->html .= '<div class="field_label">';
						if($fields[$j]->showtitle)
							$items[$i]->html .= $fields[$j]->name;
						if($fields[$j]->showicon and is_file(JPATH_SITE.DS.'images'.DS.'joomd'.DS.$fields[$j]->icon))
							$items[$i]->html .= '<img src="'.JURI::root().'images/joomd/'.$fields[$j]->icon.'" alt="" style="max-height:16px;" align="absbottom" />';
						$items[$i]->html .= '</div>';
					}
					
					$items[$i]->html .= '<div class="field_value '.$fields[$j]->cssclass.'">';
					if($n==0)
						$items[$i]->html .= '<a href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$items[$i]->typeid.'&id='.$items[$i]->id).'">'.$_field->displayfieldvalue($items[$i]->id, $fields[$j]->id, array('short'=>true)).'</a>';
					else
						$items[$i]->html .= $_field->displayfieldvalue($items[$i]->id, $fields[$j]->id, array('short'=>true));
						
					$items[$i]->html .= '</div>';
					
					$items[$i]->html .= '<div class="clr"></div></div>';
					
				}
				
			}
			
		}
		
		return $items;
		
	}

}
