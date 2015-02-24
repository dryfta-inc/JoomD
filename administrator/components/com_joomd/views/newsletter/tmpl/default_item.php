<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/index.php?option=com_ccboard&view=forumlist&Itemid=63
-----------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$delete    = JHTML::_( 'jdgrid.delete', $i );
$checked    = JHTML::_( 'jdgrid.id', $i, $row->id );

?>
<tr id="order_<?php echo $i; ?>" class="<?php echo "row$k"; ?>">
	<td align="center"><?php echo $i+1; ?></td>
	<td align="center"><?php echo $checked; ?></td>
	<td><?php echo $row->email; ?></td>
	<td align="center"><?php echo $row->type; ?></td>
    <td align="center"><?php echo $row->cat; ?></td>
    <td align="center"><?php echo $delete; ?></td>
	<td><?php echo $row->id; ?></td>
</tr>
