<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $id$
 * @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.controller');

/**
 * The Backup Administrator class
 *
 */
class AkeebaControllerBuadmin extends JController
{
	public function  __construct($config = array()) {
		parent::__construct($config);
		if(AKEEBA_JVERSION=='16')
		{
			// Access check, Joomla! 1.6 style.
			$user = JFactory::getUser();
			if (!$user->authorise('akeeba.download', 'com_akeeba')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				$this->redirect();
			}
		} else {
			// Custom ACL for Joomla! 1.5
			$aclModel = JModel::getInstance('Acl','AkeebaModel');
			if(!$aclModel->authorizeUser('download')) {
				$this->setRedirect('index.php?option=com_akeeba');
				return JError::raiseWarning(403, JText::_('Access Forbidden'));
				$this->redirect();
			}
		}
		
		$base_path = JPATH_COMPONENT_ADMINISTRATOR.'/plugins';
		$model_path = $base_path.'/models';
		$view_path = $base_path.'/views';
		$this->addModelPath($model_path);
		$this->addViewPath($view_path);
	}

	/**
	 * Show a list of backup attempts
	 *
	 */
	public function display()
	{
		$session = JFactory::getSession();
		$session->set('buadmin.task', 'default', 'akeeba');
		
		parent::display();
	}
	
	public function restorepoint()
	{
		if(!AKEEBA_PRO) {
			JError::raiseError('403',JText::_('AKEEBA_POSTSETUP_NOTAVAILABLEINCORE'));
			return false;
		}
		
		$session = JFactory::getSession();
		$session->set('buadmin.task', 'restorepoint', 'akeeba');
		
		JRequest::setVar('layout','restorepoint');
		
		parent::display();
	}

	/**
	 * Downloads the backup file of a specific backup attempt,
	 * if it's available
	 *
	 */
	public function download()
	{
		$cid = JRequest::getVar('cid',array(),'default','array');
		$id = JRequest::getInt('id');
		$part = JRequest::getInt('part',-1);

		if(empty($id))
		{
			if(is_array($cid) && !empty($cid))
			{
				$id = $cid[0];
			}
			else
			{
				$id = -1;
			}
		}

		if($id <= 0)
		{
			$session = JFactory::getSession();
			$task = $session->get('buadmin.task', 'default', 'akeeba');
			
			$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
			parent::display();
			return;
		}

		$stat = AEPlatform::getInstance()->get_statistics($id);
		$allFilenames = AEUtilStatistics::get_all_filenames($stat);

		// Check single part files
		if( (count($allFilenames) == 1) && ($part == -1) )
		{
			$filename = array_shift($allFilenames);
		}
		elseif( (count($allFilenames) > 0) && (count($allFilenames) > $part) && ($part >= 0) )
		{
			$filename = $allFilenames[$part];
		}
		else
		{
			$filename = null;
		}

		if(is_null($filename) || empty($filename) || !@file_exists($filename) )
		{
			$session = JFactory::getSession();
			$task = $session->get('buadmin.task', 'default', 'akeeba');
			
			$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDDOWNLOAD'), 'error');
			parent::display();
			return;
		}
		else
		{
			// For a certain unmentionable browser -- Thank you, Nooku, for the tip
			if(function_exists('ini_get') && function_exists('ini_set')) {
				if(ini_get('zlib.output_compression')) {
					ini_set('zlib.output_compression', 'Off');
				}
			}
			
			// Remove php's time limit -- Thank you, Nooku, for the tip
			if(function_exists('ini_get') && function_exists('set_time_limit')) {
				if(!ini_get('safe_mode') ) {
				    @set_time_limit(0);
		        }
			}
			
			$basename = @basename($filename);
			$filesize = @filesize($filename);
			$extension = strtolower(str_replace(".", "", strrchr($filename, ".")));

			while (@ob_end_clean());
			@clearstatcache();
			// Send MIME headers
			header('MIME-Version: 1.0');
			header('Content-Disposition: attachment; filename='.$basename);
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			
			switch($extension)
			{
				case 'zip':
					// ZIP MIME type
					header('Content-Type: application/zip');
					break;

				default:
					// Generic binary data MIME type
					header('Content-Type: application/octet-stream');
					break;
			}
			// Notify of filesize, if this info is available
			if($filesize > 0) header('Content-Length: '.@filesize($filename));
			// Disable caching
	        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	        header("Expires: 0");
			header('Pragma: no-cache');
			flush();
			if($filesize > 0)
			{
				// If the filesize is reported, use 1M chunks for echoing the data to the browser
				$blocksize = 1048756; //1M chunks
				$handle    = @fopen($filename, "r");
				// Now we need to loop through the file and echo out chunks of file data
				if($handle !== false) while(!@feof($handle)){
				    echo @fread($handle, $blocksize);
				    @ob_flush();
					flush();
				}
				if($handle !== false) @fclose($handle);
			} else {
				// If the filesize is not reported, hope that readfile works
				@readfile($filename);
			}
			exit(0);
		}

	}

