<script type="text/javascript">

	$jd(function()	{
		
		$jd('.report_pan a').live('click', function(event)	{
			
			$jd('input[name="item_id"]').val($jd(this).attr('rel'))
			
			$jd("#report_item").dialog({
				title: "<?php echo JText::_('REPORTITEM'); ?>",
				show:"highlight",
				hide:"fade",
				height:350,
				width:275
			});
			
		});
		
		$jd('#report_item .report_send').live('click', function(event)	{
		
			var item_id = $jd('input[name="item_id"]').val();
			var email = $jd('input[name="report_email"]').val(); 
			var comment = $jd('textarea[name="report_comment"]').val(); 
			
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			
			if ( email == "")	{
				alert("<?php echo JText::_('ENTEREMAIL'); ?>");
				return false;
			}
			else if( !reg.test(email)){
				alert("<?php echo JText::_('ENTERVALIDEMAIL'); ?>");
				return false;
			}
			else if(comment == ""){
				alert("<?php echo JText::_('ENTERCOMMENT'); ?>");
				return false;
			}
			else{
				$jd.ajax({
					url: "<?php echo JURI::root(); ?>",
					type: "POST",
					dataType:'json',
					data: {'option':'com_joomd', 'view':'item', 'task':'report_item', 'id':item_id, 'email':email, 'comment':comment, 'abase':1, '<?php echo jutility::getToken(); ?>':1},
					beforeSend: function()	{
						$jd("#report_item").prepend('<div class="loadingdisplay"></div>');
					},
					complete: function()	{
						$jd("#report_item .loadingdisplay").hide();
					},
					success: function(data){
						
						if(data.result == "success"){
							$jd('#report_item').dialog('close');
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

<div id="report_item">
	<div class="report_item_section">
        <div class="report_row">
        <?php echo JText::_('YEMAIL'); ?><br />
        <input type="text" name="report_email" id="email" size="25" value="<?php echo $this->user->email; ?>" />
        </div>
        <div class="report_row">
          <?php echo JText::_('COMMENT'); ?><br />
          <textarea name="report_comment" id="comment" rows="10" col="10"></textarea>
        </div>
      <p align="center">
        <input type="button" class="report_send" name="submit" id="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
      <p>
	</div>
</div>