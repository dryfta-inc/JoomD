<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id$
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

class AEPlatformJoomla15 extends AEPlatformAbstract
{
	/** @var int Platform class priority */
	public $priority = 50;
	
	public $platformName = 'joomla15';
	
	/**
	 * Performs heuristics to determine if this platform object is the ideal
	 * candidate for the environment Akeeba Engine is running in.
	 * 
	 * @return bool
	 */
	public function isThisPlatform()
	{
		// Make sure we're running under a web interface
		if(!array_key_exists('REQUEST_METHOD', $_SERVER)) return false;
		// Make sure _JEXEC is defined
		if(!defined('_JEXEC')) return false;
		// We need JVERSION to be defined
		if(!defined('JVERSION')) return false;
		// Check if JFactory exists
		if(!class_exists('JFactory')) return false;
		// Check if JApplication exists
		if(!class_exists('JApplication')) return false;
		// Still here? It has to be Joomla! running in web mode.
		return true;
	}
	
	/**
	 * Registers Akeeba's class autoloader with Joomla!
	 */
	public function register_autoloader()
	{
		// Try to register AEAutoloader with SPL, or fall back to making use of JLoader
		// Obviously, performance is better with SPL, but not all systems support it.
		if( function_exists('spl_autoload_register') )
		{
			// Joomla! is using its own autoloader function which has to be registered first...
			if(function_exists('__autoload')) spl_autoload_register('__autoload');
			// ...and then register ourselves.
			spl_autoload_register('AEAutoloader');
		}
		else
		{
			// Guys, it's 2011 at the time of this writing. If you have a host which
			// doesn't support SPL yet, SWITCH HOSTS!
			throw new Exception('Akeeba Backup REQUIRES the SPL extension to be loaded and activated',500);
		}
	}

	/**
	 * Returns an associative array of stock platform directories
	 * @return array
	 */
	public function get_stock_directories()
	{
		static $stock_directories = array();

		if(empty($stock_directories))
		{
			$jreg =& JFactory::getConfig();
			$tmpdir = $jreg->getValue('config.tmp_path');
			$stock_directories['[SITEROOT]'] = $this->get_site_root();
			$stock_directories['[ROOTPARENT]'] = @realpath($this->get_site_root().'/..');
			$stock_directories['[SITETMP]'] = $tmpdir;
			$stock_directories['[DEFAULT_OUTPUT]'] = $this->get_site_root().'/administrator/components/com_akeeba/backup';
		}

		return $stock_directories;
	}

	/**
	 * Returns the absolute path to the site's root
	 * @return string
	 */
	public function get_site_root()
	{
		static $root = null;

		if( empty($root) || is_null($root) )
		{
			$root = JPATH_ROOT;

			if(empty($root) || ($root == DIRECTORY_SEPARATOR) || ($root == '/'))
			{
				// Try to get the current root in a different way
				if(function_exists('getcwd')) {
					$root = getcwd();
				}
				
				$app =& JFactory::getApplication();
				if( $app->isAdmin() )
				{
					if(empty($root)) {
						$root = '../';
					} else {
						$adminPos = strpos($root, 'administrator');
						if($adminPos !== false) {
							$root = substr($root, 0, $adminPos);
						} else {
							$root = '../';
						}
						// Degenerate case where $root = 'administrator'
						// without a leading slash before entering this
						// if-block
						if(empty($root)) $root = '../';
					}
				}
				else
				{
					if(empty($root) || ($root == DIRECTORY_SEPARATOR) || ($root == '/') ) {
						$root = './';
					}
				}
			}
		}
		return $root;
	}

	/**
	 * Returns the absolute path to the installer images directory
	 * @return string
	 */
	public function get_installer_images_path()
	{
		return JPATH_ADMINISTRATOR.'/components/com_akeeba/assets/installers';
	}

	/**
	 * Returns the active profile number
	 * @return int
	 */
	public function get_active_profile()
	{
		if( defined('AKEEBA_PROFILE') )
		{
			return AKEEBA_PROFILE;
		}
		else
		{
			$session =& JFactory::getSession();
			return $session->get('profile', null, 'akeeba');
		}
	}

	/**
	 * Returns the selected profile's name. If no ID is specified, the current
	 * profile's name is returned.
	 * @return string
	 */
	public function get_profile_name($id = null)
	{
		if(empty($id)) $id = $this->get_active_profile();
		$id = (int)$id;

		$sql = 'SELECT `description` FROM `#__ak_profiles` WHERE `id` = '.$id;
		$db =& AEFactory::getDatabase( $this->get_platform_database_options() );
		$db->setQuery($sql);
		return $db->loadResult();
	}

