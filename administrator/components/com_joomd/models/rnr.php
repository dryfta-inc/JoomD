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
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.model' );


class JoomdModelRnr extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	var $_warnings = array();
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.rnr.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
		
		// In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$this->_filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',	'filter_state',	'',	'word' );
		$this->_filter_user	= $mainframe->getUserStateFromRequest( $context.'filter_user',	'filter_user',	0,	'int' );
		$this->_filter_type	= $mainframe->getUserStateFromRequest( $context.'filter_type',	'filter_type',	0,	'int' );
		$this->_filter_item	= $mainframe->getUserStateFromRequest( $context.'filter_item',	'filter_item',	0,	'int' );
		$this->_filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		
		$this->_filter_search			= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search			= JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );		

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setWarning($msg)
	{
		
		array_push($this->_warnings, $msg);
		
	}
	
	function _buildQuery()
	{
		$query = 'select i.*, t.name as type, if(u.id, u.name, "'.JText::_('ANONYMOUS').'") as user from #__joomd_reviews as i left join #__joomd_types as t on i.typeid=t.id left join #__users as u on i.created_by=u.id ';

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
		$this->item->akey = $this->_akey;
		$this->item->filter_search = $this->_filter_search;
		$this->item->filter_user = $this->_filter_user;
		$this->item->filter_type = $this->_filter_type;
		$this->item->filter_item = $this->_filter_item;
		$this->item->filter_state = $this->_filter_state;
		$this->item->filter_order = $this->_filter_order;
		$this->item->filter_order_Dir = $this->_filter_order_Dir;
		
        // Load the type if it doesn't already exist
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
			
		if($this->_filter_user)
			$where[] = 'i.created_by = '.$this->_filter_user;
			
		if($this->_filter_type)
			$where[] = 'i.typeid = '.$this->_filter_type;
			
		if($this->_filter_item)
			$where[] = 'i.itemid = '.$this->_filter_item;
		
		if(!empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(u.name) regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(u.name) like '.$this->_db->Quote( $akey.'%', false );
			
		}
		
		if($this->_filter_search)	{
			
			$where2 = array();
			
			$where2[] = 'i.id = '.$this->_db->Quote( $this->_db->getEscaped( $this->_filter_search, true ), false );
			
			$where2[] = 'LOWER( u.name ) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $this->_filter_search, true ).'%', false );
			
			$where2[] = 'LOWER( i.name ) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $this->_filter_search, true ).'%', false );
			
			$where2[] = 'LOWER( i.comment ) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $this->_filter_search, true ).'%', false );
			
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
	
	function getUsers()
    {
		$query = 'select u.id, u.name from #__users as u join #__joomd_reviews as r on u.id=r.created_by order by u.name asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
				
        return $rows;
    }
	
	function getTypes()
    {
		$query = 'select * from #__joomd_types order by name asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
				
        return $rows;
    }
	
	function getPluginitems()
    {
		
		if($this->_filter_type)	{
			
			$typeid = $this->_filter_type;
			
		}
		else	{
			$item = $this->getItem();

			$typeid = JRequest::getInt('typeid', $item->typeid);

		}
		
		
		if(!$typeid)
			return array();
		
		$type = Joomd::getType($typeid);
		
		$field = new JoomdAppField();
		$field->setType($type->id);
		
		$firstfield = $field->get_firstfield();
		
		if($firstfield->id)
			$query = 'select i.id, t.field_'.$firstfield->id.' as title from #__joomd_'.$type->plugin.' as i join #__joomd_type'.$type->id.' as t on i.id=t.itemid join #__joomd_reviews as r on i.id=r.itemid where i.typeid = '.$type->id.' group by i.id order by i.id asc';
		else
			$query = 'select i.id, i.id as title from #__joomd_'.$type->plugin.' as i join #__joomd_reviews as r on i.id=r.itemid where i.typeid = '.$type->id.' group by i.id order by i.id asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
        return $rows;
    }
	
	function store()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		$post = JRequest::get('post');
		$post['id'] = JRequest::getInt('id', 0);
		
		$obj = new stdClass();
		
		$obj->result = 'error';
			
		$row =  $this->getTable();
		
		if(!$row->bind($post))	{
			$obj->error = $row->getError();
			return $obj;
		}
		
		if(!$row->check())	{
			$obj->error = $row->getError();
			return $obj;
		}
		
		// If there was an error with registration, set the message and display form
		if ( !$row->store() )
		{
			$obj->error = $row->getError();
			return $obj;
		}
		
		if(!$post['id'])
			$obj->id = $row->id;
				
		$obj->result = 'success';
		$obj->msg = JText::_('REVIEWSAVESUCCESS');
				
		return $obj;
		
	}
	
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );		

		if (count( $cid ) < 1) {
			$this->setError( JText::_( 'PLZSELECTANITEM', true ) );
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
	
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		
		if(count($cid) < 1)	{
			$this->setError(JText::_('PLZSELECTANITEM'));
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