<?php
	/*
	 * $JA#COPYRIGHT$
	 */
  // no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
//Upgraded by ThangNN
class JFormFieldlofcolorpicker extends JFormField {
	/*
	 * Category name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$type = 'Lofcolorpicker';
	
	function getInput(){
		
		$uri = $this->getCurrentURL();
		$this->loadjscss( $uri ); 	
		$value = $this->value?$this->value:(string)$this->element['default'];
		$string =  '<input class="color" value="'.$value.'" name="'.$this->name.'" >';
		return $string;	
	}
	
	/**
	 * get current url
	 */
	function getCurrentURL(){
		$uri = str_replace(DS,"/",str_replace( JPATH_SITE, JURI::base (), dirname(__FILE__) ));
		$uri = str_replace("/administrator", "", $uri);
		return $uri;
	}
	
	/**
	 * load css and js file
	 */
	function loadjscss( $uri ){
		if (!defined ('_JA_PARAM_HELPER_RAINBOW_')) {
			define ('_JA_PARAM_HELPER_RAINBOW_', 1);
			JHTML::script($uri."/".'lofcolorpicker/jscolor.js');
		}
	
	} 
}
?>