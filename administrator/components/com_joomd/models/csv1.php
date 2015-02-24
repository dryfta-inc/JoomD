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

	}

function getExport_data()	{
	
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
		
		$query = 'select id, type, name from #__joomd_field where id in ('.implode(',', $fids).') order by ordering asc';
		$this->_db->setQuery($query);
		$fields = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		
		foreach($fields as $field)
			array_push($header, $field->name);
				
		//making part of mysql query for field title 
		$subquery = 'ty.field_'.implode(', ty.field_', $fids);
			
		$resultlist=array();
			 
		$query = 'select c.name as catname, item.alias as alias,'.$subquery.' from #__joomd_types as t left join #__joomd_tnc as tnc on t.id=tnc.typeid left join #__joomd_category as c on tnc.catid=c.id left join #__joomd_item_cat as ic on c.id=ic.catid left join #__joomd_item as item on ic.itemid = item.id left join #__joomd_type'.$types[$i]->id.' as ty on item.id=ty.itemid where t.id='.$types[$i]->id.' and c.id in ('.implode(',', $cids).') and item.alias <> "" group by item.id';

		$this->_db->setQuery($query);
		$resultlists=$this->_db->loadRowList();
echo $this->_db->getErrorMsg();

		if(count($resultlists)){ 
			array_unshift($resultlists, $header);
			array_unshift($resultlists, (array)$types[$i]->name);
			array_push($resultlists, array());
		}

		$result = array_merge($result, $resultlists);
						
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

	$user =& JFactory::getUser();

	$fp = fopen($file_tmp, "r");

	$invalid = ''; 

	while(($data = fgetcsv($fp, 100000, ",")) !== FALSE)	{

		if($data[0]<>'' && $data[1]==''){
		
			$fid=array();
			$type = $data[0];
			$query ='select id from #__joomd_types where lower(name) = '.$this->_db->Quote(strtolower($type));
			$this->_db->setQuery($query);
			$typeid = $this->_db->loadResult();

			if($typeid == ''){
				return $type.' '.JText::_('TYPE_NOT_EXIST');
			}
			
		}
		elseif(strtolower($data[0])=="category" && strtolower($data[1])=='alias'){
			
			for($i=2;$i<count($data);$i++){
				
				$title[$i-2]=$data[$i];
				
				if($data[$i]<>''){
				
					$query = 'select id from #__joomd_field where lower(name)='.$this->_db->Quote(strtolower($data[$i]));
					$this->_db->setQuery($query);
					$fid[$i-2] = $this->_db->loadResult();

				}

			}

		}
		elseif(strtolower($data[0])<>"category" && $data[1]<>''){
			
			$alias = $data[1];
			
			$query ='select id from #__joomd_category where lower(name)='.$this->_db->Quote(strtolower($data[0]));
			$this->_db->setQuery($query);
			$catid = $this->_db->loadResult();
			//jexit($query);

			if($catid == ''){
				return $data[0].' '.JText::_('CATE_NOT_EXIST');
			}

			if($alias <> '' && $fid[0] <> '' ){

				$query = "select count(*) from #__joomd_item where alias ='$data[1]'";
				$this->_db->setQuery($query); 
				$count = $this->_db->loadResult();
				
				if($count>0){
		
					if($invalid<>''){
						$invalid .= ', '.$data[1];
					}
					else	{
						$invalid .=$data[1];
					}

				}
		
				else	{					
					
					$fields ='';
					for($i=0;$i<count($fid);$i++){
						$fields .= 'field_'.$fid[$i];
						if($i<>count($fid)-1){
							$fields .= ', ';
						}			  
					}

					$values = '';
		
					for($i=0;$i<count($fid);$i++){
						$values .= $this->_db->Quote($data[$i+2]);			  
						if($i<>count($fid)-1){
							$values .= ', ';
						}
					}
					
					$date=JFactory::getDate();
					$query = 'insert into #__joomd_item (alias,typeid,published,created,created_by) values ('.$this->_db->Quote($data[1]).','.$typeid.',1,'.$this->_db->Quote($date->toMySQL()).','.$user->id.')';
					$this->_db->setQuery($query);					 
					if(!$this->_db->query()){
						return $this->_db->_getErrorMsg();
					}
					
					$query = 'select id from #__joomd_item where alias='.$this->_db->Quote($data[1]).' order by id desc limit 1';
					$this->_db->setQuery($query);
					$itemid = $this->_db->loadResult();
					
					$query = 'insert into #__joomd_type'.$typeid.' (itemid, '.$fields.') values('.$itemid.','.$values.')';
					$this->_db->setQuery($query);

					if(!$this->_db->query()){
						return $this->_db->getErrorMsg();
					}

					$query = 'insert into #__joomd_item_cat (catid,itemid) values('.$catid.','.$itemid.')';
					$this->_db->setQuery($query);
					if(!$this->_db->query()){
						return $this->_db->_getErrorMsg();
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



?>