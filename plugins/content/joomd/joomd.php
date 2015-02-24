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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentJoomd extends JPlugin
{


	function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}
		
		$db =& JFactory::getDBO();
		// simple performance check to determine whether bot should process further
		if ( JString::strpos( $article->text, 'loadtypes' ) === false and JString::strpos( $article->text, 'loadcats' ) === false and JString::strpos( $article->text, 'loaditems' ) === false and JString::strpos( $article->text, 'loaditem' ) === false ) {
			return true;
		}
		
		$lang = JFactory::getLanguage();
		$lang->load('com_joomd');
		
		// expression to search for (type list)
		$regex1 = '/{loadtypes}/i';
		
		// expression to search for (category list)
		$regex2 = '/{loadcats\s+([0-9]*?)}/i';
		
		// expression to search for (entry list)
		$regex3 = '/{loaditems\s+(.*?)}/i';
		
		// expression to search for (entry)
		$regex4 = '/{loaditem\s+(.*?)}/i';

 		// Find all instances of plugin and put in $matches for loadtypes
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex1, $article->text, $matches, PREG_SET_ORDER);
		// No matches, skip this
		if ($matches) {
			foreach ($matches as $match) {

				$output = $this->_loadtypes();
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);
				
			}
		}
		
		// Find all instances of plugin and put in $matches for loadcats
		preg_match_all($regex2, $article->text, $matches, PREG_SET_ORDER);
		// No matches, skip this
		if ($matches) {
			foreach ($matches as $match) {

				$output = $this->_loadcats($match[1]);
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);
				
			}
		}
		
		// Find all instances of plugin and put in $matches for loaditems
		preg_match_all($regex3, $article->text, $matches, PREG_SET_ORDER);
		// No matches, skip this
		if ($matches) {
			foreach ($matches as $match) {
				
				$temp = explode(',', $match[1]);
				
				$temp[1] = isset($temp[1])?$temp[1]:null;
				
				$output = $this->_loaditems($temp[0], $temp[1]);
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);
				
			}
		}
		
		// Find all instances of plugin and put in $matches for loaditem
		preg_match_all($regex4, $article->text, $matches, PREG_SET_ORDER);
		// No matches, skip this
		if ($matches) {
			foreach ($matches as $match) {

				$temp = explode(',', $match[1]);
				
				if(!(isset($temp[1]) and isset($temp[1])))
					continue;
				
				$output = $this->_loaditem($temp[0], $temp[1]);
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);
				
			}
		}
	
	}
	
	function _loadtypes()
	{
		
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		
		$query = 'select * from #__joomd_types where published = 1 and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		$html = '<ul class="joomdtypes">';
		
		for($i=0;$i<count($items);$i++)	{
			
			$html .= '<li><a href="'.JRoute::_('index.php?option=com_joomd&view=category&typeid='.$items[$i]->id).'">'.$items[$i]->name.'</a></li>';
			
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	
	function _loadcats($typeid)
	{
		
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		
		settype($typeid, 'int');
		
		if(!$typeid)
			return null;
		
		require_once(JPATH_SITE.DS.'components'.DS.'com_joomd'.DS.'libraries'.DS.'core.php');
		
		$type = Joomd::getType($typeid);
		
		$query = 'select catid from #__joomd_tnc where typeid = '.$typeid;
		$db->setQuery( $query );
		$ids = (array)$db->loadResultArray();
		
		$query = 'select * from #__joomd_category where published = 1 and access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$query .= count($ids)?(' and id in ('.implode(',', $ids).')'):'id=0';
		
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		$html = '<ul class="joomdcats">';
		
		for($i=0;$i<count($items);$i++)	{
			
			$html .= '<li><a href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&typeid='.$type->id.'&catid='.$items[$i]->id).'">'.$items[$i]->name.'</a></li>';
			
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	
	function _loaditems($typeid, $catid=null)
	{
		
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		
		settype($typeid, 'int');
		settype($catid, 'int');
		
		if(!$typeid)
			return null;
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/app.php');
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$type = Joomd::getType($typeid);
		
		$field = new JoomdAppField();
		$field->setType($type->id);
		
		$firstfield = $field->get_firstfield(array('published'=>1, 'cats'=>$catid));
		
		$query = 'select i.*, t.* from #__joomd_'.$type->app.' as i join #__joomd_type'.$type->id.' as t on i.id=t.itemid where i.published = 1';
		
		if($catid)	{
			$q = 'select itemid from #__joomd_'.$type->app.'_cat where catid = '.$catid;
			$db->setQuery( $q );
			$ids = (array)$db->loadResultArray();
			
			$query .= count($ids)?(' and id in ('.implode(',', $ids).')'):' and id=0';
			
		}

		$query .= ' and i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$query .= ' and i.typeid = '.$type->id;
				
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		$html = '<ul class="joomdcats">';
		
		for($i=0;$i<count($items);$i++)	{
			
			$value = $field->displayfieldvalue($items[$i]->id, $firstfield->id, true);
			
			$html .= '<li><a href="'.JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$type->id.'&id='.$items[$i]->id).'">'.$value.'</a></li>';
			
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	
	function _loaditem($typeid, $itemid)
	{
		
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		
		settype($typeid, 'int');
		settype($itemid, 'int');
		
		if(!$typeid)
			return null;
				
		require_once(JPATH_SITE.'/components/com_joomd/libraries/app.php');
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$app = new JoomdApp();;
		$app->initialize();
		
		$type = Joomd::getType($typeid);
		
		$field = new JoomdAppField();
		$field->setType($type->id);
		
		$query = 'select i.*, t.* from #__joomd_'.$type->app.' as i join #__joomd_type'.$type->id.' as t where i.published = 1 and i.id = '.$itemid.' and i.typeid = '.$type->id.' and i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		
		$db->setQuery( $query );
		$item = $db->loadObject();
		
		if(empty($item))
			return null;
			
		$fields =& $field->getFields(array('itemid'=>$item->id, 'published'=>1, 'detail'=>true));
		
		$html = '<div class="joomditem">';
		
		for($i=0;$i<count($fields);$i++)	{
			
			$value = $field->getfieldvalue($item->id, $fields[$i]->id);
			
			if(!empty($value))	{
				
				$html .= '<div class="field_block '.$fields[$i]->cssclass.'">';
			
				$html .= '<div class="field_label">';
				
				if($fields[$i]->showtitle)
					$html .= $fields[$i]->name;
				
				if($fields[$i]->showicon && !empty($fields[$i]->icon) && is_file(JPATH_SITE.'/images/joomd/'.$fields[$i]->icon))
					$html .= '&nbsp;<img src="images/joomd/'.$fields[$i]->icon.'" alt="'.$fields[$i]->name.'" style="max-height:16px;" align="absbottom" />';
				
				$html .= '</div>';
				
				$html .= '<div class="field_value">';
				
				$html .= $field->displayfieldvalue($item->id, $fields[$i]->id);
				
				$html .= '</div>';
				
				$html .= '</div>';
				
			}
			
		}
		
		$html .= '</div>';
			
		return $html;
				
	}

}