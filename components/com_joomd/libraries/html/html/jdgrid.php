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

defined('JPATH_BASE') or die();
/**
 * Utility class for creating HTML Grids
 *
 * @static
 * @subpackage	HTML
 * @since		1.5
 */
class JHTMLJdgrid
{
	/**
	 * @param	string	The link title
	 * @param	string	The order field for the column
	 * @param	string	The current direction
	 * @param	string	The selected ordering
	 * @param	string	An optional task override
	 */
	public static function sort( $title, $order, $direction = 'asc', $selected = 0, $task=NULL, $t=NULL)
	{
		$direction	= strtolower( $direction );
		$images		= array( 'sort_asc.png', 'sort_desc.png' );
		$index		= intval( $direction == 'desc' );
		$direction	= ($direction == 'desc') ? 'asc' : 'desc';
		
		if(empty($t))
			$t = JText::_('CLKSHORTBYCOL');
			
		$html = '<a href="javascript:void(0);" id="sort" class=\''.$order.' '.$direction.'\' title="'.$t.'">'.$title;
		
		if ($order == $selected ) {
			$html .= '<img src="'.JURI::root().'components/com_joomd/assets/images/'.$images[$index].'" alt="" />';
		}
		$html .= '</a>';
		return $html;
	}

	/**
	* @param int The row index
	* @param int The record id
	* @param boolean
	* @param string The name of the form element
	*
	* @return string
	*/
	public static function id( $rowNum, $recId, $checkedOut=false, $name='cid' )
	{
		if ( $checkedOut ) {
			return '';
		} else {
			return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="ic(this.checked);" />';
		}
	}

	public static function access( &$row, $i, $archived = NULL )
	{
		if ( !$row->access )  {
			$color_access = 'style="color: green;"';
			$task_access = 'accessregistered';
		} else if ( $row->access == 1 ) {
			$color_access = 'style="color: red;"';
			$task_access = 'accessspecial';
		} else {
			$color_access = 'style="color: black;"';
			$task_access = 'accesspublic';
		}

		if ($archived == -1)
		{
			$href = $row->groupname;
		}
		else
		{
			$href = '
			<a href="javascript:void(0);" id="gridaccess" class="cb'.$i.' '. $task_access .'" '. $color_access .'>
			'. $row->groupname .'</a>'
			;
		}

		return $href;
	}

	public static function checkedOut( &$row, $i, $identifier = 'id' )
	{
		$user   =  JFactory::getUser();
		$userid = $user->get('id');

		$result = false;
		if(is_a($row, 'JTable')) {
			$result = $row->isCheckedOut($userid);
		} else {
			$result = JTable::isCheckedOut($userid, $row->checked_out);
		}

		$checked = '';
		if ( $result ) {
			$checked = JHTMLGrid::_checkedOut( $row );
		} else {
			$checked = JHTML::_('grid.id', $i, $row->$identifier );
		}

		return $checked;
	}

	public static function published( &$row, $i, $checkedOut=false, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		
		$img 	= $row->published ? $imgY : $imgX;
		$task 	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? JText::_( 'PUBLISHED' ) : JText::_( 'UNPUBLISHED' );
		$action = $row->published ? JText::_( 'UNPUBITEM' ) : JText::_( 'PUBITEM' );
		
		if($checkedOut)
			return '<img src="'.JURI::root().'components/com_joomd/assets/images/'. $img .'" border="0" alt="'. $alt .'" />';

		$href = '<a href="javascript:void(0);" onclick="javascript:listItemTask(\''.$task.'\', \'cb'.$i.'\');" title="'. $action .'" class="gridicons"><span class="jdgrid"><img src="'.JURI::root().'components/com_joomd/assets/images/'. $img .'" border="0" alt="'. $alt .'" /></span></a>';

		return $href;
	}
	
	public static function featured( &$row, $i, $checkedOut=false, $imgY = 'featured.png', $imgX = 'disabled.png', $prefix='' )
	{
		
		$img 	= $row->featured ? $imgY : $imgX;
		$task 	= $row->featured ? 'unfeatured' : 'featured';
		$alt 	= $row->featured ? JText::_( 'FEATURED' ) : JText::_( 'UNFEATURED' );
		$action = $row->featured ? JText::_( 'UNFEATUREDTEXT' ) : JText::_( 'FEATUREDTEXT' );
		
		if($checkedOut)
			return '<img src="'.JURI::root().'components/com_joomd/assets/images/'. $img .'" border="0" alt="'. $alt .'" />';

		$href = '<a href="javascript:void(0);" class="featuredgrid" id="featured_'.$i.'" data-task="'.$task.'" data-cb="cb'.$i.'" title="'. $action .'"><span class="jdgrid"><img src="'.JURI::root().'components/com_joomd/assets/images/'. $img .'" border="0" alt="'. $alt .'" /></span></a>';

		return $href;
	}
	
