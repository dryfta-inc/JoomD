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


class Draggable extends Joomdui
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
		
		//which axis to move
		$params['axis']				= isset($params['axis'])?$params['axis']:'';
		
		//Constrains dragging to within the bounds of the specified element or region. Possible string values: 'parent', 'document', 'window', [x1, y1, x2, y2].
		$params['containment']		= isset($params['containment'])?$params['containment']:'';
		
		//enable the browser window to scroll
		$params['scroll']			= isset($params['scroll'])?$params['scroll']:true;
		$params['scrollSensitivity']= isset($params['scrollSensitivity'])?$params['scrollSensitivity']:'100';
		$params['scrollSpeed']		= isset($params['scrollSpeed'])?$params['scrollSpeed']:'10';
		
		//minimum distance to start dragging
		$params['distance']			= isset($params['distance'])?$params['distance']:0;
		
		//delay to start dragging
		$params['delay']			= isset($params['delay'])?$params['delay']:0;
		
		//If set to a selector or to true (equivalent to '.ui-draggable'), the draggable will snap to the edges of the selected elements when near an edge of the element.
		$params['snap']				= isset($params['snap'])?$params['snap']:false;
		
		//Determines which edges of snap elements the draggable will snap to. Ignored if snap is false. Possible values: 'inner', 'outer', 'both'
		$params['snapMode']			= isset($params['snapMode'])?$params['snapMode']:'';
		
		//define the x or y axis grid at which the draggable item drags
		$params['grid']				= isset($params['grid'])?$params['grid']:'';
		
		//If set to true, the element will return to its start position when dragging stops. Possible string values: 'valid', 'invalid'. If set to invalid, revert will only occur if the draggable has not been dropped on a droppable. For valid, it's the other way around.
		$params['revert']			= isset($params['revert'])?$params['revert']:false;
		
		//Allows for a helper element to be used for dragging display. Possible values: 'original', 'clone', Function. If a function is specified, it must return a DOMElement.
		$params['helper']			= isset($params['helper'])?$params['helper']:'';
		
		//If helper is custom then pass the text in this
		$params['helpertext']		= isset($params['helpertext'])?$params['helpertext']:'';
		
		//Sets the offset of the dragging helper relative to the mouse cursor. Coordinates can be given as a hash using a combination of one or two keys: { top, left, right, bottom }.
		$params['cursorAt']			= isset($params['cursorAt'])?$params['cursorAt']:false;
		
		//The css cursor during the drag operation.
		$params['cursor']			= isset($params['cursor'])?$params['cursor']:'';
		
		//top margin of cursor
		$params['top']				= isset($params['top'])?$params['top']:false;
		
		//bottom margin of cursor
		$params['bottom']			= isset($params['bottom'])?$params['bottom']:false;
		
		//left margin of cursor
		$params['left']				= isset($params['left'])?$params['left']:false;
		
		//If specified, restricts drag start click to the specified element(s).
		$params['handle']			= isset($params['handle'])?$params['handle']:'';
		
		//Prevents dragging from starting on specified elements.
		$params['cancel']			= isset($params['cancel'])?$params['cancel']:'';
		
		//Opacity for the helper while being dragged.
		$params['opacity']			= isset($params['opacity'])?$params['opacity']:1;
		
		//This event is triggered when dragging starts.
		$params['start']			= isset($params['start'])?$params['start']:'';
		
		//This event is triggered when the mouse is moved during the dragging.
		$params['drag']				= isset($params['drag'])?$params['drag']:'';
		
		//This event is triggered when dragging stops.
		$params['stop']				= isset($params['stop'])?$params['stop']:'';
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
				
				$jd( "#'.$id.'" ).draggable({
					
					opacity: "'.$params['opacity'].'"';
					
				if($params['axis'] <> "")
					$js .= ', axis: "'.$params['axis'].'"';
					
				if(!$params['scroll'])
					$js .= ', scroll: false';
				else
					$js .= ', scrollSpeed: ' . $params['scrollSpeed'].'
					, scrollSensitivity: '.$params['scrollSensitivity'];
				
				if($params['containment'] <> "")
					$js .= ', containment: "'.$params['containment'].'"';
				
				if($params['distance'])
					$js .= ', distance: '.$params['distance'];
								
				if($params['delay'])
					$js .= ', delay: '.$params['delay'];
					
				if($params['snap'])	{
					if($params['snap'] === true)
						$js .= ', snap: true';
					else	{
						$js .= ', snap: "'.$params['snap'].'"';
						if($params['snapMode'] <> "")
							$js .= ', snapMode: "'.$params['snapMode'].'"';
					}
				}
				
				if($params['grid'] <> "")
					$js .= ', grid: ['.$params['grid'].']';
					
				if($params['revert'])
					$js .= ', revert: true';
					
				if($params['helper'] <> "")	{
					if($params['helper'] == 'custom')	{
						$js .= ', helper: function( event ) {
							return $( "'.$params['helpertext'].'" );
						}';
					}
					else
						$js .= ', helper: "'.$params['helper'].'"';
				}
				
				if($params['cursorAt'])	{
					
					$js .= ', cursorAt: {';
					
					$js .= 'cursor: "'.$params['cursor'].'"';

					if($params['top'] !== false)
						$js .= ', top: '.$params['top'];
					if($params['left'] !== false)
						$js .= ', left: '.$params['left'];
					if($params['bottom'] !== false)
						$js .= ', bottom: '.$params['bottom'];
					$js .= '}';
					
				}
				
				if($params['handle'] <> "")
					$js .= ', handle: "'.$params['handle'].'"';
					
				if($params['cancel'] <> "")
					$js .= ', cancel: "'.$params['cancel'].'"';
					
				if($params['start'] <> "")
					$js .= ', start: function(){'.$params['start'].'}';
					
				if($params['drag'] <> "")
					$js .= ', drag: function(){'.$params['drag'].'}';
					
				if($params['stop'] <> "")
					$js .= ', stop: function(){'.$params['stop'].'}';
						
		$js .= '});';
		
		$js .= '$jd( "#'.$id.'" ).disableSelection();';
			
		$js .= '});';
				
		$document->addScriptDeclaration($js);
		
	}
	
}
