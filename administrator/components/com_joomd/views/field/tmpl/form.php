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

$files = array();

if(!empty($this->item->icon) and is_file(JPATH_SITE.'/images/joomd/'.$this->item->icon))	{
	$file->name = $this->item->icon;
	$file->thumbnail_url = JURI::root().'images/joomd/'.$this->item->icon;
	$file->delete_url = 'index.php?option=com_joomd&view=field&task=delete_icon&id='.$this->item->id.'&abase=1';
	array_push($files, $file);
}


Joomdui::uploadfile('#fieldicon', array('fieldname'=>'icon', 'buttontext'=>JText::_('ADDICON'), 'files'=>$files));

$this->multiselect->initialize('form[name=\'adminform\'] select:not(#types, #catid)', array('minWidth'=>150));

$this->multiselect->initialize('select#types', array('header'=>true, 'multiple'=>true, 'click'=>'loadcats', 'checkAll'=>'loadcats', 'uncheckAll'=>'loadcats', 'noneSelectedText'=>JText::_('SELTYPES')));

$this->multiselect->initialize('select#catid', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'noneSelectedText'=>JText::_('SELCAT')));

?>

<script type="text/javascript">

	$jd(document).ready(function()	{
								 				
		var types = new Array();
		$jd('select[name="types[]"] option:selected').each(function() {
			types.push($jd(this).val());
		});
		
		loadcats(types, 'click', null);
		
		loadfieldoptions();
									
	});
	
	function loadcats(checked, event, ui)
	{
		
		if(!checked)	{
			var checked=new Array();
		}
		
		if(checked.length > 0)	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'field', 'task':'reloadcats', 'types[]':checked, 'cid[]':<?php echo (int)$this->item->id; ?>, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('select#catid').html(res.list);
					$jd('select#catid').multiselect("refresh");
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
		else	{
			$jd('select#catid').html('');
			$jd('select#catid').multiselect("refresh");
		}
		
	}
	
	function loadfieldoptions()
	{
		
		var e = $jd('select[name="type"]').val();
		
		if(e == 0)	{
			$jd('#fieldoptions').html('');
		}
		
		else	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'field', 'task':'loadfieldoptions', 'id':<?php echo (int)$this->item->id; ?>, 'type':e, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('#fieldoptions').html(res.html);
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
		
	}
	
	function validateit()
	{
		
		var form = document.adminform;
		
		if(form.name.value == "")	{
			alert("<?php echo JText::_('PLSENTFTIT'); ?>");
			return false;
		}
		
		var types = new Array();
		$jd('select[name="types[]"] option:selected').each(function() {
			types.push($jd(this).val());
		});
		
		if(types.length < 1)	{
			alert("<?php echo JText::_('PLSSELATLOTYP'); ?>");
			return false;
		}
		
		var cats = new Array();
		$jd('select[name="catid[]"] option:selected').each(function() {
			cats.push($jd(this).val());
		});
		
		if(cats.length < 1)	{
			alert("<?php echo JText::_('PLSSELALOCAT'); ?>");
			return false;
		}
		
		if(form.type.value == 0)	{
			alert("<?php echo JText::_('PLSSELFTYP'); ?>");
			return false;
		}
		
		if(typeof(validateoptions) == 'function')	{
                
			if(!validateoptions())
				return false;
			
		}
		
		return true;
		
	}
	
	function show(e)
	{
		
		if(e.value == 10 || e.value == 11)	{
			$jd('#multiblock').show();
		}
		else	{
			$jd('#multiblock').hide();
		}
		
	}

</script>

<div id="element-box">

