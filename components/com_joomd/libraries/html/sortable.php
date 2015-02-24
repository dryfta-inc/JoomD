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


class Sortable extends Joomdui
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

	
	function initialize($id, $params = array())
	{
		
		$this->_id = $id;
		
		//Disables (true) or enables (false) the sortable. Can be set when initialising (first creating) the sortable.
		$params['disabled'] = isset($params['disabled'])?$params['disabled']:false;
		
		//Send the sorted array in this id in Ajax request.
		$params['id'] = isset($params['id'])?$params['id']:'ordering';
		
		//other sortable element to connect to this one-way
		$params['connectWith'] = isset($params['connectWith'])?$params['connectWith']:'';
		
		//Constrains dragging to within the bounds of the specified element - can be a DOM element, 'parent', 'document', 'window', or a jQuery selector. 
		$params['containment'] = isset($params['containment'])?$params['containment']:'';
		
		//Time in milliseconds to define when the sorting should start.
		$params['delay'] = isset($params['delay'])?$params['delay']:0;
		
		//Tolerance, in pixels, for when sorting should start.
		$params['distance'] = isset($params['distance'])?$params['distance']:0;
		
		//If false items from this sortable can't be dropped to an empty linked sortable.
		$params['dropOnEmpty'] = isset($params['dropOnEmpty'])?$params['dropOnEmpty']:false;
		
		//If true, forces the helper to have a size.
		$params['forceHelperSize'] = isset($params['forceHelperSize'])?$params['forceHelperSize']:false;
		
		//If true, forces the placeholder to have a size.
		$params['forcePlaceholderSize'] = isset($params['forcePlaceholderSize'])?$params['forcePlaceholderSize']:false;
		
		//Whether to make an ajax request
		$params['post'] = isset($params['post'])?$params['post']:false;
		
		//post data as an array
		$params['postdata'] = isset($params['postdata'])?$params['postdata']:array();
		
		//post data variables as an array
		$params['postvars'] = isset($params['postvars'])?$params['postvars']:array();
		
		//axis to move in
		$params['axis'] = isset($params['axis'])?$params['axis']:'';
		
		//If set to true, the item will be reverted to its new DOM position with a smooth animation.
		$params['revert'] = isset($params['revert'])?$params['revert']:false;
		
		//cancel the sortable on a particular record 
		$params['cancel'] = isset($params['cancel'])?$params['cancel']:false;
		
		//returning javscript function 
		$params['selection'] = isset($params['selection'])?$params['selection']:true;
		
		//returning javscript function 
		$params['return'] = isset($params['return'])?$params['return']:'';
		
		//reordering javscript function 
		$params['reorder'] = isset($params['reorder'])?$params['reorder']:'';
		
		//Restricts sort start click to the specified element.
		$params['handle'] = isset($params['handle'])?$params['handle']:false;
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
		$jd( "'.$id.'" ).sortable({
		   delay: "'.$params['delay'].'"';
		   
		if($params['distance'])
			$js .= ', distance: '.$params['distance'];
		
		if($params['handle'])
			$js .= ', handle: "'.$params['handle'].'"';
		
		if($params['cancel'])
			$js .= ', cancel: "'.$params['cancel'].'"';
		   
		if($params['revert'])
			$js .= ', revert: true';
			
		if($params['connectWith'])
			$js .= ', connectWith: "#'.$params['connectWith'].'"';
			
		if($params['containment'])
			$js .= ', containment: "'.$params['containment'].'"';
			
		if($params['dropOnEmpty'])
			$js .= ', dropOnEmpty: true';
			
		if($params['forceHelperSize'])
			$js .= ', forceHelperSize: true';
			
		if($params['forcePlaceholderSize'])
			$js .= ', forcePlaceholderSize: true';
			
		if($params['disabled'])
			$js .= ', disabled: true';
			
		if($params['axis'])
			$js .= ', axis: "'. $params['axis'].'"';
		
		if($params['post'])	{
		
			$js .= ', stop: function(event, ui) {
		  
		  		var results = $jd( "'.$id.'" ).sortable("toArray");
				$jd.ajax({
				  async:false,
				  url: "'.$params['url'].'",
				  type: "POST",
				  dataType:  "json",
				  data: {"'.$params['id'].'": results';
				  
				if(count($params['postdata']))	{
				
					foreach($params['postdata'] as $k=>$v)
						$arr[] = '"'.$k.'": "'.$v.'"';
					
					$js .= ', '.implode(', ', $arr);
					
				}
				
				if(count($params['postvars']))	{
				
					foreach($params['postvars'] as $k=>$v)
						$arr[] = '"'.$k.'": '.$v;
					
					$js .= ', '.implode(', ', $arr);
					
				}
				
				  $js .= '},
				  beforeSend: function()	{
                  	$jd(".loadingblock").show();
                  },
                  complete: function()	{
                  	$jd(".loadingblock").hide();
                  },
				  success: function(data)	{';
				  
				  if($params['reorder'])
					$js .= $params['reorder'].'(results);';
				   
				  $js .= 'var titles = $jd("'.$id.'").children();
					var size = titles.size();
					for(var i =0;i<size;i++)	{
						$jd(titles[i]).attr("id", "order_"+i);
					}
					'.$params['return'].'(data);
				  }
				  });
				  
				}';
				  
		   }
		   
		   $js .= '
		   
		});';
		
		if(!$params['selection'])
			$js .= '$jd( "#'.$id.'" ).disableSelection();';
		
		$js .= '});
		';
		
		$document->addScriptDeclaration($js);
		
	}
	
	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param string The pane identifier
	 */
	function startPane()
	{
		
		return '<div id="'.substr_replace($this->_id, '', 0, 1).'">';
	}

    /**
	 * Ends the pane
	 */
	function endPane() {
		return '</div>';
	}

	/**
	 * Creates a tab panel with title text and starts that panel
	 *
	 * @param	string	$text - The name of the tab
	 * @param	string	$id - The tab identifier
	 */
	function startPanel( $id )
	{
		return '<div id="'.$id.'">';
	}

	/**
	 * Ends a tab page
	 */
	function endPanel()
	{
		return '</div>';
	}
	
}
