<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD CSV Application
# ------------------------------------------------------------------------
# author    Noorullah Kalim - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

 // Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class JoomdModelCsv extends JModel
{

    var $_total = null;
	var $_pagination = null;
	function __construct()
	{
		parent::__construct();
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		$this->_field = new JoomdAppField();

	}

	function getExport_data()
	{
		
		$typeids = JRequest::getVar('types', array(), 'post', 'array');
		$catids = JRequest::getVar('cats', array(), 'post', 'array');
		$fieldids = JRequest::getVar('fields', array(), 'post', 'array');
		$result=array();	
		
		if(count($typeids) < 1)
			return JText::_('PLZ_SELECT_TYPE');
			
		if(count($catids) < 1)
			return JText::_('PLZ_SELECT_CAT');
			
		if(count($fieldids) < 1)
			return JText::_('PLZ_SELECT_FIELD');
		
		$query = 'select id, name from #__joomd_types where id in ('.implode(',', $typeids).')';
		$this->_db->setQuery( $query );
		$types = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		
		for($i=0;$i<count($types);$i++)	{
			
			$header = array('Category','Alias');
			
			$query = 'select catid from #__joomd_tnc where typeid = '.$types[$i]->id;
			$this->_db->setQuery( $query );
			$tcatids = (array)$this->_db->loadColumn();
			echo $this->_db->getErrorMsg();
			
			
			$cids = array_intersect($catids, $tcatids);
			$cids = array_values($cids);
			
			if(count($cids) < 1)
				return JText::_('CATS_NOT_FOUND');
			
			$query = 'select fieldid from #__joomd_tnf where typeid = '.$types[$i]->id;
			$this->_db->setQuery( $query );
			$tfieldids = (array)$this->_db->loadColumn();
			echo $this->_db->getErrorMsg();
			
			$fids = array_intersect($fieldids, $tfieldids);
			$fids = array_values($fids);
			
			if(count($fids) < 1)
				return JText::_('FIELDS_NOT_FOUND');
				
			$this->_field->setType($types[$i]->id);
			
			$query = 'select id, type, name from #__joomd_field where id in ('.implode(',', $fids).') order by ordering asc';
			$this->_db->setQuery($query);
			$fields = $this->_db->loadObjectList();
			echo $this->_db->getErrorMsg();
			
			foreach($fields as $field)
				array_push($header, $field->name);
				
			$resultlist=array();
				 
			$query = 'select item.id, c.name as catname, item.alias from #__joomd_types as t left join #__joomd_tnc as tnc on t.id=tnc.typeid left join #__joomd_category as c on tnc.catid=c.id left join #__joomd_item_cat as ic on c.id=ic.catid left join #__joomd_item as item on ic.itemid = item.id where t.id='.$types[$i]->id.' and c.id in ('.implode(',', $cids).') and item.alias <> "" group by item.id';
	
			$this->_db->setQuery($query);
			$resultlists=$this->_db->loadRowList();
			echo $this->_db->getErrorMsg();
			
			if(count($resultlists)){
				
				$list = array();
				
				for($j=0;$j<count($resultlists);$j++)	{
					
					$list[$j] = array($resultlists[$j][1], $resultlists[$j][2]);
					
					foreach($fields as $field)	{
						$list[$j][] = $this->_field->getfieldvalue($resultlists[$j][0], $field->id);
					}
					
				}
				
				array_unshift($list, $header);
				array_unshift($list, (array)$types[$i]->name);
				array_push($list, array());
				
				$result = array_merge($result, $list);
				
			}
							
		}
		
		return $result;		
	
	}
	
	function importEntrydata()
	{ 
			
		jimport('joomla.filesystem.file');
	
		$file = JRequest::getVar("file", null, 'FILES', 'array');
	
		$file_name    = str_replace(' ', '', JFile::makeSafe($file['name']));		
		$file_tmp     = $file["tmp_name"];
	
		$ext = strrchr($file_name, '.');
	
		if(filesize($file_tmp) == 0)
			return JText::_('PLZ_SELECT_FILE');
	
		if($ext <> '.csv')
			return JText::_('ONLY_CSV');
	
		$user = JFactory::getUser();
	
		$fp = fopen($file_tmp, "r");
	
		$invalid = ''; 
	
		while(($data = fgetcsv($fp, 100000, ",")) !== FALSE)	{
			
			if(empty($data[0]))
				continue;
			
			elseif(empty($data[1])){
			
				$fids=array();
				$type = $data[0];
				$query ='select id from #__joomd_types where lower(name) = '.$this->_db->Quote(strtolower($type));
				$this->_db->setQuery($query);
				$typeid = (int)$this->_db->loadResult();
	
				if(empty($typeid)){
					
					try{
						$typeid = $this->inserttype(array('name'=>$data[0]));
					}catch(Exception $e){
						return $e->getMessage();
					}
					
				}
				
			}
			elseif(strtolower($data[0])=="category" && strtolower($data[1])=='alias'){
				
				for($i=2;$i<count($data);$i++){
								
					if(!empty($data[$i])){
					
						$query = 'select id from #__joomd_field where lower(name)='.$this->_db->Quote(strtolower($data[$i]));
						$this->_db->setQuery($query);
						$fid = $this->_db->loadResult();
						
						if(empty($fid)){
					
							try{
								$fid = $this->insertfield(array('name'=>$data[$i], 'typeid'=>$typeid));
							}catch(Exception $e){
								return $e->getMessage();
							}
							
						}
						else	{
							try{
								$this->updatefield(array('id'=>$fid, 'name'=>$data[$i], 'typeid'=>$typeid));
							}catch(Exception $e){
								return $e->getMessage();
							}
						}
						
						array_push($fids, $fid);
	
					}
	
				}
	
			}
			elseif(strtolower($data[0])<>"category" && !empty($data[1])){
				
				$alias = $data[1];
				
				$alias = empty($data[1])?$data[2]:$data[1];
		
				$alias = JFilterOutput::stringURLSafe($alias);
				
				if(trim(str_replace('-','',$alias)) == '') {
					$datenow =  JFactory::getDate();
					$alias = $datenow->format("Y-m-d-H-i-s");
				}
				
				$query ='select id from #__joomd_category where lower(name)='.$this->_db->Quote(strtolower($data[0]));
				$this->_db->setQuery($query);
				$catid = $this->_db->loadResult();
	
				if(empty($catid)){
					
					try{
						$catid = $this->insertcat(array('name'=>$data[0], 'typeid'=>$typeid));
					}catch(Exception $e){
						return $e->getMessage();
					}
					
				}
				
				if(!empty($alias) and count($fids)){
	
					$query = 'select id from #__joomd_item where alias = '.$this->_db->Quote($alias);
					$this->_db->setQuery($query); 
					$itemid = $this->_db->loadResult();
					
					if(!empty($itemid)){
						
						$query = 'select count(*) from #__joomd_item_cat where catid = '.$catid.' and itemid = '.$itemid;
						$this->_db->setQuery( $query );
						$count = $this->_db->loadResult();
						
						if($count < 1)	{
							
							$query = 'insert into #__joomd_item_cat (catid,itemid) values('.$catid.','.$itemid.')';
							$this->_db->setQuery($query);
							if(!$this->_db->query()){
								return $this->_db->getErrorMsg();
							}
							
						}
						else	{
						
							if($invalid<>''){
								$invalid .= ', '.$data[1];
							}
							else	{
								$invalid .=$data[1];
							}
							
						}
					
					}
			
					else	{					
						
						$date=JFactory::getDate();
						$now = $date->toMySQL();
						
						$array['id'] = null;
						$array['alias'] = $alias;
						$array['typeid'] = $typeid;
						$array['published'] = 1;
						$array['created'] = $now;
						$array['publish_up'] = $now;
						$array['created_by'] = $user->id;
						
						$array['ordering'] = 1;
						$query = 'select ordering from #__joomd_item order by ordering desc limit 1';
						$this->_db->setQuery( $query );
						$array['ordering'] += $this->_db->loadResult();
						
						$itemtable = $this->getTable('item');
						
						if(!$itemtable->bind($array))	{
							return $itemtable->getError();
						}
						
						// If there was an error with registration, set the message and display form
						if ( !$itemtable->store() )
						{
							return $itemtable->getError();
						}
						
						$itemid = $itemtable->id;
						
						$insert = new stdClass();
						$insert->itemid = $itemid;
						
						for($i=0;$i<count($fids);$i++){
							$v = $data[$i+2];
							
							if(!empty($v))	{
							
								$k = 'field_'.$fids[$i];			  
								$insert->$k = $v;
							
							}
							
							if(!$this->insertcnf($catid, $fids[$i])){
								return $this->getError();
							}
							
						}
						
						if(!$this->_db->insertObject('#__joomd_type'.$typeid, $insert, 'itemid')){
							return $this->_db->stderr();
						}
	
						$query = 'insert into #__joomd_item_cat (catid,itemid) values('.$catid.','.$itemid.')';
						$this->_db->setQuery($query);
						if(!$this->_db->query()){
							return $this->_db->getErrorMsg();
						}
	
					} 
			
				}
	
			}
	
		}
	
		fclose($fp);
	
		if($invalid<>''){
			return $invalid.' '.JText::_('ALIAS_NOT_INSERTED_REMAINING_INSERTED');
		}
		else{		
			return JText::_('SUCCESS_INSERTED');
		}
		  
	}
	
	function inserttype($array)
	{
		
		$array['id'] = null;
		$array['published'] = 1;
		
		$query = 'select id from #__joomd_apps where type = 1 and item = 1 order by id asc limit 1';
		$this->_db->setQuery( $query );
		$array['appid'] = $this->_db->loadResult();
		
		$typetable = $this->getTable('type');
		
		if(!$typetable->bind($array))	{
			throw new Exception($typetable->getError());
		}
		
		if(!$typetable->check())	{
			throw new Exception($typetable->getError());
		}
		
		// If there was an error with registration, set the message and display form
		if ( !$typetable->store() )
		{
			throw new Exception($typetable->getError());
		}
		
		return $typetable->id;
		
	}
	
	function updatefield($array)
	{
		
		$this->_field->setType($array['typeid']);
				
		if(!$this->_field->updatefield($array['id'], 1))	{
			throw new Exception($this->_field->getError());
		}				
		
		$query = 'select count(*) from #__joomd_tnf where typeid = '.$array['typeid'].' and fieldid = '.$array['id'];
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count < 1)	{
		
			$query = 'insert into #__joomd_tnf (typeid, fieldid) values('.$array['typeid'].', '.$array['id'].')';
			$this->_db->setQuery($query);
			if(!$this->_db->query()){
				throw new Exception($this->_db->getErrorMsg());
			}
		
		}
		
		return true;
		
	}
	
	function insertfield($array)
	{
		
		$array['id'] = null;
		$array['type'] = 1;
		$array['detail'] = 1;
		$array['published'] = 1;
		
		$fieldtable = $this->getTable('field');
					
		if(!$fieldtable->bind($array))	{
			throw new Exception($fieldtable->getError());
		}
		
		if(!$fieldtable->check())	{
			throw new Exception($fieldtable->getError());
		}
		
		// If there was an error with registration, set the message and display form
		if ( !$fieldtable->store() )
		{
			throw new Exception($fieldtable->getError());
		}
		
		$this->_field->setType($array['typeid']);
				
		if(!$this->_field->addfield($fieldtable->id, $fieldtable->type))	{
			throw new Exception($this->_field->getError());
		}				
		
		$query = 'insert into #__joomd_tnf (typeid, fieldid) values('.$array['typeid'].', '.$fieldtable->id.')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()){
			throw new Exception($this->_db->getErrorMsg());
		}
		
		return $fieldtable->id;
		
	}
	
	function insertcat($array)
	{
		
		$array['id'] = null;
		$array['published'] = 1;
		
		$cattable = $this->getTable('category');
					
		if(!$cattable->bind($array))	{
			throw new Exception($cattable->getError());
		}
		
		if(!$cattable->check())	{
			throw new Exception($cattable->getError());
		}
		
		// If there was an error with registration, set the message and display form
		if ( !$cattable->store() )
		{
			throw new Exception($cattable->getError());
		}
		
		$query = 'insert into #__joomd_tnc (typeid, catid) values('.$array['typeid'].', '.$cattable->id.')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()){
			throw new Exception($this->_db->getErrorMsg());
		}
		
		return $cattable->id;
		
	}
	
	function insertcnf($catid, $fieldid)
	{
		
		$query = 'select count( * ) from #__joomd_cnf where catid = '.$catid.' and fieldid = '.$fieldid;
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count < 1)	{
			
			$query = 'insert into #__joomd_cnf (catid, fieldid) values ('.$catid.', '.$fieldid.')';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()){
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
		}
		
		return true;
		
	}
	
	
 function getType(){
	$query = "select * from #__joomd_types order by name";

	$this->_db->setQuery($query);

	$type = $this->_db->loadObjectList();

	return $type;
	}

 function list_category(){
   $post= JRequest::get('post');
   $ids = $post['ids'];
   $ids = implode(',',$ids);
   $query = "select * from #__joomd_category where id in (select catid from #__joomd_tnc where typeid in (".$ids."))";
   $this->_db->setQuery($query);
   $cats = $this->_db->loadObjectList();
   
   $html=" ";
   for($i=0; $i<count($cats); $i++){
   
   $html .="<option value=".$cats[$i]->id.">".$cats[$i]->name."</option>"; 
   }
   return $html;
  }
  
 function list_field(){
   $post= JRequest::get('post');
   $ids = $post['ids'];
   $ids = implode(',',$ids);
   $query = "select * from #__joomd_field where id in (select fieldid from #__joomd_cnf where catid in (".$ids."))";

   $this->_db->setQuery($query);
   $fields = $this->_db->loadObjectList();
   
   $html=" ";
   for($i=0; $i<count($fields); $i++){
   
   $html .="<option value=".$fields[$i]->id.">".$fields[$i]->name."</option>"; 
   }
   return $html;


  }  
  
  
}
