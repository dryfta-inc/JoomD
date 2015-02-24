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
 

class TableCategory extends JTable
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
    
	var $parent = null;
    var $name = null;
	var $alias = null;
	var $featured = 0;
	var $introtext = null;
	var $fulltext = null;
	var $img = null;
	var $created = null;
	var $created_by = null;
	var $ordering = null;
	var $published = 1;
	var $access = null;
	var $hits = null;
	var $language = '*';
    
    function TableCategory( &$db ) {
        parent::__construct('#__joomd_category', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		
		$user =  JFactory::getUser();
		
		if(!$array['id'])	{
			
			$date =  JFactory::getDate();
			$array['created'] = $date->toMySQL();
			
			$array['created_by'] = (int)$user->get('id');
			
			$array['ordering'] = 1;
			$query = 'select ordering from #__joomd_category order by ordering desc limit 1';
			$this->_db->setQuery( $query );
			$array['ordering'] += $this->_db->loadResult();
			
		}
		
		return parent::bind($array, $ignore);
		
	}
	
	function check()
	{
		
		$this->id = intval($this->id);
		
		if($this->name == "")	{
			$this->setError(JText::_('PLSENTCATN'));
			return false;
		}
		
		$query = 'select count(*) from #__joomd_category where id <> '.$this->id.' and lower(name) = '.$this->_db->Quote(strtolower($this->name));
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			$this->setError(JText::_('TITLEALREADYEXISTS'));
			return false;
		}
		
		$this->alias = empty($this->alias)?$this->name:$this->alias;
		
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		
		$query = 'select count(*) from #__joomd_category where alias = "'.$this->alias.'" and id <> '.$this->id;
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			$this->setError(JText::sprintf('DUPLICATEALIAS', $this->alias));
			return false;
		}
		
		if(trim(str_replace('-','',$this->alias)) == '') {
			$datenow =  JFactory::getDate();
			$this->alias = $datenow->format("Y-m-d-H-i-s");
		}
		
		return parent::check();
		
	}
	
	function store($updateNulls = false)
	{
		
		if(!parent::store($updateNulls))	{
			return false;
		}
		
		if(isset($_POST['types']))	{
		
			$types = JRequest::getVar('types', array(), 'post', 'array');
			JArrayHelper::toInteger($types);
			
			$query = 'delete from #__joomd_tnc where catid = '.$this->id;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			foreach($types as $type)	{
			
				$query = 'insert into #__joomd_tnc values('.$type.', '.$this->id.')';
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
			}
		
		}
		
		if(isset($_POST['fields']))	{
		
			$fields = JRequest::getVar('fields', array(), 'post', 'array');
			JArrayHelper::toInteger($fields);
			
			$query = 'delete from #__joomd_cnf where catid = '.$this->id;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			foreach($fields as $field)	{
			
				$query = 'insert into #__joomd_cnf values('.$this->id.', '.$field.')';
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
			}
		
		}
		
		return true;		
		
	}
	
}
