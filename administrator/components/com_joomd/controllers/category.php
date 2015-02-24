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
		
		// Register Extra tasks
		$this->registerTask( 'add',	'edit' );
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'unfeatured', 'featured' );

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
		$model = $this->getModel('category');
		
		$obj = $model->store();
		
		echo json_encode($obj);
		
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function delete()
	{
		$model = $this->getModel('category');
		
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
	
	function publish()
	{
		$model = $this->getModel('category');
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
	
	function featured()
	{
		$model = $this->getModel('category');
		$task = $this->getTask();
		
		$json = $model->featured();
		
		echo json_encode($json);
		
	}
	
	function reorder()
	{
	
		$model = $this->getModel('category');
		
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
	
		$model = $this->getModel('category');
		
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
		
		$model = $this->getModel('category');
		
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
				
				require(JPATH_ADMINISTRATOR.'/components/com_joomd/views/category/tmpl/default_item.php');
				
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
	
	function delete_img()
	{
		
		$model = $this->getModel('category');
		
		if($model->delete_img())	{
			$msg = JText::_('IMAGEDELETESUCCESS');
			echo '{"result":"success", "msg":"'.$msg.'"}';
		}
		else	{
			
			echo '{"result":"error", "error":"'.$model->getError().'"}';
			
		}
		
	}
	
	function loadcats()
	{
		
		$model = $this->getModel('category');
		
		$cats = $model->getCats();
		$catid = JRequest::getInt('catid', 0);
		
		$list = '<option value="">'.JText::_('TOP').'</option>';
		
		for($i=0;$i<count($cats);$i++)	{
		
			$list .= '<option value="'.$cats[$i]->id.'"';
			if($catid==$cats[$i]->id)
				$list .= ' selected="selected"';
			$list .= '>'.$cats[$i]->treename.'</option>';
			
		}
		
		echo $list;
		
	}
	
	function reloadcats()
	{
		
		$model = $this->getModel('category');
		
		$cats = $model->getCats();
		$item = $model->getItem();
		
		$obj = new stdClass();
		
		$obj->list = '<option value="0">'.JText::_('TOP').'</option>';
		
		for($i=0;$i<count($cats);$i++)	{
		
			$obj->list .= '<option value="'.$cats[$i]->id.'"';
			if($cats[$i]->id == $item->parent)
				$obj->list .= ' selected="selected"';
			$obj->list .= '>'.$cats[$i]->treename.'</option>';
			
		}
		
		$obj->result = 'success';
		
		echo json_encode($obj);
		
	}
	
	function loadfields()
	{
		
		$model = $this->getModel('category');
		
		$fields = $model->getFields();
		
		$obj = new stdClass();
		
		$obj->list = '';
		
		for($i=0;$i<count($fields);$i++)	{
		
			$obj->list .= '<option value="'.$fields[$i]->id.'"';
			if($fields[$i]->selected)
				$obj->list .= ' selected="selected"';
			$obj->list .= '>'.$fields[$i]->name.'</option>';
			
		}
		
		$obj->result = 'success';
		
		echo json_encode($obj);
		
	}
	
}