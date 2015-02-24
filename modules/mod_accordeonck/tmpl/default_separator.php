<?php
/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * Adapted from the original mod_menu on Joomla.site - Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * */

// No direct access.
defined('_JEXEC') or die;

// retrieve the style params
$fontsize = $params->get('fontsize'.$item->level) ? 'font-size:'.$params->get('fontsize'.$item->level).';' : '';

$style = ($fontsize AND $params->get('usestyles')) ? ' style="'.$fontsize.'"' : '';

// Note. It is important to remove spaces between elements.
//$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
$imageevent = "";
if ($item->deeper AND $params->get('eventtarget') == 'link') {
    // $class = 'toggler toggler_'.$item->level.' '.$item->anchor_css.' ';
	$class = 'toggler toggler_'.($item->level-($params->get('startLevel')-1)).' '.$item->anchor_css.' ';
} elseif($item->deeper AND $params->get('eventtarget') == 'image') {
    $class = $item->anchor_css ? $item->anchor_css.' ' : '';
	$imageevent = "<img src=".JURI::root() . $params->get('imageplus', 'modules/mod_accordeonck/assets/plus.png') . " class=\"toggler toggler_".($item->level-($params->get('startLevel')-1)) . "\" align=\"" . $imageposition . "\"/>";
} else {
    $class = $item->anchor_css ? $item->anchor_css.' ' : '';
}
$class = (isset($class) AND $class) ? 'class="' . $class . '"' : '';

if ($item->menu_image) {
		if ($params->get('imgalignement', 'none') != 'none') {
			$imgalignement = ( $params->get('imgalignement') == 'left' ) ? ' align="left"' : ' align="right"' ;
		} else {
			$imgalignement = '';
		}
		$item->params->get('menu_text', 1 ) ? 
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'"'. $imgalignement .' /><span class="image-title">'.$item->ftitle.$item->desc.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'"'. $imgalignement .' />';
} 
else { 
	$linktype = $item->ftitle.$item->desc;
}

echo $imageevent; ?><a <?php echo $class; ?>href="javascript:void(0);"<?php echo $style; ?>><span class="separator"><?php echo $linktype; ?></span></a>

