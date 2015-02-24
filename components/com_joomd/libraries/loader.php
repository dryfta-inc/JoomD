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

defined('_JEXEC') or die('Restricted access');

if(!class_exists('Joomd'))
	require_once(JPATH_SITE.'/components/com_joomd/libraries/core.php');

//Contains the function to initialize all the classes

class Loader extends Joomd	{
	
	public static function loadjs()
	{
		
		$doc =  JFactory::getDocument();
		
		$mainframe =  JFactory::getApplication();
		
		$config = Joomd::getConfig();
		
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.1.7.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.core.1.8.16.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/main.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.tmpl.min.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.blockUI.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.widget.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.mouse.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.progressbar.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.position.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.slider.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.button.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.draggable.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.resizable.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.effects.core.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.sortable.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.accordion.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.tabs.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.datepicker.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.timepicker.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ui.dialog.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.tipsy.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.printElement.js');
		$doc->addScript('http://maps.google.com/maps/api/js?sensor=true');
		
		loader::uploadfile();
		loader::multiselect();
		loader::htmlbox();
				
	}
	
	public static function loadcss()
	{
		$mainframe =  JFactory::getApplication();
		$doc =  JFactory::getDocument();
		$config = Joomd::getConfig();
		
		if($mainframe->isSite())
			$doc->addStyleSheet(JURI::root().'components/com_joomd/templates/'.$config->theme.'/css/style.css');
		else
			$doc->addStyleSheet('components/com_joomd/assets/css/style.css');

		$doc->addStyleSheet(JURI::root().'components/com_joomd/assets/css/jquery.ui.all.css');
		$doc->addStyleSheet(JURI::root().'components/com_joomd/assets/css/joomd.css');
		$doc->addStyleSheet(JURI::root().'components/com_joomd/assets/css/tipsy.css');
		
	}
	
	public static function uploadfile()
	{
		$doc =  JFactory::getDocument();
		
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.iframe-transport.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.fileupload.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.fileupload-ui.js');
		
	}
	
	public static function multiselect()
	{
		$doc =  JFactory::getDocument();
		
		$doc->addStyleSheet(JURI::root().'components/com_joomd/assets/css/jquery.multiselect.css');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.multiselect.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.multiselect.filter.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/prettify.js');
		
	}
	
	public static function printElement()
	{
		
		$doc =  JFactory::getDocument();
		
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.printElement.js');
		
	}
	
	public static function htmlbox()
	{
		
		$doc =  JFactory::getDocument();
		
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/htmlbox.colors.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/htmlbox.styles.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/xhtml.js');
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/htmlbox.full.js');
		
	}
	
	//Load the effect core file
	
	public static function effect($effect = 'core')
	{
		
		$doc =  JFactory::getDocument();
		
		// check whether it is already loaded
		static $core = false;
		static $bounce = false;
		static $clip = false;
		static $blind = false;
		static $drop = false;
		static $explode = false;
		static $fade = false;
		static $fold = false;
		static $highlight = false;
		static $pulsate = false;
		static $scale = false;
		static $shake = false;
		static $slide = false;
		static $transfer = false;
				
		if($$effect)
			return;
		
		$$effect = true;
				
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.effects.'.$effect.'.js');
		
	}
	
	//loads the shortcut icon slider in back end
	public static function loadpanel()
	{
	
		$doc =  JFactory::getDocument();
		
		$items = joomd::getApps();
		
		ob_start();
				
		echo '<div class="home_panel cpanelicon">';
		
		for($i=0;$i<count($items);$i++)	{
			
			if(is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$items[$i]->name.'/app_'.$items[$i]->name.'.php');
				
				$class = "JoomdApp".ucfirst($items[$i]->name);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'icon_display'))	{
						
						echo $class->icon_display();
						
					}
				
				}
				
			}
			
		}
		
		echo '<div class="clr"></div></div>';
				
		echo '<a class="trigger">Icons</a>';
		
		$html = ob_get_contents();
		
		ob_end_clean();
		
		$script = '$jd(function()	{
								
						$jd("body").prepend(\''.addslashes($html).'\');
						
						$jd(".icon").tipsy({live:true, html:true});
						
						$jd(".icon a").live("click", function(event)	{
							
							event.preventDefault();
							
							openShortwindow(this, {"abase":1});
							$jd( ".home_panel"  ).toggle( "drop");
							$jd("a.trigger").removeClass("open");
							
						});
						
				   });';
		
		joomdui::effect('.home_panel', 'a.trigger', array('selectedEffect'=>'drop', 'displaytime'=>200));
		
		$doc->addScriptDeclaration($script);
	
	}
	
}