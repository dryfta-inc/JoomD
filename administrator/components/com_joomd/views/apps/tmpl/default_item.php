<?php

$checked    = JHTML::_( 'jdgrid.id', $i, $row->id );
$edit    = JHTML::_( 'jdgrid.edit', $i );
$link = 'index.php?option=com_joomd&view=apps&layout=form&cid[]='. $row->id;

?>
<tr id="order_<?php echo $i; ?>" class="<?php echo "row$k"; ?>">
	<td align="center" class="sort_handle_s"><?php echo $i+1; ?></td>
	<td align="center"><?php echo $checked; ?></td>
	<td><a href="<?php echo $link; ?>"><?php echo JText::_($row->label); ?></a></td>
    <td align="center"><?php echo JText::_($row->type); ?></td>
	<td style="padding-left:3%;"><input type="text" name="ordering[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align:center; vertical-align:top;" <?php echo $disabled; ?> /> <?php echo $edit; ?></td>
	<td class="sort_handle_l"><?php echo $row->id; ?></td>
</tr>
