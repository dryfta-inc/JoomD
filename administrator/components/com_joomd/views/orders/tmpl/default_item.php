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

defined('_JEXEC') or die('Restricted access');

$link = 'index.php?option=com_joomd&view=orders&layout=form&cid[]='. $row->id;

?>
<tr id="order_<?php echo $i; ?>" class="<?php echo "row$k"; ?>">
	<td align="center"><?php echo $i+1; ?></td>
	<td><a href="<?php echo $link; ?>"><?php echo $row->username; ?></a></td>
	<td ><?php	echo $row->pname;	?></td>
	<td align="center"><?php echo $row->order_number; ?></td>
	<td align="center"><?php echo $row->payment_price; ?></td>
	<td align="center"><?php if($row->order_status=="c") echo JText::_('CONFIRM'); else echo JText::_('PENDING'); ?></td>
	<td><?php echo $row->id; ?></td>
 </tr>