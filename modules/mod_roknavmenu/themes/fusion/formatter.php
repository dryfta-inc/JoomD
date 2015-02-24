<?php
/**
 * @version   1.9 February 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 *
 */
class RokNavMenuFusionFormatter extends AbstractJoomlaRokMenuFormatter {
	function format_subnode(&$node) {
	    // Format the current node
		
		if ($node->getType() == 'menuitem' or $node->getType() == 'separator') {
		    if ($node->hasChildren() ) {
    			$node->addLinkClass("daddy");
    		}     		
    		$node->addLinkClass("item");
		}
		if ($node->getLevel() == "0") {
			$node->addListItemClass("root");
		}
	}
}