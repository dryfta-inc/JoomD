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

/**
 * The JTable child implementing #__ak_acl data handling
 *
 */
class TableAcl extends JTable
{
	/** @var int Primary key */
	public $user_id;

	/** @var string Permissions (JSON-encoded) */
	public $permissions;

	/**
	 * Constructor
	 *
	 * @param JDatabase $db Joomla!'s database
	 */
	public function __construct( &$db )
	{
		parent::__construct('#__ak_acl', 'user_id', $db);
	}
}