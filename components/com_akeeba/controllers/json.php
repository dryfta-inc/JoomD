<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 2, or later
 * @version $Id: json.php 615 2011-05-19 13:01:52Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

defined('AKEEBA_BACKUP_ORIGIN') or define('AKEEBA_BACKUP_ORIGIN','json');

// Load framework base classes
jimport('joomla.application.component.controller');

class AkeebaControllerJson extends JController
{
	/**
	 * Starts a backup
	 * @return
	 */
	public function display()
	{
		// Many versions of PHP suffer from a brain-dead buggy JSON library. Let's
		// load our own (actually it's PEAR's Services_JSON).
		require_once JPATH_SITE.'/administrator/components/com_akeeba/helpers/jsonlib.php';

		// Use the model to parse the JSON message
		if(function_exists('ob_start')) @ob_start();
		$sourceJSON = JRequest::getVar('json', null, 'default', 'raw', 2);
		// On some !@#$%^& servers where magic_quotes_gpc is On we might get extra slashes added
		if(function_exists('get_magic_quotes_gpc')) {
			if(get_magic_quotes_gpc()) {
				$sourceJSON = stripslashes($sourceJSON);
			}
		}
		
		$model = JModel::getInstance('Json','AkeebaModel');
		$json = $model->execute($sourceJSON);
		if(function_exists('ob_clean')) @ob_clean();
		
		// Just dump the JSON and tear down the application, without plugins executing
		echo $json;	
		$app = JFactory::getApplication();
		$app->close();
	}

}

