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


class Tabs extends Joomdui
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
	
	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param string The pane identifier
	 */
	function startPane($id, $params = array())
	{
		
		$this->_id = $id;
		$this->_params = $params;
		$this->header = '';
		
		return '<div id="'.$this->_id.'">';
	}

    /**
	 * Ends the pane
	 */
	function endPane() {
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
			$jd("#'.$this->_id.'").prepend("'.addslashes('<ul>'.$this->header.'</ul>').'");
		});';
		$document->addScriptDeclaration($js);
		
		//Store the latest selected tab in a cookie
		$this->_params['cookie'] = isset($this->_params['cookie'])?$this->_params['cookie']:false;
		
		//The type of event to be used for selecting a tab.
		$this->_params['event'] = isset($this->_params['event'])?$this->_params['event']:'click';
		
		//Display header at the bottom
		$this->_params['bottom'] = isset($this->_params['bottom'])?$this->_params['bottom']:false;
		
		//Display header at the vertical
		$this->_params['vertical'] = isset($this->_params['vertical'])?$this->_params['vertical']:false;
		
		//Set to true to allow an already selected tab to become unselected again upon reselection.
		$this->_params['collapsible'] = isset($this->_params['collapsible'])?$this->_params['collapsible']:false;
		
		//Zero-based index of the tab to be selected on initialization. To set all tabs to unselected pass -1 as value.
		$this->_params['selected'] = isset($this->_params['selected'])?$this->_params['selected']:0;
		
		//Enable animations for hiding and showing tab panels.
		$this->_params['effect'] = isset($this->_params['effect'])?$this->_params['effect']:'';
		
		//The duration option can be a string representing one of the three predefined speeds ("slow", "normal", "fast")
		$this->_params['slide'] = isset($this->_params['slide'])?$this->_params['slide']:'normal';

		
		$js = '$jd(function() {
		
		$jd( "#'.$this->_id.'" ).tabs({
			event: "'.$this->_params['event'].'"';
			
			
		if($this->_params['selected'])
			$js .= ', selected: '.$this->_params['selected'];

		
		if($this->_params['collapsible'])
			$js .= ', collapsible:true';
		
		 if($this->_params['cookie']){
			
			$document->addScript(JURI::root().'components/com_joomd/assets/js/jquery.cky.js');
		 
			 $js .=', cookie: {
				expires: 1
			}';
		}
		
		if($this->_params['effect'])	{
			$js .= ', fx:	{
				'.$this->_params['effect'].': "toggle",
				slide: "'.$this->_params['slide'].'"
			}';
		}
			
				
		$js .= '});';
		
		if($this->_params['vertical'])	{
			$js .= '$jd( "#'.$this->_id.'" ).addClass( "ui-tabs-vertical ui-helper-clearfix" );
					$jd( "#'.$this->_id.' li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );';
					
			$css = '.ui-tabs-vertical { width: 55em; }
	.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
	.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
	.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
	.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-selected { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
	.ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}';
	
			$document->addStyleDeclaration($css);
			
		}
					
		if($this->_params['bottom']){
		$js .=' $jd( ".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *" )
			.removeClass( "ui-corner-all ui-corner-top" )
			.addClass( "ui-corner-bottom" );';
		}
		
		$js .= '}
		);';
		
		$document->addScriptDeclaration($js);
		
		
		return '</div>';
	}

	/**
	 * Creates a tab panel with title text and starts that panel
	 *
	 * @param	string	$text - The name of the tab
	 * @param	string	$id - The tab identifier
	 */
	function startPanel( $id, $title )
	{	
		$this->header .= '<li><a href="#'.$id.'">'.$title.'</a></li>';
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
