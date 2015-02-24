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
 
jimport( 'joomla.application.component.model' );


class JoomdModelConfig extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();

	}
	
	function getConfig()
	{
	
		$query = 'select * from #__joomd_config';
		$this->_db->setQuery( $query );
		$config = $this->_db->loadObject();
		
		$registry = new JRegistry;
		$registry->loadString($config->social);
		$config->social = $registry;
		
		return $config;
		
	}
	
	function getPanels()
	{
		
		$items = Joomd::getApps();
		
		$data = array();
		
		for($i=0;$i<count($items);$i++)	{
			
			if(is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php'))	{
				
				$class = "JoomdApp".ucfirst($items[$i]->name);
				
				if(!class_exists($class))
					require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php');
								
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'config_display'))	{
						
						$items[$i]->html = $class->config_display();
						$data[] = $items[$i];
						
					}
				
				}
				
			}
			
		}
		
		return $data;
	
	}
	
	function store()
	{
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		$obj = new stdClass();
		
		$obj->error = '';
		$obj->result = 'error';
		
		$post = JRequest::get('post');
		
		$copyright	= JRequest::getInt('copyright', 0);
		$template	= JRequest::getInt('template', 1);
		$asearch	= JRequest::getInt('asearch', 0);
		$scroll		= JRequest::getInt('scroll', 0);
		$captcha	= JRequest::getInt('captcha', 0);
		$thumb_width= JRequest::getInt('thumb_width', 0);
		$thumb_height= JRequest::getInt('thumb_height', 0);
		$email		= str_replace(' ', '', JRequest::getVar('email', ''));
		
		$emails = empty($post['email'])?array():explode(',', $email);
		
		foreach($emails as $v)	{
			
			if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $v))	{
				$obj->error = JText::_('PLSSENTVLIDEM');
				return $obj;
			}
		}
		
		$email = implode(',', $emails);
		
		$social	= JRequest::getVar('social', array(), 'post', 'array');
		
		$social	= json_encode($social);
		
		$insert = new stdClass();
		
		$insert->id				= 1;
		$insert->copyright		= $copyright;
		$insert->template		= $template;
		$insert->asearch		= $asearch;
		$insert->scroll			= $scroll;
		$insert->captcha		= $captcha;
		$insert->email			= $email;
		$insert->social			= $social;
		$insert->thumb_width	= $thumb_width;
		$insert->thumb_height	= $thumb_height;
		
		if(!$this->_db->updateObject('#__joomd_config', $insert, 'id'))
			$obj->error = $this->_db->stderr();
			
		elseif(!$this->saveconfig($post))
			$obj->error = $this->getError();
		
		else	{
			
			$obj->msg = JText::_('SAVECONFIG');
			$obj->result = 'success';
			
		}
		
		return $obj;
		
	}
	
	function saveconfig($post)	{
		
		$items = Joomd::getApps();
				
		for($i=0;$i<count($items);$i++)	{
			
			if(is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php'))	{
				
				$class = "JoomdApp".ucfirst($items[$i]->name);
				
				if(!class_exists($class))
					require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php');
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'config_save'))	{
						
						if(!$class->config_save($post, $this))
							return false;
						
					}
				
				}
				
			}
			
		}
		
		return true;
		
	}
	
	function getThemes()
	{
		
		$this->scanthemes();
		
		$query = 'select * from #__joomd_templates order by name asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	function scanthemes()
	{
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$dir = JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'templates';
		
		$query = 'select name from #__joomd_templates order by name asc';
		$this->_db->setQuery( $query );
		$excludes = (array)$this->_db->loadResultArray();
		
		$themes = Jfolder::folders($dir, '.', false, false, $excludes);
		
		if(count($themes))	{
			
			for($i=0;$i<count($themes);$i++)	{
				
				if(!empty($themes[$i]))	{
					$query = 'insert into #__joomd_templates (name) values ('.$this->_db->Quote($themes[$i]).')';
					$this->_db->setQuery( $query );
					
					if(!$this->_db->query())	{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
				
			}
																											
		}
		
		foreach($excludes as $t)	{
			
			if(!Jfolder::exists($dir.DS.$t))	{
				$query = 'delete from #__joomd_templates where id <> 1 and name = '.$this->_db->Quote($t);
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			
		}
		
		return true;
		
	}

}

?>