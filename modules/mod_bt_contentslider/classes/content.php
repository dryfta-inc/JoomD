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
//Get to process link
jimport('joomla.application.component.model');
JModel::addIncludePath(JPATH_SITE . '/components/com_content/models');
require_once JPATH_SITE . DS . 'modules' . DS . 'mod_bt_contentslider' . DS . 'helpers' . DS . 'route_content.php';
require_once JPATH_SITE . DS . 'modules' . DS . 'mod_bt_contentslider' . DS . 'classes' . DS . 'btsource.php';

/**
 * class BtContentDataSource
 */
class BtContentDataSource extends BTSource {
	function getList() {
		$params = &$this->_params;

		$formatter = $params->get('style_displaying', 'title');
		$titleMaxChars = $params->get('title_max_chars', '100');
		$limit_title_by = $params->get('limit_title_by', 'char');
		$descriptionMaxChars = $params->get('description_max_chars', 100);
		$limitDescriptionBy = $params->get('limit_description_by', 'char');
		$isThumb = $params->get('image_thumb', 1);
		$replacer = $params->get('replacer', '...');
		$isStrips = $params->get("auto_strip_tags", 1);

		if ($isStrips) {
			$allow_tags = $params->get("allow_tags", array());
			$stringtags = '';
			if (!is_array($allow_tags)) {
				$allow_tags = explode(',', $allow_tags);
			}
			foreach ($allow_tags as $tag) {
				$stringtags .= '<' . $tag . '>';
			}
		}
		if (!$params->get('default_thumb', 1)) {
			$this->_defaultThumb = '';
		}
		$itemid = $params->get('itemid', 0);

		$ordering = $params->get('ordering', 'created-asc');

		$db = JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();

		$appParams = $app->getParams();

		$model->setState('params', $appParams);

		$model->setState('list.select', 'a.fulltext, a.id, a.title, a.alias, a.title_alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,' . ' a.modified, a.modified_by,a.publish_up, a.publish_down, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' . ' a.hits, a.featured,' . ' LENGTH(a.fulltext) AS readmore');
		// Set the filters based on the module params

		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('limit_items', 12));
		$model->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		//var_dump($access);
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		//var_dump($authorised);
		$model->setState('filter.access', $access);

		$source = trim($params->get('source', 'category'));

		//if category
		if ($source == 'category') {
			// Category filter
			$model->setState('filter.category_id', $params->get('category', array()));
			//esle article_ids
		}
		else {
			$ids = preg_split('/,/', $params->get('article_ids', ''));
			$tmp = array();
			foreach ($ids as $id) {
				$tmp[] = (int) trim($id);
			}
			$model->setState('filter.article_id', $tmp);
		}

		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id')) {
			case 'by_me':
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;
			case 0:
				break;

			default:
				$model->setState('filter.author_id', (int) $params->get('user_id'));
				break;
		}

		// Filter by language
		//$model->setState('filter.language',$app->getLanguageFilter());

		//  Featured switch
		switch ($params->get('show_featured')) {
			case 3:
				$model->setState('filter.featured', 'only');
				break;
			case 2:
				$model->setState('filter.featured', 'hide');
				break;
			default:
				$model->setState('filter.featured', 'show');
				break;
		}

		// Set ordering
		$ordering = explode('-', $ordering);
		if (trim($ordering[0]) == 'rand') {
			$model->setState('list.ordering', ' RAND() ');
		}
		else {
			$model->setState('list.ordering', 'a.' . $ordering[0]);
			$model->setState('list.direction', $ordering[1]);
		}

		$items = $model->getItems();

		foreach ($items as &$item) {

			//var_dump($item);

			$item->slug = $item->id . ':' . $item->alias;

			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the article
				//Item link
				$item->link = JRoute::_(BTContentSliderRoute::getArticleRoute($item->slug, $item->catslug, $itemid));
			}
			else {

				$item->link = JRoute::_('index.php?option=com_user&view=login');
			}
			$item->date = JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2'));

			$item->thumbnail = '';
			if ($params->get('show_image', 1)) {
				$item = $this->generateImages($item, $isThumb);
			}

			//$item->fulltext = $item->introtext;
			//$item->introtext = JHtml::_('content.prepare', $item->introtext);

			//category Link
			$item->categoryLink = JRoute::_(BTContentSliderRoute::getCategoryRoute($item->catid, $itemid));

			if ($limit_title_by == 'word' && $titleMaxChars > 0) {

				$item->title_cut = self::substrword($item->title, $titleMaxChars, $replacer, $isStrips);

			}
			elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {
				$item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);
			}

			if ($limitDescriptionBy == 'word' && $descriptionMaxChars > 0) {

				$item->description = self::substrword($item->introtext, $descriptionMaxChars, $replacer, $isStrips, $stringtags);

			}
			elseif ($limitDescriptionBy == 'char' && $descriptionMaxChars > 0) {

				$item->description = self::substring($item->introtext, $descriptionMaxChars, $replacer, $isStrips, $stringtags);
			}
			$item->authorLink = "#";
		}
		return $items;
	}
}

?>