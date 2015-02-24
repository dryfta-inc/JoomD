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

$this->multiselect->initialize('select', array('minWidth'=>150));

?>

<div id="joomdpanel">

<form action="index.php?option=com_joomd&view=field" method="post" name="adminlist">

<?php	include(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'html'.DS.'alphabet_search.php');	?>

<table width="100%">
	<tr>
		<td align="left" width="20%">&nbsp;
			<?php echo JText::_( 'FILTER' ); ?>:
			<?php echo $this->lists['search']; ?>
		</td>
        <td width="60%" align="right">
        	<?php
				echo $this->lists['type'].'&nbsp;';
				echo $this->lists['cat'].'&nbsp;';
				echo $this->lists['state'].'&nbsp;';
				echo $this->lists['language'];
			?>
        </td>
	</tr>
</table>

<?php $ordering = ($this->lists['order'] == 'i.ordering'); ?>

    <table class="adminlist">
	<thead>
			<th width="10" align="left">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="10" align="left">
				<input type="checkbox" name="toggle" value="<?php echo count( $this->items ); ?>" />
			</th>
			<th align="left">
				<?php echo JHTML::_('jdgrid.sort', JText::_('FIELD'), 'i.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th align="left" width="100">
				<?php echo JHTML::_('jdgrid.sort', JText::_('LIST'), 'i.list', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th align="left" width="100">
				<?php echo JHTML::_('jdgrid.sort', JText::_('REQUIRED'), 'i.required', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th align="left" width="100">
				<?php echo JHTML::_('jdgrid.sort', JText::_('FIELD_TYPE'), 'i.type', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="250" align="left">
				<?php echo JHTML::_('jdgrid.sort', JText::_('ACTIONS'), 'i.ordering', @$this->lists['order_Dir'], @$this->lists['order'], null, JText::_('CLKHSBYORD') ); ?>
                <?php echo JHTML::_('jdgrid.order',  'saveorder', $ordering ); ?>
			</th>
            <th width="20" align="left">
				<?php echo JHTML::_('jdgrid.sort', 'ID', 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
	</thead>
	
     <?php $dis = count($this->items)<$this->params->total?'block':'none'; ?>
    
	<tfoot>
		<tr><td colspan="9"><div class="pagination" style="display:<?php echo $dis; ?>"><?php echo JHTML::_('jdgrid.loadmore', JText::_('LMORE'), $this->params->total, count($this->items) ); ?></div></td></tr>
	</tfoot>
	
	<tbody>

    <?php
	
	$disabled = $ordering?'':'disabled="disabled"';
	
    $k = 0;
	
	if(count($this->items))	{
	
		for ($i=0; $i < count( $this->items ); $i++)
		{
			$row =  $this->items[$i];
			
			require(dirname(__FILE__).DS.'default_item.php');
			
			$k = 1 - $k;
		}
		
	}
		
	else
		echo '<tr class="no_item_block"><td colspan="9" align="center">'.JText::_('NO_ITEM_CREATED').'</td></tr>';
	
    ?>
    </tbody>
	</table>

 <?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="field" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
<input type="hidden" name="limit" value="<?php echo $this->params->limit; ?>" />
<input type="hidden" name="limitstart" value="<?php echo $this->params->limitstart; ?>" />
<input type="hidden" name="total" value="<?php echo $this->params->total; ?>" />
<input type="hidden" name="count" value="<?php echo count($this->items); ?>" />
<input type="hidden" name="abase" id="abase" value="1" />

</form>

<div class="clr"></div>

</div>
