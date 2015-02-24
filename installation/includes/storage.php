<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer temporary storage
 */

defined('_ABI') or die('Direct access is not allowed');

class ABIStorage
{
	/**
	 * Chooses the data storage method (file/session)
	 * @var string
	 */
	var $_method;

	/**
	 * Where temporary data is stored when using file storage
	 * @var string
	 */
	var $_storagefile;

	/**
	 * The temporary data, as an associative array
	 * @var array
	 */
	var $_data;

	/**
	 * Singleton implementation
	 * @return ABIStorage
	 */
	static function &getInstance()
	{
		static $instance = null;

		if(!is_object($instance))
		{
			$instance = new ABIStorage();
		}

		return $instance;
	}

	function ABIStorage()
	{
		if(is_writable(ini_get('session.save_path')))
		{
			$this->_method = 'session';
		}
		else
		{
			$storagefile = JPATH_INSTALLATION.'/storagedata.dat';
			$this->_storagefile = $storagefile;
			$this->_method = 'file';
		}

		$this->loadData();
	}

	/**
	 * Is the storage class able to save the data between page loads?
	 * @return bool True if everything works properly
	 */
	function isStorageWorking()
	{
		switch($this->_method)
		{
			case 'file':
				if(!file_exists($this->_storagefile)) {
					$dummy = '';
					$fp = @fopen($this->_storagefile,'wb');
					if($fp === false) {
						$result = false;
					} else {
						@fclose($fp);
						@unlink($this->_storagefile);
						$result = true;
					}
					return $result;
				} else {
					return @is_writable($this->_storagefile);
				}
				break;

			case 'session':
				return @is_writable(ini_get('session.save_path'));
				break;
		}

		return false;
	}

	/**
	 * Resets the internal storage
	 */
	function reset()
	{
		$this->_data = array();
	}

	/**
	 * Loads temporary data from a file or a session variable (auto detect)
	 */
	function loadData()
	{
		switch($this->_method)
		{
			case 'file':
				$this->_load_file();
				break;

			case 'session':
				$this->_load_session();
				break;
		}
	}

	/**
	 * Saves temporary data to a file or a session variable (auto detect)
	 */
	function saveData()
	{
		switch($this->_method)
		{
			case 'file':
				$this->_save_file();
				break;

			case 'session':
				$this->_save_session();
				break;
		}
	}

	/**
	 * Sets or updates the value of a temporary variable
	 * @param $key string The variable's name
	 * @param $value string The value to store
	 */
	function set($key, $value)
	{
		$this->_data[$key] = $value;
	}

	/**
	 * Returns the value of a temporary variable
	 * @param $key string The variable's name
	 * @param $default mixed The default value, null if not specified
	 * @return mixed The variable's value
	 */
	function get($key, $default = null)
	{
		if(array_key_exists($key, $this->_data))
		{
			return $this->_data[$key];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Removes a variable from the storage
	 * @param $key string The name of the variable to remove
	 */
	function remove($key)
	{
		if(array_key_exists($key, $this->_data))
		{
			unset($this->_data[$key]);
		}
	}

	/**
	 * Loads temporary data from a file
	 */
	function _load_file()
	{
		$file = @fopen($this->_storagefile,'rb');
		if($file === false)
		{
			$this->_data = array();
			return;
		}
		else
		{
			$raw_data = fread($file, filesize($this->_storagefile));
		}
		if(@strlen($raw_data) > 0)
		{
			$this->decode_data($raw_data);
		}
		else
		{
			$this->_data = array();
		}
	}

	/**
	 * Saves temporary data to a file
	 */
	function _save_file()
	{
		$data = $this->encode_data();
		$fp = @fopen($this->_storagefile,'wb');
		@fwrite($fp, $data);
		@fclose($fp);
	}

	/**
	 * Loads temporary data from a session variable
	 */
	function _load_session()
	{
		session_start();
		if( isset($_SESSION['abidata']) )
		{
			$data = $_SESSION['abidata'];
		}
		else
		{
			$data = '';
		}
		$this->decode_data($data);
	}

	/**
	 * Saves temporary data to a session variable
	 */
	function _save_session()
	{
		$_SESSION['abidata'] = $this->encode_data();
		//session_write_close();
	}

	/**
	 * Returns a serialized form of the temporary data
	 * @return string The serialized data
	 */
	function encode_data()
	{
		$data = serialize($this->_data);
		if( function_exists('base64_encode') && function_exists('base64_decode') )
		{
			// Prefer Basse64 ebcoding of data
			$data = base64_encode($data);
		}
		elseif( function_exists('convert_uuencode') && function_exists('convert_uudecode') )
		{
			// UUEncode is just as good if Base64 is not available
			$data = convert_uuencode( $data );
		}
		elseif( function_exists('bin2hex') && function_exists('pack') )
		{
			// Ugh! Let's use plain hex encoding
			$data = bin2hex($data);
		}
		// Note: on an anal server we might end up with raw data; all bets are off!

		return $data;
	}

	/**
	 * Loads the temporary data off their serialized form
	 * @param $data
	 */
	function decode_data($data)
	{
		$this->_data = array();

		if( function_exists('base64_encode') && function_exists('base64_decode') )
		{
			// Prefer Basse64 ebcoding of data
			$data = base64_decode($data);
		}
		elseif( function_exists('convert_uuencode') && function_exists('convert_uudecode') )
		{
			// UUEncode is just as good if Base64 is not available
			$data = convert_uudecode( $data );
		}
		elseif( function_exists('bin2hex') && function_exists('pack') )
		{
			// Ugh! Let's use plain hex encoding
			$data = pack("H*" , $data);
		}
		// Note: on an anal server we might end up with raw data; all bets are off!

		$temp = @unserialize($data);
		if(is_array($temp))
		{
			$this->_data = $temp;
		}
		else
		{
			$this->_data = array();
		}
	}
}