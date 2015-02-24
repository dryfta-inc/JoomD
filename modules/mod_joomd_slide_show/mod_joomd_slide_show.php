<?php

/*------------------------------------------------------------------------
# mod_joomd_items - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

// Require the Library
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_joomd' . DS . 'libraries' . DS . 'core.php' );

// Initialize the library
$jdapp = Joomd::getApp();

$jdapp->initialize();
	
$doc = JFactory::getDocument();

$doc->addScript(JURI::root().'modules/mod_joomd_slide_show/js_css/sliderman.1.3.7.js');
$doc->addStyleSheet(JURI::root().'modules/mod_joomd_slide_show/js_css/sliderman.css');


$typeid = (int)$params->get('typeid', 1);

$type = modJoomd_slide_showHelper::getType($typeid);

if(empty($type))	{
	$items = array();
}
else	{
	$items = modJoomd_slide_showHelper::getItems($type, $params);
 
 
	$titlefield = $params->get('titlefield', 0);
	$imagefield = $params->get('imagefield', 0);
	$descrfield = $params->get('descrfield', 0);

	if(!class_exists('JoomdAppField'))
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
	
	$_field = new JoomdAppField();
	$_field->setType($type->id);
	
	$titlefield = $_field->getField($titlefield);
	$imagefield = $_field->getField($imagefield);
	$descrfield = $_field->getField($descrfield);
	
}


require(JModuleHelper::getLayoutPath('mod_joomd_slide_show'));