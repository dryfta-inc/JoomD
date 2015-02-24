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


class Droppable extends Joomdui
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
		
		//Specify droppable region
		$params['accept']				= isset($params['accept'])?$params['accept']:'';
		
		//Specify droppable region
		$params['gallery']				= isset($params['gallery'])?$params['gallery']:'';
		
		//Specify droppable region
		$params['trash']				= isset($params['trash'])?$params['trash']:'';
		
		//specify greedy region
		$params['greedy']		        = isset($params['greedy'])?$params['greedy']:false;
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
				
				$jd( "#'.$id.'" ).droppable({
					
				activeClass: "ui-state-hover"';
							
			   $js .=',hoverClass: "ui-state-active"';
			  		
				if($params['accept'] <> "")
					$js .= ', accept: "#'.$params['accept'].'"';
				
				if($params['greedy'])
				    $js .=',greedy:true';
					
				$js .=',drop: function( event, ui ) {
				        $jd( this )
					    .addClass( "ui-state-highlight" )
					    .find( "p" )
						.html( "Dropped!" );}';
				
        $js .= '});';
		
		$js .= '});';
		
		
		$document->addScriptDeclaration($js);
		
	}
	
}
