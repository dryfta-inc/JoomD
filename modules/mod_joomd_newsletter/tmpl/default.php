<?php

/*------------------------------------------------------------------------
# mod_joomd_newsletter - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomd_nl_signup">
<div class="system_message"></div>
<div class="loadingblock"></div>

<?php	if(count($items) > 0)	{
	
	$doc = JFactory::getDocument();
	
	$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.1.7.js');
	$doc->addScript(JURI::root().'components/com_joomd/assets/js/main.js');
	
	$user = JFactory::getUser();
	
	$multiselect->initialize('form[name=\'joomd_newsletter_signup\'] select#jd_nl_cats', array('filter'=>true, 'header'=>true, 'multiple'=>true, 'noneSelectedText'=>JText::_('SELCATEGORY')));
	
?>

<script type="text/javascript">

	$jd(function()	{
	
		$jd('form[name="joomd_newsletter_signup"]').live('submit', function(event)	{
		
			event.preventDefault();
			
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			
			var cats = new Array();
			$jd('form[name="joomd_newsletter_signup"] select[name="cats[]"] option:selected').each(function() {
				cats.push($jd(this).val());
			});
			
			if(cats.length < 1)	{
				alert("<?php echo JText::_('PLEASESELECTCATEGORY'); ?>");
				return false;
			}
		
			var email = $jd('form[name="joomd_newsletter_signup"] input[name="email"]').val();
			
			if(email == "")	{
				alert("<?php echo JText::_('ENTEREMAIL'); ?>");
				return false;
			}
			
			if(reg.test(email) == false) {
				alert("<?php echo JText::_('ENTERVALIDEMAIL'); ?>");
				return false;
			}
			
			var data = $jd("form[name='joomd_newsletter_signup']").serializeArray();
			
			$jd.ajax({
				  url: "<?php echo JURI::root(); ?>",
				  type: "POST",
                  dataType:"json",
				  data: data,
                  beforeSend: function()	{
                  	$jd(".joomd_nl_signup .loadingblock").show();
                  },
                  complete: function()	{
                  	$jd(".joomd_nl_signup .loadingblock").hide();
                  },
				  success: function(data)	{
					  
					if(data.result == "success")
						$jd(".joomd_nl_signup .system_message").html(data.msg).slideDown().delay(2500).slideUp();
					else
						alert(data.error);
						
				  },
                  error: function(jqXHR, textStatus, errorThrown)	{
                  	alert(textStatus);
                  }
			});
			
		
		});
	
	});

</script>

<form action="<?php echo JURI::root(); ?>" method="post" name="joomd_newsletter_signup">

<p><?php echo JText::_('CATEGORY'); ?><br />

<select name="cats[]" id="jd_nl_cats" multiple="multiple" size="5">

	<?php

		for($i=0;$i<count($items);$i++)	{
			echo '<option value="'.$items[$i]->id.'">'.$items[$i]->name.'</option>';
		}

	?>

</select>
</p>

<p><?php echo JText::_('EMAIL'); ?><br />

<input type="text" name="email" id="jd_nl_email" value="<?php echo $user->email; ?>" />

</p>

<p>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_joomd" />
    <input type="hidden" name="task" value="newsletter_signup" />
    <input type="hidden" name="typeid" value="<?php echo $type->id; ?>" />
    <input type="hidden" name="abase" value="1" />
    <input type="submit" name="signup" value="<?php echo JText::_('SIGNUP'); ?>" />
</p>


</form>


<?php	}

else	{
	echo JText::_('NOCATEGORYFOUND');
}

?>

</div>