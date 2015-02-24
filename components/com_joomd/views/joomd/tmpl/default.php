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


// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>


<div id="joomdpanel<?php echo $this->params->def('pageclass_sfx'); ?>">

<?php	for($i=0;$i<count($this->blocks);$i++) :	?>

<div class="front_block <?php echo $this->blocks[$i]->cssclass; ?>">
	<?php echo $this->blocks[$i]->html; ?>
</div>

<?php	endfor;	?>

</div>