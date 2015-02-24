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


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.'/components/com_joomd/libraries/app.php');

class JoomdAppNewsletter extends JoomdApp	{
	
	function __construct()
	{
				
		parent::__construct();
		
		$this->initialize();
		
	}
	
	function initialize()
	{
		
		static $init = false;
		
		if($init)
			return;
				
		$this->loadLanguage();
				
		$init = true;
		
	}
	
	function loadLanguage()
	{
		
		static $loaded = false;
		
		if($loaded)
			return true;
		
		$lang = JFactory::getLanguage();
		
		$lang->load('app_newsletter', JDPATH_BASE);
		
		$loaded = true;
		
		return true;
		
	}
	
	function add_submenu()
	{
		$view = JRequest::getCmd('view', '');
		
		$active = $view == 'newsletter';
	
		JSubMenuHelper::addEntry( '<span class="hasTip" title="'.JText::_('SUBMENU_NEWSLETTER_DESCR').'">'.JText::_('NEWSLETTER').'</span>' , 'index.php?option=com_joomd&view=newsletter' , $active );
		
	}
	
}