<?php

$class = $i%2?'item_row':'item_row_bg';

$class .= $item->featured?' featured':'';

echo '<div class="'.$class.' itemrow_type'.$this->type->id.'">';

for($j=0;$j<count($this->fields);$j++)	{
			
	echo '<div class="item_cell '.$this->fields[$j]->cssclass.'">&nbsp;';
	
	$value = $this->field->getfieldvalue($item->id, $this->fields[$j]->id);

	if(!empty($value))	{
	
		if($j==0)	{
			echo '<a href="'.JRoute::_('index.php?option=com_joomd&view='.$this->type->app.'&layout=detail&typeid='.$item->typeid.'&id='.$item->id).'">';
				echo $this->field->displayfieldvalue($item->id, $this->fields[$j]->id, array('short'=>true));
			echo '</a>';
		}
		else
			echo $this->field->displayfieldvalue($item->id, $this->fields[$j]->id, array('short'=>true));
		
	}
	
	echo '</div>';
	
}

if($this->type->listconfig->get('more'))
	echo '<div class="item_cell more_info"><a href="'.JRoute::_('index.php?option=com_joomd&view='.$this->type->app.'&layout=detail&typeid='.$item->typeid.'&id='.$item->id).'">'.JText::_('MOREINFO').'</a></div>';
	
echo '<div class="clr"></div></div>';