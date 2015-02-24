<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer Utilities
 */

defined('_ABI') or die('Direct access is not allowed');

// Work around magic_quotes_gpc
if (@get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

// Work around magic_quotes_runtime
if( function_exists('set_magic_quotes_runtime') )
{
	@set_magic_quotes_runtime(false);
}

/**
 * Returns a request parameter
 * @param $name string The name of the parameter
 * @param $def mixed The default value (otherwise it's null)
 * @return mixed The value of the request parameter
 */
function getParam( $name, $def=null, $nostrip = false ) {
	$return = null;
	if (isset( $_REQUEST[$name] )) {
		$value = $_REQUEST[$name];
		if (is_string( $value )) {
			$value = ($_REQUEST[$name]);
			// Unescape request parameters
			$value = trim( $value );
			if(!$nostrip) $value = strip_tags( $value );
		}
		return $value;
	} else {
		return $def;
	}
}

/**
 * Renders an AJAX return XML document from a hash array
 * @param $data array The hash array containing the key/value pairs to return
 * @return string The XML data you should return to the browser
 */
function renderXML(&$data)
{
	$out = '###<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n".'<restoredata>'."\n";
	if(is_array($data))
	{
		if(count($data) > 0)
		{
			foreach($data as $key => $value)
			{
				$out .= "\t<$key>$value</$key>\n";
			}
		}
	}
	$out .= '</restoredata>###';
	return $out;
}

/**
 * A PHP based INI file parser.
 *
 * Thanks to asohn ~at~ aircanopy ~dot~ net for posting this handy function on
 * the parse_ini_file page on http://gr.php.net/parse_ini_file
 *
 * @param string $file Filename to process
 * @param bool $process_sections True to also process INI sections
 * @param bool $rawdata If true, the $file contains raw INI data, not a filename
 * @return array An associative array of sections, keys and values
 * @access private
 */
function _parse_ini_file($file, $process_sections = false, $rawdata = false)
{
	$process_sections = ($process_sections !== true) ? false : true;

	if(!$rawdata)
	{
		$ini = @file($file);
	}
	else
	{
		$file = str_replace("\r","",$file);
		$ini = explode("\n", $file);
	}

	if (count($ini) == 0) {return array();}
	if(empty($ini)) return array();

	$sections = array();
	$values = array();
	$result = array();
	$globals = array();
	$i = 0;
	foreach ($ini as $line) {
		$line = trim($line);
		$line = str_replace("\t", " ", $line);

		// Comments
		if (!preg_match('/^[a-zA-Z0-9[]/', $line)) {continue;}

		// Sections
		if ($line{0} == '[') {
			$tmp = explode(']', $line);
			$sections[] = trim(substr($tmp[0], 1));
			$i++;
			continue;
		}

		// Key-value pair
		list($key, $value) = explode('=', $line, 2);
		$key = trim($key);
		$value = trim($value);
		if (strstr($value, ";")) {
			$tmp = explode(';', $value);
			if (count($tmp) == 2) {
				if ((($value{0} != '"') && ($value{0} != "'")) ||
				preg_match('/^".*"\s*;/', $value) || preg_match('/^".*;[^"]*$/', $value) ||
				preg_match("/^'.*'\s*;/", $value) || preg_match("/^'.*;[^']*$/", $value) ){
					$value = $tmp[0];
				}
			} else {
				if ($value{0} == '"') {
					$value = preg_replace('/^"(.*)".*/', '$1', $value);
				} elseif ($value{0} == "'") {
					$value = preg_replace("/^'(.*)'.*/", '$1', $value);
				} else {
					$value = $tmp[0];
				}
			}
		}
		$value = trim($value);
		$value = trim($value, "'\"");

		if ($i == 0) {
			if (substr($line, -1, 2) == '[]') {
				$globals[$key][] = $value;
			} else {
				$globals[$key] = $value;
			}
		} else {
			if (substr($line, -1, 2) == '[]') {
				$values[$i-1][$key][] = $value;
			} else {
				$values[$i-1][$key] = $value;
			}
		}
	}

	for($j = 0; $j < $i; $j++) {
		if ($process_sections === true) {
			$result[$sections[$j]] = $values[$j];
		} else {
			$result[] = $values[$j];
		}
	}

	return $result + $globals;
}