<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer FTP Connection Class
 * Adapted from JoomlaPack UNITE, the unattended site installation suite
 */

defined('_ABI') or die('Direct access is not allowed');

class ABIFtp
{
	var $error;
	var $ftphost;
	var $ftpport;
	var $ftpuser;
	var $ftppass;
	var $ftpdir;
	var $_handle;

	/**
	 * Class constructor
	 * @param $host string The FTP hostname
	 * @param $port int The FTP port number
	 * @param $user string The FTP user name
	 * @param $pass string The FTP password
	 * @param $dir string The FTP initial directory
	 * @return ABIFtp
	 */
	function ABIFtp($host, $port, $user, $pass, $dir)
	{
		$this->ftphost = $host;
		$this->ftpport = $port;
		$this->ftpuser = $user;
		$this->ftppass = $pass;
		if(substr($dir,0,1) != '/') $dir = '/'.$dir;
		$this->ftpdir = is_null($dir) ? '' : $dir;
	}

	/**
	 * Singleton implementation
	 * @param $host string The FTP hostname. If it's non empty, it forcibly creates a new instance
	 * @param $port int The FTP port number
	 * @param $user string The FTP user name
	 * @param $pass string The FTP password
	 * @param $dir string The FTP initial directory
	 * @return FTPConnector
	 */
	static function &getInstance($host = '', $port = '', $user = '', $pass = '', $dir = '')
	{
		static $instance;

		// Forcibly create new instance if host parameter exists
		if(!empty($host))
		{
			if(is_object($instance)) $instance->disconnect();
			$instance = null;
		}

		// Create a new isntance if it doesn't exist
		if( !is_object($instance) || (!empty($host)) )
		{
			$instance = new ABIFtp($host, $port, $user, $pass, $dir);
		}

		return $instance;
	}

	/**
	 * Returns the last error message
	 * @return string The error message
	 */
	function getError()
	{
		return $this->error;
	}

	/**
	 * Tries to connect to the FTP server
	 * @return bool True on success
	 */
	function connect($ignoredir = false)
	{
		if($this->ftpdir == '') $ignoredir = true; // If no initial directory was passed, skip changing to it (quite obvious why is that)

		// Connect to server
		$this->_handle = @ftp_connect($this->ftphost, $his->ftpport);
		if($this->_handle === false)
		{
			$this->error='Wrong FTP host';
			return false;
		}

		// Login
		if(! @ftp_login($this->_handle, $this->ftpuser, $this->ftppass))
		{
			$this->error='Wrong FTP username and/or password';
			@ftp_close($this->_handle);
			return false;
		}

		if(!$ignoredir)
		{
			// Change to initial directory
			if(! @ftp_chdir($this->_handle, $this->ftpdir))
			{
				$this->error='Wrong FTP initial directory';
				@ftp_close($this->_handle);
				return false;
			}
		}

		// Use passive mode
		@ftp_pasv($this->_handle, true);
		return true;
	}

	/**
	 * Disconnects from the FTP server
	 */
	function disconnect()
	{
		@ftp_close($this->_handle);
	}

	/**
	 * Returns true if the given FTP directory exists
	 * @param $dir string The directory to check for
	 * @return bool True if the directory exists
	 */
	function is_dir( $dir )
	{
		return @ftp_chdir( $this->_handle, $dir );
	}

	/**
	 * Recursively creates an FTP directory if it doesn't exist
	 * @param $dir The directory to create
	 * @return bool True on success, false if creation failed
	 */
	function makeDirectory( $dir )
	{
		$check = '/'.trim($this->ftpdir,'/').'/'.$dir;
		if($this->is_dir($check)) return true;

		$alldirs = explode('/', $dir);
		$previousDir = '/'.trim($this->ftpdir);
		foreach($alldirs as $curdir)
		{
			$check = $previousDir.'/'.$curdir;
			if(!$this->is_dir($check))
			{
				if(@ftp_mkdir($this->_handle, $check) === false)
				{
					$this->error = 'Could not create directory '.$dir;
					return false;
				}
				@ftp_chmod($this->_handle, 0755, $check);
			}
			$previousDir = $check;
		}

		return true;
	}

	function changeToInitialDirectory()
	{
		return @ftp_chdir($this->_handle, $this->ftpdir);
	}

	function getPwd()
	{
		return @ftp_pwd($this->_handle);
	}

