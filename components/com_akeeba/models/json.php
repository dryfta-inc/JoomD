<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// JSON API version number
define('AKEEBA_JSON_API_VERSION', '319');

/*
 * Short API version history:
 * 300	First draft. Basic backup working. Encryption semi-broken.
 * 316	Fixed download feature.
 */

// Load framework base classes
jimport('joomla.application.component.model');

// Force load the AEUtilEncrypt class if it's Akeeba Backup Professional
if(AKEEBA_PRO == 1) $dummy = new AEUtilEncrypt;

if(!defined('AKEEBA_BACKUP_ORIGIN'))
{
	define('AKEEBA_BACKUP_ORIGIN','json');
}

class AkeebaModelJson extends JModel
{
	const	STATUS_OK					= 200;	// Normal reply
	const	STATUS_NOT_AUTH				= 401;	// Invalid credentials
	const	STATUS_NOT_ALLOWED			= 403;	// Not enough privileges
	const	STATUS_NOT_FOUND			= 404;  // Requested resource not found
	const	STATUS_INVALID_METHOD		= 405;	// Unknown JSON method
	const	STATUS_ERROR				= 500;	// An error occurred
	const	STATUS_NOT_IMPLEMENTED		= 501;	// Not implemented feature
	const	STATUS_NOT_AVAILABLE		= 503;	// Remote service not activated

	const	ENCAPSULATION_RAW			= 1;	// Data in plain-text JSON
	const	ENCAPSULATION_AESCTR128		= 2;	// Data in AES-128 stream (CTR) mode encrypted JSON
	const	ENCAPSULATION_AESCTR256		= 3;	// Data in AES-256 stream (CTR) mode encrypted JSON
	const	ENCAPSULATION_AESCBC128		= 4;	// Data in AES-128 standard (CBC) mode encrypted JSON
	const	ENCAPSULATION_AESCBC256		= 5;	// Data in AES-256 standard (CBC) mode encrypted JSON

	private	$json_errors = array(
			'JSON_ERROR_NONE' => 'No error has occurred (probably emtpy data passed)',
			'JSON_ERROR_DEPTH' => 'The maximum stack depth has been exceeded',
			'JSON_ERROR_CTRL_CHAR' => 'Control character error, possibly incorrectly encoded',
			'JSON_ERROR_SYNTAX' => 'Syntax error'
			);

	/** @var int The status code */
	private	$status = 200;
	/** @var int Data encapsulation format */
	private $encapsulation = 1;
	/** @var mixed Any data to be returned to the caller */
	private $data = '';
	/** @var string A password passed to us by the caller */
	private $password = null;
	/** @var string The method called by the client */
	private $method_name = null;
	
