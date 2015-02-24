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

// No direct access
defined('_JEXEC') or die('Restricted access');

$item = $this->item;

?>

<script type="text/javascript">

	$jd(function()	{
				 
		<?php if($this->type->detailconfig->get('print')) :	?>
					
		$jd('span.icon_print').live('click', function(event)	{

		$jd('.dialogbox').dialog({
			width:900,
			height:600,
			open: function(event, ui)	{
				
				$jd.ajax({
                      url: "<?php echo JURI::root(); ?>index.php",
                      type: "POST",
                      dataType:"json",
                      data: {'option':'com_joomd', 'view':'item', 'task':'printone', 'layout':'detail', 'typeid':<?php echo (int)$item->typeid; ?>, 'id':<?php echo $item->id; ?>},
                      beforeSend: function()	{
                        $jd(".loadingblock").show();
                      },
                      complete: function()	{
                        $jd(".loadingblock").hide();
                      },
                      success: function(res)	{
                        
                      	if(res.result == "success")	{
							$jd(".dialogbox").html(res.html);
						}
						else
							displayalert(res.error, "error");
                      },
					  error: function(jqXHR, textStatus, errorThrown)	{
						$jd('.dialogbox').dialog('close');
                        displayalert(textStatus, "error");
                      }
                });
				
			}
		});
		
		});
		
		<?php endif; ?>
		
		<?php	if($this->type->detailconfig->get('save') and !$this->user->get('guest'))	:	?>
		
		$jd('.item_info_bar .save_pan a').live('click', function(event)	{
			
			var that = this;
			var html = $jd(that).html();
			
			
			$jd.ajax({
				url: "<?php echo JURI::root(); ?>index.php",
				type: "POST",
				dataType:'json',
				data: {'option':'com_joomd', 'view':'item', 'task':$jd(that).attr('class'), 'typeid':<?php echo $this->type->id; ?>, 'id':$jd(that).attr('rel'), 'abase':1, '<?php echo jutility::getToken(); ?>':1},
				beforeSend: function()	{
					$jd(that).html('<span class="loading_min"></span>');
				},
				success: function(data){
					
					if(data.result == "success"){
						if($jd(that).attr('class')=="save_item")	{
							$jd(that).html("<?php echo JText::_('REMOVE_ITEM'); ?>");
							$jd(that).attr('title', "<?php echo JText::_('REMOVE_ITEM'); ?>");
							$jd(that).addClass('remove_item');
							$jd(that).removeClass('save_item');
						}
						else	{
							$jd(that).html("<?php echo JText::_('SAVE_ITEM'); ?>");
							$jd(that).attr('title', "<?php echo JText::_('SAVE_ITEM'); ?>");
							$jd(that).addClass('save_item');
							$jd(that).removeClass('remove_item');
						}
						
						
					}
					else	{
						$jd(that).html(html);
						alert(data.error);
					}
				},
				error: function(jqXHR, textStatus, errorThrown)	{
					$jd(that).html(html);
					alert(textStatus);
				}
			});
		
		});
		
		<?php	endif;	?>
		
	});

</script>

<div id="joomdpanel<?php echo $this->params->get('pageclass_sfx'); ?>">

<form name="listform" action="<?php echo JRoute::_('index.php?option=com_joomd&view=item'); ?>" method="post">

