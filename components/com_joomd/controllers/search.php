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

class JoomdControllerSearch extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->jdapp = Joomd::getApp();
		
		$this->config =  Joomd::getConfig('item');
		$this->_user =  Joomd::getUser('item');
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$this->model = $this->getModel('search');
		
		$this->field = $this->model->_field;
		
		$this->type = Joomd::getType();		
		
		$this->checkAccess();
				
	}
	
	function checkAccess()
	{
		
		if(!in_array($this->type->config->get('list'), $this->_user->getAuthorisedViewLevels()))	{
			$this->jdapp->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JRoute::_('index.php?option=com_joomd&view=search'))), JText::_('PLSLOHTOACC'));
		}
		
	}
	
	function search()
	{
	
		// Check for request forgeries (removed in JoomD v2.1)
		//JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$post = JRequest::get('post');
		
		$arr = array();
		
		//remove unnecessary variables
		unset($post['task']);
		unset($post['submit']);
		
		foreach($post as $k=>$v)
		{
			
			if(!empty($v) and !strstr($k, 'multiselect'))
				$arr[$k] = $v;
			
		}
	
		$uri = JURI::getInstance();
		$uri->setQuery($arr);
		$uri->setVar('option', 'com_joomd');
		$uri->setVar('view', 'search');
		$uri->setVar('layout', 'search');
		
		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	
	}
	
	function getItems()
	{
		
		$layout = JRequest::getCmd('layout', 'default');
		$path = Joomd::getTemplatePath('search', $layout.'_item.php');
		
		$params = $this->model->getParams();
		
		$this->items = $this->model->getItems();
		$this->fields =  $this->field->getFields(array('cats'=>$params->cats, 'published'=>1, 'list'=>true));
		
		ob_start();
		$n = $params->limitstart;
		for($i=0;$i<count($this->items);$i++)	{
		
			$item =  $this->items[$i];
						
			require($path);
			
			$n++;
		}
		
		$html = mb_convert_encoding(ob_get_contents(), 'UTF-8');
		
		ob_end_clean();
		
		$data = new stdClass();
		
		$data->html = $html;
		$data->count = count($this->items);
		$data->total = $params->total;
		
		return $data;
		
	}
	
	function loaditems()
	{
		
		$data = $this->getItems();
		
		$json = new stdClass();
		
		$json->result = "success";
		$json->html = $data->html;
		$json->count = $data->count;
		$json->total = $data->total;
		
		jexit(json_encode($json));
	
	}

}