<div class="m">

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=field" method="post" name="adminform" id="adminform" enctype="multipart/form-data">
<div class="col101">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'FIELDDET' ); ?></legend>
<table class="admintable">
  <tr>
    <td class="key"><?php echo JText::_('TITLE'); ?>:</td>
    <td><input type="text" name="name" id="name" value="<?php echo $this->item->name; ?>" size="40" /> <em class="required">*</em></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('TYPE'); ?>:</td>
    <td colspan="2">
	<select name="types[]" id="types" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($this->types);$i++)	{	?>
		
			<option value="<?php echo $this->types[$i]->id; ?>" <?php if($this->types[$i]->selected) echo 'selected="selected"'; ?>>
			<?php echo $this->types[$i]->name; ?>
			</option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('CATEGORIES'); ?>:</td>
    <td colspan="2">
	<select name="catid[]" id="catid" multiple="multiple" size="5">
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('FTYPE'); ?>:</td>
    <td colspan="2">
	<select name="type" id="type" onchange="loadfieldoptions();">
    	<option value="0"><?php echo JText::_('PLSSELFTYP'); ?></option>
	<?php
	
		for($i=0;$i<count($this->fieldtypes);$i++)	{	?>
		
			<option value="<?php echo $this->fieldtypes[$i]->id; ?>" <?php if($this->fieldtypes[$i]->id==$this->item->type) echo 'selected="selected"'; ?>><?php echo JText::_($this->fieldtypes[$i]->label); ?></option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
     <span class="hasTip" title="<?php echo JText::_('FIELDTYPEDESC'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('DESCRIPTION'); ?>:</td>
    <td colspan="2">
    <textarea name="text" id="text" rows="5" cols="40"><?php echo $this->item->text; ?></textarea>
    <span class="hasTip" title="<?php echo JText::_('FIELDDESC'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span>
    </td>
  </tr> 
  <tr>
    <td class="key"><?php echo JText::_('FICON'); ?>:</td>
    <td colspan="2">
		<div id="fieldicon"></div>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('REQ'); ?>:</td>
    <td><input type="radio" name="required" id="required" value="1" <?php if($this->item->required) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="required" id="required" value="0" <?php if(!$this->item->required) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCCATLISTVIE'); ?>:</td>
    <td><input type="radio" name="category" id="category" value="1" <?php if($this->item->category) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="category" id="category" value="0" <?php if(!$this->item->category) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?> <span class="hasTip" title="<?php echo JText::_('FIELDCATEGORYTT'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCINENTLISV'); ?>:</td>
    <td><input type="radio" name="list" id="list" value="1" <?php if($this->item->list) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="list" id="list" value="0" <?php if(!$this->item->list) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?> <span class="hasTip" title="<?php echo JText::_('FIELDITEMLISTTT'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCINENTDETV'); ?>:</td>
    <td><input type="radio" name="detail" id="detail" value="1" <?php if($this->item->detail) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="detail" id="detail" value="0" <?php if(!$this->item->detail) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?> <span class="hasTip" title="<?php echo JText::_('FIELDITEMDETAILTT'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCINSERFORM'); ?>:</td>
    <td><input type="radio" name="search" id="search" value="1" <?php if($this->item->search) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="search" id="search" value="0" <?php if(!$this->item->search) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?> <span class="hasTip" title="<?php echo JText::_('FIELDSEARCHTT'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('DISPLAY'); ?>:</td>
    <td><input type="checkbox" name="showtitle" id="showtitle" value="1" <?php if($this->item->showtitle) echo 'checked="checked"'; ?> /> <?php echo JText::_('FTITLE'); ?> <input type="checkbox" name="showicon" id="showicon" value="1" <?php if($this->item->showicon) echo 'checked="checked"'; ?> /> <?php echo JText::_('FICON'); ?> <span class="hasTip" title="<?php echo JText::_('FIELDDISPLAYTT'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('CSSCSUF'); ?>:</td>
    <td>
    <input type="text" name="cssclass" id="cssclass" value="<?php echo $this->item->cssclass; ?>" size="40" /> <span class="hasTip" title="<?php echo JText::_('FIELDCLASSTT'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span>
    </td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PUBLISHED'); ?>:</td>
    <td><input type="radio" name="published" id="published" value="1" <?php if($this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?> <input type="radio" name="published" id="published" value="0" <?php if($this->item->published === 0) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?></td>
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
			
				echo '<option value="'.$this->order_list[$i]->ordering.'"';
				if($this->item->ordering == $this->order_list[$i]->ordering)
					echo ' selected="selected" ';
				echo '>'.$this->order_list[$i]->ordering.'::'.$this->order_list[$i]->name.'</option>';
			
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
</fieldset>

<div id="fieldoptions"></div>

</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="field" />
<input type="hidden" name="abase" id="abase" value="1" />
</form>


<div class="clr"></div>

</div>

</div>

</div>
<div class="clr"></div>