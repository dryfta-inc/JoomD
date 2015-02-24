<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

define('JD_MAX', 3);
define('JD_WAIT', 45);

jimport('joomla.event.plugin');

class plgSystemJoomdcaptcha extends JPlugin
{
	
	function plgSystemJoomdcaptcha(&$subject, $config) {

		parent::__construct($subject, $config);
	}
	
	// Function wrappers for TriggerEvent usage
	function onJDCaptchaDisplay()
	{
		$session =  JFactory::getSession();
		$plugin =  JPluginHelper::getPlugin('system', 'joomdcaptcha');
		
		$params = $this->params;
		
		$txtcolor = $params->get('txtcolor', null);
		$bgcolor = $params->get('bgcolor', null);
		
		if (!empty($txtcolor))
		{
			$txtcolor = explode(',', $txtcolor);
		}
		if (!empty($bgcolor))
		{
			$bgcolor = explode(',', $bgcolor);
		}
       	
		require_once(JPATH_PLUGINS.DS.'system'.DS.'joomdcaptcha'.DS.'joomdcaptcha.class.php');
	
		$joomdcap = new Joomdcaptcha($params->get('length', 4), $params->get('size', 20));

		$session->set( 'joomdcapt_uid', md5($joomdcap->get()) );
		
		$joomdcap->draw($txtcolor, $bgcolor);
		exit();
	}

	function onCaptchaCheck($word, &$return)
	{
		// $return = false if wrong; true if correct; int if many attempts 
		$mainframe =  JFactory::getApplication();

		$session =  JFactory::getSession();
		
		$tries = $session->get('joomdcapt_att', 1);
		$lasttry = $session->get('joomdcapt_tim', 0);
		
		$now = time();

		if ($tries > JD_MAX)		// many attempts
		{
			if ( ($diff = JD_WAIT - ($now - $lasttry)) > 0 )		// if so fast
			{
				$return = (int)$diff;
				return false;
			}
			else				// continue if sleep enough
			{
				$tries = 0;
			}
		}

		$session->set('joomdcapt_att', ++$tries);
		$session->set('joomdcapt_tim', $now);
		
		if (empty($word))
		{
			$return = false;
		}
		else
		{
	  		$correct = $session->get('joomdcapt_uid');
	  		
	  		if (md5($word) == $correct)
			{
	  			$session->set('joomdcapt_att', 1);
	  			$return = true;
	  		}
			else
			{
				$return = false;
			}
		}

		return $return;
	}
    
	function check($word)
	{
		$mainframe =  JFactory::getApplication();
		$return = false;
		$mainframe->triggerEvent('onCaptchaCheck', array($word, &$return));
		
		// parse language
		if ($return === false)
		{
            $return = JText::_('TIN_WRONG');
        }
        else if (is_int($return))
        {
            $return = sprintf( JText::_('TIN_MAX_ATTEMPTS'), $return );
		}

		return $return;
	}
}
