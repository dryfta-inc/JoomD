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


class JoomdModelType extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	var $_warnings = array();
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.type.list.'; 
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
	
	function setWarning($msg)
	{
		
		array_push($this->_warnings, $msg);
		
	}
	
	protected function _buildQuery()
	{
		$query = 'select i.*, a.label as app from #__joomd_types as i left join #__joomd_apps as a on i.appid=a.id left join #__languages as l on l.lang_code = i.language ';

		return $query;
	}
	
	protected function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function &getItem()
    {
		
		$row =  $this->getTable();
		
		$row->load($this->_id);
		
		$config = Joomd::getConfig('item');			
				
		if(empty($row->config))	{
			$row->config	= $config->config;
			$row->config->set('template', $config->template);
		}
		else	{
			$registry = new JRegistry;
			$registry->loadString($row->config);
			$row->config = $registry;
		}
		
		if(empty($row->acl))	{
			$row->acl	= $config->acl;
		}
		else	{
			$registry = new JRegistry;
			$registry->loadString($row->acl);
			$row->acl = $registry;
		}
		
		if(empty($row->listconfig))
			$row->listconfig	= $config->listconfig;
		else	{
			$registry = new JRegistry;
			$registry->loadString($row->listconfig);
			$row->listconfig = $registry;
		}
		
		if(empty($row->detailconfig))
			$row->detailconfig	= $config->detailconfig;
		else	{
			$registry = new JRegistry;
			$registry->loadString($row->detailconfig);
			$row->detailconfig = $registry;
		}
		
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
	
	protected function _buildItemFilter()
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
	
	protected function _buildItemOrderBy()
	{
 
        $orderby = ' group by i.id ORDER BY '.$this->_filter_order.' '.$this->_filter_order_Dir;
 
        return $orderby;
	}
	
	function getOrder_list()
	{
		
		$query = 'select * from #__joomd_types order by ordering asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
	
	}
	
	function getApps()
	{
		
		$query = 'select * from #__joomd_apps where item = 1';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	function getThemes()
	{
		
		$model = $this->getInstance('Config', 'JoomdModel');
		$model->scanthemes();
		
		$query = 'select * from #__joomd_templates order by name asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	function &getCats()
    {
		
		$query = 'select *, name as title, parent as parent_id, if(tc.catid, 1, 0) as selected from #__joomd_category as c left join #__joomd_tnc as tc on (c.id=tc.catid and tc.typeid='.$this->_id.') order by c.name asc';
		
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
	
	function getFields()
	{
		
		$query = 'select f.id, f.name, if(tf.fieldid, 1, 0) as selected from #__joomd_field as f left join #__joomd_tnf as tf on (f.id=tf.fieldid and tf.typeid='.$this->_id.') order by f.name asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	function store()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$post = JRequest::get('post');
		$post['id'] = JRequest::getInt('id', 0);
		
		$post['config']			= json_encode(JRequest::getVar('config', array(), 'post', 'array'));
		$post['acl']			= json_encode(JRequest::getVar('acl', array(), 'post', 'array'));
		$post['listconfig']		= json_encode(JRequest::getVar('listconfig', array(), 'post', 'array'));
		$post['detailconfig']	= json_encode(JRequest::getVar('detailconfig', array(), 'post', 'array'));
		
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
		
		$obj->alias = $row->alias;
		
		$obj->result = 'success';
		$obj->msg = JText::_('SAVESUCCESS');
				
		return $obj;
		
	}
	
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );

		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );		

		if (count( $cid ) < 1) {
			$this->setError( JText::_( 'PLSSELALEAONERE', true ) );
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
			$this->setError(JText::_('PLSSELALEAONERE'));
			return false;
		}
		
		$row =  $this->getTable();
		
		if (!$row->publish($cid, $publish))	{
			$this->setError($row->getError());
			return false;
		}
		
		return true;
	
	}
	
	/**
	 * Moves the order of a record
	 */
	function reorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
				
		$ordering = JRequest::getVar('ordering', array(0), 'post', 'array');
		
		if(count($ordering) < 1)	{
			$this->setError( JText::_('NO_ITEM_FOUND') );
			return false;	
		}
		
		$items = $this->getItems();
		
		$row =  $this->getTable();
		
		for($i=0;$i<count($items);$i++)	{
			
			$order = substr($ordering[$i], 6);
			
			$row->load($items[$order]->id);				
			$row->ordering = $items[$i]->ordering;
			
			if(!$row->store())	{
				$this->setError($row->getError());
				return false;
			}
				
			
		}
		
		return true;
	}

	/**
	 * Saves the orders of the supplied list
	 */
	function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );


		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (empty( $cid )) {
			$this->setError( JText::_('NO_ITEM_SELECTED') );
			return false;
		}

		$total		= count( $cid );
		$row =  $this->getTable();

		$order 		= JRequest::getVar( 'ordering', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order);

		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError( $row->getError() );
					return false;
				}
			}
		}

		return true;
	}
	
	//to change the access level of items
	function access()
	{
		$mainframe =  JFactory::getApplication();

		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );

		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$task	= JRequest::getCmd( 'task' );

		if (empty( $cid )) {
			$this->setError(JText::_('NO_ITEM_SELECTED'));
			return false;
		}

		switch ( $task )
		{
			case 'accesspublic':
				$access = 0;
				break;

			case 'accessregistered':
				$access = 1;
				break;

			case 'accessspecial':
				$access = 2;
				break;
		}

		$row =  $this->getTable();
		$row->load( (int) $cid[0] );
		$row->access = $access;

		if ( !$row->store() ) {
			$this->setError($row->getError());
			return false;
		}
		
		return true;
		
	}
	
}

?>