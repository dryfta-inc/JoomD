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
 
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the Library
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_joomd' . DS . 'libraries' . DS . 'core.php' );

// Initialize the library
$jdapp = Joomd::getApp();

$jdapp->initialize();

$controller = JRequest::getWord('view', 'joomd');

// Require the base controller
 
require_once( JPATH_SITE.'/components/com_joomd/controller.php' );
 
// Require specific controller if requested
if($controller) {
    $path = JPATH_SITE.'/components/com_joomd/controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$classname    = 'JoomdController'.$controller;
$controller   = new $classname();
 
// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );
 
// Redirect if set by the controller
$controller->redirect();

//to display the buttons etc
$controller->afterdisplay();

//finalize rendering the content
$jdapp->finalize();