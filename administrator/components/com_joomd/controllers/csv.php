<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD CSV Application
# ------------------------------------------------------------------------
# author    Noorullah Kalim - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class JoomdControllerCsv extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	   {
		parent::__construct();
		
		JRequest::setVar( 'view', 'csv' );

		}
		
	function export_data()
	{
	
		$model =& $this->getModel('csv');
		$data = $model->getExport_data();	
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		
		foreach ($data as $fields) {
			fputcsv($output, $fields);
		}
		
		jexit();
	}
 
	function import_data()	{
	
		JRequest::checkToken() or jexit( JText::_('Invalid Token') );
		
		$mainframe = JFactory::getApplication();
		
		$model = $this->getModel('csv');
		
		$msg = $model->importEntrydata();
		
		$this->setRedirect('index.php?option=com_joomd&view=csv', $msg);
	
	}
 
 
	function list_category()	{
		
		$model = $this->getModel('csv');
		$htm = $model->list_category();
		
		$json = new stdClass();
		
		$json->result = "success";
		$json->html = $htm;
		
		echo json_encode($json);
	 
	}

	function list_field()	{
		
		$model = $this->getModel('csv');
		$htm = $model->list_field();
		
		$json = new stdClass();
		
		$json->result = "success";
		$json->html = $htm;
		
		echo json_encode($json);
	
	}
	 


}