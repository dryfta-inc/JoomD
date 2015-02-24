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
defined('_JEXEC') or die('Restricted access');
 

class TableRnr extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
 
    /**
     * @var string
     */
    
    var $rate = 0;
	var $name = null;
	var $comment = null;
	var $ip = null;
	var $typeid = null;
	var $itemid = null;
	var $created = null;
	var $created_by = null;
	var $published = 0;
    
    function TableRnr( &$db ) {
        parent::__construct('#__joomd_reviews', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		
		if(!$array['id'])	{
			
			$user =  JFactory::getUser();
			$date = JFactory::getDate();
			$now = $date->toMySQL();
			
			$array['created'] = $now;
			$array['created_by'] = $user->id;
			$array['ip'] = $_SERVER['REMOTE_ADDR'];
			
		}
		
		return parent::bind($array, $ignore);
		
	}
	
	function check()
	{
		
		$this->id = intval($this->id);
		
		if(!$this->typeid)	{
			$this->setError( JText::_('TYPENOTFOUND') );
			return false;
		}
		
		if(!$this->itemid)	{
			$this->setError( JText::_('ITEMNOTFOUND') );
			return false;
		}
		
		if(empty($this->rate))	{
			$this->setError( JText::_('PLZGIVESOMERATING') );
			return false;
		}
		
		return parent::check();
		
	}
	
}
