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
defined('_JEXEC') or die('Restricted access');

$items = Joomd::getApps();

for($i=0;$i<count($items);$i++)	{
	
	if(!empty($items[$i]->name) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php'))	{
				
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php');
			
		$class = "JoomdApp".ucfirst($items[$i]->name);
		
		if(class_exists($class))	{
		
			$class = new $class;
			
			if(method_exists($class, 'add_submenu'))	{
				
				$class->add_submenu();
				
			}
		
		}
		
	}	
	
}

JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('TYPE_ADD_TEXT').'">add</span>' , 'index.php?option=com_joomd&view=type&layout=form' );

?>