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
 
class JoomdModelCategory extends JModel
{
		
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$this->config = Joomd::getConfig();
		
		$context			= 'com_joomd.category.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
 
        // In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$menus		=  $mainframe->getMenu();
		$menu    	= $menus->getActive();
		
		//get the order by from the menu parameters
		$params = $mainframe->getParams();
		
		if(is_object($menu))	{
		if(!strstr($menu->link, 'view=category'))
			$params->set('orderby', 'i.ordering asc');
		}
		$orderby = explode(' ', $params->def('orderby', 'i.ordering asc'));
		
		$orderby[1] = isset($orderby[1])?$orderby[1]:'';
		
		$this->_filter_order	= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', $orderby[0], 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', $orderby[1], 'word' );
		$this->_filter_search = $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search = JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );
		
		$this->typeid = $mainframe->getUserStateFromRequest( $context.'typeid', 'typeid', 0,	'int' );
		
		if(!$this->typeid)
			$this->jdapp->redirect('index.php', JText::_('TYPENOTRECOGNIZED'));
		
		$this->_type = Joomd::getType();
		
		$this->catid = JRequest::getInt('catid', 0);
		$this->featured = JRequest::getInt('featured', 0);
		
		$this->Itemid = JRequest::getVar('Itemid', '');
		
		$this->_user =  Joomd::getUser();
		
	}
	
	function _buildQuery()
	{
		
		$query = 'select i.*, count(a.id) as items from #__joomd_category as i';
		
		$query .= $this->_buildSubquery();

		return $query;
		
	}
	
	function _buildSubquery()
	{
		
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		
		$query = ' left join #__joomd_'.$this->_type->app.'_cat as ip on i.id=ip.catid left join #__joomd_'.$this->_type->app.' as a on ';
		
		$where = array();
		
		$where[] = 'ip.itemid=a.id';
		$where[] = 'a.published = 1';
		$where[] = 'a.typeid = '.$this->_type->id;
		$where[] = 'a.access in ('.implode(',', $this->_user->getAuthorisedViewLevels()).')';
		
		if($app->getLanguageFilter())	{
			$where[] = 'a.language in ('.$this->_db->quote($lang->getTag()).', '.$this->_db->Quote('*').')';
		}
		
		$filter = count($where)?(' ( '.implode(' and ', $where).' ) '):'';
		 
		$query .= $filter;
		
		return $query;
		
	}
	
	function getItems()
	{
		
		if(empty($this->_data))	{
		
			$query = $this->_buildQuery();
			
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
		
			$this->_data = $this->_getList($query, $this->_limitstart, $this->_limit);
			echo $this->_db->getErrorMsg();
		}
		
		for($i=0;$i<count($this->_data);$i++)
		{
			
			$query = $this->_buildQuery();
			
			$filter = $this->_buildItemFilter($this->_data[$i]->id);
			$query .= $filter;
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			$this->_db->setQuery( $query );
			$this->_data[$i]->child = $this->_db->loadObjectList();
			
		}
		
		return $this->_data;
		
	}
	
	function _buildItemFilter($parent = 0)
	{
        $mainframe =  JFactory::getApplication();
		$lang = JFactory::getLanguage();
				
		if(!$parent)
			$parent = $this->catid;
		
        $where = array();
		
		if($mainframe->getLanguageFilter())	{
			$where[] = 'i.language in ('.$this->_db->quote($lang->getTag()).', '.$this->_db->Quote('*').')';
		}
		
		$where[] = 'i.published = 1';

		$where[] = 'i.access in ('.implode(',', $this->_user->getAuthorisedViewLevels()).')';
					
		$query = 'select catid from #__joomd_tnc where typeid = '.$this->typeid;
		$this->_db->setQuery( $query );
		$cats = (array)$this->_db->loadResultArray();
		
		$where[] = count($cats)?'i.id in ('.implode(', ', $cats).')':'i.id=0';
		
		if($this->featured)	{
			$where[] = 'i.featured = 1';
		}
		
		if($this->config->asearch and !empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(i.name) regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(i.name) like '.$this->_db->Quote( $akey.'%', false );
			
		}
		
		$where[] = 'i.parent = ' . $parent;
								
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
		
		$this->item = new stdClass();
		
		$this->item->option = 'com_joomd';
		$this->item->view = 'category';
		$this->item->abase = 1;
		$this->item->task = '';
		$this->item->Itemid = $this->Itemid;
		$this->item->limit = $this->_limit;
		$this->item->limitstart = $this->_limitstart;
		$this->item->akey = $this->_akey;
		$this->item->filter_search = $this->_filter_search;
		$this->item->typeid = $this->typeid;
		$this->item->catid = $this->catid;
		$this->item->featured = $this->featured;
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
	
	function getParent()
	{
		
		if($this->catid)	{
			
			$query = 'select * from #__joomd_category where published = 1 and id = '.$this->catid;
			$this->_db->setQuery( $query );
			$item = $this->_db->loadObject();
			
			return $item;
			
		}
		
		return null;
		
	}
	
	function newsletter_signup()
	{
		
		$obj = new stdClass();
		
		$obj->result = "error";
		
		$typeid = JRequest::getInt('typeid', 0);
		$cats = JRequest::getVar('cats', array(), 'post', 'array');
		$email = JRequest::getVar('email', '');
		
		JArrayHelper::toInteger( $cats );
		
		if(!$typeid)	{
			$obj->error = JText::_('TYPENOTFOUND');
			return $obj;
		}
		
		if(!count($cats))	{
			$obj->error = JText::_('PLEASESELECTCATEGORY');
			return $obj;
		}
		
		if($email == "")	{
			$obj->error = JText::_('ENTEREMAIL');
			return $obj;
		}
		
		if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email))	{
			$obj->error = JText::_('ENTERVALIDEMAIL');
			return $obj;
		}
		
		foreach($cats as $cat)	{
		
			$query = 'select count(*) from #__joomd_newsletter where typeid = '.(int)$typeid.' and catid = '.$cat.' and email = '.$this->_db->Quote($email);
			$this->_db->setQuery( $query );
			$count = $this->_db->loadResult();
			
			if($count)	{
				continue;
			}
			
			$query = 'insert into #__joomd_newsletter (typeid, catid, email) values('.(int)$typeid.', '.$cat.', '.$this->_db->Quote($email).')';
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$obj->error = $this->_db->getErrorMsg();
				return $obj;
			}
		
		}
		
		$obj->result = "success";
		$obj->msg = JText::_('SIGNEDUPSUCCESSFULLY');
		
		return $obj;
		
	}
	
}
