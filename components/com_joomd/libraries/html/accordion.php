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


class Accordion extends Joomdui
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
	function startPane( $id, $params = array())
	{
		
		$this->_id = $id;
		
		//Whether all the sections can be closed at once. Allows collapsing the active section by the triggering event (click is the default).
		$params['collapsible'] = isset($params['collapsible'])?$params['collapsible']:false;
		
		//Selector for the active element. Set to false to display none at start
		$params['autoHeight'] = isset($params['autoHeight'])?$params['autoHeight']:false;
		
		//Selector for the active element. Set to false to display none at start
		$params['active'] = isset($params['active'])?$params['active']:0;
		
		$params['class'] = isset($params['class'])?$params['class']:'panel';
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
		var stop = false;
		var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};
		$jd( "#'.$id.' h3" ).live("click", function( event ) {
			if ( stop ) {
				event.stopImmediatePropagation();
				event.preventDefault();
				stop = false;
			}
		});
		
		$jd( "#'.$id.'" )
			.accordion({';
		if($params['active'] === false)
			$js .= 'active: false,';
		else
			$js .= 'active: '.(int)$params['active'].',';
			
		if($params['autoHeight'] === false)
			$js .= 'autoHeight: false,';
		else
			$js .= 'autoHeight: true,';
		
		$js .= 'collapsible: "'.$params['collapsible'].'",
				icons: icons,
				header: "> div > h3"
			});
		
		});';
		
		$document->addScriptDeclaration($js);
		
		return '<div id="'.$this->_id.'" class="accordian_panel">';

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
	function startPanel( $id, $title )
	{
		return '<div id="'.$id.'" class="'.$this->_params['class'].'"><h3><a href="#">'.$title.'</a></h3><div>';
	}

	/**
	 * Ends a tab page
	 */
	function endPanel()
	{
		return '</div></div>';
	}
	
}
