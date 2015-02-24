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

//Video Field type since JoomD 2.3

class JoomdAppFieldVideo	{
	
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
		
		$view = JRequest::getVar('view', '');
		
		$custom = json_decode($field->custom);
		
		$multiple = isset($custom->multiple)?$custom->multiple:null;
		$thumb = isset($custom->thumb)?$custom->thumb:null;
		
		$suf = ($multiple==1)?'[]':'';
		
		$array = array('fieldname'=>'field_'.$field->id.$suf, 'buttontext'=>JText::_('ADD').' '.$field->name);
		
		if($multiple == 1)
			$array['maxNumberOfFiles'] = 'undefined';
			
		if(!empty($value))	{
			
			$files = array();
			
			$vals = explode('|', $value);
			
			foreach($vals as $val)	{
				$file=null;
				if(!empty($val) and is_file(JPATH_SITE.'/images/joomd/'.$val))	{
					$file->name = $val;
					
					if(!empty($thumb))
						$file->thumbnail_url = $thumb;
													
					$file->delete_url = 'index.php?option=com_joomd&view='.$view.'&task=delete_custom&id='.$itemid.'&fieldid='.$field->id.'&filename='.$file->name.'&abase=1';
					$files[] = $file;
				}
				
			}
			
			$array['files'] = $files;
			
		}
		
		Joomdui::uploadfile('form[name=\''.$params['form'].'\'] #field_'.$field->id, $array);
		
