<?php

/*------------------------------------------------------------------------
# mod_joomd_search - JoomD
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
require_once( JPATH_ROOT . '/components/com_joomd/libraries/apps/field/app_field.php' );

// Initialize the library
$jdapp = new JoomdApp();

$jdapp->initialize();

$user = JFactory::getUser();

$typeid = (int)$params->def('typeid', 1);

$type = modJoomd_searchHelper::getType($typeid);

if(!empty($type))	{

$cat = $params->def('cat', 1);

$field = new JoomdAppField();
$field->setType($typeid);

$multiselect = Joomdui::getMultiselect();

if($cat)	{
	$multiselect->initialize('form[name=\'searchform\'] select#cats', array('filter'=>true, 'height'=>'200', 'header'=>true, 'multiple'=>true));
	$cats = modJoomd_searchHelper::getCats($typeid);
}

$fields = $field->getFields(array('published'=>1, 'search'=>1, 'access'=>$user->aid));

require(JModuleHelper::getLayoutPath('mod_joomd_search'));

}

else
	echo JText::_('TYPENOTFOUND');