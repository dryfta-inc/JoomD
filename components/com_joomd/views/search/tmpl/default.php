<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<div id="joomdpanel<?php echo $this->params->get('pageclass_sfx'); ?>">

<?php

$this->multiselect->initialize('form[name=\'csearchform\'] select:not(#cats)');
$this->multiselect->initialize('form[name=\'csearchform\'] select#cats', array('height'=>'200', 'header'=>true, 'filter'=>true, 'multiple'=>true));

echo '<div class="componentheading"><h1>' . $this->params->def('page_title') . '</h1></div>';

?>

<form action="<?php echo JRoute::_('index.php?option=com_joomd&view=search&layout=search'); ?>" method="post" name="csearchform">

<?php /* if(!$this->cparams->typeid)	{	?>

<div class="search_col1"><?php echo JText::_('Type'); ?>: </div>


 <div class="search_col2">

	<select name="typeid[]" id="typeid" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($this->types);$i++)
			echo '<option value="'.$this->types[$i]->id.'">'.$this->types[$i]->name.'</option>';
	
	?>
	</select>

</div>

<div class="clr"></div>

<?php	} */	?>

<?php if($this->params->def('cat', 1)):	?>

<div class="search_col1"><?php echo JText::_('CATEGORY'); ?>: </div>


 <div class="search_col2">

	<select name="cats[]" id="cats" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($this->cats);$i++)
			echo '<option value="'.$this->cats[$i]->id.'">'.$this->cats[$i]->treename.'</option>';
	
	?>
	</select>

</div>

<div class="clr"></div>

<?php	endif;	?>

<?php	for($i=0;$i<count($this->fields);$i++)	{	?>
	
		<div class="search_col1"><?php echo $this->fields[$i]->name; ?>:</div>
		<div class="search_col2">
		<?php	echo $this->field->loadsearchfield($this->fields[$i]->id, 0, array('form'=>'csearchform'));	?>
		</div>
		
		<div class="clr"></div>
	
<?php	}	?>

<div class="clr"></div>

<div class="search_button">
	
	<input type="submit" name="submit" id="submit" value="<?php echo JText::_('SEARCH'); ?>" /> &nbsp; <input type="reset" name="reset" id="reset" value="<?php echo JText::_('RESET'); ?>" />

</div>

<?php //removed in JoomD v2.1 echo JHTML::_( 'form.token' ); ?>

<?php if($this->cparams->typeid)	{	?>
<input type="hidden" name="typeid" value="<?php echo $this->cparams->typeid; ?>" />
<?php	}	?>
<input type="hidden" name="Itemid" value="<?php echo $this->cparams->Itemid; ?>" />
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="view" value="search" />
<input type="hidden" name="task" value="search" />

</form>

</div>