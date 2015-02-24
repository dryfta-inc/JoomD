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

class JoomdControllerCategory extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->jdapp = Joomd::getApp();
		
		$this->checkAccess();
				
	}
	
	function checkAccess()
	{
		
		$this->type = Joomd::getType();
		
		$model = $this->getModel('category');
		
		$catid = JRequest::getInt('catid', 0);
		$category = $model->getParent();
		
		if(empty($category) and $catid)	{
			$this->jdapp->redirect(JRoute::_('index.php?option=com_joomd&view=joomd'), JText::_('CATEGORYNOTFOUND'));
			return;
		}
		elseif($catid)	{
			$user =  JFactory::getUser();
				
			if(!in_array($category->access, $user->getAuthorisedViewLevels()))
				$this->jdapp->redirect('index.php', JText::_('AUTH_NOACCESS'));
			
		}
		
	}
	
	function getItems()
	{
		
		$config = Joomd::getConfig();
		
		$model = $this->getModel('category');
		$layout = JRequest::getCmd('layout', 'default');
		$path = Joomd::getTemplatePath('category', $layout.'_item.php');
		
		$items = $model->getItems();
		$params = $model->getParams();
		
		ob_start();
				
		for ($i=0; $i < count( $items ); $i++)
		{
			$this->item =  $items[$i];
			
			require($path);
			
		}
		
		$html = mb_convert_encoding(ob_get_contents(), 'UTF-8');
		
		ob_end_clean();
		
		$data = new stdClass();
		
		$data->html = $html;
		$data->count = count($items);
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