	function listDetails($path = null, $type = 'all')
	{
		// Adapted from Joomla!'s FTP class

		// Initialize variables
		$dir_list = array();
		$data = null;
		$regs = null;
		// For now we will just set it to false
		$recurse = false;

		if (($contents = @ftp_rawlist($this->_handle, $path)) === false) {
			return false;
		}

		// If only raw output is requested we are done
		if ($type == 'raw') {
			return $data;
		}

		// If we received the listing of an emtpy directory, we are done as well
		if (empty($contents[0])) {
			return $dir_list;
		}

		// If the server returned the number of results in the first response, let's dump it
		if (strtolower(substr($contents[0], 0, 6)) == 'total ') {
			array_shift($contents);
			if (!isset($contents[0]) || empty($contents[0])) {
				return $dir_list;
			}
		}

		// Regular expressions for the directory listing parsing
		$regexps['UNIX'] = '([-dl][rwxstST-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{1,2}:[0-9]{2})|[0-9]{4}) (.+)';
		$regexps['MAC'] = '([-dl][rwxstST-]+).* ?([0-9 ]* )?([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)';
		$regexps['WIN'] = '([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)';

		// Find out the format of the directory listing by matching one of the regexps
		$osType = null;
		foreach ($regexps as $k=>$v) {
			if (ereg($v, $contents[0])) {
				$osType = $k;
				$regexp = $v;
				break;
			}
		}
		if (!$osType) {
			return false;
		}

		/*
		 * Here is where it is going to get dirty....
		 */
		if ($osType == 'UNIX') {
			foreach ($contents as $file) {
				$tmp_array = null;
				if (ereg($regexp, $file, $regs)) {
					$fType = (int) strpos("-dl", $regs[1] { 0 });
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = $regs[1];
					//$tmp_array['number'] = $regs[2];
					$tmp_array['user'] = $regs[3];
					$tmp_array['group'] = $regs[4];
					$tmp_array['size'] = $regs[5];
					$tmp_array['date'] = date("m-d", strtotime($regs[6]));
					$tmp_array['time'] = $regs[7];
					$tmp_array['name'] = $regs[9];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1) {
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0) {
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..') {
					$dir_list[] = $tmp_array;
				}
			}
		}
		elseif ($osType == 'MAC') {
			foreach ($contents as $file) {
				$tmp_array = null;
				if (ereg($regexp, $file, $regs)) {
					$fType = (int) strpos("-dl", $regs[1] { 0 });
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = $regs[1];
					//$tmp_array['number'] = $regs[2];
					$tmp_array['user'] = $regs[3];
					$tmp_array['group'] = $regs[4];
					$tmp_array['size'] = $regs[5];
					$tmp_array['date'] = date("m-d", strtotime($regs[6]));
					$tmp_array['time'] = $regs[7];
					$tmp_array['name'] = $regs[9];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1) {
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0) {
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..') {
					$dir_list[] = $tmp_array;
				}
			}
		} else {
			foreach ($contents as $file) {
				$tmp_array = null;
				if (ereg($regexp, $file, $regs)) {
					$fType = (int) ($regs[7] == '<DIR>');
					$timestamp = strtotime("$regs[3]-$regs[1]-$regs[2] $regs[4]:$regs[5]$regs[6]");
					//$tmp_array['line'] = $regs[0];
					$tmp_array['type'] = $fType;
					$tmp_array['rights'] = '';
					//$tmp_array['number'] = 0;
					$tmp_array['user'] = '';
					$tmp_array['group'] = '';
					$tmp_array['size'] = (int) $regs[7];
					$tmp_array['date'] = date('m-d', $timestamp);
					$tmp_array['time'] = date('H:i', $timestamp);
					$tmp_array['name'] = $regs[8];
				}
				// If we just want files, do not add a folder
				if ($type == 'files' && $tmp_array['type'] == 1) {
					continue;
				}
				// If we just want folders, do not add a file
				if ($type == 'folders' && $tmp_array['type'] == 0) {
					continue;
				}
				if (is_array($tmp_array) && $tmp_array['name'] != '.' && $tmp_array['name'] != '..') {
					$dir_list[] = $tmp_array;
				}
			}
		}

		return $dir_list;
	}

