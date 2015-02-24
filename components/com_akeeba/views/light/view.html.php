<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 2, or later
 * @version $Id: view.html.php 409 2011-01-24 09:30:22Z nikosdion $
 * @since 2.1
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class AkeebaViewLight extends JView
{
	public function display()
	{
		parent::display(JRequest::getCmd('tpl',null));
	}
}