<?php


	echo '<div class="componentheading"><h1>' . $this->params->def('page_title') . '</h1></div>';
	
	//to display the add item icon
	if($this->type->detailconfig->get('add') and Joomd::isAuthorised('addaccess'))	{
		Joomdui::displayaddicon();
	}

	for($i=0;$i<count($this->fields);$i++)	{
		
		$value = $this->field->getfieldvalue($item->id, $this->fields[$i]->id);
		
		if(!empty($value))	{
			
			if($i==0 and $this->fields[$i]->type ==1)
				continue;
			
			echo '<div class="field_block '.$this->fields[$i]->cssclass.'">';
			
			echo '<div class="field_label">';
			
			if($this->fields[$i]->showtitle)
				echo $this->fields[$i]->name;
			
			if($this->fields[$i]->showicon && !empty($this->fields[$i]->icon) && is_file(JPATH_SITE.'/images/joomd/'.$this->fields[$i]->icon))
				echo '&nbsp;<img src="'.JURI::root().'images/joomd/'.$this->fields[$i]->icon.'" alt="'.$this->fields[$i]->name.'" style="max-height:16px;" align="absbottom" />';
			
			echo '</div>';
			
			echo '<div class="field_value">';
			
			echo $this->field->displayfieldvalue($item->id, $this->fields[$i]->id);
			
			echo '</div>';
			
			echo '</div>';
		
		}
	
	}
	
	if($this->type->detailconfig->get('author') or $this->type->detailconfig->get('created') or $this->type->detailconfig->get('modified_by') or $this->type->detailconfig->get('modified') or $this->type->detailconfig->get('hits') or $this->type->detailconfig->get('report') or $this->type->detailconfig->get('contact') or $this->type->detailconfig->get('email') or $this->type->detailconfig->get('print'))	{
		
		echo '<div class="item_info_bar">';
		
		if($this->type->detailconfig->get('hits'))
			echo '<div class="hit_pan">'.JText::_('TOTALHITS').': '.$item->hits.'</div>';
		
		if($this->type->detailconfig->get('report'))	{
			
			echo '<div class="report_pan"><a href="javascript:void(0);" class="report_item" title="'.JText::_('REPORTITEM').'" rel="'.$item->id.'">'.JText::_('REPORTITEM').'</a></div>';
			
			$path = Joomd::getTemplatePath('item', 'includes/report_item.php');
			require($path);
		}
		
		if($this->type->detailconfig->get('author'))	{
			$creator = $item->created_by?('<a href="'.JRoute::_('index.php?option=com_joomd&view=item&typeid='.$this->type->id.'&userid='.$item->created_by).'">'.$item->creator.'</a>'):$item->creator;
			echo '<div class="author_pan">'.JText::_('AUTHOR').': '.$creator.'</div>';
		}
		
		if($this->type->detailconfig->get('created'))	{
			echo '<div class="created_pan">'.JText::_('CREATED_ON').': '.$item->created.'</div>';
		}
		
		if($this->type->detailconfig->get('modified_by') and $item->modified_by)	{
			$modifier = JFactory::getUser($item->modified_by)->name;
			echo '<div class="modified_by_pan">'.JText::_('MODIFIED_BY').': '.$modifier.'</div>';
		}
		
		if($this->type->detailconfig->get('modified') and $item->modified)	{
			echo '<div class="modified_pan">'.JText::_('MODIFIED_ON').': '.$item->modified.'</div>';
		}
		
		if($this->type->detailconfig->get('contact'))	{
			
			echo '<div class="contact_pan"><a href="javascript:void(0);" class="contact_owner" title="'.JText::_('CONTACTOWNER').'" rel="'.$item->id.'">'.JText::_('CONTACTOWNER').'</a></div>';
			
			$path = Joomd::getTemplatePath('item', 'includes/contact.php');
			require($path);
			
		}
		
		if($this->type->detailconfig->get('save') and !$this->user->get('guest'))	{
		
			echo '<div class="save_pan">';
			
			if($item->save)	{
				$save_class = 'remove_item';
				$title = JText::_('REMOVE_ITEM');
			}
			else	{
				$save_class = 'save_item';
				$title = JText::_('SAVE_ITEM');
			}
			
			echo '<a href="javascript:void(0);" class="'.$save_class.'" title="'.$title.'" rel="'.$item->id.'">'.$title.'</a>';
			
			echo '</div>';
			
		}
		
		if($this->type->detailconfig->get('email')) {
			
			$path = Joomd::getTemplatePath('item', 'includes/share_item.php');
			require($path);
		
			echo '<div class="share_pan"><span class="icon_email">'.JText::_('EMAIL_ITEM').'</span></div>';
			
		}
		
		if($this->type->detailconfig->get('print')) {
			echo '<div class="print_pan"><span class="icon_print">'.JText::_('PRINT_ITEM').'</span></div>';
		}
		
		echo '<div class="clr"></div></div>';
		
	}

?>

<?php
	
	echo JHTML::_( 'form.token' );
	
	foreach($this->cparams as $k=>$v)	
		echo '<input type="hidden" id="'.$k.'" name="'.$k.'" value="'.$v.'" />';

?>

<input type="hidden" name="item_id" id="item_id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="cid[]" id="cid" value="" />

</form>

<div class="clr"></div>
</div>

<?php	require(JPATH_ROOT.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'tmpl'.DS.'jd_social.php');	?>

<?php echo Joomd::onAfterDisplay();	?>