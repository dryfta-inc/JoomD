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


class Datepicker extends Joomdui
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
		
		//Initialize a datepicker with the defaultDate option specified. 
		$params['defaultDate']		= isset($params['defaultDate'])?$params['defaultDate']:'';
		
		//Date Format
		$params['format']		= isset($params['format'])?$params['format']:'yy-mm-dd';
		
		//Set whether to display icon or not
		$params['icon']			= isset($params['icon'])?$params['icon']:true;
		
		//The jQuery selector for another field that is to be updated with the selected date from the datepicker.
		$params['altField']			= isset($params['altField'])?$params['altField']:'';
		
		//The dateFormat to be used for the altField option
		$params['altformat']	= isset($params['altformat'])?$params['altformat']:'yy-mm-dd';
		
		//Display dates in other months (non-selectable) at the start or end of the current month.
		$params['showOtherMonths']	= isset($params['showOtherMonths'])?$params['showOtherMonths']:false;
		
		//When true days in other months shown before or after the current month are selectable.
		$params['selectOtherMonths']	= isset($params['selectOtherMonths'])?$params['selectOtherMonths']:false;
		
		//Set a minimum selectable date via a Date object or as a string in the current dateFormat
		$params['minDate']		= isset($params['minDate'])?$params['minDate']:'';
		
		//Set a maximum selectable date via a Date object or as a string in the current dateFormat
		$params['maxDate']		= isset($params['maxDate'])?$params['maxDate']:'';
		
		//Whether to show the button panel.
		$params['showButtonPanel']	= isset($params['showButtonPanel'])?$params['showButtonPanel']:false;
		
		//Allows you to change the month by selecting from a drop-down list.
		$params['changeMonth']			= isset($params['changeMonth'])?$params['changeMonth']:false;
		
		//Allows you to change the year by selecting from a drop-down list.
		$params['changeYear']			= isset($params['changeYear'])?$params['changeYear']:false;
		
		//Allows you to set year range.
		$params['yearRange']			= isset($params['yearRange'])?$params['yearRange']:false;
		
		//When true a column is added to show the week of the year
		$params['showWeek']			= isset($params['showWeek'])?$params['showWeek']:false;
		
		//Set the first day of the week: Sunday is 0, Monday is 1, ..
		$params['firstDay']			= isset($params['firstDay'])?$params['firstDay']:1;
		
		//Set how many months to show at once.
		$params['numberOfMonths']			= isset($params['numberOfMonths'])?$params['numberOfMonths']:1;
		
		//The text to display for the next month link
		$params['nextText']			= isset($params['nextText'])?$params['nextText']:JText::_('NEXT');
		
		//The text to display for the previous month link
		$params['prevText']			= isset($params['prevText'])?$params['prevText']:JText::_('PREV');
		
		$this->_params = $params;
		
		$document =  JFactory::getDocument();
		
		$js = '$jd(function() {
				
				$jd( "'.$id.'" ).datepicker({
					
					dateFormat: "'.$params['format'].'",
					nextText: "'.$params['nextText'].'",
					prevText: "'.$params['prevText'].'",
					numberOfMonths: '.$params['numberOfMonths'];
					
				if($params['defaultDate'] <> "")
					$js .= ', defaultDate: "'.$params['defaultDate'].'"';
					
				if(!$params['changeMonth'])
					$js .= ', changeMonth:false';
			
				if($params['showOtherMonths'])	{
					$js .= ', showOtherMonths: true';
				
					if($params['selectOtherMonths'])
						$js .= 'selectOtherMonths: true';
				
				}
				
				if($params['icon'])
					$js .= ', showOn: "button",
					buttonImage: "'.JURI::root().'components/com_joomd/assets/images/calendar.gif",
					buttonImageOnly: true';
					
				if($params['altField'] <> "")
					$js .= ', altField: "'.$params['altField'].'",
					altFormat: "'.$params['altformat'].'"';
				
				if($params['minDate'] <> "")
					$js .= ', minDate: "'.$params['minDate'].'"';
					
				if($params['maxDate'] <> "")
					$js .= ', maxDate: "'.$params['maxDate'].'"';
					
				if($params['showButtonPanel'])
					$js .= ', showButtonPanel: true';
					
				if($params['changeMonth'])
					$js .= ', changeMonth: true';
					
				if($params['changeYear'])	{
					$js .= ', changeYear: true';
					
					if($params['yearRange'])
						$js .= ', yearRange: "'.$params['yearRange'].'"';
				}
					
				if($params['showWeek'])
					$js .= ', showWeek: true';
							
				if($params['firstDay'])
					$js .= ', firstDay: '.$params['firstDay'];
					
						
		$js .= '	});
			
			});';
		
		$document->addScriptDeclaration($js);
		
	}
	
}
