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

	$jd(function() {
		
		$jd(".showicon").live("click", function(event)	{
			
			var parent = $jd(this).parent().parent();
			
			$jd(parent).find('.listchild').slideToggle("fast", function()	{
				
				if($jd(parent).find('.listchild').css('display') == 'block')	{
				
					$jd(event.target).css('background-position', '158px -194px');
									
				}
				
				else	{
					
					$jd(event.target).css('background-position', '190px -194px');
								
				}
		
			});
			
		
		});
		
	});

</script>

<div id="joomdpanel<?php echo $this->params->def('pageclass_sfx'); ?>">

<form name="listform" action="<?php echo JText::_('index.php?option=com_joomd&view=category'); ?>" method="post">

<?php

	echo '<div class="componentheading"><h1>' . $this->params->def('page_title') . '</h1></div>';
		
	if(!empty($this->parent) and !empty($this->parent->fulltext))
		echo '<div class="descr">'.$this->parent->fulltext.'</div>';
	
	if($this->config->asearch)
		include(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'html'.DS.'alphabet_search.php');
	
	echo '<div class="catlist">';
	
	if(count($this->items))	{
	
		for($i=0;$i<count($this->items);$i++)	{
		
			$this->item =  $this->items[$i];
			
			require(dirname(__FILE__).DS.'default_item.php');

		}
	
	}
	
	else
		echo JText::_('NOCATFND');
	
	echo '</div>';
	
	$dis = count($this->items)<$this->cparams->total?'block':'none';
	echo '<div class="pagination" style="display:'.$dis.';">'.JHTML::_('jdgrid.loadmore', JText::_('LOADMORE'), $this->cparams->total, count($this->items) ).'</div>';

?>

<div class="clr"></div>

<?php

	echo JHTML::_( 'form.token' );

	foreach($this->cparams as $k=>$v)	
		echo '<input type="hidden" name="'.$k.'" value="'.$v.'" />';

?>
<input type="hidden" name="count" value="<?php echo count($this->items); ?>" />

</form>

</div>

<?php	require(JPATH_ROOT.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'tmpl'.DS.'jd_social.php');	?>