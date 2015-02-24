<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * Adapted from the original mod_menu on Joomla.site - Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class ModaccordeonckHelper {

    static function GetMenu(&$params) {
        // Initialise variables.
        $list = array();
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $app = JFactory::getApplication();
        $menu = $app->getMenu();

        // If no active menu, use default
        $active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

        $path = $active->tree;
        $start = (int) $params->get('startLevel');
        $end = (int) $params->get('endLevel');
        $showAll = 1;
        $maxdepth = $params->get('maxdepth');
        $items = $menu->getItems('menutype', $params->get('menutype'));

       $lastitem = 0;

        if ($items) {
			// load the list of all published modules
			$modulesList = ModaccordeonckHelper::CreateModulesList();
			
            foreach ($items as $i => $item) {

                if (($start && $start > $item->level)
                        || ($end && $item->level > $end)
                        || (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
                        || ($maxdepth && $item->level > $maxdepth)
                        || ($start > 1 && !in_array($item->tree[0], $path))
                ) {
                    unset($items[$i]);
                    continue;
                }

                $item->deeper = false;
                $item->shallower = false;
                $item->level_diff = 0;

                if (isset($items[$lastitem])) {
                    $items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
                    $items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
                    $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
                }

                $item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

                $lastitem = $i;
                $item->active = false;
                $item->flink = $item->link;

                switch ($item->type) {
                    case 'separator':
                        // No further action needed.
                        continue;

                    case 'url':
                        if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
                            // If this is an internal Joomla link, ensure the Itemid is set.
                            $item->flink = $item->link . '&Itemid=' . $item->id;
                        }
                        break;

                    case 'alias':
                        // If this is an alias use the item id stored in the parameters to make the link.
                        $item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
                        break;

                    default:
                        $router = JSite::getRouter();
                        if ($router->getMode() == JROUTER_MODE_SEF) {
                            $item->flink = 'index.php?Itemid=' . $item->id;
                        } else {
                            $item->flink .= '&Itemid=' . $item->id;
                        }
                        break;
                }

                if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
                    $item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
                } else {
                    $item->flink = JRoute::_($item->flink);
                }

                $item->ftitle = htmlspecialchars($item->title);
                $item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''));
                $item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''));
                $item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', '')) : '';

                // manage plugin parameters, need the plugin maximenu_ck_params to be installed and active
                //$item->description = $item->params->get('accordeonck_desc', '');
                $item->insertmodule = $item->params->get('accordeonck_insertmodule', 0);
                $item->module = $item->params->get('accordeonck_module', '');
                $item->content = '';

				// manage description
				$titreCK = explode("||", $item->ftitle);
				if (isset($titreCK[1])) {
					$item->desc = $titreCK[1];
				} else {
					$item->desc = '';
				}
				$item->ftitle = $titreCK[0];
				$item->desc = $item->params->get('accordeonck_desc', '') ? $item->params->get('accordeonck_desc', '') : $item->desc;
				$desccustomcss = ( $params->get('desccustomcss'.$item->level, 'display: block;color: #666;') AND $params->get('usestyles') ) ? ' style="'.$params->get('desccustomcss'.$item->level, 'display: block;color: #666;').'"' : '';
				if ($item->desc) {
                    $item->desc = '<span class="accordeonckdesc"'.$desccustomcss.'>' . $item->desc . '</span>';
                }

				// manage module
                if ($item->insertmodule AND $item->module) {
                    $item->content = '<div class="accordeonckmod">' . ModaccordeonckHelper::GenModuleById($item->module, $params, $modulesList) . '<div style="clear:both;"></div></div>';
                } else if (stristr($item->ftitle, '[modid=')) {
					$item->content = '<div class="accordeonckmod">' . ModaccordeonckHelper::GenModuleById($item->ftitle, $params, $modulesList) . '<div style="clear:both;"></div></div>';
				}
            }

            if (isset($items[$lastitem])) {
                $items[$lastitem]->deeper = (($start ? $start : 1) > $items[$lastitem]->level);
                $items[$lastitem]->shallower = (($start ? $start : 1) < $items[$lastitem]->level);
                $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
            }
        }

        return $items;
    }

    static function GenModuleById($title, &$params, &$modulesList) {
        $attribs['style'] = 'none';

        // get the title of the module to load
        $title = str_replace('[modid=', '', $title); 
		$title = str_replace(']', '', $title);
        $modtitle = $modulesList[$title]->title;
        $modname = $modulesList[$title]->module;
        $modname = preg_replace('/mod_/', '', $modname);

        // load the module
        if (JModuleHelper::isEnabled($modname)) {
            $module = JModuleHelper::getModule($modname, $modtitle);
            if ($module) {
				return JModuleHelper::renderModule($module, $attribs);
			} 
        }
		
		return "<p>No module found !</p>";
    }

    static function CreateModulesList() {
        $db = JFactory::getDBO();
        $query = "
			SELECT *
			FROM #__modules
			WHERE published=1
			ORDER BY id
			;";
        $db->setQuery($query);
        $modulesList = $db->loadObjectList('id');
        return $modulesList;
    }

}

?>