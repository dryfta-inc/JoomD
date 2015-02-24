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
echo '<!-- NS-DHTML - Newsscroller Self DHTML for Joomla 2.5 by Kubik-Rubik.de - Viktor Vogel -->';
?>
<div class="nsdhtml<?php echo $moduleclass_sfx ?>">
    <div id="marqueecontainer" onmouseover="copyspeed=pausespeed" onmouseout="copyspeed=marqueespeed">
        <div id="vmarquee" class="vmarquee">
            <?php echo $html_content; ?>
        </div>
    </div>
    <?php if($copy) : ?>
        <br />
        <div id="vmarqueesmall">
            <a title="NS-DHTML - Joomla! 2.5 - Kubik-Rubik.de - Viktor Vogel" target="_blank" href="http://joomla-extensions.kubik-rubik.de/">NS-DHTML</a>
        </div>
    <?php endif; ?>
</div>