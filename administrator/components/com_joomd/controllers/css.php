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


class JoomdControllerCss extends JoomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
	}
	
	function save()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$config = Joomd::getConfig();
		
		$content = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		jimport('joomla.filesystem.file');
		
		$file = JPATH_SITE.DS.'components/com_joomd/templates/'.$config->theme.'/css/style.css';
		
		ob_start();
	
		$ret = jfile::write($file, $content);
		
		$error = ob_get_contents();
		ob_end_clean();
		
		$obj = new stdClass();
		
		if($ret === false)	{
			$obj->result = 'error';
			$obj->error = $temp;
		}
		else	{
			$obj->result = 'success';
			$obj->msg = JText::_('SAVESUCCESS');
		}
		
		echo json_encode($obj);
		
	}
	
}