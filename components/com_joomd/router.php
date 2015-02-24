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

/**
 * @param	array	A named array
 * @return	array
 */
function JoomdBuildRoute( &$query )
{
	$db = JFactory::getDBO();
	
	$segments = array();
	
	$app = JFactory::getApplication();
	
	$menu = $app->getMenu();
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
	}
	
	
	
	if(!isset($query['view']))
		unset($query['typeid']);
	
	if(isset($query['typeid']))	{
		$typeid = (int)$query['typeid'];
		unset($query['typeid']);
	}
	else
		$typeid = 1;
	
	if(isset($query['view']))	{
		
		$q = 'select alias from #__joomd_types where id = '.$typeid;
		$db->setQuery( $q );
		$segments[] = $db->loadResult();
		
		switch($query['view'])	{
			
			case 'joomd':
			$segments[] = get_text('FRONTPAGE');
			break;
			
			case 'category':
				$segments[] = get_text('CATEGORIES');
								
				if(isset($query['catid']))	{
					$q = 'select alias from #__joomd_category where id = '.(int)$query['catid'];
					$db->setQuery( $q );
					$alias = $db->loadResult();
					
					$segments[] = empty($alias)?$query['catid']:$alias;
					
					unset($query['catid']);
				}
				
				if(isset($query['featured']))	{
					
					$segments[] = get_text('FEATURED');
					
					unset($query['featured']);
				}
				
			break;
			
			case 'item':
				
				$segments[] = get_text('JDITEMS');
								
				if(isset($query['userid']))	{
					
					$segments[] = get_text('AUTHOR');
					
					$q = 'select username from #__users where id = '.(int)$query['userid'];
					$db->setQuery( $q );
					$segments[] = $db->loadResult();
										
					unset($query['userid']);
				}
				
				if(isset($query['catid']))	{
					$q = 'select alias from #__joomd_category where id = '.(int)$query['catid'];
					$db->setQuery( $q );
					$alias = $db->loadResult();
					
					$segments[] = get_text('CATEGORY');
					
					$segments[] = empty($alias)?$query['catid']:$alias;
					
					unset($query['catid']);
				}
				
				if(isset($query['featured']))	{
					
					$segments[] = get_text('FEATURED');
					
					unset($query['featured']);
				}
				
				if(isset($query['layout']))	{
					$segments[] = get_text('VIEW');
					
					if(isset($query['id']))	{
						$q = 'select alias from #__joomd_item where id = '.(int)$query['id'];
						$db->setQuery( $q );
						$alias = $db->loadResult();
						
						$segments[] = empty($alias)?$query['id']:$alias;
						
						unset($query['id']);
					}
					unset($query['layout']);
				}
				
			break;
		
			case 'itempanel' :
				
				$segments[] = get_text('YOURITEMS');
								
				if(isset($query['layout']))	{
					
					if(isset($query['cid']) and $query['cid'][0]>0)	{
						$segments[] = get_text('EDIT');
						$q = 'select alias from #__joomd_item where id = '.(int)$query['cid'][0];
						$db->setQuery( $q );
						$alias = $db->loadResult();
						
						$segments[] = empty($alias)?$query['cid'][0]:$alias;
						
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = 'new';
						unset($query['cid']);
					}
					else	
						$segments[] = get_text('NEW');
						
					unset($query['layout']);
				}
				
			break;
			
			case 'search':
			
				if(isset($query['layout']) and $query['layout'] == "search")	{
					$segments[] = get_text('SEARCHRESULTS');
					unset($query['layout']);
				}
				else
					$segments[] = get_text('SEARCH');
			
			break;
		
		}
		
		unset($query['view']);
	}
	
	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/banners/task/bid/Itemid
 *
 * index.php?/banners/bid/Itemid
 */
