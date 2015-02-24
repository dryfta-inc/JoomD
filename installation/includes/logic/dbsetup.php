<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Logic: Database restoration setup
 */

defined('_ABI') or die('Direct access is not allowed');

global $view; // Import global $view variable
$output =& ABIOutput::getInstance();

// Load variables off the storage
$storage =& ABIStorage::getInstance();
$valid = $storage->get('installerflag', '0');
if(!$valid) {
	$output->setError(ABIText::_('ERROR_STORAGE_NOT_WORKING').'<br/><a href="https://www.akeebabackup.com/documentation/troubleshooter/abisession.html" target="_blank">'.ABIText::_('GENERIC_HELPME_CLICKHERE').'</a>');
}
$laststep = $storage->get('step', null); // The last step we finished executing
$databases = $storage->get('databases', null); // The databases array, holding connection data to all the backed up databases
$activeDatabase = $storage->get('activedatabase', null); // The active database; that's the INI section name in the databases.ini file

// If the storage doesn't have the databases array set, load the databases.ini
if(is_null($databases))
{
	$dbini_rel = 'sql/databases.ini';
	$dbini_abs = JPATH_INSTALLATION.'/'.$dbini_rel;

	// Can we read the relative filename?
	if(@file_exists($dbini_rel) && @is_readable($dbini_rel))
	{
		$databasesIniFilename = $dbini_rel; // Yes, use relative path
	} else {
		$databasesIniFilename = $dbini_abs; // No, use absolute path
	}

	// Try loading databases.ini
	$databases = _parse_ini_file($databasesIniFilename, true);
	
	if(empty($databases)) {
		// Quit with an error if the databases.ini is not present
		$output->setError(ABIText::_('ERROR_NODBINI'));
		$output->setButtons("submitForm('index')",null);
		return;
	}

	$storage->set('databases', $databases);
}

// Try to override the database information from whatever is stored in the automation INI
$automation =& ABIAutomation::getInstance();
if($automation->hasAutomation())
{
	foreach($databases as $name => $data)
	{
		$autoData = $automation->getSection($name);
		if(count($autoData) > 0)
		{
			// Get rid of the SQL file name...
			if(isset($autoData['sqlfile'])) unset($autoData['sqlfile']);
			$databases[$name] = array_merge($data, $autoData);
		}
	}
	$storage->set('databases', $databases);
}

// If the task is dbnext or dbprev, first find the right database to restore
$task = getParam('task','db');
if($task == 'dbnext') // Find the next database
{
	// If the last step was 'index', the storage was erased, so there is no need
	// to check this and forcibly use the first database definition; it happens
	// automatically.

	// Get all db entries section names from databases.ini
	$dbkeys = array_keys($databases);
	if(is_null($activeDatabase))
	{
		// No active database, frocibly use the first entry
		$activeDatabase = $dbkeys[0];
	}
	else
	{
		// Find the next database
		$found = false;
		$newActiveDatabase = '';
		foreach($dbkeys as $key)
		{
			if($found)
			{
				$newActiveDatabase = $key;
				break;
			}
			if($key == $activeDatabase) $found = true;
		}
		if($newActiveDatabase == $activeDatabase)
		{
			// We have already hit the last element? THIS MUST NEVER, EVER HAPPEN!
			//die('Tried to step beyond the last database. This should never happen.');
			$activeDatabase = $dbkeys[count($dbkeys) - 1];
		}
		elseif($newActiveDatabase != '')
			$activeDatabase = $newActiveDatabase;
	}
	$storage->set('activedatabase', $activeDatabase);
}
elseif($task == 'dbprev') // Find the previous database
{
	// Get all db entries section names from databases.ini
	$dbkeys = array_keys($databases);
	// If the previous step was 'setup', we do not try to step back one database,
	// but reshow the last database's setup page. In any other case, we proceed
	// business as usual
	if($storage->get('step') == 'setup')
	{
		if(is_null($activeDatabase))
		{
			// No active database, frocibly use the last entry
			$activeDatabase = array_pop($dbkeys);
			$dbkeys[] = $activeDatabase;
		}
	}
	else
	{
			// Find the previous database
			$found = false;
			$dbkeys = array_reverse($dbkeys);  // Reverse the array. Now, we can run the same algorithm as 'find next database'.
			foreach($dbkeys as $key)
			{
				$newActiveDatabase = $key;
				if($found)
				{
					break;
				}
				if($key == $activeDatabase) $found = true;
			}
			if($newActiveDatabase == $activeDatabase)
			{
				// We have already hit the last element? THIS MUST NEVER, EVER HAPPEN!
				die('Tried to step before the first database. This should never happen.');
			}
			$activeDatabase = $newActiveDatabase;
			// Finally return the $dbkeys array to its previous sorting status
			array_reverse($dbkeys);
	}
	$storage->set('activedatabase', $activeDatabase);
}

