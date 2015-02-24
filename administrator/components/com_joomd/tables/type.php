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
 

class TableType extends JTable
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
    
    var $name = null;
	var $alias = null;
	var $descr = null;
	var $appid = null;
	var $ordering = null;
	var $published = 1;
	var $access = null;
	var $language = '*';
	var $config = null;
	var $acl = null;
	var $listconfig = null;
	var $detailconfig = null;
    
    function TableType( &$db ) {
        parent::__construct('#__joomd_types', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		
		if(!$array['id'])	{
			
			$array['ordering'] = 1;
			$query = 'select ordering from #__joomd_types order by ordering desc limit 1';
			$this->_db->setQuery( $query );
			$array['ordering'] += $this->_db->loadResult();
			
		}
		
		return parent::bind($array, $ignore);
		
	}
	
	function check()
	{
		
		$this->id = intval($this->id);
		$this->appid = intval($this->appid);
		
		if($this->name == "")	{
			$this->setError( JText::_('PLSENTTYPTIT') );
			return false;
		}
		
		$query = 'select count(*) from #__joomd_types where id <> '.$this->id.' and lower(name) = '.$this->_db->Quote(strtolower($this->name));
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			$this->setError( JText::_('TITLEALREADYEXISTS'));
			return false;
		}
		
		if(!$this->appid)	{
			$this->setError( JText::_('PLSSELAPP'));
			return false;
		}
		
		$this->alias = empty($this->alias)?$this->name:$this->alias;
		
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		
		$query = 'select count(*) from #__joomd_types where alias = "'.$this->alias.'" and id <> '.$this->id;
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
		
		$query = 'show tables like '.$this->_db->Quote($this->_db->getPrefix().'joomd_type'.$this->id);
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObject();
		
		if(empty($result))	{
			
			$query = 'CREATE TABLE IF NOT EXISTS `#__joomd_type'.$this->id.'` (
				  `itemid` int(11) NOT NULL,
				  PRIMARY KEY (`itemid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
		}
		
		if(isset($_POST['cats']))	{
			
			$cats = JRequest::getVar('cats', array(), 'post', 'array');
			JArrayHelper::toInteger($cats);
			
			$query = 'delete from #__joomd_tnc where typeid = '.$this->id;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			foreach($cats as $cat)	{
				
				$query = 'insert into #__joomd_tnc values('.$this->id.', '.$cat.')';
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
			
			$query = 'delete from #__joomd_tnf where typeid = '.$this->id;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			foreach($fields as $field)	{
			
				$query = 'insert into #__joomd_tnf values('.$this->id.', '.$field.')';
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
			}
			
		}
		
		return true;
		
	}
	
	function delete($oid=null)
	{
		
		if(!parent::delete($oid))	{
			return false;
		}
		
		$query = 'DROP TABLE IF EXISTS `#__joomd_type'.$oid.'`;';
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'delete f,c from #__joomd_tnf as f inner join #__joomd_tnc as c using(typeid) where typeid = '.$oid;
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;
		
	}
	
}
