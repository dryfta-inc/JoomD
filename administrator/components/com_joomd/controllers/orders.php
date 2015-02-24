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


class JoomdControllerOrders extends joomdController
{

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	function __construct()
	{

		parent::__construct();

		
		JRequest::setVar( 'view', 'orders' );

		// Register Extra tasks
		$this->registerTask( 'add',	'edit' );

	}



	/**

	 * display the edit form

	 * @return void

	 */

	function edit()
	{
		JRequest::setVar( 'layout', 'form'  );
		
		$html = parent::edit();
		
		jexit($html);
		
	}

		

	function reorder()
	{

		$model = $this->getModel('orders');

		if($model->reorder())	{

			jexit('{"result":"success", "msg": "'.JText::_('RESTORESUCCESSFULLY').'"}');

		}
		else	{

			jexit('{"result":"error", "error": "'.$model->getError().'"}');

		}

	}


	function getItems()
	{

		$model = $this->getModel('orders');

		$items = $model->getItems();
		$params = $model->getParams();

		ob_start();

		$k = 0;
		$i = $params->limitstart;
		for ($n=0; $n < count( $items ); $n++)
		{
			$row =  $items[$n];
			
			require(JPATH_ADMINISTRATOR.'/components/com_joomd/views/orders/tmpl/default_item.php');
			
			$k = 1 - $k;
			$i++;
			
		}

		$html = mb_convert_encoding(ob_get_contents(), 'UTF-8');

		ob_end_clean();
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