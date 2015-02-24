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
		
		$this->jdapp = Joomd::getApp();
		
		$this->config = Joomd::getConfig('item');
		$this->user =  Joomd::getUser();
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$this->model = $this->getModel('item');
		
		$this->field = $this->model->_field;
		
		$this->checkAccess();
		
		$this->registerTask( 'add',	'edit' );
		
	}
	
	//to check the access level of individual item/in configuration/by type/by category
	function checkAccess()
	{
		
		$layout = JRequest::getCmd('layout', '');
				
		if($layout == 'detail')	{
			$item = $this->model->getItem();
			JRequest::setVar('typeid', $item->typeid);
			
			//to check the access level of item
			if(!Joomd::CanAccessItem($item))
				$this->jdapp->redirect('index.php', $this->jdapp->getError());
		
		/*  removed as not fine while displaying the item list without category selection and in search
			if(!$this->model->checkCategoryAccess())	{
				Joomd::redirect('index.php', JText::_('AUTH_NOACCESS'));
			}
		*/	
		
		}
		
		//to check the assigned type and it's access level
		$this->type = Joomd::getType();
		
		$catid = JRequest::getInt('catid', 0);
		$category = $this->model->getCategory();
		
		//check category if its exists
		if(empty($category) and $catid)	{
			$this->jdapp->redirect(JRoute::_('index.php?option=com_joomd'), JText::_('CATEGORYNOTFOUND'));			
		}
		elseif($catid)	{
			
			if(!Joomd::CanAccessItem($category))
				$this->jdapp->redirect('index.php', $this->jdapp->getError());
			
		}
		
		//can access the list layout set in configuration
		if(!in_array($this->type->config->get('list'), $this->user->getAuthorisedViewLevels()))	{
			$this->jdapp->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JRoute::_('index.php?option=com_joomd&view=item&typeid='.$this->type->id))), JText::_('AUTH_NOACCESS'));
			return;
		}
		
		//can access the detail layout set in configuration
		if(!in_array($this->type->config->get('detail'), $this->user->getAuthorisedViewLevels()) and $layout == "detail")	{
			$this->jdapp->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JRoute::_('index.php?option=com_joomd&view=item&typeid='.$this->type->id))), JText::_('AUTH_NOACCESS'));
			return;
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
	
	function getItems()
	{
		
		$config = Joomd::getConfig();
		
		$layout = JRequest::getCmd('layout', 'default');
		$path = Joomd::getTemplatePath('item', $layout.'_item.php');
		
		$params = $this->model->getParams();
		
		$this->items = $this->model->getItems();
		$this->fields =  $this->field->getFields(array('cats'=>$params->catid, 'published'=>1, 'list'=>true, 'access'=>true));
		
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
	
	function loaditem($item, $fields)
	{
				
		$this->item = $item;
		$this->fields = $fields;
		
		$firstfield = $this->field->get_firstfield(array('published'=>1));
		
		$html = '';
		
		if(!Joomd::CanAccessItem($item))
			return $html;
	/*			
		if(!$this->model->checkCategoryAccess($item->id))
			return $html;
	*/	
		
		if($firstfield->type == 1)	{
			$html .= '<div class="componentheading"><h1>'.$this->field->displayfieldvalue($this->item->id, $firstfield->id).'</h1></div>';
		}
		
		for($i=0;$i<count($this->fields);$i++)	{
		
			$value = $this->field->getfieldvalue($this->item->id, $this->fields[$i]->id);
			
			if(!($i==0 and $this->fields[$i]->type == 1) and !empty($value))	{
			
				$html .= '<div class="field_block '.$this->fields[$i]->cssclass.'">';
				
				$html .= '<div class="field_label">';
				
				if($this->fields[$i]->showtitle)
					$html .= $this->fields[$i]->name;
				
				if($this->fields[$i]->showicon && !empty($this->fields[$i]->icon) && is_file(JPATH_SITE.'/images/joomd/'.$this->fields[$i]->icon))
					$html .= '&nbsp;<img src="'.JURI::root().'images/joomd/'.$this->fields[$i]->icon.'" alt="'.$this->fields[$i]->name.'" style="max-height:16px;" align="absbottom" />';
				
				$html .= '</div>';
				
				$html .= $this->field->displayfieldvalue($this->item->id, $this->fields[$i]->id);
				
				$html .= '</div>';
			
			}
		
		}
		
		return $html;
		
	}
	
	function printall()
	{
		
		$doc = JFactory::getDocument();
		
		$items =  $this->model->getItems();
		
		$obj = new stdClass();
		
		$obj->html = '<!DOCTYPE html>
					<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$doc->getLanguage().'" lang="'.$doc->getLanguage().'">
					<head>
					<meta http-equiv="content-type" content="text/html; charset='.$doc->getCharset().'" />
					<link href="'.JURI::root().'components/com_joomd/templates/'.$this->config->theme.'/css/style.css" media="print" rel="stylesheet" type="text/css" /><body><a href="javascript:void(0);" onClick="$jd(\'#joomdprint\').printElement();" class="printicon"><span>print</span></a><div id="joomdprint">';
		
		for($i=0;$i<count($items);$i++)	{
			
			//get fields to generate the pdf/print
			$fields =  $this->field->getFields(array('itemid'=>$items[$i]->id, 'published'=>1, 'detail'=>true, 'type'=>array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));
			
			$obj->html .= '<div class="item_row">'.$this->loaditem($items[$i], $fields).'</div>';
			
		}
		
		$obj->html .= '</div></body></html>';
		
		$obj->result = 'success';
		
		jexit(json_encode($obj));
		
	}
	
	function printone()
	{
		
		$doc = JFactory::getDocument();
		
		$item = $this->model->getItem();
		
		$obj = new stdClass();
		
		$obj->html = '<!DOCTYPE html>
					<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$doc->getLanguage().'" lang="'.$doc->getLanguage().'">
					<head>
					<meta http-equiv="content-type" content="text/html; charset='.$doc->getCharset().'" />
					<link href="'.JURI::root().'components/com_joomd/templates/'.$this->config->theme.'/css/style.css" media="print" rel="stylesheet" type="text/css" /><body><a href="javascript:void(0);" onClick="$jd(\'#joomdprint\').printElement();" class="printicon"><span>print</span></a><div id="joomdprint">';
		
		//get fields to generate the pdf/print
		$fields =  $this->field->getFields(array('itemid'=>$item->id, 'published'=>1, 'detail'=>true, 'type'=>array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));
		
		$obj->html .= $this->loaditem($item, $fields);
		
		$obj->html .= '</div></body></html>';
		
		$obj->result = 'success';
		
		jexit(json_encode($obj));
		
	}

	function sendemail()
	{
		
		$app =  JFactory::getApplication();
		$sitename = $app->getCfg('sitename');
		
		$to = JRequest::getVar('mailto');
		$sender = JRequest::getVar('sender');
		$from = JRequest::getVar('from');
		$subject = JRequest::getVar('subject');
		$url = JRequest::getVar('url', '', 'post', 'string');
		
		$subject = $sitename.'::'.$subject;
				
		$body = JText::sprintf('SHARE_MAIL_BODY', $sender, $from, $url);

		$sent = Joomd::notify($subject, $body, array('from'=>array($from, $sender), 'to'=>$to));
		
		if(is_object($sent))
			jexit('{"result":"error", "error":"'.JText::_('SORRYEMAILNOTSENT').'"}');
		else
			jexit('{"result":"success", "msg":"'.JText::_('MAILISSENT').'"}');
		
	}
	
	function report_item()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = $this->model->report_item();

		jexit(json_encode($obj));
				
	}
	
	function save_item()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = $this->model->save_item();

		jexit(json_encode($obj));
				
	}
	
	function remove_item()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = $this->model->remove_item();

		jexit(json_encode($obj));
				
	}
	
	function contact_item()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = $this->model->contact_item();

		jexit(json_encode($obj));
				
	}

}