<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2011 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/index.php?option=com_ccboard&view=forumlist&Itemid=63
-----------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->multiselect->initialize('select', array('minWidth'=>150));

?>

<div id="joomdpanel">

<?php

	echo $this->tabs->startPane('configtab', array('cookie'=>true));
	
	echo $this->tabs->startPanel('subs', JText::_('SUBSCRIBERS'));
	
	require(dirname(__FILE__).DS.'subs.php');
	
	echo $this->tabs->endPanel();
	
	echo $this->tabs->startPanel('newstemp', JText::_('TEMPLATES'));
	
	require(dirname(__FILE__).DS.'newstemp.php');
	
	echo $this->tabs->endPanel();
	
	echo $this->tabs->endPane();
	
?>


<div class="clr"></div>

</div>
