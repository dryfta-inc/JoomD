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

<div id="joomdpanel">

<form action="index.php?option=com_joomd&view=orders" method="post" name="adminlist">

<?php

	include(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'html'.DS.'alphabet_search.php');

?>

<table width="100%">
	<tr>
		<td align="left" width="20%">&nbsp;
			<?php echo JText::_( 'FILTER' ); ?>:
			<?php echo $this->lists['search']; ?>
		</td>
	</tr>
</table>

    <table class="adminlist">
	<thead>
			<th width="20" align="left">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th class="title">
					<?php echo JHTML::_('jdgrid.sort', JText::_('USERNAME'), 'u.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th class="title">
					<?php echo JHTML::_('jdgrid.sort', JText::_('PACKAGENAME'), 'pname', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="100">
					<?php echo JHTML::_('jdgrid.sort', JText::_('ORDERNUMBER'), 'i.order_number', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="100">
					<?php echo JHTML::_('jdgrid.sort', JText::_('PAYMENT'), 'i.payment_price', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th width="100">
					<?php echo JHTML::_('jdgrid.sort', JText::_('ORDERSTATUS'), 'i.order_status', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th width="20">
					<?php echo JHTML::_('jdgrid.sort', 'ID', 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
	</thead>
	<?php $dis = count($this->items)<$this->params->total?'block':'none'; ?>
	<tfoot>
		<tr><td colspan="7"><div class="pagination" style="display:<?php echo $dis; ?>"><?php echo JHTML::_('jdgrid.loadmore', JText::_('LOADMORE'), $this->params->total, count($this->items) ); ?></div></td></tr>
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
<input type="hidden" name="view" value="orders" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
<input type="hidden" name="limit" value="<?php echo $this->params->limit; ?>" />
<input type="hidden" name="limitstart" value="<?php echo $this->params->limitstart; ?>" />
<input type="hidden" name="total" value="<?php echo $this->params->total; ?>" />
<input type="hidden" name="count" value="<?php echo count($this->items); ?>" />
<input type="hidden" name="abase" value="1" />

</form>

<div class="clr"></div>

</div>
