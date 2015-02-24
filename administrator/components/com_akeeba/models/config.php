<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: config.php 632 2011-05-22 20:44:46Z nikosdion $
 * @since 3.2.5
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.model');

class AkeebaModelConfig extends JModel
{
	public function testFTP()
	{
		$config = array(
			'host' => $this->getState('host'),
			'port' => $this->getState('port'),
			'user' => $this->getState('user'),
			'pass' => $this->getState('pass'),
			'initdir' => $this->getState('initdir'),
			'usessl' => $this->getState('usessl'),
			'passive' => $this->getState('passive'),
		);

		// Perform the FTP connection test
		$test = new AEArchiverDirectftp();
		$test->initialize('', $config);
		$errors = $test->getError();
		if(empty($errors) || $test->connect_ok)
		{
			$result = true;
		}
		else
		{
			$result = $errors;
		}
		return $result;
	}
	
	public function testSFTP()
	{
		$config = array(
			'host' => $this->getState('host'),
			'port' => $this->getState('port'),
			'user' => $this->getState('user'),
			'pass' => $this->getState('pass'),
			'initdir' => $this->getState('initdir'),
		);

		// Perform the FTP connection test
		$test = new AEArchiverDirectsftp();
		$test->initialize('', $config);
		$errors = $test->getError();
		if(empty($errors) || $test->connect_ok)
		{
			$result = true;
		}
		else
		{
			$result = $errors;
		}
		return $result;
	}
}