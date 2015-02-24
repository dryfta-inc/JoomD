<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2011 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/index.php?option=com_ccboard&view=forumlist&Itemid=63
-----------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<script type="text/javascript">
	
	$jd(function()	{
	
		$jd('div.toolbuttons a').live('click', function(event)	{		
		
		var task = $jd(this).attr('rel');
		$jd('input[name="task"]').val(task);
		
		if($jd('select[name="typeid"]').val() == 0)	{
			alert("<?php echo JText::_('PLZSELECTTYPE'); ?>");
			return false;
		}
		
		if($jd('select[name="catid"]').val() == 0)	{
			alert("<?php echo JText::_('PLZSELECTCAT'); ?>");
			return false;
		}
		
		if($jd('input[name="subject"]').val() == '')	{
			alert("<?php echo JText::_('PLSENTERSUBJECT'); ?>");
			return false;
		}
		
		tinyMCE.activeEditor.save();
				
		if($jd('textarea[name="body"]').val() == '')	{
			alert("<?php echo JText::_('PLSENTEREMAILBODY'); ?>");
			return false;
		}
		
		if(task == 'send_newsletter')	{
			
			if($jd('input[name="id"]').val() == 0)	{
				alert("<?php echo JText::_('PLEASESAVENEWSLETTER'); ?>");
				return false;
			}
			
			var data = {'option':'com_joomd', 'view':'newsletter', 'task':task, 'id':$jd('input[name="id"]').val(), 'abase':1, "<?php echo jutility::getToken(); ?>":1};
			
		}
		else
			var data = $jd("form[name='adminform']").serializeArray();
		
        
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
				
				if(res.result == 'success')	{
					if(res.id)
						$jd('input[name="id"]').val(res.id);
					displayalert(res.msg, 'message', false);
				}
				else
					displayalert(res.error, 'error', false);
					
			  },
			  error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);                 
			  }
		});
		
		});
	
	});
	
	function loadcats(e)
	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'newsletter', 'task':'get_cats', 'filter_type':e.value, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
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
	
	function loadtemplate()
	{
		var form = document.adminform;
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'newsletter', 'task':'get_temp', 'typeid':form.typeid.value, 'catid':form.catid.value, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('input[name="id"]').val(res.id);
					$jd('input[name="subject"]').val(res.subject);
					tinyMCE.activeEditor.setContent(res.body);
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

<form action="index.php?option=com_joomd&view=newsletter" method="post" name="adminform" id="adminform" enctype="multipart/form-data">
<div class="col101">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'NEWSLETTERTEMPLATES' ); ?></legend>

<table width="100%">
<tr>
<td valign="top" width="65%">

<table class="admintable">
<tr>
	<td colspan="2" align="right"><div class="toolbuttons"><a class="icon-32-save gridicons" title="<?php echo JText::_('SAVE'); ?>" rel="save"></a></div> <div class="toolbuttons"><a class="icon-32-syncemail gridicons" title="<?php echo JText::_('SENDNEWSLETTER'); ?>" rel="send_newsletter"></a></div></td>
</tr>
  <tr>
    <td class="key"><?php echo JText::_('TYPE'); ?>:</td>
    <td colspan="2">
	<select name="typeid" id="typeid" onChange="loadcats(this);">
    <option value="0"><?php echo JText::_( 'SELECTTYPE' ); ?></option>
	<?php
	
		for($i=0;$i<count($this->types);$i++)	{	?>
		
			<option value="<?php echo $this->types[$i]->id; ?>">
			<?php echo $this->types[$i]->name; ?>
			</option>
		
	<?php	}	?>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('CATEGORIES'); ?>:</td>
    <td colspan="2">
	<select name="catid" id="catid" onchange="loadtemplate();">
    <option value="0"><?php echo JText::_('SELCAAT'); ?></option>
	</select> <em class="required">*</em>
	</td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('SUBJECT'); ?>:</td>
    <td><input type="text" name="subject" id="subject" value="" size="40" /> <em class="required">*</em></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('TEMPLATE'); ?>:</td>
    <td colspan="2">
    <?php
    	$editor =& JFactory::getEditor();
		echo $editor->display( 'body',  null, '550', '300', '60', '20', array('readmore', 'pagebreak')) ;
	?>
    </td>
  </tr> 
</table>
</td>
<td valign="top" width="35%" align="right">
	<table class="adminlist">
    	<tr>
        	<th colspan="2"><?php echo JText::_('KEYWORDSTEXT'); ?></th>
        </tr>
        <tr>
        	<td class="key">{sitename}</td>
            <td><?php echo JText::_('SITENAME'); ?></td>
        </tr>
        <tr>
        	<td class="key">{siteurl}</td>
            <td><?php echo JText::_('SITEURL'); ?></td>
        </tr>
        <tr>
        	<td class="key">{email}</td>
            <td><?php echo JText::_('EMAIL'); ?></td>
        </tr>
    	<tr>
        	<td class="key">{typename}</td>
            <td><?php echo JText::_('TYPENAME'); ?></td>
        </tr>
        <tr>
        	<td class="key">{catname}</td>
            <td><?php echo JText::_('CATNAME'); ?></td>
        </tr>
        <tr>
        	<td class="key">{itemlistlink}</td>
            <td><?php echo JText::_('ITEMLISTLINK'); ?></td>
        </tr>
    </table>
</td>
</tr>
</table>
</fieldset>
</div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="newsletter" />
<input type="hidden" name="abase" id="abase" value="1" />
</form>