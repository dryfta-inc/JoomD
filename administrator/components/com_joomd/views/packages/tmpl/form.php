<?php
/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Mohammad arshi - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if($this->abase)
	require_once(JPATH_ADMINISTRATOR.'/components/com_joomd/helpers/common_header.php');

$this->multiselect->initialize('select#unit', array('minWidth'=>100));
$this->multiselect->initialize('form[name=\'adminform\'] select:not(#unit, #types, #cats)');

$this->multiselect->initialize('select#types', array('header'=>true, 'multiple'=>true, 'click'=>'loadcats', 'checkAll'=>'loadcats', 'uncheckAll'=>'loadcats', 'noneSelectedText'=>JText::_('SELTYPES')));

$this->multiselect->initialize('select#cats', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'noneSelectedText'=>JText::_('SELCAT')));

?>
<script type="text/javascript" >

	$jd(document).ready(function()	{
								 				
		var types = new Array();
		$jd('select[id="types"] option:selected').each(function() {
			types.push($jd(this).val());
		});
		
		loadcats(types, 'click', null);
											
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
			  data: {'option':'com_joomd', 'view':'packages', 'task':'loadcats', 'types[]':checked, 'cid[]':<?php echo (int)$this->item->id; ?>, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('select#cats').html(res.list);
					$jd('select#cats').multiselect("refresh");
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
			$jd('select#cats').html('');
			$jd('select#cats').multiselect("refresh");
		}
		
	}

//form validation
function validateit(button)
{
		form=document.adminform;
				
		if(form.name.value==""){
			alert("<?php echo JText::_("PENTERPACKAGENAME"); ?>");
			return false;
		}
		
		else if(form.amount.value==""){
			alert("<?php echo JText::_("PENTERAMOUNT"); ?>");
			return false;
		}
		else if(isNaN(form.amount.value)){
			alert("<?php echo JText::_("PENTERAMOUNTDIGIT"); ?>");
			return false;
		}
		else if(form.items.value==""){
			alert("<?php echo JText::_("PENTERNUOFITEMS"); ?>");
			return false;
		}
		else if(isNaN(form.items.value)){
			alert("<?php echo JText::_("PENTERAVALIDNUINITEMS"); ?>");
			return false;
		}
		else if(form.period.value=="" || form.period.value==0){
			alert("<?php echo JText::_("PENTERAPERIODGTHANZERO"); ?>");
			return false;
		}
		else if(isNaN(form.period.value)){
			alert("<?php echo JText::_("PENTERAVALIDNUINDAYS"); ?>");
			return false;
		}
		else{
			return true;
		}
}

</script>
<div id="element-box">
  <div class="t">
    <div class="t">
      <div class="t"></div>
    </div>
  </div>
<div class="m">

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=packages" method="post" name="adminform" id="adminform" enctype="multipart/form-data" >
<div class="col101">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'SUBSPACKDETAILS' ); ?></legend>
<table class="admintable">
	
  <tr>
    <td class="key"><?php echo JText::_('PACKAGENAME'); ?>:</td>
    <td colspan="2"><input type="text" name="name" id="name" size="40" maxlength="100" value="<?php echo $this->item->name; ?>" /> <em class="required">*</em></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('AMOUNT'); ?>:</td>
    <td colspan="2"><input type="text" name="amount" id="amount" size="40" maxlength="100" value="<?php echo $this->item->amount; ?>" /> <em class="required">*</em></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('ITEMS'); ?>:</td>
    <td colspan="2">
    	<input type="text" name="items" id="items" size="20" maxlength="10" value="<?php echo $this->item->items; ?>" /> <em class="required">*</em> <span class="hasTip" title="<?php echo JText::_('ENTERNOOFCOOUPONHMUN'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="absbottom" /></span>
    </td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('VALIDITY'); ?>:</td>
    <td colspan="2">
    	<input type="text" name="period" id="period" size="20" maxlength="3" value="<?php echo $this->item->period; ?>" />&nbsp;
        <select name="unit" id="unit">
        	<option value="D" <?php if($this->item->unit == "D") echo 'selected="selected"'; ?>><?php echo JText::_('DAYS'); ?></option>
            <option value="M" <?php if($this->item->unit == "M") echo 'selected="selected"'; ?>><?php echo JText::_('MONTHS'); ?></option>
            <option value="W" <?php if($this->item->unit == "W") echo 'selected="selected"'; ?>><?php echo JText::_('WEEKS'); ?></option>
            <option value="Y" <?php if($this->item->unit == "Y") echo 'selected="selected"'; ?>><?php echo JText::_('YEARS'); ?></option>
        </select> <em class="required">*</em>
        <span class="hasTip" title="<?php echo JText::_('ENTERNOOFDFMU'); ?>"><img src="components/com_joomd/assets/images/icon-16-info.png" border="0" alt="<?php echo JText::_('INFO'); ?>" align="absbottom" /></span>
    </td>
  </tr>
  <tr>
  	<td class="key"><?php echo JText::_('INCLUDE_TYPES'); ?>:</td>
    <td colspan="2">
    	<select name="params[types][]" id="types" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($this->types);$i++)	{	
			
			$types = (array)$this->item->params->get('types');
			
	?>
			
			<option value="<?php echo $this->types[$i]->id; ?>" <?php if(in_array($this->types[$i]->id, $types)) echo 'selected="selected"'; ?>>
			<?php echo $this->types[$i]->name; ?>
			</option>
		
	<?php	}	?>
	</select>
    </td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCLUDE_CATS'); ?>:</td>
    <td colspan="2">
	<select name="params[cats][]" id="cats" multiple="multiple" size="5">
	</select>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCLUDE_FEATURED'); ?>:</td>
    <td colspan="2">
	<input type="radio" name="params[featured]" id="featured1" value="1" <?php if($this->item->params->get('featured')) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?>
	<input type="radio" name="params[featured]" id="featured0" value="0" <?php if(!$this->item->params->get('featured')) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PUBLISHED'); ?>:</td>
    <td colspan="2">
	<input type="radio" name="published" value="1" <?php if($this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('YS'); ?>
	<input type="radio" name="published" value="0" <?php if(!$this->item->published) echo 'checked="checked"'; ?> /> <?php echo JText::_('NS'); ?>
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
<input type="hidden" name="view" value="packages" />
<input type="hidden" name="abase" value="1" />
</form>


<div class="clr"></div>

</div>

</div>
  <div class="b">
    <div class="b">
      <div class="b"></div>
    </div>
  </div>
</div>
<div class="clr"></div>