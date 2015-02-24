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

?>

<script type="text/javascript">
	
	$jd(function() {
        
        $jd("form[name='cssform']").submit(function() {
          return false;
        });
		
	});
	
	function listItemTask(task)
	{
		
		var data = $jd("form[name='cssform']").serializeArray();
                
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

<form action="index.php?option=com_joomd&view=type" method="post" name="cssform">

<div class="content"><textarea name="content"  id="content" style="width:100%!important; height:400px;"><?php echo $this->content; ?></textarea></div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="css" />
<input type="hidden" name="abase" id="abase" value="1" />

</form>

<div class="clr"></div>
</div>
