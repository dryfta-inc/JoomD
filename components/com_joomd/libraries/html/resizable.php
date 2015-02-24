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


class Resizable extends Joomdui
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
		//Enables animation effect.
		$params['animate']		= isset($params['animate'])?$params['animate']:false;
		//Fixes ratio of Height and Width
		$params['aspectRatio']	= isset($params['aspectRatio'])?$params['aspectRatio']:'';
		$params['containment']	= isset($params['containment'])?$params['containment']:'';
		$params['delay']		= isset($params['delay'])?$params['delay']:'';
		$params['distance']	    = isset($params['distance'])?$params['distance']:'';
		$params['helper']	    = isset($params['helper'])?$params['helper']:'';
		//Fixes maximum height 
		$params['maxHeight']	= isset($params['maxHeight'])?$params['maxHeight']:'';
		//Fixes maximum width 
		$params['maxWidth']		= isset($params['maxWidth'])?$params['maxWidth']:'';
		//Fixes minimum height 
		$params['minHeight']	= isset($params['minHeight'])?$params['minHeight']:'10';
		//Fixes minimum width 
		$params['minWidth']	    = isset($params['minWidth'])?$params['minWidth']:'';
		$params['grid']			= isset($params['grid'])?$params['grid']:'';
		$params['alsoResize']	= isset($params['alsoResize'])?$params['alsoResize']:'';
		$params['handles']		= isset($params['handles'])?$params['handles']:'';
		//Enables ghost effect
		$params['ghost']		= isset($params['ghost'])?$params['ghost']:false;

		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
				
				$jd( "#'.$id.'" ).resizable({
				
				minHeight:'.$params['minHeight'].'';				
				
				if($params['aspectRatio'] <> "")
					$js .= ',aspectRatio: "'.$params['aspectRatio'].'"';	
						
				if($params['containment'] <> "")
				   $js .= ', containment: "'.$params['containment'].'"';
			
				if($params['delay'] <> "")	
				   $js .= ', delay: "'.$params['delay'].'"';
				
				if($params['distance'] <> "")	
				   $js .= ', distance: "'.$params['distance'].'"';
				
				if($params['helper'] <> "")	
				   $js .= ', helper:"ui-resizable-helper"';
				
			    if($params['maxHeight'] <> "")	
				   $js .= ', maxHeight: "'.$params['maxHeight'].'"';
								
				if($params['maxWidth'] <> "")	
				   $js .= ', maxWidth: "'.$params['maxWidth'].'"';
						
			    if($params['minWidth'] <> "")	
				   $js .= ', minWidth: "'.$params['minWidth'].'"';
				
				if($params['grid'] <> "")
					$js .= ', grid: '.$params['grid'].'';
				
				if($params['alsoResize'] <> "")
					$js .= ', alsoResize: "#'.$params['alsoResize'].'"';
					
				if($params['animate'])
					$js .= ', animate: "'.$params['animate'].'"';
				
				if($params['ghost'])
					$js .= ', ghost:true';
				
		$js .= '});';
		
		if($params['alsoResize']<>"")
		$js .='$jd( "#'.$params['alsoResize'].'" ).resizable();';
		
			
		$js .='});';
		
		$document->addScriptDeclaration($js);
		
	}
	
}
