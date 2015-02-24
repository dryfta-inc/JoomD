<?php

/*------------------------------------------------------------------------
# mod_joomd_newsletter - JoomD
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

if(!class_exists('Loader'))
	require_once(JPATH_SITE.'/components/com_joomd/libraries/loader.php');
	
if(!class_exists('Joomdui'))
	require_once(JPATH_SITE.'/components/com_joomd/libraries/joomdui.php');

if(!class_exists('JoomdAppField'))
	require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');

$loader = new Loader();
$loader->loadjs();
$loader->loadcss();

$multiselect = Joomdui::getMultiselect();

$typeid = (int)$params->get('typeid', 1);

$type = modJoomd_newsletterHelper::getType($typeid);

if(empty($type))	{
	echo JText::_('NOCATEGORYFOUND');
}
else	{
	$items = modJoomd_newsletterHelper::getItems($type, $params);
	require(JModuleHelper::getLayoutPath('mod_joomd_newsletter'));
}