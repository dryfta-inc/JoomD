<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: view.raw.php 409 2011-01-24 09:30:22Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class AkeebaViewJson extends JView
{
	public function display($tpl = null)
	{
		if(function_exists('ob_start')) @ob_start();

		$sourceJSON = JRequest::getVar('json', null, 'default', 'raw', 2);
		$model = JModel::getInstance('Json','AkeebaModel');
		
		$json = $model->execute($sourceJSON);

		$this->assign('json',	$json);

		// # Fix 2.4: Drop the output buffer
		if(function_exists('ob_clean')) @ob_clean();
		parent::display('raw');
	}
}
?>