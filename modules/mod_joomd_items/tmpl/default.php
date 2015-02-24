<?php

/*------------------------------------------------------------------------
# mod_joomd_items - JoomD
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

if(count($items) > 0)	{
	
	$user = JFactory::getUser();

	echo '<ul class="items">';

	for($i=0;$i<count($items);$i++)	{
	
		echo '<li><a href="'.JRoute::_('index.php?option=com_joomd&view='.$type->plugin.'&layout=detail&typeid='.$type->id.'&id='.$items[$i]->id).'">';
		
		foreach($fields as $field)	{
						
			$value = $_field->getfieldvalue($items[$i]->id, $field->id);
			
			if(!empty($value))
				echo '<div class="field_cell '.$field->cssclass.'">'.$_field->displayfieldvalue($items[$i]->id, $field->id, true).'</div>';
			
		}
		
		echo '</a></li>';
	
	}


echo '</ul>';

}

else	{
	echo JText::_('NOENTRYFOUND');
}