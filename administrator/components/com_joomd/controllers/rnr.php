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


class JoomdControllerRnr extends JoomdController
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
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		
		JRequest::setVar( 'layout', 'form'  );
		
		$html = parent::edit();
		
		echo $html;
		
	}
	
	function save()
	{
		$model = $this->getModel('rnr');
		
		$obj = $model->store();
		
		echo json_encode($obj);
		
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function delete()
	{
		$model = $this->getModel('rnr');
		
		$json = new stdClass();
		
		if($model->delete())	{
						
			$msg = JText::_('REVIEWDELETESUCCESS');
			
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
	
	function publish()
	{
		$model = $this->getModel('rnr');
		$task = $this->getTask();
		
		$json = new stdClass();
		
		if($model->publish())	{
			
			$data = $this->getItems();
			
			$msg = ($task=="publish")?JText::sprintf('REVIEWPUBLISHSUCCESS'):JText::sprintf('REVIEWUNPUBLISHSUCCESS');
			
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
	
	function getItems()
	{
		
		$model = $this->getModel('rnr');
		
		$items = $model->getItems();
		$params = $model->getParams();
		
		ob_start();
				
		$k = 0;
		$i = $params->limitstart;
		
		if(count($items))	{
		
			for ($n=0; $n < count( $items ); $n++)
			{
				$row =  $items[$n];
				
				require(JPATH_COMPONENT.DS.'views'.DS.'rnr'.DS.'tmpl'.DS.'default_item.php');
				
				$k = 1 - $k;
				$i++;
				
			}
			
		}
		
		else
			echo '<tr class="no_item_block"><td colspan="8" align="center">'.JText::_('NO_ITEM_CREATED').'</td></tr>';
		
		$html = ob_get_contents();
		
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
	
	function get_pitems()
	{
		
		$model = $this->getModel('rnr');
		
		$item = $model->getItem();
		
		$items = $model->getPluginitems();
		
		$obj = new stdClass();
		
		$obj->result = 'success';
		$obj->list = '<option value="0">'.JText::_('SELECTITEM').'</option>';
		
		for($i=0;$i<count($items);$i++)
		{
			
			$obj->list .= '<option value="'.$items[$i]->id.'"';
			if($item->itemid==$items[$i]->id)
				$obj->list .= ' selected="selected"';
			$obj->list .= '>'.$items[$i]->title.'</option>';
			
		}
		
		echo json_encode($obj);
		
	}
	
}