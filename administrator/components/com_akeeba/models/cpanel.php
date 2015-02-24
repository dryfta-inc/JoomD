<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.model');

/**
 * The Control Panel model
 *
 */
class AkeebaModelCpanel extends JModel
{
	/**
	 * Contructor; dummy for now
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get an array of icon definitions for the Control Panel
	 *
	 * @return array
	 */
	public function getIconDefinitions()
	{
		AEPlatform::getInstance()->load_version_defines();
		$core	= $this->loadIconDefinitions(JPATH_COMPONENT_ADMINISTRATOR.'/views');
		if(AKEEBA_PRO) {
			$pro	= $this->loadIconDefinitions(JPATH_COMPONENT_ADMINISTRATOR.'/plugins/views');
		} else {
			$pro = array();
		}
		$ret = array_merge_recursive($core, $pro);

		return $ret;
	}

	private function loadIconDefinitions($path)
	{
		$ret = array();

		if(!@file_exists($path.'/views.ini')) return $ret;

		$ini_data = AEUtilINI::parse_ini_file($path.'/views.ini', true);
		if(!empty($ini_data))
		{
			foreach($ini_data as $view => $def)
			{
				$task = array_key_exists('task',$def) ? $def['task'] : null;
				$ret[$def['group']][] = $this->_makeIconDefinition($def['icon'], JText::_($def['label']), $view, $task);
			}
		}

		return $ret;
	}

	/**
	 * Returns a list of available backup profiles, to be consumed by JHTML in order to build
	 * a drop-down
	 *
	 * @return array
	 */
	public function getProfilesList()
	{
		$db =& $this->getDBO();
		$query = "SELECT ".$db->nameQuote('id').", ".$db->nameQuote('description').
				" FROM ".$db->nameQuote('#__ak_profiles').
				" ORDER BY ".$db->nameQuote('id')." ASC";
		$db->setQuery($query);
		$rawList = $db->loadAssocList();

		$options = array();
		if(!is_array($rawList)) return $options;

		foreach($rawList as $row)
		{
			$options[] = JHTML::_('select.option', $row['id'], $row['description']);
		}

		return $options;
	}

	/**
	 * Returns the active Profile ID
	 *
	 * @return int The active profile ID
	 */
	public function getProfileID()
	{
		$session =& JFactory::getSession();
		return $session->get('profile', null, 'akeeba');
	}

	/**
	 * Creates an icon definition entry
	 *
	 * @param string $iconFile The filename of the icon on the GUI button
	 * @param string $label The label below the GUI button
	 * @param string $view The view to fire up when the button is clicked
	 * @return array The icon definition array
	 */
	public function _makeIconDefinition($iconFile, $label, $view = null, $task = null )
	{
		return array(
			'icon'	=> $iconFile,
			'label'	=> $label,
			'view'	=> $view,
			'task'	=> $task
		);
	}

	/**
	 * Was the last backup a failed one? Used to apply magic settings as a means of
	 * troubleshooting.
	 *
	 * @return bool
	 */
	public function isLastBackupFailed()
	{
		// Get the last backup record ID
		$list = AEPlatform::getInstance()->get_statistics_list(array('limitstart' => 0, 'limit' => 1));
		if(empty($list)) return false;
		$id = $list[0];

		$statmodel->setId($id);
		$record = AEPlatform::getInstance()->get_statistics($id);

		return ($record['status'] == 'fail');
	}

	/**
	 * Checks that the media permissions are 0755 for directories and 0644 for files
	 * and fixes them if they are incorrect.
	 *
	 * @param $force	bool	Forcibly check subresources, even if the parent has correct permissions
	 *
	 * @return bool False if we couldn't figure out what's going on
	 */
	public function fixMediaPermissions($force = false)
	{
		// Are we on Windows?
		if (function_exists('php_uname'))
		{
			$isWindows = stristr(php_uname(), 'windows');
		}
		else
		{
			$isWindows = (DIRECTORY_SEPARATOR == '\\');
		}

		// No point changing permissions on Windows, as they have ACLs
		if($isWindows) return true;

		// Check the parent permissions
		$parent = JPATH_ROOT.'/media/com_akeeba';
		$parentPerms = fileperms($parent);

		// If we can't determine the parent's permissions, bail out
		if($parentPerms === false) return false;

		// Fix the parent's permissions if required
		if($parentPerms != 0755) {
			$this->chmod($parent, 0755);
		} else {
			if(!$force) return true;
		}

		// During development we use symlinks and we don't wanna see that big fat warning
		if(@is_link($parent)) return true;

		jimport('joomla.filesystem.folder');

		$result = true;

		// Loop through subdirectories
		$folders = JFolder::folders($parent,'.',3,true);
		foreach($folders as $folder) {
			$perms = fileperms($folder);
			if($perms != 0755) $result &= $this->chmod($folder, 0755);
		}

		// Loop through files
		$files = JFolder::files($parent,'.',3,true);
		foreach($files as $file) {
			$perms = fileperms($file);
			if($perms != 0644) $result &= $this->chmod($file, 0644);
		}

		return $result;
	}

