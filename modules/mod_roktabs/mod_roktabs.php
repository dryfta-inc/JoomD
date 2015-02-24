<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__) . DS . 'helper.php');

JHTML::_('behavior.mootools');

$conf =& JFactory::getConfig();
if ($conf->getValue('config.caching') && $params->get("module_cache", 0)) {
    $cache =& JFactory::getCache('mod_roktabs');
    $list  = $cache->call(array('modRokTabsHelper', 'getList'), $params);
} else {
    $list = modRokTabsHelper::getList($params);
}

require(JModuleHelper::getLayoutPath('mod_roktabs'));