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
defined('_JEXEC') or die('Restricted access');
 

class TableApps extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
 
    /**
     * @var string
     */
    
    var $name = null;
	var $label = null;
	var $descr = null;
	var $type = null;
	var $item = null;
	var $published = 1;
	var $ordering = null;
 	var $params  = null;
	var $iscore = null;
    
    function TableApps( &$db ) {
        parent::__construct('#__joomd_apps', 'id', $db);
    }
}
