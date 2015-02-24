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

// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

class JoomdModelSearch extends JModel
{
	
	var $last_time = 0;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.search.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
 
        // In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$menus		=  JSite::getMenu();
		$menu    	= $menus->getActive();
		
		//get the order by from the menu parameters
		$params = $mainframe->getParams();
		
		if(is_object($menu))	{
		if(!strstr($menu->link, 'view=search'))
			$params->set('orderby', 'i.ordering asc');
		}
		
		$orderby = explode(' ', $params->def('orderby', 'i.ordering asc'));

		
		$orderby[1] = isset($orderby[1])?$orderby[1]:'';
		
		$this->_filter_order	= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', $orderby[0], 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', $orderby[1], 'word' );
		
		$this->_typeid = $mainframe->getUserStateFromRequest( $context.'typeid', 'typeid', 0,	'int' );
		
		$this->_type = Joomd::getType($this->_typeid);
		
		$this->cats = JRequest::getVar('cats', array(), '', 'get', 'array');
		$this->Itemid = JRequest::getVar('Itemid', '');
		
		$this->_user = $this->getUser();
				
		$this->_field = new JoomdAppField();

	}
	
	function getUser()
	{
		
		return Joomd::getUser();
		
	}
	
	function _buildQuery()
	{
				
		$query = 'select i.* from #__joomd_'.$this->_type->app.' as i join #__joomd_type'.$this->_type->id.' as t on i.id=t.itemid ';
		
		return $query;
	}
	
	
	function &getItems()
    {
       
	   $query = $this->_buildQuery();
			
		$filter = $this->_buildItemFilter();
		$query .= $filter;
		$orderby = $this->_buildItemOrderBy();
		$query .= $orderby;
		
		$this->_data = $this->_getList($query, $this->_limitstart, $this->_limit);
		
        return $this->_data;
    }
	
	function _buildItemFilter()
	{
 
        $app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$date = JFactory::getDate();
		$now = $date->toMySQL();		
		
		$post = JRequest::get('get');
				
		$post['cats'] = JRequest::getVar('cats', array(), '', 'get', 'array');
		
		JArrayHelper::toInteger( $post['cats'] );
		
		$post['task'] = isset($post['task'])?$post['task']:null;
		
		$fields =  $this->_field->getFields(array('published'=>1, 'search'=>1));
		
		$where = array();
		
		if($app->getLanguageFilter())	{
			$where[] = 'i.language in ('.$this->_db->quote($lang->getTag()).', '.$this->_db->Quote('*').')';
		}
		
		$where[] = 'i.published = 1';
		
		$where[] = 'i.access in ('.implode(',', $this->_user->getAuthorisedViewLevels()).')';
		
		$where[] = 'i.typeid = '.$this->_type->id;
		
		$where[] = 'i.publish_up <= '.$this->_db->Quote($now);
				
		$where[] = '( i.publish_down >= '.$this->_db->Quote($now).' or i.publish_down = "0000-00-00 00:00:00" )';
		
		if(count($post["cats"]))
			$where[] = 'i.id in ( select itemid from #__joomd_'.$this->_type->app.'_cat where catid in ( '.implode(', ', $post["cats"]).' ) ) ';
		
		for($i=0;$i<count($fields);$i++)	{
		
			$custom = json_decode($fields[$i]->custom);
			$multiple = isset($custom->multiple)?$custom->multiple:0;			
			
			$value = isset($post['field_'.$fields[$i]->id])?$post['field_'.$fields[$i]->id]:null;
			
			if(!empty($value))	{
			
				if($fields[$i]->type == 14)	{
					
					$where[] = 't.field_'.$fields[$i]->id.' in ( select id from #__joomd_field_address where address like '.$this->_db->Quote( '%'.$this->_db->escape( $value, true ).'%', false ).' )';
					
				}
				elseif($fields[$i]->type == 3 or $multiple)	{
					
					if(count($value))	{
						
						$where2 = array();
						
						foreach($value as $v)
							$where2[] = 't.field_'.$fields[$i]->id.' like '.$this->_db->Quote( '%'.$this->_db->escape( $v, true ).'%', false );
							
						if(count($where2))
							$where[] = '('.implode(' or ', $where2).')';
						
					}
				}
				
				else	{
					
					$where[] = 't.field_'.$fields[$i]->id.' like '.$this->_db->Quote( '%'.$this->_db->escape( $value, true ).'%', false );
				
				}
			
			}
			
		}
	
			
		$filter = ' where ' . implode(' and ', $where);
		
		return $filter;
		
	}
	
	function _buildItemOrderBy()
	{
 
        $orderby = ' group by i.id order by '.$this->_filter_order.' '.$this->_filter_order_Dir;
 
        return $orderby;
	}
	
	function getParams()
  	{	
		
		$layout = JRequest::getCmd('layout', 'default');
		
		$this->item = new stdClass();
		
		$this->item->option = 'com_joomd';
		$this->item->view = 'search';
		$this->item->layout = 'search';
		$this->item->abase = 1;
		$this->item->task = '';
		$this->item->typeid = $this->_typeid;
		$this->item->Itemid = $this->Itemid;
		
		if($layout == "search")	{
			
			$post = JRequest::get('get');
			
			foreach($post as $k=>$v)
				$this->item->$k = $v;
			
			$this->item->cats = $this->cats;
			$this->item->limit = $this->_limit;
			$this->item->limitstart = $this->_limitstart;
			$this->item->filter_order = $this->_filter_order;
			$this->item->filter_order_Dir = $this->_filter_order_Dir;
			
			// Load the total if it doesn't already exist
			if (empty($this->_total)) {
				$query = $this->_buildQuery();
				$filter = $this->_buildItemFilter();
				$query .= $filter;
				$orderby = $this->_buildItemOrderBy();
				$query .= $orderby;
				$this->item->total = (int) $this->_getListCount($query);    
			}
		
		}
		
        return $this->item;
  	}
	
	function getTypes()
	{
		
		$query = 'select * from #__joomd_types where published = 1';
		
		$query .= ' and access in ('.implode(',', $this->_user->getAuthorisedViewLevels()).')';
		
		$query .= ' order by name asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	function getCats()
	{
		
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		
		$where = array();
		
		if($app->getLanguageFilter())	{
			$where[] = 'language in ('.$this->_db->quote($lang->getTag()).', '.$this->_db->Quote('*').')';
		}
		
		$where[] = 'published = 1';
		
		$where[] = 'access in ('.implode(',', $this->_user->getAuthorisedViewLevels()).')';
		
		if($this->_type->id)	{
			$query = 'select catid from #__joomd_tnc where typeid = '.$this->_type->id;
			$this->_db->setQuery( $query );
			$types = (array)$this->_db->loadResultArray();
			
			$where[] = count($types)?('id in ('.implode(',', $types).')'):'id=0';
		}
		
		$filter = ' where '.implode(' and ', $where);
		
		$query = 'select *, name as title, parent as parent_id from #__joomd_category'. $filter.' order by name asc';
		
		$this->_db->setQuery($query);
		$rows = (array)$this->_db->loadObjectList();
		
		if(!count($rows))
			return array();
		
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
		
		$cats = array_slice($list, 0);
		
        return $cats;
	
	}
	
}