	/**
	 * Returns the backup origin
	 * @return string Backup origin: backend|frontend
	 */
	public function get_backup_origin()
	{
		if(defined('AKEEBA_BACKUP_ORIGIN')) return AKEEBA_BACKUP_ORIGIN;

		if(JFactory::getApplication()->isAdmin()) {
			return 'backend';
		} else {
			return 'frontend';
		}
	}

	/**
	 * Returns a MySQL-formatted timestamp out of the current date
	 * @param string $date[optional] The timestamp to use. Omit to use current timestamp.
	 * @return string
	 */
	public function get_timestamp_database($date = 'now')
	{
		jimport('joomla.utilities.date');
		$jdate = new JDate($date);
		return $jdate->toMySQL();
	}

	/**
	 * Returns the current timestamp, taking into account any TZ information,
	 * in the format specified by $format.
	 * @param string $format Timestamp format string (standard PHP format string)
	 * @return string
	 */
	public function get_local_timestamp($format)
	{
		jimport('joomla.utilities.date');

		$jregistry =& JFactory::getConfig();
		$tzDefault = $jregistry->getValue('config.offset');
		$user =& JFactory::getUser();
		$tz = $user->getParam('timezone', $tzDefault);

		if(version_compare('JVERSION', '1.6.0', 'ge')) {
			$dateNow = new JDate('now',$tz);
		} else {
			$dateNow = new JDate();
			$dateNow->setOffset($tz);
		}

		return $dateNow->toFormat($format);
	}

	/**
	 * Returns the current host name
	 * @return string
	 */
	public function get_host()
	{
		$uri =& JURI::getInstance();
		return $uri->getHost();
	}

	/**
	 * Gets the best matching database driver class, according to CMS settings
	 * @param bool $use_platform If set to false, it will forcibly try to assign one of the primitive type (AEDriverMySQL/AEDriverMySQLi) and NEVER tell you to use an AEPlatformDriver* class
	 * @return string
	 */
	public function get_default_database_driver( $use_platform = true )
	{
		$jconfig =& JFactory::getConfig();
		$driver = $jconfig->getValue('config.dbtype');

		// Let's see what driver Joomla! uses...
		if( $use_platform )
		{
			$hasNookuContent = file_exists(JPATH_ROOT.'/plugins/system/nooku.php');
			switch($driver)
			{
				// MySQL or MySQLi drivers are known to be working; use their
				// Akeeba Engine extended version, AEDriverPlatformJoomla
				case 'mysql':
					if($hasNookuContent) {
						return 'AEDriverMysql';
					} else {
						return 'AEDriverPlatformJoomla';
					}
					break;

				case 'mysqli':
					if($hasNookuContent) {
						return 'AEDriverMysqli';
					} else {
						return 'AEDriverPlatformJoomla';
					}
					break;

				// Some custom driver. Uh oh!
				default:
					break;
			}
		}

		// Is this a subcase of mysqli or mysql drivers?
		if( strtolower(substr($driver, 0, 6)) == 'mysqli' )
		{
			return 'AEDriverMysqli';
		}
		elseif( strtolower(substr($driver, 0, 5)) == 'mysql' )
		{
			return 'AEDriverMysql';
		}

		// If we're still here, we have to guesstimate the correct driver. All bets are off.
		if(function_exists('mysqli_connect'))
		{
			// MySQLi available. Let's use it.
			return 'AEDriverMysqli';
		}
		else
		{
			// MySQLi is not available; let's use standard MySQL.
			return 'AEDriverMysql';
		}
	}

	/**
	 * Returns a set of options to connect to the default database of the current CMS
	 * @return array
	 */
	public function get_platform_database_options()
	{
		static $options;

		if(empty($options))
		{
			$conf =& JFactory::getConfig();
			$options = array(
				'host'		=> $conf->getValue('config.host'),
				'user'		=> $conf->getValue('config.user'),
				'password'	=> $conf->getValue('config.password'),
				'database'	=> $conf->getValue('config.db'),
				'prefix'	=> $conf->getValue('config.dbprefix')
			);
		}

		return $options;
	}

	/**
	 * Provides a platform-specific translation function
	 * @param string $key The translation key
	 * @return string
	 */
	public function translate($key)
	{
		return JText::_($key);
	}

	/**
	 * Populates global constants holding the Akeeba version
	 */
	public function load_version_defines()
	{
		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/version.php'))
		{
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/version.php');
		}

		if(!defined('AKEEBA_VERSION')) define("AKEEBA_VERSION", "svn");
		if(!defined('AKEEBA_PRO')) define('AKEEBA_PRO', false);
		if(!defined('AKEEBA_DATE')) {
			jimport('joomla.utilities.date');
			$date = new JDate();
			define( "AKEEBA_DATE", $date->toFormat('%Y-%m-%d') );
		}
	}

