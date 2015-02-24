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

Joomdui::iButton('#joomdpanel');

$this->multiselect->initialize('form[name=\'configform\'] select:not(.multiple)', array('minWidth'=>150));

$this->multiselect->initialize('form[name=\'configform\'] select.multiple', array('header'=>true, 'multiple'=>true, 'noneSelectedText'=>JText::_('SELECT_GROUPS')));

?>

<script type="text/javascript">
	
	$jd(function() {
        
        $jd("form[name='configform']").submit(function() {
          return false;
        });
				
	});
	
	function listItemTask(task)
	{
		
		var data = $jd("form[name='configform']").serializeArray();
                
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: data,
			  beforeSend: function()	{
				$jd(".loadingblock").show();
			  },
			  complete: function()	{
				$jd(".loadingblock").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')
					displayalert(res.msg, 'message', false);
				else
					displayalert(res.error, 'error', false);
					
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);                 
			  }
		});
		
	}

</script>

<div id="joomdpanel">

<form action="index.php?option=com_joomd&view=config" method="post" name="configform" id="configform" enctype="multipart/form-data">
       
<?php

	echo $this->tabs->startPane('configtab', array('cookie'=>true));
	
	echo $this->tabs->startPanel('general', JText::_('GENERAL'));
	
	?>
	<table border="0" width="100%">
	<tr>
    	<td valign="top" width="35%">
        	<fieldset class="adminform">
			<legend><?php echo JText::_( 'GENERALCONFIG' ); ?></legend>
            	<table class="admintable">
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGTEMPLATE'); ?>"><?php echo JText::_('SELTEMP'); ?></label></td>
                    <td>
                        <select name="template" id="template">
                            <?php
                                for($i=0;$i<count($this->themes);$i++)	{
                                    echo '<option value="'.$this->themes[$i]->id.'"';
                                    if($this->themes[$i]->id==$this->config->template)
                                        echo ' selected="selected"';
                                    echo '>'.$this->themes[$i]->name.'</option>';
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGSCROLL'); ?>"><?php echo JText::_('SCROOLLOADING'); ?></label></td>
                    <td><input type="checkbox" name="scroll" id="scroll" <?php if($this->config->scroll) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGCAPTCHA'); ?>"><?php echo JText::_('CAPTCHA'); ?></label></td>
                    <td><input type="checkbox" name="captcha" id="captcha" <?php if($this->config->captcha) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
                 <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGNOTIFYEMAIL'); ?>"><?php echo JText::_('NOTIFIEMAIL'); ?></label></td>
                    <td><input type="text" name="email" id="email" value="<?php echo $this->config->email; ?>" />
                    </td>
                </tr>
                 <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGTHUMBWIDTH'); ?>"><?php echo JText::_('TWIDTH'); ?></label></td>
                    <td><input type="text" name="thumb_width" id="thumb_width" value="<?php echo $this->config->thumb_width; ?>" /></td>
                </tr>
                 <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGTHUMBHEIGHT'); ?>"><?php echo JText::_('THUMHEIGHT'); ?></label></td>
                    <td><input type="text" name="thumb_height" id="thumb_height" value="<?php echo $this->config->thumb_height; ?>" />
                    </td>
                </tr>
            	<tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGASEARCH'); ?>"><?php echo JText::_('ASEARCH'); ?></label></td>
                    <td><input type="checkbox" name="asearch" id="asearch" <?php if($this->config->asearch) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIGCOPYRIGHT'); ?>"><?php echo JText::_('COPYRIGHT_INFO'); ?></label></td>
                    <td><input type="checkbox" name="copyright" id="copyright" <?php if($this->config->copyright) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
            </table>
            </fieldset>
        </td>
        <td valign="top">
        	<fieldset class="adminform">
            <legend><?php echo JText::_( 'SOCIALPLUGINCONFIG' ); ?></legend>
            <table class="admintable">
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_SOCIAL'); ?>"><?php echo JText::_('DISPLAYSOCIALBUTTONS'); ?></label></td>
                    <td><input type="checkbox" name="social[share]" id="share" <?php if($this->config->social->get('share')) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_FBLIKE'); ?>"><?php echo JText::_('DISPLAYFBLIKE'); ?></label></td>
                    <td><input type="checkbox" name="social[fblike]" id="fblike" <?php if($this->config->social->get('fblike')) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_TWEET'); ?>"><?php echo JText::_('DISPLAYTWEET'); ?></label></td>
                    <td><input type="checkbox" name="social[tweet]" id="tweet" <?php if($this->config->social->get('tweet')) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_GPLUS'); ?>"><?php echo JText::_('DISPLAYGPLUS'); ?></label></td>
                    <td><input type="checkbox" name="social[gplus]" id="gplus" <?php if($this->config->social->get('gplus')) echo 'checked="checked"'; ?> value="1" />
                    </td>
                </tr>
            </table>
            </fieldset>
        </td>
    </tr>
    </table>
	
	<?php
	
	echo $this->tabs->endPanel();
	
	for($i=0;$i<count($this->panels);$i++)	{
		
		echo $this->tabs->startPanel('config'.$this->panels[$i]->name, $this->panels[$i]->label);
		
		echo $this->panels[$i]->html;
		
		echo $this->tabs->endPanel();
		
	}
	
	echo $this->tabs->endPane();

?>        
            
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="config" />
<input type="hidden" name="abase" value="1" />

</form>
	
<div class="clr"></div></div>