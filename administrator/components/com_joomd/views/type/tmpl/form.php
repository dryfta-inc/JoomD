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

$this->multiselect->initialize('form[name=\'adminform\'] select:not(#cats, #fields)', array('minWidth'=>150));

$this->multiselect->initialize('select#cats', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'noneSelectedText'=>JText::_('SELCATS')));

$this->multiselect->initialize('select#fields', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'noneSelectedText'=>JText::_('SELFIELDS')));

?>

<script type="text/javascript">
	
	function validateit()
	{
		
		var form = document.adminform;
		
		if(form.name.value == 0)
		{
			
			alert("<?php echo JText::_('PLSENTTIT'); ?>");
			return false;
			
		}
		
		if(form.appid.value == 0)
		{
			
			alert("<?php echo JText::_('PLSSELAPP'); ?>");
			return false;
			
		}
		
		return true;
		
	}

</script>


<div id="element-box">

<div class="m">

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=type" method="post" name="adminform" id="adminform" enctype="multipart/form-data">

<div class="width-60 fltlft">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'TYPSETAIL' ); ?></legend>
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
    <td class="key"><?php echo JText::_('DESCRIPTION'); ?>:</td>
    <td><textarea cols="40" rows="4" id="descr" name="descr"><?php echo $this->item->descr; ?></textarea></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('APP'); ?>:</td>
    <td colspan="2">
	<select name="appid" id="appid">
	<?php
	
		for($i=0;$i<count($this->apps);$i++)	{	?>
		
			<option value="<?php echo $this->apps[$i]->id; ?>" <?php if($this->apps[$i]->id==$this->item->appid) echo 'selected="selected"'; ?>>
			<?php echo JText::_($this->apps[$i]->label); ?>
			</option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCLUDE_CATS'); ?>:</td>
    <td colspan="2">
	<select name="cats[]" id="cats" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($this->cats);$i++)	{	?>
		
			<option value="<?php echo $this->cats[$i]->id; ?>" <?php if($this->cats[$i]->selected) echo 'selected="selected"'; ?>>
			<?php echo $this->cats[$i]->name; ?>
			</option>
		
	<?php	}	?>
	</select>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('INCLUDE_FIELDS'); ?>:</td>
    <td colspan="2">
	<select name="fields[]" id="fields" multiple="multiple" size="5">
	<?php
	
		for($i=0;$i<count($this->fields);$i++)	{	?>
		
			<option value="<?php echo $this->fields[$i]->id; ?>" <?php if($this->fields[$i]->selected) echo 'selected="selected"'; ?>>
			<?php echo $this->fields[$i]->name; ?>
			</option>
		
	<?php	}	?>
	</select>
	</td>
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

<div class="width-40 fltrt">

<?php

echo $this->pane->startpane('typeparam', array('collapsible'=>true));

	echo $this->pane->startPanel('config', JText::_('CONFIG_OPTIONS'));

