<?php
/**
* Shadowbox Joomla! Plugin v4.0.0
*
* @author Joe Palmer
* @copyright Copyright (C) 2009/2012 SoftForge Ltd. - http://www.softforge.co.uk
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JApplication::registerEvent( 'onAfterDispatch', 'plgSystemShadowbox' );

/**
* Plugin that adds the shadowbox effect
*/
function plgSystemShadowbox() {

	$app      =& JFactory::getApplication();
	$document =& JFactory::getDocument();
	$version = new JVersion;
	$joomla = $version->getShortVersion();

	// check if site is active
	if (!($app->getName() == 'site' && is_a($document, 'JDocumentHTML'))) {
		return true;
	}
	
	// get plugin info
	jimport( 'joomla.html.parameter' );
	$plugin =& JPluginHelper::getPlugin('system', 'shadowbox');
 	$params = new JParameter($plugin->params);
	if(substr($joomla,0,3) == '1.5'){
		$plugin_base = JURI::base() . 'plugins/system/shadowbox/';
	} else {
		$plugin_base = JURI::base() . 'plugins/system/shadowbox/shadowbox/';
	}

	// check whether plugin has been unpublished
	if (!$params->get('enabled', 1)) {
		return true;
	}

	// get parameters
	$imgPlayer = $params->get('imgPlayer', '1');
	$swfPlayer = $params->get('swfPlayer', '0');
	$flvPlayer = $params->get('flvPlayer', '0');
	$qtPlayer = $params->get('qtPlayer', '0');
	$wmpPlayer = $params->get('wmpPlayer', '0');
	$iframePlayer = $params->get('iframePlayer', '0');
	$htmlPlayer = $params->get('htmlPlayer', '0');
	$jsCompress = $params->get('jsCompress', '1');
	$adapter = $params->get('adapter', 'base');
	$animate = $params->get('animate', '1');
	$animateFade = $params->get('animateFade', '1');
	$animSequence = $params->get('animSequence', 'sync');
	$autoDimensions = $params->get('autoDimensions', '0');
	$autoplayMovies = $params->get('autoplayMovies', '1');
	$continuous = $params->get('continuous', '0');
	$counterLimit = $params->get('counterLimit', '10');
	$counterType = $params->get('counterType', 'default');
	$displayCounter = $params->get('displayCounter', '1');
	$displayNav = $params->get('displayNav', '1');
	$enableKeys = $params->get('enableKeys', '1');
	$fadeDuration = $params->get('fadeDuration', '0.35');
	$handleOversize = $params->get('handleOversize', 'resize');
	$handleUnsupported = $params->get('handleUnsupported', 'link');
	$initialHeight = $params->get('initialHeight', '160');
	$initialWidth = $params->get('initialWidth', '320');
	$language = $params->get('language', 'en');
	$modal = $params->get('modal', '1');
	$overlayColor = $params->get('overlayColor', '#000');
	$overlayOpacity = $params->get('overlayOpacity', '0.8');
	$resizeDuration = $params->get('resizeDuration', '0.35');
	$showMovieControls = $params->get('showMovieControls', '1');
	$showOverlay = $params->get('showOverlay', '1');
	$slideshowDelay = $params->get('slideshowDelay', '0');
	$viewportPadding = $params->get('viewportPadding', '20');
	$skipSetup = $params->get('skipSetup', '1');
	$useSizzle = $params->get('useSizzle', '1');
	
	// build javascript for <head>
	$players = '';
	if ($imgPlayer) $players .= 'img-';
	if ($swfPlayer) $players .= 'swf-';
	if ($flvPlayer) $players .= 'flv-';
	if ($qtPlayer) $players .= 'qt-';
	if ($wmpPlayer) $players .= 'wmp-';
	if ($iframePlayer) $players .= 'iframe-';
	if ($htmlPlayer) $players .= 'html-';
	
	// build other parameters
	$parameters = '';
	if (!$animate) $parameters .= 'animate: false, ';
	if (!$animateFade) $parameters .= 'animateFade: false, ';
	if ($animSequence != "sync") $parameters .= 'animSequence: "'.$animSequence.'", ';
	if (!$autoplayMovies) $parameters .= 'autoplayMovies: false, ';
	if ($autoDimensions) $parameters .= 'autoDimensions: true, ';
	if ($continuous) $parameters .= 'continuous: true, ';
	if ($counterLimit != 10) $parameters .= 'counterLimit: '.$counterLimit.', ';
	if ($counterType != "default") $parameters .= 'counterType: "'.$counterType.'", ';
	if (!$displayCounter) $parameters .= 'displayCounter: false, ';
	if (!$displayNav) $parameters .= 'displayNav: false, ';
	if (!$enableKeys) $parameters .= 'enableKeys: false, ';
	if ($fadeDuration != 0.35) $parameters .= 'fadeDuration: '.$fadeDuration.', ';
	if ($handleOversize != "resize") $parameters .= 'handleOversize: "'.$handleOversize.'", ';
	if ($handleUnsupported != "link") $parameters .= 'handleUnsupported: "'.$handleUnsupported.'", ';
	if ($initialHeight != 160) $parameters .= 'initialHeight: '.$initialHeight.', ';
	if ($initialWidth != 320) $parameters .= 'initialWidth: '.$initialWidth.', ';
	if (!$modal) $parameters .= 'modal: false, ';
	if ($overlayColor != "#000") $parameters .= 'overlayColor: "'.$overlayColor.'", ';
	if ($overlayOpacity != 0.8) $parameters .= 'overlayOpacity: '.$overlayOpacity.', ';
	if ($resizeDuration != 0.35) $parameters .= 'resizeDuration: '.$resizeDuration.', ';
	if (!$showMovieControls) $parameters .= 'showMovieControls: false, ';
	if (!$showOverlay) $parameters .= 'showOverlay: false, ';
	if ($slideshowDelay != 0) $parameters .= 'slideshowDelay: '.$slideshowDelay.', ';
	if ($viewportPadding != 20) $parameters .= 'viewportPadding: '.$viewportPadding.', ';
	if (!$skipSetup) $parameters .= 'skipSetup: false, ';
	
	// add parameters if necessary
	$javascript = '';
	if ($parameters != '') {
		$javascript .= '<script type="text/javascript">Shadowbox.init({ '.substr($parameters, 0, -2).' });</script>';
	} else {
		$javascript .= '<script type="text/javascript">Shadowbox.init();</script>';
	}
	
	// build shadowbox link
	$url = '';
	if($jsCompress) {
		$url = $plugin_base.'min/index.php?g=sb&ad='.$adapter.'&lan='.$language.'&play='.substr($players, 0, -1);
		if (!$useSizzle) $url .= '&css=no';
	} else {
		$url = $plugin_base.'examples/build/shadowbox.js';
	}

	// inject javascript and css into <head>
	$document->addStyleSheet($plugin_base . 'examples/build/shadowbox.css');
	$document->addScript($url);
	$document->addCustomTag($javascript);
	
}