<?php
/**
 * @Copyright
 *
 * @package     Newsscroller Self DHTML for Joomla 2.5
 * @author      Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 * @version     Version: 2.5-1 - 02-Feb-2012
 * @link        Project Site {@link http://joomla-extensions.kubik-rubik.de/ns-newsscroller-self-dhtml}
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

$copy = $params->get('copy', 1);
$moduleclass_sfx = $params->get('moduleclass_sfx', '');

// Create output
$html_content = NewsscrollerDhtmlHelper::scrollContent($params);

// Load CSS in the head
NewsscrollerDhtmlHelper::loadHeadData($params);

require(JModuleHelper::getLayoutPath('mod_newsscroller_self_dhtml', 'default'));

// Load JavaScript code
NewsscrollerDhtmlHelper::javascript($params);