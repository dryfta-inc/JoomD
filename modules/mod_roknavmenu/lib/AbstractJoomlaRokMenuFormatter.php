<?php
/**
 * @version   1.9 February 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

abstract class AbstractJoomlaRokMenuFormatter extends AbstractRokMenuFormatter {
    protected function _format_subnodes(&$node) {
        parent::_format_subnodes($node);
		//See if the the roknavmenudisplay plugins want to play
		JPluginHelper::importPlugin('roknavmenu');
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onRokNavMenuModifyLink', array (&$node, $this->args));
    }
}
