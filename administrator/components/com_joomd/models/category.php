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


class JoomdModelCategory extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	var $_warnings = array();
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.category.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
		
		// In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$this->_filter_language		= $mainframe->getUserStateFromRequest( $context.'filter_language',	'filter_language',	'' );
		
		$this->_filter_type	= $mainframe->getUserStateFromRequest( $context.'filter_type',	'filter_type',	1,	'int' );
		$this->_filter_state	= $mainframe->getUserStateFromRequest( $context.'filter_state',	'filter_state',	'',	'word' );
		
		$this->_filter_order	= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.ordering', 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		
		$this->_filter_search			= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search			= JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );
		
		$this->_config = Joomd::getConfig();		

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setWarning($msg)
	{
		
		array_push($this->_warnings, $msg);
		
	}
	
	function _buildQuery()
	{
		$query = 'select i.*, i.name as title, i.parent as parent_id from #__joomd_category as i left join #__languages as l on l.lang_code = i.language ';

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
			$this->_db->setQuery($query);
			$search = $this->_db->loadObjectList();
			
			$query = $this->_buildQuery();
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();
			
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
			
			$list1 = array();
			
			foreach($list as $l)	{
				
				foreach($search as $s)	{
					
					if($l->id == $s->id)
						$list1[] = $l;
					
				}
				
			}

			if($this->_limit)
				$this->_data = array_slice($list1, $this->_limitstart, $this->_limit);
			else
				$this->_data = array_slice($list1, $this->_limitstart);
		
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
		$this->item->filter_type = $this->_filter_type;
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
		
		if ( $this->_filter_type )	{
			
			$query = 'select catid from #__joomd_tnc where typeid = ' . $this->_filter_type;
			$this->_db->setQuery( $query );
			$cats = (array)$this->_db->loadResultArray();
			
			$where[] = count($cats)?'i.id in ('.implode(', ', $cats).')':'i.id = 0';
		}
		
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
	
	function &getTypes()
    {
		$query = 'select *, false as selected from #__joomd_types order by name asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		
		if($this->_id)	{
		
			$query = 'select typeid from #__joomd_tnc where catid = ' . $this->_id;
			$this->_db->setQuery( $query );
			$types = (array)$this->_db->loadResultArray();
			
			for($i=0;$i<count($rows);$i++)	{
			
				if(in_array($rows[$i]->id, $types))
					$rows[$i]->selected = true;
				else
					$rows[$i]->selected = false;
			
			}
		
		}
		elseif(count($rows) == 1)
			$rows[0]->selected = true;
				
        return $rows;
    }
	
	function &getCats()
    {
		
		$types = JRequest::getVar('types', array(), '', 'array');
		
		$where = '';
		
		if(count($types))	{
		
			$query = 'select catid from #__joomd_tnc where typeid in ('.implode(', ', $types).')';
			$this->_db->setQuery( $query );
			$cats = (array)$this->_db->loadResultArray();
			
			$where = count($cats)?' and id in ('.implode(', ', $cats).')':' and id = 0';
		
		}
		
		$query = 'select *, name as title, parent as parent_id from #__joomd_category where id <> '.$this->_id.$where.' order by name asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
			
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
		
		$types = JRequest::getVar('types', array(), '', 'array');
		
		$where = '';
		
		if(count($types))	{
		
			$query = 'select fieldid from #__joomd_tnf where typeid in ('.implode(', ', $types).')';
			$this->_db->setQuery( $query );
			$fields = (array)$this->_db->loadResultArray();
			
			$where = count($fields)?' where id in ('.implode(', ', $fields).')':' where id = 0';
		
		}
		
		$query = 'select f.id, f.name, if(cf.fieldid, 1, 0) as selected from #__joomd_field as f left join #__joomd_cnf as cf on (f.id=cf.fieldid and cf.catid='.$this->_id.') '.$where.' order by f.name asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	function getOrder_list()
	{
		
		$query = 'select * from #__joomd_category order by ordering asc';
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
		$post['fulltext'] = JRequest::getVar('fulltext', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		$obj->file[0]->error = '';
		
		$image = JRequest::getVar('img', null, 'FILES', 'array');
		
		$allowed = array('.jpg', '.jpeg', '.gif', '.png', '.bmp');
		$max_size = 2000000;
		$time = time();
				
		jimport('joomla.filesystem.file');
				
		$image_name    = str_replace(' ', '', JFile::makeSafe($image['name']));		
		$image_tmp     = $image["tmp_name"];
			
		if($image_name <> "")	{
				
			$ext = strrchr($image_name, '.');
				
			if(!in_array($ext, $allowed))	{
				$obj->error = $obj->file[0]->error = JText::_('THISIMGNALL');
				return $obj;
			}
			
			if(filesize($image_tmp) > $max_size)	{
				$obj->error = $obj->file[0]->error = JText::_('URIMGEXEEDMAXFSIZ');
				return $obj;
			}
			
			if(!move_uploaded_file($image_tmp, JPATH_SITE.'/images/joomd/'.$time.$image_name))	{
				$obj->error = $obj->file[0]->error = JText::_('IMAGE_NOT_UPLOADAED');
				return $obj;				
			}
			
			$post['img'] = $time.$image_name;
						
			if($this->_config->thumb_width or $this->_config->thumb_height)	{
			
				if(Joomd::create_scaled_image(JPATH_SITE.'/images/joomd/'.$post['img'], array('max_width'=>$this->_config->thumb_width, 'max_height'=>$this->_config->thumb_height)) === false)	{
					$obj->error = $obj->file[0]->error = JText::_('THUMB_NOT_CREATED');
					return $obj;
				}
				
				$obj->file[0]->thumbnail_url = JURI::root().'images/joomd/thumbs/'.$post['img'];
			
			}
			else
				$obj->file[0]->thumbnail_url = JURI::root().'images/joomd/'.$post['img'];
			
			unset($obj->file[0]->error);
			
			$obj->file[0]->name = $post['img'];
			$obj->file[0]->url = JURI::root().'images/joomd/'.$post['img'];
			$obj->file[0]->delete_url = 'index.php?option=com_joomd&view=category&task=delete_img&id=0&abase=1';
			$obj->file[0]->delete_type = 'POST';
			
		}
		
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
		
		$obj->id = $row->id;
		$obj->alias = $row->alias;
		
		if(isset($obj->file[0]->delete_url))
			$obj->file[0]->delete_url = str_replace('id=0', 'id='.$row->id, $obj->file[0]->delete_url);
		
		unset($obj->file[0]->error);
		$obj->result = 'success';
		$obj->msg = JText::_('SAVESUCCESS');
		
		return $obj;
		
	}
	
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );

		$cids = JRequest::getVar( 'cid', array(), '', 'array' );		
		
		JArrayHelper::toInteger($cid);
		
		if (count( $cids ) < 1) {
			$this->setError( JText::_( 'PLSSELALEAONERE', true ) );
			return false;
		}
		
		$row =  $this->getTable();
			
		foreach ($cids as $cid)
		{
			$this->_addChildren($cid, $cids);
		}
	
		$id = implode(', ', (array)$cids);
	
		$query = 'delete c, t from #__joomd_category as c left join #__joomd_tnc as t on c.id = t.catid where c.id in ( '.$id.' )';
		$this->_db->setQuery( $query );
		if(!$this->_db->query())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		return true;
	}
	
	function _addChildren($id, &$list)
	{
		// Initialize variables
		$return = true;

		// Get all rows with parent of $id
		$query = 'SELECT id' .
				' FROM #__joomd_category' .
				' WHERE parent = '.(int) $id;
		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();

		// Make sure there aren't any errors
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Recursively iterate through all children... kinda messy
		// TODO: Cleanup this method
		foreach ($rows as $row)
		{
			$found = false;
			foreach ($list as $idx)
			{
				if ($idx == $row->id) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$list[] = $row->id;
			}
			$return = $this->_addChildren($row->id, $list);
		}
		return $return;
	}
	
	function delete_img()
	{
		
		$id = JRequest::getInt('id', 0);
		
		if(!$id)	{
			$this->setError(JText::_('PLSSELAFTODEL'));
			return false;
		}
		
		$query = 'select img from #__joomd_category where id = '.$id;
		$this->_db->setQuery( $query );
		$img = $this->_db->loadResult();
		
		if(!empty($img) and is_file(JPATH_SITE.'/images/joomd/'.$img))	{
			
			unlink(JPATH_SITE.'/images/joomd/'.$img);
			
			if(!empty($img) and is_file(JPATH_SITE.'/images/joomd/thumbs/'.$img))	{
				unlink(JPATH_SITE.'/images/joomd/thumbs/'.$img);
			}
			
		}
		
		$query = 'update #__joomd_category set img = "" where id = '.$id;
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
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
			$this->setError( $row->getError() );
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
		$obj->msg = $featured?JText::_('CATFEATUREDSUCCESS'):JText::_('CATUNFEATUREDSUCCESS');
		
		return $obj;
	
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
			$this->setError( $row->getError() );
			return false;
		}
		
		return true;
		
	}
	
}

?>