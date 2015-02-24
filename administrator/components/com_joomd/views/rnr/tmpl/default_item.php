<?php


$field = new JoomdFields($row->typeid);
		
$firstfield = $field->get_firstfield();

$checked    = JHTML::_( 'jdgrid.id', $i, $row->id );

$published    = JHTML::_( 'jdgrid.published', $row, $i );
$delete    = JHTML::_( 'jdgrid.delete', $i );
$edit    = JHTML::_( 'jdgrid.edit', $i );
$link = 'index.php?option=com_joomd&view=rnr&layout=form&cid[]='. $row->id;

?>
<tr id="order_<?php echo $i; ?>" class="<?php echo "row$k"; ?>">
	<td align="center" class="sort_handle_s"><?php echo $i+1; ?></td>
	<td align="center"><?php echo $checked; ?></td>
	<td><a href="<?php echo $link; ?>"><?php echo $row->user; ?></a></td>
	<td align="center"><?php echo $row->type;	?></td>
    <td align="center"><?php echo $field->displayfieldvalue($row->itemid, $firstfield->id);	?></td>
    <td align="center"><?php echo $row->rate;	?></td>
	<td style="padding-left:3%;"><?php echo $edit; ?> <?php echo $published; ?> <?php echo $delete; ?></td>
	<td class="sort_handle_l"><?php echo $row->id; ?></td>
</tr>
