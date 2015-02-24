<?php
/**
 * @version   1.9 February 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!interface_exists('RokMenuTheme')) {

    /**
     *
     */
    interface RokMenuTheme {

        /**
         * @abstract
         * @return array
         */
        public function getDefaults();

        /**
         * @abstract
         * @param  $args array
         * @return RokMenuFormatter
         */
        public function getFormatter($args);

        /**
         * @abstract
         * @param  $args
         * @return RokMenuLayout
         */
        public function getLayout($args);

    }
}
