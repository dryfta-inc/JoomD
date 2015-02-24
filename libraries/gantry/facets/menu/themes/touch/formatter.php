<?php
/**
 * @package   Gantry Template - RocketTheme
 * @version   3.2.20 June 19, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined('GANTRY_VERSION') or die('Restricted access');

gantry_import('facets.menu.gantrymenuformatter');

/*
 * Created on Jan 16, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class GantryMenuFormatterTouch extends GantryMenuFormatter {
	function format(&$node, &$menu_params) {
	    // Format the current node

		if ($node->type == 'menuitem' or $node->type == 'separator') {
		    if ($node->hasChildren() ) {
    			$node->addLinkClass("daddy");
    		}  else {
    		    $node->addLinkClass("orphan");
    		}

    		$node->addLinkClass("item");

		}
		if ($node->level == 1) {
		$node->addListItemClass("root");

		}

	}

}
