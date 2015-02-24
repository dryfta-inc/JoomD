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

require_once(JPATH_ADMINISTRATOR.'/components/com_joomd/helpers/common_header.php');

$this->multiselect->initialize('table.edittable select:not(#catid)', array('minWidth'=>150));

$this->multiselect->initialize('table.edittable select#catid', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'click'=>'loadfields', 'checkAll'=>'loadfields', 'uncheckAll'=>'loadfields', 'noneSelectedText'=>JText::_('SELCAT')));

$canState = Joomd::canState($this->item);
$canFeature = Joomd::canFeature($this->item);

?>

<script type="text/javascript">

	$jd(document).ready(function()	{
								 
		$jd('.tooltip').tipsy({live:true});
		
		$jd('input.date').datetimepicker({dateFormat: "yy-mm-dd", timeFormat: "hh:mm:ss"});
		
		var cats = new Array();
		
		if($jd('select[name="catid[]"] option:selected').length > 0)	{
			
			$jd('select[name="catid[]"] option:selected').each(function() {
				cats.push($jd(this).val());
			});
		
		}
				
		loadfields(cats, 'click', null);
									
	});
	
	function loadfields(checked, event, ui)
	{
		if(!checked)	{
			var checked=new Array();
		}
		
		if(checked.length > 0)	{
		
		$jd.ajax({
			  url: "<?php echo JURI::root(); ?>",
			  type: "POST",
			  data: {'option':'com_joomd', 'view':'itempanel', 'task':'loadfields', 'typeid':<?php echo $this->cparams->typeid; ?>, 'catid[]':checked, 'id':<?php echo (int)$this->item->id; ?>, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
					$jd('#fieldtable').html(res);
					
			  },
			  error: function()	{
				  alert('error');				  
			  }
		});
		
		}
		else	{
			$jd('#fieldtable').html('');
		}
		
	}
	
	function validateit()
	{
		
		var form = document.adminform;
		
		var cats = new Array();
		$jd('select[name="catid[]"] option:selected').each(function() {
			cats.push($jd(this).val());
		});
		
		if(cats.length < 1)	{
			alert("<?php echo JText::_('PLSSELATLOCAT'); ?>");
			return false;
		}
		
		if(typeof(validatefields) == 'function')	{
                
			if(!validatefields())
				return false;
			
		}
		
		return true;
		
	}

</script>

<div id="element-box">

<div class="m">

