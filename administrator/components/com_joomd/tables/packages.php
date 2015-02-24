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
 

class TablePackages extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
    var $name = null;
	var $amount = 0;
	var $period = 0;
	var $unit = null;
	var $items = 0;
	var $published = 1;
	var $ordering = null;
	var $created = null;
	var $created_by = null;
	var $params = null;
    
    function TablePackages( &$db ) {
        parent::__construct('#__joomd_package', 'id', $db);
    }
}
