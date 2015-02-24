<?php

/*------------------------------------------------------------------------
# mod_joomd_newsletter - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomd_newsletterHelper
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
		
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		
		$query = 'select * from #__joomd_category where published = 1';
		
		$query .= ' and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$query .= ' and id in (select catid from #__joomd_tnc where typeid = '.(int)$type->id.')';
		
		$query .= ' order by name asc';
			
		$db->setQuery( $query );
		
		$items = $db->loadObjectList();
		
		return $items;
		
	}

}