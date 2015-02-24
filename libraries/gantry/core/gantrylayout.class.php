<?php
/**
 * @package   gantry
 * @subpackage core
 * @version   3.2.20 June 19, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('GANTRY_VERSION') or die();

/**
 * Base class for all Gantry custom features.
 *
 * @package gantry
 * @subpackage core
 */
class GantryLayout {
    var $render_params = array();

    function render($params = array()){
        global $gantry;
        ob_start();
        return ob_get_clean();
    }

    function _getParams($params = array()){
        $ret = new stdClass();
        $ret_array = array_merge($this->render_params, $params);
        foreach($ret_array as $param_name => $param_value){
            $ret->$param_name = $param_value;
        }
        return $ret;
    }
}