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


defined('_JEXEC') or die('Restricted access');

if($this->abase)
	require_once(JPATH_ADMINISTRATOR.'/components/com_joomd/helpers/common_header.php');


if(!$this->item->id)	{
	joomdui::uploadfile('#file');

?>

<script type="text/javascript">

function validateit()
{

	if(files_to_upload.length < 1)	{
		alert("<?php echo JText::_('PLSSELTOUP'); ?>");
		return false;
	}
	else	{
		return true;		
	}

}

</script>

<?php	}	?>


<div id="element-box">

<div class="m">

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=apps" method="post" name="adminform" id="adminform" enctype="multipart/form-data">
<div class="col101">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'APP_DETAIL' ); ?></legend>
<table class="admintable">
	<?php if($this->item->id)	:	?>
  <tr>
    <td class="key"><?php echo JText::_('LABEL'); ?>:</td>
    <td><?php echo JText::_($this->item->label); ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('ORDERING'); ?>:</td>
    <td colspan="2">
	<?php
		
		echo '<select name="ordering" id="ordering">';
		
		for($i=0;$i<count($this->order_list);$i++)	{
		
			echo '<option value="'.$this->order_list[$i]->ordering.'"';
			if($this->item->ordering == $this->order_list[$i]->ordering)
				echo ' selected="selected" ';
			echo '>'.$this->order_list[$i]->ordering.'::'.$this->order_list[$i]->name.'</option>';
		
		}
		
		echo '</select>';

	?>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('DESCRIPTION'); ?>:</td>
    <td><?php echo JText::_($this->item->descr); ?></td>
  </tr>
  <?php if(!$this->item->iscore):	?>
  <tr>
    <td class="key"><?php echo JText::_('PUBLISHED'); ?>:</td>
    <td><input type="radio" name="published" id="published" value="1" <?php if($this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="published" id="published" value="0" <?php if(!$this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
  <?php endif; ?>
  <?php	else	:	?>
  
  <tr>
    <td class="key">
		<?php	echo JText::_('UPLOAD_APP');	?>:
     </td>
    <td colspan="2"><div id="file"></div></td>
 </tr>
 <?php	endif;	?>
  
</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="apps" />
<input type="hidden" name="abase" id="abase" value="1" />
</form>


<div class="clr"></div>

</div>

</div>

<div class="clr"></div>