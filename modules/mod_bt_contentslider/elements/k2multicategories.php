<?php
/**
 * @package 	mod_bt_contentslider - BT ContentSlider Module
 * @version		1.1
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2011 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
jimport('joomla.form.formfield');
class JFormFieldK2Multicategories extends JFormFieldList {
	protected $type = 'K2Multicategories'; //the form field type
    var $options = array();
    protected function getInput() {
		// Initialize variables.
		$html = array();
		$attr = '';
		
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
	
	// Get the field options.
		$options = (array) $this->getOptions();
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}
		
		return implode($html);
	}
    protected function getOptions() {
        // Initialize variables.
        $session = JFactory::getSession();
  
        $db = JFactory::getDBO();
        // generating query
		$db->setQuery("SELECT c.name AS name, c.id AS id, c.parent AS parent FROM #__k2_categories AS c WHERE published = 1 AND trash = 0 ORDER BY c.name, c.parent ASC");
 		// getting results
   		$results = $db->loadObjectList();
   		
		if(count($results)){
  	     	// iterating
			$temp_options = array();
			
			foreach ($results as $item) {
				array_push($temp_options, array($item->id, $item->name, $item->parent));	
			}
			foreach ($temp_options as $option) {
        		if($option[2] == 0) {
        	    	$this->options[] = JHtml::_('select.option', $option[0], $option[1]);
        	    	$this->recursive_options($temp_options, 1, $option[0]);
        	    }
        	}		
            return $this->options;
		} else {	
            return $this->options;
		}
	}
 	// bind function to save
    function bind( $array, $ignore = '' ) {
        if (key_exists( 'field-name', $array ) && is_array( $array['field-name'] )) {
        	$array['field-name'] = implode( ',', $array['field-name'] );
        }
        
        return parent::bind( $array, $ignore );
    }
    function recursive_options($temp_options, $level, $parent){
		foreach ($temp_options as $option) {
      		if($option[2] == $parent) {
		  		$level_string = '';
		  		for($i = 0; $i < $level; $i++) $level_string .= '- - ';
        	    $this->options[] = JHtml::_('select.option',  $option[0], $level_string . $option[1]);
       	    	$this->recursive_options($temp_options, $level+1, $option[0]);
			}
       	}
    }
}
