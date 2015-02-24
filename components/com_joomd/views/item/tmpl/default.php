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
 
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">

	$jd(function()	{
		
		<?php if($this->type->listconfig->get('print')) :	?>
		
		$jd('span.icon_print').live('click', function(event)	{
		
			$jd('.dialogbox').dialog({
				width:900,
				height:600,
				open: function(event, ui)	{
					
					$jd.ajax({
						  url: "<?php echo JURI::root(); ?>",
						  type: "POST",
						  dataType:"json",
						  data: {'option':'com_joomd', 'view':'item', 'task':'printall', 'typeid':<?php echo (int)$this->type->id; ?>, 'catid':<?php echo $this->cparams->catid; ?>, 'limit':$jd('input[name="limit"]').val(), 'limitstart':$jd('input[name="limitstart"]').val()},
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
		
		<?php	if($this->type->listconfig->get('save') and !$this->user->get('guest'))	:	?>
		
		$jd('.item_info_bar .save_pan a').live('click', function(event)	{
			
			var that = this;
			var html = $jd(that).html();
			
			
			$jd.ajax({
				url: "<?php echo JURI::root(); ?>",
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

	if($this->type->listconfig->get('print') or $this->type->listconfig->get('email'))	{
	
	$url = $_SERVER['REQUEST_URI'];
	
?>

		<div class="header_button">
		
		<?php if($this->type->listconfig->get('email')) {
			
			$path = Joomd::getTemplatePath('item', 'includes/share_item.php');
			require($path);
			
		?>
			<a href="javascript:void(0);"><span class="icon_email">email</span></a>
		<?php	}	?>
        
        <?php if($this->type->listconfig->get('print')) { ?>
			<a href="javascript:void(0);"><span class="icon_print">print</span></a>
		<?php	}	?>

		<div class="clr"></div>
		
		</div>

<?php
	
	}

	echo '<div class="componentheading"><h1>' . $this->params->def('page_title') . '</h1></div>';
	
	if($this->config->asearch)
		include(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'html'.DS.'alphabet_search.php');
	
	//to display the add item icon
	if($this->type->listconfig->get('add') and Joomd::isAuthorised('addaccess'))	{
		Joomdui::displayaddicon();
	}
	
	if($this->type->listconfig->get('report'))	{
		$path = Joomd::getTemplatePath('item', 'includes/report_item.php');
		require($path);
	}
	
	if($this->type->listconfig->get('contact'))	{
		$path = Joomd::getTemplatePath('item', 'includes/contact.php');
		require($path);
	}
	
	if($this->type->listconfig->get('header'))	{
		
		echo '<div class="item_row_header">';
			
		for($i=0;$i<count($this->fields);$i++)	{
		
			echo '<div class="item_cell jdheader'.$this->fields[$i]->cssclass.'">';
			
			//to display the header with/without sorting option
			if(in_array($this->fields[$i]->type, array(10,11,12,13)))
				echo $this->fields[$i]->name;
			else
				echo JHTML::_('jdgrid.sort', $this->fields[$i]->name, 'field_'.$this->fields[$i]->id, @$this->cparams->filter_order_Dir, @$this->cparams->filter_order );

			
			echo '</div>';
		
		}
		
		echo '<div class="clr"></div></div>';
	
	}
	
	echo '<div class="itemlist itemlist_type'.$this->type->id.'">';
	
	if(count($this->items))	{
					
		//all the item list part display here
		for($i=0;$i<count($this->items);$i++)	{
		
			$item =  $this->items[$i];
						
			require(dirname(__FILE__).DS.'default_item.php');
			

		}
					
	}
	
	else
		echo JText::_('NORESFOND');
		
	echo '</div>';
		
	//load more icon
	$dis = count($this->items)<$this->cparams->total?'block':'none';
	echo '<div class="pagination" style="display:'.$dis.';">'.JHTML::_('jdgrid.loadmore', JText::_('LOADMORE'), $this->cparams->total, count($this->items) ).'</div>';

?>


<?php
	
	echo JHTML::_( 'form.token' );
	
	foreach($this->cparams as $k=>$v)	
		echo '<input type="hidden" id="'.$k.'" name="'.$k.'" value="'.$v.'" />';

?>

<input type="hidden" name="item_id" id="item_id" value="" />
<input type="hidden" name="count" value="<?php echo count($this->items); ?>" />
<input type="hidden" name="cid[]" id="cid" value="" />

</form>

<div class="clr"></div>
</div>

<?php	require(JPATH_ROOT.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'tmpl'.DS.'jd_social.php');	?>

<?php

	$url = 'index.php?option=com_joomd&task=rss&view=item&typeid='.$this->type->id;
	if($this->cparams->catid)
		$url .= '&catid='.$this->cparams->catid;
		
	$url = JURI::root().substr(JRoute::_($url), strlen(JURI::base(true))+1);

?>

<?php if($this->type->listconfig->get('rss'))	{	?>
<div class="jd_rss">
<a href="<?php echo $url; ?>" rel="alternate" title="RSS 2.0"><img src="<?php echo JURI::root(); ?>components/com_joomd/assets/images/rss_button01.gif" border="0" width="16" /></a>
</div>
<?php	}	?>