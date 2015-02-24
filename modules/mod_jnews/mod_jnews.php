<?php
defined('_JEXEC') or die('Access Denied!');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

if(!defined('JNEWS_JPATH_ROOT')){
	if ( defined('JPATH_ROOT') AND class_exists('JFactory') ) {	// joomla 15
		$mainframe = JFactory::getApplication();
		define ('JNEWS_JPATH_ROOT' , JPATH_ROOT );
	}
}

jimport('joomla.filesystem.file');
if ( strtolower( substr( JPATH_ROOT, strlen(JPATH_ROOT)-13 ) ) =='administrator' ) {
	$adminPath = strtolower( substr( JPATH_ROOT, strlen(JPATH_ROOT)-13 ) );
} else {
	$adminPath = JPATH_ROOT;
}
$mainAdminPathDefined = $adminPath . DS.'components'.DS.'com_jnews'.DS.'defines.php';

if ( JFile::exists( $mainAdminPathDefined ) ) {
	require_once( $mainAdminPathDefined );
	
	if ( JFile::exists(JNEWS_JPATH_ROOT . DS.'administrator'.DS.'components'.DS.JNEWS_OPTION.DS.'classes'.DS.'class.jnews.php')) {
		require_once(JNEWS_JPATH_ROOT . DS.'administrator'.DS.'components'.DS.JNEWS_OPTION.DS.'classes'.DS.'class.jnews.php');
	} else {
		die ("jNews Module\n<br />This module needs jNews component.");
	}
	
	$jNewsModule = new jnews_module();
	echo $jNewsModule->normal( $params );
	
}
