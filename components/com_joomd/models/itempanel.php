<?php

/*------------------------------------------------------------------------
# com_joomd - Joomd Manager
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
 
class JoomdModelItempanel extends JModel
{
	
	var $_notice=null;
	
	function __construct()
	{
		parent::__construct();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomd'.DS.'tables');
		
		$mainframe =  JFactory::getApplication();
		$this->jdapp = Joomd::getApp();
		
		$context = 'com_joomd.itempanel.list.'; 
 
        $this->config = Joomd::getConfig('item');
		$this->_user = Joomd::getUser('item');
		
		// Get pagination request variables
		$this->limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->limitstart = JRequest::getInt('limitstart', 0 );
 
        // In case limit has been changed, adjust it
        $this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
		
		$this->_filter_language		= $mainframe->getUserStateFromRequest( $context.'filter_language',	'filter_language',	'' );
		
		$this->_typeid = $mainframe->getUserStateFromRequest( $context.'typeid', 'typeid', 0,	'int' );
		$this->_type = Joomd::getType($this->_typeid);
		
		$this->filter_cat		= $mainframe->getUserStateFromRequest( $context.'filter_cat',	'filter_cat',	0,	'int' );
		$this->filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',	'filter_state',	'',	'word' );
		$this->filter_order	= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.ordering', 'cmd' );
        $this->filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$this->filter_search = $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->filter_search = JString::strtolower( $this->filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );
		
		$this->Itemid = JRequest::getVar('Itemid', '');
		
		$this->_field = new JoomdAppField();
		
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
		
	}
	
	//to display the notice if it's ajax
	function getNotice()
	{
		
		return $this->_notice;
		
	}
	
	function setNotice($msg=null)
	{
		
		$this->_notice = $msg;
		
	}
	//to display the notice if it's ajax
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function _buildQuery()
	{
		
		$query = 'SELECT i.*, t.* from #__joomd_item as i join #__joomd_type'.$this->_typeid.' as t on i.id=t.itemid left join #__languages as l on l.lang_code = i.language ';
		
		return $query;
	}
	
	function &getItem()
    {
		
		$row =  $this->getTable('item');
		
		$item = $row->loaditem($this->_id);
		
		if($item->id and !Joomd::canEdit($item))
			$this->jdapp->redirect(JRoute::_('index.php?option=com_joomd&view=itempanel&typeid='.$this->_typeid),$this->jdapp->getError());
		elseif(!$item->id and !Joomd::isAuthorised('addaccess'))
			$this->jdapp->redirect(JRoute::_('index.php?option=com_joomd&view=itempanel&typeid='.$this->_typeid), $this->jdapp->getError());
		
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
			
			$this->_data = $this->_getList($query, $this->limitstart, $this->limit);
		}

        return $this->_data;
    }
	
	function _buildItemFilter()
	{
       
	    $where = array();
		
		$where[] = 'i.typeid = '.$this->_typeid;
		
		if(!Joomd::isAuthorised('manageall'))
			$where[] = ' i.created_by = '.(int)$this->_user->id;
				
		if ( $this->filter_state == 'P' )
			$where[] = 'i.published = 1';
		
		else if ($this->filter_state == 'U' )
			$where[] = 'i.published = 0';
			
		if(!empty($this->_filter_language))
			$where[] = 'i.language = '.$this->_db->Quote($this->_filter_language);
		
		if ($this->filter_cat)	{
			
			$query = 'select itemid from #__joomd_item_cat where catid = '.$this->filter_cat;
			$this->_db->setQuery( $query );
			$ids = $this->_db->loadResultArray();
			
			$in = count($ids)?implode(',', $ids):'0';
			
			$where[] = ' i.id in ( '.$in.' )';
			
		}
		
		$firstfield =  $this->_field->get_firstfield();
		
		if($this->config->asearch and !empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(t.field_'.$firstfield->id.') regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(t.field_'.$firstfield->id.') like '.$this->_db->Quote( $akey.'%', false );
			
		}

		if($this->filter_search)	{
			
			$where2 = array();
			
			$where2[] = 'LOWER( t.field_'.$firstfield->id.' ) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $this->_filter_search, true ).'%', false );

			$where[] = count($where2)?'( '.implode(' or ', $where2).' )':'i.id=0';
		}
		
		$filter = count($where) ? ' where ' . implode(' and ', $where) : '';		

        return $filter;
	}
	
	function _buildItemOrderBy()
	{
 
        $orderby = ' group by i.id order by '.$this->filter_order.' '.$this->filter_order_Dir;
 
        return $orderby;
		
	}
	
	function getParams()
  	{	
		
		$layout = JRequest::getCmd('layout', 'default');
		
		$item = new stdClass();
		
		$item->option		= 'com_joomd';
		$item->view			= 'itempanel';
		$item->abase		= 1;
		$item->task			= '';
		$item->Itemid		= $this->Itemid;
		$item->typeid 		= $this->_typeid;
		
		if($layout == 'default')	{
			
			$item->boxchecked	= 0;
			$item->limit		= $this->limit;
			$item->limitstart	= $this->limitstart;
			$item->filter_language = $this->_filter_language;
			$item->filter_cat	= $this->filter_cat;
			$item->akey 		= $this->_akey;
			$item->filter_search= $this->filter_search;
			$item->filter_state = $this->filter_state;
			$item->filter_order = $this->filter_order;
			$item->filter_order_Dir = $this->filter_order_Dir;
			
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			$item->total = (int) $this->_getListCount($query);
		
		}
		else
			$item->id = $this->_id;
		
        return $item;
  	}
	
	function getCats()
    {
		
		$wheres = Joomd::getCategoryFilter();
				
		$query = 'select catid from #__joomd_tnc where typeid = '.$this->_typeid;
		$this->_db->setQuery( $query );
		$cats = (array)$this->_db->loadResultArray();
		
		$where = count($cats)?('i.id in ('.implode(', ', $cats).')'):'id = 0';
		array_push($wheres, $where);
		
		$filter = count($wheres)?' where '.implode(' and ', $wheres):'';
		
		$query = 'select i.*, i.name as title, i.parent as parent_id, if(ic.catid, true, false) as selected from #__joomd_category as i left join #__joomd_item_cat as ic on (i.id=ic.catid and ic.itemid='.(int)$this->_id.') '.$filter.' order by i.name asc';
		
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
		
		$data = array_slice($list, 0);
		
        return $data;
    }
	
	function store()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		$post = JRequest::get('post');
		$post['cats'] = JRequest::getVar('catid', array(), 'post', 'array');
		
		JArrayHelper::toInteger( $post['cats'] );
		
		$row =  $this->getTable('item');
		
		$row->load(JRequest::getInt('id', 0));
		
		try{
			Joomd::onBeforeStore();
		} catch(Exception $e)
		{
			$obj->error = $e->getMessage();
			return $obj;
		}
		
		if(!Joomd::canState($row))
			$post['published'] = (int)($this->_type->config->get('moderate')==0);
			
		if(!Joomd::canFeature($row))
			unset($post['featured']);
				
		if($row->id and !Joomd::canEdit($row))
			$this->jdapp->redirect(JRoute::_('index.php?option=com_joomd&view=itempanel&typeid='.$this->_typeid), $this->jdapp->getError());
		elseif(!$row->id and !Joomd::isAuthorised('addaccess'))
			$this->jdapp->redirect(JRoute::_('index.php?option=com_joomd&view=itempanel&typeid='.$this->_typeid), $this->jdapp->getError());
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		$obj->file = array();
		
		if($this->config->captcha and class_exists('plgSystemJoomdcaptcha'))	{
		
			$captchk = plgSystemJoomdcaptcha::check($post['captcha']);
		
			if ($captchk !== true)	{
      			$obj->error = JText::_('THECONCDUENTINC');
				return $obj;
			}
		}
		
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
		if(!$row->storeitem($post))	{
			$obj->error = $row->getError();
			return $obj;
		}
		
		$obj->id = $row->id;
		$obj->alias = $row->alias;
		
		
		if(!$post['id'])	{
			
			foreach((array)$obj->file as $file)	{
				
				if(isset($file->delete_url))	{
					
					$file->delete_url = str_replace('delete_custom&id=', 'delete_custom&id='.$obj->id, $file->delete_url);
					
				}
				
			}
			
		}
		
		try{
			Joomd::onAfterStore($row);
		} catch(Exception $e)
		{
			$obj->error = $e->getMessage();
			return $obj;
		}
				
		$obj->result = 'success';
		$obj->msg = JText::_('ITEMSVSUCC');
		
		return $obj;
		
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
		
		$row =  $this->getTable('item');
		
		foreach ($cid as $id)
		{
			
			if(!Joomd::canState($row))	{
				$this->setError(JText::_('AUTH_NOACCESS'));
				return false;
			}
			
			if(!$row->publish((array)$id, $publish))	{
				$this->setError($row->getError());
				return false;
				
			}

		}
		
		return true;
	
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
		
		$row =  $this->getTable('item');

		foreach ($cid as $id)
		{
			
			if(!Joomd::canDelete($row))	{
				$this->setError(JText::_('AUTH_NOACCESS'));
				return false;
			}
			
			if(!$row->delete($id))	{
				$this->setError($row->getError());
				return false;
				
			}

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
		
		$row =  $this->getTable('item');
		
		foreach($cids as $cid)	{
						
			$row->load($cid);				
			$row->featured = $featured;
			
			if(!Joomd::canFeature($row))	{
				$this->setError(JText::_('AUTH_NOACCESS'));
				return false;
			}
			
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
	
	function delete_custom()
	{
		
		$id = JRequest::getInt('id', 0);
		$fieldid = JRequest::getInt('fieldid', 0);
		$filename = JRequest::getVar('filename', null);
		
		if(!$id or !$fieldid)	{
			$this->setError(JText::_('PLSSELFTODEL'));
			return false;
		}
		
		$row =  $this->getTable('item');
		
		$row->load($id);
		
		if(!Joomd::canEdit($row))	{
			$this->setError(JText::_('AUTH_NOACCESS'));
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
	
}