		$html = '<div id="field_'.$field->id.'"></div>';
		
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
		
		
		return $js;
		
	}
	
	//display the field value in front end based on parameters
	function displayfieldvalue($itemid, $fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$custom = json_decode($field->custom);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		if(empty($value) or !is_file(JPATH_SITE.'/images/joomd/'.$value))
			return '';
		
		$height = isset($custom->height)?$custom->height:400;
		$width = isset($custom->width)?$custom->width:600;
		$thumb = isset($custom->thumb)?$custom->thumb:null;
		
		$url = JURI::root().'images/joomd/'.$value;
		
		$ext = strrchr($value, '.');
		
		if($ext == '.mp4' or $ext == '.flv')	{
			
			$doc = JFactory::getDocument();
			$doc->addScript('components/com_joomd/assets/js/jwplayer.js');
			
			$js = '<script>jwplayer("field_'.$field->id.'").setup({
			  flashplayer: "'.JURI::root().'components/com_joomd/assets/player.swf",
			  file: "'.$url.'",
			  height: 400,
			  width: 550,
			  controlbar: "bottom"';
			  
			 if(!empty($thumb) and is_file($thumb))
				$js .= ', image='.$thumb;
					
			 $js .= '});</script>';
								
			$html = '<div id="field_'.$field->id.'"></div>';
			
			return $html.$js;

		}
		else	{
			
			$html = '<object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" type="application/x-oleobject" style="width: '.$width.'px; height: '.$height.'px;">
						<param name="URL" value="'.$url.'">
						<param name="stretchToFit" value="1">
						<param name="wmode" value="transparent">
						<param name="showControls" value="1">
						<param name="scale" value="exactfit">
						<param name="showStatusBar" value="0">
						<param name="animationAtStart" value="1">
						<param name="autoStart" value="0">
						<param name="enableFullScreenControls" value="0">
						<embed src="'.$url.'" style="width: '.$width.'px; height: '.$height.'px;" scale="exactfit" autostart="0" animationatstart="1" enablefullscreencontrols="0" type="application/x-mplayer2" wmode="transparent">
					</object>';
					
			return $html;
			
		}
				
	}
	
	//returns the field value
	function getfieldvalue($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$query = 'select field_'.$field->id.' from #__joomd_type'.$this->parent->_typeid.' where itemid = '.$itemid;
		
		$this->parent->_db->setQuery( $query );
		$value = $this->parent->_db->loadResult();
		
		if(empty($value) or !is_file(JPATH_SITE.'/images/joomd/'.$value))
			return null;
			
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
		
		$view = JRequest::getVar('view', '');
		
		$pvalue = $this->getfieldvalue($itemid, $fieldid);
		
		$maxsize = isset($custom->maxsize)?$custom->maxsize*1000:5000000;
		$filetypes = isset($custom->filetypes)?$custom->filetypes:'.mp4,.mpeg,.flv,.avi';
		$thumb = isset($custom->thumb)?$custom->thumb:null;
		
		$files = array();
		
		$allowed = explode(',', str_replace(' ', '', $filetypes));
			
		$time = time();
				
		jimport('joomla.filesystem.file');
		
		$image = JRequest::getVar('field_'.$field->id, null, 'FILES', 'array');
			
		$file=new stdClass();
		
		$image_name = str_replace(' ', '', JFile::makeSafe($image['name']));
		
		if(!empty($image_name))	{
		
			$image_tmp     = $image["tmp_name"];
			
			$ext = strrchr($image_name, '.');
			
			if(!in_array($ext, $allowed))	{
				$file->error = JText::_('THISTYPNALL');
				array_push($files, $file);
				return $files;
			}
			
			if(!empty($maxsize) && filesize($image_tmp) > $maxsize)	{
				$file->error = JText::_('FSEXMAXFS');
				array_push($files, $file);
				return $files;
			}
			
			if(!move_uploaded_file($image_tmp, JPATH_SITE.'/images/joomd/'.$time.$image_name))	{
				$file->error = JText::_('SIMDCNUP');
				array_push($files, $file);
				return $files;				
			}
			
			if(!empty($thumb))
				$file->thumbnail_url = $thumb;
			
			$post['field_'.$field->id] = $file->name = $time.$image_name;
							
			$file->delete_url = 'index.php?option=com_joomd&view='.$view.'&task=delete_custom&id='.$itemid.'&fieldid='.$field->id.'&filename'.$file->name.'&abase=1';
			$file->delete_type = 'POST';
			
			array_push($files, $file);
			
			$value = implode('|', (array)$post['field_'.$field->id]);
		
			$return = $files;
		
		}
		elseif($field->required && empty($pvalue) && !is_file(JPATH_SITE.'/images/joomd/'.$pvalue))	{
			$file->error = JText::sprintf('SREQ', $field->name);
			
			array_push($files, $file);
		
			$value = null;
		
			$return = $files;
			
		}
		else	{
			$return = array();
			$value = null;

		}
		
		JRequest::setVar('field_'.$field->id, $value);
				
		return $return;
		
	}
	
	//saves the field value
	function saveField($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		if(isset($_POST['field_'.$field->id]))	{
				
			$value = JRequest::getVar('field_'.$field->id, '', 'post');		
		
			$query = 'update #__joomd_type'.$this->parent->_typeid.' set field_'.$field->id.' = '.$this->parent->_db->Quote($value).' where itemid = '.$itemid;
			$this->parent->_db->setQuery( $query );
			
			if(!$this->parent->_db->query())	{
				$this->parent->setError($this->parent->_db->getErrorMsg());
				return false;
			}
			
		}
				
		return true;
		
	}
	
	//loads field options for customizations
	function loadfieldoptions($id, $type)
	{
		
		$field = $this->parent->getField($id);
		
		$custom = json_decode($field->custom);
		
		$maxsize = isset($custom->maxsize)?$custom->maxsize:5000000;
		$filetypes = isset($custom->filetypes)?$custom->filetypes:'.mp4,.mpeg,.flv,.avi';
		$width = isset($custom->width)?$custom->width:600;
		$height = isset($custom->height)?$custom->height:400;
		$thumb = isset($custom->thumb)?$custom->thumb:null;
	
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
		  </tr>
		  <tr>
			<td class="key">'.JText::_('ALLOWEDTYPES').':</td>
			<td colspan="2"><input type="text" name="custom[filetypes]" id="customfiletypes" value="'.$filetypes.'" size="40" /> <span class="hasTip" title="'.JText::_('FIELDVIDEOALLOWEDTT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('MAXALLOWEDSIZE').':</td>
			<td colspan="2"><input type="text" name="custom[maxsize]" id="custommaxsize" value="'.$maxsize.'" size="10" /> <span class="hasTip" title="'.JText::_('FIELDFILESIZETT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('THUMNAIL').':</td>
			<td colspan="2"><input type="text" name="custom[thumb]" id="customthumb" value="'.$thumb.'" size="40" /> <span class="hasTip" title="'.JText::_('FIELDFILETHUMB').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  </tbody>
			</table>
			
		</fieldset>';
				
		return $html;
		
	}
	
}