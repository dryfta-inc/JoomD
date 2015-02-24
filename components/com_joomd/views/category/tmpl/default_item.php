<?php

$class = $this->item->featured?' featured':'';

echo '<div class="cat_row'.$class.'">';
			
	echo '<div class="cat_title"><a href="'.JRoute::_('index.php?option=com_joomd&view='.$this->type->app.'&typeid='.$this->type->id.'&catid='.$this->item->id).'">'.$this->item->name.' ('.$this->item->items.')</a></div>';
	
	echo '<div class="cat_data">';
	
	if($this->item->fulltext <> "")
		echo '<div class="descr">'.$this->item->fulltext.'</div>';
		
	if(count($this->item->child))	{
		
		echo '<div class="cat_child">';
		
		echo '<div class="subhead">'.JText::_('SUBCAT').' <span class="showicon">&nbsp;&nbsp;&nbsp;&nbsp;</span></div><div class="listchild">';
		
		for($j=0;$j<count($this->item->child);$j++)	{
			
			$child = $this->item->child[$j];
			
			echo '<div class="child_title"><a href="'.JRoute::_('index.php?option=com_joomd&view='.$this->type->app.'&typeid='.$this->type->id.'&catid='.$child->id).'">'.$child->name.' ('.$child->items.') </a></div>';
			if($j%2 <> 0)
				echo '<div class="clr"></div>';
			
		}
		
		echo '</div></div>';
		
	}
		
	echo '</div>';
	
	if(!empty($this->item->img) and is_file(JPATH_SITE.'/images/joomd/thumbs/'.$this->item->img))
		echo '<div class="cat_img"><img src="images/joomd/thumbs/'.$this->item->img.'" border="0" alt="'.$this->item->name.'" /></div>';
	elseif(!empty($this->item->img) and is_file(JPATH_SITE.'/images/joomd/'.$this->item->img))
		echo '<div class="cat_img"><img src="images/joomd/'.$this->item->img.'" border="0" alt="'.$this->item->name.'" /></div>';
	
	echo '<div class="clr"></div></div>';