	function read($remote, &$buffer)
	{
		$tmpFile = tmpfile();

		if(@$tmpFile === false)
		{
			return false;
		}

		if(@ftp_fget($this->_handle, $tmpFile, $remote, FTP_BINARY) === false)
		{
			fclose($tmpFile);
			return false;
		}

		// Read tmp buffer contents
		rewind($tmpFile);
		$buffer = '';
		while (!feof($tmpFile)) {
			$buffer .= fread($tmpFile, 8192);
		}
		fclose($tmpFile);
		return true;
	}

	function write($remote, &$buffer)
	{
		$tmpFile = tmpfile();

		if(@$tmpFile === false)
		{
			return false;
		}

		@fwrite($tmpFile, $buffer);
		@fflush($tmpFile);
		@fseek($tmpFile, 0);

		$result = @ftp_fput($this->_handle, $remote, $tmpFile, FTP_BINARY);
		@fclose($tmpFile);
		return $result;
	}

	/**
	 * Automatically find the Joomla! root directory. You must have already logged
	 * in, ignoring the initial directory setting.
	 * @return string|bool The root path, or false if we couldn't find it.
	 */
	function findRoot()
	{
		$ftpPaths = array();

		// Get the FTP CWD, in case it is not the FTP root
		$cwd = $this->getPwd();
		if ($cwd === false) {
			return false;
		}
		$cwd = rtrim($cwd, '/');

		// Get list of folders in the CWD
		$ftpFolders = $this->listDetails(null, 'folders');
		if ($ftpFolders === false || count($ftpFolders) == 0) {
			return false;
		}
		for ($i=0, $n=count($ftpFolders); $i<$n; $i++) {
			$ftpFolders[$i] = $ftpFolders[$i]['name'];
		}

		// Check if Joomla! is installed at the FTP CWD
		$dirList = array('administrator', 'components', 'installation', 'language', 'libraries', 'plugins');
		if (count(array_diff($dirList, $ftpFolders)) == 0) {
			$ftpPaths[] = $cwd.'/';
		}

		// Process the list: cycle through all parts of JPATH_SITE, beginning from the end
		$parts		= explode(DIRECTORY_SEPARATOR, JPATH_SITE);
		$tmpPath	= '';
		for ($i=count($parts)-1; $i>=0; $i--)
		{
			$tmpPath = '/'.$parts[$i].$tmpPath;
			if (in_array($parts[$i], $ftpFolders)) {
				$ftpPaths[] = $cwd.$tmpPath;
			}
		}

		// Check all possible paths for the real Joomla! installation
		$checkValue = @file_get_contents(JPATH_SITE.'/includes/joomla/version.php');
		foreach ($ftpPaths as $tmpPath)
		{
			$filePath = rtrim($tmpPath, '/').'/libraries/joomla/version.php';
			$buffer = null;
			@$this->read($filePath, $buffer);
			if ($buffer == $checkValue)
			{
				$ftpPath = $tmpPath;
				break;
			}
		}

		// Return the FTP root path
		if (isset($ftpPath)) {
			return $ftpPath;
		} else {
			return false;
		}
	}

	function recursiveDelete($directory)
	{
	    # here we attempt to delete the file/directory
	    @ftp_chdir($this->_handle, $this->ftpdir.'/'.$directory);
	    if( !(@ftp_rmdir($this->_handle, $directory) || @ftp_delete($this->_handle, $directory)) )
	    {
	        # if the attempt to delete fails, get the file listing
	        $filelist = @ftp_rawlist($this->_handle, $this->ftpdir.'/'.$directory);

	        # loop through the file list and recursively delete the FILE in the list
	        foreach($filelist as $current)
	        {
	        	if(empty($current)) continue;
	        	$split = preg_split('[ ]', $current, 9, PREG_SPLIT_NO_EMPTY);
	        	$file = $this->ftpdir.'/'.$directory.'/'.$split[8];
	        	$isdir = ($split[0]{0} === 'd') ? true : false;

	        	if($isdir)
	        	{
	            	$this->recursiveDelete($file);
	        	}
	        	else
	        	{
	        		@ftp_chdir($this->_handle, $this->ftpdir.'/'.$directory);
	        		@ftp_delete($this->_handle, $file);
	        	}
	        }

	        #if the file list is empty, delete the DIRECTORY we passed
	        @ftp_chdir($this->_handle, $this->ftpdir.'/'.$directory);
	        if(!(@ftp_rmdir($this->_handle, $directory))) @ftp_delete($this->_handle, $directory);
	    }
	}
}