<?php

/*------------------------------------------------------------------------
# mod_joomd_myitem - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2011 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomd_myitemsHelper
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
		
		if($user->get('guest'))
			return array();
		
		$query = 'select i.id from #__joomd_'.$type->app.' as i join #__joomd_type'.$type->id.' as t on i.id=t.itemid ';
		
		$where = array();
		
		$where[] = 'i.typeid = '.$type->id;
		
		$where[] = 'i.created_by = '.(int)$user->id;
		
		$query .= ' where '.implode(' and ', $where);
		
		$query .= ' order by '.$order;
		
		if($limit)
			$query .= ' limit ' . $limit;
			
		$db->setQuery( $query );
		
		$items = $db->loadObjectList();
		
		return $items;
		
	}

}
