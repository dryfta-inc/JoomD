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

$this->multiselect->initialize('table.admintable select:not(#catid)', array('minWidth'=>150));

$this->multiselect->initialize('select#catid', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'click'=>'loadfields', 'checkAll'=>'loadfields', 'uncheckAll'=>'loadfields', 'noneSelectedText'=>JText::_('SELCAT')));

?>

<script type="text/javascript">
	
	$jd(document).ready(function()	{
		
		$jd('.tooltip').tipsy({live:true});
		
		$jd('input.date').datetimepicker({dateFormat: "yy-mm-dd", timeFormat: "hh:mm:ss"});
		
		$jd('.modal_jform_created_by').unbind('click').click(function(event)	{
			event.preventDefault();
			if($jd('.userdialog').length==0)
				$jd('#joomdpanel').prepend('<div class="userdialog" />');
			
			$jd('.userdialog').html($jd('<iframe width="770" height="465" />').attr("src", $jd(this).attr('href'))).dialog({height:"500", width:"800"});
		});
		
		var cats = new Array();
		
		if($jd('select[name="catid[]"] option:selected').length > 0)	{
			
			$jd('select[name="catid[]"] option:selected').each(function() {
				cats.push($jd(this).val());
			});
		
		}
				
		loadfields(cats, 'click', null);
									
	});
	
	function jSelectUser_updateUser(id, title)
	{
		
		$jd('.created_by_label').html(title);
		$jd('input[name="created_by"]').val(id);
		$jd('.userdialog').dialog('close');
		
	}
	
	function loadfields(checked, event, ui)
	{
		
		if(!checked)	{
			var checked=new Array();
		}
		
		if(checked.length > 0)	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  data: {'option':'com_joomd', 'view':'item', 'task':'loadfields', 'typeid':<?php echo $this->params->typeid; ?>, 'catid[]':checked, 'id':<?php echo (int)$this->item->id; ?>, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
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

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=item" method="post" name="adminform" id="adminform" enctype="multipart/form-data">
<div class="width-60 fltlft">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'EDETAIL' ); ?></legend>
<table class="admintable">
  <tr>
    <td class="key"><?php echo JText::_('ALIAS'); ?>:</td>
    <td><input type="text" name="alias" id="alias" value="<?php echo $this->item->alias; ?>" size="40" /> <span class="tooltip" title="<?php echo JText::_('ALIASFIELDINFO'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('CATEGORIES'); ?>:</td>
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
  <tr>
    <td class="key"><?php echo JText::_('FEATURED'); ?>:</td>
    <td><input type="radio" name="featured" id="featured" value="1" <?php if($this->item->featured) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="featured" id="featured" value="0" <?php if(!$this->item->featured) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PUBLISHED'); ?>:</td>
    <td><input type="radio" name="published" id="published" value="1" <?php if($this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="published" id="published" value="0" <?php if(!$this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
  <tr>
	<td valign="top" class="key">
		<?php echo JText::_( 'ALEVEL' ); ?>:
	</td>
	<td>
		<?php echo $this->lists['access']; ?>
	</td>
 </tr>
  <tr>
    <td class="key"><?php echo JText::_('ORDERING'); ?>:</td>
    <td colspan="2">
	<?php
		
		if($this->item->id)	{
			
			echo '<select name="ordering" id="ordering">';
		
			for($i=0;$i<count($this->order_list);$i++)	{
			
				echo '<option value="'.$this->order_list[$i][1].'"';
				if($this->item->ordering == $this->order_list[$i][1])
					echo ' selected="selected" ';
				echo '>'.$this->order_list[$i][1].'::'.$this->order_list[$i][0].'</option>';
			
			}
			
			echo '</select>';
		}
		else
			echo JText::_('NEWORDERING');
		
	?>
	</td>
  </tr>
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

</fieldset>
</div>

<div class="width-40 fltrt">

<?php

if(in_array($this->type->config->get('publishing', 4), array(2,4)) or in_array($this->type->config->get('meta', 4), array(2,4)))	{
	
	echo $this->pane->startpane('itemparams', array('collapsible'=>true));
	
	if(in_array($this->type->config->get('publishing', 4), array(2,4)))	{
	
	echo $this->pane->startPanel('publishingparam', JText::_('PUBLISHING_OPTIONS'));
	
?>
	<fieldset class="panelform">
	<ul class="adminformlist">
    	<li>
        	<label class="tooltip" title="<?php echo JText::_('SET_HERE_CREATOR'); ?>"><?php echo JText::_('CREATED_BY'); ?></label>
        	<?php if($this->item->created_by) $value = JFactory::getUser($this->item->created_by)->name; else $value = JText::_('ANONYMOUS');  ?><span class="created_by_label"><?php echo $value; ?></span> <a class="modal_jform_created_by" href="index.php?option=com_users&view=users&layout=modal&tmpl=component&field=updateUser" title="<?php echo JText::_('SELECT_USER'); ?>"><?php echo JText::_('SELECT_USER'); ?></a>
            <input type="hidden" name="created_by" value="<?php echo $this->item->created_by; ?>" />
        </li>
        <li>
        	<label class="tooltip" title="<?php echo JText::_('SET_HERE_CREATION_DATE'); ?>"><?php echo JText::_('CREATED_DATE'); ?></label>
        	<input type="text" name="created" class="inputbox date" size="20" value="<?php echo $this->item->created; ?>" />
        </li>
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
	
	if(in_array($this->type->config->get('meta', 1), array(2,4)))	{
	
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
        <li>
        	<label class="tooltip" title="<?php echo JText::_('TT_ROBOTS'); ?>"><?php echo JText::_('ROBOTS'); ?></label>
        	<select name="metadata[robots]">
                <option <?php if($this->item->metadata->get('robots')=="index, follow") echo 'selected="selected"'; ?> value="index, follow">Index, Follow</option>
                <option <?php if($this->item->metadata->get('robots')=="noindex, follow") echo 'selected="selected"'; ?> value="noindex, follow">No index, follow</option>
                <option <?php if($this->item->metadata->get('robots')=="index, nofollow") echo 'selected="selected"'; ?> value="index, nofollow">Index, No follow</option>
                <option <?php if($this->item->metadata->get('robots')=="noindex, nofollow") echo 'selected="selected"'; ?> value="noindex, nofollow">No index, no follow</option>
			</select>
        </li>
        <li>
        	<label class="tooltip" title="<?php echo JText::_('TT_AUTHOR'); ?>"><?php echo JText::_('AUTHOR'); ?></label>
        	<input type="text" name="metadata[author]" class="inputbox" size="20" value="<?php echo $this->item->metadata->get('author'); ?>" />
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
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="item" />
<input type="hidden" name="abase" id="abase" value="1" />
<input type="hidden" name="typeid" value="<?php echo $this->params->typeid; ?>" />
</form>

</div>

</div>

</div>