	public function execute($json)
	{
		// Check if we're activated
		$enabled = AEPlatform::getInstance()->get_platform_configuration_option('frontend_enable', 0);
		if(!$enabled)
		{
			$this->data = 'Access denied';
			$this->status = self::STATUS_NOT_AVAILABLE;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return $this->getResponse();
		}

		// Try to JSON-decode the request's input first
		$request = @$this->json_decode($json, false);
		if(is_null($request))
		{
			// Could not decode JSON
			$this->data = 'JSON decoding error';
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return $this->getResponse();
		}

		// Decode the request body
		// Request format: {encapsulation, body{ [key], [challenge], method, [data] }} or {[challenge], method, [data]}
		if( isset($request->encapsulation) && isset($request->body) )
		{
			if(!class_exists('AEUtilEncrypt') && !($request->encapsulation == self::ENCAPSULATION_RAW))
			{
				// Encrypted request found, but there is no encryption class available!
				$this->data = 'This server does not support encrypted requests';
				$this->status = self::STATUS_NOT_AVAILABLE;
				$this->encapsulation = self::ENCAPSULATION_RAW;
				return $this->getResponse();
			}

			// Fully specified request
			switch( $request->encapsulation )
			{
				case self::ENCAPSULATION_AESCBC128:
					if(!isset($body))
					{
						$request->body = base64_decode($request->body);
						$body = AEUtilEncrypt::AESDecryptCBC($request->body, $this->serverKey(), 128);
					}
					break;

				case self::ENCAPSULATION_AESCBC256:
					if(!isset($body))
					{
						$request->body = base64_decode($request->body);
						$body = AEUtilEncrypt::AESDecryptCBC($request->body, $this->serverKey(), 256);
					}
					break;

				case self::ENCAPSULATION_AESCTR128:
					if(!isset($body))
					{
						$body = AEUtilEncrypt::AESDecryptCtr($request->body, $this->serverKey(), 128);
					}
					break;

				case self::ENCAPSULATION_AESCTR256:
					if(!isset($body))
					{
						$body = AEUtilEncrypt::AESDecryptCtr($request->body, $this->serverKey(), 256);
					}
					break;

				case self::ENCAPSULATION_RAW:
					$body = $request->body;
					break;
			}

			if(!empty($request->body))
			{
				$body = rtrim( $body, chr(0) );
				$request->body = $this->json_decode($body);
				if(is_null($request->body))
				{
					// Decryption failed. The user is an imposter! Go away, hacker!
					$this->data = 'Authentication failed';
					$this->status = self::STATUS_NOT_AUTH;
					$this->encapsulation = self::ENCAPSULATION_RAW;
					return $this->getResponse();
				}
			}
		}
		elseif( isset($request->body) )
		{
			// Partially specified request, assume RAW encapsulation
			$request->encapsulation = self::ENCAPSULATION_RAW;
			$request->body = $this->json_decode($request->body);
		}
		else
		{
			// Legacy request
			$legacyRequest = clone $request;
			$request = (object) array( 'encapsulation' => self::ENCAPSULATION_RAW, 'body' => null );
			$request->body = $this->json_decode($legacyRequest);
			unset($legacyRequest);
		}

		// Authenticate the user. Do note that if an encrypted request was made, we can safely assume that
		// the user is authenticated (he already knows the server key!)
		if($request->encapsulation == self::ENCAPSULATION_RAW)
		{
			$authenticated = false;
			if(isset($request->body->challenge))
			{
				list($challenge,$check) = explode(':', $request->body->challenge);
				$crosscheck = strtolower(md5($challenge.$this->serverKey()));
				$authenticated = ($crosscheck == $check);
			}
			if(!$authenticated)
			{
				// If the challenge was missing or it was wrong, don't let him go any further
				$this->data = 'Invalid login credentials';
				$this->status = self::STATUS_NOT_AUTH;
				$this->encapsulation = self::ENCAPSULATION_RAW;
				return $this->getResponse();
			}
		}

		// Replicate the encapsulation preferences of the client for our own output
		$this->encapsulation = $request->encapsulation;

		// Store the client-specified key, or use the server key if none specified and the request
		// came encrypted.
		$this->password = isset($request->body->key) ? $request->body->key : null;
		$hasKey = property_exists($request->body, 'key') ? !is_null($request->body->key) : false;
		if(!$hasKey && ($request->encapsulation != self::ENCAPSULATION_RAW) )
		{
			$this->password = $this->serverKey();
		}

		// Does the specified method exist?
		$method_exists = false;
		$method_name = '';
		if(isset($request->body->method))
		{
			$method_name = ucfirst($request->body->method);
			$this->method_name = $method_name;
			$method_exists = method_exists($this, '_api'.$method_name );
		}
		if(!$method_exists)
		{
			// The requested method doesn't exist. Oops!
			$this->data = "Invalid method $method_name";
			$this->status = self::STATUS_INVALID_METHOD;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return $this->getResponse();
		}

		// Run the method
		$params = array();
		if(isset($request->body->data)) $params = (array)$request->body->data;
		$this->data = call_user_func( array($this, '_api'.$method_name) , $params);

		return $this->getResponse();
	}

	/**
	 * Packages the response to a JSON-encoded object, optionally encrypting the
	 * data part with a caller-supplied password.
	 * @return string The JSON-encoded response
	 */
	private function getResponse()
	{
		// Initialize the response
		$response = array(
			'encapsulation'	=> $this->encapsulation,
			'body'		=> array(
				'status'		=> $this->status,
				'data'			=> null
			)
		);

		switch($this->method_name)
		{
			case 'Download':
				$data = json_encode($this->data);
				break;
			default:
				$data = $this->json_encode($this->data);
				break;
		}

		if(empty($this->password)) $this->encapsulation = self::ENCAPSULATION_RAW;

		switch($this->encapsulation)
		{
			case self::ENCAPSULATION_RAW:
				break;

			case self::ENCAPSULATION_AESCTR128:
				$data = AEUtilEncrypt::AESEncryptCtr($data, $this->password, 128);
				break;

			case self::ENCAPSULATION_AESCTR256:
				$data = AEUtilEncrypt::AESEncryptCtr($data, $this->password, 256);
				break;

			case self::ENCAPSULATION_AESCBC128:
				$data = base64_encode(AEUtilEncrypt::AESEncryptCBC($data, $this->password, 128));
				break;

			case self::ENCAPSULATION_AESCBC256:
				$data = base64_encode(AEUtilEncrypt::AESEncryptCBC($data, $this->password, 256));
				break;
		}

		$response['body']['data'] = $data;
		
		switch($this->method_name)
		{
			case 'Download':
				return '###' . json_encode($response) . '###';
				break;
			default:
				return '###' . $this->json_encode($response) . '###';
				break;
		}
	}

