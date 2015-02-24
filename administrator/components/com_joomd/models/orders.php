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


class JoomdModelOrders extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.orders.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
		
		// In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
				
		$this->_filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		
		$this->_filter_search			= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search			= JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function _buildQuery()
	{
		$query = 'select i.*,u.username,p.name as pname from #__joomd_orders as i join #__users as u on i.userid=u.id join #__joomd_package as p on i.packid=p.id';

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
		
		if(empty($this->_data))	{
		
			$query = 'select i.*,u.username,p.name as pname from #__joomd_orders as i left join #__users as u on i.userid=u.id left join #__joomd_package as p on i.packid=p.id  WHERE i.id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		
		}
		
		if(!$this->_data)	{
			$this->_data->id = 0;
			$this->_data->name = null;
			$this->_data->pname = null;
			$this->_data->userid = null;
			$this->_data->username = null;
			$this->_data->order_number = null;
			$this->_data->packid = null;
			$this->_data->payment_date = null;
			$this->_data->payment_price = null;
			$this->_data->payment_currency = null;
			$this->_data->payment_method= null;
			$this->_data->order_status = null;
			$this->_data->credit = null;
			$this->_data->credit_expiry  = null;
			$this->_data->txn_id = null;
			$this->_data->createdon = null;
		}
		
        return $this->_data;
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
		echo $this->_db->getErrorMsg();
        return $this->_data;
		
    }
	
	function getParams()
  	{
		$this->item = new stdClass();
		
		$this->item->limit = $this->_limit;
		$this->item->limitstart = $this->_limitstart;
		$this->item->akey = $this->_akey;
		$this->item->filter_search = $this->_filter_search;
		$this->item->filter_order = $this->_filter_order;
		$this->item->filter_order_Dir = $this->_filter_order_Dir;
		
        // Load the store if it doesn't already exist
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
	
	//filter function 
	function _buildItemFilter()
	{

        $where = array();
		
		if(!empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(u.username) regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(u.username) like '.$this->_db->Quote( $akey.'%', false );
			
		}
		
		if($this->_filter_search)	{
			
			$where2 = array();
			
			$where2[] = 'i.id = '.$this->_db->Quote( $this->_db->escape( $this->_filter_search, true ), false );
			
			$where2[] = 'LOWER( u.username ) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $this->_filter_search, true ).'%', false );
			$where2[] = 'LOWER( i.order_number ) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $this->_filter_search, true ).'%', false );
			$where2[] = 'LOWER( i.id ) = '.$this->_db->Quote( $this->_db->escape( $this->_filter_search, true ), false );
			
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
	
}

?>