<div id="joomdpanel<?php echo $this->params->get('pageclass_sfx'); ?>">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=itempanel" method="post" name="editform" id="editform" enctype="multipart/form-data">
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'ITEMDET' ); ?></legend>
<table class="edittable">
  <tr>
    <td class="key"><?php echo JText::_('ALIAS'); ?>:</td>
    <td><input type="text" name="alias" id="alias" value="<?php echo $this->item->alias; ?>" size="40" /> <span class="tooltip" title="<?php echo JText::_('ALIASFIELDINFO'); ?>"><img src="<?php echo JURI::root(); ?>components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('CATEGORY'); ?>:</td>
    <td colspan="2">
	<select name="catid[]" id="catid" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($this->cats);$i++)	{	?>
		
			<option value="<?php echo $this->cats[$i]->id; ?>" <?php if($this->cats[$i]->selected) echo 'selected="selected"'; ?>>
			<?php echo $this->cats[$i]->treename; ?>
			</option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <?php if($canFeature) : ?>
  <tr>
    <td class="key"><?php echo JText::_('FEATURED'); ?>:</td>
    <td><input type="radio" name="featured" id="featured" value="1" <?php if($this->item->featured) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="featured" id="featured" value="0" <?php if(!$this->item->featured) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
  <?php endif; ?>
  <?php if($canState) : ?>
  <tr>
    <td class="key"><?php echo JText::_('PUBLISHED'); ?>:</td>
    <td><input type="radio" name="published" id="published" value="1" <?php if($this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="published" id="published" value="0" <?php if(!$this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
  <?php endif; ?>
  <tr>
    <td class="key"><?php echo JText::_('LANGUAGE'); ?>:</td>
    <td colspan="2">
		<select name="language" id="language">
        	<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->item->language);?>
        </select>
	</td>
  </tr>
</table>

<div id="fieldtable">
</div>

<?php	if($this->config->captcha)	{	?>
<table class="edittable">
  <tr>
    <td class="key"><?php echo JText::_( 'ENTERTHETEXT' ); ?>:</td>
    <td><div class="captcha">
	<img name="captchaimg" src="<?php echo JURI::root(); ?>index.php?option=com_joomd&task=captcha_display" alt="<?php echo JText::_( 'JOOMDCAPTCHA' );?>" /><br />
 <?php echo JText::_('CANREDTHIS'); ?> <a href="javascript:void(0);" onclick="document.editform.captcha.value = '';document.images['captchaimg'].src='<?php echo JURI::root(); ?>index.php?option=com_joomd&task=captcha_display&t='+(new Date()).getTime();return false;"><?php echo JText::_('TRYANOTHER'); ?></a><br /><input type="text" name="captcha" id="captcha" size="12" class="inputbox required" value="" autocomplete="off" /></div>
	</td>
  </tr>
</table>
<?php	}	?>

	</fieldset>

</div>

<div class="width-40 fltrt">

<?php

if(in_array($this->type->config->get('publishing', 4), array(3,4)) or in_array($this->type->config->get('meta', 4), array(3,4)))	{
	
	echo $this->pane->startpane('itemparams', array('collapsible'=>true));
	
	if(in_array($this->type->config->get('publishing', 4), array(3,4)))	{
	
	echo $this->pane->startPanel('publishingparam', JText::_('PUBLISHING_OPTIONS'));
	
?>
	<fieldset class="panelform">
	<ul class="adminformlist">
        <li>
        	<label class="tooltip" title="<?php echo JText::_('SET_HERE_START_PUBLISH_DATE'); ?>"><?php echo JText::_('START_PUBLISHING'); ?></label>
        	<input type="text" name="publish_up" class="inputbox date" size="20" value="<?php echo $this->item->publish_up; ?>" />
        </li>
        <li>
        	<label class="tooltip" title="<?php echo JText::_('SET_HERE_FINISH_PUBLISH_DATE'); ?>"><?php echo JText::_('FINISH_PUBLISHING'); ?></label>
        	<input type="text" name="publish_down" class="inputbox date" size="20" value="<?php echo $this->item->publish_down; ?>" />
        </li>
        <?php if($this->item->id) :	?>
        <li>
        	<label class="tooltip"><?php echo JText::_('MODIFIED_BY'); ?></label>
        	<?php if($this->item->modified_by) $value = JFactory::getUser($this->item->modified_by)->name; else $value = null; ?><input type="text" name="modified_by" class="readonly" disabled="disabled" value="<?php echo $value; ?>" />
        </li>
        <li>
        	<label class="tooltip"><?php echo JText::_('MODIFIED_DATE'); ?></label>
        	<input type="text" name="modified" class="readonly" disabled="disabled" value="<?php echo $this->item->modified; ?>" />
        </li>
        <li>
        	<label class="tooltip"><?php echo JText::_('HITS'); ?></label>
        	<input type="text" name="hits" class="readonly" disabled="disabled" value="<?php echo $this->item->hits; ?>" />
        </li>
        <?php endif; ?>
    </ul>
	</fieldset>
    
<?php
	
	echo $this->pane->endPanel();
	
	}
	
	if(in_array($this->type->config->get('meta', 1), array(3,4)))	{
	
	echo $this->pane->startPanel('metaparams', JText::_('METADATA_OPTIONS'));
	
?>

	<fieldset class="panelform">
    <ul class="adminformlist">
    	<li>
        	<label class="tooltip" title="<?php echo JText::_('TT_META_DESCRIPTION'); ?>"><?php echo JText::_('META_DESCRIPTION'); ?></label>
        	<textarea name="metadata[meta_desc]" class="inputbox" rows="3" cols="25"><?php echo $this->item->metadata->get('meta_desc'); ?></textarea>
        </li>
        <li>
        	<label class="tooltip" title="<?php echo JText::_('TT_META_KEYWORDS'); ?>"><?php echo JText::_('META_KEYWORDS'); ?></label>
        	<textarea name="metadata[meta_key]" class="inputbox" rows="3" cols="25"><?php echo $this->item->metadata->get('meta_key'); ?></textarea>
        </li>
    </ul>
	</fieldset>

<?php
	
	echo $this->pane->endPanel();
	
	}
	
	echo $this->pane->endPane();
	
}
	
?>

</div>

<div class="clr"></div>

<?php

	echo JHTML::_( 'form.token' );
	
	foreach($this->cparams as $k=>$v)	
		echo '<input type="hidden" id="'.$k.'" name="'.$k.'" value="'.$v.'" />';
		
?>

</form>


</div>

</div>

</div>

</div>