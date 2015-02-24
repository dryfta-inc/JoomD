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

if(!class_exists('AEPlatformJoomla15')) {
	require_once dirname(__FILE__).'/../joomla15/platform.php';
}

if(!defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR); // Still required by Joomla! :(
}

class AEPlatformJoomlacli extends AEPlatformJoomla15
{
	/** @var int Platform class priority */
	public $priority = 60;
	
	public $platformName = 'joomlacli';
		
	public function getPlatformDirectories()
	{
		return array(
			dirname(__FILE__),
			dirname(__FILE__).'/../joomla15'
		);
	}
	
	/**
	 * Performs heuristics to determine if this platform object is the ideal
	 * candidate for the environment Akeeba Engine is running in.
	 * 
	 * @return bool
	 */
	public function isThisPlatform()
	{
		// Make sure we're not running under a web interface
		if(array_key_exists('REQUEST_METHOD', $_SERVER)) return false;
		// Make sure _JEXEC is defined
		if(!defined('_JEXEC')) return false;
		// Make sure this is the CLI script
		if(!defined('AKEEBACLI')) return false;
		// If it's backup.php, it should also have parseOptions() defined
		if(!function_exists('parseOptions')) return false;
		// I think we're running under Joomla! in our custom backup.php CLI mode
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
			throw new Exception('This script requires the SPL extension to be activated in order to work',500);
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
			$tmpdir = AEUtilJconfig::getValue('tmp_path');
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
				// JFactory doesn't exist - we are on native backup mode
				$adminPos = strpos($root, 'administrator');
				if($adminPos !== false) {
					$root = substr($root, 0, $adminPos);
				} else {
					// Normally, this should never happen!
					$root = '../';
				}
			}
		}
		return $root;
	}

	/**
	 * Returns the active profile number
	 * @return int
	 */
	public function get_active_profile()
	{
		return AKEEBA_PROFILE;
	}

	/**
	 * Returns the backup origin
	 * @return string Backup origin: backend|frontend
	 */
	public function get_backup_origin()
	{
		if(defined('AKEEBA_BACKUP_ORIGIN')) {
			return AKEEBA_BACKUP_ORIGIN;
		} else {
			return 'cli';
		}
	}

	/**
	 * Returns a MySQL-formatted timestamp out of the current date
	 * @param string $date[optional] The timestamp to use. Omit to use current timestamp.
	 * @return string
	 */
	public function get_timestamp_database($date = 'now')
	{
		require_once JPATH_ROOT.'/libraries/joomla/base/object.php';
		require_once JPATH_ROOT.'/libraries/joomla/utilities/date.php';
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
		require_once JPATH_ROOT.'/libraries/joomla/base/object.php';
		require_once JPATH_ROOT.'/libraries/joomla/utilities/date.php';

		$tz = AEUtilJconfig::getValue('offset');
		$format = str_replace( '%A', date('%A'), $format );

		$dateNow = new JDate();
		$dateNow->setOffset($tz);

		return $dateNow->toFormat($format);
	}

	/**
	 * Returns the current host name
	 * @return string
	 */
	public function get_host()
	{
		require_once JPATH_ROOT.'/libraries/joomla/environment/uri.php';
		$url = AEPlatform::getInstance()->get_platform_configuration_option('siteurl','');
		$oURI = new JURI($url);
		return $oURI->getHost();
	}

	/**
	 * Gets the best matching database driver class, according to CMS settings
	 * @param bool $use_platform If set to false, it will forcibly try to assign one of the primitive type (AEDriverMySQL/AEDriverMySQLi) and NEVER tell you to use an AEPlatformDriver* class
	 * @return string
	 */
	public function get_default_database_driver( $use_platform = true )
	{
		$driver = AEUtilJconfig::getValue('dbtype');

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
			$options = array(
				'host'		=> AEUtilJconfig::getValue('host'),
				'user'		=> AEUtilJconfig::getValue('user'),
				'password'	=> AEUtilJconfig::getValue('password'),
				'database'	=> AEUtilJconfig::getValue('db'),
				'prefix'	=> AEUtilJconfig::getValue('dbprefix')
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
		if(class_exists('AEUtilTranslate'))
		{
			return AEUtilTranslate::_($key); // Doing so forces autoloading of the custom translator class
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
	 * Deletes a file from the local server using direct file access or FTP
	 * @param string $file
	 * @return bool
	 */
	public function unlink($file)
	{
		$result = @unlink($file);
	}

	/**
	 * Moves a file around within the local server using direct file access or FTP
	 * @param string $from
	 * @param string $to
	 * @return bool
	 */
	public function move($from, $to)
	{
		$result = @rename($from, $to);
		if(!$result) {
			$result = @copy($from, $to);
			if($result) {
				$result = $this->unlink($from);
			}
		}
		
		return $result;
	}

	/**
	 * Joomla!-specific function to get an instance of the mailer class
	 * @return JMail
	 */
	public function &getMailer()
	{
		jimport('joomla.mail.mail');

		$sendmail 	= AEUtilJconfig::getValue('sendmail');
		$smtpauth 	= AEUtilJconfig::getValue('smtpauth');
		$smtpuser 	= AEUtilJconfig::getValue('smtpuser');
		$smtppass  	= AEUtilJconfig::getValue('smtppass');
		$smtphost 	= AEUtilJconfig::getValue('smtphost');
		$smtpsecure	= AEUtilJconfig::getValue('smtpsecure');
		$smtpport	= AEUtilJconfig::getValue('smtpport');
		$mailfrom 	= AEUtilJconfig::getValue('mailfrom');
		$fromname 	= AEUtilJconfig::getValue('fromname');
		$mailer 	= AEUtilJconfig::getValue('mailer');

		// Create a JMail object
		$mail 		=& JMail::getInstance();

		// Default mailer is to use PHP's mail function
		switch ($mailer)
		{
			case 'smtp' :
				AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Using SMTP");
				$mail->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
				break;
			case 'sendmail' :
				AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Using sendmail");
				$mail->useSendmail($sendmail);
				break;
			default :
				AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"-- Using PHP email()");
				$mail->IsMail();
				break;
		}

		$mail->Encoding = '8bit';
		$mail->CharSet = 'utf-8';

		// Set default sender
		$mail->setSender(array ($mailfrom, $fromname));

		return $mail;
	}

}

$aeplatformjoomlacli = new AEPlatformJoomlacli();
if($aeplatformjoomlacli->isThisPlatform()) {

	// Load the version.php of the suitable Joomla! release
	if(!class_exists('JVersion')) {
		$paths = array(
			JPATH_SITE.'/libraries/cms/version/version.php', // Joomla! 2.5
			JPATH_SITE.'/libraries/joomla/version.php', // Joomla! 1.6/1.7
			JPATH_SITE.'/includes/version.php', // Joomla! 1.5
		);

		foreach($paths as $path) {
			if(@file_exists($path)) {
				require_once $path;
				break;
			}
		}
	}
	
	$jversion = new JVersion();
	$joomlaversion = $jversion->getShortVersion();
	
	// Load the JLoader class
	if(!defined('JPATH_PLATFORM')) define('JPATH_PLATFORM',1);
	require_once(JPATH_SITE.'/libraries/loader.php');
	
	// Load the CMS autoloader (J! 2.5)
	if(version_compare($joomlaversion,'2.5.0','ge')) {
		require_once(JPATH_LIBRARIES.'/cms.php');
	}

	// Load the JError and JException classes
	require_once(JPATH_SITE.'/libraries/joomla/base/object.php');
	require_once(JPATH_SITE.'/libraries/joomla/error/exception.php');
	require_once(JPATH_SITE.'/libraries/joomla/error/error.php');
	
	// Include some libraries (Joomla! 2.5)
	if(version_compare($joomlaversion,'2.5.0','ge')) {
		include_once(JPATH_LIBRARIES.'/joomla/utilities/date.php');
		include_once(JPATH_LIBRARIES.'/joomla/log/log.php');
		include_once(JPATH_LIBRARIES.'/joomla/log/entry.php');
		include_once(JPATH_LIBRARIES.'/joomla/utilities/string.php');
		include_once(JPATH_LIBRARIES.'/joomla/string/string.php');
	}
	
	// Custom callback for fatal Joomla! API errors (i.e. when an E_ERROR is raised)
	class AkeebaCustomError
	{
		function customErrorPage(& $error)
		{
			echo "\n\n";
			echo "-------------------------------------------------------------------------------\n";
			echo "JOOMLA! FRAMEWORK FATAL ERROR {$error->code}\n";
			echo $error->message."\n";
			echo "-------------------------------------------------------------------------------\n";

			$backtrace	= $error->getTrace();
			if( is_array( $backtrace ) )
			{
				echo "Stack Dump for Debugging (#/function/file):\n";
				$j	=	1;
				for( $i = count( $backtrace )-1; $i >= 0 ; $i-- )
				{
					echo "$j\t";
					if( isset( $backtrace[$i]['class'] ) ) {
						echo "\t".$backtrace[$i]['class'].$backtrace[$i]['type'].$backtrace[$i]['function'].'()';
					} else {
						echo "\t".$backtrace[$i]['function'].'()';
					}
					if( isset( $backtrace[$i]['file'] ) ) {
						echo "\t".$backtrace[$i]['file'].':'.$backtrace[$i]['line'];
					}
					echo "\n";
					$j++;
				}
				echo "-------------------------------------------------------------------------------\n";
			}

			echo "\nThe backup process has failed.\n";
			die();
		}
	}
	$GLOBALS['_JERROR_HANDLERS'][E_ERROR] = array( 'mode' => 'callback', 'options' => array('AkeebaCustomError','customErrorPage') );

	// Simulates JApplication::enqueueMessage() for the command-line clients
	class AkeebaCustomPseudoapp
	{
		public function enqueueMessage($message, $type)
		{
			switch($type)
			{
				case 'error':
					echo "*** ERROR: ";
					break;

				case 'warning':
					echo "*** WARNING: ";
					break;

				default:
					echo "*** NOTICE: ";
					break;
			}

			echo "$message\n";
		}
	}
	global $mainframe;
	$mainframe = new AkeebaCustomPseudoapp();

	// A simplistic implementation of JClientHelper to return FTP options (used by JFile's methods)
	if(!class_exists('JClientHelper'))
	{
		class JClientHelper
		{
			public static function getCredentials($client, $force = false)
			{
				$options = array(
					'enabled'	=> AEUtilJconfig::getValue('ftp_enable'),
					'host'		=> AEUtilJconfig::getValue('ftp_host'),
					'port'		=> AEUtilJconfig::getValue('ftp_port'),
					'user'		=> AEUtilJconfig::getValue('ftp_user'),
					'pass'		=> AEUtilJconfig::getValue('ftp_pass'),
					'root'		=> AEUtilJconfig::getValue('ftp_root')
				);
				return $options;
			}
		}
	}

}