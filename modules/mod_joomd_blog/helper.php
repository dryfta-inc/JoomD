<?php

/*------------------------------------------------------------------------
# mod_joomd_blog - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomd_blogHelper
{
	static function getType($typeid)
	{
		
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		
		$query = 'select t.*, a.name as app from #__joomd_types as t join #__joomd_apps as a on (t.appid=a.id) where a.published = 1 and t.published = 1 and t.id = '.$typeid;
		
		$query .= ' and t.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
			
		$db->setQuery( $query );
		$type = $db->loadObject();
		
		return $type;
		
	}
	
	static function getItems($type, $params)
	{	
		$limit = (int)$params->get('limit', 5);
		
		$cats = (array)$params->get('cats', array());
		$featured = (int)$params->get('featured', 0);
		$order = $params->get('orderby', 'i.ordering asc');
		
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$date = JFactory::getDate();
		$now = $date->toMySQL();
		
		$query = 'select i.*, t.* from #__joomd_'.$type->app.' as i join #__joomd_type'.$type->id.' as t on i.id=t.itemid';
				
		$where = array();
		
		$where[] = 'i.published = 1';
		
		$where[] = 'i.typeid = '.$type->id;
		
		$where[] = 'i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$where[] = 'i.publish_up <= '.$db->Quote($now);
				
		$where[] = '( i.publish_down >= '.$db->Quote($now).' or i.publish_down = "0000-00-00 00:00:00" )';
		
		if(count($cats))
			$where[] = 'i.id in (select itemid from #__joomd_'.$type->app.'_cat where catid in ('. implode(',', $cats).'))';
		
		if($featured)
			$where[] = 'i.featured = 1';
			
		$query .= ' where '.implode(' and ', $where);
		
		$query .= ' order by '.$order;
		
		if($limit)
			$query .= ' limit ' . $limit;

		 
		$db->setQuery( $query );
		
		$items = $db->loadObjectList();
		
		for($i=0;$i<count($items);$i++)	{
			
			$query = 'select id, name from #__joomd_category where id in ( select catid from #__joomd_'.$type->app.'_cat where itemid = '.$items[$i]->id.' )';
			$db->setQuery( $query );
			$items[$i]->cats = (array)$db->loadObjectList();
			
		}
		
		return $items;
		
	}

}
