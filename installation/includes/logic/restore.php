<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Logic: Database restoration
 * Based on BigDump by Alexey Ozerov
 */

defined('_ABI') or die('Direct access is not allowed');

// A list of MySQL errors we can safely ignore
// Reference: http://dev.mysql.com/doc/refman/4.1/en/error-messages-server.html
$skipMySQLErrorNumbers = array(
	1262,
	1263,
	1264,
	1265,	// "Data truncated" warning
	1266,
	1287,
	1299
	// , 1406	// "Data too long" error
);

$partsMap = array();

require_once( JPATH_INSTALLATION.'/includes/db.php' );

/**
 * Returns the remaining time before we have to break step
 * @param bool $reset If true, the stopwatch is reset
 * @param int $newmax If $reset == true, sets the new time limit
 */
function getTimeRemaining($reset = false, $newmax = 5)
{
	static $start = 0;
	static $maxAllowed = 5;

	if($reset) {
		$start = microtime(true);
		$maxAllowed = $newmax;
	}

	return $maxAllowed - (microtime(true) - $start);
}

/**
 * Removes the volatile information from the storage
 */
function removeInformationFromStorage()
{
	$storage =& ABIStorage::getInstance();
	$storage->remove('start');
	$storage->remove('foffset');
	$storage->remove('totalqueries');
	$storage->remove('curpart');
	$storage->remove('partsmap');
	$storage->saveData();
}

/**
 * Throws an error back to the caller via an XML response
 * @param string $message The error message to return
 */
function throwError($message)
{
	// Render the AJAX response containing the error
	$ret = array('error' => $message);
	echo renderXML($ret);
	// Remove information from the storage
	removeInformationFromStorage();
	die();
}

/**
 * Sends a var_dump of each and every parameter passed to this function to the caller,
 * as an error message through an XML reponse. Used for debugging only.
 */
function throwDump()
{
	$parameters = func_num_args();

	$msg = '';
	@ob_start();
	for($i = 0; $i < $parameters; $i++)
	{
		$msg .= "<pre>";
		var_dump(func_get_arg($i));
		$msg .= ob_get_contents() . '</pre><br/>';
		ob_clean();
	}
	ob_end_clean();
	throwError($msg);
}

/**
 * Figures out how many SQL parts we have and populates the $partsMap array
 */
function getPartsMap()
{
	global $partsMap, $parts, $sqlfile, $totalSize, $runSize;

	$storage =& ABIStorage::getInstance();

	$partsMap = array();
	$path = JPATH_INSTALLATION.'/sql';
	$totalSize = 0;
	$runSize = 0;

	for($index = 0; $index <= $parts; $index++)
	{
		if($index == 0)
		{
			$basename = $sqlfile;
		}
		else
		{
			$basename = substr($sqlfile, 0, -4).'.s'.sprintf('%02u', $index);
		}
		
		$file = $path.'/'.$basename;
		if(!file_exists($file)) {
			$file = 'sql/'.$basename;
		}
		$filesize = @filesize($file) ;
		$totalSize += intval($filesize);
		$partsMap[] = $file;
	}

	$storage->set('totalsize', $totalSize);
	$storage->set('runsize', $runSize);
	$storage->saveData();
}

/**
 * Proceeds to opening the next SQL part file
 * @return bool True on success
 */
function getNextFile()
{
	global $partsMap, $curpart, $foffset, $parts;

	$storage =& ABIStorage::getInstance();

	if( $curpart >= ($parts - 1) ) return false;

	$curpart++;
	$foffset = 0;
	$storage->set('curpart', $curpart);
	$storage->set('foffset', $foffset);
	$storage->saveData();

	return openFile();
}

/**
 * Opens the SQL part file whose ID is specified in the $curpart global variable and updates
 * the $file, $start and $foffset variables
 * @return bool True on success
 */
