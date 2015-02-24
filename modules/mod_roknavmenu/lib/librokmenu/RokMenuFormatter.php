<?php
/**
 * @version   1.9 February 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!interface_exists('RokMenuFormatter')) {
    /**
     *
     */
    interface RokMenuFormatter {
        /**
         * @abstract
         * @param  $args
         * @return void
         */
        public function __construct(&$args);

        /**
         * @abstract
         * @param  $menu
         * @return void
         */
        public function format_tree(&$menu);

        /**
         * @abstract
         * @param RokMenuNodeTree $menu
         * @return void
         */
        public function format_menu(&$menu);

        /**
         * @abstract
         * @param RokMenuNode $node
         * @return void
         */
        public function format_subnode(&$node);

        /**
         * @abstract
         * @param  array $active_branch
         * @return void
         */
        public function setActiveBranch(array $active_branch);

        /**
         * @abstract
         * @param  int $current_node
         * @return void
         */
        public function setCurrentNodeId($current_node);
    }
}