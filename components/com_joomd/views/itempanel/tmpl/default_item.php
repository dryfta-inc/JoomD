<?php

$canEdit = Joomd::canEdit($row);
$canDelete = Joomd::canDelete($row);
$canState = Joomd::canState($row);
$canFeature = Joomd::canFeature($row);

$checked    = JHTML::_( 'jdgrid.id', $i, $row->id );
$delete    = JHTML::_( 'jdgrid.delete', $i, $canDelete===false );
$edit    = JHTML::_( 'jdgrid.edit', $i, $canEdit===false);
$featured	= JHTML::_( 'jdgrid.featured', $row, $i, $canFeature===false);

if($canState)
	$published = JHTML::_( 'jdgrid.published', $row, $i );
else	{
	
	$img = $row->published?'tick.png':'publish_x.png';
	$alt = $row->published?JText::_('PUBLISHED'):JText::_('UNPUBLISHED');
	
	$published = '<img src="'.JURI::root().'components/com_joomd/assets/images/'.$img.'" alt="'.$alt.'" title="'.$alt.'" />';
}

$link = JRoute::_('index.php?option=com_joomd&view=itempanel&layout=form&typeid='.$row->typeid.'&cid[]='.$row->id);

?>
<tr id="order_<?php echo $i; ?>" class="<?php echo "row$k"; ?>">
	
	<td align="center"><?php echo $i+1; ?></td>
	<td align="center"><?php echo $checked; ?></td>
	<td><a href="<?php echo $link; ?>"><?php echo $this->field->displayfieldvalue($row->id, $this->firstfield->id, array('short'=>true)); ?></a></td>
    <td align="center"><?php echo $featured; ?></td>
	<td align="center"><?php echo $published; ?></td>
	<td align="center"><?php echo $edit; ?> <?php echo $delete; ?></td>
	
</tr>
