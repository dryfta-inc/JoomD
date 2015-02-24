<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer configuration manipulation class
 */

defined('_ABI') or die('Direct access is not allowed');

class ABIConfiguration
{
	var $_config;

	/**
	 * Singleton implementation
	 * @return ABIConfiguration
	 */
	static function &getInstance()
	{
		static $instance;

		if(!is_object($instance))
		{
			$instance = new ABIConfiguration();
		}

		return $instance;
	}

	/**
	 * Constructor
	 * @return ABIConfiguration
	 */
	function ABIConfiguration()
	{
		// Try to fetch the configuration cached in the Storage
		$storage =& ABIStorage::getInstance();
		$this->_config = $storage->get('configuration', null);
		if(!is_array($this->_config))
		{
			// There was no cached configuration; load and cache
			$this->loadConfiguration();
			$storage->set('configuration', $this->_config);
		}
	}

	/**
	 * Gets a configuration value
	 * @param $key string The key (variable name)
	 * @param $default mixed The default value to return if the key doesn't exist
	 * @return mixed The variable's value
	 */
	function get($key, $default = null)
	{
		if(array_key_exists($key, $this->_config))
		{
			return $this->_config[$key];
		}
		else
		{
			// The key was not found. Set it with the default value, store and
			// return the default value
			$this->_config[$key] = $default;
			$storage =& ABIStorage::getInstance();
			$storage->set('configuration', $this->_config);
			return $default;
		}
	}

	/**
	 * Sets a variable's value and stores the configuration array in the global
	 * Storage.
	 * @param $key The variable name
	 * @param $value The value to set it to
	 */
	function set($key, $value)
	{
		$this->_config[$key] = $value;
		$storage =& ABIStorage::getInstance();
		$storage->set('configuration', $this->_config);
	}

	function loadConfiguration()
	{
		// Begin by assigning a default configuration
		$conf1 = array(
			'offline'				=> 0,
			'offline_message'		=> 'This site is down for maintenance.<br /> Please check back again soon.',
			'sitename'				=> 'Joomla!',
			'editor'				=> 'tinymce',
			'list_limit'			=> '20',
			'debug'					=> '0',
			'debug_lang'			=> '0',
			'dbtype'				=> 'mysqli',
			'host'					=> 'localhost',
			'user'					=> 'jos_',
			'password'				=> '',
			'db'					=> '',
			'dbprefix'				=> '',
			'live_site'				=> '',
			'secret'				=> 'FBVtggIk5lAzEU9H',
			'gzip'					=> '0',
			'error_reporting'		=> '-1',
			'helpurl'				=> 'http://help.joomla.org',
			'ftp_host'				=> '',
			'ftp_port'				=> '',
			'ftp_user'				=> '',
			'ftp_pass'				=> '',
			'ftp_root'				=> '',
			'ftp_enable'			=> '',
			'offset'				=> '0',
			'mailer'				=> 'mail',
			'mailfrom'				=> '',
			'fromname'				=> '',
			'sendmail'				=> '/usr/sbin/sendmail',
			'smtpauth'				=> '0',
			'smtpuser'				=> '',
			'smtppass'				=> '',
			'smtphost'				=> 'localhost',
			'caching'				=> '0',
			'cache_handler'			=> 'file',
			'cachetime'				=> '15',
			'MetaDesc'				=> 'Joomla! - the dynamic portal engine and content management system',
			'MetaKeys'				=> 'joomla, Joomla',
			'MetaTitle'				=> '1',
			'MetaAuthor'			=> '1',
			'sef'					=> '0',
			'sef_rewrite'			=> '0',
			'sef_suffix'			=> '',
			'feed_limit'			=> '10',
			'log_path'				=> '/var/logs',
			'tmp_path'				=> '/tmp',
			'lifetime'				=> '15',
			'session_handler'		=> 'database',
			'force_ssl'				=> '0',
			'feed_email'			=> 'author'
		);
		// Merge Joomla! version specific configuration keys (different for 1.5
		// and 1.6).
		$jversion = $this->joomlaVersion();
		$extraconf = array();
		if(version_compare($jversion, '1.6.0', 'ge')) {
			$extraconf = array(
				'access'				=> '1',
				'offset_user'			=> 'UTC',
				'smtpsecure'			=> 'none',
				'smtpport'				=> '25',
				'unicodeslugs'			=> '0',
				'MetaRights'			=> '',
				'sitename_pagetitles'	=> '0',
				'cookie_domain'			=> '',
				'cookie_path'			=> ''
			);
		} else {
			$extraconf = array(
				'legacy'				=> '0',
				'xmlrpc_server'			=> '1',
				'debug_db'				=> '0'
			);
		}
		$conf1 = array_merge($conf1, $extraconf);
		
		// Next up read the configuration.php from the site
		if(file_exists(JPATH_SITE.'/configuration.php'))
		{
			$conf2 = $this->parseFile(JPATH_SITE.'/configuration.php');
		}
		elseif(file_exists('../configuration.php')) {
			$conf2 = $this->parseFile('../configuration.php');
		}
		else
		{
			$conf2 = array();
		}

		// We will merge both arrays by performing an iteration, overriding conf1's
		// elements by those of conf2.
		if( is_array($conf2) && count($conf2) )
		{
			foreach($conf1 as $key => $value)
			{
				if(!array_key_exists($key, $conf2))
				{
					$conf2[$key] = $value;
				}
			}
		}

		// $conf2 now holds the merged data. Save them in the class variable.
		$this->_config = $conf2;
	}

