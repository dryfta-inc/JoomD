<?php

/*------------------------------------------------------------------------
# mod_joomd_tagcloud - JoomD
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

$doc = JFactory::getDocument();
$doc->addStyleSheet('modules/mod_joomd_tagcloud/style.css');

$typeid = (int)$params->get('typeid', 1);
$fieldid = (int)$params->get('fieldid', 0);

$type = modJoomd_tagcloudHelper::getType($typeid);

if(empty($type))	{
	echo JText::_('TYPENOTFOUND');
	return false;
}

if(!class_exists('JoomdAppField'))
	require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');

$_field = new JoomdAppField();
$_field->setType($typeid);

$field = $_field->getField($fieldid, array('published'=>1));

if(!$field->id)	{
	$obj->items = array();
}
else	{
	$obj = modJoomd_tagcloudHelper::getItems($type, $params);	
}

require(JModuleHelper::getLayoutPath('mod_joomd_tagcloud'));