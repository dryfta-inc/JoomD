<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.model' );
jimport( 'joomla.installer.installer' );
jimport('joomla.installer.helper');


class JoomdModelApps extends JModel
{
    
    var $_total = null;
	var $_pagination = null;
	var $_warnings = array();
	var $_return = null;
	var $_manifest = null;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.apps.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
		
		// In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$this->_filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',	'filter_state',	'',	'word' );
		
		$this->_filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.ordering', 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		
		$this->_filter_search			= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search			= JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );		

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setWarning($msg)
	{
		
		array_push($this->_warnings, $msg);
		
	}
	
	function _buildQuery()
	{
		$query = 'select i.*, t.name as type from #__joomd_apps as i join #__joomd_apptype as t on i.type=t.id ';

		return $query;
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function &getItem()
    {
		
		$row =  $this->getTable();
		
		$row->load($this->_id);
		
        return $row;
    }
	
	function &getItems()
    {
        if(empty($this->_data))	{
		
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			
			$this->_data = $this->_getList($query, $this->_limitstart, $this->_limit);
		
		}
		echo $this->_db->getErrorMsg();
        return $this->_data;
    }
	
	function getParams()
  	{
		$this->item = new stdClass();
		
		$this->item->limit = $this->_limit;
		$this->item->limitstart = $this->_limitstart;
		$this->item->akey = $this->_akey;
		$this->item->filter_search = $this->_filter_search;
		$this->item->filter_state = $this->_filter_state;
		$this->item->filter_order = $this->_filter_order;
		$this->item->filter_order_Dir = $this->_filter_order_Dir;
		
        // Load the total if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
            $this->item->total = (int) $this->_getListCount($query);    
        }
        return $this->item;
  	}
	
	function _buildItemFilter()
	{
		
        $where = array();
				
		if ( $this->_filter_state == 'P' )
			$where[] = 'i.published = 1';
		
		else if ($this->_filter_state == 'U' )
			$where[] = 'i.published = 0';
		
		if(!empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(i.label) regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(i.label) like '.$this->_db->Quote( $akey.'%', false );
			
		}
		
		if($this->_filter_search)	{
			
			$where2 = array();
			
			$where2[] = 'i.id = '.$this->_db->Quote( $this->_db->escape( $this->_filter_search, true ), false );
			
			$where2[] = 'LOWER( i.label ) LIKE '.$this->_db->Quote( '%'.$this->_db->escape( $this->_filter_search, true ).'%', false );
			
			$where[] = count($where2)?'('.implode(' or ', $where2).')':'';
			
		}
		
		$filter = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
		 
        return $filter;
	}
	
	function _buildItemOrderBy()
	{
 
        $orderby = ' group by i.id ORDER BY '.$this->_filter_order.' '.$this->_filter_order_Dir;
 
        return $orderby;
	}
	
	function getOrder_list()
	{
		
		$query = 'select * from #__joomd_apps order by label asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		return $items;
	
	}

	function store()
	{
    	
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		if(JRequest::getInt('id', 0))	{
			
			$obj = $this->update();
			
		}
		
		else	{
			
			$obj = $this->save();
		
		}
		
		
		return $obj;
		
	}
	
	protected function save()	{
	
		$post = JRequest::get('post');
		
		$obj = new stdClass();
		$file = new stdClass();
		$obj->result = 'error';
		$file->error = '';
		
		$this->installer =  JInstaller::getInstance();
		$package = $this->_getPackageFromUpload();
		
		if (!$package) {
			$obj->error = $this->getError();
			$file->error = $obj->error;	
		}
		
		elseif (!$this->setUpInstall($package['dir'])) {
			$obj->error = $this->getError();
			$file->error = $obj->error;
		}
		
		elseif(!$this->install())	{
			$obj->error = $this->getError();
			$file->error = $obj->error;
		}
		
		else	{
			
			if (!is_file($package['packagefile'])) {
				$config =  JFactory::getConfig();
				$package['packagefile'] = $config->getValue('config.tmp_path').'/'.$package['packagefile'];
			}
			
			JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
			
			
			$obj->id = $this->_id;
			
			$obj->warnings = $this->_warnings;
			
			$obj->result = 'success';
			
			$obj->msg = JText::_('SAVESUCCESS');
			
		}
		
		$obj->warnings = $this->_warnings;
		
		$obj->file = array($file);
		
		if($obj->result == 'success')	{
		
		if($this->manifest->attributes('type') == 'joomdapp')	{
		
		$this->item = $this->getItem();
		$this->order_list = $this->getOrder_list();
		
		ob_start();
		
		?>
        
        <tr>
    <td class="key"><?php echo JText::_('LABEL'); ?>:</td>
    <td><?php echo JText::_($this->item->label); ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('ORDERING'); ?>:</td>
    <td colspan="2">
	<?php
		
		echo '<select name="ordering" id="ordering">';
		
		for($i=0;$i<count($this->order_list);$i++)	{
		
			echo '<option value="'.$this->order_list[$i]->ordering.'"';
			if($this->item->ordering == $this->order_list[$i]->ordering)
				echo ' selected="selected" ';
			echo '>'.$this->order_list[$i]->ordering.'::'.$this->order_list[$i]->label.'</option>';
		
		}
		
		echo '</select>';

	?>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('DESCRIPTION'); ?>:</td>
    <td><?php echo JText::_($this->item->descr); ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PUBLISHED'); ?>:</td>
    <td><input type="radio" name="published" id="published" value="1" <?php if($this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="published" id="published" value="0" <?php if(!$this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
  <?php
  
  		$obj->html = ob_get_contents();
		
		ob_end_clean();
		
		}
		
		}
		
		return $obj;
		
	}
	
	protected function update()
	{
		
		$post = JRequest::get('post');
		
		$obj = new stdClass();
		
		$obj->result = 'error';
			
		$row =  $this->getTable();
		
		$row->load($post['id']);
		
		if(!$row->iscore)
			$row->published	= $post['published'];
		$row->ordering	= $post['ordering'];
		
		// If there was an error with registration, set the message and display form
		if ( !$row->store() )
		{
			$obj->error = $row->getError();
		}
		else	{
			$obj->result = 'success';
			$obj->msg = JText::_('INFOSVSUCC');
			
		}
				
		return $obj;
		
	}
	
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$id = JRequest::getInt( 'id', 0 );		

		if (!$id) {
			$this->setError( JText::_( 'SLEAPLGTD', true ) );
			return false;
		}
		
		$row =  $this->getTable();

		if(!$this->uninstall($id))	{
				
			return false;
			
		}


		return true;
	}
	
	function publish()
	{
	
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
		
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		
		if(count($cid) < 1)	{
			$this->setError(JText::_('PLSSELALEAONERE'));
			return false;
		}
		
		$row =  $this->getTable();
		
		if (!$row->publish($cid, $publish))	{
			$this->setError($row->getError());
			return false;
		}
		
		return true;
	
	}
	
	/**
	 * Moves the order of a record
	 */
	function reorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );
				
		$ordering = JRequest::getVar('ordering', array(0), 'post', 'array');
		
		if(count($ordering) < 1)	{
			$this->setError( JText::_('NO_ITEM_FOUND') );
			return false;	
		}
		
		$items = $this->getItems();
		
		$row =  $this->getTable();
		
		for($i=0;$i<count($items);$i++)	{
			
			$order = substr($ordering[$i], 6);
			
			$row->load($items[$order]->id);				
			$row->ordering = $items[$i]->ordering;
			
			if(!$row->store())	{
				$this->setError($row->getError());
				return false;
			}
				
			
		}
		
		return true;
	}

	/**
	 * Saves the orders of the supplied list
	 */
	function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALIDTOKEN') );


		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (empty( $cid )) {
			$this->setError( JText::_('NO_ITEM_SELECTED') );
			return false;
		}

		$total		= count( $cid );
		$row =  $this->getTable();
		$groupings = array();

		$order 		= JRequest::getVar( 'ordering', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order);

		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError( $row->getError() );
					return false;
				}
			}
		}

		return true;
	}
	
	//package installation
	
	protected function install()
	{
		
		$this->manifest =  $this->_manifest->document;
		
		if($this->manifest->attributes('type') == 'joomdapp')	{
			
			if($this->manifest->attributes('apptype') == 3)
				return $this->installfield();
			else
				return $this->installapp();
		}
		
		else	{
			$this->setError(JText::_('PLGCNREC'));
			return false;
		}
		
	}
	
	protected function installfield()
	{
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the extensions name
		$name =  $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'cmd');
		$name = strtolower(str_replace(' ', '', $name));		
		
		$this->installer->set('name', $name);
		
		$isNew = $this->_id?false:true;
		$update = true;
		
		// Get the component description
		$description =  $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->installer->set('message', $description->data());
		} else {
			$this->installer->set('message', '' );
		}

		// Get some important manifest elements
		$this->adminElement		=  $this->manifest->getElementByPath('administration');
		$this->siteFiles		=  $this->manifest->getElementByPath('files');
		

		// Set the installation target paths
		$this->installer->setPath('classPath', JPath::clean(JPATH_SITE.'/components/com_joomd/libraries/apps/field/fields'));
		$this->installer->setPath('extension_site', $this->installer->getPath('classPath'));
		$this->installer->setPath('extension_administrator', $this->installer->getPath('classPath'));
		
		// Filesystem Processing Section

		// If the component site or admin directory already exists, then we will assume that the component is already
		// installed or another component is using that directory.

		if (is_file($this->installer->getPath('classPath').'/'.$name.'.php'))
		{
			
			$query = 'select id from #__joomd_apps where name = '.$this->_db->Quote($this->installer->get('name'));
			$this->_db->setQuery( $query );
			$this->_id = $this->_db->loadResult();
			
			if (!$this->installer->isOverwrite() or !$this->_id)
			{
				
				// Overwrite is set.
				// If the APP exists say so.
				$this->setError(JText::sprintf('ANOTHER_APP_ALREADY_INSTALLED', $this->installer->getPath('classPath')));
	
				return false;
				
			}
		}
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Find files to copy
		if ( is_a($this->siteFiles, 'JSimpleXMLElement') )
		{
		
			if ($this->parseFiles($this->siteFiles) === false) {
				// Install failed, rollback any changes
				$this->installer->abort();
				return false;
			}
		
		}
		
		if ( is_a($this->adminElement, 'JSimpleXMLElement') )
		{
			
			$this->adminElement->languages	=  $this->adminElement->getElementByPath('languages');
			
			if ( is_a($this->adminElement->languages, 'JSimpleXMLElement') )
				$this->parseLanguages($this->adminElement->languages, 1);
			
		}
		
		
		$this->manifest->languages	=  $this->manifest->getElementByPath('languages');
		
		if ( is_a($this->manifest->languages, 'JSimpleXMLElement') )
			$this->parseLanguages($this->manifest->languages);
		
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		 
		if (isset($this->manifest->install->sql))
		{
			$utfresult = $this->parseSQLFiles($this->manifest->install->sql);

			if ($utfresult === false)
			{
				// Install failed, rollback changes
				$this->installer->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_SQL_ERROR', $this->_db->stderr(true)));

				return false;
			}
		}
				
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->copyManifest()) {
			$this->setError(JText::_('COMPO').' '.JText::_('INSTALL').': '.JText::_('CNOTCSETF'));
			// Install failed, rollback changes
			$this->installer->abort();
			return false;
		}
		
		if(!$this->_buildMenus())	{
			$this->installer->abort();
			return false;
		}
		
		return true;
		
	}
	
	protected function installapp()
	{
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the extensions name
		$name =  $this->manifest->getElementByPath('name');
		$filter = new JFilterInput();
		$name = $filter->clean($name->data(), 'cmd');
		$name = strtolower(str_replace(' ', '', $name));		
		
		$this->installer->set('name', $name);
		
		$isNew = $this->_id?false:true;
		$update = true;
		
		// Get the component description
		$description =  $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->installer->set('message', $description->data());
		} else {
			$this->installer->set('message', '' );
		}

		// Get some important manifest elements
		$this->adminElement		=  $this->manifest->getElementByPath('administration');
		$this->siteFiles		=  $this->manifest->getElementByPath('files');
		

		// Set the installation target paths
		$this->installer->setPath('extension_site', JPath::clean(JPATH_SITE.'/components/com_joomd'));
		$this->installer->setPath('extension_administrator', JPath::clean(JPATH_ADMINISTRATOR.'/components/com_joomd'));
		
		$this->installer->setPath('classPath', $this->installer->getPath('extension_site').'/libraries/apps/'.$name);
		
		// Filesystem Processing Section

		// If the component site or admin directory already exists, then we will assume that the component is already
		// installed or another component is using that directory.

		if (is_dir($this->installer->getPath('classPath')))
		{
			// Look for an update function or update tag
			$updateElement = isset($this->manifest->update)?$this->manifest->update:false;
			// Upgrade manually set or
			// Update function available or
			// Update tag detected
			
			$query = 'select id from #__joomd_apps where name = '.$this->_db->Quote($this->installer->get('name'));
			$this->_db->setQuery( $query );
			$this->_id = $this->_db->loadResult();
			
			if ($this->_id and ($this->installer->isUpgrade() || ($this->installer->manifestClass && method_exists($this->installer->manifestClass, 'update')) || $updateElement))
			{
				$this->setError(JText::sprintf('ANOTHER_APP_ALREADY_INSTALLED', $this->installer->getPath('classPath')));
	
				return false;
				//return $this->updateapp(); // transfer control to the update function will use in the next version
			}
			elseif (!$this->installer->isOverwrite() or !$this->_id)
			{
				
				// Overwrite is set.
				// If the APP exists say so.
				$this->setError(JText::sprintf('ANOTHER_APP_ALREADY_INSTALLED', $this->installer->getPath('classPath')));
	
				return false;
				
			}
		}
		
		$params = new stdClass();
		
		$params->aview = array(); $params->sview=array();
		
		if ( isset($this->manifest->scriptfile) )	{
		
			$scriptfile = (string) $this->manifest->scriptfile;
			
			if ($manifestScript)	{
	
				// If there is an manifest class file, lets load it; we'll copy it later (don't have dest yet)
				$manifestScript = (string) $this->manifest->scriptfile;
		
				if ($manifestScript)
				{
					$manifestScriptFile = $this->installer->getPath('source') . '/' . $manifestScript;
		
					if (is_file($manifestScriptFile))
					{
						// Load the file
						include_once $manifestScriptFile;
					}
		
					// Set the class name
					$classname = $this->installer->get('element') . 'InstallerScript';
		
					if (class_exists($classname))
					{
						// Create a new instance
						$this->installer->manifestClass = new $classname($this);
						// And set this so we can copy it later
						$this->set('manifest_script', $manifestScript);
		
						// Note: if we don't find the class, don't bother to copy the file
					}
				
				}
			
			}
		
		}
		
		// Run preflight if possible (since we know we're not an update)
		ob_start();
		ob_implicit_flush(false);
		
		if ($this->installer->manifestClass && method_exists($this->installer->manifestClass, 'preflight'))
		{	
				
			if ($this->installer->manifestClass->preflight('install', $this) === false)
			{
				// Install failed, rollback changes
				$this->setError(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'));
				$this->installer->abort();
				return false;
			}
		}

		// Create msg object; first use here
		$msg = ob_get_contents();
		ob_end_clean();
		
		
		// If the component directory does not exist, let's create it
		$created = false;
		
		if (!file_exists($this->installer->getPath('classPath')))
		{
			if (!$created = JFolder::create($this->installer->getPath('classPath')))
			{
				$this->setError(JText::sprintf('JLIB_INSTALLER_ERROR_COMP_INSTALL_FAILED_TO_CREATE_DIRECTORY_SITE', $this->installer->getPath('classPath')));
				return false;
			}
		}

		// Since we created the component directory and will want to remove it if we have to roll back
		// the installation, let's add it to the installation step stack

		if ($created)
		{
			$this->installer->pushStep(array('type' => 'folder', 'path' => $this->installer->getPath('classPath')));
		}
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Find files to copy
		if ( is_a($this->siteFiles, 'JSimpleXMLElement') )
		{
		
			foreach ($this->siteFiles->children() as $child)
			{
				if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'views') {
					if ($this->parseViews($child) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}
					array_push($params->sview, $child->attributes('name'));
					$this->installer->pushStep(array ('type' => 'folder', 'path' => $this->installer->getPath('extension_site').'/views/'.$child->attributes('name')));
				}
				else	{
					if ($this->parseFiles($child) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}				
				}
			}
		
		}
		
		if ( is_a($this->adminElement, 'JSimpleXMLElement') )
		{
			
			$this->adminFiles		=  $this->adminElement->getElementByPath('files');

			foreach ($this->adminFiles->children() as $child)
			{
				if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'views') {
					if ($this->parseViews($child, 1) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}
					array_push($params->aview, $child->attributes('name'));
					$this->installer->pushStep(array ('type' => 'folder', 'path' => $this->installer->getPath('extension_administrator').'/views/'.$child->attributes('name')));
				}
				else	{
					if ($this->parseFiles($child, 1) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}				
				}
			}
			
			$this->adminElement->languages	=  $this->adminElement->getElementByPath('languages');
			
			if ( is_a($this->adminElement->languages, 'JSimpleXMLElement') )
				$this->parseLanguages($this->adminElement->languages, 1);
			
		}
		
		
		$this->manifest->languages	=  $this->manifest->getElementByPath('languages');
		
		if ( is_a($this->manifest->languages, 'JSimpleXMLElement') )
			$this->parseLanguages($this->manifest->languages);
		
		
		// If there is a manifest script, let's copy it.
		if ($this->installer->get('manifest_script'))
		{
			$path['src'] = $this->installer->getPath('source') . '/' . $this->installer->get('manifest_script');
			$path['dest'] = $this->installer->getPath('classPath') . '/' . $this->installer->get('manifest_script');

			if (!file_exists($path['dest']) || $this->installer->getOverwrite())
			{
				if (!$this->installer->copyFiles(array($path)))
				{
					// Install failed, rollback changes
					$this->installer->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_MANIFEST'));

					return false;
				}
			}
		}
		
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		/*
		 * Let's run the install queries for the component
		 *	If backward compatibility is required - run queries in xml file
		 *	If Joomla 1.5 compatible, with discreet sql files - execute appropriate
		 *	file for utf-8 support or non-utf-8 support
		 */
		$this->install = $this->manifest->getElementByPath('install/sql');
		
		if (is_a($this->install, 'JSimpleXMLElement'))
		{
			$utfresult = $this->parseSQLFiles($this->install);

			if ($utfresult === false)
			{
				// Install failed, rollback changes
				$this->installer->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_SQL_ERROR', $this->_db->stderr(true)));

				return false;
			}
		}
		
		
		ob_start();
		ob_implicit_flush(false);
		
		if ($this->installer->manifestClass && method_exists($this->installer->manifestClass, 'install'))
		{
			if ($this->installer->manifestClass->install($this) === false)
			{
				// Install failed, rollback changes
				$this->installer->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'));

				return false;
			}
		}

		// Append messages
		$msg .= ob_get_contents();
		ob_end_clean();
		
		$this->installer->set('params', $params);		
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->copyManifest()) {
			$this->setError(JText::_('COMPO').' '.JText::_('INSTALL').': '.JText::_('CNOTCSETF'));
			// Install failed, rollback changes
			$this->installer->abort();
			return false;
		}
		
		if(!$this->_buildMenus())	{
			$this->installer->abort();
			return false;
		}
		
		// And now we run the postflight
		ob_start();
		ob_implicit_flush(false);

		if ($this->installer->manifestClass && method_exists($this->installer->manifestClass, 'postflight'))
		{
						
			$this->installer->manifestClass->postflight('install', $this);
		}

		// Append messages
		$msg .= ob_get_contents();
		ob_end_clean();
		
		return true;
		
	}
	
	protected function updateapp()
	{
		
		// Set the overwrite setting
		$this->installer->setOverwrite(true);

		// Get the extension manifest object
		$this->manifest = $this->installer->getManifest();
		
		/**
		 * Hunt for the original XML file
		 */
		$old_manifest = null;
		// Create a new installer because findManifest sets stuff
		// Look in the administrator first
		$tmpInstaller = new JInstaller;
		$tmpInstaller->setPath('source', $this->installer->getPath('classPath'));

		if (!$tmpInstaller->findManifest())
		{
			// Then the site
			$old_manifest = null;
			$this->oldAdminFiles = null;
			$this->oldFiles = null;
		}
		else
		{
			$old_manifest = $tmpInstaller->getManifest();
			
			$this->oldAdminFiles = $old_manifest->administration->files;
			$this->oldFiles = $old_manifest->files;
			
		}
		
		$params = new stdClass();
		
		$params->aview = array(); $params->sview=array();
		
		if ( isset($this->manifest->scriptfile) )	{
		
			$scriptfile = (string) $this->manifest->scriptfile;
			
			if ($manifestScript)	{
	
				// If there is an manifest class file, lets load it; we'll copy it later (don't have dest yet)
				$manifestScript = (string) $this->manifest->scriptfile;
		
				if ($manifestScript)
				{
					$manifestScriptFile = $this->installer->getPath('source') . '/' . $manifestScript;
		
					if (is_file($manifestScriptFile))
					{
						// Load the file
						include_once $manifestScriptFile;
					}
		
					// Set the class name
					$classname = $this->installer->get('element') . 'InstallerScript';
		
					if (class_exists($classname))
					{
						// Create a new instance
						$this->installer->manifestClass = new $classname($this);
						// And set this so we can copy it later
						$this->set('manifest_script', $manifestScript);
		
						// Note: if we don't find the class, don't bother to copy the file
					}
				
				}
			
			}
		
		}
		
		// Run preflight if possible (since we know we're not an update)
		ob_start();
		ob_implicit_flush(false);

		if ($this->installer->manifestClass && method_exists($this->installer->manifestClass, 'preflight'))
		{
			if ($this->installer->manifestClass->preflight('update', $this) === false)
			{
				// Install failed, rollback changes
				$this->installer->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'));

				return false;
			}
		}

		// Create msg object; first use here
		$msg = ob_get_contents();
		ob_end_clean();
		
		// If the component directory does not exist, let's create it
		$created = false;
		
		if (!file_exists($this->installer->getPath('classPath')))
		{
			if (!$created = JFolder::create($this->installer->getPath('classPath')))
			{
				$this->setError(JText::sprintf('JLIB_INSTALLER_ERROR_COMP_INSTALL_FAILED_TO_CREATE_DIRECTORY_SITE', $this->installer->getPath('classPath')));
				return false;
			}
		}
		
		if ($created)
		{
			$this->installer->pushStep(array('type' => 'folder', 'path' => $this->installer->getPath('classPath')));
		}
		
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Find files to copy
		if ( is_a($this->siteFiles, 'JSimpleXMLElement') )
		{
		
			foreach ($this->siteFiles->children() as $child)
			{
				if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'views') {
					if ($this->parseViews($child) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}
					array_push($params->sview, $child->attributes('name'));
					$this->installer->pushStep(array ('type' => 'folder', 'path' => $this->installer->getPath('extension_site').'/views/'.$child->attributes('name')));
				}
				else	{
					if ($this->parseFiles($child) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}				
				}
			}
		
		}
		
		if ( is_a($this->adminElement, 'JSimpleXMLElement') )
		{
			
			$this->adminFiles		=  $this->adminElement->getElementByPath('files');

			foreach ($this->adminFiles->children() as $child)
			{
				if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'views') {
					if ($this->parseViews($child, 1) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}
					array_push($params->aview, $child->attributes('name'));
					$this->installer->pushStep(array ('type' => 'folder', 'path' => $this->installer->getPath('extension_administrator').'/views/'.$child->attributes('name')));
				}
				else	{
					if ($this->parseFiles($child, 1) === false) {
						// Install failed, rollback any changes
						$this->installer->abort();
						return false;
					}				
				}
			}
			
			$this->adminElement->languages	=  $this->adminElement->getElementByPath('languages');
			
			if ( is_a($this->adminElement->languages, 'JSimpleXMLElement') )
				$this->parseLanguages($this->adminElement->languages, 1);
			
		}
		
		
		$this->manifest->languages	=  $this->manifest->getElementByPath('languages');
		
		if ( is_a($this->manifest->languages, 'JSimpleXMLElement') )
			$this->parseLanguages($this->manifest->languages);
		
		
		// If there is a manifest script, let's copy it.
		if ($this->installer->get('manifest_script'))
		{
			$path['src'] = $this->installer->getPath('source') . '/' . $this->installer->get('manifest_script');
			$path['dest'] = $this->installer->getPath('classPath') . '/' . $this->installer->get('manifest_script');

			if (!file_exists($path['dest']) || $this->installer->getOverwrite())
			{
				if (!$this->installer->copyFiles(array($path)))
				{
					// Install failed, rollback changes
					$this->installer->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_MANIFEST'));

					return false;
				}
			}
		}
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		/*
		 * Let's run the update queries for the component
		 */

		if ($this->manifest->update)
		{
			$result = $this->installer->parseSchemaUpdates($this->manifest->update->schemas, $eid);

			if ($result === false)
			{
				// Install failed, rollback changes
				$this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_UPDATE_SQL_ERROR', $db->stderr(true)));

				return false;
			}
		}
		
	}
	
	protected function _buildMenus()
	{
		
		$isNew = $this->_id?false:true;
		
		$name = $this->installer->get('name');
		$label = $name;
		$descr = $this->installer->get('message');
		$published = 1;
		$item = 0;
		$type = 5;
		$params = json_encode($this->installer->get('params'));
		
		$ordering = 1;
		$query = 'select ordering from #__joomd_apps order by ordering desc limit 1';
		$this->_db->setQuery( $query );
		$ordering += $this->_db->loadResult();
								
		if($this->manifest->attributes('label'))
			$label = $this->manifest->attributes('label');
		
		if($this->manifest->attributes('apptype'))
			$type = $this->manifest->attributes('apptype');
			
		if($this->manifest->attributes('item'))
			$item = $this->manifest->attributes('item');
					
		if($isNew)
			$query = 'insert into #__joomd_apps (name, label, descr, type, item, ordering, published, params) values ('.$this->_db->Quote($name).', '.$this->_db->Quote($label).', '.$this->_db->Quote($descr).', '.(int)$type.', '.(int)$item.', '.(int)$ordering.', '.(int)$published.', '.$this->_db->Quote($params).')';
		else
			$query = 'update #__joomd_apps set name = '.$this->_db->Quote($name).', label = '.$this->_db->Quote($label).', descr = '.$this->_db->Quote($descr).', type = '.(int)$type.', item = '.(int)$item.', params = '.$this->_db->Quote($params).' where id = '.$this->_id;
		$this->_db->setQuery($query);
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if($isNew)
			$this->_id = $this->_db->insertid();
		
		if($this->manifest->attributes('apptype')==3)	{
			
			$query = 'insert into #__joomd_fieldtypes (type, label) values ('.$this->_db->Quote($name).', '.$this->_db->Quote($label).')';
			$this->_db->setQuery($query);
		
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			return true;
			
		}
		
		$query = 'select extension_id from #__extensions where element = '.$this->_db->Quote('com_joomd');
		$this->_db->setQuery( $query );
		$component_id = $this->_db->loadResult();
		
		$query = 'select id from #__menu where client_id = 1 and component_id = '.(int)$component_id.' and parent_id = 1 and type = '.$this->_db->Quote('component').' order by id asc limit 1';
		$this->_db->setQuery( $query );
		$parent_id = $this->_db->loadResult();
		
		$table = JTable::getInstance('menu');
		
		if ( is_a($this->adminElement, 'JSimpleXMLElement') )	{
		
			$menuElement =  $this->adminElement->getElementByPath('menu');
			
			if (is_a($menuElement, 'JSimpleXMLElement') and $isNew) {
				
				$data = array();
				$data['menutype'] = 'main';
				$data['client_id'] = 1;
				$data['title'] = (string) $menuElement->data();
				$data['alias'] = (string) $menuElement->data();
				$data['link'] = ($menuElement->attributes('link'))?('index.php?'.$menuElement->attributes('link')):'index.php?option=com_joomd&view='.$name;
				$data['type'] = 'component';
				$data['published'] = 1;
				$data['parent_id'] = $parent_id;
				$data['component_id'] = $component_id;
				$data['img'] = ((string) $menuElement->attributes('img')) ? (string) ('components/com_joomd/assets/images/icon-16-'.$menuElement->attributes('img')) : 'class:component';
				$data['home'] = 0;
	
				if (!$table->setLocation($parent_id, 'last-child') || !$table->bind($data) || !$table->check() || !$table->store())
				{
					// Install failed, warn user and rollback changes
					$this->setError($table->getError());
					return false;
				}
	
				/*
				 * Since we have created a menu item, we add it to the installation step stack
				 * so that if we have to rollback the changes we can undo it.
				 */
				$this->installer->pushStep(array('type' => 'menu', 'id' => $component_id));
				
			}
		
		}
		
		return true;
		
	}
	
	//getting package from the upload
	
	function _getPackageFromUpload()
	{
		// Get the uploaded file information
		$userfile = JRequest::getVar('file', null, 'files', 'array' );

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			$this->setError(JText::_('WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			$this->setError(JText::_('WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			$this->setError(JText::_('NOFILESEL'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 )
		{
			$this->setError(JText::_('WARNINSTALLUPLOADERROR'));
			return false;
		}
		
		$ext = strrchr($userfile['name'], '.');
		
		if($ext <> ".zip")	{
			$this->setError(JText::_('ONLYZIPFALLO'));
			return false;
		}

		// Build the appropriate paths
		$config =  JFactory::getConfig();
		$tmp_dest 	= $config->get('tmp_path').'/'.$userfile['name'];
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Unpack the downloaded package file
		$package = $this->unpack($tmp_dest);

		return $package;
	}
	
	//set up install
	
	function setUpInstall($source)
	{	
		
		// Get an array of all the xml files from teh installation directory
		$xmlfiles = JFolder::files($source, '.xml$', 1, true);
		// If at least one xml file exists
		if (!empty($xmlfiles)) {
			foreach ($xmlfiles as $file)
			{
				// Is it a valid joomla installation manifest file?
				$manifest = $this->_isManifest($file);
				if (!is_null($manifest)) {

					// If the root method attribute is set to upgrade, allow file overwrite
					$root =  $manifest->document;
					if ($root->attributes('method') == 'upgrade') {
						$this->installer->setOverwrite( true );
					}

					// Set the manifest object and path
					$this->_manifest =  $manifest;
					$this->installer->setPath('manifest', $file);

					// Set the installation source path to that of the manifest file
					$this->installer->setPath('source', dirname($file));

					return true;
				}
			}

			// None of the xml files found were valid install files
			$this->setError(JText::_('ERRORNOTFINDJOOMLAXMLSETUPFILE'));
			return false;
		} else {
			// No xml files were found in the install folder
			$this->setError(JText::_('ERRORXMLSETUP'));
			return false;
		}

		return true;
	}
	
	protected function uninstall($id)
	{
		// Initialize variables
		$retval	= true;

		// First order of business will be to load the component object table from the database.
		// This should give us the necessary information to proceed.
		$row = $this->getTable();
		
		if ( !$row->load((int) $id) || !trim($row->name) ) {
			$this->setError(JText::_('ERRORUNKOWNEXTENSION'));
			return false;
		}

		// Is the component we are trying to uninstall a core one?
		// Because that is not a good idea...
		if ($row->iscore) {
			$this->setError(JText::_('WARNCORECOMPONENT2'));
			return false;
		}
		
		$this->installer =  JInstaller::getInstance();
		
		$this->installer->set('name', $row->name);
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Find and load the XML install file for the component
		$this->setUpInstall(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$row->name);
		
		$manifest = $this->_manifest;
		
		if (!is_a($manifest, 'JSimpleXML')) {
			
			$this->setError(JText::_('CNOTFSETTXMLSERFPCNBU'));
			// Return
			return false;
		}
		
		// Get the root node of the manifest document
		$this->manifest =  $manifest->document;
		
		if($this->manifest->attributes('apptype')==3)	{
			return $this->uninstallfield($row);
		}
		else	{
			return $this->uninstallapp($row);
		}
		
	}
	
	protected function uninstallapp($row)
	{
		
		$retval = true;
		
		// Get the admin and site paths for the component
		$this->installer->setPath('extension_administrator', JPath::clean(JPATH_ADMINISTRATOR.'/components'.'/com_joomd'));
		$this->installer->setPath('extension_site', JPath::clean(JPATH_SITE.'/components/com_joomd'));
				
		$this->installer->setPath('classPath', $this->installer->getPath('extension_site').'/libraries/apps/'.$row->name);
		
		// Get some important manifest elements
		$this->adminElement		=  $this->manifest->getElementByPath('administration');
		$this->siteFiles		=  $this->manifest->getElementByPath('files');
		
		//language section to remove the app language in 2.3.0
		$this->manifest->languages	=  $this->manifest->getElementByPath('languages');
		
		if ( is_a($this->manifest->languages, 'JSimpleXMLElement') )
			$this->removeLanguages($this->manifest->languages);
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		$this->uninstall = $this->manifest->getElementByPath('uninstall/sql');
		if (is_a($this->uninstall, 'JSimpleXMLElement'))
		{
			$retval = $this->parseSQLFiles($this->uninstall);
		}
		
		// Find files to remove		
		if ( is_a($this->siteFiles, 'JSimpleXMLElement') )
		{
		
			foreach ($this->siteFiles->children() as $child)
			{
				if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'views') {
					if(JFolder::exists($this->installer->getPath('extension_site').'/views/'.$child->attributes('name')))
						JFolder::delete($this->installer->getPath('extension_site').'/views/'.$child->attributes('name'));
						
					if(JFile::exists($this->installer->getPath('extension_site').'/controllers/'.$child->attributes('name').'.php'))
						JFile::delete($this->installer->getPath('extension_site').'/controllers/'.$child->attributes('name').'.php');
						
					if(JFile::exists($this->installer->getPath('extension_site').'/models/'.$child->attributes('name').'.php'))
						JFile::delete($this->installer->getPath('extension_site').'/models/'.$child->attributes('name').'.php');
				}
				else	{
					if ($this->removeFiles($child) === false) {
						$retval = false;
					}				
				}
			}
					
		}
		
		if ( is_a($this->adminElement, 'JSimpleXMLElement') )
		{
			
			$this->adminFiles =  $this->adminElement->getElementByPath('files');		
			
			foreach ($this->adminFiles->children() as $child)
			{
				if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'views') {
					if(JFolder::exists($this->installer->getPath('extension_administrator').'/views/'.$child->attributes('name')))
						JFolder::delete($this->installer->getPath('extension_administrator').'/views/'.$child->attributes('name'));
						
					if(JFile::exists($this->installer->getPath('extension_administrator').'/controllers/'.$child->attributes('name').'.php'))
						JFile::delete($this->installer->getPath('extension_administrator').'/controllers/'.$child->attributes('name').'.php');
						
					if(JFile::exists($this->installer->getPath('extension_administrator').'/models/'.$child->attributes('name').'.php'))
						JFile::delete($this->installer->getPath('extension_administrator').'/models/'.$child->attributes('name').'.php');
				}
				else	{
					if ($this->removeFiles($child, 1) === false) {
						$retval = false;
					}				
				}
			}
			
			$this->adminElement->languages	=  $this->adminElement->getElementByPath('languages');
			
			if ( is_a($this->adminElement->languages, 'JSimpleXMLElement') )
				$this->removeLanguages($this->adminElement->languages, 1);
			
		}
		
		// Delete the component site directory
		if (is_dir($this->installer->getPath('classPath')))
		{
			if (!JFolder::delete($this->installer->getPath('classPath')))
			{
				$this->setError(JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_FAILED_REMOVE_DIRECTORY_SITE'));
				$retval = false;
			}
		}
		
		$this->removeMenus($row);
		
		return $retval;
		
	}
	
	protected function uninstallfield($row)
	{
		
		// Set the installation target paths
		$this->installer->setPath('classPath', JPath::clean(JPATH_SITE.'/components/com_joomd/libraries/apps/field/fields'));
		$this->installer->setPath('extension_site', $this->installer->getPath('classPath'));
		$this->installer->setPath('extension_administrator', $this->installer->getPath('classPath'));
		
		
		// Get some important manifest elements
		$this->adminElement		=  $this->manifest->getElementByPath('administration');
		$this->siteFiles		=  $this->manifest->getElementByPath('files');
		
		// Find files to remove		
		if ( is_a($this->siteFiles, 'JSimpleXMLElement') )
		{
		
			if ($this->removeFiles($this->siteFiles) === false) {
				$retval = false;
			}
					
		}
		
		//language section to remove the app language in 2.3.0
		$this->manifest->languages	=  $this->manifest->getElementByPath('languages');
		
		if ( is_a($this->manifest->languages, 'JSimpleXMLElement') )
			$this->removeLanguages($this->manifest->languages);
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		/*
		 * Let's run the uninstall queries for the component
		 *	If backward compatibility is required - run queries in xml file
		 *	If Joomla 1.5 compatible, with discreet sql files - execute appropriate
		 *	file for utf-8 support or non-utf support
		 */
		// no backward compatibility queries found - try for Joomla 1.5 type queries
		// second argument is the utf compatible version attribute
		$utfresult = $this->parseSQLFiles($this->manifest->getElementByPath('uninstall/sql'));
		if ($utfresult === false) {
			$retval = false;
		}
		
		if ( is_a($this->adminElement, 'JSimpleXMLElement') )
		{
			
			$this->adminElement->languages	=  $this->adminElement->getElementByPath('languages');
			
			if ( is_a($this->adminElement->languages, 'JSimpleXMLElement') )
				$this->removeLanguages($this->adminElement->languages, 1);
			
		}
		
		// Delete the component site directory
		if (is_dir($this->installer->getPath('classPath')))
		{
			if (!JFolder::delete($this->installer->getPath('classPath')))
			{
				$this->setError(JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_FAILED_REMOVE_DIRECTORY_SITE'));
				$retval = false;
			}
		}
		
		$this->removeMenus($row);
		
		return $retval;
		
	}
	
	function removeMenus($row)
	{
		
		$retval = true;
		
		$query = 'select extension_id from #__extensions where element = '.$this->_db->Quote('com_joomd');
		$this->_db->setQuery( $query );
		$component_id = $this->_db->loadResult();
		
		$query = 'select id from #__menu where client_id = 1 and component_id = '.(int)$component_id.' and parent_id = 1 and type = '.$this->_db->Quote('component').' order by id asc limit 1';
		$this->_db->setQuery( $query );
		$parent_id = $this->_db->loadResult();
		
		$table = JTable::getInstance('menu');
		
		if ( is_a($this->adminElement, 'JSimpleXMLElement') )	{
		
			$menuElement =  $this->adminElement->getElementByPath('menu');
			
			if (is_a($menuElement, 'JSimpleXMLElement')) {
				
				$link = ($menuElement->attributes('link'))?('index.php?'.$menuElement->attributes('link')):'index.php?option=com_joomd&view='.$row->name;
				
				$query = 'delete from #__menu where component_id = '.$component_id.' and parent_id = '.$parent_id.' and client_id = 1 and link = '.$this->_db->Quote($link).' limit 1';
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					$retval = false;
				}
				
			}			
		
		}
		
		$query = 'delete from #__joomd_apps where id = '.$row->id;
		$this->_db->setQuery( $query );
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			$retval = false;
		}
		
		return $retval;
		
	}
	
	//check whether this a valid xml file
	
	function &_isManifest($file)
	{
		// Initialize variables
		$null	= null;
		$xml	=  JFactory::getXMLParser('Simple');

		// If we cannot load the xml file return null
		if (!$xml->loadFile($file)) {
			// Free up xml parser memory and return null
			unset ($xml);
			return $null;
		}

		/*
		 * Check for a valid XML root tag.
		 * @todo: Remove backwards compatability in a future version
		 * Should be 'extension'.
		 */
		$root =  $xml->document;
		if (!is_object($root) || ($root->name() != 'extension')) {
			// Free up xml parser memory and return null
			unset ($xml);
			return $null;
		}

		// Valid manifest file return the object
		return $xml;
	}
	
	function removeFiles($element, $cid=0)
	{
		// Initialize variables
		$copyfiles = array ();
		
		// Get the client info
		jimport('joomla.application.helper');
		$client =  JApplicationHelper::getClientInfo($cid);

		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return 0;
		}

		// Get the array of file nodes to process
		$files = $element->children();
		if (count($files) == 0) {
			// No files to process
			return 0;
		}
		
		if ($client) {
			$pathname = 'extension_'.$client->name;
			$destination = $this->installer->getPath($pathname);
		} else {
			$pathname = 'extension_root';
			$destination = $this->installer->getPath($pathname);
		}
		
		switch($element->name())
		{
			
			case 'assets':
			$destination = $destination.'/assets';
			break;
			
			case 'template':
			$destination = JPATH_SITE.'/components/com_joomd/templates';
			break;
			
			case 'app':
			$destination = JPATH_SITE.'/components/com_joomd/libraries/apps/'.$this->installer->get('name');
			break;
			
			case 'files':
			break;
			
			default:
			case 'others':
			$destination = $destination.'/others';
			break;
			
		}

		// Process each file in the $files array (children of $tagName).
		foreach ($files as $file)
		{
			
			if ($folder = $file->attributes('folder')) {
				$path['dest'] = $destination.'/'.$folder.'/'.$file->data();
			}
			else	{
				$path['dest']	= $destination.'/'.$file->data();
			}

			// Is this path a file or folder?
			if( $file->name() == 'folder')	{
			
				if(JFolder::exists($path['dest']))
					 JFolder::delete($path['dest']);
				
			}
			else	{
			
				if(JFile::exists($path['dest']))
					 JFile::delete($path['dest']);
			
			}
			
			array_push($copyfiles, $path);

		}

		return count($copyfiles);
	}
	
	/**
	 * Method to parse through a files element of the installation manifest and take appropriate
	 * action.
	 *
	 * @access	public
	 * @param	object	$element 	The xml node to process
	 * @param	int		$cid		Application ID of application to install to
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function parseViews($element, $cid=0)
	{
		// Initialize variables
		$copyfiles = array ();
		
		// Get the client info
		jimport('joomla.application.helper');
		$client =  JApplicationHelper::getClientInfo($cid);

		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return 0;
		}

		// Get the array of file nodes to process
		$files = $element->children();
		if (count($files) == 0) {
			// No files to process
			return 0;
		}

		/*
		 * Here we set the folder we are going to remove the files from.
		 */
		if ($client) {
			$pathname = 'extension_'.$client->name;
			$dest = $this->installer->getPath($pathname);
		} else {
			$pathname = 'extension_root';
			$dest = $this->installer->getPath($pathname);
		}
		
		$view = $element->attributes('name');
		
		$destination = $dest.'/views/'.$view;
		
		if(JFolder::exists($destination) and !$this->installer->isOverwrite())	{
			$this->setError(sprintf(JText::_('VALREAEXIST'), $view));
			return false;
		}

		/*
		 * Here we set the folder we are going to copy the files from.
		 *
		 * Does the element have a folder attribute?
		 *
		 * If so this indicates that the files are in a subdirectory of the source
		 * folder and we should append the folder attribute to the source path when
		 * copying files.
		 */
		if ($folder = $element->attributes('folder')) {
			$source = $this->installer->getPath('source').'/'.$folder;
		} else {
			$source = $this->installer->getPath('source');
		}

		// Process each file in the $files array (children of $tagName).
		foreach ($files as $file)
		{
			$path['src']	= $source.'/'.$file->data();
			
			if($file->data() == 'model.php')
				$path['dest']	= $dest.'/models/'.$view.'.php';
			elseif($file->data() == 'controller.php')
				$path['dest']	= $dest.'/controllers/'.$view.'.php';
			elseif($file->data() == 'table.php' && $client->name == 'administrator')
				$path['dest']	= $dest.'/tables/'.$view.'.php';
			else
				$path['dest']	= $destination.'/'.$file->data();

			// Is this path a file or folder?
			$path['type']	= ( $file->name() == 'folder') ? 'folder' : 'file';

			/*
			 * Before we can add a file to the copyfiles array we need to ensure
			 * that the folder we are copying our file to exits and if it doesn't,
			 * we need to create it.
			 */
			if (basename($path['dest']) != $path['dest']) {
				$newdir = dirname($path['dest']);

				if (!JFolder::create($newdir)) {
					$this->setError(JText::_('FTOCREDIR').' "'.$newdir.'"');
					return false;
				}
			}

			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}

		return $this->copyFiles($copyfiles);
	}
	
	function parseFiles($element, $cid=0)
	{
		// Initialize variables
		$copyfiles = array ();
		
		// Get the client info
		jimport('joomla.application.helper');
		$client =  JApplicationHelper::getClientInfo($cid);

		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return 0;
		}

		// Get the array of file nodes to process
		$files = $element->children();
		if (count($files) == 0) {
			// No files to process
			return 0;
		}
		
		if ($client) {
			$pathname = 'extension_'.$client->name;
			$destination = $this->installer->getPath($pathname);
		} else {
			$pathname = 'extension_root';
			$destination = $this->installer->getPath($pathname);
		}
		
		switch($element->name())
		{
			
			case 'assets':
			$destination = $destination.'/assets';
			break;
			
			case 'template':
			$destination = JPATH_SITE.'/components/com_joomd/templates';
			break;
			
			case 'app':
			$destination = JPATH_SITE.'/components/com_joomd/libraries/apps/'.$this->installer->get('name');
			break;
			
			case 'files':
			break;
			
			default:
			case 'others':
			$destination = $destination.'/others';
			break;
			
		}

		/*
		 * Here we set the folder we are going to copy the files from.
		 *
		 * Does the element have a folder attribute?
		 *
		 * If so this indicates that the files are in a subdirectory of the source
		 * folder and we should append the folder attribute to the source path when
		 * copying files.
		 */
		
		if ($folder = $element->attributes('folder')) {
			$source = $this->installer->getPath('source').'/'.$folder;
		} else {
			$source = $this->installer->getPath('source');
		}

		// Process each file in the $files array (children of $tagName).
		foreach ($files as $file)
		{
			$path['src']	= $source.'/'.$file->data();
			
			if ($folder = $file->attributes('folder')) {
				$path['dest'] = $destination.'/'.$folder.'/'.$file->data();
			}
			else	{
				$path['dest']	= $destination.'/'.$file->data();
			}

			// Is this path a file or folder?
			$path['type']	= ( $file->name() == 'folder') ? 'folder' : 'file';

			/*
			 * Before we can add a file to the copyfiles array we need to ensure
			 * that the folder we are copying our file to exits and if it doesn't,
			 * we need to create it.
			 */
			if (basename($path['dest']) != $path['dest']) {
				$newdir = dirname($path['dest']);

				if (!JFolder::create($newdir)) {
					$this->setError(JText::_('FTOCREDIR').' "'.$newdir.'"');
					return false;
				}
			}

			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}

		return $this->copyFiles($copyfiles);
	}
	
	/**
	 * Copy files from source directory to the target directory
	 *
	 * @access	public
	 * @param	array $files array with filenames
	 * @param	boolean $overwrite True if existing files can be replaced
	 * @return	boolean True on success
	 * @since	1.5
	 */
	function copyFiles($files, $overwrite=null)
	{
		/*
		 * To allow for manual override on the overwriting flag, we check to see if
		 * the $overwrite flag was set and is a boolean value.  If not, use the object
		 * allowOverwrite flag.
		 */
		if (is_null($overwrite) || !is_bool($overwrite)) {
			$overwrite = $this->installer->setOverwrite( true );
		}

		/*
		 * $files must be an array of filenames.  Verify that it is an array with
		 * at least one file to copy.
		 */
		if (is_array($files) && count($files) > 0)
		{
			foreach ($files as $file)
			{
				// Get the source and destination paths
				$filesource	= JPath::clean($file['src']);
				$filedest	= JPath::clean($file['dest']);
				$filetype	= array_key_exists('type', $file) ? $file['type'] : 'file';

				if (!file_exists($filesource)) {
					/*
					 * The source file does not exist.  Nothing to copy so set an error
					 * and return false.
					 */
					$this->setError($filesource.'::'.$filedest.JText::sprintf('FILEDNEXIS', $filesource));
					return false;
				} elseif (file_exists($filedest) && !$overwrite) {

						/*
						 * It's okay if the manifest already exists
						 */
						if ($this->installer->getPath( 'manifest' ) == $filesource) {
							continue;
						}

						/*
						 * The destination file already exists and the overwrite flag is false.
						 * Set an error and return false.
						 */
						$this->setError(JText::sprintf('WARNSAME', $filedest));
						return false;
				} else {

					// Copy the folder or file to the new location.
					if ( $filetype == 'folder') {

						if (!(JFolder::copy($filesource, $filedest, null, $overwrite))) {
							$this->setError('JInstaller::install: '.JText::sprintf('FAILEDTOCF', $filesource, $filedest));
							return false;
						}

						$step = array ('type' => 'folder', 'path' => $filedest);
					} else {

						if (!(JFile::copy($filesource, $filedest))) {
							$this->setError('JInstaller::install: '.JText::sprintf('FTOCFL', $filesource, $filedest));
							return false;
						}

						$step = array ('type' => 'file', 'path' => $filedest);
					}

					/*
					 * Since we copied a file/folder, we want to add it to the installation step stack so that
					 * in case we have to roll back the installation we can remove the files copied.
					 */
					$this->installer->pushStep( $step );
				}
			}
		} else {

			/*
			 * The $files variable was either not an array or an empty array
			 */
			$this->setError('Files not found.');
			return false;
		}
		return count($files);
	
	}
	
	
	/**
	 * Backward compatible Method to parse through a queries element of the
	 * installation manifest file and take appropriate action.
	 *
	 * @access	public
	 * @param	object	$element 	The xml node to process
	 * @return	mixed	Number of queries processed or False on error
	 * @since	1.5
	 */
	function parseQueries($element)
	{
		// Get the database connector object
		$db =  $this->_db;

		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return 0;
		}

		// Get the array of query nodes to process
		$queries = $element->children();
		if (count($queries) == 0) {
			// No queries to process
			return 0;
		}

		// Process each query in the $queries array (children of $tagName).
		foreach ($queries as $query)
		{
			$db->setQuery($query->data());
			if (!$db->query()) {
				$this->setError('JInstaller::install: '.JText::_('SQLERR')." ".$db->stderr(true));
				return false;
			}
		}
		return (int) count($queries);
	}

	/**
	 * Method to extract the name of a discreet installation sql file from the installation manifest file.
	 *
	 * @access	public
	 * @param	object	$element 	The xml node to process
	 * @param	string	$version	The database connector to use
	 * @return	mixed	Number of queries processed or False on error
	 * @since	1.5
	 */
	function parseSQLFiles($element)
	{
		// Initialize variables
		$queries = array();
		$db =  $this->_db;
		$dbDriver = strtolower($db->name);
		if ($dbDriver == 'mysqli') {
			$dbDriver = 'mysql';
		}
		$dbCharset = ($db->hasUTF()) ? 'utf8' : '';

		if (!is_a($element, 'JSimpleXMLElement')) {
			// The tag does not exist.
			return 0;
		}

		// Get the array of file nodes to process
		$files = $element->children();
		if (count($files) == 0) {
			// No files to process
			return 0;
		}

		// Get the name of the sql file to process
		$sqlfile = '';
		foreach ($files as $file)
		{
			$fCharset = (strtolower($file->attributes('charset')) == 'utf8') ? 'utf8' : '';
			$fDriver  = strtolower($file->attributes('driver'));
			if ($fDriver == 'mysqli') {
				$fDriver = 'mysql';
			}

			if( $fCharset == $dbCharset && $fDriver == $dbDriver) {
				$sqlfile = $file->data();
				// Check that sql files exists before reading. Otherwise raise error for rollback
				if ( !file_exists( $this->installer->getPath('classPath').'/'.$sqlfile ) ) {
					$this->setError(JText::_('SQLFDNEXIST'));
					return false;
				}
				$buffer = file_get_contents($this->installer->getPath('classPath').'/'.$sqlfile);

				// Graceful exit and rollback if read not successful
				if ( $buffer === false ) {
					$this->setError(JText::_('CNREADSQFL'));
					return false;
				}

				// Create an array of queries from the sql file
				jimport('joomla.installer.helper');
				$queries = JInstallerHelper::splitSql($buffer);

				if (count($queries) == 0) {
					// No queries to process
					return 0;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							$this->setError('JInstaller::install: '.JText::_('SQLERR')." ".$db->stderr(true));
							return false;
						}
					}
				}
				
				$path['src'] = $this->installer->getPath('source').'/'.$sqlfile;
				$path['dest']  = $this->installer->getPath('classPath').'/'.$sqlfile;
					
				$this->copyFiles(array ($path), true);
				
			}
		}

		return (int) count($queries);
	}
	
	/**
	 * Copies the installation manifest file to the extension folder in the given client
	 *
	 * @access	public
	 * @param	int		$cid	Where to copy the installfile [optional: defaults to 1 (admin)]
	 * @return	boolean	True on success, False on error
	 * @since	1.5
	 */
	function copyManifest($cid=0)
	{
		// Get the client info
		jimport('joomla.application.helper');
		$client =  JApplicationHelper::getClientInfo($cid);

		$path['src'] = $this->installer->getPath('manifest');
		
		if($this->manifest->attributes('apptype')==3)
			$path['dest']  = $this->installer->getPath('classPath').'/'.$this->installer->get('name').'.xml';
		else
			$path['dest']  = $this->installer->getPath('classPath').'/app_'.$this->installer->get('name').'.xml';
			
		return $this->copyFiles(array ($path), true);
	}
	
	/**
	 * Method to parse through a languages element of the installation manifest and take appropriate
	 * action.
	 *
	 * @access	public
	 * @param	object	$element 	The xml node to process
	 * @param	int		$cid		Application ID of application to install to
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function parseLanguages($element, $cid=0)
	{
		// Initialize variables
		$copyfiles = array ();
		$loadfiles = array();
		// Get the client info
		jimport('joomla.application.helper');
		$client =  JApplicationHelper::getClientInfo($cid);

		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return 0;
		}

		// Get the array of file nodes to process
		$files = $element->children();
		if (count($files) == 0) {
			// No files to process
			return 0;
		}

		/*
		 * Here we set the folder we are going to copy the files to.
		 *
		 * 'languages' Files are copied to JPATH_BASE/language/ folder
		 */
		$destination = $client->path.'/components/com_joomd/language';

		/*
		 * Here we set the folder we are going to copy the files from.
		 *
		 * Does the element have a folder attribute?
		 *
		 * If so this indicates that the files are in a subdirectory of the source
		 * folder and we should append the folder attribute to the source path when
		 * copying files.
		 */
		if ($folder = $element->attributes('folder')) {
			$source = $this->installer->getPath('source').'/'.$folder;
		} else {
			$source = $this->installer->getPath('source');
		}

		// Process each file in the $files array (children of $tagName).
		foreach ($files as $file)
		{
			/*
			 * Language files go in a subfolder based on the language code, ie.
			 *
			 * 		<language tag="en-US">en-US.mycomponent.ini</language>
			 *
			 * would go in the en-US subdirectory of the language folder.
			 *
			 * We will only install language files where a core language pack
			 * already exists.
			 */
			 
			 if ($file->attributes('tag') == '')	{
				$this->setError(JText::_('PLZ_USE_LANGTAG_IN_LANG'));
				return false;
			 }
			 else	{
				$path['src']	= $source.'/'.$file->data();
				$path['dest']	= $destination.'/'.$file->attributes('tag').'/'.basename($file->data());

				// If the language folder is not present, then the core pack hasn't been installed... ignore
				if (!JFolder::exists(dirname($path['dest']))) {
					continue;
				}
			}

			/*
			 * Before we can add a file to the copyfiles array we need to ensure
			 * that the folder we are copying our file to exits and if it doesn't,
			 * we need to create it.
			 */
			if (basename($path['dest']) != $path['dest']) {
				$newdir = dirname($path['dest']);

				if (!JFolder::create($newdir)) {
					$this->setError(JText::_('FTOCREDIR').' "'.$newdir.'"');
					return false;
				}
			}
			
			// Add the file to the copyfiles array
			$copyfiles[] = $path;
			
			//add to load language
			if($client->id = 1 and strstr($path['dest'], '.sys.ini'))
				$loadfiles[] = substr($path['dest'], strrpos($path['dest'], $file->attributes('tag'))+6, -4);
		}
		
		$return = $this->copyFiles($copyfiles);
		
		$lang = JFactory::getLanguage();
		
		foreach($loadfiles as $file)
			$lang->load($file, $client->path.'/components/com_joomd');
		
		return $return;
	}
	
	//method to remove the app language files v 2.3.0
	function removeLanguages($element, $cid=0)
	{

		// Get the client info
		jimport('joomla.application.helper');
		$client =  JApplicationHelper::getClientInfo($cid);

		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return 0;
		}

		// Get the array of file nodes to process
		$files = $element->children();
		if (count($files) == 0) {
			// No files to process
			return 0;
		}

		/*
		 * Here we set the folder we are going to copy the files to.
		 *
		 * 'languages' Files are copied to JPATH_BASE/language/ folder
		 */
		$destination = $client->path.'/components/com_joomd/language';

		/*
		 * Here we set the folder we are going to copy the files from.
		 *
		 * Does the element have a folder attribute?
		 *
		 * If so this indicates that the files are in a subdirectory of the source
		 * folder and we should append the folder attribute to the source path when
		 * copying files.
		 */
		if ($folder = $element->attributes('folder')) {
			$source = $this->installer->getPath('source').'/'.$folder;
		} else {
			$source = $this->installer->getPath('source');
		}

		// Process each file in the $files array (children of $tagName).
		foreach ($files as $file)
		{
			/*
			 * Language files go in a subfolder based on the language code, ie.
			 *
			 * <language tag="en-US">en-US.myapp.ini</language>
			 *
			 * would go in the en-US subdirectory of the language folder.
			 *
			 * We will only install language files where a core language pack
			 * already exists.
			 */
			if ($file->attributes('tag') != '') {
				$path['dest']	= $destination.'/'.$file->attributes('tag').'/'.basename($file->data());

				// If the language folder is not present, then the core pack hasn't been installed... ignore
				if (!JFolder::exists(dirname($path['dest']))) {
					continue;
				}
			} else {
				$path['dest']	= $destination.'/'.$file->data();
			}

			if(is_file($path['dest']) and basename($path['dest']) <> $path['dest'])
				JFile::delete($path['dest']);
			
		}

		return true;
	}
	
	/**
	 * Unpacks a file and verifies it as a Joomla element package
	 * Supports .gz .tar .tar.gz and .zip
	 *
	 * @static
	 * @param string $p_filename The uploaded package filename or install directory
	 * @return Array Two elements - extractdir and packagefile
	 * @since 1.5
	 */
	function unpack($p_filename)
	{
		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($p_filename).'/'.$tmpdir);
		$archivename = JPath::clean($archivename);

		// do the unpacking of the archive
		$result = JArchive::extract( $archivename, $extractdir);

		if ( $result === false ) {
			return false;
		}


		/*
		 * Lets set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory.  In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory.  If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */
		$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));

		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir.'/'.$dirList[0]))
			{
				$extractdir = JPath::clean($extractdir.'/'.$dirList[0]);
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval['dir'] = $extractdir;

		/*
		 * Get the extension type and return the directory/type array on success or
		 * false on fail.
		 */
		 
		 
		if ($retval['type'] = $this->detectType($extractdir))
		{
			return $retval;
		} else
		{
			return false;
		}
	}
	
	/**
	 * Method to detect the extension type from a package directory
	 *
	 * @param   string  $p_dir  Path to package directory
	 *
	 * @return  mixed  Extension type string or boolean false on fail
	 *
	 * @since   1.6
	 */
	public function detectType($p_dir)
	{
		// Search the install dir for an XML file
		$files = JFolder::files($p_dir, '\.xml$', 1, true);

		if (!count($files))
		{
			$this->setError(JText::_('ERRORNOTFINDXMLSETUPFILE'));
			return false;
		}

		foreach ($files as $file)
		{
			if (!$xml = JFactory::getXML($file))
			{
				continue;
			}

			if ($xml->getName() != 'install' && $xml->getName() != 'extension')
			{
				unset($xml);
				continue;
			}

			$type = (string) $xml->attributes()->type;
			// Free up memory
			unset($xml);
			return $type;
		}

		$this->setError(JText::_('ERRORNOTFINDXMLSETUPFILE'));
		// Free up memory.
		unset($xml);
		return false;
	}
	
}

?>