function openFile()
{
	global $file, $filename, $partsMap, $curpart, $foffset, $start;

	$filename = $partsMap[$curpart];

	if ( !$file = @fopen($filename, "rt") ) {
		throwError(ABIText::_('ERROR_CANTOPENDUMPFILE').' '.$filename);
		return false;
	}
	else
	{
		// Get the file size
		if (fseek($file, 0, SEEK_END) == 0) {
			$filesize = ftell($file);
		} else {
			throwError(ABIText::_('ERROR_UNKNOWNFILESIZE'));
			return false;
		}
	}

	// Check start and foffset are numeric values
	if (!is_numeric($start) || !is_numeric($foffset))
	{
		throwError(ABIText::_('ERROR_INVALIDPARAMETERS'));
		return false;
	}

	$start = floor($start);
	$foffset = floor($foffset);

	// Check $foffset upon $filesize
	if ($foffset > $filesize)
	{
		throwError(ABIText::_('ERROR_AFTEREOF'));
		return false;
	}

	// Set file pointer to $foffset
	if (fseek($file, $foffset) != 0)
	{
		throwError(ABIText::_('ERROR_CANTSETOFFSET'));
		return false;
	}

	return true;
}


// Make sure we are in RAW mode
$output =& ABIOutput::getInstance();
$output->setMode('raw');

// Get data from the Storage
$storage =& ABIStorage::getInstance();
$partsMap = $storage->get('partsmap', null);
$totalSize = $storage->get('totalsize', null);
$runSize = $storage->get('runsize', null);
$databases = $storage->get('databases');
$activeDatabase = $storage->get('activedatabase');

// Check that we have an active database and a list of databases to begin with
if(!is_array($databases) || !is_string($activeDatabase))
{
	throwError(ABIText::_('ERROR_NODATABASE').' '.$activeDatabase);
	return;
}

// Load the active database parameter in the current namespace
extract($databases[$activeDatabase]);

// Parse request data, if any
$dbtype		= getParam('dbtype', $dbtype);
$dbhost		= getParam('dbhost', $dbhost);
$dbuser		= getParam('dbuser', $dbuser, true);
$dbpass		= getParam('dbpass', $dbpass, true);
$dbname		= getParam('dbname', $dbname, true);
$existing	= @getParam('existing', $existing);
$prefix		= getParam('prefix', $prefix);
$suppressfk = getParam('suppressfk', $suppressfk);
/* -- OBSOLETE --
$maxchunk	= getParam('maxchunk', $maxchunk);
$maxqueries = getParam('maxqueries', $maxqueries);
*/
$maxtime = getParam('maxtime', $maxtime);
$replacesql	= getParam('replacesql', $replacesql);
$forceutf8	= getParam('forceutf8', $forceutf8);

// Make sure the up-to-date data are stored
$databases[$activeDatabase]['dbtype'] = $dbtype;
$databases[$activeDatabase]['dbhost'] = $dbhost;
$databases[$activeDatabase]['dbuser'] = $dbuser;
$databases[$activeDatabase]['dbpass'] = $dbpass;
$databases[$activeDatabase]['dbname'] = $dbname;
$databases[$activeDatabase]['existing'] = $existing;
$databases[$activeDatabase]['prefix'] = $prefix;
$databases[$activeDatabase]['suppressfk'] = $suppressfk;
/* -- OBSOLETE --
$databases[$activeDatabase]['maxchunk'] = $maxchunk;
$databases[$activeDatabase]['maxqueries'] = $maxqueries;
*/
$databases[$activeDatabase]['maxtime'] = $maxtime;
$databases[$activeDatabase]['replacesql'] = $replacesql;
$databases[$activeDatabase]['forceutf8'] = $forceutf8;
$storage->set('databases', $databases);

define('DATA_CHUNK_LENGTH',	65536);			// How many bytes to read per step
define('MAX_QUERY_LINES',	300);			// How many lines may be considered to be one query (except text lines)
/* -- OBSOLETE --
define('LINESPERSESSION',	$maxqueries);	// Maximum lines to be executed per one import session
define('BYTESPERSESSION',	$maxchunk);		// Maximum data to be restored per one import session
*/

// Allowed comment delimiters: lines starting with these strings will be dropped by BigDump
$comment[] = '#';
$comment[] = '-- ';
$comment[]='---';
$comment[]='/*!';

// Start the timer
getTimeRemaining(true, $maxtime);

