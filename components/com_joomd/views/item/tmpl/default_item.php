<?php

$class = $i%2?'item_row':'item_row_bg';

$class .= $item->featured?' featured':'';
			
echo '<div class="'.$class.' itemrow_type'.$this->type->id.'">';

echo '<div class="item_content">';

for($j=0;$j<count($this->fields);$j++)	{
			
	echo '<div class="item_cell '.$this->fields[$j]->cssclass.'">';
	
	$value = $this->field->getfieldvalue($item->id, $this->fields[$j]->id);
	
	if(empty($value))
		echo '&nbsp;';
	else	{
	
		if($j==0)	{
			echo '<a href="'.JRoute::_('index.php?option=com_joomd&view=item&layout=detail&typeid='.$item->typeid.'&id='.$item->id).'">';
			echo $this->field->displayfieldvalue($item->id, $this->fields[$j]->id, array('short'=>true));
			echo '</a>';
		}
		else
			echo $this->field->displayfieldvalue($item->id, $this->fields[$j]->id, array('short'=>true));
		
	}
	
	//to display the action icons if user is logged in
	if(!$this->user->get('guest') && $j+1 == count($this->fields))	{
		echo '<div class="owner_icons">';
			Joomdui::displayicons($item);
		echo '</div>';
	}
	
	echo '</div>';
	
}

if($this->type->listconfig->get('more'))
	echo '<div class="item_cell more_info"><a href="'.JRoute::_('index.php?option=com_joomd&view=item&layout=detail&typeid='.$item->typeid.'&id='.$item->id).'">'.JText::_('MOREINFO').'</a></div>';

echo '<div class="clr"></div>';

echo '</div>';

if($this->type->listconfig->get('author') or $this->type->listconfig->get('created') or $this->type->listconfig->get('modified_by') or $this->type->listconfig->get('modified') or $this->type->listconfig->get('hits') or $this->type->listconfig->get('report') or $this->type->listconfig->get('contact'))	{
		
		echo '<div class="item_info_bar">';
		
		if($this->type->listconfig->get('hits'))
			echo '<div class="hit_pan">'.JText::_('TOTALHITS').': '.$item->hits.'</div>';
		
		if($this->type->listconfig->get('report'))	{
			
			echo '<div class="report_pan"><a href="javascript:void(0);" class="report_item" title="'.JText::_('REPORTITEM').'" rel="'.$item->id.'">'.JText::_('REPORTITEM').'</a></div>';

		}
		
		if($this->type->listconfig->get('author'))	{
			$creator = $item->created_by?('<a href="'.JRoute::_('index.php?option=com_joomd&view=item&typeid='.$this->type->id.'&userid='.$item->created_by).'">'.$item->creator.'</a>'):$item->creator;
			echo '<div class="author_pan">'.JText::_('AUTHOR').': '.$creator.'</div>';
		}
		
		if($this->type->listconfig->get('created'))	{
			echo '<div class="created_pan">'.JText::_('CREATED_ON').': '.$item->created.'</div>';
		}
		
		if($this->type->listconfig->get('modified_by') and $item->modified_by)	{
			$modifier = JFactory::getUser($item->modified_by)->name;
			echo '<div class="modified_by_pan">'.JText::_('MODIFIED_BY').': '.$modifier.'</div>';
		}
		
		if($this->type->listconfig->get('modified') and $item->modified)	{
			echo '<div class="modified_pan">'.JText::_('MODIFIED_ON').': '.$item->modified.'</div>';
		}
		
		if($this->type->listconfig->get('contact'))	{
			
			echo '<div class="contact_pan"><a href="javascript:void(0);" class="contact_owner" title="'.JText::_('CONTACTOWNER').'" rel="'.$item->id.'">'.JText::_('CONTACTOWNER').'</a></div>';
			
		}
		
		if($this->type->listconfig->get('save') and !$this->user->get('guest'))	{
		
			echo '<div class="save_pan">';
			
			if($item->save)	{
				$save_class = 'remove_item';
				$title = JText::_('REMOVE_ITEM');
			}
			else	{
				$save_class = 'save_item';
				$title = JText::_('SAVE_ITEM');
			}
			
			echo '<a href="javascript:void(0);" class="'.$save_class.'" title="'.$title.'" rel="'.$item->id.'">'.$title.'</a>';
			
			echo '</div>';
			
		}
		
		echo '<div class="clr"></div></div>';
		
		echo '<div class="clr"></div>';
		
	}

echo '</div>';