function JoomdParseRoute( $segments )
{	
	$db =  JFactory::getDBO();
	$vars = array();

	// view is always the first element of the array
	$count = count($segments);
	
	$language = JFactory::getLanguage();
	
	$language->load('com_joomd');
		
	$q = 'select id from #__joomd_types where alias = '.$db->Quote(str_replace(':', '-', $segments[0]));
	$db->setQuery( $q );
	$id = $db->loadResult();
	
	$vars['typeid'] = empty($id)?$segments[0]:$id;
	
	$count--;
	array_shift( $segments );
	
	if ($count)
	{
		
		$segments[0] = str_replace(':', '-', $segments[0]);
		
		switch($segments[0])	{
			
			case get_text('FRONTPAGE'):
				$vars['view'] = 'joomd';
				$count--;
				array_shift( $segments );
			break;
			
			case get_text('CATEGORIES'):
				$vars['view'] = 'category';
				$count--;
				array_shift( $segments );
				
				if($count and $segments[0] <> get_text('FEATURED'))	{	
					$q = 'select id from #__joomd_category where alias = '.$db->Quote(str_replace(':', '-', $segments[0]));
					$db->setQuery( $q );
					$id = $db->loadResult();
					
					$vars['catid'] = empty($id)?$segments[0]:$id;
									
					$count--;
					array_shift( $segments );
				}
				
				if($count)
					$vars['featured'] = 1;
				
			break;
			
			case get_text('JDITEMS'):
			
				$vars['view'] = 'item';
				
				$count--;
				array_shift( $segments );
				
				if($count)	{
				
					if($segments[0] == get_text('VIEW'))	{
						$vars['layout'] = 'detail';
						$count--;
						array_shift( $segments );
						
						if($count)	{
							
							$q = 'select id from #__joomd_item where alias = '.$db->Quote(str_replace(':', '-', $segments[0]));
							$db->setQuery( $q );
							$id = $db->loadResult();
							
							$vars['id'] = empty($id)?$segments[0]:$id;
							
							$count--;
							array_shift( $segments );
							
						}
						
					}
					
					else	{
						
						if($count and $segments[0] == get_text('AUTHOR'))	{
							
							$count--;
							array_shift( $segments );
							
							if($count)	{
							
								$q = 'select id from #__users where username = '.$db->Quote(str_replace(':', '-', $segments[0]));
								$db->setQuery( $q );
								$id = $db->loadResult();
								
								$vars['userid'] = empty($id)?$segments[0]:$id;
												
								$count--;
								array_shift( $segments );
							
							}
							
						}
						
						if($count and $segments[0] == get_text('CATEGORY'))	{
							
							$count--;
							array_shift( $segments );
							
							if($count)	{
							
								$q = 'select id from #__joomd_category where alias = '.$db->Quote(str_replace(':', '-', $segments[0]));
								$db->setQuery( $q );
								$id = $db->loadResult();
								
								$vars['catid'] = empty($id)?$segments[0]:$id;
												
								$count--;
								array_shift( $segments );
								
							}
							
						}
						
						if($count)
							$vars['featured'] = 1;
							
						$count--;
						array_shift( $segments );
					}
				
				}
				
			break;
			
			case get_text('YOURITEMS'):
				
				$vars['view'] = 'itempanel';
				
				$count--;
				array_shift( $segments );
				
				if($count)	{
				
					$vars['layout'] = 'form';
					
					$count--;
					array_shift( $segments );
					
					if($count)	{
						$q = 'select id from #__joomd_item where alias = '.$db->Quote(str_replace(':', '-', $segments[0]));
						$db->setQuery( $q );
						$id = $db->loadResult();
						
						$vars['cid'][] = empty($id)?$segments[0]:$id;
						
						$count--;
						array_shift( $segments );
					}
				
				}
			
			break;
			
			case get_text('SEARCHRESULTS'):
				
				$vars['view'] = 'search';
				$vars['layout'] = 'search';
				
				$count--;
				array_shift( $segments );
					
				$count--;
				array_shift( $segments );
				
			break;
			
			case get_text('SEARCH'):
				
				$vars['view'] = 'search';
				
				$count--;
				array_shift( $segments );
				
			break;
			
		}
		
	}

	return $vars;
}

function get_text($text)
{
	
	return str_replace(' ', '-', (strtolower(JText::_($text))));
	
}