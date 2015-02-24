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


class JoomdControllerItem extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$this->model = $this->getModel('item');
		
		// Register Extra tasks
		$this->registerTask( 'add',	'edit' );
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'unfeatured', 'featured' );		
		
		$this->field = new JoomdAppField();
				
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
	
	//to save/apply the item
	function save()
	{		
		$obj = $this->model->store();
		
		echo json_encode($obj);
		
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function delete()
	{		
		$json = new stdClass();
		
		if($this->model->delete())	{
						
			$msg = JText::_('DELETESUCCESS');
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = $msg;
			$json->error = $this->model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
			
		}
		else	{
			$json->result = "error";
			$json->msg = '';
			$json->error = $this->model->getError();
			
		}
		
		echo json_encode($json);

	}
	
	// publish/unpublish the items
	function publish()
	{
		$task = $this->getTask();
		
		$json = new stdClass();
		
		if($this->model->publish())	{
			
			$data = $this->getItems();
			
			$msg = ($task=="publish")?JText::_('PUBLISHSUCCESS'):JText::_('UNPUBLISHSUCCESS');
			
			$json->result = "success";
			$json->msg = $msg;
			$json->error = $this->model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
			
		}
		else	{
		
			$json->result = "error";
			$json->msg = '';
			$json->error = $this->model->getError();
		
		}
		
		echo json_encode($json);
		
	}
	
	function featured()
	{
		$task = $this->getTask();
		
		$json = $this->model->featured();
		
		echo json_encode($json);
		
	}
	
	//to reorder the items
	function reorder()
	{
			
		$json = new stdClass();
		
		if($this->model->reorder())	{
			
			$data = $this->getItems();
			
			$msg = JText::_('NEWORDERSAVE');
			
			$json->result = "success";
			$json->msg = $msg;
			$json->error = $this->model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
			
		}
		else	{
		
			$json->result = "error";
			$json->msg = '';
			$json->error = $this->model->getError();
		
		}
		
		echo json_encode($json);
	
	}
	
	//to save the ordering of all items
	function saveorder()
	{
			
		$json = new stdClass();
		
		if($this->model->saveorder())	{
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = JText::_('NEWORDERSAVE');
			$json->error = $this->model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
						
		}
		else	{
			
			$json->result = "error";
			$json->error = $this->model->getError();
			
		}
		
		echo json_encode($json);
	
	}

	
	//prepare the rendering template for loading items
	function getItems()
	{
				
		$items = $this->model->getItems();
		$params = $this->model->getParams();
		
		$this->firstfield =  $this->field->get_firstfield();
		
		ob_start();
		
		$ordering = ($params->filter_order == 'i.ordering');
		
		$disabled = $ordering?'':'disabled="disabled"';
		
		$k = 0;
		$i = $params->limitstart;
		
		if(count($items))	{
		
			for ($n=0; $n < count( $items ); $n++)
			{
				$row =  $items[$n];
				
				require(JPATH_ADMINISTRATOR.'/components/com_joomd/views/item/tmpl/default_item.php');
				
				$k = 1 - $k;
				$i++;
				
			}
			
		}
		
		else
			echo '<tr class="no_item_block"><td colspan="6" align="center">'.JText::_('NO_ITEM_CREATED').'</td></tr>';
		
		$html = mb_convert_encoding(ob_get_contents(), 'UTF-8');
		
		ob_end_clean();
		
		$data = new stdClass();
		
		$data->html = $html;
		$data->count = count($items);
		$data->total = $params->total;
		
		return $data;
		
	}
	
	//to reload the list for all operations
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
	
	//to delete custom field files
	function delete_custom()
	{
				
		if($this->model->delete_custom())	{
			$msg = JText::_('IMAGEDELETESUCCESS');
			echo '{"result":"success", "msg":"'.$msg.'"}';
		}
		else	{
			
			echo '{"result":"error", "error":"'.$this->model->getError().'"}';
			
		}
		
	}
	
	//to load the fields in item form layout
	function loadfields()
	{
		
		$cats = JRequest::getVar( 'catid', array(), 'post', 'array' );
		$id = JRequest::getInt('id', 0);
		
		$html = $this->field->loadformfields($id, array('cats'=>$cats));
		
		echo $html;
		
	}
	
	//to reload the cats in menu parameters
	
	function loadcats()
	{
				
		$cats = $this->model->getCats();
		$catid = JRequest::getInt('catid', 0);
		
		$list = '<option value="">'.JText::_('ALL').'</option>';
		
		for($i=0;$i<count($cats);$i++)	{
		
			$list .= '<option value="'.$cats[$i]->id.'"';
			if($catid==$cats[$i]->id)
				$list .= ' selected="selected"';
			$list .= '>'.$cats[$i]->treename.'</option>';
			
		}
		
		echo $list;
		
	}
	
}