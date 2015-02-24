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

//WYSIWIG Field type since JoomD 2.3

class JoomdAppFieldWysiwig	{
	
	protected $parent;
	
	function __construct($parent)
	{
		
		$this->parent = $parent;
		
	}
	
	//Creates a new field
	function addfield($id)
	{
		
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' add column `field_'.$id.'` text';
			
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
	
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' change `field_'.$id.'` `field_'.$id.'` text';
			
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
		
		$rows = isset($custom->rows)?$custom->rows:null;
		$cols = isset($custom->cols)?$custom->cols:null;
		
		$editor = Joomdui::getEditor();
		$editor->initialize( 'form[name=\''.$params['form'].'\'] #field_'.$field->id );
		$html = '<textarea name="field_'.$field->id.'" id="field_'.$field->id.'" class="form_'.$field->cssclass.'"';
		if($rows)
			$html .= ' rows="'.$rows.'"';
		if($cols)
			$html .= ' cols="'.$cols.'"';
		$html .= '>'.$value.'</textarea>';
		
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
			$js .= 'if($jd(\'form[name="'.$params['form'].'"] textarea[name="field_'.$field->id.'"]\').val() == "") { alert("'.JText::sprintf('SIAREQF', $field->name).'"); return false; }';
		
		
		return $js;
		
	}
	
	//display the field value in front end based on parameters
	function displayfieldvalue($itemid, $fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$custom = json_decode($field->custom);
		$char = isset($custom->char)?$custom->char:100;
		
		$params['short'] = isset($params['short'])?$params['short']:false;
		$params['char'] = isset($params['char'])?$params['char']:$char;
				
		$value = $this->getfieldvalue($itemid, $field->id);
		
		if($params['short'])
			return substr(strip_tags($value), 0, $params['char']).'...';
		else	{
			$content = isset($custom->content)?$custom->content:0;
			$layout = JRequest::getVar('layout', '');
			if($layout == 'detail' and $content)	{
				$dispatcher	=  JDispatcher::getInstance();
				$item = new stdClass();
				$item->id = $field->id;
				$item->title = $field->name;
				$item->text = $value;
				JPluginHelper::importPlugin('content');
				$registry = new JRegistry();
				$registry->loadArray($params);
				$results = $dispatcher->trigger('onContentPrepare', array ('com_joomd.item', & $item, & $registry, 0));
				$value = $item->text;
			}
			return $value;
		}
				
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
		
		$value = JRequest::getVar('field_'.$field->id, '', 'post', 'string', JREQUEST_ALLOWRAW);
			
		if($field->required && empty($value))	{
			$this->parent->setError(JText::sprintf('SIAREQF', $field->name));
			return false;
		}
		
		return true;
		
	}
	
	//saves the field value
	function saveField($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
				
		$value = JRequest::getVar('field_'.$field->id, null, 'post', 'string', JREQUEST_ALLOWRAW);
	
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
		
		$rows = isset($custom->rows)?$custom->rows:7;
		$cols = isset($custom->cols)?$custom->cols:50;
		$char = isset($custom->char)?$custom->char:100;
		$content = isset($custom->content)?$custom->content:0;
		
		$html = '<fieldset class="adminform">

		<legend>'.JText::_('FIELD_OPTIONS').'</legend>
		
		<table class="admintable">
			<tbody><tr>
			<td class="key">'.JText::_('ROW').':</td>
			<td colspan="2"><input type="text" name="custom[rows]" id="customrows" value="'.$rows.'" size="3" /></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('COLS').':</td>
			<td colspan="2"><input type="text" name="custom[cols]" id="customcols" value="'.$cols.'" size="3" /></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('INTRO_TEXT_CHARACTER').':</td>
			<td colspan="2"><input type="text" name="custom[char]" id="customchar" value="'.$char.'" size="3" /><span class="hasTip" title="'.JText::_('FIELD_INTROTEXT_CHARACTER_LIMIT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		   <tr>
			<td class="key">'.JText::_('ENABLECONPLG').':</td>
			<td colspan="2"><input type="radio" name="custom[content]" id="customcontent1" value="1"';
			if($content == 1)
				$html .= ' checked="checked"';
			$html .= '
			> '. JText::_('YS').' <input type="radio" name="custom[content]" id="customcontent0" value="0"';
			if($content == 0)
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