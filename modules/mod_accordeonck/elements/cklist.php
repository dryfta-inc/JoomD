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

class JFormFieldCklist extends JFormField {

    protected $type = 'cklist';

    protected function getInput() {
        // Initialize variables.
        $html = array();
        $attr = '';
        $icon = $this->element['icon'];
        $suffix = $this->element['suffixck'];

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';
        $attr .= 'style="width:105px;border-radius:3px;-moz-border-radius:3px;padding:1px;"';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

        // Get the field options.
        $options = (array) $this->getOptions();

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $this->element['readonly'] == 'true') {
            $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
        }
        // Create a regular list.
        else {
            $html[] = $icon ? '<img src="' .$this->getPathToImages() . '/images/' . $icon . '" style="margin-right:5px;" />' : '<div style="float:left;width:15px;margin-right:5px;">&nbsp;</div>';
            $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
        }

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
            $tmp = JHtml::_('select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', ((string) $option['disabled'] == 'true'));

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
