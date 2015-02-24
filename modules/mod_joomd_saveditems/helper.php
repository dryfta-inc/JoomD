<?php

/*------------------------------------------------------------------------
# mod_joomd_saveditems - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomd_saveditemsHelper
{
	static function getType($typeid)
	{
		
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		
		if($user->get('guest'))
			return array();
		
		$query = 'select t.*, a.name as app from #__joomd_types as t join #__joomd_apps as a on (t.appid=a.id) where a.published = 1 and t.published = 1 and t.id = '.$typeid;
		
		$query .= ' and t.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
			
		$db->setQuery( $query );
		$type = $db->loadObject();
		
		return $type;
		
	}
	
	static function getItems($type, $params)
	{	
		
		$limit = (int)$params->get('limit', 5);
		$order = $params->get('orderby', 'i.ordering asc');
	
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$date = JFactory::getDate();
		$now = $date->toMySQL();		
		
		if($user->get('guest'))
			return array();
		
		$query = 'select id from #__joomd_'.$type->app.' as i join #__joomd_type'.$type->id.' as t on i.id=t.itemid ';
		
		$where = array();
		
		$where[] = 'i.published = 1';
		
		$where[] = 'i.typeid = '.$type->id;
		
		$where[] = 'i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$where[] = 'i.publish_up <= '.$db->Quote($now);
				
		$where[] = '( i.publish_down >= '.$db->Quote($now).' or i.publish_down = "0000-00-00 00:00:00" )';
					
		$q = 'select itemid from #__joomd_user_'.$type->app.' where userid = '.$user->id;
		$db->setQuery( $q );
		$ids = (array)$db->loadResultArray();
		
		$where[] = count($ids)?('i.id in ( '.implode(',', $ids).' )'):'i.id=0';
		
		$query .= ' where '.implode(' and ', $where);
		
		$query .= ' order by '.$order;
		
		if($limit)
			$query .= ' limit ' . $limit;
			
		$db->setQuery( $query );
		
		$items = $db->loadObjectList();
		echo $db->getErrorMsg();
		return $items;
		
	}

}
