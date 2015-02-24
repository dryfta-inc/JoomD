<?php

/*------------------------------------------------------------------------
# mod_joomd_tagcloud - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomd_tagcloudHelper
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
		
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$date = JFactory::getDate();
		$now = $date->toMySQL();
		
		$query = 'select avg(i.hits) as avg, min(i.hits) as min, max(i.hits) as max from #__joomd_'.$type->app.' as i ';
		
		$where = array();
		
		$where[] = 'i.published = 1';
		
		$where[] = 'i.typeid = '.$type->id;
		
		$where[] = 'i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$where[] = 'i.publish_up <= '.$db->Quote($now);
				
		$where[] = '( i.publish_down >= '.$db->Quote($now).' or i.publish_down = "0000-00-00 00:00:00" )';
		
		if(count($cats))
			$where[] = ' i.id in (select itemid from #__joomd_'.$type->app.'_cat where catid in ('.implode(', ', $cats).'))';
		
		if($featured)
			$where[] = ' i.featured = 1';
			
		$filter = ' where '.implode(' and ', $where);
		
		$query .= $filter;
		
		$db->setQuery( $query );
		$obj = $db->loadObject();
		echo $db->getErrorMsg();
		
		$query = 'select i.id, i.hits from #__joomd_'.$type->app.' as i ';
		$query .= $filter.' order by i.hits desc';
		
		if($limit)
			$query .= ' limit ' . $limit;
			
		$db->setQuery( $query );
		
		$obj->items = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		return $obj;
		
	}

}
