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

// Note. It is important to remove spaces between elements.
?>
<div class="accordeonck <?php echo $params->get('moduleclass_sfx'); ?>">
<ul class="menu<?php echo $class_sfx;?>" id="<?php echo $menuID; ?>">
<?php
foreach ($list as $i => &$item) :
	$class = '';
	if ($item->id == $active_id) {
		$class .= 'current ';
	}

	if (	$item->type == 'alias' &&
			in_array($item->params->get('aliasoptions'),$path)
		||	in_array($item->id, $path)) {
	  $class .= 'active ';
	}
	if ($item->deeper) {
		$class .= 'deeper ';
	}
	
	if ($item->parent) {
		$class .= 'parent ';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	echo '<li id="item-'.$item->id.'"'.$class.'>';
        
	if ($item->content) {
        echo $item->content;
    } else {
		// Render the menu item.
		switch ($item->type) :
			case 'separator':
			case 'url':
			case 'component':
				require JModuleHelper::getLayoutPath('mod_accordeonck', 'default_'.$item->type);
				break;

			default:
				require JModuleHelper::getLayoutPath('mod_accordeonck', 'default_url');
				break;
		endswitch;
	}
	

	// The next item is deeper.
	if ($item->deeper) {
		echo '<ul class="content_'.($item->level-($params->get('startLevel')-1)).'">';
	}
	// The next item is shallower.
	else if ($item->shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul></div>
