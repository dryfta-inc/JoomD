<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class JFormFieldCkcolor extends JFormField {

    protected $type = 'ckcolor';

    protected function getInput() {
        $path = 'modules/mod_maximenuck/elements/jscolor/';
        JHTML::_('script', 'jscolor.js', $path);

        $html = '<img src="' . $this->getPathToImages() . '/images/color.png" /><input class="color {';
        $html.= 'required:false,';  // empty possible
        $html.= 'pickerPosition:\'top\',';    // or left / right / top
        $html.= 'pickerBorder:2,pickerInset:3,';    // or right / top
        $html.= 'hash:true';        // # behind value
        $html.= '}" type="text" value="' . $this->value . '" name="' . $this->name . '" style="width:100px;border-radius:3px;-moz-border-radius:3px;" />';
        return $html;
    }

    protected function getPathToImages() {
        $localpath = dirname(__FILE__);
        $rootpath = JPATH_ROOT;
        $httppath = trim(JURI::root(), "/");
        $pathtoimages = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
        return $pathtoimages;
    }

    protected function getLabel() {
        $label = '';
        // Get the label text from the XML element, defaulting to the element name.
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = JText::_($text);

        // Build the class for the label.
        $class = !empty($this->description) ? 'hasTip' : '';

        $label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

        // If a description is specified, use it to build a tooltip.
        if (!empty($this->description)) {
            $label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' .
                            JText::_($this->description), ENT_COMPAT, 'UTF-8') . '"';
        }

        $label .= ' style="min-width:150px;max-width:150px;width:150px;display:block;float:left;padding:1px;">' . $text . '</label>';

        return $label;
    }

}