	public static function edit($i, $checkedOut=false, $task = 'edit', $alt='edit' , $img = 'edit.png', $title = 'Edit')
	{
		
		if($checkedOut)
			return '';
		
		$html = '<a href="javascript:void(0);" id="gridedit" class="cb'. $i .' '.$task.'" title="'. $title .'"><span class="jdgrid"><img src="'.JURI::root().'components/com_joomd/assets/images/'. $img .'" border="0" alt="'. $alt .'" /></span></a>';

		return $html;
		
	}
	
	public static function fedit($id, $width='', $height='', $title = 'Edit')
	{		
		
		$html = '<a href="javascript:void(0);" onclick="javascript:document.getElementById(\'cid\').value='.$id.';openDiag(\'edit\'';
		if(!empty($width))
			$html .= ', '.$width;
		if(!empty($height))
			$html .= ', '.$height;
		$html .= ');" title="'. JText::_($title) .'"><span class="jdgrid"><img src="'.JURI::root().'components/com_joomd/assets/images/edit.png" border="0" alt="edit" /></span></a>';

		return $html;
		
	}
	
	public static function fdelete($id, $task = 'delete', $alt='delete' , $img = 'trash.png', $title = 'Delete')
	{
		
		$html = '<a href="javascript:void(0);" onclick="javascript:document.getElementById(\'cid\').value='.$id.';listItemTask(\''.$task.'\');" title="'. JText::_($title) .'"><span class="jdgrid"><img src="'.JURI::root().'components/com_joomd/assets/images/'. $img .'" border="0" alt="'. $alt .'" /></span></a>';

		return $html;
		
	}
	
	public static function delete($i, $checkedOut=false, $task = 'delete', $alt='delete' , $img = 'trash.png', $title = 'Delete')
	{
		
		if($checkedOut)
			return '';
		
		$html = '<a href="javascript:void(0);" onclick="javascript:listItemTask(\''.$task.'\', \'cb'.$i.'\');" title="'. JText::_($title) .'" class="gridicons"><span class="jdgrid"><img src="'.JURI::root().'components/com_joomd/assets/images/'. $img .'" border="0" alt="'. $alt .'" /></span></a>';

		return $html;
		
	}
	
	public static function order( $task = 'saveorder', $ordering, $alt = 'save', $img='filesave.png', $title="Save Order" )
	{
		$display = $ordering?'':'style="display:none;"';
		$html = '<a href="javascript:void(0);" id="gridsaveorder" class="'.$task.'" title="'. $title .'" '.$display.'><span class="jdgrid"></span></a>';

		return $html;
	}

	public static function state( $filter_state='*', $published='Published', $unpublished='Unpublished', $archived=NULL, $trashed=NULL )
	{
		$state[] = JHTML::_('select.option',  '', JText::_( 'SELSTAT' ) );
		//Jinx : Why is this used ?
		//$state[] = JHTML::_('select.option',  '*', JText::_( 'Any' ) );
		$state[] = JHTML::_('select.option',  'P', JText::_( $published ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( $unpublished ) );

		if ($archived) {
			$state[] = JHTML::_('select.option',  'A', JText::_( $archived ) );
		}

		if ($trashed) {
			$state[] = JHTML::_('select.option',  'T', JText::_( $trashed ) );
		}

		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="inputbox" size="1" onchange="filterlist(this);"', 'value', 'text', $filter_state );
	}
	
	public static function search($filter_search = "", $id = "#filter_search", $min = 0)
	{
		
		$doc = JFactory::getDocument();
		
		$formid = substr_replace($id, '', 0, 1);
		
		$js = '$jd(function() {
			   	
				$jd("'.$id.'").live("keyup", function()	{
					
					if($jd(this).val().length < '.$min.')
						return;
					
					filterlist();
				
				});
			
			});';
			
		$doc->addScriptDeclaration($js);
		
		$html = '<input type="text" id="'.$formid.'" name="'.$formid.'" value="'.$filter_search.'" size="40" autocomplete="off" />';
		
		return $html;
		
	}

	public static function _checkedOut( &$row, $overlib = 1 )
	{
		$hover = '';
		if ( $overlib )
		{
			$text = addslashes(htmlspecialchars($row->editor));

			$date 	= JHTML::_('date',  $row->checked_out_time, JText::_('DATE_FORMAT_LC1') );
			$time	= JHTML::_('date',  $row->checked_out_time, '%H:%M' );

			$hover = '<span class="editlinktip hasTip" title="'. JText::_( 'CHECKOUT' ) .'::'. $text .'<br />'. $date .'<br />'. $time .'">';
		}
		$checked = $hover .'<img src="images/checked_out.png"/></span>';

		return $checked;
	}
	
	public static function loadmore($title = 'Load more', $total, $count)
	{
		
		if($total > $count)
			$text = '<a href="javascript:void(0);" onclick="startscroll();" class="loadmore">'.$title.'</a>';
		else
			$text = '<span class="loadmore">'.$title.'</span>';
		
		return $text;
	
	}
}
