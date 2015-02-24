<?php defined('_JEXEC') or die('Restricted access');

$this->multiselect->initialize('form[name=\"listform\"] select', array('minWidth'=>150));

echo $this->toolbar->render();


echo $this->params->def('page_title');

$now =  JFactory::getDate();

?>

<div class="clr" style="height:15px;"></div>

<div id="joomdpanel">


<form name="listform" action="<?php echo JText::_('index.php?option=com_joomd&view=itempanel'); ?>" method="post">

<?php

if($this->config->asearch)
	include(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'html'.DS.'alphabet_search.php');

?>

<table width="100%">
	<tr>
		<td align="left" width="30%">&nbsp;
			<?php echo JText::_( 'FILTER' ); ?>:
			<?php echo $this->lists['search']; ?>
		</td>
        <td width="60%" style="text-align:right;">
        	<?php
				echo $this->lists['cat'] . '&nbsp;';
				echo $this->lists['state'].'&nbsp;';
				echo $this->lists['language'];
			?>
        </td>
	</tr>
</table>


    <table class="itemlist">
    <thead>
			<tr>
				<th width="10" align="left">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="10" align="left">
				<input type="checkbox" name="toggle" value="<?php echo count( $this->items ); ?>" />
			</th>
			<th align="left">
				<?php
					echo JHTML::_('jdgrid.sort', $this->firstfield->name, 't.field_'.$this->firstfield->id, @$this->lists['order_Dir'], @$this->lists['order'] );

				?>
			</th>
            <th align="center" width="50">
				<?php echo JHTML::_('jdgrid.sort', JText::_('FEATURED'), 'i.featured', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
            <th align="center" width="50">
				<?php echo JHTML::_('jdgrid.sort', JText::_('PUBLISHED'), 'i.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="100" align="center">
				<?php echo JText::_('ACTION'); ?>
			</th>
			</tr>
		</thead>
	
    <?php $dis = count($this->items)<$this->cparams->total?'block':'none'; ?>
    
	<tfoot>
    <tr>
      <td colspan="6"><div class="pagination" style="display:<?php echo $dis; ?>"><?php echo JHTML::_('jdgrid.loadmore', JText::_('LOADMORE'), $this->cparams->total, count($this->items) ); ?></div></td>
    </tr>
  	</tfoot>
	
    <tbody>
    <?php
    $k = 0;
	
	if(count($this->items))	{
	
		for ($i=0; $i<count( $this->items ); $i++)
		{
			$row =  $this->items[$i];
			
			require(dirname(__FILE__).DS.'default_item.php');
			
			$k = 1 - $k;
		}
	
	}
	
	else
		echo '<tr class="no_item_block"><td colspan="6" align="center">'.JText::_('NO_ITEM_CREATED').'</td></tr>';
	
    ?>
    </tbody>
    
    </table>
    
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="itempanel" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
<input type="hidden" name="limit" value="<?php echo $this->cparams->limit; ?>" />
<input type="hidden" name="limitstart" value="<?php echo $this->cparams->limitstart; ?>" />
<input type="hidden" name="total" value="<?php echo $this->cparams->total; ?>" />
<input type="hidden" name="count" value="<?php echo count($this->items); ?>" />
<input type="hidden" name="abase" id="abase" value="1" />
<input type="hidden" name="typeid" value="<?php echo $this->cparams->typeid; ?>" />

</form>

</div>