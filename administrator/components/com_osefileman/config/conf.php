<?php
// ensure this file is being included by a parent file
defined('_JEXEC') or die('Restricted access');
//------------------------------------------------------------------------------
// Configuration Variables
// login to use joomlaXplorer: (true/false)
$GLOBALS["require_login"] = false;
//$lang = JLanguage::getInstance( $lang );
	$lang = JFactory::getLanguage();
	$filedir = $lang->getDefault();
	$GLOBALS["language"] = $filedir;
	// the filename of the QuiXplorer script: (you rarely need to change this)
	if($_SERVER['SERVER_PORT'] == 443 ) {
		$GLOBALS["script_name"] = "https://".$GLOBALS['__SERVER']['HTTP_HOST'].$GLOBALS['__SERVER']["PHP_SELF"];
	}
	elseif ($_SERVER['SERVER_PORT'] == 2087 )
	{
		$GLOBALS["script_name"] = "https://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
	}
	elseif ($_SERVER['SERVER_PORT'] != 80 )
	{
		$GLOBALS["script_name"] = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
	}
	else {
			$GLOBALS["script_name"] = "http://".$GLOBALS['__SERVER']['HTTP_HOST'].$GLOBALS['__SERVER']["PHP_SELF"];
	}

	// allow Zip, Tar, TGz -> Only (experimental) Zip-support
	if( function_exists("gzcompress")) {
	  	$GLOBALS["zip"] = $GLOBALS["tgz"] = true;
	}
	else {
	  	$GLOBALS["zip"] = $GLOBALS["tgz"] = false;
	}

//------------------------------------------------------------------------------
// Global User Variables (used when $require_login==false)

	if( strstr( JPATH_BASE, "/" )) {
		$GLOBALS["separator"] = "/";
	}
	else {
		$GLOBALS["separator"] = "\\";
	}

	// the home directory for the filemanager: (use '/', not '\' or '\\', no trailing '/')

	// !Note! This has been changed since joomlaXplorer 1.3.0
	// and now grants access to all directories for one level ABOVE this Site
	$dir_above = substr( JPATH_ROOT, 0, strrpos( JPATH_ROOT, $GLOBALS["separator"] ));

	if( !@is_readable($dir_above) || !is_dir($dir_above) ) {
		$GLOBALS["home_dir"] = JPATH_ROOT;
		// the url corresponding with the home directory: (no trailing '/')
		$GLOBALS["home_url"] = JURI::root();
	}
	else {
		$GLOBALS["home_dir"] = $dir_above;
		// the url corresponding with the home directory: (no trailing '/')
		$GLOBALS["home_url"] = substr( JURI::root(), 0, strrpos(JURI::root(), '/'));
	}

	// show hidden files in QuiXplorer: (hide files starting with '.', as in Linux/UNIX)
	$GLOBALS["show_hidden"] = true;

	// filenames not allowed to access: (uses PCRE regex syntax)
	$GLOBALS["no_access"] = "^\.ht";

	// user permissions bitfield: (1=modify, 2=password, 4=admin, add the numbers)
	$GLOBALS["permissions"] = 7;
//------------------------------------------------------------------------------
/* NOTE:
	Users can be defined by using the Admin-section,
	or in the file "config/.htusers.php".
	For more information about PCRE Regex Syntax,
	go to http://www.php.net/pcre.pattern.syntax
*/
//------------------------------------------------------------------------------
?>
