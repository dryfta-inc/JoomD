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


class Dialog extends Joomdui
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
		
		//make the dialog box draggable
		$params['draggable'] = isset($params['draggable'])?$params['draggable']:false;
		
		//make the dialog box resizable
		$params['resizable'] = isset($params['resizable'])?$params['resizable']:false;
		
		//enables you to close the box on press of esc key
		$params['closeOnEscape'] = isset($params['closeOnEscape'])?$params['closeOnEscape']:false;
		
		//enables you to set the position of the box eg. 'center', 'left', 'right', 'top', 'bottom' and an array as for top and right values [350,100]
		$params['position'] = isset($params['position'])?$params['position']:false;
		
		$params['buttonId'] = isset($params['buttonId'])?$params['buttonId']:'';
		
		//Selector for the modal element.
		$params['modal'] = isset($params['modal'])?$params['modal']:false;
		
		//Selector for the buttons element.
		$params['buttons'] = isset($params['buttons'])?$params['buttons']:array();
		
		//Selector for the height element.
		$params['height'] = isset($params['height'])?$params['height']:140;
		//Selector for the height element.
		$params['width'] = isset($params['width'])?$params['width']:300;
	
		//Selector for the autoOpen element.
		$params['autoOpen'] = isset($params['autoOpen'])?$params['autoOpen']:false;
		
		//Selector for the show element.
		$params['show'] = isset($params['show'])?$params['show']:'';
		
		//Selector for the hide element.
		$params['hide'] = isset($params['hide'])?$params['hide']:'';
		
		//open event
		$params['open'] = isset($params['open'])?$params['open']:false;
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
				
		$jd( "'.$id.'" )
			.dialog({';
			
			if($params['draggable'])	{
				$js .= 'draggable: true';
			}
			else
				$js .= 'draggable: false';
				
			if($params['resizable'])	{
				$js .= ', resizable: true';
			}
			else
				$js .= ', resizable: false';
				
			$js .= ', width:'.$params['width'];
			$js .= ', height:'.$params['height'];
				
			if($params['closeOnEscape'])
				$js .= ', closeOnEscape: true';
			else
				$js .= ', closeOnEscape: false';
			
			if($params['position'] <> "")	{
				if(is_array($params['position']))
					$js .= ', position:['.implode(',', $params['position']).']';
				else
					$js .= ', position:"'.$params['position'].'"';
			}				
			
			if($params['modal'])
				   	$js .= ',modal:true';
					
			if( $params['buttonId']=='' or $params['autoOpen'] )
				   	$js .= ',autoOpen:true';
			else
					$js .= ',autoOpen:false';
					
			if($params['show'] <> "")	{
			   	loader::effect($params['show']);
				$js .= ',show:"'.$params['show'].'"';
			}
			
			if($params['hide'] <> "")	{
				loader::effect($params['hide']);
				$js .= ',hide:"'.$params['hide'].'"';		
			}
			
			if(count($params['buttons'])){
				
				$i=1;
				$js .= ', buttons: {';
				
				foreach($params['buttons'] as $k=>$v)	{
				
					$js .= '"'.$k.'" : function() {
						'.$v.'(this);'.'
					}';
					
					if($i<>count($params['buttons'])){$js .=',';}
					$i++;
						
				}
					
				$js .= '}';
				
			}
			
			if($params['open'])	{
				
				$js .= ', open: function(event, ui) {
					
					if(typeof('.$params['open'].') == "function")
						'.$params['open'].'(event, ui);
					}';
				
			}
				
		$js .= '});';
		
		if($params['buttonId']<>''){
			$js .='$jd( "'.$params['buttonId'].'" ).click(function() {
				$jd( "'.$id.'" ).dialog( "open" );
				return false;
			});';
		}

		
		$js .= '});';
		
		$document->addScriptDeclaration($js);
		
	}
	
}
