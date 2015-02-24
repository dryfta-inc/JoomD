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

//youtube Field type since JoomD 2.3

class JoomdAppFieldYoutube	{
	
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
		
		$html = '<input type="text" name="field_'.$field->id.'" id="field_'.$field->id.'" class="form_'.$field->cssclass.'" value="'.$value.'" />';
		
		return $html;
		
	}
	
	//Loads the field in search form
	function loadsearchform($fieldid, $itemid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		$html = '<input type="text" name="field_'.$field->id.'" id="field_'.$field->id.'" class="search_'.$field->cssclass.'" value="" />';
		
		return $html;
		
	}
	
	//Loads the Field validation
	function loadvalidation($fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		$js = '';
		
		if($field->required)
			$js .= 'if($jd(\'form[name="'.$params['form'].'"] input[name="field_'.$field->id.'"]\').val() == "") { alert("'.JText::sprintf('SIAREQF', $field->name).'"); return false; }';
		
		
		return $js;
		
	}
	
	//display the field value in front end based on parameters
	function displayfieldvalue($itemid, $fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		$custom = json_decode($field->custom);		
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		$height		= isset($custom->height)?$custom->height:400;
		$width		= isset($custom->width)?$custom->width:600;
		$fullscreen = isset($custom->fullscreen)?$custom->fullscreen:0;
		
		$url = 'http://www.youtube.com/embed/'.$value.'?rel=0';
		
		$html = '<iframe width="'.$width.'" height="'.$height.'" src="'.$url.'" frameborder="0"';
		
		if($fullscreen)
			$html .= ' allowfullscreen';
		
		$html .= '></iframe>';
		
		return $html;
				
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
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
		
	}
	
	//checks the field value before storing
	function checkField($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = JRequest::getVar('field_'.$field->id, '');
			
		if($field->required && empty($value))	{
			$this->setError(JText::sprintf('SIAREQF', $field->name));
			return false;
		}
					
		if(strpos($value, 'v=') !== false)	{
			$pos1 = strpos($value, 'v=')+2;
			$pos2 = strpos($value, '&', $pos1);
			if($pos2 === false)
				$value = substr($value, $pos1);
			else
				$value = substr($value, $pos1, $pos2-$pos1);
			
		}
		elseif(strpos($value, 'youtu.be/') !== false)	{
			$pos1 = strpos($value, 'youtu.be/')+9;
			
			$value = substr($value, $pos1);
			
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
		
		$fullscreen = isset($custom->fullscreen)?$custom->fullscreen:null;
		$width = isset($custom->width)?$custom->width:null;
		$height = isset($custom->height)?$custom->height:null;
	
		$html = '<fieldset class="adminform">

		<legend>'.JText::_('FIELD_OPTIONS').'</legend>
		
		<table class="admintable">
			<tbody>
			<tr>
			<td class="key">'.JText::_('WIDTH').':</td>
			<td colspan="2"><input type="text" name="custom[width]" id="customwidth" value="'.$width.'" size="3" /> <span class="hasTip" title="'.JText::_('FIELDVIDEOWIDTH').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('HEIGHT').':</td>
			<td colspan="2"><input type="text" name="custom[height]" id="customheight" value="'.$height.'" size="3" /> <span class="hasTip" title="'.JText::_('FIELDVIDEOHEIGHT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>';/*
		  <tr>
			<td class="key">'.JText::_('ALLOWFULLSCREEN').':</td>
			<td colspan="2"><input type="radio" name="custom[fullscreen]" id="customfullscreene1" value="1"';
			if($fullscreen == 1)
				$html .= ' checked="checked"';
			$html .= '
			> '. JText::_('YS').' <input type="radio" name="custom[fullscreen]" id="customfullscreen0" value="0"';
			if($fullscreen == 0)
				$html .= ' checked="checked"';
			$html .= '> '. JText::_('NS').' <span class="hasTip" title="'.JText::_('FIELDVIDEOFULLSCREEN').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span>
			</td>
		  </tr>*/
		  $html .= '</tbody>
		</table>
			
		</fieldset>';
		
		return $html;
		
	}
	
}