// Check if this is the first or last database. This information is used to decide on
// what the Next/Previous buttons do!
if(count($dbkeys) == 1)
{
	$firstDatabase = $dbkeys[0];
	$lastDatabase = $firstDatabase;
}
else
{
	$firstDatabase = array_shift($dbkeys);
	$lastDatabase = array_pop($dbkeys);
}
if($activeDatabase == $firstDatabase)
{
	$storage->set('databaseposition','first');
} elseif($activeDatabase == $lastDatabase)
{
	$storage->set('databaseposition','last');
} else {
	$storage->set('databaseposition', 'middle');
}

// Fetch the active database's parameters
$parameters = $databases[$activeDatabase];

// Pass the results to the global $view array
$view =& $parameters;

// Add a friendly database name
if($activeDatabase == 'joomla.sql')
{
	$view['friendlyName'] = ABIText::_('MAINDB');
}
else
{
	$view['friendlyName'] = ABIText::_('EXTRADB').' '.$activeDatabase;
}

// Set the database driver, in case it's not present
if(!isset($view['dbtype']))
{
		$view['dbtype'] = 'mysqli';
}

// Set fine-tuning options if not present
if(!isset($view['suppressfk'])) $view['suppressfk'] = true;
if(!isset($view['maxtime'])) $view['maxtime'] = 5;
if(!isset($view['existing'])) $view['existing'] = 'drop';
if(!isset($view['replacesql'])) $view['replacesql'] = false;
if(!isset($view['forceutf8'])) $view['forceutf8'] = false;

// Get any fine-tuning options from the automation, if defined
if($automation->hasAutomation())
{
	if( isset($databases[$activeDatabase]['suppressfk']) )
	{
		$view['suppressfk'] = ($databases[$activeDatabase]['suppressfk'] == 'on');
	}

	if( isset($databases[$activeDatabase]['maxtime']) )
	{
		$view['maxtime'] = $databases[$activeDatabase]['maxtime'];
	}

	if( isset($databases[$activeDatabase]['replacesql']) )
	{
		$view['replacesql'] = ($databases[$activeDatabase]['replacesql'] == 'on');
	}

	if( isset($databases[$activeDatabase]['forceutf8']) )
	{
		$view['forceutf8'] = ($databases[$activeDatabase]['forceutf8'] == 'on');
	}
}

// Check the host name in order to show a warnign dialog
$newhost = $_SERVER['HTTP_HOST'];
$extrainfo = _parse_ini_file('extrainfo.ini', false);
if(!array_key_exists('host', $extrainfo)) {
	$view['showdialog'] = true;
} else {
	$view['showdialog'] = $newhost != $extrainfo['host'];
}

// Make sure the stuff we might have added in the database definition are stored between page reloads
$databases[$activeDatabase] = $view;
$storage->set('databases', $databases);

// Set some output parameters - The previous/next buttons
if($activeDatabase == 'joomla.sql')
{
	// First database. Previous leads to index.

	if(count($databases) == 1)
	{
		// If it's also the ONLY database, next leads to setup
		$output->setButtons("submitForm('index')","submitForm('setup')");
	}
	else
	{
		$output->setButtons("submitForm('index')","submitForm('dbnext')");
	}
} elseif($storage->get('databaseposition') == 'last') {
	// Last database. The next button leads to setup
	$output->setButtons("submitForm('dbprev')","submitForm('setup')");
} else {
	// Middle position
	$output->setButtons("submitForm('dbprev')","submitForm('dbnext')");
}

// Notify that we are in the db step
$output->setActiveStep('db');
$storage->set('step', 'db');