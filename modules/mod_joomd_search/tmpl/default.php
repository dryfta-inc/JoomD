<?php

/*------------------------------------------------------------------------
# mod_joomd_search - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdsearchpanel">

<form action="<?php echo JRoute::_('index.php?option=com_joomd&view=search&layout=search'); ?>" method="post" name="searchform">

<?php if($cat)	{	?>

<div class="search_col1"><?php echo JText::_('CATEGORY'); ?>: </div>


 <div class="search_col2">

	<select name="cats[]" id="cats" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($cats);$i++)
			echo '<option value="'.$cats[$i]->id.'">'.$cats[$i]->treename.'</option>';
	
	?>
	</select>

</div>

<div class="clr"></div>

<?php	}	?>

<?php	for($i=0;$i<count($fields);$i++)	{	?>
	
		<div class="search_col1"><?php echo $fields[$i]->name; ?>: </div>
		<div class="search_col2">

<?php

	echo $field->loadsearchfield($fields[$i]->id, 0, array('form'=>'searchform'));

?>
		</div>
		
		<div class="clr"></div>
	
<?php	}	?>

<div class="clr"></div>

<div class="search_button">
	
	<input type="submit" name="submit" id="submit" value="<?php echo JText::_('SEARCH'); ?>" /> &nbsp; <input type="reset" name="reset" id="reset" value="<?php echo JText::_('RESET'); ?>" />

</div>

<?php //removed in JoomD v2.1 echo JHTML::_( 'form.token' ); ?>

<input type="hidden" name="typeid" value="<?php echo $typeid; ?>" />

<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="view" value="search" />
<input type="hidden" name="task" value="search" />

</form>	


</div>