<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Translation Class
 */

defined('_ABI') or die('Direct access is not allowed');

/**
 * Akeeba Translation Class
 */
class ABIText
{
	/**
	 * An associative array of translation strings
	 * @var array
	 */
	var $_lang;

	/**
	 * Singleton implementation
	 * @return ABIText
	 */
	static function &getInstance()
	{
		static $instance;

		if(!is_object($instance))
		{
			$instance = new ABIText();
		}

		return $instance;
	}

	/**
	 * Class constructor. Loads the translation files for the installer,
	 * honouring user's browser settings
	 * @return ABIText
	 */
	function ABIText()
	{
		// Load default language (English)
		$langEnglish = $this->parse_lang_file(JPATH_INSTALLATION.'/lang/en.ini');

		// Try to get user's preffered language (set in browser's settings and transmitted through the request)
		$prefLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		$prefFileName = JPATH_INSTALLATION.'/lang/'.$prefLang.'.ini';
		if( file_exists($prefFileName) && ($prefLang != 'en') ) {
			$langLocal = $this->parse_lang_file($prefFileName);
			$this->_lang = array_merge($langEnglish, $langLocal);
			unset( $langLocal );
			unset( $langEnglish );
		} else {
			$this->_lang = $langEnglish;
		}
	}
	
	function parse_lang_file($filename)
	{
		$ret = array();
		if(!file_exists($filename)) return array();
		$lines = file($filename);
		foreach($lines as $line)
		{
			$line = ltrim($line);
			if( (substr($line,0,1) == '#') || (substr($line,0,2) == '//') ) continue;
			$entries = explode('=',$line,2);
			if(isset($entries[1])) $ret[$entries[0]] = rtrim($entries[1],"\n\r");
		}
		return $ret;
	}

	/**
	 * Performs the real translation of the static _() function
	 * @param $key string Translation key
	 * @return string Translation text
	 */
	function _realTranslate($key)
	{
		if(array_key_exists($key, $this->_lang))
		{
			return $this->_lang[$key];
		}
		else
		{
			return $key;
		}
	}

	/**
	 * Returns the translation text of a given key
	 * @param $key string Translation key
	 * @return string Translation text
	 * @static
	 */
	public static function _($key)
	{
		static $instance;

		if(!is_object($instance))
		{
			$instance =& ABIText::getInstance();
		}

		return $instance->_realTranslate($key);
	}

}