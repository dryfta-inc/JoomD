<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldCkradio extends JFormField {

    protected $type = 'ckradio';

    protected function getInput() {
        // Initialize variables.
        $html = array();
        $icon = $this->element['icon'];
        $suffix = $this->element['suffixck'];

        // Initialize some field attributes.
        $class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';

        // Start the radio field output.
        $html[] = $icon ? '<img src="' . $this->getPathToImages() . '/images/' . $icon . '" style="margin-right:5px;" />' : '<div style="float:left;width:15px;margin-right:5px;">&nbsp;</div>';
        $html[] = '<fieldset id="' . $this->id . '"' . $class . ' style="border-left:1px dotted #333;padding-left:0px;"><div style="width:5px;height:0px;border-bottom:1px dotted #333;"></div>';

        // Get the field options.
        $options = $this->getOptions();

        // Build the radio field output.
        foreach ($options as $i => $option) {

            // Initialize some option attributes.
            $checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
            $class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
            $disabled = !empty($option->disable) ? ' disabled="disabled"' : '';

            // Initialize some JavaScript option attributes.
            $onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

            $html[] = '<div style="clear:both;"><input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' .
                    ' value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"'
                    . $checked . $class . $onclick . $disabled . ' style="margin-left:5px;"/>';

            $html[] = '<label for="' . $this->id . $i . '"' . $class . '>' . JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label></div>';
        }

        // End the radio field output.
        $html[] = '<div style="width:5px;height:0px;border-bottom:1px dotted #333;clear:both"></div></fieldset>';

        return implode($html);
    }

    protected function getPathToImages() {
        $localpath = dirname(__FILE__);
        $rootpath = JPATH_ROOT;
        $httppath = trim(JURI::root(), "/");
        $pathtoimages = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
        return $pathtoimages;
    }

    protected function getOptions() {
        // Initialize variables.
        $options = array();

        foreach ($this->element->children() as $option) {

            // Only add <option /> elements.
            if ($option->getName() != 'option') {
                continue;
            }

            // Create a new option object based on the <option /> element.
            $tmp = JHtml::_('select.option', (string) $option['value'], trim((string) $option), 'value', 'text', ((string) $option['disabled'] == 'true'));

            // Set some option attributes.
            $tmp->class = (string) $option['class'];

            // Set some JavaScript option attributes.
            $tmp->onclick = (string) $option['onclick'];

            // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
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
