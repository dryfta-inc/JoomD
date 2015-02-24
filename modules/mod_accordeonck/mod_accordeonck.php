<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * Adapted from the original mod_menu on Joomla.site - Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . DS . 'helper.php');


$list = ModaccordeonckHelper::getMenu($params);
if (!$list) return false;

// retrieve parameters from the module
$startlevel = $params->get('startLevel', '0');
$endlevel = $params->get('endLevel', '10');
$menuID = $params->get('tag_id', 'accordeonck' . (int) (microtime() * 100000));
$mooduration = $params->get('mooduration', 500);
$mootransition = $params->get('mootransition', 'linear');
$imageplus = $params->get('imageplus', 'modules/mod_accordeonck/assets/plus.png');
$imageminus = $params->get('imageminus', 'modules/mod_accordeonck/assets/minus.png');
$imageposition = $params->get('imageposition', 'right');
$eventtype = $params->get('eventtype', 'click');
$fadetransition = $params->get('fadetransition', 'false');

// laod the css and js in the page	
JHTML::_("behavior.framework", true);	
$document = JFactory::getDocument();
$document->addScript(JURI::base() . 'modules/mod_accordeonck/assets/mod_accordeonck.js');

if ($params->get('usestyles') == 1) {
    $document->addStylesheet(JURI::base() . 'modules/mod_accordeonck/assets/mod_accordeonck_css.php?cssid='.$menuID);
	$css = "#" . $menuID . " li a.toggler { background: url(" . JURI::root() . $imageplus . ") " . $imageposition . " center no-repeat !important; }
	#" . $menuID . " li a.toggler.open { background: url(" . JURI::root() . $imageminus . ") " . $imageposition . " center no-repeat !important; }
	#" . $menuID . " li ul li ul li ul { border:none !important; padding-top:0px !important; padding-bottom:0px !important; }";
	$document->addStyleDeclaration($css);
}

$js = "window.addEvent('domready', function() {new accordeonMenuCK(document.getElement('#" . $menuID . "'),{"
        . "fadetransition : " . $fadetransition . ","
		. "eventtype : '" . $eventtype . "',"
		. "mooTransition : '" . $mootransition . "',"
        . "menuID : '" . $menuID . "',"
		. "imagePlus : '" . JURI::root() . $imageplus . "',"
		. "imageMinus : '" . JURI::root() . $imageminus . "',"
        . "mooDuree : " . $mooduration . "});"
        . "});";

$document->addScriptDeclaration($js);

$list = ModaccordeonckHelper::getMenu($params);
$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path = isset($active) ? $active->tree : array();
$showAll = 1;
$class_sfx = htmlspecialchars($params->get('class_sfx'));

if (count($list)) {
    require JModuleHelper::getLayoutPath('mod_accordeonck', $params->get('layout', 'default'));
}