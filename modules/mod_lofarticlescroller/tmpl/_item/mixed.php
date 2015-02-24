<div class="lof-inner" <?php echo $style; ?>>
   
        <?php if( $showTitle ): ?>
     <a class="lof-title" target="<?php echo $openTarget; ?>" title="<?php echo $row->title; ?>" href="<?php echo $row->link;?>">
       <?php echo substr($row->title, 0, 60); ?>
     </a>
     <?php endif; ?>
   
    <?php if( $row->thumbnail ): ?>
        <a target="<?php echo $openTarget; ?>" class="lof-image-link <?php echo $thumbnailAlignment;?>" style=" height:<?php echo $thumbHeight ;?>px; width:<?php echo $thumbWidth ;?>px; display:block" title="<?php echo $row->title;?>" href="<?php echo $row->link;?>">
          <img src="<?php echo $row->thumbnail; ?>" title="<?php echo $row->title?>" alt="<?php echo $row->title?>" />
        </a> 
    <?php endif ; ?>

 
      <?php if( $showDate ): ?>
       <i><?php echo $row->date; ?></i><br />
     <?php endif; ?>
     
	<?php echo $row->description; ?>

    <?php if( $showReadmore ) : ?>
      <a target="<?php echo $openTarget; ?>" class="lof-readmore" title="<?php echo $row->title;?>" href="<?php echo $row->link;?>">
        <?php echo JText::_('READ_MORE');?>
      </a>
    <?php endif; ?>
</div>