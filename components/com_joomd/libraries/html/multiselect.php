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


class Multiselect extends Joomdui
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
		
		$document =  JFactory::getDocument();
		
		//Either a boolean value denoting whether or not to display the header, or a string value. If you pass a string, the default “check all”, “uncheck all”, and “close” links will be replaced with the specified text.
		$params['header'] = isset($params['header'])?$params['header']:false;
		
		//Height of the checkbox container (scroll area) in pixels. If set to “auto”, the height will calculate based on the number of checkboxes in the menu.
		$params['height'] = isset($params['height'])?$params['height']:'auto';
		
		//set the maximum height of the container
		$params['maxHeight'] = isset($params['maxHeight'])?$params['maxHeight']:null;
		
		//Minimum width of the entire widget in pixels. Setting to “auto” will disable.
		$params['minWidth']	= isset($params['minWidth'])?$params['minWidth']:null;
		
		//The text of the “check all” link.
		$params['checkAllText']	= isset($params['checkAllText'])?$params['checkAllText']:JText::_('CHECK_ALL');
		
		//The text of the “uncheck all” link.
		$params['uncheckAllText']	= isset($params['uncheckAllText'])?$params['uncheckAllText']:JText::_('UNCHECK_ALL');
		
		//The default text the select box when no options have been selected.
		$params['noneSelectedText']	= isset($params['noneSelectedText'])?$params['noneSelectedText']:null;
		
		//The text to display in the select box when options are selected. (#) will automatically replaced by the number of checkboxes selected
		$params['selectedText']	= isset($params['selectedText'])?$params['selectedText']:null;
		
		//A numeric value denoting whether or not to display the checked opens in a list, and how many. A value of 0 or false is disabled showing the selectedtext.
		$params['selectedList']	= isset($params['selectedList'])?$params['selectedList']:1;
		
		//The name of the effect to use when the menu opens. To control the speed as well, pass in an array: ['slide', 500]
		$params['show']		= isset($params['show'])?$params['show']:null;
		
		//The name of the effect to use when the menu closes. To control the speed as well, pass in an array: ['explode', 500]
		$params['hide']	= isset($params['hide'])?$params['hide']:null;
		
		//A boolean value denoting whether or not to automatically open the menu when the widget is initialized.
		$params['autoOpen']	= isset($params['autoOpen'])?$params['autoOpen']:false;
		
		//If set to false, the widget will use radio buttons instead of checkboxes, forcing users to select only one option.
		$params['multiple'] = isset($params['multiple'])?$params['multiple']:false;
		
		//Additional class(es) to apply to BOTH the button and menu for further customization. Separate multiple classes with a space. You’ll need to scope your CSS to differentiate between the button/menu:
		$params['classes'] = isset($params['classes'])?$params['classes']:null;
		
		//This option allows you to position the menu anywhere you’d like relative to the button; centered, above, below (default), etc.
		$params['position']	= isset($params['position'])?$params['position']:array('left top', 'left bottom');
		
		//Fires when a checkbox is checked or unchecked.
		$params['click'] = isset($params['click'])?$params['click']:false;
		
		//Fires when checkall is checked or unchecked.
		$params['checkAll'] = isset($params['checkAll'])?$params['checkAll']:false;
		
		//Fires when uncheckall is checked or unchecked.
		$params['uncheckAll'] = isset($params['uncheckAll'])?$params['uncheckAll']:false;
		
		//If set to true will display a filter textbox
		$params['filter'] = isset($params['filter'])?$params['filter']:false;
		
		
		$this->_params = $params;
		
		$js = '$jd(function() {
				
				$jd( "'.$id.'" ).multiselect({
					
					selectedList: '.$params['selectedList'];
					
		if($params['header'])
			$js .= ', header: true';
		else
			$js .= ', header: false';
			
		if(!empty($params['height']))
			$js .= ', height: "'.$params['height'].'"';
			
		if(!empty($params['maxHeight']))
			$js .= ', maxHeight: "'.$params['maxHeight'].'"';
			
		if(!empty($params['minWidth']))
			$js .= ', minWidth: "'.$params['minWidth'].'"';
			
		if(!empty($params['checkAllText']))
			$js .= ', checkAllText: "'.$params['checkAllText'].'"';
			
		if(!empty($params['uncheckAllText']))
			$js .= ', uncheckAllText: "'.$params['uncheckAllText'].'"';
			
		if(!empty($params['noneSelectedText']))
			$js .= ', noneSelectedText: "'.$params['noneSelectedText'].'"';
			
		if(!empty($params['selectedText']))
			$js .= ', selectedText: "'.$params['selectedText'].'"';
			
		if(!empty($params['show']))
			$js .= ', show: "'.$params['show'].'"';
			
		if(!empty($params['hide']))
			$js .= ', hide: "'.$params['hide'].'"';
			
		if(!empty($params['classes']))
			$js .= ', classes: "'.$params['classes'].'"';
			
		if(!empty($params['position']))
			$js .= ', position: {';
				
				if(is_array($params['position']))
					$js .= 'my: "'.$params['position'][0].'",
							at: "'.$params['position'][1].'"';
				else
					$js .= 'my: "'.$params['position'].'",
							at: "'.$params['position'].'"';
				
			$js .= '}';
			
		if($params['autoOpen'])
			$js .= ', autoOpen: true';
			
		if(!$params['multiple'])
			$js .= ', multiple: false';
		
		if($params['click'])	{
			
			$js .= ', click: function(event, ui)	{
				
						var checked = $jd("'.$id.'").multiselect("getChecked").map(function(){
						   return this.value;	
						}).get();
						
						'.$params['click'].'(checked, event, ui);
				
					}';
			
		}
		
		if($params['checkAll'])	{
			
			$js .= ', checkAll: function()	{
				
						var checked = $jd("'.$id.'").multiselect("getChecked").map(function(){
						   return this.value;	
						}).get();
						
						'.$params['checkAll'].'(checked);
				
					}';
			
		}
		
		if($params['uncheckAll'])	{
			
			$js .= ', uncheckAll: function()	{
						
						'.$params['uncheckAll'].'();
				
					}';
			
		}
		
		if($params['filter'])
			$js .= '}).multiselectfilter();';
		else
			$js .= '});';
			
		$js .= '});';
		
		$document->addScriptDeclaration($js);
		
	}
	
	function refresh($id)
	{
		
		$js = '$jd("'.$id.'").multiselect("refresh");';
		
		return $js;
		
	}
	
}
