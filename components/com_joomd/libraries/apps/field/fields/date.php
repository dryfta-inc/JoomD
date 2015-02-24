<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD Field Application
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//Date Field type since JoomD 2.3

class JoomdAppFieldDate	{
	
	protected $parent;
	
	function __construct($parent)
	{
		
		$this->parent = $parent;
		
	}

	//Creates a new field
	function addfield($id)
	{
		
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' add column `field_'.$id.'` date not null';
			
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
		
	}
	
	//Updates the already existing field
	function updatefield($id)
	{
	
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' change `field_'.$id.'` `field_'.$id.'` date not null';
			
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
	
	}
	
	//Deletes the field
	function deletefield($id)
	{
	
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' drop `field_'.$id.'`';
			
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
	
	}
	
	//Loads the field in Item add/edit form
	function loadeditform($fieldid, $itemid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		$custom = json_decode($field->custom);
		
		$datepicker = Joomdui::getDatepicker();
				
		$year = isset($custom->year)?$custom->year:false;
		$month = isset($custom->month)?$custom->month:false;
		
		$datepicker->initialize('form[name=\''.$params['form'].'\'] #field_'.$field->id, array('changeMonth'=>$month, 'changeYear'=>$year));
		
		$value = ($value == '0000-00-00')?null:$value;
			
		
		$html = '<input type="text" name="field_'.$field->id.'" id="field_'.$field->id.'" class="form_'.$field->cssclass.'" value="'.$value.'" />';
		
		return $html;
		
	}
	
	//Loads the field in search form
	function loadsearchform($fieldid, $itemid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		$custom = json_decode($field->custom);
		
		$datepicker = Joomdui::getDatepicker();
				
		$year = isset($custom->year)?$custom->year:false;
		$month = isset($custom->month)?$custom->month:false;
		
		$datepicker->initialize('form[name=\''.$params['form'].'\'] #field_'.$field->id, array('changeMonth'=>$month, 'changeYear'=>$year));				
		
		$html = '<input type="text" name="field_'.$field->id.'" id="field_'.$field->id.'" class="search_'.$field->cssclass.'" value="" />';
		
		return $html;
		
	}
	
	//Loads the Field validation
	function loadvalidation($fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		$js = '';
		
		if($field->required)
			$js .= 'if($jd(\'form[name="'.$params['form'].'"] input[name="field_'.$field->id.'"]\').val() == "" || $jd(\'form[name="'.$params['form'].'"] input[name="field_'.$field->id.'"]\').val() == "0000-00-00") { alert("'.JText::sprintf('SIAREQF', $field->name).'"); return false; }';
		
		$js .= 'if($jd(\'form[name="'.$params['form'].'"] input[name="field_'.$field->id.'"]\').val() != "" && !isDate($jd(\'form[name="'.$params['form'].'"] input[name="field_'.$field->id.'"]\').val())) { alert("'.JText::sprintf('PLSENTVALID', $field->name).'"); return false; }';
		
		
		return $js;
		
	}
	
	//display the field value in front end based on parameters
	function displayfieldvalue($itemid, $fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		if($value == "0000-00-00")
			return null;
		else
			return $value;
				
	}
	
	//returns the field value
	function getfieldvalue($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$query = 'select field_'.$field->id.' from #__joomd_type'.$this->parent->_typeid.' where itemid = '.$itemid;
		
		$this->parent->_db->setQuery( $query );
		$value = $this->parent->_db->loadResult();
		
		if($value == '0000-00-00')
			$value = null;
			
		return $value;
		
	}

	
	//update the field value
	function updatefieldvalue($itemid, $fieldid, $value='')
	{
		
		$field = $this->parent->getField($fieldid);
		
		$query = 'update #__joomd_type'.$this->parent->_typeid.' set field_'.$field->id.' = '.$this->parent->_db->Quote($value).' where itemid = '.$itemid;
		$this->parent->_db->setQuery( $query );
			
		if(!$this->parent->_db->query())	{
			$this->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
		
	}
	
	//checks the field value before storing
	function checkField($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = JRequest::getVar('field_'.$field->id, '');
			
		$value = ($value=='0000-00-00')?null:$value;
		
		if($field->required && empty($value))	{
			$this->parent->setError(JText::sprintf('SIAREQF', $field->name));
			return false;
		}
		
		JRequest::setVar('field_'.$field->id, $value);
		
		return true;
		
	}
	
	//saves the field value
	function saveField($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
				
		$value = JRequest::getVar('field_'.$field->id, '', 'post');		
	
		$query = 'update #__joomd_type'.$this->parent->_typeid.' set field_'.$field->id.' = '.$this->parent->_db->Quote($value).' where itemid = '.$itemid;
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
				
		return true;
		
	}
	
	//loads field options for customizations
	function loadfieldoptions($id, $type)
	{
		
		$field = $this->parent->getField($id);
		
		$custom = json_decode($field->custom);
		
		$month = isset($custom->month)?$custom->month:null;
		$year = isset($custom->year)?$custom->year:null;
		
		$html = '<fieldset class="adminform">

		<legend>'.JText::_('FIELD_OPTIONS').'</legend>
		
		<table class="admintable">
			<tbody><tr>
			<td class="key">'.JText::_('SMONDROP').':</td>
			<td colspan="2"><input type="radio" name="custom[month]" id="custommonth1" value="1"';
			if($month == 1)
				$html .= ' checked="checked"';
			$html .= '/> '.JText::_('YS').' <input type="radio" name="custom[month]" id="custommonth0" value="0"';
			if($month == 0)
				$html .= ' checked="checked"';
			$html .= ' /> '.JText::_('NS').'</td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('SYDD').':</td>
			<td colspan="2"><input type="radio" name="custom[year]" id="customyear1" value="1"';
			if($year == 1)
				$html .= ' checked="checked"';
			$html .= ' /> '.JText::_('YS').' <input type="radio" name="custom[year]" id="customyear0" value="0"';
			if($year == 0)
				$html .= ' checked="checked"';
			$html .= '/> '.JText::_('NS').'</td>
		  </tr>
		  </tbody>
			</table>
			
		</fieldset>';
		
		return $html;
		
	}
	
}