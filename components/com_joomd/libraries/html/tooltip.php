<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


class Tooltip extends Joomdui
{
	
	var $_id = null;
	
	var $_params = array();
	
	
	/**
	 * Constructor
	 *
	 * @param int useCookies, if set to 1 cookie will hold last used tab between page refreshes
	 */
	function __construct()
	{

	}
	
	function initialize( $id, $params = array() )
	{
		
		$this->_id = $id;
		
		//To display the tooltip from other source except title pass the attribute of the element
		$params['attr']			= isset($params['attr'])?$params['attr']:false;
		
		//set the time delay in miliseconds
		$params['delay']		= isset($params['delay'])?$params['delay']:0;
		
		//set the keyword to differentiate the title and body
		$params['showBody']		= isset($params['showBody'])?$params['showBody']:false;
		
		//fade effect time in mili seconds
		$params['fade']			= isset($params['fade'])?$params['fade']:false;
		
		//left property in pixels
		$params['left']			= isset($params['left'])?$params['left']:0;
		
		//top property in pixels
		$params['top']			= isset($params['top'])?$params['top']:0;
		
		//add extra class in tooltip
		$params['extraClass']	= isset($params['extraClass'])?$params['extraClass']:false;
		
		//align the tooltip in reverse direction
		$params['positionLeft']	= isset($params['positionLeft'])?$params['positionLeft']:false;
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
				
			$jd("'.$id.'").tooltip({';
					
			
			$js .= 'delay:'.$params['delay'];
			
			if($this->_params['attr'])
				$js .= ', bodyHandler: function() {
							return $jd($jd(this).attr("'.$this->_params['attr'].'")).html();
						}';
			
			if($this->_params['showBody'])
				$js .= ', showBody: "'.$params['showBody'].'"';
			
			if($this->_params['fade'])
				$js .= ', fade: '.$params['fade'];
				
			if($this->_params['left'])
				$js .= ', left: '.$params['left'];
				
			if($this->_params['top'])
				$js .= ', top: '.$params['top'];
				
			if($this->_params['extraClass'])
				$js .= ', extraClass: "'.$params['extraClass'].'"';
				
			if($this->_params['positionLeft'])
				$js .= ', top: true';
		
						
		$js .= '});
			
			});';
		
		$document->addScriptDeclaration($js);
		
	}
	
}