	private function serverKey()
	{
		static $key = null;

		if(is_null($key))
		{
			$key = AEPlatform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');
		}

		return $key;
	}

	private function _apiGetVersion()
	{
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/liveupdate.php';
		$updateInformation = LiveUpdate::getUpdateInformation();
		
		return (object)array(
			'api'			=> AKEEBA_JSON_API_VERSION,
			'component'		=> AKEEBA_VERSION,
			'date'			=> AKEEBA_DATE,
			'updateinfo'	=> $updateInformation
		);
	}

	private function _apiGetProfiles()
	{
		require_once JPATH_SITE.'/administrator/components/com_akeeba/models/profiles.php';
		$model = new AkeebaModelProfiles();
		$profiles = $model->getProfilesList(true);
		$ret = array();

		if(count($profiles))
		{
			foreach($profiles as $profile)
			{
				$temp = new stdClass();
				$temp->id = $profile->id;
				$temp->name = $profile->description;
				$ret[] = $temp;
			}
		}

		return $ret;
	}

	private function _apiStartBackup($config)
	{
		// Get the passed configuration values
		$defConfig = array(
			'profile'		=> 1,
			'description'	=> '',
			'comment'		=> ''
		);
		$config = array_merge($defConfig, $config);
		foreach($config as $key => $value) {
			if(!array_key_exists($key, $defConfig)) unset($config[$key]);
		}
		extract($config);
		
		// Nuke the factory
		AEFactory::nuke();

		// Set the profile
		$profile = (int)$profile;
		if(!is_numeric($profile)) $profile = 1;
		$session =& JFactory::getSession();
		$session->set('profile', $profile, 'akeeba');
		AEPlatform::getInstance()->load_configuration($profile);
		
		// Use the default description if none specified
		if(empty($description))
		{
			jimport('joomla.utilities.date');
			$user =& JFactory::getUser();
			$userTZ = $user->getParam('timezone',0);
			$dateNow = new JDate();
			$dateNow->setOffset($userTZ);
			if( AKEEBA_JVERSION == '16' ) {
				$description = JText::_('BACKUP_DEFAULT_DESCRIPTION').' '.$dateNow->format(JText::_('DATE_FORMAT_LC2'), true);
			} else {
				$description = JText::_('BACKUP_DEFAULT_DESCRIPTION').' '.$dateNow->toFormat(JText::_('DATE_FORMAT_LC2'));
			}
		}

		// Start the backup
		AECoreKettenrad::reset();
		$memory_filename = AEUtilTempvars::get_storage_filename(AKEEBA_BACKUP_ORIGIN);
		@unlink($memory_filename);
		
		$kettenrad =& AECoreKettenrad::load(AKEEBA_BACKUP_ORIGIN);
		$options = array(
			'description'	=> $description,
			'comment'		=> $comment,
			'tag'			=> AKEEBA_BACKUP_ORIGIN
		);
		$kettenrad->setup($options); // Setting up the engine
		$array = $kettenrad->tick(); // Initializes the init domain
		AECoreKettenrad::save(AKEEBA_BACKUP_ORIGIN);

		$array = $kettenrad->getStatusArray();
		if($array['Error'] != '')
		{
			// A backup error had occurred. Why are we here?!
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'A backup error had occurred: '.$array['Error'];
		}
		else
		{
			$statistics =& AEFactory::getStatistics();
			$array['BackupID'] = $statistics->getId();
			$array['HasRun'] = 1; // Force the backup to go on.
			return $array;
		}
	}

