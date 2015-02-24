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
$imageevent = "";
// Note. It is important to remove spaces between elements.
if ($item->deeper AND $params->get('eventtarget') == 'link') {
    // $class = 'toggler toggler_'.$item->level.' '.$item->anchor_css.' ';
	$class = 'toggler toggler_'.($item->level-($params->get('startLevel')-1)).' '.$item->anchor_css.' ';
	if ($params->get('eventtype') == 'click')
		$item->flink = 'javascript:void(0);';
} elseif($item->deeper AND $params->get('eventtarget') == 'image') {
    $class = $item->anchor_css ? $item->anchor_css.' ' : '';
	$imageevent = "<img src=".JURI::root() . $params->get('imageplus', 'modules/mod_accordeonck/assets/plus.png') . " class=\"toggler toggler_".($item->level-($params->get('startLevel')-1)) . "\" align=\"" . $imageposition . "\"/>";
} else {
    $class = $item->anchor_css ? $item->anchor_css.' ' : '';
}

if (	$item->type == 'alias' &&
			in_array($item->params->get('aliasoptions'),$path)
		||	in_array($item->id, $path)) {
	  $class .= 'isactive ';
	}

	$class = (isset($class) AND $class) ? 'class="' . $class . '"' : '';
	
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
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

switch ($item->browserNav) :
	default:
	case 0:
echo $imageevent; ?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" <?php echo $title.$style; ?>><?php echo $linktype; ?></a><?php
		break;
	case 1:
		// _blank
echo $imageevent; ?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title.$style; ?>><?php echo $linktype; ?></a><?php
		break;
	case 2:
		// window.open
		$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$params->get('window_open');
echo $imageevent; ?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','<?php echo $attribs;?>');return false;" <?php echo $title.$style; ?>><?php echo $linktype; ?></a><?php
		break;
endswitch;
