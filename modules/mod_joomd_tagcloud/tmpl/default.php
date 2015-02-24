<?php

/*------------------------------------------------------------------------
# mod_joomd_tagcloud - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php

if(count($obj->items) > 0)	{
	
	$user = JFactory::getUser();

	echo '<ul class="tag_cloud_items">';
		
	$obj->avg_hits = $obj->max - $obj->min;
	if ($obj->avg_hits == "0") {
		$obj->avg_hits = "1";
	}
	
	$min = $params->def('min', 10);
	$max = $params->def('max', 32);
	$avg = $max-$min;
	
	$done = array();
	$count = count($obj->items);
	
	for($i=0;$i<count($obj->items);$i++)	{
		
		do	{
			$rand = rand(0, $count-1);
		}while(in_array($rand, $done));
		
		array_push($done, $rand);
		
		$item = $obj->items[$rand];
		
		$value = $_field->getfieldvalue($item->id, $fieldid);
		
		if(!empty($value))	{
			
			$font_size = $min + ($avg * (($item->hits - $obj->min) / $obj->avg_hits));
			
			$font_size = round($font_size, 2);
			
			echo '<li><a href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$type->id.'&id='.$item->id).'" style="font-size:'.$font_size.'px;">'.$value.'</a></li>';
			
		}
	
	}


echo '</ul><div class="clr"></div>';

}

else	{
	echo JText::_('NOITEMFOUND');
}