?>
<fieldset class="panelform">
<ul class="adminformlist">
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_TEMPLATE'); ?>"><?php echo JText::_('TEMPLATE'); ?></label>
        <select name="config[template]" id="configtemplate">
        	<?php
				for($i=0;$i<count($this->themes);$i++)	{
					echo '<option value="'.$this->themes[$i]->id.'"';
					if($this->themes[$i]->id==$this->item->config->get('template'))
						echo ' selected="selected"';
					echo '>'.$this->themes[$i]->name.'</option>';
				}
			?>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_NOTIFY_ITEM_OWNER'); ?>"><?php echo JText::_('NOTIFY_ITEM_OWNER'); ?></label>
        <select name="config[notify]" id="confignotify">
        	<option value="1" <?php if($this->item->config->get('notify')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->config->get('notify')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_MODERATE_ITEMS'); ?>"><?php echo JText::_('MODERATE_ITEMS'); ?></label>
        <select name="config[moderate]" id="configmoderate">
        	<option value="1" <?php if($this->item->config->get('moderate')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->config->get('moderate')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_ITEM_LIST'); ?>"><?php echo JText::_('ITEM_LIST'); ?></label>
        <?php echo JHtml::_('access.assetgrouplist', 'config[list]', $this->item->config->get('list'), 'class="inputbox"') ?>
    </li>
    <li>
	    <label class="hasTip" title="<?php echo JText::_('TT_ITEM_DETAIL'); ?>"><?php echo JText::_('ITEM_DETAIL'); ?></label>
        <?php echo JHtml::_('access.assetgrouplist', 'config[detail]', $this->item->config->get('detail'), 'class="inputbox"') ?>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_META_OPTIONS'); ?>"><?php echo JText::_('SHOW_META_OPTIONS'); ?></label>
        <select name="config[meta]" id="configmeta1">
            <option value="1" <?php if($this->item->config->get('meta')==1) echo 'selected="selected"'; ?>><?php echo JText::_('NON'); ?></option>
            <option value="2" <?php if($this->item->config->get('meta')==2) echo 'selected="selected"'; ?>><?php echo JText::_('BACKEND'); ?></option>
            <option value="3" <?php if($this->item->config->get('meta')==3) echo 'selected="selected"'; ?>><?php echo JText::_('FRONTEND'); ?></option>
            <option value="4" <?php if($this->item->config->get('meta')==4) echo 'selected="selected"'; ?>><?php echo JText::_('BOTH'); ?></option>
        </select>
    </li>
    <li>
	    <label class="hasTip" title="<?php echo JText::_('TT_PUBLISH_OPTIONS'); ?>"><?php echo JText::_('SHOW_PUBLISH_OPTIONS'); ?></label>
        <select name="config[publishing]" id="configpublishing">
            <option value="1" <?php if($this->item->config->get('publishing')==1) echo 'selected="selected"'; ?>><?php echo JText::_('NON'); ?></option>
            <option value="2" <?php if($this->item->config->get('publishing')==2) echo 'selected="selected"'; ?>><?php echo JText::_('BACKEND'); ?></option>
            <option value="3" <?php if($this->item->config->get('publishing')==3) echo 'selected="selected"'; ?>><?php echo JText::_('FRONTEND'); ?></option>
            <option value="4" <?php if($this->item->config->get('publishing')==4) echo 'selected="selected"'; ?>><?php echo JText::_('BOTH'); ?></option>
        </select>
    </li>
</ul>
</fieldset>
<?php
	
	echo $this->pane->endPanel();
	
	echo $this->pane->startPanel('acl', JText::_('ACL_OPTIONS'));

?>
<fieldset class="panelform">
<ul class="adminformlist">
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_ADD_ITEM'); ?>"><?php echo JText::_('ADD_ITEM'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[addaccess][]', $this->item->acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_EDIT_OWN_ITEM'); ?>"><?php echo JText::_('EDIT_OWN_ITEM'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[editaccess][]', $this->item->acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
        <label class="hasTip" title="<?php echo JText::_('TT_EDIT_ALL_ITEM'); ?>"><?php echo JText::_('EDIT_ALL_ITEM'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[editall][]', $this->item->acl->get('editall'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_EDIT_OWN_STATE'); ?>"><?php echo JText::_('EDIT_OWN_STATE'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[stateaccess][]', $this->item->acl->get('stateaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
        <label class="hasTip" title="<?php echo JText::_('TT_EDIT_ALL_STATE'); ?>"><?php echo JText::_('EDIT_ALL_STATE'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[stateall][]', $this->item->acl->get('stateall'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
        <label class="hasTip" title="<?php echo JText::_('TT_DELETE_OWN_ITEM'); ?>"><?php echo JText::_('DELETE_OWN_ITEM'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[deleteaccess][]', $this->item->acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_DELETE_ALL_ITEM'); ?>"><?php echo JText::_('DELETE_ALL_ITEM'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[deleteall][]', $this->item->acl->get('deleteall'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_FEATURE_OWN_ITEM'); ?>"><?php echo JText::_('FEATURE_OWN_ITEM'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[featureaccess][]', $this->item->acl->get('featureaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('TT_FEATURE_ALL_ITEM'); ?>"><?php echo JText::_('FEATURE_ALL_ITEM'); ?></label>
        <?php echo JHtml::_('access.usergroup', 'acl[featureall][]', $this->item->acl->get('featureall'), 'class="multiple" multiple="multiple" size="5"', false) ?>
    </li>
</ul>
</fieldset>
<?php
	
	echo $this->pane->endPanel();
	
	echo $this->pane->startPanel('list', JText::_('LIST_LAYOUT_OPTIONS'));
	
?>

<fieldset class="panelform">
<ul class="adminformlist">
	<li>
	    <label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_HEADER'); ?>"><?php echo JText::_('SHOWHEAD'); ?></label>
        <select name="listconfig[header]" id="listconfigheader">
        	<option value="1" <?php if($this->item->listconfig->get('header')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('header')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MORE_BUTTON'); ?>"><?php echo JText::_('MOREINFOBUTT'); ?></label>
        <select name="listconfig[more]" id="listconfigmore">
        	<option value="1" <?php if($this->item->listconfig->get('more')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('more')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_RSS_FEED_BUTTON'); ?>"><?php echo JText::_('RSS_FEED_BUTTON'); ?></label>
        <select name="listconfig[rss]" id="listconfigrss">
        	<option value="1" <?php if($this->item->listconfig->get('rss')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('rss')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_ADD_BUTTON'); ?>"><?php echo JText::_('SHOWADDBUTTON'); ?></label>
        <select name="listconfig[add]" id="listconfigadd">
        	<option value="1" <?php if($this->item->listconfig->get('add')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('add')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CONTACT_FORM'); ?>"><?php echo JText::_('DISPLAYCONTACT_FORM'); ?></label>
        <select name="listconfig[contact]" id="listconfigcontact">
        	<option value="1" <?php if($this->item->listconfig->get('contact')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('contact')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_REPORT_ITEM'); ?>"><?php echo JText::_('DISPLAYREPORT_ITEM'); ?></label>
        <select name="listconfig[report]" id="listconfigreport">
        	<option value="1" <?php if($this->item->listconfig->get('report')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('report')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_SAVE_ITEM'); ?>"><?php echo JText::_('DISPLAYSAVE_ITEM'); ?></label>
        <select name="listconfig[save]" id="listconfigsave">
        	<option value="1" <?php if($this->item->listconfig->get('save')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('save')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_HITS'); ?>"><?php echo JText::_('DISPLAYHITS'); ?></label>
        <select name="listconfig[hits]" id="listconfighits">
        	<option value="1" <?php if($this->item->listconfig->get('hits')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('hits')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CREATION_DATE'); ?>"><?php echo JText::_('DISPLAYCREATION_DATE'); ?></label>
        <select name="listconfig[created]" id="listconfigcreated">
        	<option value="1" <?php if($this->item->listconfig->get('created')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('created')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_AUTHOR'); ?>"><?php echo JText::_('DISPLAYAUTHOR'); ?></label>
        <select name="listconfig[author]" id="listconfigauthor">
        	<option value="1" <?php if($this->item->listconfig->get('author')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('author')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_DATE'); ?>"><?php echo JText::_('DISPLAYMODIFIED_DATE'); ?></label>
        <select name="listconfig[modified]" id="listconfigmodified">
        	<option value="1" <?php if($this->item->listconfig->get('modified')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('modified')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_BY'); ?>"><?php echo JText::_('DISPLAYMODIFIED_BY'); ?></label>
        <select name="listconfig[modified_by]" id="listconfigmodified_by">
        	<option value="1" <?php if($this->item->listconfig->get('modified_by')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('modified_by')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_PRINT_ICON'); ?>"><?php echo JText::_('PRINTI'); ?></label>
        <select name="listconfig[print]" id="listconfigprint">
        	<option value="1" <?php if($this->item->listconfig->get('print')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('print')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_EMAIL_ICON'); ?>"><?php echo JText::_('EMAILI'); ?></label>
        <select name="listconfig[email]" id="listconfigemail">
        	<option value="1" <?php if($this->item->listconfig->get('email')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->listconfig->get('email')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
</ul>
</fieldset>

<?php
	
	echo $this->pane->endPanel();
	
	echo $this->pane->startPanel('detail', JText::_('DETAIL_LAYOUT_OPTIONS'));
	
?>

<fieldset class="panelform">
<ul class="adminformlist">
	<li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_ADD_BUTTON'); ?>"><?php echo JText::_('SHOWADDBUTTON'); ?></label>
        <select name="detailconfig[add]" id="detailconfigadd">
        	<option value="1" <?php if($this->item->detailconfig->get('add')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('add')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CONTACT_FORM'); ?>"><?php echo JText::_('DISPLAYCONTACT_FORM'); ?></label>
        <select name="detailconfig[contact]" id="detailconfigcontact">
        	<option value="1" <?php if($this->item->detailconfig->get('contact')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('contact')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_REPORT_ITEM'); ?>"><?php echo JText::_('DISPLAYREPORT_ITEM'); ?></label>
        <select name="detailconfig[report]" id="detailconfigreport">
        	<option value="1" <?php if($this->item->detailconfig->get('report')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('report')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_SAVE_ITEM'); ?>"><?php echo JText::_('DISPLAYSAVE_ITEM'); ?></label>
        <select name="detailconfig[save]" id="detailconfigsave">
        	<option value="1" <?php if($this->item->detailconfig->get('save')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('save')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_HITS'); ?>"><?php echo JText::_('DISPLAYHITS'); ?></label>
        <select name="detailconfig[hits]" id="detailconfighits">
        	<option value="1" <?php if($this->item->detailconfig->get('hits')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('hits')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CREATION_DATE'); ?>"><?php echo JText::_('DISPLAYCREATION_DATE'); ?></label>
        <select name="detailconfig[created]" id="detailconfigcreated">
        	<option value="1" <?php if($this->item->detailconfig->get('created')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('created')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_AUTHOR'); ?>"><?php echo JText::_('DISPLAYAUTHOR'); ?></label>
        <select name="detailconfig[author]" id="detailconfigauthor">
        	<option value="1" <?php if($this->item->detailconfig->get('author')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('author')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_DATE'); ?>"><?php echo JText::_('DISPLAYMODIFIED_DATE'); ?></label>
        <select name="detailconfig[modified]" id="detailconfigmodified">
        	<option value="1" <?php if($this->item->detailconfig->get('modified')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('modified')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_BY'); ?>"><?php echo JText::_('DISPLAYMODIFIED_BY'); ?></label>
        <select name="detailconfig[modified_by]" id="detailconfigmodified_by">
        	<option value="1" <?php if($this->item->detailconfig->get('modified_by')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('modified_by')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
    <li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_PRINT_ICON'); ?>"><?php echo JText::_('PRINTI'); ?></label>
        <select name="detailconfig[print]" id="detailconfigprint">
        	<option value="1" <?php if($this->item->detailconfig->get('print')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('print')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
	<li>
    	<label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_EMAIL_ICON'); ?>"><?php echo JText::_('EMAILI'); ?></label>
        <select name="detailconfig[email]" id="detailconfigemail">
        	<option value="1" <?php if($this->item->detailconfig->get('email')) echo 'selected="selected"'; ?>><?php echo JText::_('YS'); ?></option>
            <option value="0" <?php if(!$this->item->detailconfig->get('email')) echo 'selected="selected"'; ?>><?php echo JText::_('NS'); ?></option>
        </select>
    </li>
</ul>
</fieldset>

<?php
	
	echo $this->pane->endPanel();

	echo $this->pane->endPane();
?>

</div>

<div class="clr"></div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="type" />
<input type="hidden" name="abase" id="abase" value="1" />
</form>

</div>
</div>

</div>