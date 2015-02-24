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


class JoomdModelItem extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	var $_warnings = array();
	var $_typeid = 0;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.item.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
		
		// In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$this->_filter_language		= $mainframe->getUserStateFromRequest( $context.'filter_language',	'filter_language',	'' );
		
		$this->_typeid = $mainframe->getUserStateFromRequest( 'typeid', 'typeid', 0, 'int');
		
		$this->_filter_cat		= $mainframe->getUserStateFromRequest( $context.'filter_cat',	'filter_cat',	0,	'int' );
		$this->_filter_state	= $mainframe->getUserStateFromRequest( $context.'filter_state',	'filter_state',	'',	'word' );
		
		$this->_filter_order	= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.ordering', 'cmd' );
        $this->_filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		
		$this->_filter_search	= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search	= JString::strtolower( $this->_filter_search );
		
		$this->_akey				= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey				= JString::strtolower( $this->_akey );
		
		$this->_field = new JoomdAppField();
		
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setWarning($msg)
	{
		
		array_push($this->_warnings, $msg);
		
	}
	
	function _buildQuery()
	{
		$query = 'select i.* from #__joomd_item as i join #__joomd_type'.$this->_typeid.' as t on i.id=t.itemid left join #__languages as l on l.lang_code = i.language ';

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
		
		$item = $row->loaditem($this->_id);
		
        return $item;
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
		
		$this->item->typeid = $this->_typeid;
		$this->item->limit = $this->_limit;
		$this->item->limitstart = $this->_limitstart;
		$this->item->filter_language = $this->_filter_language;
		$this->item->filter_cat = $this->_filter_cat;
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
		
		$where[] = 'i.typeid = '.$this->_typeid;
		
		if ( $this->_filter_state == 'P' )
			$where[] = 'i.published = 1';
		
		else if ($this->_filter_state == 'U' )
			$where[] = 'i.published = 0';
		
		if(!empty($this->_filter_language))
			$where[] = 'i.language = '.$this->_db->Quote($this->_filter_language);
		
		if ( $this->_filter_cat )	{
			
			$query = 'select itemid from #__joomd_item_cat where catid ='.$this->_filter_cat;
			$this->_db->setQuery( $query );
			$entries = (array)$this->_db->loadResultArray();
			
			$where[] = count($entries)?'i.id in ('.implode(', ', $entries).')':'i.id = 0';
		}
		
		$firstfield =  $this->_field->get_firstfield();
		
		if($firstfield->id and !empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(t.field_'.$firstfield->id.') regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(t.field_'.$firstfield->id.') like '.$this->_db->Quote( $akey.'%', false );
			
		}
		
		if($this->_filter_search)	{
			
			$where2 = array();
			
			$where2[] = 'i.id = '.$this->_db->Quote( $this->_db->escape( $this->_filter_search, true ), false );
			
			if($firstfield->id)
				$where2[] = 'LOWER( t.field_'.$firstfield->id.' ) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $this->_filter_search, true ).'%', false );
			
			$where[] = count($where2)?'( '.implode(' or ', $where2).' )':'i.id=0';
			
		}
		
		$filter = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
		 
        return $filter;
	}
	
	function _buildItemOrderBy()
	{
 
        $orderby = ' group by i.id ORDER BY '.$this->_filter_order.' '.$this->_filter_order_Dir;
 
        return $orderby;
	}
	
	function &getCats()
    {
		
		$where = '';
				
		$query = 'select catid from #__joomd_tnc where typeid = '.$this->_field->_typeid;
		$this->_db->setQuery( $query );
		$cats = (array)$this->_db->loadResultArray();
		
		$where = count($cats)?'where id in ('.implode(', ', $cats).')':' where id = 0';
		
		
		$query = 'select *, name as title, parent as parent_id, false as selected from #__joomd_category '.$where.' order by name asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		
		if($this->_id)	{
		
			$query = 'select catid from #__joomd_item_cat where itemid = ' . $this->_id;
			$this->_db->setQuery( $query );
			$cats = (array)$this->_db->loadResultArray();
			
			for($i=0;$i<count($rows);$i++)	{
			
				if(in_array($rows[$i]->id, $cats))
					$rows[$i]->selected = true;
				else
					$rows[$i]->selected = false;
			
			}
		
		}
			
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
	
	function getOrder_list()
	{
		
		$firstfield =  $this->_field->get_firstfield();
		
		$query = 'select t.field_'.$firstfield->id.', i.ordering from #__joomd_item as i join #__joomd_type'.$this->_typeid.' as t on i.id=t.itemid order by i.ordering asc';
			
		$this->_db->setQuery( $query );
		$items = $this->_db->loadRowList();
		
		return $items;
	
	}
	
	function store()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		$post = JRequest::get('post');
		$post['cats']	= JRequest::getVar('catid', array(), 'post', 'array');
				
		$obj = new stdClass();
		
		$obj->result = 'error';
		$obj->file = array();
		
		$row =  $this->getTable();
		
		if(!$row->bind($post))	{
			$obj->error = $row->getError();
			return $obj;
		}
		
		$obj = $row->checkitem($post);
		
		if($obj->result == 'error')	{
			return $obj;
		}
		
		$obj->result = 'error';	
		
		// If there was an error with registration, set the message and display form
		if ( !$row->storeitem($post) )
		{
			$obj->error = $row->getError();
			return $obj;
		}
		
		$obj->id = $row->id;
		$obj->alias = $row->alias;
		
		if(!$post['id'])	{
			
			foreach($obj->file as $file)	{
				
				if(isset($file->delete_url))	{
					
					$file->delete_url = str_replace('delete_custom&id=0', 'delete_custom&id='.$obj->id, $file->delete_url);
					
				}
				
			}
			
		}
		
		$query = 'delete from #__joomd_item_cat where itemid = '.$row->id;
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$obj->error = $this->_db->getErrorMsg();
			return $obj;
		}
		
		foreach($post['cats'] as $cat)	{
		
			$query = 'insert into #__joomd_item_cat values('.$cat.', '.$row->id.')';
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$obj->error = $this->_db->getErrorMsg();
				return $obj;
			}
			
		}
		
		$obj->result = 'success';
		$obj->msg = JText::_('SAVESUCCESS');
		
		return $obj;
		
	}
	
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );

		JArrayHelper::toInteger( $cid );
		

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
	
	function delete_custom()
	{
		
		$id = JRequest::getInt('id', 0);
		$fieldid = JRequest::getInt('fieldid', 0);
		$filename = JRequest::getVar('filename', null);
		
		if(!$id or !$fieldid)	{
			$this->setError(JText::_('PLSSELAFTODEL'));
			return false;
		}
		
		$val = $this->_field->getfieldvalue($id, $fieldid);
		
		if(!empty($val))	{
			
			$values = explode('|', str_replace(' ', '', $val));
			
			for($i=0;$i<count($values);$i++)	{
				$value = $values[$i];
				
				if(empty($filename))	{
				
					if(is_file(JPATH_SITE.'/images/joomd/'.$value))
						unlink(JPATH_SITE.'/images/joomd/'.$value);
						
					if(is_file(JPATH_SITE.'/images/joomd/thumbs/'.$value))
						unlink(JPATH_SITE.'/images/joomd/thumbs/'.$value);
						
					unset($values[$i]);
					
				}
				elseif($filename == $value)	{
					if(is_file(JPATH_SITE.'/images/joomd/'.$value))
						unlink(JPATH_SITE.'/images/joomd/'.$value);
					
					if(is_file(JPATH_SITE.'/images/joomd/thumbs/'.$value))
						unlink(JPATH_SITE.'/images/joomd/thumbs/'.$value);
						
					unset($values[$i]);	
					
					break;
				}
				
			}
			
			$val = implode('|', $values);
			
		}
		
		if(!$this->_field->updatefieldvalue($id, $fieldid, $val))	{
			$this->setError($this->_field->getError());
			return false;
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
	
	//to make it featured/unfeatured
	function featured()
	{
	
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$cids		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$featured	= ($task == 'featured')?1:0;
		$i = JRequest::getInt('i', 0);
		
		if(count($cids) < 1)	{
			$this->setError(JText::_('PLSSELALEAONERE'));
			return false;
		}
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		
		$row =  $this->getTable();
		
		foreach($cids as $cid)	{
						
			$row->load($cid);				
			$row->featured = $featured;
			
			if(!$row->store())	{
				$obj->error = $row->getError();
				return $obj;
			}
				
			
		}
		
		$obj->output = JHTML::_( 'jdgrid.featured', $row, $i );
		$obj->result = "success";
		$obj->msg = $featured?JText::_('ITEMFEATUREDSUCCESS'):JText::_('ITEMUNFEATUREDSUCCESS');
		
		return $obj;
	
	}
	
	/**
	 * Moves the order of a record
	 */
	function reorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
				
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
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );


		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (empty( $cid )) {
			$this->setError( JText::_('NO_ITEM_SELECTED') );
			return false;
		}

		$total		= count( $cid );
		$row =  $this->getTable();
		$groupings = array();

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
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$row->access = (int)$access;

		if ( !$row->store() ) {
			$this->setError($row->getError());
			return false;
		}
		
		return true;
		
	}
	
}

?>