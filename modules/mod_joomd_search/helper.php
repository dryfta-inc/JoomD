<?php

/*------------------------------------------------------------------------
# mod_joomd_search - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomd_searchHelper
{
	static function getType($id)
	{
		
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		
		$query = 'select * from #__joomd_types where published = 1 and id = '.$id;
		
		$query .= ' and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
			
		$db->setQuery( $query );
		$type = $db->loadObject();
		
		return $type;
		
	}
	
	static function getCats($typeid)
	{	
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$query = 'select catid from #__joomd_tnc where typeid = '.$typeid;
		$db->setQuery( $query );
		$cats = (array)$db->loadResultArray();
		
		if(!count($cats))
			$cats = array(0);
		
		$query = 'select *, name as title, parent as parent_id from #__joomd_category where published = 1 and id in ('.implode(', ', $cats).')';
		
		$query .= ' and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$query .= ' order by name asc';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		$children = array();
		// first pass - collect children
			
		foreach ($rows as $v )
		{
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
			
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ) );
		
		$items = array_slice($list, 0);
		
		return $items;
		
	}

}