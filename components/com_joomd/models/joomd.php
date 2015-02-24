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
 
jimport( 'joomla.application.component.model' );
 
class JoomdModelJoomd extends JModel
{
		
	function __construct()
	{
		parent::__construct();
		
		$this->_user =  Joomd::getUser();
		
	}
	
	function getBlocks()
	{
		
		$apps = Joomd::getApps();
		
		$blocks = array();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app->name.'/app_'.$app->name.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app->name.'/app_'.$app->name.'.php');
					
				$class = "JoomdApp".ucfirst($app->name);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'front_display'))	{
						
						$html = $class->front_display();
						
						if(!empty($html))	{
						
							$block = new stdClass();
							
							$block->cssclass = 'block_app'.$app->id;
							$block->html = $html;
							
							array_push($blocks, $block);
						
						}
						
					}
				
				}
				
			}
		
		}
		
		return $blocks;
		
	}
	
}
