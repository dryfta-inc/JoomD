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
 

class TableField extends JTable
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
	var $text = null;
	var $default = null;
	var $type = null;
	var $custom = null;
	var $options = null;
	var $cssclass = null;
	var $category = null;
	var $list = null;
	var $detail = 1;
	var $search = null;
	var $required = null;
	var $showtitle = 1;
	var $showicon = null;
	var $icon = null;
	var $created = null;
	var $created_by = null;
	var $ordering = null;
	var $published = 1;
	var $access = null;
	var $language = '*';
    
    function TableField( &$db ) {
        parent::__construct('#__joomd_field', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		
		$options = isset($array['custom']['options'])?trim($array['custom']['options']):null;
		
		$temp = explode("\n", $options);
		$n=count($temp);
		for($i=0;$i<$n;$i++)	{
			
			$temp[$i] = trim($temp[$i]);
			
			if(empty($temp[$i]))	{
				unset($temp[$i]);
			}
			
		}
		
		$array['custom']['options'] = implode("\n", $temp);
		
		$user =  JFactory::getUser();
		
		if(!$array['id'])	{
			
			$date =  JFactory::getDate();
			$array['created'] = $date->toMySQL();
			
			$array['created_by'] = (int)$user->get('id');
			
			$array['ordering'] = 1;
			$query = 'select ordering from #__joomd_field order by ordering desc limit 1';
			$this->_db->setQuery( $query );
			$array['ordering'] += $this->_db->loadResult();
			
		}
		
		return parent::bind($array, $ignore);
		
	}
	
	function check()
	{
		
		$this->id = intval($this->id);
		$this->type = intval($this->type);
		
		if($this->name == "")	{
			$this->setError(JText::_('PLSENTFTIT'));
			return false;
		}
		
		return parent::check();
		
	}
	
	function store($updateNulls = false)
	{
		
		if(!parent::store($updateNulls))	{
			return false;
		}
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		$this->_field = new JoomdAppField();
		
		if(isset($_POST['types']))	{
			
			$types = JRequest::getVar('types', array(), 'post', 'array');
			$id = JRequest::getInt('id', 0);
			
			$query = 'delete from #__joomd_tnf where fieldid = '.$this->id;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			foreach($types as $type)	{
				
				$this->_field->setType($type);
				
				if($id)	{
					if(!$this->_field->updatefield($this->id, $this->type))	{
						$this->setError($this->_field->getError());
						return false;
					}
				}
				else	{
					if(!$this->_field->addfield($this->id, $this->type))	{
						$this->setError($this->_field->getError());
						return false;
					}				
				}
				
				$query = 'insert into #__joomd_tnf values('.$type.', '.$this->id.')';
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
			}
		
		}
		
		if(isset($_POST['catid']))	{
			
			$cats = JRequest::getVar('catid', array(), 'post', 'array');
		
			$query = 'delete from #__joomd_cnf where fieldid = '.$this->id;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			foreach($cats as $cat)	{
			
				$query = 'insert into #__joomd_cnf values('.$cat.', '.$this->id.')';
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