header("Expires: Mon, 1 Dec 2003 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("MIME-Version: 1.0");
header("Content-Type: text/xml");

// Initialization
$file = false;
$foffset = $storage->get('foffset', 0);
$curpart = $storage->get('curpart', 0);
if( empty($partsMap) || ($foffset == 0) ) {
	getPartsMap();
	$storage->set('partsmap', $partsMap);
}

// *******************************************************************************************
// START IMPORT SESSION HERE
// *******************************************************************************************
$start = $storage->get('start',0);
$foffset = $storage->get('foffset', 0);

// Open the file
if (isset ($activeDatabase)) {
	if(!openFile()) return;
}

// Connect to the database server
$db =& ABIDatabase::getInstance($dbtype, $dbhost, $dbuser, $dbpass, $dbname, $prefix);

if(!$db->connect())
{
	throwError( ABIText::_('ERROR_DBCONFAIL') );
	if( is_object($db) ) $db->disconnect();
	return;
}

// Enforce skipping foreign key checks, if specified
if($suppressfk)
{
	$db->query('SET FOREIGN_KEY_CHECKS = 0');
}

// Start processing queries from $file
$query = "";
$queries = 0;
$totalqueries = $storage->get('totalqueries',0);
$linenumber = $start;
$totalsizeread = 0;

// Stay processing as long as we have time or the query is still incomplete
while ( getTimeRemaining() > 0 ) {
	// Read one line (1 line = 1 query)
	$query = "";
	while (!feof($file) && (strpos($query, "\n") === false) ) {
		$query .= fgets($file, DATA_CHUNK_LENGTH);
	}

	// An empty query is EOF. Are we done or should I skip to the next file?
	if(empty($query) || ($query === false))
	{
		if($curpart >= ($parts - 1)) {
			break;
		} else {
			// Register the bytes read
			$current_foffset = @ftell($file);
			$runSize += $current_foffset - $foffset;
			// Get the next file
			if(!getNextFile()) return;
			// Rerun the fetcher
			continue;
		}
	}

	if(substr($query,-1) != "\n")
	{
		// WTF? We read more data than we should?! Roll back the file
		$rollback = strlen($query) - strpos($query, "\n");
		fseek($file, -$rollback, SEEK_CUR);
		// And chop the line
		$query = substr($query, 0, $rollback);
	}

	// Handle DOS linebreaks
	$query = str_replace("\r\n", "\n", $query);
	$query = str_replace("\r", "\n", $query);

	// Skip comments and blank lines only if NOT in parents
	$skipline = false;
	reset($comment);
	foreach ($comment as $comment_value) {
		if (trim($query) == "" || strpos($query, $comment_value) === 0) {
			$skipline = true;
			break;
		}
	}
	if ($skipline) {
		$linenumber++;
		continue;
	}

	$query = trim($query, " \n");
	$query = rtrim($query, ';');

	// CREATE TABLE query pre-processing
	// If the table has a prefix, back it up (if requested). In any case, drop
	// the table. before attempting to create it.
	$replaceAll = false;
	$changeEncoding = false;
	if( substr($query, 0, 12) == 'CREATE TABLE')
	{
		// Yes, try to get the table name
		$restOfQuery = trim(substr($query, 12, strlen($query)-12 )); // Rest of query, after CREATE TABLE
		// Is there a backtick?
		if(substr($restOfQuery,0,1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos = strpos($restOfQuery, '`', 1);
			$tableName = substr($restOfQuery,1,$pos - 1);
		}
		else
		{
			// Nope, let's assume the table name ends in the next blank character
			$pos = strpos($restOfQuery, ' ', 1);
			$tableName = substr($restOfQuery,1,$pos - 1);
		}
		unset($restOfQuery);

		// Should I back the table up?
		if( ($prefix != '') && ($existing == 'backup') && (strpos($tableName, '#__') == 0))
		{
			// It's a table with a prefix, a prefix IS specified and we are asked to back it up.
			// Start by dropping any existing backup tables
			$backupTable = str_replace('#__', 'bak_', $tableName);
			$dropQuery = 'DROP TABLE IF EXISTS `'.$backupTable.'`;';
			$db->setQuery(trim($dropQuery), false);
			if (!$db->query()) {
				// Query failure
				$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". trim($dumpline) . "</p>\n";
				$message .= "<p>".ABIText::_('ERROR_LBLQUERY') .  trim(nl2br(htmlentities($dropQuery))) ."</p>\n";
				$message .="<p>MySQL: " . $db->getError() . "</p>\n";
				throwError($message);
				if( is_object($db) ) $db->disconnect();
				return;
				break;
			}

			// Then, rename the old table
			$renameQuery = 'ALTER TABLE `'.str_replace('#__', $prefix, $tableName).'` RENAME `'.$backupTable.'`;';
			$db->setQuery(trim($renameQuery), false);
			$db->query();
		}
		// Try to drop the table anyway
		$dropQuery = 'DROP TABLE IF EXISTS `'.$tableName.'`;';
		$db->setQuery(trim($dropQuery), false);
		if (!$db->query()) {
			// Query failure
			$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". trim($dumpline) . "</p>\n";
			$message .= "<p>".ABIText::_('ERROR_LBLQUERY') .  trim(nl2br(htmlentities($dropQuery))) ."</p>\n";
			$message .="<p>MySQL: " . $db->getError() . "</p>\n";
			throwError($message);
			if( is_object($db) ) $db->disconnect();
			return;
			break;
		}

		$replaceAll = true; // When processing CREATE TABLE commands, we might have to replace SEVERAL metaprefixes.

		// Crude check: Community builder's #__comprofiler_fields includes a DEFAULT value which use a metaprefix,
		// so replaceAll must be false in that case.
		if($tableName == '#__comprofiler_fields') {
			$replaceAll = false;
		}

		$changeEncoding = $forceutf8;
	} else
	// CREATE VIEW query pre-processing
	// In any case, drop the view before attempting to create it. (Views can't be renamed)
	if( (substr($query, 0, 7) == 'CREATE ') && (strpos($query, ' VIEW ') !== false) )
	{
		// Yes, try to get the view name
		$view_pos = strpos($query, ' VIEW ');
		$restOfQuery = trim( substr($query, $view_pos + 6) ); // Rest of query, after VIEW string
		// Is there a backtick?
		if(substr($restOfQuery,0,1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos = strpos($restOfQuery, '`', 1);
			$tableName = substr($restOfQuery,1,$pos - 1);
		}
		else
		{
			// Nope, let's assume the table name ends in the next blank character
			$pos = strpos($restOfQuery, ' ', 1);
			$tableName = substr($restOfQuery,1,$pos - 1);
		}
		unset($restOfQuery);

		// Try to drop the view anyway
		$dropQuery = 'DROP VIEW IF EXISTS `'.$tableName.'`;';
		$db->setQuery(trim($dropQuery), false);
		if (!$db->query()) {
			// Query failure
			$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". trim($dumpline) . "</p>\n";
			$message .= "<p>".ABIText::_('ERROR_LBLQUERY') .  trim(nl2br(htmlentities($dropQuery))) ."</p>\n";
			$message .="<p>MySQL: " . $db->getError() . "</p>\n";
			throwError($message);
			if( is_object($db) ) $db->disconnect();
			return;
			break;
		}

		$replaceAll = true; // When processing views, we might have to replace SEVERAL metaprefixes.
	}
	// CREATE PROCEDURE pre-processing
	elseif( (substr($query, 0, 7) == 'CREATE ') && (strpos($query, 'PROCEDURE ') !== false) )
	{
		// Try to get the procedure name
		$entity_keyword = ' PROCEDURE ';
		$entity_pos = strpos($query, $entity_keyword);
		$restOfQuery = trim( substr($query, $entity_pos + strlen($entity_keyword)) ); // Rest of query, after entity key string
		// Is there a backtick?
		if(substr($restOfQuery,0,1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos = strpos($restOfQuery, '`', 1);
			$entity_name = substr($restOfQuery,1,$pos - 1);
		}
		else
		{
			// Nope, let's assume the entity name ends in the next blank character
			$pos = strpos($restOfQuery, ' ', 1);
			$entity_name = substr($restOfQuery,1,$pos - 1);
		}
		unset($restOfQuery);

		// Try to drop the entity anyway
		$dropQuery = 'DROP'.$entity_keyword.'IF EXISTS `'.$entity_name.'`;';
		$db->setQuery(trim($dropQuery), false);
		if (!$db->query()) {
			// Query failure
			$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". trim($dumpline) . "</p>\n";
			$message .= "<p>".ABIText::_('ERROR_LBLQUERY') .  trim(nl2br(htmlentities($dropQuery))) ."</p>\n";
			$message .="<p>MySQL: " . $db->getError() . "</p>\n";
			throwError($message);
			if( is_object($db) ) $db->disconnect();
			return;
			break;
		}

		$replaceAll = true; // When processing entities, we might have to replace SEVERAL metaprefixes.
	}
	// CREATE FUNCTION pre-processing
	elseif( (substr($query, 0, 7) == 'CREATE ') && (strpos($query, 'FUNCTION ') !== false) )
	{
		// Try to get the procedure name
		$entity_keyword = ' FUNCTION ';
		$entity_pos = strpos($query, $entity_keyword);
		$restOfQuery = trim( substr($query, $entity_pos + strlen($entity_keyword)) ); // Rest of query, after entity key string
		// Is there a backtick?
		if(substr($restOfQuery,0,1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos = strpos($restOfQuery, '`', 1);
			$entity_name = substr($restOfQuery,1,$pos - 1);
		}
		else
		{
			// Nope, let's assume the entity name ends in the next blank character
			$pos = strpos($restOfQuery, ' ', 1);
			$entity_name = substr($restOfQuery,1,$pos - 1);
		}
		unset($restOfQuery);

		// Try to drop the entity anyway
		$dropQuery = 'DROP'.$entity_keyword.'IF EXISTS `'.$entity_name.'`;';
		$db->setQuery(trim($dropQuery), false);
		if (!$db->query()) {
			// Query failure
			$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". trim($dumpline) . "</p>\n";
			$message .= "<p>".ABIText::_('ERROR_LBLQUERY') .  trim(nl2br(htmlentities($dropQuery))) ."</p>\n";
			$message .="<p>MySQL: " . $db->getError() . "</p>\n";
			throwError($message);
			if( is_object($db) ) $db->disconnect();
			return;
			break;
		}

		$replaceAll = true; // When processing entities, we might have to replace SEVERAL metaprefixes.
	}
	// CREATE TRIGGER pre-processing
	elseif( (substr($query, 0, 7) == 'CREATE ') && (strpos($query, 'TRIGGER ') !== false) )
	{
		// Try to get the procedure name
		$entity_keyword = ' TRIGGER ';
		$entity_pos = strpos($query, $entity_keyword);
		$restOfQuery = trim( substr($query, $entity_pos + strlen($entity_keyword)) ); // Rest of query, after entity key string
		// Is there a backtick?
		if(substr($restOfQuery,0,1) == '`')
		{
			// There is... Good, we'll just find the matching backtick
			$pos = strpos($restOfQuery, '`', 1);
			$entity_name = substr($restOfQuery,1,$pos - 1);
		}
		else
		{
			// Nope, let's assume the entity name ends in the next blank character
			$pos = strpos($restOfQuery, ' ', 1);
			$entity_name = substr($restOfQuery,1,$pos - 1);
		}
		unset($restOfQuery);

		// Try to drop the entity anyway
		$dropQuery = 'DROP'.$entity_keyword.'IF EXISTS `'.$entity_name.'`;';
		$db->setQuery(trim($dropQuery), false);
		if (!$db->query()) {
			// Query failure
			$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". trim($dumpline) . "</p>\n";
			$message .= "<p>".ABIText::_('ERROR_LBLQUERY') .  trim(nl2br(htmlentities($dropQuery))) ."</p>\n";
			$message .="<p>MySQL: " . $db->getError() . "</p>\n";
			throwError($message);
			if( is_object($db) ) $db->disconnect();
			return;
			break;
		}

		$replaceAll = true; // When processing entities, we might have to replace SEVERAL metaprefixes.
	}
	elseif( substr($query,0,6) == 'INSERT' )
	{
		if($replacesql)
		{
			// Use REPLACE instead of INSERT selected
			$query = 'REPLACE '.substr($query,7);
		}
		$replaceAll = false;
	}
	else
	{
		// Maybe a DROP statement from the extensions filter?
		$replaceAll = true;
	}

	if(!empty($query)) {
		$db->setQuery(trim($query), !$replaceAll);
		if (!$db->query()) {
			// Skip over errors we can safely ignore...
			if( in_array($db->errno, $skipMySQLErrorNumbers) ) continue;

			// Query failure
			$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". substr(trim($query),0,200).' ...' . "</p>\n";
			//$message = "<p>".ABIText::_('ERROR_DBERRATLINE')." $linenumber: ". trim($query) . "</p>\n";
			//$message .= "<p>".ABIText::_('ERROR_LBLQUERY') .  trim(nl2br(htmlentities($query))) ."</p>\n";
			$message .="<p>MySQL: " . $db->getError() . "</p>\n";

			throwError($message);
			if( is_object($db) ) $db->disconnect();
			return;
			break;
		}

		// Do we have to force UTF8 encoding?
		if($changeEncoding) {
			// Get a list of columns
			$sql = 'SHOW FULL COLUMNS FROM `'.$tableName.'`';
			$columns = $db->getAssocArray($sql);
			$mods = array(); // array to hold individual MODIFY COLUMN commands
			if(is_array($columns)) foreach($columns as $column)
			{
				// Make sure we are redefining only columns which do support a collation
				$col = (object)$column;
				if( empty($col->Collation) ) continue;

				$null = $col->Null == 'YES' ? 'NULL' : 'NOT NULL';
				$default = is_null($col->Default) ? '' : "DEFAULT '".$db->escape($col->Default)."'";
				$mods[] = "MODIFY COLUMN `{$col->Field}` {$col->Type} $null $default COLLATE utf8_general_ci";
			}

			// Begin the modification statement
			$sql = "ALTER TABLE `$tableName` ";

			// Add commands to modify columns
			if(!empty($mods))
			{
				$sql .= implode(', ', $mods).', ';
			}

			// Add commands to modify the table collation
			$sql .= 'DEFAULT CHARACTER SET UTF8 COLLATE utf8_general_ci;';
			$db->setQuery($sql);
			$db->query();
			$db->reset();
		}
	}

	$totalsizeread += strlen($query);
	$totalqueries++;
	$queries++;
	$query = "";
	$linenumber++;
}

// Disconnect the database
if( is_object($db) ) $db->disconnect();

// Get the current file position
$current_foffset = ftell($file);
if ($current_foffset === false) {
	if ($file) fclose($file);
	throwError(ABIText::_('ERROR_CANTREADPOINTER'));
	return;
}
else
{
	$runSize += $current_foffset - $foffset;
	$foffset = $current_foffset;
}

// Return statistics
$pct_done = ceil($runSize / $totalSize * 100);
$bytes_done = $runSize;
$bytes_tota = $totalSize;
$bytes_togo = $totalSize - $runSize;
$kbytes_done = round($bytes_done / 1024, 2);
$kbytes_tota = round($bytes_tota / 1024, 2);

// Check for global EOF
if( ($curpart >= ($parts-1)) && feof($file) ) $bytes_togo = 0;

if ($bytes_togo == 0) {
	// Clear stored variables if we're finished
	$lines_togo = '0';
	$lines_tota = $linenumber -1;
	$queries_togo = '0';
	$queries_tota = $totalqueries;
	removeInformationFromStorage();
}
else
{
	// Save variables in storage!
	$storage->set('start', $start);
	$storage->set('foffset', $foffset);
	$storage->set('totalqueries', $totalqueries);
	$storage->set('runsize', $runSize);
}

// Close the file
if ($file) fclose($file);

// Return meaningful data to AJAX
$ret = array(
	'percent'			=> $pct_done,
	'message'			=> sprintf(ABIText::_('LBL_PROGRESS'), $kbytes_done, $kbytes_tota, $pct_done),
	'error'				=> '',
	'done'				=> ($bytes_togo == 0) ? '1' : '0'
);
echo renderXML($ret);