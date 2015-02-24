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


class Editor extends Joomdui
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
	//	loader::htmlbox();
	}
	
	function initialize( $id, $params = array() )
	{
		
		$document =  JFactory::getDocument();
		
		$params['skin']		= isset($params['skin'])?$params['skin']:'blue';
		
		$params['code']		= isset($params['code'])?$params['code']:true;
		$params['undo']		= isset($params['undo'])?$params['undo']:true;
		$params['font']		= isset($params['font'])?$params['font']:true;
		$params['para']		= isset($params['para'])?$params['para']:true;
		$params['list']		= isset($params['list'])?$params['list']:true;
		$params['link']		= isset($params['link'])?$params['link']:true;
		
		$params['formats']	= isset($params['formats'])?$params['formats']:true;
		$params['colors']	= isset($params['colors'])?$params['colors']:true;
		
		$params['tags']	= isset($params['tags'])?$params['tags']:true;
		$params['styles']	= isset($params['styles'])?$params['styles']:true;
		
		
		$this->_params = $params;
		
		$js = '$jd(function() {
				
				$jd( "'.$id.'" ).htmlbox({
					idir: "'.JURI::root().'components/com_joomd/assets/images/",
					skin: "'.$params['skin'].'",
					
					toolbars: [
						[';
						 
				$js .= '"separator", "code"';
				
				if($params['undo'])
					$js .= ', "separator", "undo", "redo"';
					
				if($params['font'])
					$js .= ', "separator","bold","italic","underline","strike","sup","sub"';
					
				if($params['para'])
					$js .= ', "separator","justify","left","center","right"';
					
				if($params['list'])
					$js .= ', "separator","ol","ul","indent","outdent"';
					
				if($params['link'])
					$js .= ', "separator","link","unlink","image"';
				
						 
				$js .= ']';
				
				if($params['formats'] or $params['colors'])	{
				
					$js .= ', ["separator"';
				
					if($params['formats'])
						$js .= ',"formats","fontsize","fontfamily", "separator"';
					
					if($params['colors'])
						$js .= ',"fontcolor","highlight"';
						
					$js .= ']';
					
				}
				
				if($params['tags'] or $params['styles'])	{
				
					$js .= ', ["separator"';
				
					if($params['tags'])
						$js .= ',"removeformat","striptags","hr","paragraph","quote"';
					
					if($params['styles'])
						$js .= ',"styles"';
						
					$js .= ']';
					
				}
						
			$js .= ']
					
				});
				
			});';
		
		$document->addScriptDeclaration($js);
		
	}
	
}
