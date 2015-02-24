<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Logic: Site setup
 */

defined('_ABI') or die('Direct access is not allowed');

// Make sure we are in RAW mode
$output =& ABIOutput::getInstance();
$output->setMode('raw');

// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// of course PHP has to have the rights to delete the directory
// you specify and all files and folders inside the directory
// ------------------------------------------------------------
function recursive_remove_directory($directory, $empty=FALSE)
{
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}
	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;
	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return FALSE;
	// ... else if the path is readable
	}else{
		// we open the directory
		$handle = opendir($directory);
		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;
				// if the new path is a directory
				if(is_dir($path))
				{
					// we call this function with the new path
					recursive_remove_directory($path);
				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);
		// if the option to empty is not set to true
		if($empty == FALSE)
		{
			// try to delete the now empty directory
			if(!rmdir($directory))
			{
				// return false if not possible
				return FALSE;
			}
		}
		// return success
		return TRUE;
	}
}

// Set HTTP headers
header("Expires: Mon, 1 Dec 2003 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("MIME-Version: 1.0");
header("Content-Type: text/xml");

$act = getParam('act');

switch($act)
{
	case 'findFtpRoot':
		// Fetch data from the request parameters
		$ftp_host = getParam('ftp_host');
		$ftp_port = getParam('ftp_port');
		$ftp_user = getParam('ftp_user');
		$ftp_pass = getParam('ftp_pass');
		// Try to connect to the FTP
		require_once(JPATH_INSTALLATION.'/includes/ftp.php');
		$ftp =& ABIFtp::getInstance($ftp_host, $ftp_port, $ftp_user, $ftp_pass, null);
		if(!$ftp->connect(true))
		{
			// Could not connect to FTP
			$ret = array('error' => ABIText::_('ERROR_CANTCONNECTFTP'));
			break;
		}
		$root = $ftp->findRoot();
		if($root === false)
		{
			$ret = array('error' => ABIText::_('ERROR_CANTFINDROOT'));
		}
		else
		{
			// Make the return array
			$ret = array('root' => $root);
			// Save our successful settings to the configuration object
			require_once(JPATH_INSTALLATION.'/includes/configuration.php');
			$configuration =& ABIConfiguration::getInstance();
			$configuration->set('ftp_host',$ftp_host);
			$configuration->set('ftp_port',$ftp_port);
			$configuration->set('ftp_user',$ftp_user);
			$configuration->set('ftp_pass',$ftp_pass);
		}
		break;

	case 'checkFtp':
		// Fetch data from the request parameters
		$ftp_host = getParam('ftp_host');
		$ftp_port = getParam('ftp_port');
		$ftp_user = getParam('ftp_user');
		$ftp_pass = getParam('ftp_pass');
		$ftp_root = getParam('ftp_root');
		// Try to connect to the FTP
		require_once(JPATH_INSTALLATION.'/includes/ftp.php');
		$ftp =& ABIFtp::getInstance($ftp_host, $ftp_port, $ftp_user, $ftp_pass, $ftp_root);
		if(!$ftp->connect(true))
		{
			// Could not connect to FTP
			$ret = array('error' => ABIText::_('ERROR_CANTCONNECTFTP'));
			break;
		}
		else
		{
			// Make the return array
			$ret = array('error' => ''); // Blank error means OK!
			// Save our successful settings to the configuration object
			require_once(JPATH_INSTALLATION.'/includes/configuration.php');
			$configuration =& ABIConfiguration::getInstance();
			$configuration->set('ftp_host',$ftp_host);
			$configuration->set('ftp_port',$ftp_port);
			$configuration->set('ftp_user',$ftp_user);
			$configuration->set('ftp_pass',$ftp_pass);
			$configuration->set('ftp_root',$ftp_root);
		}
		break;

	case 'deleteself':
		recursive_remove_directory(JPATH_INSTALLATION);
		unlink(JPATH_BASE.'/akeeba_connection_test.png');
		$ret = array('success' => true);
		break;

	default:
		$ret = array('error' => ABIText::_('ERROR_INVALIDCOMMAND'));
}

echo renderXML($ret);