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

<div id="joomdpanel<?php echo $this->params->get('pageclass_sfx'); ?>">

<form name="listform" action="<?php echo JText::_('index.php?option=com_joomd&view=search'); ?>" method="post">


<?php

$css = '#joomdpanel .item_cell { width:'.$this->params->def('width', 100).'px; }';
$this->document->addStyleDeclaration($css);

	echo '<div class="componentheading"><h1>' . $this->params->def('page_title') . '</h1></div>';
	
	if($this->type->listconfig->get('header'))	{
		
		echo '<div class="item_row_header">';
			
		for($i=0;$i<count($this->fields);$i++)	{
		
			echo '<div class="item_cell">';
		
			if(in_array($this->fields[$i]->type, array(10,11,12,13)))
				echo $this->fields[$i]->name;
			else
				echo JHTML::_('jdgrid.sort', $this->fields[$i]->name, 'field_'.$this->fields[$i]->id, @$this->cparams->filter_order_Dir, @$this->cparams->filter_order );

			
			echo '</div>';
		
		}
		
		echo '<div class="clr"></div></div>';
	
	}
	
	echo '<div class="itemlist">';
	
	if(count($this->items))	{
	
		for($i=0;$i<count($this->items);$i++)	{
		
			$item =  $this->items[$i];
			
			require(dirname(__FILE__).DS.'search_item.php');
			

		}
			
	}
	
	else
		echo JText::_('NORESFOND');
		
	echo '</div>';
		
	$dis = count($this->items)<$this->cparams->total?'block':'none';
	echo '<div class="pagination" style="display:'.$dis.';">'.JHTML::_('jdgrid.loadmore', JText::_('LOADMORE'), $this->cparams->total, count($this->items) ).'</div>';

?>

<div class="clr"></div>


<?php

//removed in JoomD v2.1
//	echo JHTML::_( 'form.token' );
	
	foreach($this->cparams as $k=>$v)	{
		
		if(is_array($v))	{
			foreach($v as $fv)
				echo '<input type="hidden" id="'.$k.'" name="'.$k.'[]" value="'.$fv.'" />';
		}
		else
			echo '<input type="hidden" id="'.$k.'" name="'.$k.'" value="'.$v.'" />';
	
	}

?>

<input type="hidden" name="count" value="<?php echo count($this->items); ?>" />
<input type="hidden" name="cid[]" id="cid" value="" />

</form>

</div>