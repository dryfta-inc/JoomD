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

class AkeebaViewBackup extends JView
{
	/**
	 * This mess of a code is probably not one of my highlights in my code
	 * writing career. It's logically organized, badly architectured but I can
	 * still maintain it - and it works!
	 */
	function display()
	{
		// Add some buttons
		JToolBarHelper::back('AKEEBA_CONTROLPANEL', 'index.php?option='.JRequest::getCmd('option'));
		JToolBarHelper::spacer();

		// Load the Status Helper
		if(!class_exists('AkeebaHelperStatus')) JLoader::import('helpers.status', JPATH_COMPONENT_ADMINISTRATOR);
		$helper =& AkeebaHelperStatus::getInstance();

		// Determine default description
		jimport('joomla.utilities.date');
		$jregistry =& JFactory::getConfig();
		$tzDefault = $jregistry->getValue('config.offset');
		$user =& JFactory::getUser();
		$tz = $user->getParam('timezone', $tzDefault);
		if( AKEEBA_JVERSION == '16' ) {
			$dateNow = new JDate('now', $tz);
			$backup_description = JText::_('BACKUP_DEFAULT_DESCRIPTION').' '.$dateNow->format(JText::_('DATE_FORMAT_LC2'), true);
		} else {
			$dateNow = new JDate();
			$dateNow->setOffset($tz);
			$backup_description = JText::_('BACKUP_DEFAULT_DESCRIPTION').' '.$dateNow->toFormat(JText::_('DATE_FORMAT_LC2'));
		}
		$backup_description = AkeebaHelperEscape::escapeJS($backup_description,"'");

		$default_description = $backup_description;
		$backup_description = JRequest::getVar('description', $default_description);

		$comment = JRequest::getVar('comment', '', 'default', 'none', 2);

		// Get a potential return URL
		$returnurl = JRequest::getVar('returnurl',null);
		if(empty($returnurl)) $returnurl = '';

		// If a return URL is set *and* the profile's name is "Site Transfer
		// Wizard", we are running the Site Transfer Wizard
		if(!class_exists('AkeebaModelProfiles')) JLoader::import('models.profiles', JPATH_COMPONENT_ADMINISTRATOR);
		if(!class_exists('AkeebaModelCpanel')) JLoader::import('models.cpanel', JPATH_COMPONENT_ADMINISTRATOR);
		$cpanelmodel = new AkeebaModelCpanel();
		$profilemodel = new AkeebaModelProfiles();
		$profilemodel->setId($cpanelmodel->getProfileID());
		$profile_data = $profilemodel->getProfile();
		$isSTW = ($profile_data->description == 'Site Transfer Wizard (do not rename)') &&
			!empty($returnurl);
		$this->assign('isSTW', $isSTW);
		
		// Get the domain details from scripting facility
		$registry =& AEFactory::getConfiguration();
		$script = $registry->get('akeeba.basic.backup_type','full');
		$scripting = AEUtilScripting::loadScripting();
		$domains = array();
		if(!empty($scripting)) foreach( $scripting['scripts'][$script]['chain'] as $domain )
		{
			$description = JText::_($scripting['domains'][$domain]['text']);
			$domain_key = $scripting['domains'][$domain]['domain'];
			if( $isSTW && ($domain_key == 'Packing') ) {
				$description = JText::_('BACKUP_LABEL_DOMAIN_PACKING_STW');
			}
			$domains[] = array($domain_key, $description);
		}
		$json_domains = AkeebaHelperEscape::escapeJS(json_encode($domains),'"\\');

		// Get the maximum execution time and bias
		$maxexec = $registry->get('akeeba.tuning.max_exec_time',14) * 1000;
		$bias = $registry->get('akeeba.tuning.run_time_bias',75);
		
		// Check if the output directory is writable
		$quirks = AEUtilQuirks::get_quirks();
		$unwritableOutput = array_key_exists('001', $quirks);

		// Pass on data
		$this->assign('haserrors', !$helper->status);
		$this->assign('hasquirks', $helper->hasQuirks());
		$this->assign('quirks', $helper->getQuirksCell(!$helper->status));
		$this->assign('description', $backup_description);
		$this->assign('comment', $comment);
		$this->assign('domains', $json_domains);
		$this->assign('maxexec', $maxexec);
		$this->assign('bias', $bias);
		$this->assign('useiframe', $registry->get('akeeba.basic.useiframe',0) ? 'true' : 'false');
		$this->assign('returnurl', $returnurl);
		$this->assign('unwritableoutput', $unwritableOutput);
		if($registry->get('akeeba.advanced.archiver_engine','jpa') == 'jps')
		{
			$this->assign('showjpskey', 1);
			$this->assign('jpskey', $registry->get('engine.archiver.jps.key',''));
		}
		else
		{
			$this->assign('showjpskey', 0);
		}
		$this->assign('autostart', JRequest::getInt('autostart',0));

		// Pass on profile info
		$this->assign('profileid', $cpanelmodel->getProfileID()); // Active profile ID
		$this->assign('profilelist', $cpanelmodel->getProfilesList()); // List of available profiles
		
		// Pass on state information pertaining to SRP
		$srpinfo = array(
			'tag'				=> JRequest::getCmd('tag','backend'),
			'type'				=> JRequest::getCmd('type',''),
			'name'				=> JRequest::getCmd('name',''),
			'group'				=> JRequest::getCmd('group',''),
			'customdirs'		=> JRequest::getVar('customdirs',array(),'default','array',2),
			'extraprefixes'		=> JRequest::getVar('extraprefixes',array(),'default','array',2),
			'customtables'		=> JRequest::getVar('customtables',array(),'default','array',2),
			'skiptables'		=> JRequest::getVar('skiptables',array(),'default','array',2),
			'xmlname'			=> JRequest::getString('xmlname','')
		);
		$this->assign('srpinfo',	$srpinfo);

		// Add references to CSS and JS files
		AkeebaHelperIncludes::includeMedia(false);

		// Add live help
		AkeebaHelperIncludes::addHelp();
		
		// Set the toolbar title
		if($srpinfo['tag'] == 'restorepoint') {
			$subtitle = JText::_('AKEEBASRP');
		} elseif($isSTW) {
			$subtitle = JText::_('SITETRANSFERWIZARD');
		} else {
			$subtitle = JText::_('BACKUP');
		}
		JToolBarHelper::title(JText::_('AKEEBA').':: <small>'.$subtitle.'</small>','akeeba');

		parent::display(JRequest::getCmd('tpl',null));
	}
}