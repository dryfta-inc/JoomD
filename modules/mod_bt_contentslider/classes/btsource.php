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
 * class BTSource
 */
require_once JPATH_SITE . DS . 'modules' . DS . 'mod_bt_contentslider' . DS . 'classes' . DS . 'images.php';

abstract class BTSource {

	public $_thumbnailPath = "";
	public $_thumbnaiURL = "";
	public $_defaultThumb = 'modules/mod_bt_contentslider/images/no-image.jpg';
	public $_imagesRendered = array('thumbnail' => array(), 'mainImage' => array());
	public $_params = array();

	public function __construct($params = null) {
		$this->_params = $params;
	}

	function setThumbPathInfo($path, $url) {
		$this->_thumbnailPath = $path;
		$this->_thumbnaiURL = $url;
		return $this;
	}

	public function setImagesRendered($name = array()) {
		$this->_imagesRendered = $name;
		return $this;
	}

	public function renderThumb($path, $width = 280, $height = 150, $isThumb = true) {
		if ($isThumb) {
			$path = str_replace(JURI::base(), '', $path);

			$imagSource = JPATH_SITE . DS . str_replace('/', DS, $path);
			$imagSource = urldecode($imagSource);
			if (file_exists($imagSource)) {

				$tmp = explode("/", $path);
				$imageName = $width . "x" . $height . "-" . $tmp[count($tmp) - 1];
				$thumbPath = $this->_thumbnailPath . $imageName;
				if (!file_exists($thumbPath)) {
					//create thumb
					BTImageHelper::createImage($imagSource, $thumbPath, $width, $height, true);

				}
				$path = $this->_thumbnaiURL . $imageName;
			}
		}
		//return path
		return $path;
	}

	/**
	 * parser a image in the content of article.
	 *
	 * @param.
	 * @return
	 */
	public function parseImages($row) {

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

	//create thumb and save link to item
	public function generateImages($item, $isThumb = true) {
		//
		$item = $this->parseImages($item);

		foreach ($this->_imagesRendered as $key => $value) {

			if ($item->{$key} && $image = $this->renderThumb($item->{$key}, $value[0], $value[1], $isThumb)) {
				$item->{$key} = $image;

			}
		}
		return $item;
	}

	/**
	 * Get a subtring with the max length setting.
	 *
	 * @param string $text;
	 * @param int $length limit characters showing;
	 * @param string $replacer;
	 * @return tring;
	 */
	public static function substring($text, $length = 100, $replacer = '...', $isStrips = true, $stringtags = '') {

		$string = $isStrips ? strip_tags($text, $stringtags) : $text;
		if (mb_strlen($string) < $length)
			return $string;
		$string = mb_substr($string, 0, $length);
		return $string . $replacer;
	}

	/**
	 * Get a subtring with the max word setting
	 *
	 * @param string $text;
	 * @param int $length limit characters showing;
	 * @param string $replacer;
	 * @return tring;
	 */

	public static function substrword($text, $length = 100, $replacer = '...', $isStrips = true, $stringtags = '') {
		$string = $isStrips ? strip_tags($text, $stringtags) : $text;

		$tmp = explode(" ", $string);

		if (count($tmp) < $length)
			return $string;

		$string = implode(" ", array_slice($tmp, 0, $length)) . $replacer;

		return $string;

	}
	abstract public function getList();
}

?>