	/**
	 * Returns the platform name and version
	 * @param string $platform_name Name of the platform, e.g. Joomla!
	 * @param string $version Full version of the platform
	 */
	public function getPlatformVersion( &$platform_name, &$version )
	{
		$platform_name = "Joomla!";
		$v = new JVersion();
		$version = $v->getLongVersion();
	}

	/**
	 * Logs platform-specific directories with _AE_LOG_INFO log level
	 */
	public function log_platform_special_directories()
	{
		AEUtilLogger::WriteLog(_AE_LOG_INFO, "JPATH_BASE         :" . JPATH_BASE );
		AEUtilLogger::WriteLog(_AE_LOG_INFO, "JPATH_SITE         :" . JPATH_SITE );
		AEUtilLogger::WriteLog(_AE_LOG_INFO, "JPATH_ROOT         :" . JPATH_ROOT );
		AEUtilLogger::WriteLog(_AE_LOG_INFO, "JPATH_CACHE        :" . JPATH_CACHE );
		AEUtilLogger::WriteLog(_AE_LOG_INFO, "Computed root      :" . $this->get_site_root() );
		
		// Detect UNC paths and warn the user
		if(DIRECTORY_SEPARATOR == '\\') {
			if( (substr(JPATH_ROOT, 0, 2) == '\\\\') || (substr(JPATH_ROOT, 0, 2) == '//') ) {
				AEUtilLogger::WriteLog(_AE_LOG_WARNING, 'Your site\'s root is using a UNC path (e.g. \\SERVER\path\to\root). PHP has known bugs which may');
				AEUtilLogger::WriteLog(_AE_LOG_WARNING, 'prevent it from working properly on a site like this. Please take a look at');
				AEUtilLogger::WriteLog(_AE_LOG_WARNING, 'https://bugs.php.net/bug.php?id=40163 and https://bugs.php.net/bug.php?id=52376. As a result your');
				AEUtilLogger::WriteLog(_AE_LOG_WARNING, 'backup may fail.');
			}
		}
	}

	/**
	 * Loads a platform-specific software configuration option
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get_platform_configuration_option($key, $default)
	{
		// Get the component configuration option WITHOUT using the bloody ever-changing Joomla! API...
		return AEUtilComconfig::getValue($key, $default);
	}

	/**
	 * Returns a list of emails to the Super Administrators
	 * @return unknown_type
	 */
	public function get_administrator_emails()
	{
		$db =& AEFactory::getDatabase( $this->get_platform_database_options() );
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$query = 'SELECT u.name, u.email FROM #__users AS u INNER JOIN #__user_usergroup_map AS m ON(m.user_id = u.id) '.
					' WHERE m.group_id = 8 ';
		} else {
			$query = 'SELECT name, email FROM #__users'.
					' WHERE usertype = \'Super Administrator\' ';
		}
		$db->setQuery($query);
		$superAdmins =& $db->loadAssocList();

		$mails = array();
		if(!empty($superAdmins))
		{
			foreach($superAdmins as $admin)
			{
				$mails[] = $admin['email'];
			}
		}

