<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2011 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/index.php?option=com_ccboard&view=forumlist&Itemid=63
-----------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<script type="text/javascript">

	function get_pitems()
	{
		
		$jd.ajax({
			  url: "index.php",
			  type: "POST",
			  dataType:"json",
			  data: {'option':'com_joomd', 'view':'newsletter', 'task':'get_cats', 'filter_type':$jd('select[name="filter_type"]').val(), "<?php echo jutility::getToken(); ?>":1, 'abase':1},
			  success: function(res)	{
				
				if(res.result == 'success')	{
					$jd('select[name="filter_cat"]').html(res.list);
					$jd('select[name="filter_cat"]').multiselect("refresh");
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

</script>

<form action="index.php?option=com_joomd&view=newsletter" method="post" name="adminlist">

<?php	require(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'html'.DS.'alphabet_search.php');	?>

<table width="100%">
	<tr>
		<td align="left" width="20%">&nbsp;
			<?php echo JText::_( 'FILTER' ); ?>:
			<?php echo $this->lists['search']; ?>
		</td>
        <td width="60%" align="right">
        	<?php
				echo $this->lists['type'].'&nbsp;';
				echo $this->lists['cat'];
			?>
        </td>
	</tr>
</table>

    <table class="adminlist">
	<thead>
			<th width="20" align="left">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="10" align="left">
				<input type="checkbox" name="toggle" value="<?php echo count( $this->items ); ?>" />
			</th>
			<th align="left">
				<?php echo JHTML::_('jdgrid.sort', JText::_('EMAIL'), 'i.email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th align="left" width="200">
				<?php echo JHTML::_('jdgrid.sort', JText::_('TYPE'), 't.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th align="left" width="200">
				<?php echo JText::_('CATEGORIES'); ?>
			</th>
            <th align="left" width="200">
				<?php echo JText::_('ACTIONS'); ?>
			</th>
            <th width="20" align="left">
				<?php echo JHTML::_('jdgrid.sort', 'ID', 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
	</thead>
	
	<tfoot>
		<tr><td colspan="7"><?php echo JHTML::_('jdgrid.loadmore', JText::_('LMORE'), $this->params->total, count($this->items) ); ?></td></tr>
	</tfoot>
	
	<tbody>

    <?php
		
    $k = 0;
    for ($i=0; $i < count( $this->items ); $i++)
    {
        $row =  $this->items[$i];
		
		require(dirname(__FILE__).DS.'default_item.php');
		
        $k = 1 - $k;
    }
    ?>
    </tbody>
	</table>

 <?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="newsletter" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
<input type="hidden" name="limit" value="<?php echo $this->params->limit; ?>" />
<input type="hidden" name="limitstart" value="<?php echo $this->params->limitstart; ?>" />
<input type="hidden" name="total" value="<?php echo $this->params->total; ?>" />
<input type="hidden" name="count" value="<?php echo count($this->items); ?>" />
<input type="hidden" name="abase" id="abase" value="1" />

</form>