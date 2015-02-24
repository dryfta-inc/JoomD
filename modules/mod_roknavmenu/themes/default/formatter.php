<?php
/**
 * @version   1.9 February 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


if (!class_exists('RokNavMenuDefaultFormatter')) {
    class RokNavMenuDefaultFormatter extends AbstractJoomlaRokMenuFormatter {
        function format_subnode(&$node) {
            if ($node->getId() == $this->current_node) {
                $node->setCssId('current');
            }
            if (in_array($node->getId(), array_keys($this->active_branch))){
                $node->addListItemClass('active');
            }
        }
    }
}