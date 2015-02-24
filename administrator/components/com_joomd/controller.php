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

jimport('joomla.application.component.controller');


class JoomdController extends JController
{
	
	function display($cachable = false, $urlparams = false)
	{
		
		$document =  JFactory::getDocument();
		
		parent::display();
		
	}
	
	function edit()
	{
		
		ob_start();
		
		parent::display();
		
		$body = ob_get_contents();
		
		ob_end_clean();
				
		$document =  JFactory::getDocument();
		
		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();
		
		$head = '';
		
		/*	
		foreach($document->_styleSheets as $k=>$v)	{
			
			$head .= $tab.'<link rel="stylesheet" href="'.$k.'" type="'.$v['mime'].'" />'.$lnEnd;
		}
	*/	
		foreach($document->_style as $k=>$v)	{
			
			$head .= $tab.'<style type="'.$k.'">'.$v.'</style>'.$lnEnd;
		}
	/*	
		$scripts = array('includes/js/joomla.javascript.js', 'media/system/js/mootools.js', 'tiny_mce.js');
		
		foreach($document->_scripts as $k=>$v)	{
			if(!(strpos($k, $scripts[0]) or strpos($k, $scripts[1]) or strpos($k, $scripts[2])))
				$head .= $tab.'<script type="'.$v.'" src="'.$k.'" />'.$lnEnd;
	
		}
	*/	
		foreach($document->_custom as $custom) {
			$head .= $tab.$custom.$lnEnd;
		}
		
	
		
		foreach($document->_script as $k=>$v)
			$head .= $tab.'<script type="'.$k.'">'.$v.'</script>'.$lnEnd;
		
		$html = $head.$body;
		
		return $html;
		
	}
	
	function syncemail()
	{
		
		$model = $this->getModel('joomd');
		
		$emails = $model->getEmails();
		
		header('Content-Disposition: attachment; filename="emails.txt"');
		
		echo implode(',', $emails);	
		
		jexit();
		
	}
	
	function drawchart()
	{
		
		$model = $this->getModel('joomd');
		
		$json = $model->gethitChart();
		
		echo $json;
		
	}

}