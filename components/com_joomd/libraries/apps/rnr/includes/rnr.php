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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

	<?php if($config['comment_type']==2)	{	?>
    
    <div id="joomd_reviews">
    
    <div id="fb-root"></div>
	<script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=109647842392782";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    
    <div class="fb-comments" data-href="<?php echo JURI::root().substr(JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$type->id.'&id='.$id), strlen(JURI::base(true))+1); ?>" data-num-posts="2" data-width="470"></div>
    
    </div>
    
    <?php	}	else	{
		
	$doc =  JFactory::getDocument();
	$doc->addStyleSheet(JURI::root().'components/com_joomd/assets/css/rnr.css');
	
	$ac = Joomdui::getAccordion();
	
	echo $ac->startPane('joomd_reviews', array('collapsible'=>true, 'active'=>false));
	
	echo $ac->startPanel('review1', JText::_('REVIEWS'));	
		
	?>
    
    <script type="text/javascript">
	
		$jd(function()	{
			
			$jd('.review_form .item_rating_stars a').live('click', function(event){
				var width = $jd(this).text()*20;
				$jd('.review_form ul.item_rating_list li:first').css('width', width+'%');
				$jd('form[name="post_review"] input[name="review_rate"]').val($jd(this).text());
			});
			
			$jd('form[name="post_review"]').live('submit', function(event)	{
				
				event.preventDefault();
				
				var rate = $jd('form[name="post_review"] input[name="review_rate"]').val();
				var name = $jd('form[name="post_review"] input[name="review_title"]').val()
				var comment = $jd('form[name="post_review"] textarea[name="review_comment"]').val();
				
				if(rate==0 || rate == "")	{
					alert("<?php echo JText::_('PLZGIVESOMERATING'); ?>");
					return false;
				}
				
				$jd.ajax({
					url: "<?php echo JURI::root(); ?>",
					type: "POST",
					dataType:'json',
					data: {'option':'com_joomd', 'task':'app_task', 'action':'rnr-post_review', 'typeid': <?php echo (int)$type->id; ?>, 'itemid':<?php echo $id; ?>, 'rate':rate, 'name':name, 'comment':comment, 'abase':1, '<?php echo jutility::getToken(); ?>':1},
					beforeSend: function()	{
						$jd('form[name="post_review"]').css('opacity', '0.2');
					},
					complete: function()	{
						$jd('form[name="post_review"]').css('opacity', '1');
					},
					success: function(data){
						
						if(data.result == "success"){
							
							<?php if($config['moderate'])	{	?>
								$jd(".review_form .system_message").html(data.msg).slideDown().delay(2500).slideUp();
							<?php	}	else	{	?>
								
								var width = rate*20;
								
								var html = '<div class="review_rowlist">';
								
								html += '<h4><ul class="item_rating_list"><li id="item_current_rating" class="item_current_rating" style="width:'+width+'%;"></li></ul></h4>';
				
								html += '<span class="create_date"><?php echo JText::_('REVIEWEDON'); ?>: <em><?php echo JText::_('JUSTNOW'); ?></em></span>';
								
								html += '<div class="review_creator"><?php echo JText::_('REVIEWEDBY'); ?>: <em><?php if($user->get('guest')) echo JText::_('YOU'); else echo $user->name; ?></em></div>';
								
								if(name != "")
									html += '<div class="review_title">'+name+'</div>';
									
								if(comment != "")
									html += '<div class="review_comment">'+comment+'</div>';
								
								html += '</div>';
								if($jd(".review_list>.review_rowlist").length > 0)
									$jd(".review_list").prepend(html);
								else
									$jd(".review_list").html(html);
								
							<?php	}	?>
							
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
    
    <?php
	
	if(in_array($config['comment_access'], $user->getAuthorisedViewLevels())):
	
	?>
    
    <div class="review_form">
    
    <h3><?php echo JText::_('POSTAREVIEW'); ?></h3>
    
    <div class="system_message"></div>
        <form name="post_review" action="index.php" method="post">
        
        <div class="post_row">
            <span class="field_label"><?php echo JText::_('RATING'); ?>:</span>&nbsp;<?php echo JoomdAppRnr::display_stars(); ?>
        </div>
        <div class="post_row">
            <span class="field_label"><?php echo JText::_('TITLE'); ?>:</span>&nbsp;<input type="text" name="review_title" id="review_title" size="50" />
        </div>
        <div class="post_row">
            <span class="field_label"><?php echo JText::_('COMMENT'); ?>:</span><br />
            <textarea name="review_comment" id="review_comment" rows="5" cols="70"></textarea>
        </div>
        <div class="post_row">
        	<input type="submit" name="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
        </div>
        
        </form>
    </div>
    
    <?php endif;	?>
    
    <div class="review_list">
    
    	<?php
		
			if(count($items))	{
				
			for($i=0;$i<count($items);$i++)	{
				
				echo '<div class="review_rowlist">';
				
				$width = $items[$i]->rate*20;
				
				echo '<h4><ul class="item_rating_list"><li id="item_current_rating" class="item_current_rating" style="width:'.$width.'%;"></li></ul></h4>';
				
				echo '<span class="create_date">'.JText::_('REVIEWEDON').': <em>'.$items[$i]->created.'</em></span>';
				
				echo '<div class="review_creator">'.JText::_('REVIEWEDBY').': <em>'.$items[$i]->creator.'</em></div>';
				
				if(!empty($items[$i]->name))
					echo '<div class="review_title">'.$items[$i]->name.'</div>';
					
				if(!empty($items[$i]->comment))
					echo '<div class="review_comment">'.$items[$i]->comment.'</div>';
				
				echo '</div>';
				
			}
			
			}
			else
				echo JText::_('NOREVIEWFOUND');
		
		?>
    
    </div>
    
    <?php
    
	echo $ac->endPanel();
	
	echo $ac->endPane();
	
	}
	
?>