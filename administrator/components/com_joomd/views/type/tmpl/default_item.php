<?php


$checked    = JHTML::_( 'jdgrid.id', $i, $row->id );

$published    = JHTML::_( 'jdgrid.published', $row, $i );
$delete    = JHTML::_( 'jdgrid.delete', $i );
$edit    = JHTML::_( 'jdgrid.edit', $i );
$link = 'index.php?option=com_joomd&view=type&layout=form&cid[]='. $row->id;

?>
<tr id="order_<?php echo $i; ?>" class="<?php echo "row$k"; ?>">
	<td align="center" class="sort_handle_s"><?php echo $i+1; ?></td>
	<td align="center"><?php echo $checked; ?></td>
	<td><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
	<td align="center"><?php echo JText::_($row->app); ?></td>
	<td style="padding-left:3%;"><input type="text" name="ordering[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align:center; vertical-align:top;" <?php echo $disabled; ?> /> <?php echo $edit; ?> <?php echo $published; ?> <?php echo $delete; ?></td>
	<td class="sort_handle_l"><?php echo $row->id; ?></td>
</tr>
