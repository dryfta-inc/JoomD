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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_SITE.'/components/com_joomd/libraries/loader.php');
require_once(JPATH_SITE.'/components/com_joomd/libraries/functions.php');
require_once(JPATH_SITE.'/components/com_joomd/libraries/joomdui.php');

class JoomdApp extends JApplication	{
	
	public $_abase = 0;
	public $_db = null;
	public $_notice = null;
	public $_warning = null;
	
	function __construct()
	{
		
		$app = JFactory::getApplication();
		
		parent::__construct(array('clientId'=>$app->getClientId()));
		
		$this->_doc = JFactory::getDocument();
		$this->_db =  JFactory::getDBO();
	
	}
		
	//initializes all the settings to work with the joomd framework
	function initialize()
	{
		
		static $init = false;
		
		if($init)
			return;
		
		$mainframe = JFactory::getApplication();
		
		set_exception_handler('JoomdException');
		
		JHTML::addIncludePath(JPATH_ROOT.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'html'.DS.'html');
		
		if($mainframe->isAdmin())	{
			define('JDPATH_BASE', JPATH_ADMINISTRATOR.'/components/com_joomd');
		}
		else	{
			define('JDPATH_BASE', JPATH_SITE.'/components/com_joomd');
		}
		
		$this->_abase = JRequest::getInt('abase', 0);
		
		if($this->_abase)	{
			header('Content-type: text/html; charset='.$this->_doc->getCharset());
			JRequest::setVar('tmpl', 'component');
		}
		else	{
			$loader = new Loader();
			$loader->loadjs();
			$loader->loadcss();
		}
				
		$this->loadLanguage();
		
		$init = true;
		
	}
	
	//load all the necessary language files.
	function loadLanguage()
	{
		
		$mainframe = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$view = JRequest::getVar('view', 'joomd');
		
		//get all the apps and iterate through each need to change it
		
		$query = 'select name, type, params from #__joomd_apps';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		for($i=0;$i<count($items);$i++)	{
			
			$registry = new JRegistry;
			$registry->loadString($items[$i]->params);
			$params = $registry;
			
			if($mainframe->isAdmin())	{
				if($items[$i]->type==3)
					$lang->load('field_'.$items[$i]->name.'.sys', JDPATH_BASE);
				else
					$lang->load('app_'.$items[$i]->name.'.sys', JDPATH_BASE);
				$v = 'aview';
			}
			else
				$v = 'sview';
			
			$findme = (array)$params->get($v);
						
			if(in_array($view, $findme))	{
				
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php');
				
				$class = 'JoomdApp'.ucfirst($items[$i]->name);
				$app = new $class;
				$app->loadLanguage();
				
			}
			
		}
		
	}
	
	//finalize the rendring
	function finalize()
	{
		
		$app = JFactory::getApplication();
		
		$config = Joomd::getConfig();
		
		//if it's ajax based call the exit the execution
		if($this->_abase)	{
			jexit();
		}
		else	{
			
			if($app->isAdmin())
				loader::loadpanel();
			elseif($config->copyright)
				Joomd::copyright();
		}
		
	}
	
	function getSubmenus()
	{
		
		$query = 'select m.*, a.descr from #__joomd_submenu as m left join #__joomd_apps as a on m.appid=a.id where a.published = 1 order by a.ordering asc, m.title asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		return $items;
		
	}
	
	//to get the warning if occurred
	function getWarning()
	{
		
		return $this->_warning;
		
	}
	
	//to set the warning if occur
	function setWarning($warning)
	{
	
		$this->_warning = $warning;
		
	}
	
	//to get the notice if occurred
	function getNotice()
	{
		
		return $this->_notice;
		
	}
	
	//to set the notice if occur
	function setNotice($notice)
	{
	
		$this->_notice = $notice;
		
	}
	
	//redirects/display the message based on request ( json type )
	function redirect($url, $msg = '', $msgType = 'message', $moved = false)
	{
				
		$abase = JRequest::getInt('abase', 0);
		
		if($abase)	{
			$headers = getallheaders();
			if(strstr($headers['Accept'], 'json'))
				jexit('{"result":"error", "error":"'.$msg.'"}');
			else
				jexit($msg);
		}
		else
			parent::redirect($url, $msg, $msgType, $moved);
		
	}
	
}