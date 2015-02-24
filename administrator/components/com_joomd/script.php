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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of JoomD component
 */
class com_joomdInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		
		$db =  JFactory::getDBO();

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		jfolder::move(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'images', JPATH_SITE.DS.'images'.DS.'joomd');
		
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		
		$db =  JFactory::getDBO();

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		if(jfolder::exists(JPATH_SITE.DS.'images'.DS.'joomd'))
			jfolder::delete(JPATH_SITE.DS.'images'.DS.'joomd');
		
		$query = 'select * from #__joomd_types';
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		for($i=0;$i<count($items);$i++)	{
			
			$query = 'drop table if exists #__joomd_type'.$items[$i]->id;
			$db->setQuery( $query );
			if(!$db->query())
				echo $db->getErrorMsg();
			
		}
			
		$query = 'delete from #__extensions where type = "plugin" and element = "joomdcaptcha" and folder = "system"';
		$db->setQuery( $query );
		$db->query();
		
		if(jfolder::exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'joomdcaptcha'))
			jfolder::delete(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'joomdcaptcha');
		
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		
	}
}
