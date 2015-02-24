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
/**
 * BtK2DataSource Class
 */
require_once JPATH_SITE . DS . 'modules' . DS . 'mod_bt_contentslider' . DS . 'classes' . DS . 'btsource.php';

class BtK2DataSource extends BTSource {
	/**
	 * looking for image inside the media folder.
	 * heave size XS, XL, S, M, L, Generic
	 */
	public function lookingForK2Image($item, $size = 'XL') {
		//Image
		$item->imageK2Image = '';
		if (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $item->id) . '_' . $size . '.jpg'))
			$item->imageK2Image = JURI::base() . 'media/k2/items/cache/' . md5("Image" . $item->id) . '_' . $size . '.jpg';
		return $item;
	}

	/**
	 * parser a image in the content of article.
	 *
	 * @param poiter $row .
	 * @return void
	 */
	public function parseImages(&$row) {
		//Get first
		//Get k2 item image form media/k2/
		$row = $this->lookingForK2Image($row);
		if ($row->imageK2Image != '') {
			$row->thumbnail = $row->mainImage = $row->imageK2Image;
			return $row;
		}
		
		$text = $row->introtext;
		$row->thumbnail = $this->_defaultThumb;
		$row->mainImage = $this->_defaultThumb;
		$regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/Us";
		
		if (!$this->_params->get('check_image_exist',1)) {
			preg_match($regex, $text, $matches);
			$images = (count($matches)) ? $matches : array();
			if (count($images)) {
				$row->mainImage = $images[1];
				$row->thumbnail = $images[1];
				$row->introtext = str_replace($images[0], "", $row->introtext);
			}
		}
		else {
			preg_match_all($regex, $text, $matches);
			foreach ($matches[1] as $key => $match) {
				@$url = getimagesize($match);
				if (is_array($url)) {
					$row->mainImage = $match[1];
					$row->thumbnail = $match;
					$row->introtext = str_replace($matches[0][$key], "", $row->introtext);
					break;
				}
			}
		}

		return $row;

	}

	/*----------------------------------*/

	/**
	 * get the list of k2 items
	 *
	 * @param JParameter $params;
	 * @return Array
	 */
	public function getList() {
		if (!is_file(JPATH_SITE . DS . "components" . DS . "com_k2" . DS . "k2.php")) {
			return array();
		}

		$params = &$this->_params;

		/* title */
		$show_title = $params->get('show_title', 1);

		$titleMaxChars = $params->get('title_max_chars', '100');
		$enable_cache = $params->get('enable_cache', 1);
		$cache_time = $params->get('cache_time', 30);
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

		$ordering = $params->get('ordering', 'created-desc');

		$limit = $params->get('limit_items', 12);

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

		require_once JPATH_SITE . DS . 'modules' . DS . 'mod_bt_contentslider' . DS . 'helpers' . DS . 'route_k2.php';
		require_once(JPath::clean(JPATH_SITE . '/components/com_k2/helpers/utilities.php'));

		$query = "SELECT  a.*, c.name as category_title,
						c.id as categoryid, c.alias as categoryalias, c.params as categoryparams" . " FROM #__k2_items as a" . " LEFT JOIN #__k2_categories c ON c.id = a.catid";

		$query .= " WHERE a.published = 1" . " AND a.access IN(" . implode(',', $user->authorisedLevels()) . ")" . " AND a.trash = 0" . " AND c.published = 1" . " AND c.access IN(" . implode(',', $user->authorisedLevels()) . ")" . " AND c.trash = 0 ";
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

		$query .= " AND ( a.publish_up = " . $db->Quote($nullDate) . " OR a.publish_up <= " . $db->Quote($now) . " )";
		$query .= " AND ( a.publish_down = " . $db->Quote($nullDate) . " OR a.publish_down >= " . $db->Quote($now) . " )";

		$query .= $condition . ' ORDER BY ' . $ordering;
		$query .= $limit ? ' LIMIT ' . $limit : '';

		//var_dump($query);die();

		$db->setQuery($query);

		$data = $db->loadObjectlist();

		if (empty($data))
			return array();

		foreach ($data as $key => &$item) {

			if (in_array($item->access, $user->authorisedLevels())) {
				//get link k2
				$item->link = JRoute::_(BTContentSliderK2Route::getItemRoute($item->id . ':' . $item->alias, $item->catid . ':' . $item->categoryalias, $itemid));
			}
			else {
				$item->link = JRoute::_('index.php?option=com_user&view=login');
			}

			$item->date = JHtml::_('date', $item->created, JText::_($dateFormat));

			//title cut
			if ($limit_title_by == 'word' && $titleMaxChars > 0) {

				$item->title_cut = self::substrword($item->title, $titleMaxChars, $replacer, $isStrips);

			}
			elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {

				$item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);

			}

			if ($limitDescriptionBy == 'word') {

				$item->description = self::substrword($item->introtext, $maxDesciption, $stringtags);

			}
			else {

				$item->description = self::substring($item->introtext, $maxDesciption, $stringtags);

			}
			//var_dump($item); die();

			$item->categoryLink = urldecode(JRoute::_(BTContentSliderK2Route::getCategoryRoute($item->catid . ':' . urlencode($item->categoryalias)), $itemid));

			//Get name author
			//If set get, else get username by userid
			if ($show_author) {
				if (!empty($item->created_by_alias)) {
					$item->author = $item->created_by_alias;
				}
				else {
					$author = &JFactory::getUser($item->created_by);
					$item->author = $author->name;
				}
				$item->authorLink = JRoute::_(BTContentSliderK2Route::getUserRoute($item->created_by));
			}

			//
			$item->thumbnail = '';
			if ($params->get('show_image')) {
				$item = $this->generateImages($item, $isThumb);
			}

		}

		return $data;
	}

	public function buildConditionQuery() {

		$source = trim($this->_params->get('source', 'k2_category'));

		if ($source == 'k2_category') {

			$catids = $this->_params->get('k2_category', '');

			if (!$catids) {
				return '';
			}
			$catids = !is_array($catids) ? $catids : '"' . implode('","', $catids) . '"';

			$condition = ' AND  a.catid IN( ' . $catids . ' )';

		}
		else {
			if (!$this->_params->get('k2_article_ids', '')) {
				return '';
			}

			$ids = preg_split('/,/', $this->_params->get('k2_article_ids', ''));

			$tmp = array();

			foreach ($ids as $id) {

				$tmp[] = (int) trim($id);

			}
			$condition = " AND a.id IN('" . implode("','", $tmp) . "')";

		}
		return $condition;
	}
}
