<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Mohammad arshi - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$checked    = JHTML::_( 'jdgrid.id', $i, $row->id );
$published    = JHTML::_( 'jdgrid.published', $row, $i );
$delete    = JHTML::_( 'jdgrid.delete', $i );
$edit    = JHTML::_( 'jdgrid.edit', $i );

$link = 'index.php?option=com_joomd&view=packages&layout=form&cid[]='.$row->id;

?>
<tr id="order_<?php echo $i; ?>" class="<?php echo "row$k"; ?>">
	<td align="center"><?php echo $i+1; ?></td>
	<td align="center"><?php echo $checked; ?></td>
	<td><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
	<td align="center">
		<?php
			echo $row->period.' ';
			switch($row->unit)
			{
				case 'D':
				echo JText::_('DAYS');
				break;
				
				case 'W':
				echo JText::_('WEEKS');
				break;
				
				case 'M':
				echo JText::_('MONTHS');
				break;
				
				case 'Y':
				echo JText::_('YEARS');
				break;
			}
		?>
	</td>
	<td align="center"><?php echo $row->items; ?></td>
	<td align="center"><input type="text" name="ordering[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center; vertical-align:top;" <?php echo $disabled; ?> /> <?php echo $edit; ?> <?php echo $published; ?> <?php echo $delete; ?></td>
	<td><?php echo $row->id; ?></td>
 </tr>