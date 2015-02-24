<?php

/*------------------------------------------------------------------------
# mod_joomd_map - Joomd
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

$user = JFactory::getUser();
$doc = JFactory::getDocument();

if(!class_exists('Joomdui'))	{
	$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.1.7.js');
	$doc->addScriptDeclaration('var $jd = jQuery.noConflict();');
}

$doc->addScript('http://maps.google.com/maps/api/js?sensor=true');
$doc->addScript('modules/mod_joomd_map/js/infobox.js');
$doc->addStyleSheet('modules/mod_joomd_map/css/style.css');

require(JModuleHelper::getLayoutPath('mod_joomd_map'));
