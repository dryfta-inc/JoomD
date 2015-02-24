<script type="text/javascript">

	$jd(function()	{
		
		$jd('.contact_pan a').live('click', function(event)	{
			
			$jd('input[name="item_id"]').val($jd(this).attr('rel'))
			
			$jd("#contact_owner").dialog({
				title: "<?php echo JText::_('CONTACTOWNER'); ?>",
				show:"highlight",
				hide:"fade",
				height:400,
				width:275
			});
			
		});
		
		$jd('#contact_owner .contact_send').live('click', function(event)	{
			
			var item_id = $jd('input[name="item_id"]').val();
			var name = $jd('input[name="contact_name"]').val();
			var email = $jd('input[name="contact_email"]').val();
			var phone = $jd('input[name="contact_phone"]').val(); 
			var enquiry = $jd('textarea[name="contact_enquiry"]').val(); 
			
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			
			if ( name == "")	{
				alert("<?php echo JText::_('ENTERNAME'); ?>");
				return false;
			}
			
			else if ( email == "")	{
				alert("<?php echo JText::_('ENTEREMAIL'); ?>");
				return false;
			}
			else if( !reg.test(email)){
				alert("<?php echo JText::_('ENTERVALIDEMAIL'); ?>");
				return false;
			}
			else if(enquiry == ""){
				alert("<?php echo JText::_('ENTERENQUIRY'); ?>");
				return false;
			}
			else{
				$jd.ajax({
					url: "<?php echo JURI::root(); ?>",
					type: "POST",
					dataType:'json',
					data: {'option':'com_joomd', 'view':'item', 'task':'contact_item', 'id':item_id, 'name':name, 'email':email, 'phone':phone, 'enquiry':enquiry, 'abase':1, '<?php echo jutility::getToken(); ?>':1},
					beforeSend: function()	{
						$jd("#contact_owner").prepend('<div class="loadingdisplay"></div>');
					},
					complete: function()	{
						$jd("#contact_owner .loadingdisplay").hide();
					},
					success: function(data){
						
						if(data.result == "success"){
							$jd('#contact_owner').dialog('close');
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
			}
		
		});
				 
	});

</script>

<div id="contact_owner">
	<div class="contact_owner_section">
        <div class="contact_row">
        <?php echo JText::_('YOURNAME'); ?><br />
        <input type="text" name="contact_name" id="contact_name" size="25" value="<?php echo $this->user->name; ?>" />
        </div>
        <div class="contact_row">
        <?php echo JText::_('YEMAIL'); ?><br />
        <input type="text" name="contact_email" id="contact_email" size="25" value="<?php echo $this->user->email; ?>" />
        </div>
        <div class="contact_row">
        <?php echo JText::_('YOURPHONE'); ?><br />
        <input type="text" name="contact_phone" id="contact_phone" size="25" value="" />
        </div>
        <div class="contact_row">
          <?php echo JText::_('ENQUIRY'); ?><br />
          <textarea name="contact_enquiry" id="contact_enquiry" rows="10" col="10"></textarea>
        </div>
      <p align="center">
        <input type="button" class="contact_send" name="submit" id="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
      </p>
	</div>
</div>