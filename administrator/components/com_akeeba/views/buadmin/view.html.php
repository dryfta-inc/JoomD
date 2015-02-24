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

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * Akeeba Backup Administrator view class
 *
 */
class AkeebaViewBuadmin extends JView
{
	protected $lists = null;

	function  __construct($config = array()) {
		parent::__construct($config);
		$this->lists = new JObject();
        
        $tmpl_path = JPATH_COMPONENT_ADMINISTRATOR.'/plugins/views/buadmin/tmpl';
		$this->addTemplatePath($tmpl_path);
	}
    
	public function display()
	{
		$task = JRequest::getCmd('task','default');

		switch($task)
		{
			case 'showcomment':
				JToolBarHelper::title(JText::_('AKEEBA').': <small>'.JText::_('BUADMIN').'</small>','akeeba');
				JToolBarHelper::back('AKEEBA_CONTROLPANEL', 'index.php?option='.JRequest::getCmd('option'));
				JToolBarHelper::save();
				JToolBarHelper::cancel();
				$document =& JFactory::getDocument();
				$document->addStyleSheet(JURI::base().'../media/com_akeeba/theme/akeebaui.css?'.AKEEBAMEDIATAG);

				$id = JRequest::getInt('id',0);
				$record = AEPlatform::getInstance()->get_statistics($id);
				$this->assign('record', $record);
				$this->assign('record_id', $id);

				JRequest::setVar('tpl','comment');
				break;

			default:
				$registry =& AEFactory::getConfiguration();

				if($task == 'default') {
					JToolBarHelper::title(JText::_('AKEEBA').': <small>'.JText::_('BUADMIN').'</small>','akeeba');
				} else {
					JToolBarHelper::title(JText::_('AKEEBA').': <small>'.JText::_('BUADMINSRP').'</small>','akeeba');
				}

				JToolBarHelper::back('AKEEBA_CONTROLPANEL', 'index.php?option='.JRequest::getCmd('option'));
				JToolBarHelper::spacer();
				JToolBarHelper::deleteList();
				JToolBarHelper::custom( 'deletefiles', 'delete.png', 'delete_f2.png', JText::_('STATS_LABEL_DELETEFILES'), true );

				// Add custom submenus
				if(AKEEBA_PRO) {
					JSubMenuHelper::addEntry(
						JText::_('BUADMIN_LABEL_BACKUPS'),
						JURI::base().'index.php?option=com_akeeba&view='.JRequest::getCmd('view').'&task=default',
						($task == 'default')
					);
					JSubMenuHelper::addEntry(
						JText::_('BUADMIN_LABEL_SRP'),
						JURI::base().'index.php?option=com_akeeba&view='.JRequest::getCmd('view').'&task=restorepoint',
						($task == 'restorepoint')
					);
				}
				
				if(AKEEBA_PRO && ($task == 'default'))
				{
					$bar = & JToolBar::getInstance('toolbar');
					$bar->appendButton( 'Link', 'restore', JText::_('DISCOVER'), 'index.php?option=com_akeeba&view=discover' );
					JToolBarHelper::publish('restore', JText::_('STATS_LABEL_RESTORE'));
				}

				if(($task == 'default')) {
					JToolBarHelper::editList('showcomment', JText::_('STATS_LOG_EDITCOMMENT'));
					
					$pModel = JModel::getInstance('Profiles','AkeebaModel');
					$enginesPerPprofile = $pModel->getPostProcessingEnginePerProfile();
					$this->assign('enginesPerProfile', $enginesPerPprofile);
				}
				JToolBarHelper::spacer();

				// "Show warning first" download button. Joomlantastic!
				$confirmationText = AkeebaHelperEscape::escapeJS( JText::_('STATS_LOG_DOWNLOAD_CONFIRM'), "'\n" );
				$baseURI = JURI::base();
				$js = <<<ENDSCRIPT
function confirmDownloadButton()
{
	var answer = confirm('$confirmationText');
	if(answer) submitbutton('download');
}

function confirmDownload(id, part)
{
	var answer = confirm('$confirmationText');
	var newURL = '$baseURI';
	if(answer) {
		newURL += 'index.php?option=com_akeeba&view=buadmin&task=download&id='+id;
		if( part != '' ) newURL += '&part=' + part
		window.location = newURL;
	}
}

ENDSCRIPT;

				$document =& JFactory::getDocument();
				$document->addScriptDeclaration($js);				
				$document->addStyleSheet(JURI::base().'../media/com_akeeba/theme/akeebaui.css?'.AKEEBAMEDIATAG);
				
				$hash = 'akeebabuadmin';
		
				// ...ordering
				$app = JFactory::getApplication();
				$this->lists->set('order',			$app->getUserStateFromRequest($hash.'filter_order',
					'filter_order', 'backupstart'));
				$this->lists->set('order_Dir',		$app->getUserStateFromRequest($hash.'filter_order_Dir',
					'filter_order_Dir', 'DESC'));
				
				// ...filter state
				$this->lists->set('fltDescription',	$app->getUserStateFromRequest($hash.'filter_description',
					'description', null));
				$this->lists->set('fltFrom',		$app->getUserStateFromRequest($hash.'filter_from',
					'from', null));
				$this->lists->set('fltTo',			$app->getUserStateFromRequest($hash.'filter_to',
					'to', null));
				$this->lists->set('fltOrigin',		$app->getUserStateFromRequest($hash.'filter_origin',
					'origin', null));
				$this->lists->set('fltProfile',		$app->getUserStateFromRequest($hash.'filter_profile',
					'profile', null));
				
				$filters = $this->_getFilters();
				$ordering = $this->_getOrdering();

				require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/statistics.php';
				$model = new AkeebaModelStatistics();
				$list =& $model->getStatisticsListWithMeta(false, $filters, $ordering);

				// Assign data to the view
				$this->assignRef( 'lists',		$this->lists); // Filter lists
				$this->assignRef( 'list',		$list); // Data
				$this->assignRef( 'pagination',	$model->getPagination($filters)); // Pagination object
				break;
		}

		// Add live help
		AkeebaHelperIncludes::addHelp();

		parent::display(JRequest::getVar('tpl'));
	}
	
