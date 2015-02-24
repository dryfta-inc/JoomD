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

class Autocomplete extends Joomdui
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
		//Fixes display duration.
		$params['url']		= isset($params['url'])?$params['url']:'';
		//Fixes display duration.
		$params['multiple']		= isset($params['multiple'])?$params['multiple']:false;
		//Fixes display duration.
		$params['tab']		= isset($params['tab'])?$params['tab']:false;
	    //Fixes display duration.
		$params['logId']		= isset($params['logId'])?$params['logId']:'';
	    //Fixes display duration.
		$params['source']		= isset($params['source'])?$params['source']:'';
	    //Fixes display duration.
		$params['logId']		= isset($params['logId'])?$params['logId']:'';
	    //Fixes display duration.
		$params['function']		= isset($params['function'])?$params['function']:'';
		//Fixes display duration.
		$params['minLength']		= isset($params['minLength'])?$params['minLength']:0;
		$this->_params = $params;
		
		$document =  JFactory::getDocument();

	   $js = '$jd(function() {';
	   
	   if($params['url']<>''){
		   $js .='function log( message ) {
				$jd( "<div/>" ).text( message ).prependTo( "'.$params['logId'].'" );
				$jd( "'.$params['logId'].'" ).attr( "scrollTop", 0 );
			}';
			}
		if($params['source']<>''){	
			$js .='var availableTags = [';
            
			if(count($params['source'])==count($params['source'],COUNT_RECURSIVE))
			  {
			  
				$count= count($params['source']);
					for($i=0;$i<$count;$i++){
					
					$js .='"'.$params['source'][$i].'"';
					
						 if($i<>$count-1)
						 {
						 $js .=",";
						 }
					 }
				}
				else{
				
					$count= count($params['source']);
					for($i=0;$i<$count;$i++){
					 $c = count($params['source'][$i]);
					 if($c==1){
					 $js .='"'.$params['source'][$i].'"';
					 }
					 else{  
					 for($j=0;$j<$c;$j++){
					 
					   $js .='"'.$params['source'][$i][$j].'"';
					
						 if($j<>$c-1)
						 {
						 $js .=",";
						 }
						
					    } 
					   }
					   
					  	 if($i<>$count-1)
						 {
						 $js .=",";
						 }
					 }
				
				}
			   $js .='];';
        }
		if($params['multiple']<>false)
		{
        $js .='function split( val ) {
				return val.split( /,\s*/ );
			}
			function extractLast( term ) {
				return split( term ).pop();
			}';

		
		}
	   $js .='$jd( "'.$id.'" )';
	   
	   if($params['tab']<>false){
	   $js .='.bind( "keydown", function( event ) {
				if ( event.keyCode === $jd.ui.keyCode.TAB &&
					$jd( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})';
	   }
	   
	   $js .='.autocomplete({
	     minLength:'.$params['minLength'].',';
		
		if($params['url']<>'')
		{
	        $js .='source: function( request, response ) {
				$jd.ajax({
					url: "'.$params['url'].'",
					dataType: "jsonp",
					data: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
						name_startsWith: request.term
					},
					beforeSend: function()	{
						$jd(".loadingblock").show();
					  },
					  complete: function()	{
						$jd(".loadingblock").hide();
					  },
					success: function( data ) {
						response( $jd.map( data.geonames, function( item ) {
							return {
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
								value: item.name
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				log( ui.item ?
					"Selected: " + ui.item.label :
					"Nothing selected, input was " + this.value);
			},
			open: function() {
				$jd( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			},
			close: function() {
				$jd( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}';
		}
		elseif($params['multiple']<>false && $params['source']<>'')
		{
			$js .='source: function( request, response ) {
				    // delegate back to autocomplete, but extract the last term
					response( $jd.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				}';
		
        }
		else{
		$js .='source: availableTags';
		$js .=',focus: function( event, ui ) {
				$jd( "'.$id.'" ).val( ui.item.label );
				return false;
			},';
			if($params['function']<>''){
			$js .='select: function( event, ui ) {
	        '.$params['function'].'(event,ui)			
			}';
		  }
		}
	    $js .='});';
		
		$js .='});';
		
		$document->addScriptDeclaration($js);
		
	}
		
}
