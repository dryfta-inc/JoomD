<?php
/**
 * @package 	mod_bt_contentslider - BT ContentSlider Module
 * @version		1.4
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2011 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';
if( !defined('ThumbLoaded') )
{
	require_once JPATH_SITE.DS.'modules'.DS.'mod_bt_contentslider'.DS.'classes'.DS.'images.php';
	define('ThumbLoaded',1);
}
class modBtContentSliderHelper {

	/**
	 * Get list articles
	 * Ver 1 : only form content
	 */
	public static function getList( &$params, $module ){
		// create thumbnail folder
	 	$thumbPath = JPATH_SITE.DS.'modules'.DS.$module->module.DS.'images'.DS;
		$thumbUrl  = JURI::base().'modules/'.$module->module.'/images/' ;

		if( !file_exists($thumbPath) ) {
			JFolder::create( $thumbPath, 0777 );
		};
		//Get source form params
		$source 	= $params->get('source','category');
		if($source == 'category' || $source == 'article_ids')
		{
			$source = 'content';
		}
		else if($source == 'k2_category' || $source == 'k2_article_ids')
		{
			$source = 'k2';
		}
		else if($source == 'btportfolio_category' || $source == 'btportfolio_article_ids')
		{
			$source = 'btportfolio';
		}
		else{
			$source = 'content';
		}


		//var_dump($source);

		$path = JPATH_SITE.DS.'modules'.DS.'mod_bt_contentslider'.DS.'classes'.DS.$source.".php";
		//echo $path;
		//die();

		if( !file_exists($path) ){
			return array();
		}
		require_once $path;
		$objectName = "Bt".ucfirst($source)."DataSource";
	 	$object = new $objectName($params );
		//3 step
		//1.set images path
		//2.Render thumb
		//3.Get List
	 	$items = $object->setThumbPathInfo($thumbPath,$thumbUrl)
			->setImagesRendered( array( 'thumbnail' =>
										array( (int)$params->get( 'thumbnail_width', 60 ), (int)$params->get( 'thumbnail_height', 60 ))
									) )
			->getList();
  		return $items;
	}

	function fetchHead($params){
		$document	= &JFactory::getDocument();
		$header = $document->getHeadData();
		$mainframe = JFactory::getApplication();
		$template = $mainframe->getTemplate();

		if(file_exists(JPATH_BASE.DS.'templates'.DS.$template.DS.'html'.DS.'mod_bt_contentslider'.DS.'css'.DS.'btcontentslider.css'))
		{
			$document->addStyleSheet(  JURI::root().'templates/'.$template.'/html/mod_bt_contentslider/css/btcontentslider.css');
		}
		else{
			$document->addStyleSheet(JURI::root().'modules/mod_bt_contentslider/tmpl/css/btcontentslider.css');
		}

		$loadJquery = true;
		switch($params->get('loadJquery',"auto")){
			case "0":
				$loadJquery = false;
				break;
			case "1":
				$loadJquery = true;
				break;
			case "auto":

				foreach($header['scripts'] as $scriptName => $scriptData)
				{
					if(substr_count($scriptName,'jquery'))
					{
						$loadJquery = false;
						break;
					}
				}
			break;
		}
		//Add js
		if($loadJquery)
		{
			$document->addScript(JURI::root().'modules/mod_bt_contentslider/tmpl/js/jquery.min.js');
		}
		$document->addScript(JURI::root().'modules/mod_bt_contentslider/tmpl/js/slides.js');
		$document->addScript(JURI::root().'modules/mod_bt_contentslider/tmpl/js/default.js');
		$document->addScript(JURI::root().'modules/mod_bt_contentslider/tmpl/js/jquery.easing.1.3.js');
	}
}
?>