	private function _getFilters()
	{
		$filters = array();
		
		if($this->lists->fltDescription) {
			$filters[] = array(
				'field'			=> 'description',
				'operand'		=> 'LIKE',
				'value'			=> $this->lists->fltDescription
			);
		}

		if($this->lists->fltFrom && $this->lists->fltTo) {
			$filters[] = array(
				'field'			=> 'backupstart',
				'operand'		=> 'BETWEEN',
				'value'			=> $this->lists->fltFrom,
				'value2'			=> $this->lists->fltTo
			);
		} elseif ($this->lists->fltFrom) {
			$filters[] = array(
				'field'			=> 'backupstart',
				'operand'		=> '>=',
				'value'			=> $this->lists->fltFrom,
			);
		} elseif($this->lists->fltTo) {
			jimport('joomla.utilities.date');
			$to = new JDate($this->lists->fltTo);
			$toUnix = $to->toUnix();
			$to = date('Y-m-d').' 23:59:59';
			
			$filters[] = array(
				'field'			=> 'backupstart',
				'operand'		=> '<=',
				'value'			=> $to,
			);
		}
		if($this->lists->fltOrigin) {
			$filters[] = array(
				'field'			=> 'origin',
				'operand'		=> '=',
				'value'			=> $this->lists->fltOrigin
			);
		}
		if($this->lists->fltProfile) {
			$filters[] = array(
				'field'			=> 'profile_id',
				'operand'		=> '=',
				'value'			=> (int)$this->lists->fltProfile
			);
		}
		
		$task = JRequest::getCmd('task','default');
		if($task == 'restorepoint') {
			$filters[] = array(
				'field'			=> 'tag',
				'operand'		=> '=',
				'value'			=> 'restorepoint'
			);
		} else {
			$filters[] = array(
				'field'			=> 'tag',
				'operand'		=> '<>',
				'value'			=> 'restorepoint'
			);
		}
		
		
		if(empty($filters)) $filters = null;
		return $filters;
	}
	
	private function _getOrdering()
	{
		$order = array(
			'by'		=> $this->lists->order,
			'order'		=> strtoupper($this->lists->order_Dir)
		);
		return $order;
	}
}