<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: scan.php 409 2011-01-24 09:30:22Z nikosdion $
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

abstract class AEAbstractScan extends AEAbstractObject
{
	/**
	 * Gets all the files of a given folder
	 * @param	string	$folder	The absolute path to the folder to scan for files
	 * @return	array	A simple array of files
	 */
	abstract public function &getFiles($folder);

	/**
	 * Gets all the folders (subdirectories) of a given folder
	 * @param	string	$folder	The absolute path to the folder to scan for files
	 * @return	array	A simple array of folders
	 */
	abstract public function &getFolders($folder);
}