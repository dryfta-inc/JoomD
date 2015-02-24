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
	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'common_header.php');

$this->multiselect->initialize('select', array('minWidth'=>150));

?>

<script type="text/javascript">
	
	function validateit()
	{
		
		var form = document.adminform;
		
		if(form.rate.value == 0)
		{
			
			alert("<?php echo JText::_('PLZGIVESOMERATING'); ?>");
			return false;
			
		}
		
		if(form.created_by.value == 0)
		{
			
			alert("<?php echo JText::_('PLZSELECTUSER'); ?>");
			return false;
			
		}
		
		if(form.typeid.value == 0)
		{
			
			alert("<?php echo JText::_('PLZSELECTTYPE'); ?>");
			return false;
			
		}
		
		if(form.itemid.value == 0)
		{
			
			alert("<?php echo JText::_('PLZSELECTITEM'); ?>");
			return false;
			
		}
		
		return true;
		
	}
	
	function get_items()
	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'rnr', 'task':'get_pitems', 'cid':<?php echo (int)$this->item->id; ?>, 'typeid':$jd('select[name="typeid"]').val(), "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('select[name="itemid"]').html(res.list);
					$jd('select[name="itemid"]').multiselect("refresh");
				}
				else	{
					displayalert(res.error, 'error', true);
				}
					
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				  displayalert(textStatus, 'error', true);
			  }
		});
		
	}

</script>


<div id="element-box">

<div class="m">

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=rnr" method="post" name="adminform" id="adminform" enctype="multipart/form-data">
<div class="col101">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'REVIEWDETAIL' ); ?></legend>
<table class="admintable">
  <tr>
    <td class="key"><?php echo JText::_('RATE'); ?>:</td>
    <td>
    	<?php $width = $this->item->rate*20; ?><ul class="item_rating_list"><li id="item_current_rating" class="item_current_rating" style="width:<?php echo $width; ?>%;"></li></ul><input type="hidden" name="rate" id="rate" value="<?php echo $this->item->rate; ?>" /> <em class="required">*</em></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('TITLE'); ?>:</td>
    <td><input type="text" name="name" id="name" value="<?php echo $this->item->name; ?>" size="40" /></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('COMMENT'); ?>:</td>
    <td><textarea name="comment" id="comment"><?php echo $this->item->comment; ?></textarea></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('USER'); ?>:</td>
    <td colspan="2">
	<select name="created_by" id="created_by">
	<?php
	
		for($i=0;$i<count($this->users);$i++)	{	?>
		
			<option value="<?php echo $this->users[$i]->id; ?>" <?php if($this->users[$i]->id==$this->item->created_by) echo 'selected="selected"'; ?>>
			<?php echo $this->users[$i]->name; ?>
			</option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('TYPE'); ?>:</td>
    <td colspan="2">
	<select name="typeid" id="typeid" onchange="get_items();">
	<?php
	
		for($i=0;$i<count($this->types);$i++)	{	?>
		
			<option value="<?php echo $this->types[$i]->id; ?>" <?php if($this->types[$i]->id==$this->item->typeid) echo 'selected="selected"'; ?>>
			<?php echo $this->types[$i]->name; ?>
			</option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('ITEM'); ?>:</td>
    <td colspan="2">
	<select name="itemid" id="itemid">
	<?php
	
		for($i=0;$i<count($this->pitems);$i++)	{	?>
		
			<option value="<?php echo $this->pitems[$i]->id; ?>" <?php if($this->pitems[$i]->id==$this->item->itemid) echo 'selected="selected"'; ?>>
			<?php echo $this->pitems[$i]->title; ?>
			</option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PUBLISHED'); ?>:</td>
    <td><input type="radio" name="published" id="published" value="1" <?php if($this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="published" id="published" value="0" <?php if(!$this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="rnr" />
<input type="hidden" name="abase" id="abase" value="1" />
</form>


<div class="clr"></div>

</div>

</div>

</div>
<div class="clr"></div>