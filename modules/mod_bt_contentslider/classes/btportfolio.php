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
require_once JPATH_SITE . DS . 'modules' . DS . 'mod_bt_contentslider' . DS . 'classes' . DS . 'btsource.php';

/**
 * BtK2DataSource Class
 */
class BtBtPortfolioDataSource extends BTSource {

	public function getList() {
		if (!is_file(JPATH_SITE . DS . "components" . DS . "com_bt_portfolio" . DS . "bt_portfolio.php")) {
			return array();
		}

		$params = &$this->_params;

		/* title */
		$show_title = $params->get('show_title', 1);

		$titleMaxChars = $params->get('title_max_chars', '100');
		$limit_title_by = $params->get('limit_title_by', 'char');
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
		/* intro */
		$show_intro = $params->get('show_intro', 1);

		$maxDesciption = $params->get('description_max_chars', 100);

		$limitDescriptionBy = $params->get('limit_description_by', 'char');

		/* open target */

		$openTarget = $params->get('open_target', 'parent');

		//select from
		$condition = $this->buildConditionQuery();
		//var_dump($condition);

		$ordering = $params->get('ordering', 'created-desc');
		if ($ordering == 'publish_up-asc')
			$ordering = 'created-desc';

		$limit = $params->get('limit_items', 12);

		//ordering_asc -> ordering asc
		//$ordering      = str_replace( '_', '  ', $ordering );

		// Set ordering
		$ordering = explode('-', $ordering);
		if (trim($ordering[0]) == 'rand') {
			$ordering = ' RAND() ';
		}
		else {
			$ordering = $ordering[0] . ' ' . $ordering[1];
		}

		//var_dump($ordering);

		//check user access to articles
		$user = &JFactory::getUser();
		$my = &JFactory::getUser();
		$aid = $my->get('aid', 0);
		//var_dump($aid);

		//
		$isThumb = $params->get('image_thumb', 1);

		$thumbWidth = (int) $params->get('thumbnail_width', 280);
		$thumbHeight = (int) $params->get('thumbnail_height', 150);

		$isStripedTags = $params->get('auto_strip_tags', 0);

		$extraURL = $params->get('open_target') != 'modalbox' ? '' : '&tmpl=component';

		$db = &JFactory::getDBO();
		$date = &JFactory::getDate();
		$now = $date->toMySQL();

		$dateFormat = $params->get('date_format', 'DATE_FORMAT_LC3');

		$show_author = $params->get('show_author', 0);

		$query = "SELECT DISTINCT a.*, c.title as category_title,
						c.id as catid" . " FROM #__bt_portfolios as a" . " LEFT JOIN #__bt_portfolio_categories c ON a.catids like CONCAT('%',c.id,'%') ";

		$query .= " WHERE a.published = 1" . " AND a.access IN(" . implode(',', $user->authorisedLevels()) . ")" . " AND c.published = 1" . " AND c.access IN(" . implode(',', $user->authorisedLevels()) . ")";
		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id')) {
			case 'by_me':
				$query .= 'AND a.created_by = ' . $userId;
				break;
			case 'not_me':
				$query .= 'AND a.created_by != ' . $userId;
				break;
			case 0:
				break;
			default:
				$query .= 'AND a.created_by = ' . $userId;
				break;
		}

		if ($params->get('show_featured', '1') == 2) {
			$query .= " AND a.featured != 1";
		}
		elseif ($params->get('show_featured', '1') == 3) {
			$query .= " AND a.featured = 1";
		}

		$jnow = &JFactory::getDate();
		$now = $jnow->toMySQL();
		$nullDate = $db->getNullDate();

		$query .= $condition . ' ORDER BY ' . $ordering;
		$query .= $limit ? ' LIMIT ' . $limit : '';

		//var_dump($query);die();

		$db->setQuery($query);

		$data = $db->loadObjectlist();

		if (empty($data))
			return array();

		foreach ($data as $key => &$item) {

			if (in_array($item->access, $user->authorisedLevels())) {

				$item->link = JRoute::_("index.php?option=com_bt_portfolio&view=portfolio&id=" . $item->id . "&Itemid=" . $itemid);
			}
			else {
				$item->link = JRoute::_('index.php?option=com_user&view=login');
			}

			$item->date = JHtml::_('date', $item->created, JText::_($dateFormat));

			//title cut
			if ($limit_title_by == 'word' && $titleMaxChars > 0) {

				$item->title_cut = self::_substrword($item->title, $titleMaxChars, $replacer, $isStrips);

			}
			elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {

				$item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);

			}

			if ($limitDescriptionBy == 'word') {

				$item->description = self::_substrword($item->description, $maxDesciption, $stringtags);

			}
			else {

				$item->description = self::substring($item->description, $maxDesciption, $stringtags);

			}
			//var_dump($item); die();

			$item->categoryLink = JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $item->catid . "&Itemid=" . $itemid);

			//Get name author
			//If set get, else get username by userid
			if ($show_author) {
				$author = &JFactory::getUser($item->created_by);
				$item->author = $author->name;

			}
			$item->thumbnail = "";
			$item->authorLink = "#";
			$url_image = '';
			if ($params->get('show_image')) {
				$db->setQuery('Select filename from #__bt_portfolio_images WHERE item_id = ' . $item->id . ' and `default` = 1');
				$image = $db->loadResult();
				if (!$image) {
					if ($this->_defaultThumb)
						;
					$url_image = JURI::root() . $this->_defaultThumb;
				}
				else {
					$url_image = JURI::root() . 'images/bt_portfolio/' . $item->id . '/thumb/' . $image;
				}
				if ($isThumb)
					$item->thumbnail = self::renderThumb($url_image, $thumbWidth, $thumbHeight, '', $isThumb);
				else {
					$item->thumbnail = $url_image;
				}
			}

		}

		return $data;
	}

	public function buildConditionQuery() {

		$source = trim($this->_params->get('source', 'btportfolio_category'));

		if ($source == 'btportfolio_category') {

			$catids = $this->_params->get('btportfolio_category', '');

			if (!$catids) {
				return '';
			}
			$catids = !is_array($catids) ? $catids : '"' . implode('","', $catids) . '"';

			$condition = ' AND  c.id IN( ' . $catids . ' )';

		}
		else {
			if (!$this->_params->get('btportfolio_article_ids', '')) {
				return '';
			}

			$ids = preg_split('/,/', $this->_params->get('btportfolio_article_ids', ''));

			$tmp = array();

			foreach ($ids as $id) {

				$tmp[] = (int) trim($id);

			}

			$condition = " AND a.id IN('" . implode("','", $tmp) . "')";

		}

		return $condition;
	}
}
