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

if(!empty($this->item->img) and is_file(JPATH_SITE.'/images/joomd/'.$this->item->img))	{
	$file->name = $this->item->img;
	$file->url = JURI::root().'images/joomd/'.$this->item->img;
	$file->thumbnail_url = JURI::root().'images/joomd/thumbs/'.$this->item->img;
	$file->delete_url = 'index.php?option=com_joomd&view=category&task=delete_img&id='.$this->item->id.'&abase=1';
	array_push($files, $file);
}


Joomdui::uploadfile('#catimg', array('fieldname'=>'img', 'buttontext'=>JText::_('ADDIMG'), 'files'=>$files));

$this->multiselect->initialize('select#types', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'click'=>'loaditems', 'checkAll'=>'loaditems', 'uncheckAll'=>'loaditems', 'noneSelectedText'=>JText::_('SELTYPES')));
$this->multiselect->initialize('select#fields', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'noneSelectedText'=>JText::_('SELFIELDS')));

$this->multiselect->initialize('form[name=\'adminform\'] select:not(#types, #fields)', array('minWidth'=>150));

?>

<script type="text/javascript">
	
	
	$jd(document).ready(function()	{
								 		
		var types = new Array();
		$jd('select[name="types[]"] option:selected').each(function() {
			types.push($jd(this).val());
		});
		
		loaditems(types, 'click', null);
									
	});
	
	function validateit()
	{
		
		var form = document.adminform;
		
		if(form.name.value == "")	{
			alert("<?php echo JText::_('PLSENTCATN'); ?>");
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
		
		return true;
		
	}
	
	function loaditems(types, event, ui)
	{
		
		loadcats(types);
		loadfields(types);
		
	}
	
	function loadcats(checked)
	{
		
		if(!checked)	{
			var checked=new Array();
		}
		
		if(checked.length > 0)	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'category', 'task':'reloadcats', 'types[]':checked, 'cid[]':<?php echo (int)$this->item->id; ?>, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('select#parent').html(res.list);
					$jd('select#parent').multiselect("refresh");
				}
				else	{
					alert(res.error);
				}
					
			  },
			  error: function()	{
				  alert('error');				  
			  }
		});
		
		}
		else	{
			$jd('select#parent').html('');
			$jd('select#parent').multiselect("refresh");
		}
		
	}
	
	function loadfields(checked)
	{
		
		if(!checked)	{
			var checked=new Array();
		}
		
		if(checked.length > 0)	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'category', 'task':'loadfields', 'types[]':checked, 'cid[]':<?php echo (int)$this->item->id; ?>, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('select#fields').html(res.list);
					$jd('select#fields').multiselect("refresh");
				}
				else	{
					alert(res.error);
				}
					
			  },
			  error: function()	{
				  alert('error');				  
			  }
		});
		
		}
		else	{
			$jd('select#fields').html('');
			$jd('select#fields').multiselect("refresh");
		}
		
	}

</script>

<div id="element-box">

<div class="m">

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=category" method="post" name="adminform" id="adminform" enctype="multipart/form-data">
<div class="col101">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'CATDETAILS' ); ?></legend>
<table class="admintable">
  <tr>
    <td class="key"><?php echo JText::_('TITLE'); ?>:</td>
    <td><input type="text" name="name" id="name" value="<?php echo $this->item->name; ?>" size="40" /> <em class="required">*</em></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('ALIAS'); ?>:</td>
    <td><input type="text" name="alias" id="alias" value="<?php echo $this->item->alias; ?>" size="40" /> <span class="hasTip" title="<?php echo JText::_('ALIASFIELDINFO'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="top" /></span></td>
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
    <td class="key"><?php echo JText::_('PCAT'); ?>:</td>
    <td colspan="2">
	<select name="parent" id="parent">
	</select>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCLUDE_FIELDS'); ?>:</td>
    <td colspan="2">
	<select name="fields[]" id="fields" multiple="multiple" size="5">
	</select>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INTROTEXT'); ?>:</td>
    <td colspan="2"><textarea name="introtext" id="introtext"><?php echo $this->item->introtext; ?></textarea></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('DESCRIPTION'); ?>:</td>
    <td colspan="2" valign="middle"><textarea id="fulltext" name="fulltext" rows="8" cols="75"><?php echo $this->item->fulltext; ?></textarea>
	<?php
		$editor = Joomdui::getEditor();
		$editor->initialize( '#fulltext' ) ;
	?></td>
  </tr>
  
  <tr>
    <td class="key"><?php echo JText::_('CATIMG'); ?>:</td>
    <td colspan="2">
		<div id="catimg"></div>
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
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="category" />
<input type="hidden" name="abase" id="abase" value="1" />
</form>


<div class="clr"></div>

</div>

</div>

</div>
<div class="clr"></div>