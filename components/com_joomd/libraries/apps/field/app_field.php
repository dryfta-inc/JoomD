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


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

require_once(JPATH_SITE.'/components/com_joomd/libraries/app.php');

class JoomdAppField extends JoomdApp	{
	
	public $_app = null;
	public $_typeid = 0;
	protected $fieldtypes = array();
	public $field=null;
	public $config=null;
	
	function __construct()
	{
		
		parent::__construct();
				
		//set the type if exists
		$this->setType();
		
		//set the app
		$this->getApp();
		
		$this->config = Joomd::getConfig();
		
	}
	
	function initialize()
	{
		
		static $init = false;
		
		if($init)
			return;
				
		$this->loadLanguage();
				
		$init = true;
		
	}
	
	function loadLanguage()
	{
		
		static $loaded = false;
		
		if($loaded)
			return true;
		
		$lang = JFactory::getLanguage();
		
		$lang->load('app_field', JDPATH_BASE);
		
		$query = 'select * from #__joomd_apps where published = 1 and type = 3 order by ordering asc';
		$this->_db->setQuery( $query );
		$types = $this->_db->loadObjectList();
		
		foreach($types as $type)
			$lang->load('field_'.$type->name, JDPATH_BASE);
		
		$loaded = true;
		
		return true;
		
	}
	
	function add_submenu()
	{
		$view = JRequest::getCmd('view', '');
		
		$active = $view == 'field';
	
		JSubMenuHelper::addEntry( '<span class="hasTip" title="'.JText::_('SUBMENU_FIELD_DESCR').'">'.JText::_('FIELDS').'</span>' , 'index.php?option=com_joomd&view=field' , $active );
		
	}
	
	function icon_display()
	{
		
		$html = '<div class="icon" title="'.JText::_('SUBMENU_FIELD_DESCR').'"><a href="index.php?option=com_joomd&view=field&task=add&cid[]=0"><img src="components/com_joomd/assets/images/icon-48-field-add.png" alt="Field" /><span>'. JText::_('ADDAF').'</span></a></div>';
		
		return $html;
		
	}
	/*
	function panel_display()
	{
				
		$query = 'select f.id, f.name as name, t.label as type from #__joomd_field as f join #__joomd_fieldtypes as t on f.type=t.id order by f.id desc limit 5';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		$html = '<table class="adminlist">';
		
		$html .= '<tr><th>'.JText::_('TITLE').'</th><th>'.JText::_('TYPE').'</th></tr>';
		
		for($i=0;$i<count($items);$i++)	{
			
			$html .= '<tr><td><a href="index.php?option=com_joomd&view=field&layout=form&cid[]='.$items[$i]->id.'">'.$items[$i]->name.'</a></td><td>'.$items[$i]->type.'</td></tr>';
			
		}
		
		$html .= '</table>';
		
		return $html;
		
	}
	*/
	
	//returns the typeid of the app of current object if not then default
	public function setType($typeid=null)
	{
		settype($typeid, 'int');
		//changed for multiple instances of same app
		$this->_typeid = $typeid?$typeid:JRequest::getInt('typeid', 0);
		
		//to make it consistent in every instance created
		JRequest::setVar('typeid', $this->_typeid);
		
	}
	
	//returns the view of the app of current object if not then item is selected
	protected function getApp()
	{
		
		if($this->_typeid and !$this->_app)	{
			$query = 'select name from #__joomd_apps where id = ( select appid from #__joomd_types where id = '.$this->_typeid.' limit 1 )';
			$this->_db->setQuery( $query );
			$this->_app = $this->_db->loadResult();
		}
		if(!$this->_app)
			$this->_app = 'item';
		
	}
	
	//check type whether the currenty type is actually exists
	protected function is_type()
	{
		
		$mainframe =  JFactory::getApplication();
		
		$query = 'select count(*) from #__joomd_types as t join #__joomd_apps as a on (t.appid=a.id) where t.id = '.$this->_typeid;
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if(!$count)
			$this->redirect(JRoute::_('index.php?option=com_joomd'), JText::_('TYPENOTRECOGNIZED'));
		
	}
		
