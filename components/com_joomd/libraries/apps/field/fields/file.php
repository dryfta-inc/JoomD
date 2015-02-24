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

//File Field type since JoomD 2.3

class JoomdAppFieldFile	{
	
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
		
		$multiple = isset($custom->multiple)?$custom->multiple:null;
		$thumb = isset($custom->thumb)?$custom->thumb:null;
		
		$view = JRequest::getVar('view', '');
		
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
		
		$params['short'] = isset($params['short'])?$params['short']:false;
		$field = $this->parent->getField($fieldid);
		
		$custom = json_decode($field->custom);
		
		$value = $this->getfieldvalue($itemid, $field->id);
		
		$thumb = isset($custom->thumb)?$custom->thumb:null;
		$val = explode('|', $value);
		
		if($params['short'])	{
			
			if(!isset($val[0]))
				$val[0] = null;
			
			if(!empty($val[0]) and is_file(JPATH_SITE.'/images/joomd/'.$val[0]))	{
				if(empty($thumb))
					return '<a href="'.JURI::root().'images/joomd/'.$val[0].'" target="_blank">'.$val[0].'</a>';
				else
					return '<a href="'.JURI::root().'images/joomd/'.$val[0].'" target="_blank"><img src="'.$thumb.'" alt="'.$field->name.'" /></a>';
			}
			
		}
		
		else	{
		
			$display = array();
			
			foreach($val as $v)	{
				
				if(!empty($v) and is_file(JPATH_SITE.'/images/joomd/'.$v))	{
					if(empty($thumb))
						$display[] = '<a href="'.JURI::root().'images/joomd/'.$v.'" target="_blank">'.$v.'</a>';
					else
						$display[] = '<a href="'.JURI::root().'images/joomd/'.$v.'" target="_blank"><img src="'.$thumb.'" alt="'.$field->name.'" /></a>';
				}				
			
			}
			
			if(count($display))	{
			
				$html = '<ul>
						<li>'.implode("</li>\n<li>", $display).'</li>
					</ul>';
							
				return $html;
				
			}
		
		}
		
		return '';
				
	}
	
	//returns the field value
	function getfieldvalue($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$query = 'select field_'.$field->id.' from #__joomd_type'.$this->parent->_typeid.' where itemid = '.$itemid;
		
		$this->parent->_db->setQuery( $query );
		$value = $this->parent->_db->loadResult();
		
		$val = explode('|', $value);
				
		foreach($val as $v)	{
			if(!empty($v) and is_file(JPATH_SITE.'/images/joomd/'.$v))
				return $value;
		}
					
		return null;
		
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
		
		$pvalue = $this->getfieldvalue($itemid, $fieldid);
		
		$view = JRequest::getVar('view', '');
		
		$multiple = isset($custom->multiple)?$custom->multiple:null;
		$maxsize = isset($custom->maxsize)?$custom->maxsize*1000:500000;
		$filetypes = isset($custom->filetypes)?$custom->filetypes:'.doc,.docx,.txt,.pdf,.xls,.exl';
		$thumb = isset($custom->thumb)?$custom->thumb:null;
		
		$files = array();
		
		$allowed = explode(',', str_replace(' ', '', $filetypes));
			
		$time = time();
				
		jimport('joomla.filesystem.file');
		
		$image = JRequest::getVar('field_'.$field->id, null, 'FILES', 'array');
		
		if($multiple == 1)	{
			
			$values = empty($pvalue)?array():explode('|', $pvalue);
			
			if($itemid)
				$values = $this->check_imagefield($itemid, $field->id);
			
			if(count($image['name']))	{
				
				foreach($image['name'] as $i=>$v)	{
					$file=new stdClass();
					$file->error = 'empty file';
					
					$image_name    = str_replace(' ', '', JFile::makeSafe($image['name'][$i]));
					$image_tmp     = $image["tmp_name"][$i];
					
					$ext = strrchr($image_name, '.');
					
					if(!in_array($ext, $allowed))	{
						$file->error = JText::_('THISTYPNALL'.$ext);
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
					
					$file->name = $time.$image_name;
					
					array_push($values, $file->name);
					
					$file->delete_url = 'index.php?option=com_joomd&view='.$view.'&task=delete_custom&id='.$itemid.'&fieldid='.$field->id.'&filename='.$file->name.'&abase=1';
					$file->delete_type = 'POST';
					
					unset($file->error);
				
					array_push($files, $file);
				
				}
				
				$value = implode('|', (array)$values);
				
				$return = $files;
			
			}
			
			elseif($field->required && empty($pvalue) && ( count($values) < 1 || !is_file(JPATH_SITE.'/images/joomd/'.$values[0])))	{
				$file=new stdClass();
				$file->error = JText::sprintf('SREQ', $field->name);
					
				array_push($files, $file);
				
				$value = null;
			
				$return = $files;
			}
			
			else	{
				$return = array();
				$value = null;
			}
		
		}
		
		else	{
			
			$file=new stdClass();
			
			$image_name    = str_replace(' ', '', JFile::makeSafe($image['name']));
			
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
		
		$multiple = isset($custom->multiple)?$custom->multiple:null;
		$maxsize = isset($custom->maxsize)?$custom->maxsize:500000;
		$filetypes = isset($custom->filetypes)?$custom->filetypes:'.doc,.docx,.txt,.pdf,.xls,.exl';
		$thumb = isset($custom->thumb)?$custom->thumb:null;
	
		$html = '<fieldset class="adminform">

		<legend>'.JText::_('FIELD_OPTIONS').'</legend>
		
		<table class="admintable">
			<tbody>
		  <tr>
			<td class="key">'.JText::_('MULTIPLE').':</td>
			<td colspan="2"><input type="radio" name="custom[multiple]" id="custommultiple1" value="1"';
			if($multiple == 1)
				$html .= ' checked="checked"';
			$html .= '
			> '. JText::_('YS').' <input type="radio" name="custom[multiple]" id="custommultiple0" value="0"';
			if($multiple == 0)
				$html .= ' checked="checked"';
			$html .= '> '. JText::_('NS').'
			</td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('ALLOWEDTYPES').':</td>
			<td colspan="2"><input type="text" name="custom[filetypes]" id="customfiletypes" value="'.$filetypes.'" size="40" /> <span class="hasTip" title="'.JText::_('FIELDFILEALLOWEDTT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="'. JText::_('INFO').'" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('MAXALLOWEDSIZE').':</td>
			<td colspan="2"><input type="text" name="custom[maxsize]" id="custommaxsize" value="'.$maxsize.'" size="10" /> <span class="hasTip" title="'.JText::_('FIELDFILESIZETT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="'. JText::_('INFO').'" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('THUMNAIL').':</td>
			<td colspan="2"><input type="text" name="custom[thumb]" id="customthumb" value="'.$thumb.'" size="40" /> <span class="hasTip" title="'.JText::_('FIELDFILETHUMB').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="'. JText::_('INFO').'" align="top" /></span></td>
		  </tr>
		  </tbody>
			</table>
			
		</fieldset>';
		
		return $html;
		
	}	
	
}