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
defined( '_JEXEC' ) or die( 'Restricted access' );


class plgSearchJoomd extends JPlugin
{
	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	function onContentSearchAreas(){
		return $this->onSearchAreas();
	}

	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null){
		return $this->onSearch($text, $phrase = '', $ordering = '', $areas = null);
	}

	function onSearchAreas()
	{
			
		$types = (array)$this->params->get( 'typeid', null );
		
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		
		$lang = JFactory::getLanguage();
		$lang->load('com_joomd');
		
		$query = 'select i.id, i.name, a.name as app from #__joomd_types as i join #__joomd_apps as a on (i.appid=a.id and a.published =1) where i.published = 1 ';
		
		if(count($types))
			$query .= ' and i.id in ('.implode(',', $types).')';
			
		$query .= ' and i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
			
		$query .= ' order by i.ordering asc';
		
		$db->setQuery( $query );
		$this->types = $db->loadObjectList();
		
		static $areas = array();
		
		foreach($this->types as $type)
			$areas['joomd_'.$type->id] = $type->name;
		
		return $areas;
	}
	
	
	function onSearch( $text, $phrase='', $ordering='', $areas=null )
	{
		
		$types = array();
		
		if (is_array( $areas )) {
			$find = array_intersect( $areas, array_keys( $this->onSearchAreas() ) );
			if (!$find) {
				return array();
			}
			else	{
				for($i=0;$i<count($this->types);$i++)	{
					if(in_array('joomd_'.$this->types[$i]->id, $find))	{
						$types[] = $this->types[$i];
					}
				}
					
			}
		}
		else
			$types = $this->types;
		
		$mainframe =& JFactory::getApplication();
	
		$db	=& JFactory::getDBO();
		$user =& JFactory::getUser();
		
		$plugin =& JPluginHelper::getPlugin('search', 'joomd');
	
		$cats = (array)$this->params->get( 'catid', null );
		$ids = (array)$this->params->get( 'fields', null );
		$limit = $this->params->get( 'limit', 50 );
		
		if(!class_exists('JoomdFields'))
			require_once(JPATH_SITE.'/components/com_joomd/libraries/classes/fields.php');
					
		$list = array();
		
		for($i=0;$i<count($types);$i++)	{
			
			$_field = new JoomdFields($types[$i]->id);
					
			$fields = $_field->getFields(array('ids'=>$ids));
			
			if(count($fields)<1)
				continue;
			
			$firstfield = $_field->get_firstfield(array('published'=>1));
			
			$where = array();
			
			$where[] = 'i.published = 1';
			
			$where[] = 'i.typeid = '.$types[$i]->id;
			
			$where[] = ' i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
				
			if(count($cats))	{
				
				$query = 'select itemid from #__joomd_'.$types[$i]->app.'_cat where catid in ('.implode(',', $cats).')';
				$db->setQuery( $query );
				$cids = (array)$db->loadResultArray();
				
				$where[] = count($cids)?'i.id in ('.implode(',', $cids).')':'i.id=0';
				
			}
			
			switch($phrase)
			{
			
				case 'exact':
				$k = $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
				
				$wheres 	= array();
				
				foreach($fields as $field)	{
					
					$wheres[] = 't.field_'.$field->id.' like '.$k;
					
				}
				
				$query = 'select id from #__joomd_category where published = 1 and name like '.$k.' and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
					
				$q = 'select catid from #__joomd_tnc where typeid = '.$types[$i]->id;
				$db->setQuery( $q );
				$catids = (array)$db->loadResultArray();
				
				$query .= count($catids)?(' and id in ('.implode(',', $catids).')'):' and id=0';
				
				$db->setQuery( $query );
				$catids = (array)$db->loadResultArray();
	
				if(count($catids))	{
					
					$query = 'select itemid from #__joomd_'.$types[$i]->app.'_cat where catid in ('.implode(',', $catids).')';
					$db->setQuery( $query );
					$ids = (array)$db->loadResultArray();
					
					if(count($ids))
						$wheres[] = 'i.id in ('.implode(', ', $ids).')';
				
				}
				
				$where[] = '(' . implode( ' or ', $wheres ) . ')';
				
				break;
				
				default:
				
				$words = explode( ' ', $text );
				$wheres = array();
				
				//search in field values
				
				foreach($fields as $field)	{
				
					$wheres2 	= array();
				
					foreach ($words as $word) {
						$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
					
						$wheres2[] = 't.field_'.$field->id.' like '.$word;

						
					}
					
					$wheres[] 	= '(' . implode( ($phrase == 'all' ? ' AND ' : ' OR '), $wheres2 ) . ')';
					
				}
				//search in field values
				
				//search in category title
				
				$wheres2 	= array();
				
				foreach ($words as $word) {
					$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
					
					$wheres2[] = 'name like '.$word;
					
				}
				
				$query = 'select id from #__joomd_category where published = 1';
				
				$query .= ' and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
					
					
				$q = 'select catid from #__joomd_tnc where typeid = '.$types[$i]->id;
				$db->setQuery( $q );
				$catids = (array)$db->loadResultArray();
				
				$query .= count($catids)?(' and id in ('.implode(',', $catids).')'):' and id=0';
				
				$query 	.= ' and (' . implode( ($phrase == 'all' ? ' AND ' : ' OR '), $wheres2 ) . ')';
				
				$db->setQuery( $query );
				$catids = (array)$db->loadResultArray();
	
				if(count($catids))	{
					
					$query = 'select itemid from #__joomd_'.$types[$i]->app.'_cat where catid in ('.implode(',', $catids).')';
					$db->setQuery( $query );
					$ids = (array)$db->loadResultArray();
					
					if(count($ids))
						$wheres[] = 'i.id in ('.implode(', ', $ids).')';
				
				}
				//search in category title
				
				
				$where[] = '(' . implode( ' or ', $wheres ) . ')';
							
				break;
			
			}
		
			$query = 'select i.id, i.created from #__joomd_'.$types[$i]->app.' as i join #__joomd_type'.$types[$i]->id.' as t on ( t.itemid = i.id )';
		
			$query .= ' where '.implode(' and ', $where);
				
			$query .= ' group by i.id order by ';
			
			switch($ordering)
			{
				
				case 'newest';
				$query .= 'i.id desc';
				break;
				
				case 'oldest';
				$query .= 'i.id asc';
				break;
				
				case 'popular';
				$query .= 'i.hits desc';
				break;
				
				case 'alpha';
				$query .= 't.field_'.$firstfield->id.' asc';
				
				break;
				
				default:
				$query .= 'i.ordering asc';
				break;
				
			}
		
		
			$query .= ' limit '.$limit;
			
			$db->setQuery( $query );
			$items = $db->loadObjectList();
			echo $db->getErrorMsg();
			for($j=0;$j<count($items);$j++)	{
		
				$items[$j]->title = $_field->displayfieldvalue($items[$j]->id, $firstfield->id, true);
				$items[$j]->section = $types[$i]->name;
				$items[$j]->browsernav = 2;
				$items[$j]->href = JRoute::_('index.php?option=com_joomd&view='.$types[$i]->app.'&layout=detail&typeid='.$types[$i]->id.'&id='.$items[$j]->id);
				
				array_push($list, $items[$j]);
				
			}
		
		}
		
		return $list;
	
	}

}
