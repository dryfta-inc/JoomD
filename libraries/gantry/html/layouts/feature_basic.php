<?php
/**
 * @package   gantry
 * @subpackage html.layouts
 * @version   3.2.20 June 19, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrylayout');

/**
 *
 * @package gantry
 * @subpackage html.layouts
 */
class GantryLayoutFeature_Basic extends GantryLayout {
    var $render_params = array(
        'contents'      =>  null
    );
    function render($params = array()){
        global $gantry;

        $rparams = $this-> _getParams($params);

        $output = '';
        $output .= $rparams->contents;
        return $output;
    }
}