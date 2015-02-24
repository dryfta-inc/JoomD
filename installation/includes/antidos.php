<?php
/**
* @package akeebainstaller
 * @subpackage Installer
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer automation
 */

defined('_ABI') or die('Direct access is not allowed');

/**
 * Enforces the minimum execution time per step, if such a thing is set up
 * @param bool $starting True when starting timing the script, false otherwise
 */
function enforce_minexectime($starting)
{
	static $start_time, $end_time;

	if($starting)
	{
		list($usec, $sec) = explode(" ", microtime());
		$start_time = ((float)$usec + (float)$sec);
	}
	else
	{
		// Try to get a sane value for PHP's maximum_execution_time INI parameter
		if(@function_exists('ini_get'))
		{
			$php_max_exec = @ini_get("maximum_execution_time");
		}
		else
		{
			$php_max_exec = 10;
		}
		if ( ($php_max_exec == "") || ($php_max_exec == 0) ) {
			$php_max_exec = 10;
		}
		// Decrease $php_max_exec time by 500 msec we need (approx.) to tear down
		// the application, as well as another 500msec added for rounding
		// error purposes. Also make sure this is never gonna be less than 0.
		$php_max_exec = max($php_max_exec * 1000 - 1000, 0);

		// Get the "minimum execution time per step" configuration constant
		$minexectime = MINEXECTIME;
		if(!is_numeric($minexectime)) $minexectime = 0;

		// Make sure we are not over PHP's time limit!
		if($minexectime > $php_max_exec) $minexectime = $php_max_exec;

		// Get current timestamp and calculate how much time has passed
		list($usec, $sec) = explode(" ", microtime());
		$end_time = ((float)$usec + (float)$sec);
		$elapsed_time = 1000 * ($end_time - $start_time);

		// Only run a sleep delay if we haven't reached the minexectime execution time
		if( ($minexectime > $elapsed_time) && ($elapsed_time > 0) )
		{
			$sleep_msec = $minexectime - $elapsed_time;
			if(function_exists('usleep'))
			{
				usleep(1000 * $sleep_msec);
			}
			elseif(function_exists('time_nanosleep'))
			{
				$sleep_sec = round($sleep_msec / 1000);
				$sleep_nsec = 1000000 * ($sleep_msec - ($sleep_sec * 1000));
				time_nanosleep($sleep_sec, $sleep_nsec);
			}
			elseif(function_exists('time_sleep_until'))
			{
				$until_timestamp = time() + $sleep_msec / 1000;
				time_sleep_until($until_timestamp);
			}
			elseif(function_exists('sleep'))
			{
				$sleep_sec = ceil($sleep_msec/1000);
				sleep( $sleep_sec );
			}
		}
		elseif( $elapsed_time > 0 )
		{
			// No sleep required, even if user configured us to be able to do so.
		}
	}
}