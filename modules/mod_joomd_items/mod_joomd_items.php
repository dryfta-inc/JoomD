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

$typeid = (int)$params->get('typeid', 1);

$type = modJoomd_itemsHelper::getType($typeid);

if(empty($type))	{
	$items = array();
}
else	{
	$items = modJoomd_itemsHelper::getItems($type, $params);

	$fieldids = (array)$params->get('fields', array());
	
	if(!class_exists('JoomdAppField'))
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
	
	$_field = new JoomdAppField();
	$_field->setType($typeid);
	
	$fields = $_field->getFields(array('ids'=>$fieldids));
		
	if(count($fields) < 1)
		$fields = array($_field->get_firstfield(array('published'=>1)));
	
}

require(JModuleHelper::getLayoutPath('mod_joomd_items'));