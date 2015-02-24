<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 * @version $Id: folders.php 430 2011-02-06 11:14:44Z nikosdion $
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

/**
 * Folder exclusion filter. Excludes certain hosting directories.
 */
class AEFilterPlatformFolders extends AEAbstractFilter
{
	public function __construct()
	{
		$this->object	= 'dir';
		$this->subtype	= 'all';
		$this->method	= 'direct';
		$this->filter_name = 'PlatformFolders';

		// We take advantage of the filter class magic to inject our custom filters
		$this->filter_data['[SITEROOT]'] = array (
			'awstats',
			'cgi-bin'
		);

		parent::__construct();
	}

}