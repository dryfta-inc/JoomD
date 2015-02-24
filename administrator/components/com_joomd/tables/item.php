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
 

class TableItem extends JTable
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
    
	var $alias = null;
	var $typeid = 1;
	var $featured = 0;
	var $ordering = null;
	var $published = 1;
	var $created = null;
	var $created_by = null;
	var $modified = null;
	var $modified_by = null;
	var $publish_up = null;
	var $publish_down = null;
	var $hits = null;
	var $access = null;
	var $language = null;
	var $metadata = null;
    
    function TableItem( &$db ) {
        
		parent::__construct('#__joomd_item', 'id', $db);
		
		$this->_typeid = JRequest::getInt('typeid', 0);
		
		$this->_field = new JoomdAppField();
		
		
    }
	
	function loaditem($id=null)
	{
		
		$date =  JFactory::getDate();
		$user =  JFactory::getUser();
		
		$this->load($id);
		
		$registry = new JRegistry;
		
		$registry->loadString($this->metadata);
		$this->metadata = $registry;
		
		if(!$this->id)	{
			
			$this->id = 0;
			$this->published = 1;
			$this->created = $date->toMySQL();
			$this->created_by = $user->id;
			$this->publish_up = $date->toMySQL();
			
			$fields = (array)$this->_field->getFields();
			
			foreach($fields as $f)
			{
				
				$name = 'field_'.$f->id;
				
				$this->$name = null;
				
			}
			
		}
		
		return $this;
		
	}
	
	function checkitem($post)
	{
		
		$app = JFactory::getApplication();
		
		$type = Joomd::getType($this->_typeid);
		
		$date =  JFactory::getDate();
		$user =  JFactory::getUser();
		$nullDate = $this->_db->getNullDate();
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		$obj->file = array();
		
		settype($this->id, 'int');
		
		$arr = array(4);
			
		if($app->isAdmin())
			array_push($arr, 2);
		if($app->isSite())
			array_push($arr, 3);
			
		if($app->isAdmin())	{
			
			if(in_array($type->config->get('publishing'), $arr))	{
				
				if(empty($this->created))
					$this->created = $date->toMySQL();
				if(empty($this->created_by))
					$this->created_by = $user->id;
				if(empty($this->publish_up))
					$this->publish_up = $date->toMySQL();
					
				if(strtotime($this->created) > strtotime($this->publish_up))
					$this->created = $this->publish_up;
					
				if(empty($this->publish_down))
					$this->publish_down = '0000-00-00 00:00:00';
					
				if(strtotime($this->publish_up) > strtotime($this->publish_down) and $this->publish_down <> "0000-00-00 00:00:00")	{
					$obj->error = JText::_('PUBLISH_END_SHOULD_BE_GREATER');
					return $obj;
				}
				
			}
			elseif(!$this->id)	{
				$this->created = $date->toMySQL();
				$this->publish_up = $date->toMySQL();
				$this->created_by = $user->id;
			}
			
		}
		else	{
			
			if(isset($post['metadata']['author']))
				unset($post['metadata']['author']);
			if(isset($post['metadata']['robot']))
				unset($post['metadata']['robot']);
			
			if($post['id'])	{
				$this->created_by = null;
				$this->created = null;
			}
			else	{
				$this->created = $date->toMySQL();
				$this->created_by = $user->id;
			}
			
			if(in_array($type->config->get('publishing'), $arr))	{
				if(empty($this->publish_up))
					$this->publish_up = $date->toMySQL();
					
				if(empty($this->publish_down))
					$this->publish_down = '0000-00-00 00:00:00';
					
				if(strtotime($this->publish_up) > strtotime($this->publish_down) and $this->publish_down <> "0000-00-00 00:00:00")	{
					$obj->error = JText::_('PUBLISH_END_SHOULD_BE_GREATER');
					return $obj;
				}
				
			}
			else	{
				$this->publish_up = $date->toMySQL();
			}
			
		}
		
		if(isset($post['metadata']))
			$this->metadata = json_encode($post['metadata']);		
		
		if($this->id)	{
			$this->modified_by = $user->id;
			$this->modified = $date->toMySQL();
		}
		else	{
			
			$this->ordering = 1;
			$query = 'select ordering from #__joomd_item order by ordering desc limit 1';
			$this->_db->setQuery( $query );
			$this->ordering += $this->_db->loadResult();
						
		}
				
		$firstfield = $this->_field->get_firstfield(array('type'=>1, 'cats'=>$post['cats']));
		
		if($firstfield->id)
			$this->alias = empty($this->alias)?$post['field_'.$firstfield->id]:$post['alias'];
		
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		
		$query = 'select count(*) from #__joomd_item where alias = "'.$this->alias.'" and id <> '.$post['id'];
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			$obj->error = JText::sprintf('DUPLICATEALIAS', $this->alias);
			return $obj;
		}
		
		if(trim(str_replace('-','',$this->alias)) == '') {
			$datenow =  JFactory::getDate();
			$this->alias = $datenow->format("Y-m-d-H-i-s");
		}
		
		$fields = $this->_field->getFields(array('cats'=>$post['cats']));
		
		foreach($fields as $field)
		{
			switch($field->type)
			{
				
				case 10:
				case 11:
				case 12:
				
				if(!isset($_FILES['field_'.$field->id]) and !$field->required)
					continue;
				
				$file = $this->_field->checkField($post['id'], $field->id);
				
				foreach((array)$file as $f)	{
				
					array_push($obj->file, $f);
					
					if(isset($f->error))	{
						$obj->error = $f->error;
						return $obj;
					}
				
				}
								
				break;
				
				default:
				
				if(!isset($_POST['field_'.$field->id]) and !$field->required)
					continue;
				
				if(!$this->_field->checkField($post['id'], $field->id))	{
					$obj->error = $this->_field->getError();
					return $obj;
				}
				
				break;
				
			}
			
		}
		
		$obj->result = 'success';
		
		return $obj;
		
	}
	
	
	
	function storeitem($post)
	{
		
		if(!parent::store())	{
			$this->setError(parent::getError());
			return false;
		}
		
		if(!$post['id'])	{
			$query = 'insert into #__joomd_type'.$this->typeid.' (itemid) values ('.$this->id.')';
			$this->_db->setQuery( $query );
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		
		$fields = $this->_field->getFields(array('cats'=>$post['cats']));
		
		if(!$this->_field->saveFields($this->id, $fields))	{
			$this->setError($this->_field->getError());
			return false;
		}
		
		return true;
		
	}
	
	function delete($oid = null)
	{
		
		if(!parent::delete($oid))
			return false;
		else	{
			$query = 'delete t, c from #__joomd_type'.$this->_typeid.' as t inner join #__joomd_item_cat as c using(itemid) where t.itemid='.(int)$oid;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
		}
		
		return true;
		
	}
	
}
