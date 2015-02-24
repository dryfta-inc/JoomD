<?php
/*------------------------------------------------------------------------
# com_joomd - JoomD CSV Application
# ------------------------------------------------------------------------
# author    Noorullah Kalim - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/


defined('_JEXEC') or die('Restricted access');

$this->multiselect->initialize('select#types', array('header'=>true, 'filter'=>true, 'multiple'=>true, 'click'=>'loadcats', 'checkAll'=>'loadcats', 'uncheckAll'=>'loadcats', 'noneSelectedText'=>JText::_('SELTYPES')));

$this->multiselect->initialize('select#cats', array('header'=>true, 'filter'=>true, 'multiple'=>true, 'click'=>'loadfields', 'checkAll'=>'loadfields', 'uncheckAll'=>'loadfields', 'noneSelectedText'=>JText::_('SELCATS')));

$this->multiselect->initialize('select#fields', array('header'=>true, 'filter'=>true, 'multiple'=>true, 'click'=>'showbutton', 'checkAll'=>'showbutton', 'uncheckAll'=>'hidebutton', 'noneSelectedText'=>JText::_('SELFIELDS')));

?>

<script>

function showbutton(){
$jd(".exportit").show();
}
function hidebutton(){
$jd(".exportit").hide();
}

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
			  data: {'option':'com_joomd', 'view':'csv', 'task':'list_category', 'ids':checked, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
				
					$jd('select#cats').html(res.html);
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
	
 function loadfields(checked, event, ui)
	{
		
		if(!checked)	{
			var checked=new Array();
		}
		
		if(checked.length > 0)	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'csv', 'task':'list_field', 'ids':checked, "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  beforeSend: function()	{
				$jd(".poploadingbox").show();
			  },
			  complete: function()	{
				$jd(".poploadingbox").hide();
			  },
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('select#fields').html(res.html);
					$jd('select#fields').multiselect("refresh");
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
			$jd('select#fields').html('');
			$jd('select#fields').multiselect("refresh");
		}
		
	}


</script>

<div id="joomdpanel">

<div class="csvpanel">
	<table border="0" width="100%">
		<tr>
        	<td valign="top" width="50%">
                <fieldset class="adminform">
					<legend><?php echo JText::_( 'EXPORT' ); ?></legend>
                    <form action="index.php?option=com_joomd&view=csv" method="post" name="adminform" id="adminform" enctype="multipart/form-data">
                    <div class="selector">
                        <div class="stitle"><?php echo JText::_('SELECT_TYPE'); ?></div>
                        <select id="types" name="types[]" multiple="multiple">
                        <?php
                        for($i=0;$i<count($this->type);$i++){
                        $type = $this->type[$i];
                        echo "<option value=".$type->id.">".$type->name."</option>";
                        } ?>
                        </select>
                    </div>
                    
                    <div class="selector">
                        <div class="stitle"><?php echo JText::_('SELECT_CATEGORY'); ?></div>
                        <select id="cats" name="cats[]" multiple="multiple">
                        </select>
                    </div>
                    
                    <div class="selector">
                        <div class="stitle"><?php echo JText::_('SELECT_FIELD'); ?></div>
                        <select id="fields" name="fields[]" multiple="multiple">
                        </select>
                    </div>
                    
                    <input type="hidden" name="option" value="com_joomd" />
                    <input type="hidden" name="task" value="export_data" />
                    <input type="hidden" name="controller" value="csv" />
                    
                    <div class="exportit" style="display:none"><input type="submit" name="submit" value="Export Data" /></div>
                    
                    </form>
                </fieldset>
            </td>
			<td valign="top" width="50%">
            	<fieldset class="adminform">
					<legend><?php echo JText::_( 'IMPORT' ); ?></legend>
                    
                    <form action="index.php?option=com_joomd&view=csv" method="post" enctype="multipart/form-data" name="adminForm">  
                        <input type="file" name="file" id="file" /> &nbsp;
                        
                        <input type="submit" name="import" value="Import Data" class="import_button" />
                        <input type="hidden" name="task" id="task" value="import_data" />
                        <?php echo JHTML::_( 'form.token' ); ?>
                    </form>
				</fieldset>
			</td>
		</tr>
	</table>
    
</div>
</div>
<div class="clr"></div>