	private function _apiStepBackup($config)
	{
		$defConfig = array(
			'profile'	=> null
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		// Try to set the profile from the setup parameters
		if(!empty($profile))
		{
			$registry = AEFactory::getConfiguration();
			$session =& JFactory::getSession();
			$session->set('profile', $profile, 'akeeba');
		}
		
		$kettenrad =& AECoreKettenrad::load(AKEEBA_BACKUP_ORIGIN);

		$registry = AEFactory::getConfiguration();
		$session =& JFactory::getSession();
		$session->set('profile', $registry->activeProfile, 'akeeba');
		
		$array = $kettenrad->tick();
		$array['Progress'] = $kettenrad->getProgress();
		AECoreKettenrad::save(AKEEBA_BACKUP_ORIGIN);

		if($array['Error'] != '')
		{
			// A backup error had occurred. Why are we here?!
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'A backup error had occurred: '.$array['Error'];
		} elseif($array['HasRun'] == false) {
			AEFactory::nuke();
			AEUtilTempvars::reset();
		}
		
		return $array;
	}

	private function _apiListBackups($config)
	{
		$defConfig = array(
			'from'			=> 0,
			'limit'			=> 50
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/statistics.php';

		$model = new AkeebaModelStatistics();
		return $model->getStatisticsListWithMeta(true);
	}

	private function _apiGetBackupInfo($config)
	{
		$defConfig = array(
			'backup_id'			=> '0'
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		// Get the basic statistics
		$record = AEPlatform::getInstance()->get_statistics($backup_id);

		// Get a list of filenames
		$backup_stats = AEPlatform::getInstance()->get_statistics($backup_id);

		// Backup record doesn't exist
		if(empty($backup_stats))
		{
			$this->status = self::STATUS_NOT_FOUND;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'Invalid backup record identifier';
		}

		$filenames = AEUtilStatistics::get_all_filenames($record);

		if(empty($filenames))
		{
			// Archives are not stored on the server or no files produced
			$record['filenames'] = array();
		}
		else
		{
			$filedata = array();
			$i = 0;

			// Get file sizes per part
			foreach($filenames as $file)
			{
				$i++;
				$size = @filesize($file);
				$size = is_numeric($size) ? $size : 0;
				$filedata[] = array(
					'part'			=> $i,
					'name'			=> basename($file),
					'size'			=> $size
				);
			}

			// Add the file info to $record['filenames']
			$record['filenames'] = $filedata;
		}

		return $record;

	}

	private function _apiDownload($config)
	{
		$defConfig = array(
			'backup_id'			=> 0,
			'part_id'			=> 1,
			'segment'			=> 1,
			'chunk_size'		=> 1
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		$backup_stats = AEPlatform::getInstance()->get_statistics($backup_id);
		if(empty($backup_stats))
		{
			// Backup record doesn't exist
			$this->status = self::STATUS_NOT_FOUND;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'Invalid backup record identifier';
		}
		$files = AEUtilStatistics::get_all_filenames($backup_stats);

		if( (count($files) < $part_id) || ($part_id <= 0) )
		{
			// Invalid part
			$this->status = self::STATUS_NOT_FOUND;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'Invalid backup part';
		}

		$file = $files[$part_id-1];
		
		$filesize = @filesize($file);
		$seekPos = $chunk_size * 1048756 * ($segment - 1);

		if($seekPos > $filesize) {
			// Trying to seek past end of file
			$this->status = self::STATUS_NOT_FOUND;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'Invalid segment';
		}
		
		$fp = fopen($file, 'rb');

		if($fp === false)
		{
			// Could not read file
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'Error reading backup archive';
		}

		rewind($fp);
		if(fseek($fp, $seekPos, SEEK_SET) === -1)
		{
			// Could not seek to position
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'Error reading specified segment';
		}

		$buffer = fread($fp, 1048756);

		if($buffer === false)
		{
			// Could not read
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return 'Error reading specified segment';
		}

		fclose($fp);

		switch($this->encapsulation)
		{
			case self::ENCAPSULATION_RAW:
				return base64_encode($buffer);
				break;

			case self::ENCAPSULATION_AESCTR128:
				$this->encapsulation = self::ENCAPSULATION_AESCBC128;
				return $buffer;
				break;

			case self::ENCAPSULATION_AESCTR256:
				$this->encapsulation = self::ENCAPSULATION_AESCBC256;
				return $buffer;
				break;

			default:
				// On encrypted comms the encryption will take care of transport encoding
				return $buffer;
				break;
		}
	}

	private function _apiDelete($config)
	{
		$defConfig = array(
			'backup_id'			=> 0
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/statistics.php';

		$model = new AkeebaModelStatistics();
		$result = $model->delete((int)$backup_id);
		if(!$result)
		{
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return $model->getError();
		}
		else
		{
			return true;
		}
	}

	private function _apiDeleteFiles($config)
	{
		$defConfig = array(
			'backup_id'			=> 0
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/statistics.php';

		$model = new AkeebaModelStatistics();
		$result = $model->deleteFile((int)$backup_id);
		if(!$result)
		{
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return $model->getError();
		}
		else
		{
			return true;
		}
	}

	private function _apiGetLog($config)
	{
		$defConfig = array(
			'tag'			=> 'remote'
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		$filename = AEUtilLogger::logName($tag);
		$buffer = file_get_contents($filename);

		switch($this->encapsulation)
		{
			case self::ENCAPSULATION_RAW:
				return base64_encode($buffer);
				break;

			case self::ENCAPSULATION_AESCTR128:
				$this->encapsulation = self::ENCAPSULATION_AESCBC128;
				return $buffer;
				break;

			case self::ENCAPSULATION_AESCTR256:
				$this->encapsulation = self::ENCAPSULATION_AESCBC256;
				return $buffer;
				break;

			default:
				// On encrypted comms the encryption will take care of transport encoding
				return $buffer;
				break;
		}

	}
	
	private function _apiDownloadDirect($config)
	{
		$defConfig = array(
			'backup_id'			=> 0,
			'part_id'			=> 1
		);
		$config = array_merge($defConfig, $config);
		extract($config);

		$backup_stats = AEPlatform::getInstance()->get_statistics($backup_id);
		if(empty($backup_stats))
		{
			// Backup record doesn't exist
			$this->status = self::STATUS_NOT_FOUND;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			@ob_end_clean();
			header('HTTP/1.1 500 Invalid backup record identifier');
			flush();
			JFactory::getApplication()->close();
		}
		$files = AEUtilStatistics::get_all_filenames($backup_stats);

		if( (count($files) < $part_id) || ($part_id <= 0) )
		{
			// Invalid part
			$this->status = self::STATUS_NOT_FOUND;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			@ob_end_clean();
			header('HTTP/1.1 500 Invalid backup part');
			flush();
			JFactory::getApplication()->close();
		}

		$filename = $files[$part_id-1];
		@clearstatcache();
		
		// For a certain unmentionable browser -- Thank you, Nooku, for the tip
		if(function_exists('ini_get') && function_exists('ini_set')) {
			if(ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}
		}
		
		// Remove php's time limit -- Thank you, Nooku, for the tip
		if(function_exists('ini_get') && function_exists('set_time_limit')) {
			if(!ini_get('safe_mode') ) {
			    @set_time_limit(0);
	        }
		}
		
		$basename = @basename($filename);
		$filesize = @filesize($filename);
		$extension = strtolower(str_replace(".", "", strrchr($filename, ".")));

		while (@ob_end_clean());
		@clearstatcache();
		// Send MIME headers
		header('MIME-Version: 1.0');
		header('Content-Disposition: attachment; filename='.$basename);
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		
		switch($extension)
		{
			case 'zip':
				// ZIP MIME type
				header('Content-Type: application/zip');
				break;

			default:
				// Generic binary data MIME type
				header('Content-Type: application/octet-stream');
				break;
		}
		// Notify of filesize, if this info is available
		if($filesize > 0) header('Content-Length: '.@filesize($filename));
		// Disable caching
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
		header('Pragma: no-cache');
		flush();
		if($filesize > 0)
		{
			// If the filesize is reported, use 1M chunks for echoing the data to the browser
			$blocksize = 1048756; //1M chunks
			$handle    = @fopen($filename, "r");
			// Now we need to loop through the file and echo out chunks of file data
			if($handle !== false) while(!@feof($handle)){
			    echo @fread($handle, $blocksize);
			    @ob_flush();
				flush();
			}
			if($handle !== false) @fclose($handle);
		} else {
			// If the filesize is not reported, hope that readfile works
			@readfile($filename);
		}
		flush();
		JFactory::getApplication()->close();
	}
	
	private function _apiUpdateGetInformation($config)
	{
		$defConfig = array(
			'force'			=> 0
		);
		$config = array_merge($defConfig, $config);
		extract($config);
		
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/liveupdate.php';
		
		$updateInformation = LiveUpdate::getUpdateInformation($force);
		
		return (object)$updateInformation;
	}
	
	private function _apiUpdateDownload($config)
	{
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/liveupdate.php';
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/classes/model.php';
		
		// Do we need to update?
		$updateInformation = LiveUpdate::getUpdateInformation();
		if(!$updateInformation->hasUpdates) {
			return (object)array(
				'download'	=> 0
			);
		}
		
		$model = new LiveupdateModel();
		$ret = $model->download();
		
		$session = JFactory::getSession();
		$target		= $session->get('target', '', 'liveupdate');
		$tempdir	= $session->get('tempdir', '', 'liveupdate');
		
		// Save the target and tempdir
		$session =& JFactory::getSession();
		$session->set('profile', 1, 'akeeba');
		AEPlatform::getInstance()->load_configuration(1);
		$config = AEFactory::getConfiguration();
		$config->set('remoteupdate.target', $target);
		$config->set('remoteupdate.tempdir', $tempdir);
		AEPlatform::getInstance()->save_configuration(1);
		
		if(!$ret) {
			// An error ocurred :(
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return "Could not download the update package";
		} else {
			return (object)array(
				'download'	=> 1
			);
		}
	}
	
	private function _apiUpdateExtract($config)
	{
		$session =& JFactory::getSession();
		$session->set('profile', 1, 'akeeba');
		AEPlatform::getInstance()->load_configuration(1);
		$config = AEFactory::getConfiguration();
		$target = $config->get('remoteupdate.target', '');
		$tempdir = $config->get('remoteupdate.tempdir', '');
		
		$session = JFactory::getSession();
		$session->set('target', $target, 'liveupdate');
		$session->set('tempdir', $tempdir, 'liveupdate');
		
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/liveupdate.php';
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/classes/model.php';
		
		$model = new LiveupdateModel();
		$ret = $model->extract();
		
		jimport('joomla.filesystem.file');
		JFile::delete($target);
		
		if(!$ret) {
			// An error ocurred :(
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return "Could not extract the update package";
		} else {
			return (object)array(
				'extract'	=> 1
			);
		}
	}
	
	private function _apiUpdateInstall($config) {
		$session =& JFactory::getSession();
		$session->set('profile', 1, 'akeeba');
		AEPlatform::getInstance()->load_configuration(1);
		$config = AEFactory::getConfiguration();
		$target = $config->get('remoteupdate.target', '');
		$tempdir = $config->get('remoteupdate.tempdir', '');
		
		$session = JFactory::getSession();
		$session->set('tempdir', $tempdir, 'liveupdate');
		
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/liveupdate.php';
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/classes/model.php';
		
		$model = new LiveupdateModel();
		$ret = $model->install();
		
		if(!$ret) {
			// An error ocurred :(
			$this->status = self::STATUS_ERROR;
			$this->encapsulation = self::ENCAPSULATION_RAW;
			return "Could not install the update package";
		} else {
			return (object)array(
				'install'	=> 1
			);
		}
	}
	
	private function _apiUpdateCleanup($config) {
		$session =& JFactory::getSession();
		$session->set('profile', 1, 'akeeba');
		AEPlatform::getInstance()->load_configuration(1);
		$config = AEFactory::getConfiguration();
		$target = $config->get('remoteupdate.target', '');
		$tempdir = $config->get('remoteupdate.tempdir', '');
		
		$session = JFactory::getSession();
		$session->set('target', $target, 'liveupdate');
		$session->set('tempdir', $tempdir, 'liveupdate');
		
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/liveupdate.php';
		require_once JPATH_ROOT.'/administrator/components/com_akeeba/liveupdate/classes/model.php';
		
		$model = new LiveupdateModel();
		$ret = $model->cleanup();
		
		jimport('joomla.filesystem.file');
		JFile::delete($target);

		$config->set('remoteupdate.target', null);
		$config->set('remoteupdate.tempdir', null);
		AEPlatform::getInstance()->save_configuration(1);

		return (object)array(
			'cleanup'	=> 1
		);
	}
	
	/**
	 * Encodes a variable to JSON using PEAR's Services_JSON
	 * @param mixed $value The value to encode
	 * @param int $options Encoding preferences flags
	 * @return string The JSON-encoded string
	 */
	private function json_encode($value, $options = 0) {
		$flags = SERVICES_JSON_LOOSE_TYPE;
		if( $options & JSON_FORCE_OBJECT ) $flags = 0;
		$encoder = new Akeeba_Services_JSON($flags);
		return $encoder->encode($value);
	}
	
	/**
	 * Decodes a JSON string to a variable using PEAR's Services_JSON
	 * @param string $value The JSON-encoded string
	 * @param bool $assoc True to return an associative array instead of an object
	 * @return mixed The decoded variable
	 */
	private function json_decode($value, $assoc = false)
	{
		$flags = 0;
		if($assoc) $flags = SERVICES_JSON_LOOSE_TYPE;
		$decoder = new Akeeba_Services_JSON($flags);
		return $decoder->decode($value);
	}
}