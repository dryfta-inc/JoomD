<?php

/*------------------------------------------------------------------------
# mod_joomd_items_slider - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if(count($items) > 0):

$char_limit = (int)$params->def('char_limit', 100);

$lightbox = (int)$params->def('lightbox', true);

//scroller options
$ctrl_type = $params->def('ctrl_type', 'scrollbar');
$num_display = (int)$params->def('num_display', 3);
$width = (int)$params->def('width', 195);
$height = (int)$params->def('height', 180);
$slide_margin = (int)$params->def('slide_margin', 5);
$auto_scroll = $params->def('auto_scroll', true);
$delay = (int)$params->def('delay', 4000);
$scroll_speed = (int)$params->def('scroll_speed', 1000);
$display_buttons = $params->def('display_buttons', true);
$display_caption = $params->def('display_caption', true);
$caption_position = $params->def('caption_position', 'inside');
$caption_align = $params->def('caption_align', 'bottom');
$caption_effect = $params->def('caption_effect', 'slide');
$mousewheel = $params->def('mousewheel', true);


//lightbox options

$rotate = $params->def('rotate', true);
$delay2 = (int)$params->def('delay2', 4000);
$duration = (int)$params->def('duration', 600);
$display_number = $params->def('display_number', true);
$display_dbuttons = $params->def('display_dbuttons', true);
$display_caption2 = $params->def('display_caption2', true);
$caption_position2 = $params->def('caption_position2', 'outside');
$cont_nav = $params->def('cont_nav', true);

?>

<script type="text/javascript">
$jd(function()	{
	
	$jd('a.title_link').unbind('click').bind('click', function(event)	{
		window.location = $jd(event.target).attr('href');
	});
	
	var $jdcontainer = $jd(".container");
	
	$jdcontainer.wtScroller({
		num_display:<?php echo $num_display; ?>,
		slide_width:<?php echo $width; ?>,
		slide_height:<?php echo $height; ?>,
		slide_margin:<?php echo $slide_margin; ?>,
		button_width:55,
		ctrl_height:25,
		margin:10,	
		auto_scroll:<?php echo $auto_scroll?'true':'false'; ?>,
		delay:<?php echo $delay; ?>,
		scroll_speed:<?php echo $scroll_speed; ?>,
		easing:"",
		auto_scale:false,
		move_one:false,
		ctrl_type:"<?php echo $ctrl_type; ?>",
		display_buttons:<?php echo $display_buttons?'true':'false'; ?>,
		mouseover_buttons:false,
		display_caption:<?php echo $display_caption?'true':'false'; ?>,
		mouseover_caption:true,
		caption_effect:"<?php echo $caption_effect; ?>",
		caption_align:"<?php echo $caption_align; ?>",
		caption_position:"<?php echo $caption_position; ?>",					
		cont_nav:<?php echo $cont_nav?'true':'false'; ?>,
		shuffle:false,
		mousewheel_scroll:true
	});
	
	<?php if($lightbox) :	?>
	
	$jd("a.box_img[rel='scroller']").wtLightBox({
		rotate:<?php echo $rotate?'true':'false'; ?>,
		delay:<?php echo $delay2; ?>,
		duration:<?php echo $duration; ?>,
		display_number:<?php echo $display_number?'true':'false'; ?>,
		display_dbuttons:<?php echo $display_dbuttons?'true':'false'; ?>,
		display_timer:true,
		display_caption:<?php echo $display_caption2?'true':'false'; ?>,
		caption_position:"<?php echo $caption_position2; ?>",
		cont_nav:<?php echo $cont_nav?'true':'false'; ?>,
		auto_fit:false,
		easing:"",
		type:"GET"
	});
	
	<?php endif; ?>
		
});

</script>

<div class="container">
    <div class="wt-scroller">
        <div class="prev-btn"></div>          
        <div class="slides">
        
        <ul class="items">
        
		<?php
            
            $user = JFactory::getUser();
         
            for($i=0;$i<count($items);$i++)	{ 
            
                echo '<li>';
                
                $img = explode('|', $_field->getfieldvalue($items[$i]->id, $imagefield->id));
                
				if($lightbox)
	                echo '<a class="box_img" href="'.JURI::root().'images/joomd/'.$img[0].'" rel="scroller" target="_blank"><img style="left: 0px; top: 0px; opacity: 1;" src="'.JURI::root().'images/joomd/thumbs/'.$img[0].'" alt=""></a>';
				else
					echo '<a href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$type->id.'&id='.$items[$i]->id).'"><img style="left: 0px; top: 0px; opacity: 1;" src="'.JURI::root().'images/joomd/thumbs/'.$img[0].'" alt=""></a>';
                
                echo '<p class="inside"><a target="_parent" href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$type->id.'&id='.$items[$i]->id).'" class="title_link">'.$_field->displayfieldvalue($items[$i]->id, $titlefield->id).'</a><br />'.$_field->displayfieldvalue($items[$i]->id, $descrfield->id, array('short'=>true, 'char'=>$char_limit)).'</p>';                
                
                echo '</li>';
            
            }
        
        
        ?>

	</ul>

	</div>          	
		
        <div class="next-btn"></div>
		<div class="lower-panel">
        </div>
	</div>
      
</div>

<?php

else :
	echo JText::_('NOITEMFOUND');

endif;