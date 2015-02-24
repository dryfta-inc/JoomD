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
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );
 
class JoomdModelItem extends JModel
{
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe =  JFactory::getApplication();
		
		$context			= 'com_joomd.item.list.'; 
        // Get pagination request variables
        $this->_limit = JRequest::getInt('limit', $mainframe->getCfg('list_limit'));
		$this->_limitstart = JRequest::getInt('limitstart', 0 );
 
        // In case limit has been changed, adjust it
        $this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);
		
		$menus		=  $mainframe->getMenu();
		$menu    	= $menus->getActive();
		
		//get the order by from the menu parameters
		$params = $mainframe->getParams();
		
		if(is_object($menu))	{
		if(!strstr($menu->link, 'view=item'))
			$params->set('orderby', 'i.ordering asc');
		}
		
		$orderby = explode(' ', $params->def('orderby', 'i.ordering asc'));

		
		$orderby[1] = isset($orderby[1])?$orderby[1]:'';
		
		$this->_filter_order	= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', $orderby[0], 'cmd' );
        $this->_filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', $orderby[1], 'word' );
		$this->_filter_search = $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '',	'string' );
		$this->_filter_search = JString::strtolower( $this->_filter_search );
		
		$this->_akey			= $mainframe->getUserStateFromRequest( $context.'akey', 'akey', '',	'string' );
		$this->_akey			= JString::strtolower( $this->_akey );
		
		$this->typeid = $mainframe->getUserStateFromRequest( $context.'typeid', 'typeid', 0,	'int' );
		$this->_type = Joomd::getType($this->typeid);
		
		$this->userid = JRequest::getInt('userid', 0);
		$this->catid = JRequest::getInt('catid', 0);
		$this->featured = JRequest::getInt('featured', 0);
		$this->Itemid = JRequest::getVar('Itemid', '');
		
		$this->_user = Joomd::getUser();
		
		$this->config =  Joomd::getConfig('item');
				
		$this->_field = new JoomdAppField();
		
		$this->setId();

	}
	
	function setId()
	{
				 
		$id = JRequest::getInt('id', 0);
		
		$layout = JRequest::getCmd('layout', '');
		
		if($layout == "detail")	{
			
			if(!$id)
				$this->jdapp->redirect('index.php?option=com_joomd&view=item', JText::_('NOEF'));
			
		}
		
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
		
	}
	
	function getUser()
	{
		
		return Joomd::getUser();
		
	}
	
	function _buildQuery()
	{
		
		$query = 'select i.*, date_format(i.created, "%D %b, %Y %h:%i %p") as created, date_format(i.modified, "%D %b, %Y %h:%i %p") as modified, t.*, nullif(u.id,null) as created_by, if(u.id, u.name, "'.JText::_('ANONYMOUS').'") as creator from #__joomd_item as i join #__joomd_type'.$this->typeid.' as t on i.id=t.itemid left join #__users as u on i.created_by=u.id';

		return $query;
		
	}
	
	function getItem()
	{
		
		$date = JFactory::getDate();
		$now = $date->toMySQL();
		
		$query = $this->_buildQuery();
		
		$where = array();
		
		$where[] = 'i.published = 1';
		$where[] = 'i.id = '.$this->_id;
		
		$where[] = 'i.access in ('.implode(',', $this->_user->getAuthorisedViewLevels()).')';
		
		$where[] = 'i.publish_up <= '.$this->_db->Quote($now);
				
		$where[] = '( i.publish_down >= '.$this->_db->Quote($now).' or i.publish_down = "0000-00-00 00:00:00" )';
		
		$query .= ' where ' . implode(' and ', $where);
		
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		$registry = new JRegistry;
		
		$registry->loadString($item->metadata);
		$item->metadata = $registry;
		
		$item->save = $this->getSaved($item->id);
		
		$this->typeid = $item->typeid;
		
		$this->hit();
		
		return $item;
	
	}
	
	function hit()
	{
						
		$date =  JFactory::getDate();
		$now = $date->toMySQL();
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if($this->_user->get('guest'))	{
			$query = 'select count(*) from #__joomd_user_item where ip = "'.$ip.'" and itemid = '.$this->_id.' and datediff("'.$now.'", hit_date) < 1';
		}
		else	{
			$query = 'select count(*) from #__joomd_user_item where userid = '.$this->_user->id.' and itemid = '.$this->_id.' and datediff("'.$now.'", hit_date) < 1';
		}
		
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			return true;
		}
		
		if($this->_user->get('guest'))	{
			
			$query = 'insert into #__joomd_user_item (itemid, hit_date, hits, ip) values ('.$this->_id.', '.$this->_db->Quote($now).', 1, '.$this->_db->Quote($ip).')';
			
			$this->_db->setQuery( $query );
			$this->_db->query();
			echo $this->_db->getErrorMsg();
		}
		
		else	{
			
			$query = 'insert into #__joomd_user_item (itemid, hit_date, hits, userid) values ('.$this->_id.', '.$this->_db->Quote($now).', 1, '.$this->_user->id.')';
			
			$this->_db->setQuery( $query );
			$this->_db->query();
			echo $this->_db->getErrorMsg();
		}
		
		$query = 'update #__joomd_item set hits = hits + 1 where id = ' . $this->_id;
		$this->_db->setQuery( $query );
		$this->_db->query();
		echo $this->_db->getErrorMsg();
		
		return true;
		
	}
	
	function getSaved($id)
	{
		
		if($this->_user->get('guest'))	{
			$ip = $_SERVER['REMOTE_ADDR'];
			$query = 'select save from #__joomd_user_item where itemid = '.(int)$id.' and ip = '.$this->_db->Quote($ip);
		}
		else	{
			$query = 'select save from #__joomd_user_item where itemid = '.(int)$id.' and userid = '.$this->_user->id;
		}
		
		$this->_db->setQuery( $query );
		$save = $this->_db->loadResult();
		
		return intval($save);
		
	}
	
	function &getItems()
    {
        if(empty($this->_data))	{
		
			$query = $this->_buildQuery();
			
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			
			$this->_data = $this->_getList($query, $this->_limitstart, $this->_limit);
			
		}
				
		for($i=0;$i<count($this->_data);$i++)	{
			
			$registry = new JRegistry;
			$registry->loadString($this->_data[$i]->metadata);
			$this->_data[$i]->metadata = $registry;
			
			$this->_data[$i]->save = $this->getSaved($this->_data[$i]->id);
			
		}

        return $this->_data;
    
	}
	
	function _buildItemFilter()
	{
 
        $where = array();
		
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$date = JFactory::getDate();
		$now = $date->toMySQL();
		
		
		if($app->getLanguageFilter())	{
			$where[] = 'i.language in ('.$this->_db->quote($lang->getTag()).', '.$this->_db->Quote('*').')';
		}
		
		$where[] = 'i.published = 1';

		$where[] = 'i.access in ('.implode(',', $this->_user->getAuthorisedViewLevels()).')';
		
		$where[] = 'i.typeid = '.$this->typeid;
		
		$where[] = 'i.publish_up <= '.$this->_db->Quote($now);
				
		$where[] = '( i.publish_down >= '.$this->_db->Quote($now).' or i.publish_down = "0000-00-00 00:00:00" )';
		
		if($this->catid)	{
			
			$query = 'select itemid from #__joomd_item_cat where catid = '.$this->catid;
			$this->_db->setQuery( $query );
			$entries = $this->_db->loadResultArray();
			
			$where[] = count($entries)?'i.id in ('.implode(', ', $entries).')':'i.id=0';
			
		}
		
		if($this->featured)	{
			$where[] = 'i.featured = 1';
		}
		
		if($this->userid)	{
			$where[] = 'i.created_by = '.$this->userid;
		}
		
		$firstfield =  $this->_field->get_firstfield(array('published'=>1));
		
		if($this->config->asearch and !empty($this->_akey) and $this->_akey <> strtolower(JText::_("ALL")))	{
			
			$akey = substr($this->_akey, 0, 1);
			
			if($akey == '#')	{
				$where[] = 'lower(t.field_'.$firstfield->id.') regexp '.$this->_db->Quote( '^[0-9]+', false );
			}
			else
				$where[] = 'lower(t.field_'.$firstfield->id.') like '.$this->_db->Quote( $akey.'%', false );
			
		}
					
		$filter = ' where ' . implode(' and ', $where);
 
        return $filter;
	}
	
	function _buildItemOrderBy()
	{
 
        $orderby = ' group by i.id order by '.$this->_filter_order.' '.$this->_filter_order_Dir;
 
        return $orderby;
	}
	
	function getParams()
  	{	
		
		$layout = JRequest::getCmd('layout', 'default');
		
		$this->item = new stdClass();
		
		$this->item->option = 'com_joomd';
		$this->item->view = 'item';
		$this->item->abase = 1;
		$this->item->task = '';
		$this->item->typeid = $this->typeid;
		$this->item->catid = $this->catid;
		$this->item->userid = $this->userid;
		$this->item->featured = $this->featured;
		$this->item->Itemid = $this->Itemid;
		
		if($layout <> "detail")	{
			
			$this->item->limit = $this->_limit;
			$this->item->limitstart = $this->_limitstart;
			$this->item->akey = $this->_akey;
			$this->item->filter_search = $this->_filter_search;
			$this->item->filter_order = $this->_filter_order;
			$this->item->filter_order_Dir = $this->_filter_order_Dir;
			
			// Load the total if it doesn't already exist
			if (empty($this->_total)) {
				$query = $this->_buildQuery();
				$filter = $this->_buildItemFilter();
				$query .= $filter;
				$orderby = $this->_buildItemOrderBy();
				$query .= $orderby;
				$this->item->total = (int) $this->_getListCount($query);    
			}
		
		}
		
        return $this->item;
  	}
	
	function getCategory()
	{
		
		if($this->catid)	{
			
			$query = 'select * from #__joomd_category where published = 1 and id = '.$this->catid;
			$this->_db->setQuery( $query );
			$item = $this->_db->loadObject();
			
			return $item;
			
		}
		
	}
	
	function checkCategoryAccess($id=null)
	{
		
		$id = empty($id)?$this->_id:(int)$id;
		
		$query = 'select * from #__joomd_category where id in ( select catid from #__joomd_item_cat where itemid = '.$id.' )';
		$this->_db->setQuery( $query );
		$items = (array)$this->_db->loadObjectList();
		
		if(count($items) == 1)	{
			return Joomd::CanAccessItem($items[0]);
		}
		
		foreach($items as $item)	{
			
			if(Joomd::CanAccessItem($item))
				return true;
			
		}
		
		return false;
		
	}
	
	function report_item()
	{
				
		$app = JFactory::getApplication();
		$sitename = $app->getCfg('sitename');
		
		$obj = new stdClass();
		
		$obj->result = "error";
		
		$id = JRequest::getInt('id', 0);
		$email = JRequest::getVar('email', '');
		$comment = JRequest::getVar('comment', '');
		
		$date =  JFactory::getDate();
		$now = $date->toMySQL();
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if(!$id)	{
			$obj->error = JText::_('ITEMNOTFOUND');
			return $obj;
		}
		
		if(empty($email))	{
			$obj->error = JText::_('ENTEREMAIL');
			return $obj;
		}
		if(empty($comment))	{
			$obj->error = JText::_('ENTERCOMMENT');
			return $obj;
		}
		
		if($this->_user->get('guest'))	{
			$query = 'select count(*) from #__joomd_user_item where ip = "'.$ip.'" and itemid = '.$id.' and datediff("'.$now.'", report) < 1';
		}
		else	{
			$query = 'select count(*) from #__joomd_user_item where userid = '.$this->_user->id.' and itemid = '.$id.' and datediff("'.$now.'", report) < 1';
		}
		
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			$obj->error = JText::_('ALREADYREPORTED');
			return $obj;
		}
		
		$subject = JText::sprintf('REPORT_SUBJECT', $sitename);
		
		$body = JText::sprintf('REPORT_MAIL_BODY', $email, $comment);
		
		$sent = Joomd::notify($subject, $body);
		
		if(is_object($sent))	{
			$obj->error = JText::_('SORRYNOTREPORTED');
			return $obj;
		}
		
		$obj->result = 'success';
		$obj->msg = JText::_('SUCCESSFUL_REPORT');
		
		if($this->_user->get('guest'))	{
			
			$query = 'select count( * ) from #__joomd_user_item where itemid = '.$id.' and ip = '.$this->_db->Quote($ip);
			$this->_db->setQuery( $query );
			$count = $this->_db->loadResult();
			
			if($count)	{
				$query = 'update #__joomd_user_item set report = '.$this->_db->Quote($now).' where itemid = '.$id.' and ip = '.$this->_db->Quote($ip);
			}
			else	{
				$query = 'insert into #__joomd_user_item (itemid, report, ip) values ('.$id.', '.$this->_db->Quote($now).', '.$this->_db->Quote($ip).')';
			}
			
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$obj->error = $this->_db->getErrorMsg();
				return $obj;
			}
			
		}
		
		else	{
			
			$query = 'select count( * ) from #__joomd_user_item where itemid = '.$id.' and userid = '.$this->_user->id;
			$this->_db->setQuery( $query );
			$count = $this->_db->loadResult();
			
			if($count)	{
				$query = 'update #__joomd_user_item set report = '.$this->_db->Quote($now).' where itemid = '.$id.' and userid = '.$this->_user->id;
			}
			else	{
				$query = 'insert into #__joomd_user_item (itemid, report, userid) values ('.$id.', '.$this->_db->Quote($now).', '.$this->_user->id.')';
			}
			
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$obj->error = $this->_db->getErrorMsg();
				return $obj;
			}
			
		}

		return $obj;
				
	}
	
	function save_item()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = new stdClass();
		
		$obj->result = "error";
		
		$id = JRequest::getInt('id', 0);
		
		if($this->_user->get('guest'))	{
			$obj->error = JText::_('LOGINFIRST');
			return $obj;
		}
		
		if(!$id)	{
			$obj->error = JText::_('ITEMNOTFOUND');
			return $obj;
		}
			
		$query = 'select * from #__joomd_user_item where userid = '.$this->_user->id.' and itemid = '.$id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		$save = empty($item)?0:$item->save;
		
		if($save)	{
			$obj->error = JText::_('ITEMALREADYSAVED');
			return $obj;
		}
		
		if(empty($item))
			$query = 'insert into #__joomd_user_item (userid, itemid, save) values ('.$this->_user->id.', '.$id.', 1)';
		else
			$query = 'update #__joomd_user_item set save = 1 where userid = '.$this->_user->id.' and itemid = '.$id;
			
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$obj->error = $this->_db->getErrorMsg();
			return $obj;
		}
		
		$obj->result = 'success';
		$obj->msg = JText::_('SAVEDSUCCESSFULLY');
		
		return $obj;
				
	}
	
	function remove_item()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = new stdClass();
		
		$obj->result = "error";
		
		$id = JRequest::getInt('id', 0);
		
		if($this->_user->get('guest'))	{
			$obj->error = JText::_('LOGINFIRST');
			return $obj;
		}
		
		if(!$id)	{
			$obj->error = JText::_('ITEMNOTFOUND');
			return $obj;
		}
		
		$query = 'select * from #__joomd_user_item where userid = '.$this->_user->id.' and itemid = '.$id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		$saved = empty($item)?0:$item->save;
		
		if(!$saved)	{
			$obj->error = JText::_('NOT_INCLUDED_IN_YOUR_LIST');
			return $obj;
		}
		
		$query = 'update #__joomd_user_item set save = 0 where userid = '.$this->_user->id.' and itemid = '.$id;
			
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$obj->error = $this->_db->getErrorMsg();
			return $obj;
		}
		
		$obj->result = 'success';
		$obj->msg = JText::_('REMOVED_SUCCESSFULLY');
		
		return $obj;
				
	}
	
	function contact_item()
	{
		
		$obj = new stdClass();
		$obj->result = "error";
		
		$id = JRequest::getInt('id', 0);
		$name = JRequest::getVar('name', '');
		$email = JRequest::getVar('email', '');
		$phone = JRequest::getVar('phone', '');
		$enquiry = JRequest::getVar('enquiry', '');
		
		$date =  JFactory::getDate();
		$now = $date->toMySQL();
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if(!$id)	{
			$obj->error = JText::_('ITEMNOTFOUND');
			return $obj;
		}
		if(empty($name))	{
			$obj->error = JText::_('ENTERNAME');
			return $obj;
		}
		if(empty($email))	{
			$obj->error = JText::_('ENTEREMAIL');
			return $obj;
		}
		if(empty($enquiry))	{
			$obj->error = JText::_('ENTERENQUIRY');
			return $obj;
		}
		
		if($this->_user->get('guest'))	{
			$query = 'select count(*) from #__joomd_user_item where ip = "'.$ip.'" and itemid = '.$id.' and datediff("'.$now.'", contact) < 1';
		}
		else	{
			$query = 'select count(*) from #__joomd_user_item where userid = '.$this->_user->id.' and itemid = '.$id.' and datediff("'.$now.'", contact) < 1';
		}
		
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			$obj->error = JText::_('ALREADYCONTACTED');
			return $obj;
		}
		
		$app = JFactory::getApplication();
		$sitename = $app->getCfg('sitename');
		
		$subject = JText::sprintf('CONTACT_SUBJECT', $sitename);
		
		$body = JText::sprintf('CONTACT_MAIL_BODY', $name, $email, $phone, $enquiry);
				
		$params['to'] = empty($this->config->email)?array():explode(',', $this->config->email);
		
		if($this->_type->config->get('notify'))	{
			
			$item = $this->getItem();
			
			if($item->created_by)	{
				$o = JFactory::getUser($item->created_by);
				if(!empty($o->email))
					array_push($params['to'], $o->email);
			}
		}
		
		$sent = Joomd::notify($subject, $body, $params);
		
		if(is_object($sent))	{
			$obj->error = JText::_('SORRYENQUIRYNOTSENT');
			return $obj;
		}
		
		
		$obj->result = 'success';
		$obj->msg = JText::_('SUCCESSFUL_ENQUIRY');
		
		if($this->_user->get('guest'))	{
			
			$query = 'select count( * ) from #__joomd_user_item where itemid = '.$id.' and ip = '.$this->_db->Quote($ip);
			$this->_db->setQuery( $query );
			$count = $this->_db->loadResult();
			
			if($count)	{
				$query = 'update #__joomd_user_item set contact = '.$this->_db->Quote($now).' where itemid = '.$id.' and ip = '.$this->_db->Quote($ip);
			}
			else	{
				$query = 'insert into #__joomd_user_item (itemid, contact, ip) values ('.$id.', '.$this->_db->Quote($now).', '.$this->_db->Quote($ip).')';
			}
			
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$obj->error = $this->_db->getErrorMsg();
				return $obj;
			}
			
		}
		
		else	{
			
			$query = 'select count( * ) from #__joomd_user_item where itemid = '.$id.' and userid = '.$this->_user->id;
			$this->_db->setQuery( $query );
			$count = $this->_db->loadResult();
			
			if($count)	{
				$query = 'update #__joomd_user_item set contact = '.$this->_db->Quote($now).' where itemid = '.$id.' and userid = '.$this->_user->id;
			}
			else	{
				$query = 'insert into #__joomd_user_item (itemid, contact, userid) values ('.$id.', '.$this->_db->Quote($now).', '.$this->_user->id.')';
			}
			
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$obj->error = $this->_db->getErrorMsg();
				return $obj;
			}
			
		}
			
		return $obj;
				
	}
	
}
