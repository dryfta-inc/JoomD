<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: acl.php 632 2011-05-22 20:44:46Z nikosdion $
 * @since 3.2.1
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.model');

/**
 * The tiny ACL system model
 */
class AkeebaModelAcl extends JModel
{
	/**
	 * Public function to authorize a user's access to a specific Akeeba AXO.
	 * @param string $axo One of Akeeba Backup's AXOs (download, configuration, backup). 
	 * @param int $user_id The user ID to control. Use null for current user.
	 */
	public function authorizeUser($axo, $user_id = null)
	{
		// Load the ACLs and cache them for future use
		static $acls = null;
		
		if(is_null($acls)) {
			$db = $this->getDBO();
			$db->setQuery('SELECT * FROM '.$db->nameQuote('#__ak_acl'));
			$acls = $db->loadObjectList('user_id');
			if(empty($acls)) $acls = array();
		}
		
		// Get the user ID and the user object
		if(!is_null($user_id)) {
			$user_id = (int)$user_id;
		}
		
		if(empty($user_id)) {
			$user =& JFactory::getUser();
			$user_id = $user->id;
		} else {
			$user =& JFactory::getUser($user_id);
		}
		
		// Get the default (group) permissions
		$defaultPerms = $this->getDefaultPermissions($user->gid);
		
		// Get the user permissions, if any
		if(array_key_exists($user_id, $acls)) {
			$acl = $acls[$user_id];	
		} else {
			$acl = null;
		}
		
		if(is_object($acl)) {
			$userPerms = json_decode($acl->permissions, true);
		} else {
			$userPerms = array();
		}
		
		// Find out the correct set of permissions (user permissions override default ones)
		$perms = array_merge($defaultPerms, $userPerms);
		
		// Return the control status of these permissions
		if(array_key_exists($axo, $perms)) {
			return $perms[$axo] == 1;
		} else {
			return true;
		}
	}
	
	
	/**
	 * Gets the default permissions for a Joomla! 1.5 user group
	 * @param int $gid The Group ID to test for
	 */
	public function getDefaultPermissions($gid)
	{
		$permissions = array(
			'backup'	=> 0,
			'configure'	=> 0,
			'download'	=> 0
		);
		
		switch($gid)
		{
			case 25:
				// Super administrator
				$permissions = array(
					'backup'	=> 1,
					'configure'	=> 1,
					'download'	=> 1
				);
				break;
				
			case 24:
				$permissions = array(
					'backup'	=> 1,
					'configure'	=> 0,
					'download'	=> 1
				);
				break;
				
			case 23:
				$permissions = array(
					'backup'	=> 1,
					'configure'	=> 0,
					'download'	=> 0
				);
				break;
		}
		
		return $permissions;
	}
	
	public function &getUserList()
	{
		$db = $this->getDBO();
		$sql = 'SELECT `id`, `username`, `usertype` FROM `#__users` WHERE `gid` >= 23 AND `block` = 0';
		$db->setQuery($sql);
		$list = $db->loadAssocList();
		for($i=0; $i < count($list); $i++)
		{
			$list[$i]['backup'] = $this->authorizeUser('backup', $list[$i]['id']);
			$list[$i]['download'] = $this->authorizeUser('download', $list[$i]['id']);
			$list[$i]['configure'] = $this->authorizeUser('configure', $list[$i]['id']);
		}
		
		return $list;
	}
}