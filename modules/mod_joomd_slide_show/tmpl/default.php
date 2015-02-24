<?php

/*------------------------------------------------------------------------
# mod_joomd_slideshow - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div id="slider_container_1">

	<div id="SliderName">

	<?php
    
    if(count($items) > 0):
        
        $user = JFactory::getUser();
    
        for($i=0;$i<count($items);$i++)	{ 
     
            $img = explode('|', $_field->getfieldvalue($items[$i]->id, $imagefield->id));
    
            echo '<a class="box_img" href="'.JURI::root().'images/joomd/'.$img[0].'" rel="scroller" target="temp_win"><img style="left: 0px; top: 0px; opacity: 1;" src="'.JURI::root().'images/joomd/'.$img[0].'" alt=""></a>';
            
            echo '<div class="SliderNameDescription"><h3><a target="_parent" href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$type->id.'&id='.$items[$i]->id).'">'.$_field->displayfieldvalue($items[$i]->id, $titlefield->id).'</a></h3>'.$_field->displayfieldvalue($items[$i]->id, $descrfield->id, array('short'=>true, 'char'=>100)).'</div>';                
            
        
        }
    
        
    else	:
        echo JText::_('NOITEMFOUND');
		
    endif;
    
    ?>
	</div>
  
    <div id="SliderNameNavigation"></div>
    <div class="c"></div>
  
</div>

<script type="text/javascript">

	// we created new effect and called it 'JoomD'. We use this name later.
	Sliderman.effect({name: 'JoomD', cols: 0, rows: 0, delay: 100, fade: true, order: 'straight_stairs'});

	var demoSlider = Sliderman.slider({container: 'SliderName', width: 640, height: 300, effects: 'JoomD',
	display: {
		pause: true, // slider pauses on mouseover
		autoplay: 6000, // 3 seconds slideshow
		always_show_loading:0, // testing loading mode
		description: {background: '#ffffff', opacity: 0.5, height: 50, position: 'bottom'}, // image description box settings
		loading: {background: '#000000', opacity: 0.2, image: 'img/loading.gif'}, // loading box settings
		buttons: {opacity: 1, prev: {className: 'SliderNamePrev', label: ''}, next: {className: 'SliderNameNext', label: ''}}, // Next/Prev buttons settings
		navigation: {container: 'SliderNameNavigation', label: '&nbsp;'} // navigation (pages) settings
	}});

</script>
