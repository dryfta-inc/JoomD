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
defined('_JEXEC') or die('Restricted access');
 
jimport( 'joomla.application.component.model' );


class JoomdModelNewsletter extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	var $_warnings = array();
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.newsletter.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
		
		// In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$this->_filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',	'filter_state',	'',	'word' );
		$this->_filter_type	= $mainframe->getUserStateFromRequest( $context.'filter_type',	'filter_type',	0,	'int' );
		$this->_filter_cat	= $mainframe->getUserStateFromRequest( $context.'filter_cat',	'filter_cat',	0,	'int' );
		$this->_filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		
		$this->_filter_search			= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search			= JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );		

	}
	
	function setWarning($msg)
	{
		
		array_push($this->_warnings, $msg);
		
	}
	
	function _buildQuery()
	{
		$query = 'select i.*, t.name as type, c.name as cat from #__joomd_newsletter as i join #__joomd_types as t on i.typeid=t.id join #__joomd_category as c on i.catid=c.id ';

		return $query;
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
		echo $this->_db->getErrorMsg();
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
		$this->item->filter_type = $this->_filter_type;
		$this->item->filter_cat = $this->_filter_cat;
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
			
		if($this->_filter_type)
			$where[] = 'i.typeid = '.$this->_filter_type;
			
		if($this->_filter_cat)
			$where[] = 'i.catid = '.$this->_filter_cat;
		
		if(!empty($this->_akey) and $this->_akey <> "all")	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(i.email) regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(i.email) like '.$this->_db->Quote( $akey.'%', false );
			
		}
		
		if($this->_filter_search)	{
			
			$where2 = array();
			
			$where2[] = 'i.id = '.$this->_db->Quote( $this->_db->escape( $this->_filter_search, true ), false );
			
			$where2[] = 'LOWER( i.email ) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $this->_filter_search, true ).'%', false );
						
			$where[] = count($where2)?'('.implode(' or ', $where2).')':'';
			
		}
		
		$filter = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
		
        return $filter;
	}
	
	function _buildItemOrderBy()
	{
 
        $orderby = ' ORDER BY '.$this->_filter_order.' '.$this->_filter_order_Dir;
 
        return $orderby;
	}
	
	function getTypes()
    {
		$query = 'select * from #__joomd_types order by name asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
				
        return $rows;
    }
	
	function getCats()
    {
		
		if(!$this->_filter_type)
			return array();
		
		$type = Joomd::getType($this->_filter_type);
		
		if(!$type->id)
			return array();
		
		$query = 'select catid from #__joomd_tnc where typeid = '.$type->id;
		$this->_db->setQuery( $query );
		$ids = $this->_db->loadResultArray();
		
		$filter = count($ids)?'where id in ('.implode(',', $ids).')':'where id = 0';
		
		$query = 'select id, name from #__joomd_category '.$filter.' order by name asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		
        return $rows;
    }
	
	function store()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"success", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$id = JRequest::getInt('id', 0);
		
		$typeid = JRequest::getInt('typeid', 0);
		$catid = JRequest::getInt('catid', 0);
		
		$subject = JRequest::getVar('subject', '');
		$body = JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		
		if(!$typeid or !$catid or empty($subject) or empty($body))	{
			$obj->error = JText::_('PLZENTERALLTHEREQUIREDFIELDS');
			return $obj;
		}
			
		if($id)	{
			$query = 'update #__joomd_newstemp set typeid = '.$typeid.', catid = '.$catid.', subject = '.$this->_db->Quote($subject).', body = '.$this->_db->Quote($body).' where id = '.$id;			
		}
		else	{
			$query = 'insert into #__joomd_newstemp (typeid, catid, subject, body) values ('.$typeid.', '.$catid.', '.$this->_db->Quote($subject).', '.$this->_db->Quote($body).')';
		}
		
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$obj->error = $this->_db->getErrorMsg();
			return $obj;
		}
		
		if(!$id)
			$obj->id = $this->_db->insertid();
				
		$obj->result = 'success';
		$obj->msg = JText::_('EMAILTEMPLATESAVESUCCESS');
				
		return $obj;
		
	}
	
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"success", "error":"'.JText::_('INVALIDTOKEN').'"}' );

		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );		

		if (count( $cid ) < 1) {
			$this->setError( JText::_( 'PLZSELECTANITEM', true ) );
			return false;
		}
		
		$query = 'delete from #__joomd_newsletter where id in ('.implode(',', $cid).')';
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	
	function get_temp()
	{
		
		$typeid = JRequest::getInt('typeid', 0);
		$catid = JRequest::getInt('catid', 0);
		
		$query = 'select id, subject, body from #__joomd_newstemp where typeid = '.$typeid.' and catid = '.$catid;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		if(empty($item))	{
			$item->id = 0;
			$item->subject = '';
			$item->body = '';
		}
		
		$item->result = "success";
		
		return $item;
		
	}
	
	function send_newsletter()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$app = JFactory::getApplication();
		
		$id 	= JRequest::getInt( 'id', 0, 'post' );
		
		if(!$id)	{
			$this->setError(JText::_('CANFINDANEWSLETTER'));
			return false;
		}
		
		$query = 'select i.*, t.name as type, c.name as cat from #__joomd_newstemp as i join #__joomd_types as t on i.typeid=t.id join #__joomd_category as c on i.catid=c.id where i.id = '.$id.' limit 1';
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		if(empty($item))	{
			$this->setError(JText::_('CANFINDANEWSLETTER'));
			return false;
		}
		
		$type = Joomd::getType($item->typeid);
		
		$sitename = $app->getCfg('sitename');
		
		$body = $item->body;
		
		$uri = JURI::getInstance();
		
		$regex = '/href="(.*)"/i';
		
		preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$body = str_replace($match[1], $url, $body);
				}
				
			}
			
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$body = str_replace($match[1], JURI::root().$match[1], $body);
				}
				
			}
			
		}

		
		if(strpos($body, '{sitename}'))	{
			$body = str_replace('{sitename}', $sitename, $body);
		}
		
		if(strpos($body, '{siteurl}'))	{
			$body = str_replace('{siteurl}', JURI::root(), $body);
		}
		
		if(strpos($body, '{typename}'))	{
			$body = str_replace('{typename}', $item->type, $body);
		}
		
		if(strpos($body, '{catname}'))	{
			$body = str_replace('{catname}', $item->cat, $body);
		}
		
		if(strpos($body, '{itemlistlink}'))	{
			$url = JURI::root().substr(JRoute::_('index.php?option=com_joomd&view='.$type->app.'&typeid='.$type->id.'&catid='.$item->catid), strlen(JURI::base(true))+1);
			$body = str_replace('{itemlistlink}', $url, $body);
		}
			
		$query = 'select email from #__joomd_newsletter where typeid = '.(int)$item->typeid.' and catid = '.(int)$item->catid;
		$this->_db->setQuery( $query );
		$emails = (array)$this->_db->loadResultArray();

		$emails = array_unique($emails);
		
		if(count($emails) < 1)	{
			$this->setError(JText::_('NOUSER'));
			return false;
		}
		
		foreach($emails as $email)	{
			
			$sbody = $body;
			
			if(strpos($body, '{email}'))	{
				$sbody = str_replace('{email}', $email, $body);
			}
		
			$sent = Joomd::notify($item->subject, $sbody, array('to'=>$email));
		
			if(is_object($sent))	{
				$this->setError(JText::_('NEWSLETTERNOTSENT'));
				return false;
			}
			
		}
				
		return true;
		
	}
	
}

?>