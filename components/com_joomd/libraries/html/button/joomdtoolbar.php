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

/**
 * Renders a standard button
 *
 * @package 	Joomla.Framework
 * @subpackage		HTML
 * @since		1.5
 */
class JButtonJoomdtoolbar extends JButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Standard';
	var $_list = 'document.adminlist';
	
	function __construct($list=null)
	{
		
		$mainframe =  JFactory::getApplication();
		
		if($mainframe->isSite())
			$this->_list = 'document.listform';
			
	}

	function fetchButton( $type='Standard', $jtype='custom', $alt = '', $task = '', $icon = '', $list = false )
	{
		
		$html = $this->$jtype($alt, $task, $icon, $list);

		return $html;
	}
	
	function dopin($alt, $task, $icon, $list)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		
		$todo		= JString::strtolower(JText::_( $alt ));
		$message	= JText::sprintf( 'PLSMSELFL', $todo );
		$message	= addslashes($message);
		$message2	= JText::_('PLZSELECTTYPE');
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"javascript:if($this->_list.filter_type.value==0) {alert('$message2');} else if($this->_list.boxchecked.value==0){alert('$message');} else{ dopin('adminlist', '$task')}\" class=\"toolbar\">\n";
		$html 	.= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html	.= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function custom($alt, $task, $icon, $list)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		$doTask	= $this->_getCommand($alt, $task, $list);
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html 	.= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html	.= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function cancel($alt, $icon)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		
		$html	= "<a href=\"javascript:history.back();\" class=\"toolbar\">\n";
		$html 	.= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html	.= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function delete($alt, $task, $icon, $list)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		$doTask	= $this->_getCommand($alt, $task, $list);
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html 	.= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html	.= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function copy($alt, $task, $icon, $list)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		$doTask	= $this->_getCommand($alt, $task, $list);
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html 	.= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html	.= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function savencopy($alt, $task, $icon)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"save('$task')\" class=\"toolbar\">\n";
		$html 	.= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html	.= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function save($alt, $task, $icon)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"save('$task')\" class=\"toolbar\">\n";
		$html  .= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html  .= "</span>\n";
		$html  .= "$i18n_text\n";
		$html  .= "</a>\n";
		
		return $html;
		
	}
	
	function state($alt, $task, $icon, $list)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		$doTask	= $this->_getCommand($alt, $task, $list);
		
		$html	 = "<a href=\"javascript:void(0);\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html 	.= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html 	.= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function dialog($alt, $task, $icon, $list)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
		
		if ($list) {
			$todo		= JString::strtolower(JText::_( $alt ));
			$message	= JText::sprintf( 'PLSMSELFL', $todo );
			$message	= addslashes($message);
			$doTask = "javascript:if($this->_list.boxchecked.value==0){alert('$message');}else{ openDiag('$task');}";
		} else {
			$doTask = "javascript:openDiag('$task');";
		}
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html .= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function help($alt, $ref, $com)
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass('help');
		
		jimport('joomla.language.help');
		$url = JHelp::createURL($ref, $com);
		
		$doTask	= "popupWindow('$url', '".JText::_('HELP', true)."', 640, 480, 1)";
		
		$html	= "<a href=\"javascript:void(0);\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html .= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}
	
	function url($alt, $ref, $icon, $target='_self')
	{
		
		$i18n_text	= JText::_($alt);
		$class	= $this->fetchIconClass($icon);
				
		$html	= "<a href=\"$ref\" class=\"toolbar\" target=\"$target\">\n";
		$html .= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html .= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</a>\n";
		
		return $html;
		
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access	public
	 * @return	string	Button CSS Id
	 * @since	1.5
	 */
	function fetchId( $type='Standard', $name = '', $text = '', $task = '', $list = true )
	{
		return 'icon-32-'.$name;
	}

	function _getCommand($name, $task, $list)
	{
		$todo		= JString::strtolower(JText::_( $name ));
		$message	= JText::sprintf( 'PLSMSELFL', $todo );
		$message	= addslashes($message);

		if ($list) {
			$cmd = "javascript:if($this->_list.boxchecked.value==0){alert('$message');}else{ listItemTask('$task')}";
		} else {
			$cmd = "javascript:listItemTask('$task')";
		}


		return $cmd;
	}
	
}