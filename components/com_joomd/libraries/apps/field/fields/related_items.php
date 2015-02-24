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

//Select List Field type since JoomD 2.3

class JoomdAppFieldRelated_items	{
	
	protected $parent;
	
	function __construct($parent)
	{
		
		$this->parent = $parent;
		
	}
	
	//Creates a new field
	function addfield($id)
	{
		
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' add column `field_'.$id.'` varchar (255) not null';
			
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
	
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' change `field_'.$id.'` `field_'.$id.'` varchar (255) not null';
			
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
		
		$multiple = isset($custom->multiple)?$custom->multiple:0;
		$options = isset($custom->options)?explode("\n", $custom->options):array();
		
		$suf = ($multiple==1)?'[]':'';
		
		$html = '<select name="field_'.$field->id.$suf.'" id="field_'.$field->id.'"';
		if($multiple)
			$html .= ' multiple="multiple"';
		$html .= '>';
		
		$values = explode('|', $value);
		
		foreach($options as $option)	{
			$option = trim($option);
			$html .= '<option value="'.$option.'"';
			if(in_array($option, $values))
				$html .= ' selected="selected"';
			$html .= '>'.$option.'</option>';
			
		}
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	//Loads the field in search form
	function loadsearchform($fieldid, $itemid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		$custom = json_decode($field->custom);
		
		$multiple = isset($custom->multiple)?$custom->multiple:0;
		$options = isset($custom->options)?explode("\n", $custom->options):array();
		
		$suf = ($multiple==1)?'[]':'';
		
		$html = '<select name="field_'.$field->id.$suf.'" id="field_'.$field->id.'"';
		if($multiple)
			$html .= ' multiple="multiple"';
		$html .= '>';
				
		foreach($options as $option)	{
			$option = trim($option);
			$html .= '<option value="'.$option.'">'.$option.'</option>';
			
		}
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	//Loads the Field validation
	function loadvalidation($fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$custom = json_decode($field->custom);
		
		$multiple = isset($custom->multiple)?$custom->multiple:null;
		
		$js = '';
		
		$suf = ($multiple==1)?'[]':'';
		
		if($field->required)
			$js .= 'if($jd(\'form[name="'.$params['form'].'"] select#field_'.$field->id.' :selected\').length == 0) { alert("'.JText::sprintf('SIAREQF', $field->name).'"); return false; }';
		
		return $js;
		
	}
	
	//display the field value in front end based on parameters
	function displayfieldvalue($itemid, $fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		return str_replace('|', ', ', $value);
				
	}
	
	//returns the field value
	function getfieldvalue($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$query = 'select field_'.$field->id.' from #__joomd_type'.$this->parent->_typeid.' where itemid = '.$itemid;
		
		$this->parent->_db->setQuery( $query );
		$value = $this->parent->_db->loadResult();
			
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
		
		$custom = json_decode($field->custom);
		
		$multiple = isset($custom->multiple)?$custom->multiple:null;
			
		if($multiple == 1)
			$value = implode('|', (array)JRequest::getVar('field_'.$field->id, array(), 'post', 'array'));
		else
			$value = JRequest::getVar('field_'.$field->id, '');
		
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
		
		$multiple = isset($custom->multiple)?$custom->multiple:null;
		$options = isset($custom->options)?$custom->options:null;
		
		$html = '<script type="text/javascript">
					
					function validateoptions() { if($jd("textarea#customoptions").val() == "") { alert("'.JText::_('PLSENTSOMVALOPT').'"); return false; } return true; }
					
				</script>';
		
		$html .= '<fieldset class="adminform">

		<legend>'.JText::_('FIELD_OPTIONS').'</legend>
		
		<table class="admintable">
			<tbody><tr>
			<td class="key">'.JText::_('OPTION').':</td>
			<td colspan="2"><textarea name="custom[options]" id="customoptions" rows="5" cols="40">'. $options.'</textarea> <em class="required">*</em> <span class="hasTip" title="'.JText::_('FIELDOPTIONTT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="'. JText::_('INFO').'" align="top" /></span>
			</td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('MULTIPLE').':</td>
			<td colspan="2"><input type="radio" name="custom[multiple]" id="custommultiple" value="1"';
			if($multiple == 1)
				$html .= ' checked="checked"';
			$html .= '
			> '. JText::_('YS').' <input type="radio" name="custom[multiple]" id="custommultiple" value="0"';
			if($multiple == 0)
				$html .= ' checked="checked"';
			$html .= '> '. JText::_('NS').'
			</td>
		  </tr>
		  </tbody>
			</table>
			
		</fieldset>';
		
		return $html;
		
	}
	
}