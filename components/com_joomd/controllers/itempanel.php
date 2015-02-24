<?php

/*------------------------------------------------------------------------
# com_joomd - Joomd Manager
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class JoomdControllerItempanel extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		
		parent::__construct();
		
		$this->jdapp = Joomd::getApp();
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
				
		$this->model = $this->getModel('itempanel');
		
		$this->config = Joomd::getConfig('item');
		$this->user = Joomd::getUser('item');
		
		$this->getTypeid();
		
		//to check the assigned type and it's access level
		$this->_type = Joomd::getType();
		
		$this->checkAccess();
		
		$this->field = new JoomdAppField();
		
		$this->registerTask( 'add',	'edit' );
		$this->registerTask( 'apply',	'save' );
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'unfeatured', 'featured' );
		
	}
	
	//to check the access level of individual item/in configuration/by type/by category
	protected function checkAccess()
	{
				
		$layout	= JRequest::getCmd('layout', '');
		$task	= JRequest::getCmd('task', '');
		
		$array = JRequest::getVar('cid',  0, '', 'array');
		$id = (int)$array[0];
		
		if(($task == "add" or $task == "save" or $task == "loadfields" or $layout == "form") and !$id)	{
			
			if(!Joomd::isAuthorised('addaccess'))	{
				$this->jdapp->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JRoute::_('index.php?option=com_joomd&view=itempanel&typeid='.$this->_type->id))), $this->jdapp->getError());
				return;
			}
			
		}
		else	{
			
			if(!Joomd::isAuthorised('manage'))	{
				$this->jdapp->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JRoute::_('index.php?option=com_joomd&view=itempanel&typeid='.$this->_type->id))), $this->jdapp->getError());
				return;
			}
			
		}
		
	}
	
	//to get typeid if itemid is there
	protected function getTypeid()
	{
		$typeid = JRequest::getInt('typeid', 0);
		$itemid = JRequest::getInt('id', 0);
		
		if(!$typeid and $itemid)	{
			$item = $this->model->getItem();
			
			JRequest::setVar('typeid', $item->typeid);
		}
		
		
	}
	
	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		
		JRequest::setVar( 'view', 'itempanel'  );
		JRequest::setVar( 'layout', 'form'  );
		
		$html = parent::edit();
		
		jexit($html);
		
	}
	
	function save()
	{
		
		$obj = $this->model->store();
		
		jexit(json_encode($obj));

	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function delete()
	{
		
		$json = new stdClass();
		
		if($this->model->delete())	{
						
			$msg = JText::_('ITEMREVSUCC');
			
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
		
		jexit(json_encode($json));

	}
	
	// publish/unpublish the items
	function publish()
	{
		$task = $this->getTask();
		
		$json = new stdClass();
		
		if($this->model->publish())	{
			
			$data = $this->getItems();
			
			$msg = ($task=="publish")?JText::_('ITEM_PUBLISHSUCCESS'):JText::_('ITEM_UNPUBLISHSUCCESS');
			
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
	
	//since JoomD 2.3 to change feature state
	function featured()
	{
		$task = $this->getTask();
		
		$json = $this->model->featured();
		
		echo json_encode($json);
		
	}
	
	protected function getItems()
	{
				
		$layout = JRequest::getCmd('layout', 'default');
		$path = Joomd::getTemplatePath('itempanel', $layout.'_item.php');
		
		$items = $this->model->getItems();
		$params = $this->model->getParams();
		
		$this->firstfield =  $this->field->get_firstfield();
		
		ob_start();
		
		$now =  JFactory::getDate();
						
		$k = 0;
		$i = $params->limitstart;
		
		if(count($items))	{
		
			for ($n=0; $n < count( $items ); $n++)
			{
				$row =  $items[$n];
							
				require($path);
				
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
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$data = $this->getItems();
		
		$json = new stdClass();
		
		$json->result = "success";
		$json->html = $data->html;
		$json->count = $data->count;
		$json->total = $data->total;
		
		jexit(json_encode($json));
	
	}
	
	function loadfields()
	{
		
		$cats = JRequest::getVar( 'catid', array(), 'post', 'array' );
		$id = JRequest::getInt('id', 0);
		
		$html = $this->field->loadformfields($id, array('cats'=>$cats, 'form'=>'editform'));
		
		jexit($html);
		
	}
	
	function delete_custom()
	{
				
		if($this->model->delete_custom())	{
			$msg = JText::_('IMAGEDELETESUCCESS');
			jexit('{"result":"success", "msg":"'.$msg.'"}');
		}
		else	{
			
			jexit('{"result":"error", "error":"'.$this->model->getError().'"}');
			
		}
		
	}

}