	/**
	 * Returns the contents of the new configuration.php file
	 * @return string
	 */
	function getConfiguration()
	{
		// Check the Joomla! version; 1.6 uses "public", 1.5 uses "var"
		$jversion = $this->joomlaVersion();
		$varkey = version_compare($jversion, '1.6.0', 'ge') ? 'public' : 'var';
		
		// Make sure the database info is up to date
		$storage =& ABIStorage::getInstance();
		$databases = $storage->get('databases');
		$activeDatabase = 'joomla.sql';
		$this->_config['dbtype'] = $databases[$activeDatabase]['dbtype'];
		$this->_config['host'] = $databases[$activeDatabase]['dbhost'];
		$this->_config['user'] = $databases[$activeDatabase]['dbuser'];
		$this->_config['password'] = $databases[$activeDatabase]['dbpass'];
		$this->_config['db'] = $databases[$activeDatabase]['dbname'];
		$this->_config['dbprefix'] = $databases[$activeDatabase]['prefix'];

		// Get the actual file contents
		$out =  "<?php\n";
		$out .= "class JConfig {\n";
		foreach($this->_config as $name => $value){
			if(is_array($value))
			{
				$temp = '(';
				foreach($value as $key => $data)
				{
					if(strlen($temp) > 1) $temp .= ', ';
					$temp .= '\'$key\' => \'$data\'';
				}
				$temp .= ')';
				$value = 'array '.$temp;
			}
			else
			{
				// Log and temp paths in Windows systems will be forward-slash encoded
				if( (($name=='tmp_path') || ($name=='log_path')) )
				{
					$value = $this->TranslateWinPath($value);
				}
				$value = "'".addslashes($value)."'";
			}
			$out .= "\t" . $varkey.' $' . $name . " = ". $value .";\n";
		}

		$out .= '}' . "\n";

		return $out;
	}

	/**
	 * Makes a Windows path more UNIX-like, by turning backslashes to forward slashes.
	 * Since JP 2.0.b1 it takes into account UNC paths, e.g.
	 * \\myserver\some\folder becomes \\myserver/some/folder
	 *
	 * @param string $p_path The path to transform
	 * @return string
	 */
	function TranslateWinPath( $p_path )
	{
		static $is_windows;

		if(empty($is_windows))
		{
			$is_windows =  (DIRECTORY_SEPARATOR == '\\');
		}

		$is_unc = false;

		if ($is_windows)
		{
			// Is this a UNC path?
			$is_unc = (substr($p_path, 0, 2) == '//');
			// Change potential windows directory separator
			if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\')){
				$p_path = strtr($p_path, '\\', '/');
			}
		}

		// FIX 2.1.b2: Remove multiple slashes
		$p_path = str_replace('///','/',$p_path);
		$p_path = str_replace('//','/',$p_path);

		// Fix UNC paths
		if($is_unc)
		{
			$p_path = '/'.$p_path;
		}

		return $p_path;
	}

	function parseFile($file)
	{
		$ret = array();
		include_once $file;

		if(class_exists('JConfig'))
		{
			foreach(get_class_vars('JConfig') as $key => $value)
			{
				$ret[$key] = $value;
			}
		}

		return $ret;
	}

	/**
	 * A simplistic function to sniff the Joomla! version of the site being
	 * restored.
	 * 
	 * @staticvar string $jversion Caches the Joomla! version string
	 * @return string The Joomla! version, e.g. 1.6.3
	 */
	public function joomlaVersion()
	{
		static $jversion = null;
		
		if(empty($jversion)) {
			// Try to load Joomla!'s version.php file
			$path = dirname(__FILE__).'/../../libraries/joomla/version.php';
			if(file_exists($path)) {
				if(is_readable($path)) {
					include_once $path;
					if(class_exists('JVersion')) {
						$jv = new JVersion();
						$jversion = $jv->getShortVersion();
					}
				}
			}
			// If that failed, sniff configuration.php
			if(empty($jversion)) {
				$jversion = '1.5.0';
				$path = '../../configuration.php';
				if(file_exists($path) && is_readable($path)) {
					$fp = @fopen($path,'rt');
					if($fp !== false) {
						while(!feof($fp)) {
							$line = @fgets($fp);
							$line = trim($line);
							if(stristr($line, 'unicodeslugs')) {
								$jversion = '1.6.0';
							}
						}
						@fclose($fp);
					}
				}
			}
		}
		
		return $jversion;
	}
	
}