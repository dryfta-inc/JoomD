<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: comconfig.php 428 2011-02-04 17:44:11Z nikosdion $
 */

/**
 * Retrieves the component configuration when Joomla! is not running, by digging directly into the Joomla!
 * database record.
 * @author Nicholas
 */
class AEUtilComconfig
{
	private static function loadConfig()
	{
		if(defined('JVERSION')) {
			$j16 = version_compare(JVERSION,'1.6.0','ge');
		} else {
			$j16 = false;
		}
		
		$db =& AEFactory::getDatabase();
		
		if(!$j16) {
			$sql = "SELECT ".$db->nameQuote('params')." FROM ".$db->nameQuote('#__components')." WHERE (".
				$db->nameQuote('link')." = ".$db->Quote('option=com_akeeba').") AND (".$db->nameQuote('parent')." = ".
				$db->Quote('0').")";
			$db->setQuery($sql);
			$config_ini  = $db->loadResult();
		} else {
			$config_ini = null;
		}

		if( $db->getErrorNum() || is_null($config_ini) )
		{
			// Maybe it's Joomla! 1.6?
			$sql = "SELECT ".$db->nameQuote('params')." FROM ".$db->nameQuote('#__extensions')." WHERE (".
				$db->nameQuote('type').' = '.$db->Quote('component').') AND ('.
				$db->nameQuote('element')." = ".$db->Quote('com_akeeba').")";
			$db->setQuery($sql);
			$config_ini  = $db->loadResult();

			// OK, Joomla! 1.6 stores values JSON-encoded so, what do I do? Right!
			$config_ini = json_decode($config_ini, true);
			return $config_ini;
		}

		return AEUtilINI::parse_ini_file($config_ini, false, true);
	}

	public static function getValue( $key, $default )
	{
		static $config;
		if(empty($config))
		{
			$config = self::loadConfig();
		}

		if(array_key_exists($key, $config))
		{
			return $config[$key];
		}
		else
		{
			return $default;
		}
	}
}