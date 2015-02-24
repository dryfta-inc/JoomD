<?php
/**
 * @version   3.2.20 June 19, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformgroup');


class GantryFormGroupPosition extends GantryFormGroup
{
    protected $type = 'position';
    protected $baseetype = 'group';

    public function getInput(){
        global $gantry;
        $clean_name=(string)$this->element['name'];
        $position_info =  $gantry->getPositionInfo($clean_name);


        $buffer = '';

		$buffer .= "<div class='wrapper'>\n";
        foreach ($this->fields as $field) {

            if (!empty($position_info) && array_key_exists('position_info',get_object_vars($field)))
                $field->position_info = $position_info;

            $itemName = $this->fieldname."-".$field->fieldname;
            
            $buffer .= '<div class="chain '.$itemName.' chain-'.strtolower($field->type).'">'."\n";
            $buffer .= '<span class="chain-label">'.JText::_($field->getLabel()).'</span>'."\n";
            $buffer .= $field->getInput();
            $buffer .= "</div>"."\n";

        }
		$buffer .= "</div>"."\n";

        return $buffer;
    }
}