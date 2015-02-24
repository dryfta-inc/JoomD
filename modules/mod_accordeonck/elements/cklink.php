<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldCktext extends JFormField
{
    protected $type = 'cktext';

    protected function getInput()
    {
        return '';
    }
	
	protected function getLabel()
    {
		$label = '';
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = JText::_($text);
		$icon = $this->element['icon'];
		
		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		
		$label .= '<div id="'.$this->id.'-link" class="'.$class.'"';
		
		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description)) {
			$label .= ' title="'.htmlspecialchars(trim($text, ':').'::' .
				JText::_($this->description), ENT_COMPAT, 'UTF-8').'"';
		}
		
		$label .= ' style="min-width:150px;max-width:150px;width:150px;display:block;float:left;padding:3px;background:#efefef;">'.$text.'</div>';
		
		return $label;
	}
}