	/**
	 * Tries to change a folder/file's permissions using direct access or FTP
	 *
	 * @param string	$path	The full path to the folder/file to chmod
	 * @param int		$mode	New permissions
	 */
	private function chmod($path, $mode)
	{
		if(is_string($mode))
		{
			$mode = octdec($mode);
			if( ($mode < 0600) || ($mode > 0777) ) $mode = 0755;
		}

		// Initialize variables
		jimport('joomla.client.helper');
		$ftpOptions = JClientHelper::getCredentials('ftp');

		// Check to make sure the path valid and clean
		$path = JPath::clean($path);

		if ($ftpOptions['enabled'] == 1) {
			// Connect the FTP client
			jimport('joomla.client.ftp');
			$ftp = &JFTP::getInstance(
				$ftpOptions['host'], $ftpOptions['port'], null,
				$ftpOptions['user'], $ftpOptions['pass']
			);
		}

		if(@chmod($path, $mode))
		{
			$ret = true;
		} elseif ($ftpOptions['enabled'] == 1) {
			// Translate path and delete
			$path = JPath::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $path), '/');
			// FTP connector throws an error
			$ret = $ftp->chmod($path, $mode);
		} else {
			return false;
		}
	}

	/**
	 * Checks if we should enable settings encryption and applies the change
	 */
	public function checkSettingsEncryption()
	{
		// Do we have a key file?
		jimport('joomla.filesystem.file');
		$filename = JPATH_COMPONENT_ADMINISTRATOR.'/akeeba/serverkey.php';
		if(JFile::exists($filename)) {			
			// We have a key file. Do we need to disable it?
			if(AEPlatform::getInstance()->get_platform_configuration_option('useencryption', -1) == 0) {
				// User asked us to disable encryption. Let's do it.
				$this->disableSettingsEncryption();
			}
		} else {
			if(!AEUtilSecuresettings::supportsEncryption()) return;
			if(AEPlatform::getInstance()->get_platform_configuration_option('useencryption', -1) != 0) {
				// User asked us to enable encryption (or he left us with the default setting!). Let's do it.
				$this->enableSettingsEncryption();
			}
		}
	}
	
	private function disableSettingsEncryption()
	{
		// Load the server key file if necessary
		jimport('joomla.filesystem.file');
		$filename = JPATH_COMPONENT_ADMINISTRATOR.'/akeeba/serverkey.php';
		$key = AEUtilSecuresettings::getKey();
		
		// Loop all profiles and decrypt their settings
		$profilesModel = JModel::getInstance('Profiles','AkeebaModel');
		$profiles = $profilesModel->getProfilesList(true);
		$db = $this->getDBO();
		foreach($profiles as $profile)
		{
			$id = $profile->id;
			$config = AEUtilSecuresettings::decryptSettings($profile->configuration, $key);
			$sql = 'UPDATE '.$db->nameQuote('#__ak_profiles').' SET '.
				$db->nameQuote('configuration').' = '.$db->Quote($config)
				.' WHERE '.
				$db->nameQuote('id').' = '.	$db->Quote($id);
			$db->setQuery($sql);
			$db->query();
		}
		
		// Finally, remove the key file
		JFile::delete($filename);
	}
	
	private function enableSettingsEncryption()
	{
		$key = $this->createSettingsKey();
		if(empty($key) || ($key==false)) return;
		
		// Loop all profiles and encrypt their settings
		$profilesModel = JModel::getInstance('Profiles','AkeebaModel');
		$profiles = $profilesModel->getProfilesList(true);
		$db = $this->getDBO();
		foreach($profiles as $profile)
		{
			$id = $profile->id;
			$config = AEUtilSecuresettings::encryptSettings($profile->configuration, $key);
			$sql = 'UPDATE '.$db->nameQuote('#__ak_profiles').' SET '.
				$db->nameQuote('configuration').' = '.$db->Quote($config)
				.' WHERE '.
				$db->nameQuote('id').' = '.	$db->Quote($id);
			$db->setQuery($sql);
			$db->query();
		}
	}
	
	private function createSettingsKey()
	{
		jimport('joomla.filesystem.file');
		$seedA = md5( JFile::read(JPATH_ROOT.'/configuration.php') );
		$seedB = md5( microtime() );
		$seed = $seedA.$seedB;
		
		$md5 = md5($seed);
		for($i = 0; $i < 1000; $i++) {
			$md5 = md5( $md5 . md5(rand(0, 2147483647)) );
		}
		
		$key = base64_encode( $md5 );
		
		$filecontents = "<?php defined('AKEEBAENGINE') or die(); define('AKEEBA_SERVERKEY', '$key'); ?>";
		$filename = JPATH_COMPONENT_ADMINISTRATOR.'/akeeba/serverkey.php';

		$result = JFile::write($filename, $filecontents);
		
		if(!$result) {
			return false;
		} else {
			return base64_decode($key);
		}
	}
	
	/**
	 * Update the cached live site's URL for the front-end backup feature (altbackup.php)
	 * and the detected Joomla! libraries path
	 */
	public function updateMagicParameters()
	{
		$component =& JComponentHelper::getComponent( 'com_akeeba' );
		if(is_object($component->params) && ($component->params instanceof JRegistry)) {
			$params = $component->params;
		} else {
			$params = new JParameter($component->params);
		}
		$params->set( 'siteurl', str_replace('/administrator','',JURI::base()) );
		if(defined('JPATH_LIBRARIES')) {
			$params->set('jlibrariesdir', AEUtilFilesystem::TranslateWinPath(JPATH_LIBRARIES));
		} elseif(defined("JPATH_PLATFORM")) {
			$params->set('jlibrariesdir', AEUtilFilesystem::TranslateWinPath(JPATH_PLATFORM));
		}
		if(AKEEBA_JVERSION == '16') {
			// Joomla! 1.6
			$joomla16 = true;
			$params->set( 'jversion', '1.6' );
		} else {
			// Joomla! 1.5
			$joomla16 = false;
			$params->set( 'jversion', '1.5' );
		}
		$db =& JFactory::getDBO();
		$data = $params->toString();
		if($joomla16)
		{
			// Joomla! 1.6
			$sql = 'UPDATE `#__extensions` SET `params` = '.$db->Quote($data).' WHERE '.
				"`element` = 'com_akeeba' AND `type` = 'component'";
		}
		else
		{
			// Joomla! 1.5
			$sql = 'UPDATE `#__components` SET `params` = '.$db->Quote($data).' WHERE '.
				"`option` = 'com_akeeba' AND `parent` = 0 AND `menuid` = 0";
		}
		$db->setQuery($sql);
		$db->query();
	}
	
	/**
	 * Makes sure that the Professional release can be updated using Joomla!'s
	 * own update system. THIS IS AN AKEEBA ORIGINAL!
	 */
	public function applyJoomlaExtensionUpdateChanges()
	{
		if(!AKEEBA_PRO) return;
		$update_url = 'https://www.akeebabackup.com/index.php?option=com_ars&view=update&task=stream&format=xml&id=2';
		$option = 'com_akeeba';
		if(!class_exists('LiveUpdateConfig')) return;
		
		$luc = new LiveUpdateConfig();
		$dlid = $luc->getAuthorization();
		
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select(array(
				$db->nq('us').'.*',
			))->from($db->nq('#__extensions').' AS '.$db->nq('ex'))
			->innerJoin(
				$db->nq('#__update_sites_extensions').' AS '.$db->nq('map').' ON ('.
				$db->nq('map').'.'.$db->nq('extension_id').' = '.
					$db->nq('ex').'.'.$db->nq('extension_id').')'
			)
			->innerJoin(
				$db->nq('#__update_sites').' AS '.$db->nq('us').' ON ('.
				$db->nq('us').'.'.$db->nq('update_site_id').' = '.
					$db->nq('map').'.'.$db->nq('update_site_id').')'
			)
			->where(
				$db->nq('ex').'.'.$db->nq('element').' = '.$db->q($option)
			)
		;
		$db->setQuery($query);
		$update_site = $db->loadObject();
		
		if($dlid) $dlid = '&'.$dlid;
		$proper_url = $update_url.$dlid.'/extension.xml';
		if($update_site->location != $proper_url) {
			if(in_array('last_check_timestamp', get_object_vars($update_site))) {
				$update_site->last_check_timestamp = 0;
			}
			$update_site->location = $proper_url;
			$db->updateObject('#__update_sites', $update_site, 'update_site_id');
		}
	}
}