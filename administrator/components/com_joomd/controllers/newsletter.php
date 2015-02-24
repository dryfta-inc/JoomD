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
defined('_JEXEC') or die('Restricted access');


class JoomdControllerNewsletter extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'unpublish', 'publish' );
		
		require_once(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'apps'.DS.'field/app_field.php');
		
	}
	
	function getItems()
	{
		
		$model = $this->getModel('newsletter');
		
		$items = $model->getItems();
		$params = $model->getParams();
		
		ob_start();
				
		$k = 0;
		$i = $params->limitstart;
		
		if(count($items))	{
		
			for ($n=0; $n < count( $items ); $n++)
			{
				$row =  $items[$n];
				
				require(JPATH_ADMINISTRATOR.'/components/com_joomd/views/newsletter/tmpl/default_item.php');
				
				$k = 1 - $k;
				$i++;
				
			}
		
		}
		
		else
			echo '<tr class="no_item_block"><td colspan="7" align="center">'.JText::_('NO_SUBSCRIBER_FOUND').'</td></tr>';
		
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
		
		echo json_encode($json);
	
	}
	
	function save()
	{
		$model = $this->getModel('newsletter');
		
		$obj = $model->store();
		
		echo json_encode($obj);
		
	}
	
	/**
	 * remove record(s)
	 * @return void
	 */
	function delete()
	{
		$model = $this->getModel('newsletter');
		
		$json = new stdClass();
		
		if($model->delete())	{
						
			$msg = JText::_('DELETESUCCESS');
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = $msg;
			$json->error = $model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
			
		}
		else	{
			$json->result = "error";
			$json->msg = '';
			$json->error = $model->getError();
			
		}
		
		echo json_encode($json);

	}
	
	function get_cats()
	{
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"success", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$model = $this->getModel('newsletter');
				
		$items = $model->getCats();
		
		$obj = new stdClass();
		
		$obj->result = 'success';
		$obj->list = '<option value="0">'.JText::_('SELCAAT').'</option>';
		
		for($i=0;$i<count($items);$i++)
		{
			
			$obj->list .= '<option value="'.$items[$i]->id.'">'.$items[$i]->name.'</option>';
			
		}
		
		echo json_encode($obj);
		
	}
	
	function get_temp()
	{
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"success", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$model = $this->getModel('newsletter');
				
		$obj = $model->get_temp();
		
		echo json_encode($obj);
		
	}
	
	function send_newsletter()
	{
		
		$model = $this->getModel('newsletter');
		
		$json = new stdClass();
		
		if($model->send_newsletter())	{
			$json->result = "success";
			$json->msg = JText::_('NEWSLETTERSENTSUCCESS');
		}
		else	{
			$json->result = "error";
			$json->error = $model->getError();
		}
		
		echo json_encode($json);
		
	}	
	
}