		return $mails;
	}

	/**
	 * Sends a very simple email using the platform's emailer facility
	 * @param string $to
	 * @param string $subject
	 * @param string $body
	 */
	public function send_email($to, $subject, $body, $attachFile = null)
	{
		AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Fetching mailer object" );
		
		$mailer =& AEPlatform::getInstance()->getMailer();
		
		if(!is_object($mailer)) {
			AEUtilLogger::WriteLog(_AE_LOG_WARNING,"Could not send email to $to - Reason: Mailer object is not an object; please check your system settings");
			return false;
		}
		
		AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Creating email message");
		
		$recipient = array($to);
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->setBody($body);

		if(!empty($attachFile))
		{
			AEUtilLogger::WriteLog(_AE_LOG_WARNING, "-- Attaching $attachFile");
			
			if(!file_exists($attachFile) || !(is_file($attachFile) || is_link($attachFile))) {
				AEUtilLogger::WriteLog(_AE_LOG_WARNING, "The file does not exist, or it's not a file; no email sent");
				return false;
			}
			
			if(!is_readable($attachFile)) {
				AEUtilLogger::WriteLog(_AE_LOG_WARNING, "The file is not readable; no email sent");
				return false;
			}
			
			$filesize = @filesize($attachFile);
			if($filesize) {
				// Check that we have AT LEAST 2.5 times free RAM as the filesize (that's how much we'll need)
				if(!function_exists('ini_get')) {
					// Assume 8Mb of PHP memory limit (worst case scenario)
					$totalRAM = 8388608;
				} else {
					$totalRAM = ini_get('memory_limit');
					if(strstr($totalRAM, 'M')) {
						$totalRAM = (int)$totalRAM * 1048576;
					} elseif(strstr($totalRAM, 'K')) {
						$totalRAM = (int)$totalRAM * 1024;
					} elseif(strstr($totalRAM, 'G')) {
						$totalRAM = (int)$totalRAM * 1073741824;
					} else {
						$totalRAM = (int)$totalRAM;
					}
					if($totalRAM <= 0) {
						// No memory limit? Cool! Assume 1Gb of available RAM (which is absurdely abundant as of March 2011...)
						$totalRAM = 1086373952;
					}
				}
				if(!function_exists('memory_get_usage')) {
					$usedRAM = 8388608;
				} else {
					$usedRAM = memory_get_usage();
				}
				
				$availableRAM = $totalRAM - $usedRAM;
				
				if($availableRAM < 2.5*$filesize) {
					AEUtilLogger::WriteLog(_AE_LOG_WARNING, "The file is too big to be sent by email. Please use a smaller Part Size for Split Archives setting.");
					AEUtilLogger::WriteLog(_AE_LOG_DEBUG, "Memory limit $totalRAM bytes -- Used memory $usedRAM bytes -- File size $filesize -- Attachment requires approx. ".(2.5*$filesize)." bytes");
					return false;
				}
			} else {
				AEUtilLogger::WriteLog(_AE_LOG_WARNING, "Your server fails to report the file size of $attachFile. If the backup crashes, please use a smaller Part Size for Split Archives setting");
			}
			
			$mailer->addAttachment($attachFile);
		}
		
		AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Sending message");

		$result = $mailer->Send();

		if($result instanceof JException)
		{
			AEUtilLogger::WriteLog(_AE_LOG_WARNING,"Could not email $to:");
			AEUtilLogger::WriteLog(_AE_LOG_WARNING,$result->message);
			$ret = $result->message;
			unset($result);
			unset($mailer);
			return $ret;
		}
		else
		{
			AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Email sent");
			return true;
		}
	}

	/**
	 * Deletes a file from the local server using direct file access or FTP
	 * @param string $file
	 * @return bool
	 */
	public function unlink($file)
	{
		if(function_exists('jimport')) {
			jimport('joomla.filesystem.file');
			$result = JFile::delete($file);
			if(!$result) $result = @unlink($file);
		} else {
			$result = parent::unlink($file);
		}
		return $result;
	}

	/**
	 * Moves a file around within the local server using direct file access or FTP
	 * @param string $from
	 * @param string $to
	 * @return bool
	 */
	public function move($from, $to)
	{
		if(function_exists('jimport')) {
			jimport('joomla.filesystem.file');
			$result = JFile::move($from, $to);
			// JFile failed. Let's try rename()
			if(!$result)
			{
				$result = @rename($from, $to);
			}
			// Rename failed, too. Let's try copy/delete
			if(!$result)
			{
				// Try copying with JFile. If it fails, use copy().
				$result = JFile::copy($from, $to);
				if(!$result) $result = @copy($from, $to);
	
				// If the copy succeeded, try deleting the original with JFile. If it fails, use unlink().
				if($result)
				{
					$result = $this->unlink($from);
				}
			}
		} else {
			$result = parent::move($from, $to);
		}
		return $result;
	}

	/**
	 * Registers Akeeba Engine's core classes with JLoader
	 * @param string $path_prefix The path prefix to look in
	 */
	protected function register_akeeba_engine_classes($path_prefix)
	{
		global $Akeeba_Class_Map;
		jimport('joomla.filesystem.folder');
		foreach($Akeeba_Class_Map as $class_prefix => $path_suffix)
		{
			// Bail out if there is such directory, so as not to have Joomla! throw errors
			if(!@is_dir($path_prefix.'/'.$path_suffix)) continue;

			$file_list = JFolder::files( $path_prefix.'/'.$path_suffix, '.*\.php' );
			if(is_array($file_list) && !empty($file_list)) foreach($file_list as $file)
			{
				$class_suffix = ucfirst(basename($file, '.php'));
				JLoader::register($class_prefix.$class_suffix, $path_prefix.'/'.$path_suffix.'/'.$file );
			}
		}
	}

	/**
	 * Joomla!-specific function to get an instance of the mailer class
	 * @return JMail
	 */
	public function &getMailer()
	{
		$mailer =& JFactory::getMailer();
		if(!is_object($mailer)) {
			AEUtilLogger::WriteLog(_AE_LOG_WARNING,"Fetching Joomla!'s mailer was impossible; imminent crash!");
		} else {
			$emailMethod = $mailer->Mailer;
			AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Joomla!'s mailer is using $emailMethod mail method.");
		}
		return $mailer;
	}
}