	/**
	 * Deletes one or several backup statistics records and their associated backup files
	 */
	public function remove()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
		}
		
		$cid = JRequest::getVar('cid',array(),'default','array');
		$id = JRequest::getInt('id');
		if(empty($id))
		{
			if(!empty($cid) && is_array($cid))
			{
				foreach ($cid as $id)
				{
					$session = JFactory::getSession();
					$task = $session->get('buadmin.task', 'default', 'akeeba');
					$result = $this->_remove($id);
					if(!$result) $this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
				}
			}
			else
			{
				$session = JFactory::getSession();
				$task = $session->get('buadmin.task', 'default', 'akeeba');
				$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
				return;
			}
		}
		else
		{
			$result = $this->_remove($id);
			$session = JFactory::getSession();
			$task = $session->get('buadmin.task', 'default', 'akeeba');
			if(!$result) $this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
		}

		$session = JFactory::getSession();
		$task = $session->get('buadmin.task', 'default', 'akeeba');
		$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_MSG_DELETED'));

		parent::display();
	}

	/**
	 * Deletes backup files associated to one or several backup statistics records
	 */
	public function deletefiles()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
		}
		
		$cid = JRequest::getVar('cid',array(),'default','array');
		$id = JRequest::getInt('id');
		$session = JFactory::getSession();
		$task = $session->get('buadmin.task', 'default', 'akeeba');
		if(empty($id))
		{
			if(!empty($cid) && is_array($cid))
			{
				foreach ($cid as $id)
				{
					$result = $this->_removeFiles($id);
					if(!$result) $this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
				}
			}
			else
			{
				$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
				return;
			}
		}
		else
		{
			$result = $this->_remove($id);
			if(!$result) $this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
		}

		$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_MSG_DELETEDFILE'));

		parent::display();
	}

	/**
	 * Removes the backup file linked to a statistics entry and the entry itself
	 *
	 * @return bool True on success
	 */
	private function _remove($id)
	{
		$session = JFactory::getSession();
		$task = $session->get('buadmin.task', 'default', 'akeeba');
		
		if($id <= 0)
		{
			$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
			return;
		}

		$model =& $this->getModel('statistics');
		return $model->delete($id);
	}

	/**
	 * Removes only the backup file linked to a statistics entry
	 *
	 * @return bool True on success
	 */
	private function _removeFiles($id)
	{
		$session = JFactory::getSession();
		$task = $session->get('buadmin.task', 'default', 'akeeba');
		if($id <= 0)
		{
			$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
			return;
		}

		$model =& $this->getModel('statistics');
		return $model->deleteFile($id);
	}

	public function showcomment()
	{
		$cid = JRequest::getVar('cid',array(),'default','array');
		$id = JRequest::getInt('id');
		$session = JFactory::getSession();
		$task = $session->get('buadmin.task', 'default', 'akeeba');

		if(empty($id))
		{
			if(is_array($cid) && !empty($cid))
			{
				$id = $cid[0];
			}
			else
			{
				$id = -1;
			}
		}

		if($id <= 0)
		{
			$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, JText::_('STATS_ERROR_INVALIDID'), 'error');
			parent::display();
			return;
		}

		JRequest::setVar('id', $id);

		parent::display();
	}

	/**
	 * Save an edited backup record
	 */
	public function save()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
		}
		
		$id = JRequest::getInt('id');
		$description = JRequest::getString('description');
		$comment = JRequest::getVar('comment',null,'default','string',4);

		$statistic = AEPlatform::getInstance()->get_statistics(JRequest::getInt('id'));
		$statistic['description']	= $description;
		$statistic['comment']		= $comment;
		AEPlatform::getInstance()->set_or_update_statistics(JRequest::getInt('id'),$statistic,$self);

		if( !$this->getError() ) {
			$message = JText::_('STATS_LOG_SAVEDOK');
			$type = 'message';
		} else {
			$message = JText::_('STATS_LOG_SAVEERROR');
			$type = 'error';
		}
		$session = JFactory::getSession();
		$task = $session->get('buadmin.task', 'default', 'akeeba');
		$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task, $message, $type);
	}

	public function restore()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' : 'Request Forbidden'));
		}
		
		$id = null;
		$cid = JRequest::getVar('cid', array(), 'default', 'array');
		if(!empty($cid))
		{
			$id = intval($cid[0]);
			if($id <= 0) $id = null;
		}
		if(empty($id)) $id = JRequest::getInt('id', -1);
		if($id <= 0) $id = null;

		$url = JURI::base().'index.php?option=com_akeeba&view=restore&id='.$id;
		$this->setRedirect($url);
		$this->redirect();
		return;
	}
	
	public function cancel()
	{
		$session = JFactory::getSession();
		$task = $session->get('buadmin.task', 'default', 'akeeba');
		$this->setRedirect(JURI::base().'index.php?option=com_akeeba&view=buadmin&task='.$task);
	}
}