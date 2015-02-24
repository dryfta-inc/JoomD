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


class JoomdControllerField extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		// Register Extra tasks
		$this->registerTask( 'add',	'edit' );
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'accesspublic', 'access' );
		$this->registerTask( 'accessregistered', 'access' );
		$this->registerTask( 'accessspecial', 'access' );
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$this->_field = new JoomdAppField();

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
		$model = $this->getModel('field');
		
		$obj = $model->store();
		
		echo json_encode($obj);
		
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function delete()
	{	
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		$cid = JRequest::getVar('cid',  0, '', 'array');
		
		$json = new stdClass();
		
		if($this->_field->delete($cid))	{
						
			$msg = JText::_('DELETESUCCESS');
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = $msg;
			$json->error = $this->_field->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
			
		}
		else	{
			$json->result = "error";
			$json->msg = '';
			$json->error = $this->_field->getError();
			
		}
		
		echo json_encode($json);

	}
	
	function publish()
	{
		$model = $this->getModel('field');
		$task = $this->getTask();
		
		$json = new stdClass();
		
		if($model->publish())	{
			
			$data = $this->getItems();
			
			$msg = ($task=="publish")?JText::_('PUBLISHSUCCESS'):JText::_('UNPUBLISHSUCCESS');
			
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
	
	function reorder()
	{
	
		$model = $this->getModel('field');
		
		$json = new stdClass();
		
		if($model->reorder())	{
			
			$data = $this->getItems();
			
			$msg = JText::_('NEWORDERSAVE');
			
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
	
	function saveorder()
	{
	
		$model = $this->getModel('field');
		
		$json = new stdClass();
		
		if($model->saveorder())	{
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = JText::_('NEWORDERSAVE');
			$json->error = $model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
						
		}
		else	{
			
			$json->result = "error";
			$json->error = $model->getError();
			
		}
		
		echo json_encode($json);
	
	}
	
	function getItems()
	{
		
		$model = $this->getModel('field');
		
		$items = $model->getItems();
		$params = $model->getParams();
		
		ob_start();
		
		$ordering = ($params->filter_order == 'i.ordering');
		
		$disabled = $ordering?'':'disabled="disabled"';
		
		$k = 0;
		$i = $params->limitstart;
		
		if(count($items))	{
		
			for ($n=0; $n < count( $items ); $n++)
			{
				$row =  $items[$n];
				
				require(JPATH_ADMINISTRATOR.'/components/com_joomd/views/field/tmpl/default_item.php');
				
				$k = 1 - $k;
				$i++;
				
			}
		
		}
		
		else
			echo '<tr class="no_item_block"><td colspan="9" align="center">'.JText::_('NO_ITEM_CREATED').'</td></tr>';
		
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
	
	function delete_icon()
	{
		
		$model = $this->getModel('field');
		
		if($model->delete_icon())	{
			$msg = JText::_('IMAGEDELETESUCCESS');
			echo '{"result":"success", "msg":"'.$msg.'"}';
		}
		else	{
			
			echo '{"result":"error", "error":"'.$model->getError().'"}';
			
		}
		
	}
	
	function reloadcats()
	{
		
		$model = $this->getModel('field');
		
		$cats = $model->getCats();
		
		$obj = new stdClass();
		
		$obj->list = '';
				
		for($i=0;$i<count($cats);$i++)	{
		
			$obj->list .= '<option value="'.$cats[$i]->id.'"';
			if($cats[$i]->selected)
				$obj->list .= ' selected="selected"';
			$obj->list .= '>'.$cats[$i]->treename.'</option>';
			
		}
		
		$obj->result = 'success';
		
		echo json_encode($obj);
		
	}
	
	function loadfieldoptions()
	{
		
		// Check for request forgeries
		JRequest::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}');
		
		$type = JRequest::getInt('type', 0);
		$id = JRequest::getInt('id', 0);
		
		if(!$type)
			jexit('{"result":"error", "error":"'.JText::_('PLEASESELFIELDT').'"}');
		
		$obj = new stdClass();
		
		$obj->html = $this->_field->loadfieldoptions($id, $type);
		
		$obj->result = 'success';
			
		echo json_encode($obj);
		
	}
	
}