<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer automation
 */

defined('_ABI') or die('Direct access is not allowed');

class ABIAutomation
{
	/**
	 * @var bool Is there automation information available?
	 */
	var $_hasAutomation = false;

	/**
	 * @var array The abiautomation.ini contents, in array format
	 */
	var $_automation = array();

	/**
	 * Singleton implementation
	 * @return ABIAutomation
	 */
	static function &getInstance()
	{
		static $instance;

		if(empty($instance))
		{
			$instance = new ABIAutomation();
		}

		return $instance;
	}

	/**
	 * Loads and parses the automation INI file
	 * @return ABIAutomation
	 */
	function ABIAutomation()
	{
		// Initialize
		$this->_hasAutomation = false;
		$this->_automation = array();

		$filenames = array('abiautomation.ini', 'kickstart.ini', 'jpi4automation.ini');

		foreach($filenames as $fn_base)
		{
			// Try to load the abiautomation.ini file
			$fn_rel = '../'.$fn_base;
			$fn_abs = JPATH_SITE.'/'.$fn_base;

			// Can I access the relative path?
			if(@file_exists($fn_rel))
			{
				$filename = $fn_rel; // Yes, use relative path
			} else {
				$filename = $fn_abs; // No, use absolute path
			}
			if(@file_exists($filename))
			{
				$this->_automation = _parse_ini_file($filename, true);
				if(!isset($this->_automation['abi']))
				{
					$this->_automation = array();
				}
				else
				{
					$this->_hasAutomation = true;
					break;
				}
			}
		}

	}

	/**
	 * Do we have automation?
	 * @return bool True if abiautomation.ini exists and has a abi section
	 */
	function hasAutomation()
	{
		return $this->_hasAutomation;
	}

	/**
	 * Returns an automation section. If the section doesn't exist, it returns an empty array.
	 * @param string $section [optional] The name of the section to load, defaults to 'abi'
	 * @return array
	 */
	function getSection($section = 'abi')
	{
		if(!$this->_hasAutomation)
		{
			return array();
		}
		else
		{
			if(isset($this->_automation[$section]))
			{
				return $this->_automation[$section];
			} else {
				return array();
			}
		}
	}

}