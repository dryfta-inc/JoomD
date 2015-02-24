<?php
 /*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Mohammad arshi - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.model' );


class joomdModelPackages extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.packages.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
		
		// In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$this->_filter_language		= $mainframe->getUserStateFromRequest( $context.'filter_language',	'filter_language',	'' );
		
		$this->_filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',	'filter_state',	'',	'word' );
		
		$this->_filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.ordering', 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		
		$this->_filter_search			= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search			= JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );

		$array = JRequest::getVar('cid',  0, '', 'array');
		
		$this->setId((int)$array[0]);
	}
	
	function _buildQuery()
	{
		$query = 'select i.* from #__joomd_package as i left join #__languages as l on l.lang_code = i.language ';

		return $query;
		
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function &getItem()
    {
		
		$row =  $this->getTable();
		
		$row->load($this->_id);
		
		$registry = new JRegistry;
		$registry->loadString($row->params);
		$row->params = $registry;
		
        return $row;
    }
	
	function &getItems()
    {
        if(empty($this->_data))	{
		
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			
			$this->_data = $this->_getList($query, $this->_limitstart, $this->_limit);
		
		}
		
        return $this->_data;
    }
	
	function getParams()
  	{
		$this->item = new stdClass();
		
		$this->item->limit = $this->_limit;
		$this->item->limitstart = $this->_limitstart;
		$this->item->filter_language = $this->_filter_language;
		$this->item->akey = $this->_akey;
		$this->item->filter_search = $this->_filter_search;
		$this->item->filter_state = $this->_filter_state;
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
        return $this->item;
  	}
	
	function _buildItemFilter()
	{

        $where = array();
		
		if ( $this->_filter_state == 'P' )
			$where[] = 'i.published = 1';
		
		else if ($this->_filter_state == 'U' )
			$where[] = 'i.published = 0';
			
		if(!empty($this->_filter_language))
			$where[] = 'i.language = '.$this->_db->Quote($this->_filter_language);
		
		if(!empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(i.name) regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(i.name) like '.$this->_db->Quote( $akey.'%', false );
			
		}
		
		if($this->_filter_search)	{
			
			$where2 = array();
			
			$where2[] = 'i.id = '.$this->_db->Quote( $this->_db->escape( $this->_filter_search, true ), false );
			
			$where2[] = 'LOWER( i.name ) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $this->_filter_search, true ).'%', false );
			
			$where[] = count($where2)?'('.implode(' or ', $where2).')':'';
			
		}
		
		$filter = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
		 
        return $filter;
	}
	
	function _buildItemOrderBy()
	{
 
        $orderby = ' group by i.id ORDER BY '.$this->_filter_order.' '.$this->_filter_order_Dir;
 
        return $orderby;
	}
	
	function getOrder_list()
	{
		
		$query = 'select * from #__joomd_package order by name asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		return $items;
	
	}
	
	function getTypes()
	{
		
		$query = 'select * from #__joomd_types order by name asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		
		return $items;
		
	}
	
	function &getCats()
    {
		
		$types = JRequest::getVar('types', array(), 'post', 'array');
		
		$where = '';
		
		if(count($types))	{
		
			$query = 'select catid from #__joomd_tnc where typeid in ('.implode(', ', $types).')';
			$this->_db->setQuery( $query );
			$cats = (array)$this->_db->loadResultArray();
			
			$where = count($cats)?'where c.id in ('.implode(', ', $cats).')':'where c.id = 0';
		
		}
		
		$query = 'select *, name as title, parent as parent_id from #__joomd_category as c '.$where.' order by name asc';
		
		$this->_db->setQuery($query);
		$rows = (array)$this->_db->loadObjectList();
			
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
		
		$data = array_slice($list, 0);
		
        return $data;
    }

	function store()
	{
    	
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		//fatching the all the posted variable 
		$post = JRequest::get( 'post' );
		$post['params'] = json_encode(JRequest::getVar('params', array(), 'post', 'array'));
		
		$obj = new stdClass();
		
		$obj->result = 'error';
				
		//getting the table 
		$row =  $this->getTable();
				
		if(!$post['period'])	{
			$obj->error = JText::_('PLEASEENTERAPERIOD');
			return $obj;
		}
		
		// Assigning Create date 
		if(!$post["id"]){
			$query = 'select ordering from #__joomd_package order by ordering desc limit 1';
			$this->_db->setQuery( $query );
			$post['ordering']   = $this->_db->loadResult() +1;
		
			$date =  JFactory::getDate();
			$post['created'] = $date->toMySQL();
		
		}
				
		//binding the posted data to the table
		if (!$row->bind( $post )){
        	$obj->error = $row->getError() ;
			return $obj;
		}
		
		//storing the posted data in the table
		else if (!$row->store()){
        		$obj->error =$row->getError();
			return $obj;
		}
		else{
					
			$obj->result = 'success';
						
			$obj->msg = JText::_('SAVESUCCESS');
			
									
		}
		if(!$post['id'])	{
			$obj->id = $this->_db->insertid();
		}
				
		return $obj;
			
	}
	
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );

		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );

		JArrayHelper::toInteger( $cid );
		

		if (count( $cid ) < 1) {
			$this->setError( JText::_( 'PSELECTSJPACAGETODELETE', true ) );
			return false;
		}
		
		$row =  $this->getTable();

		foreach ($cid as $id)
		{
			
			if(!$row->delete($id))	{
				
				$this->setError($row->getError());
				return false;
				
			}

		}

		return true;
	}
	
	function publish()
	{
	
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		
		if(count($cid) < 1)	{
			$this->setError(JText::_('PLEASESELECTATLEASTONERECORD'));
			return false;
		}
		
		$row =  $this->getTable();
		
		if (!$row->publish($cid, $publish))	{
			$this->setError($row->getError());
			return false;
		}
		
		return true;
	
	}
	
}

?>