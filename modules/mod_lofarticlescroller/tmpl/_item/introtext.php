<div class="lof-inner lof-introtext" <?php echo $style; ?>>
	<?php if( $showTitle ): ?>
     <a class="lof-title" target="<?php echo $openTarget; ?>" title="<?php echo $row->title; ?>" href="<?php echo $row->link;?>">
       <?php echo $row->title; ?>
     </a>
     <?php endif; ?>
	<?php 	echo $row->introtext; ?>
        <?php if( $showReadmore ) : ?>
      <a target="<?php echo $openTarget; ?>" class="lof-readmore" title="<?php echo $row->title;?>" href="<?php echo $row->link;?>">
        <?php echo JText::_('READ_MORE');?>
      </a>
    <?php endif; ?>
</div>