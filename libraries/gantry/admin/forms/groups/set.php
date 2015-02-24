<?php
/**
 * @version   3.2.20 June 19, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformgroup');


class GantryFormGroupSet extends GantryFormGroup
{
    protected $type = 'set';
    protected $baseetype = 'group';
    protected $hidden = true;

    public function getInput(){
        return '';
    }
}