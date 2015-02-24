<?php
/**
 * @version   3.2.20 June 19, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformgroup');


class GantryFormGroupColumns extends GantryFormGroup
{
    protected $type = 'columns';
    protected $baseetype = 'group';

    public function getInput(){
        $buffer = '';
		
		$class = $this->element['class'];
		$name = $this->id;
		
		$buffer .= "<div class=\"wrapper ".$class."\">\n";
		
		// Columns
		$leftOpen = "<div class='group-left'>\n";
		$rightOpen = "<div class='group-right'>\n";
		$noneOpen = "<div class='group-none'>\n";
		
		$divClose = "</div>\n";
		
        foreach ($this->fields as $field) {

			$position = ($field->element['position']) ? (string) $field->element['position'] : 'none';
			$position .= "Open";
			$bufferItem = "";

			$fieldName = $this->fieldname."-".$field->element['name'];
			
			$bufferItem .= "<div class=\"group ".$fieldName." group-".$field->type."\">\n";
            if ($field->show_label) $bufferItem .= "<span class=\"group-label\">".$field->getLabel()."</span>\n";
            $bufferItem .= $field->getInput();
            $bufferItem .= "</div>\n";
			
			$$position .= $bufferItem;
        }

		$buffer .= $leftOpen . $divClose . $rightOpen . $divClose . $noneOpen . $divClose;
		$buffer .= "</div>\n";

        return $buffer;

    }
}