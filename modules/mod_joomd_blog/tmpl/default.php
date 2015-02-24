<?php

/*------------------------------------------------------------------------
# mod_joomd_blog - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomd_blog_lis_p">
<?php

if(count($items) > 0)	{
	
	$user = JFactory::getUser();

	echo '<ul class="items">';

	$catid = (int)$params->get('catid', 0);
	$char_limit=$params->get('char_limit', 100);
	
	for($i=0;$i<count($items);$i++)	{ 
 
		echo '<li>';
		
		echo '<div class="item-image">'.$_field->displayfieldvalue($items[$i]->id, $imagefield->id, array('short'=>true, 'char'=>$char_limit)).'</div>';
		echo '<div class="item-title"><h4><a  href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$type->id.'&id='.$items[$i]->id).'">'.$_field->displayfieldvalue($items[$i]->id, $titlefield->id).'</a></h4></div>';
		
		echo'<div class="date_for cat_p"><span class="date_for-j_t">'.date("M d Y", strtotime($items[$i]->created)).'</span>';
		
		if(count($items[$i]->cats))	{
			foreach($items[$i]->cats as $cat)
				echo '<sapn class="cat_name_a"><a href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&typeid='.$type->id.'&catid='.$cat->id).'">'.$cat->name.'</a></span>';
		}
			
		echo '</div>';
		
		echo '<div class="field_cell '.$descrfield->cssclass.'">'.$_field->displayfieldvalue($items[$i]->id, $descrfield->id, array('short'=>true, 'char'=>$char_limit)).'</div>';
		
	 
		echo '<div class="clr"></div>
		</li>';
	
	}

	
	


echo '</ul>';

}

else	{
	echo JText::_('NOITEMFOUND');
}?>
</div>