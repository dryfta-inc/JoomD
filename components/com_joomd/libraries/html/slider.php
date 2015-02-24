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


class Slider extends Joomdui
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
		
	    //Orientation is either 'vertical' or 'horizontal'.
		
		$params['orientation']				= isset($params['orientation'])?$params['orientation']:'';
		
		// parameter for range are 'true','false'.
		
		$params['range']				= isset($params['range'])?$params['range']:'';
		// parameter for animate are 'true' or 'false'.
		$params['animate']				= isset($params['animate'])?$params['animate']:false;
		
		$params['slide']				= isset($params['slide'])?$params['slide']:'';
			
		$params['max']				    = isset($params['max'])?$params['max']:'';
		
		$params['min']				    = isset($params['min'])?$params['min']:0;
		
		$params['value']				= isset($params['value'])?$params['value']:'';
		
		$params['values']				= isset($params['values'])?$params['values']:'';
		
	    $params['step']				    = isset($params['step'])?$params['step']:'';
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
				
				$jd( "#'.$id.'" ).slider({
				 
				min:'.$params['min'].'';
					
					if($params['max'] <> "")
				   	$js .= ',max:'.$params['max'].'';
					
					if($params['range'] <> '')
				    $js .= ',range:true';
					else
					$js .=',range:"min"' ;
					 
					if($params['animate'])
                    $js .=',animate:true' ;  
                   					 
					if($params['step'])
				    $js .= ',step: '.$params['step'].'';
					 
				    if($params['orientation'] == "")
				    $js .= ',orientation: "horizontal"' ;
					else
					$js .= ',orientation: "'.$params['orientation'].'"';
					
				   	if($params['value'] == "")
				    $js .= ',value:0';
					else
					$js .= ',value: "'.$params['value'].'"';
					
					if($params['values'] <> "")
				    $js .= ',values: ['.$params['values'].']';
					
					if($params['slide'] !=='')
					{
					if($params['values'] <>'' && gettype($params['values'])=='string')
					$js .=',slide: function( event, ui ) {
				    $jd( "#'.$params['slide'].'").val(  + ui.values[ 0 ] + " - "+ ui.values[ 1 ] );}';
					else if($params['step']){
					$js .=',slide: function( event, ui ) {
				     $jd( "#'.$params['slide'].'"  ).val(  + ui.value );}';
			          }
					else 
					$js .=',slide: function(event, ui){
					$jd( "#'.$params['slide'].'" ).val( ui.value );}';
					}		
		$js .= '});';

		       	if($params['slide'] !==''){
                 if( $params['values'] <>'' && gettype($params['values'])=='string'){	
				 $js .= '$jd("#'.$params['slide'].'").val( + $jd( "#'.$id.'" ).slider( "values", 0 ) +
			          " - " + $jd( "#'.$id.'" ).slider( "values", 1 ) )';
			         }
 
				 else if($params['step']){	 
				 $js .='$jd("#'.$params['slide'].'").val( + $jd( "#'.$id.'" ).slider( "value" ) )';
					 } 
			     else  
				 $js .='$jd("#'.$params['slide'].'").val( $jd( "#'.$id.'"  ).slider( "value" ) )';  
					 }
				if($params['animate'] <> false){	 
				 $js .='$jd( "#eq > span" ).each(function() {
			            var value = parseInt( $jd( this ).text(), 10 );';
			     $js .='$jd( this ).empty().slider({
				       value: value,
				       range: "min",
				       animate: true,
				       orientation: "vertical"
		 	           });';
		          $js .= '});';	 }
					 
		$js .= '});';
				
		$document->addScriptDeclaration($js);
		
	}
	
}
