<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer main page
 */

// Flag this as a parent file
define('_ABI', '1.0');

// Minimum script execution time: 2 seconds
define('MINEXECTIME', 2000);

// Remove error reporting
@error_reporting(E_NONE);

// Setup some useful constants
$abspath = dirname(__FILE__);
if(empty($abspath)) $abspath = '.';
// Try to determine the absolute dir to the site
$siteroot = @realpath($abspath.'/..');
if(strlen($siteroot) == 0) $siteroot = '';
define('JPATH_SITE', $siteroot );
define('JPATH_BASE', $siteroot );
define('JPATH_INSTALLATION', $abspath );

// Output buffering begins before we start doing anything at all
@ob_start();

// Load base files
define('_JEXEC',1);
require_once(JPATH_INSTALLATION.'/version.php'); // Utilities
require_once(JPATH_INSTALLATION.'/includes/utils.php'); // Utilities
require_once(JPATH_INSTALLATION.'/includes/translate.php'); // Translation
require_once(JPATH_INSTALLATION.'/includes/storage.php'); // Temporary Storage
require_once(JPATH_INSTALLATION.'/includes/output.php'); // Output class
require_once(JPATH_INSTALLATION.'/includes/automation.php'); // Automation class
require_once(JPATH_INSTALLATION.'/includes/antidos.php'); // Protection from anti-DoS solutions (no more 403's!)

// Initialize the global $view array
global $view;
unset($view); // Destroy any variable trickily passed to this script...
$view = array(); // Initialize to an empty array

// Enforce minimum script execution time (start-up)
enforce_minexectime(true);

// Run the logic depending on the task
$task = getParam('task','index');
switch($task)
{
	case "index": // Requirements check
		require_once(JPATH_INSTALLATION.'/includes/logic/index.php'); // Run the logic
		require_once(JPATH_INSTALLATION.'/includes/output/index.php'); // Run the view
		break;

	case "dbnext": // Iterate to the next database
	case "dbprev": // Iterate to the previous database
		require_once(JPATH_INSTALLATION.'/includes/logic/dbsetup.php'); // Run the logic
		require_once(JPATH_INSTALLATION.'/includes/output/dbsetup.php'); // Run the view
		break;

	case "restore": // Restores the current database (called by AJAX)
		require_once(JPATH_INSTALLATION.'/includes/logic/restore.php'); // Run the logic
		// There is no "view" for this page. The logic produces the AJAX output.
		break;

	case "setup": // Site setup
		require_once(JPATH_INSTALLATION.'/includes/logic/setup.php'); // Run the logic
		require_once(JPATH_INSTALLATION.'/includes/output/setup.php'); // Run the view
		break;

	case "ajax": // AJAX power for site setup, e.g. FTP check
		require_once(JPATH_INSTALLATION.'/includes/logic/ajax.php'); // Run the logic
		// There is no "view" for this page. The logic produces the AJAX output.
		break;

	case "finish": // We just finished!
		require_once(JPATH_INSTALLATION.'/includes/logic/finish.php'); // Run the logic
		require_once(JPATH_INSTALLATION.'/includes/output/finish.php'); // Run the view
		break;

	default:
		// This is something not allowed. Die.
		die('Invalid task');
		break;
}

// Get the page's output
$content = ob_get_clean();

// Send the page data
$output =& ABIOutput::getInstance();
$output->setContent($content);
$output->output();

// Finally, save the Storage
$storage =& ABIStorage::getInstance();
$storage->saveData();

// Enforce minimum script execution time (finalization)
enforce_minexectime(false);