<?php

$url = 'index.php?option=com_joomd&view=item&typeid='.$this->type->id;
		
$layout=JRequest::getVar('layout', '');
$userid=JRequest::getInt('userid', 0);
$catid=JRequest::getInt('catid', 0);
$id=JRequest::getInt('id', 0);

//check the layout to build the url
if($layout=='detail')
	$url .= '&layout=detail&id='.$id;
if($userid)
	$url .= '&userid='.$userid;
if($catid)
	$url .= '&catid='.$catid;
	
$url = JURI::root().substr(JRoute::_($url), strlen(JURI::base(true))+1);

?>

<script type="text/javascript">

	$jd(function()	{
		
		$jd('span.icon_email').live('click', function(event)	{
			
			$jd("#share_item").dialog({
				title: "<?php echo JText::_('EMAILTHISTOFRIEND'); ?>",
				show:"highlight",
				hide:"fade",
				height:275,
				width:275
			});
			
		});
		
		$jd('#share_item .share_send').live('click', function(event)	{
						
			event.preventDefault();
			
			var form = document.listform;
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	
			// do field validation
			if ($jd('input[name="mailto"]').val() == "") {
				alert( "<?php echo JText::_('ENTERRECIEVEREMAIL'); ?>" );
				return false;
			}
			
			if(reg.test($jd('input[name="mailto"]').val()) == false) {
				alert("<?php echo JText::_('ENTERVALIDRECIEVEREMAIL'); ?>");
				return false;
			}
			if($jd('input[name="from"]').val() == "")	{
				alert("<?php echo JText::_('ENTEREMAIL'); ?>");
				return false;
			}
			if(reg.test($jd('input[name="from"]').val()) == false) {
				alert("<?php echo JText::_('ENTERVALIDEMAIL'); ?>");
				return false;
			}
			
			$jd.ajax({
				  url: "<?php echo JURI::root(); ?>",
				  type: "POST",
				  dataType:"json",
				  data: {'option':'com_joomd', 'view':'item', 'task':'sendemail', 'url':'<?php echo $url; ?>', 'mailto':$jd('input[name="mailto"]').val(), 'sender':$jd('input[name="sender"]').val(), 'from':$jd('input[name="from"]').val(), 'subject':$jd('input[name="subject"]').val() },
				  beforeSend: function()	{
					$jd("#share_item").prepend('<div class="loadingdisplay"></div>');
				  },
				  complete: function()	{
					$jd("#share_item .loadingdisplay").hide();
				  },
				  success: function(data)	{
					
					if(data.result == "success"){
						$jd('#share_item').dialog('close');
						alert(data.msg);
					}
					else	{
						alert(data.error);
					}
						
				  },
				  error: function(jqXHR, textStatus, errorThrown)	{
					alert(textStatus);
				  }
			});
					
		});
		
	});

</script>


<div id="share_item">

	<p>
		<?php echo JText::_('EMAILTO'); ?>:
		<br />

		<input type="text" name="mailto" class="inputbox" size="25" value=""/>
	</p>

	<p>
		<?php echo JText::_('YOURNAME'); ?>:
		<br />
		<input type="text" name="sender" class="inputbox" value="<?php echo $this->user->name; ?>" size="25" />
	</p>

	<p>

		<?php echo JText::_('YEMAIL'); ?>:
		<br />
		<input type="text" name="from" class="inputbox" value="<?php echo $this->user->email; ?>" size="25" />
	</p>

	<p>
		<?php echo JText::_('SUBJECT'); ?>:
		<br />
		<input type="text" name="subject" class="inputbox" value="" size="25" />
	</p>

	<p>
		<button class="button share_send"><?php echo JText::_('SEND'); ?></button>
	</p>
</div>