	protected function is_table()
	{
		
		$query = 'show tables like '.$this->_db->Quote($this->_db->getPrefix().'joomd_type'.$this->_typeid);
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObject();
		
		if(empty($result))	{
			
			$this->is_type();
			
			if(!$this->createtable())
				return false;
		}
		
		return true;
		
	}
	
	private function createtable()
	{
		
		$query = 'CREATE TABLE IF NOT EXISTS `#__joomd_type'.$this->_typeid.'` (
				  `itemid` int(11) NOT NULL,
				  PRIMARY KEY (`itemid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;
		
	}
	
	//returns the field type
	function getFieldType($id)
	{
		
		$query = 'select lower(type) from #__joomd_fieldtypes where id = '.$id;
		$this->_db->setQuery( $query );
		$type = $this->_db->loadResult();
		
		$class = 'JoomdAppField'.ucfirst($type);
		
		if(class_exists($class))	{
			$class = new $class($this);
			return $class;
		}
		elseif(!class_exists($class) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/field/fields/'.$type.'.php'))	{
			require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/fields/'.$type.'.php');
			$class = new $class($this);
			return $class;
		}
		
		return false;
		
	}
	
	//check whether the field does exists in the table
	protected function is_field($id)
	{
		
		$name = 'field_'.$id;
		
		$query = 'show fields from #__joomd_type'.$this->_typeid.' LIKE '.$this->_db->Quote($name);
		$this->_db->setQuery( $query );
		$exists = $this->_db->loadObject();
		
		return empty($exists)?false:true;
		
	}
	
	function addfield($id, $type)
	{
		
		settype($id, 'int');
		settype($type, 'int');
		
		//check whether it already exists, if does not exist then create
		if(!$this->is_table())
			return false;
				
		//check whether it already exists, if does not exist then create
		if(!$this->is_field($id))	{
			
			if(!array_key_exists($type, $this->fieldtypes))
				$this->fieldtypes[$type] = $this->getFieldtype($type);
				
			if(!is_object($this->fieldtypes[$type]))	{
				$this->setError(JText::_('FIELDTYPE_NOT_EXISTS'));
				return false;
			}
			
			return $this->fieldtypes[$type]->addfield($id);
			
		}
		
		return true;
		
	}
	
	function updatefield($id, $type)
	{
		
		$field = $this->getField($id);
		
		if(!$field->id)	{
			$this->setError(JText::_('FIELDNOTEXISTS'));
			return false;
		}
		
		if(!$this->is_field($field->id))	{
			return $this->addfield($id, $type);
		}
		
		if(!array_key_exists($type, $this->fieldtypes))
			$this->fieldtypes[$type] = $this->getFieldtype($type);
			
		if(!is_object($this->fieldtypes[$type]))	{
			$this->setError(JText::_('FIELDTYPE_NOT_EXISTS'));
			return false;
		}
		
		return $this->fieldtypes[$type]->updatefield($id);
		
	}
	
	function delete($cid)
	{	

		if (count( $cid ) < 1) {
			$this->setError( JText::_( 'SAFTDEL', true ) );
			return false;
		}
		
		JArrayHelper::toInteger( $cid );
		
		foreach ($cid as $id)
		{
			
			$field = $this->getField($id);
			
			if(!array_key_exists($field->type, $this->fieldtypes))
				$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
				
			if(!is_object($this->fieldtypes[$field->type]))	{
				$this->setError(JText::_('FIELDTYPE_NOT_EXISTS'));
				return false;
			}
			
			$query = 'select typeid from #__joomd_tnf where fieldid = '.$id;
			$this->_db->setQuery( $query );
			$types = $this->_db->loadResultArray();
			
			foreach($types as $this->_typeid)	{
			
				if($this->is_field($id))	{
					
					$field = $this->getField($id);
					
					$query = 'select count( * ) from #__joomd_field';
					$this->_db->setQuery( $query );
					$count = $this->_db->loadResult();
					
					if($count < 2)	{
						$this->setError( JText::_( 'TITCFDEL', true ) );
						return false;
					}
					else	{
						
						if(!$this->fieldtypes[$field->type]->deletefield($id))	{
							$this->setError($this->_db->getErrorMsg());
							return false;
						}
					}
				
				}
			
			}
							
			$query = 'delete from #__joomd_field where id = '.$id;
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				
				$this->setError($this->_db->getErrorMsg());
				return false;
				
			}

		}

		return true;
	}
		
	function getField($id, $params = array())
	{
		
		settype($id, 'int');
		
		if(isset($this->field->id) and $this->field->id == $id)
			return $this->field;
		
		$mainframe =  JFactory::getApplication();
		$user =  JFactory::getUser();
		
		$params['published'] = isset($params['published'])?$params['published']:false;
		$params['access'] = isset($params['access'])?$params['access']:true;
		
		$query = 'select * from #__joomd_field where id = '.$id;
		
		if($params['published'])
			$query .= ' and published = 1';
		
		if($params['access'])	{
			
			$query .= ' and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		}
		
		$this->_db->setQuery( $query );
		$this->field = $this->_db->loadObject();
		
		if(empty($this->field))	{
			
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomd'.DS.'tables');
			$this->field =  JTable::getInstance('field', 'Table');
			
			$this->field->load(0);
			
		}
		
		return $this->field;
		
	}
	
	function getFields($params = array())
	{
		
		$mainframe =  JFactory::getApplication();
		$lang = JFactory::getLanguage();
		
		$user =  JFactory::getUser();
		
		$params['itemid']	= isset($params['itemid'])?(int)$params['itemid']:0;
		$params['cats']		= isset($params['cats'])?$params['cats']:null;
		$params['category'] = isset($params['category'])?$params['category']:false;
		$params['list']		= isset($params['list'])?$params['list']:false;
		$params['detail']	= isset($params['detail'])?$params['detail']:false;
		$params['search']	= isset($params['search'])?$params['search']:false;
		$params['type']		= isset($params['type'])?$params['type']:false;
		$params['published']= isset($params['published'])?$params['published']:false;
		$params['orderby']	= isset($params['orderby'])?$params['orderby']:'ordering';
		$params['orderdir'] = isset($params['orderdir'])?$params['orderdir']:'asc';
		$params['access']	= isset($params['access'])?$params['access']:true;
		$params['ids']		= isset($params['ids'])?$params['ids']:false;
		
		settype($params['cats'], 'array');
		
		$key = array_search(0, $params['cats']);
		
		if($key !== false)
			unset($params['cats'][$key]);
			
		JArrayHelper::toInteger( $params['cats'] );
		
		$where = array();
		
		if($mainframe->isSite() and $mainframe->getLanguageFilter())	{
			$where[] = 'language in ('.$this->_db->quote($lang->getTag()).', '.$this->_db->Quote('*').')';
		}
		
		$query = 'select fieldid from #__joomd_tnf where typeid = '.$this->_typeid;
		$this->_db->setQuery( $query );
		$ids = (array)$this->_db->loadResultArray();
		
		$where[] = count($ids)?('id in ('.implode(', ', $ids).')'):'id=0';
		
		if($params['access'])	{
			
			$where[] = 'access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		}
		
		if($params['itemid'])	{
			
			$query = 'select catid from #__joomd_'.$this->_app.'_cat where itemid ='.$params['itemid'];
			$this->_db->setQuery( $query );
			$cats = (array)$this->_db->loadResultArray();
			
			if(!count($cats))
				$cats = array(0);
			
			$where[] = 'id in ( select fieldid from #__joomd_cnf where catid in ( '.implode(', ', $cats).' ) )';
		}
		
		if(count($params['cats']))	{
					
			$query = 'select fieldid from #__joomd_cnf where catid in ('.implode(', ', $params['cats']).')';
			$this->_db->setQuery( $query );
			$ids = (array)$this->_db->loadResultArray();
			
			$where[] = count($ids)?'id in ('.implode(', ', $ids).')':'id = 0';
			
		}
		
		if($params['published'])	{
			$where[] = 'published = 1';
		}
		
		if($params['ids'] !== false)	{
			
			if(is_array($params['ids']))	{
				JArrayHelper::toInteger( $params['ids'] );
				
				$where[] = count($params['ids'])?'id in ('.implode(',', $params['ids']).')':'id=0';
				
			}
			else
				$where[] = 'id = '.(int)$params['ids'];
		}
		
		if($params['type'])	{
			
			if(is_array($params['type']))	{
				JArrayHelper::toInteger( $params['cats'] );
				
				if(count($params['type']))
					$where[] = 'type in ('.implode(',', $params['type']).')';
				
			}
			else
				$where[] = 'type = '.(int)$params['type'];
		}
		
		if($params['category'])	{
			
			$where[] = 'category = 1';
			
		}
		
		if($params['list'])	{
			
			$where[] = 'list = 1';
			
		}
		
		if($params['detail'])	{
			
			$where[] = 'detail = 1';
			
		}
		
		if($params['search'])	{
			
			$where[] = 'search = 1';
						
		}
		
		$filter = count($where)?' where '.implode(' and ', $where):'';
		
		$query = 'select * from #__joomd_field '.$filter.' order by '.$params['orderby'].' '.$params['orderdir'];
		$this->_db->setQuery( $query );
		$fields = (array)$this->_db->loadObjectList();
		
		return $fields;
		
	}
	
	function get_firstfield($params = array())
	{
			
		$mainframe =  JFactory::getApplication();
		$lang = JFactory::getLanguage();
		
		$params['itemid'] = isset($params['itemid'])?(int)$params['itemid']:0;
		$params['published'] = isset($params['published'])?$params['published']:null;
		$params['type'] = isset($params['type'])?$params['type']:array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
		$params['cats'] = isset($params['cats'])?$params['cats']:null;
		
		settype($params['cats'], 'array');
		
		$key = array_search(0, $params['cats']);
		
		if($key !== false)
			unset($params['cats'][$key]);
			
		JArrayHelper::toInteger( $params['cats'] );
		
		$query = 'select fieldid from #__joomd_tnf where typeid = '.$this->_typeid;
		$this->_db->setQuery( $query );
		$ids = (array)$this->_db->loadResultArray();
		
		$where = array();
		
		if($mainframe->isSite() and $mainframe->getLanguageFilter())	{
			$where[] = 'language in ('.$this->_db->quote($lang->getTag()).', '.$this->_db->Quote('*').')';
		}
		
		$where[] = count($ids)?('id in ('.implode(', ', $ids).')'):'id=0';
		
		if($params['itemid'])	{
			
			$query = 'select catid from #__joomd_'.$this->_app.'_cat where itemid ='.$params['itemid'];
			$this->_db->setQuery( $query );
			$params['cats'] = (array)$this->_db->loadResultArray();
			
			if(!count($params['cats']))
				$params['cats'] = array(0);

		}
		
		if(count($params['cats']))	{
					
			$query = 'select fieldid from #__joomd_cnf where catid in ('.implode(', ', $params['cats']).')';
			$this->_db->setQuery( $query );
			$ids = (array)$this->_db->loadResultArray();
			
			$where[] = count($ids)?'id in ('.implode(', ', $ids).')':'id = 0';
			
		}
			
		if($params['published'])
			$where[] = 'published = 1';
			
		if($params['type'])	{
			
			if(is_array($params['type']))	{
				JArrayHelper::toInteger( $params['cats'] );
				
				if(count($params['type']))
					$where[] = 'type in ('.implode(',', $params['type']).')';
				
			}
			else
				$where[] = 'type = '.(int)$params['type'];
		}
		
		$filter = count($where)?' where '.implode(' and ', $where):'';
		
		$query = 'select id from #__joomd_field '.$filter.' order by ordering asc limit 1';
		$this->_db->setQuery( $query );
		$fieldid = $this->_db->loadResult();
		
		$field = $this->getField($fieldid);
		
		return $field;
		
	}
	
	function loadformfields($itemid, $params=array())
	{
		
		$params['validation'] = isset($params['validation'])?$params['validation']:true;
		$params['access'] = isset($params['access'])?$params['access']:true;
		
		settype($itemid, 'int');
		
		$fields = $this->getFields($params);
		
		if(count($fields))	{
			
		$mainframe =  JFactory::getApplication();
		
		if($mainframe->isSite())
			$listtable = 'edittable';
		else
			$listtable = 'admintable';
			
		$document =  JFactory::getDocument();
		
		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();
		
		$body = '<table class="'.$listtable.'">
					<tbody>'.$lnEnd;
				
		for($i=0;$i<count($fields);$i++)	{
			
			$body .= '<tr><td class="key">'.$fields[$i]->name.'</td>'.$lnEnd;
			$body .= '<td colspan="2">'.$this->loadformfield($fields[$i]->id, $itemid, $params).'</td></tr>'.$lnEnd;
			
		}
		
		$body .= '</tbody>
				</table>';
		
		$head='';
		
		if($params['validation'])	{
			
			$head = '<script type="text/javascript">'.$lnEnd.'
			
				'.$tab.'function validatefields()	{'.$lnEnd;
			
			for($i=0;$i<count($fields);$i++)	{
				
				$head .= $this->loadfieldvalidation($fields[$i]->id, $params).$lnEnd;
				
			}
			
			$head .= 'return true;'.$lnEnd.' }'.$lnEnd.'</script>'.$lnEnd;
			
		}
				
		foreach($document->_script as $k=>$v)
			$head .= $tab.'<script type="'.$k.'">'.$v.'</script>'.$lnEnd;
			
		foreach($document->_custom as $custom) {
			$head .= $tab.$custom.$lnEnd;
		}
		
				
		$html = $head.$body;
		
		return $html;
		
		
		}
		
		else
			return null;
				
		
	}
	
	function loadformfield($fieldid, $itemid, $params=array())
	{
		
		settype($itemid, 'int');
		
		$document = JFactory::getDocument();
		
		$params['icon'] = isset($params['icon'])?$params['icon']:true;
		$params['form'] = isset($params['form'])?$params['form']:'adminform';
		
		$view = JRequest::getVar('view', '');
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)
			return null;
		
		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			return JText::_('UNKCOLTYP');
		}
		
		$html = $this->fieldtypes[$field->type]->loadeditform($field->id, $itemid, $params);
		
		if($params['icon'])	{
		
			if($field->required)
				$html .= '  <em class="required">*</em>';
			
			if(!empty($field->text))
				$html .= ' <span class="hasTip info" title="'.$field->text.'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" alt="info" align="absbottom" /></span>';
			
		}
			
		return $html;
		
	}
	
	function loadsearchfield($fieldid, $itemid, $params=array())
	{
		
		settype($itemid, 'int');
		
		$document = JFactory::getDocument();
		
		$params['form'] = isset($params['form'])?$params['form']:'adminform';
		
		$view = JRequest::getVar('view', '');
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)
			return null;
			
		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			return JText::_('UNKCOLTYP');
		}
		
		if(method_exists($this->fieldtypes[$field->type], 'loadsearchform'))
			return $this->fieldtypes[$field->type]->loadsearchform($field->id, $itemid, $params);
		else
			return '';
					
	}
	
	function loadfieldvalidation($fieldid, $params=array())	{
			
		$params['form'] = isset($params['form'])?$params['form']:'adminform';
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)
			return '';
		
		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			return JText::_('UNKCOLTYP');
		}
		
		if(method_exists($this->fieldtypes[$field->type], 'loadvalidation'))
			return $this->fieldtypes[$field->type]->loadvalidation($field->id, $params);
		else
			return '';
			
	}
	
	function displayfieldvalue($itemid, $fieldid, $params=array())
	{
				
		$document =  JFactory::getDocument();
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)
			return '';

		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			return '';
		}
		
		if(method_exists($this->fieldtypes[$field->type], 'displayfieldvalue'))
			return $this->fieldtypes[$field->type]->displayfieldvalue($itemid, $field->id, $params);
		else
			return '';

		
	}
	
	function getfieldvalue($itemid, $fieldid)
	{
		
		if(!$itemid or !$fieldid)
			return null;
		
		settype($itemid, 'int');
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)
			return null;

		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			return null;
		}
		
		if(method_exists($this->fieldtypes[$field->type], 'getfieldvalue'))
			return $this->fieldtypes[$field->type]->getfieldvalue($itemid, $field->id);
		else
			return null;
		
	}
	
	function updatefieldvalue($itemid, $fieldid, $value='')
	{
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)	{
			$this->setError(JText::_('FIELDNOTEXISTS'));
			return false;
		}
		
		settype($itemid, 'int');
		
		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			$this->setError(JText::_('UNKCOLTYP'));
			return false;
		}
		
		if(method_exists($this->fieldtypes[$field->type], 'updatefieldvalue'))
			return $this->fieldtypes[$field->type]->updatefieldvalue($itemid, $field->id, $value);
		else	{
			$this->setError(JText::_('UNKCOLTYP'));
			return false;
		}
				
	}
	
	
	function checkField($itemid, $fieldid)
	{
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)	{
			$this->setError(JText::_('FIELDNOTEXISTS'));
			return false;
		}
		
		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			$this->setError(JText::_('UNKCOLTYP'));
			return false;
		}
		
		if(method_exists($this->fieldtypes[$field->type], 'checkField'))
			return $this->fieldtypes[$field->type]->checkField($itemid, $field->id);
		else	{
			$this->setError(JText::_('UNKCOLTYP'));
			return false;
		}
		
	}
	
	function saveFields($itemid, $fields)
	{
		
		foreach($fields as $field)	{
							
			if(!$this->saveField($itemid, $field->id))
				return false;
							
		}
		
		return true;
		
	}
	
	function saveField($itemid, $fieldid)
	{
		
		$field = $this->getField($fieldid);
		
		if(!$field->id)	{
			$this->setError(JText::_('FIELDNOTEXISTS'));
			return false;
		}
		
		settype($itemid, 'int');
		
		if(!array_key_exists($field->type, $this->fieldtypes))
			$this->fieldtypes[$field->type] = $this->getFieldtype($field->type);
			
		if(!is_object($this->fieldtypes[$field->type]))	{
			$this->setError(JText::_('UNKCOLTYP'));
			return false;
		}
		
		if(method_exists($this->fieldtypes[$field->type], 'saveField'))
			return $this->fieldtypes[$field->type]->saveField($itemid, $field->id);
		else	{
			$this->setError(JText::_('UNKCOLTYP'));
			return false;
		}
		
	}
	
	function loadfieldoptions($id, $type)
	{
		
		$row = $this->getField($id);
		
		settype($id, 'int');
		
		if(!array_key_exists($type, $this->fieldtypes))
			$this->fieldtypes[$type] = $this->getFieldtype($type);
			
		if(!is_object($this->fieldtypes[$type]))	{
			return JText::_('UNKCOLTYP');
		}
		
		if(method_exists($this->fieldtypes[$type], 'loadfieldoptions'))
			return $this->fieldtypes[$type]->loadfieldoptions($id, $type);
		else	{
			return JText::_('UNKCOLTYP');
		}
		
	}
	
}