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


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JoomdAppRnr extends JoomdApp	{
	
	function __construct()
	{
		
		parent::__construct();
		
		$this->initialize();
		
	}
	
	function initialize()
	{
		
		static $init = false;
		
		if($init)
			return;
				
		$this->loadLanguage();
				
		$init = true;
		
	}
	
	function loadLanguage()
	{
		
		static $loaded = false;
		
		if($loaded)
			return true;
		
		$lang = JFactory::getLanguage();
		
		$lang->load('app_rnr', JDPATH_BASE);
		
		$loaded = true;
		
		return true;
		
	}
	
	function panel_display()
	{
		
		$doc = JFactory::getDocument();
		
		$db =  JFactory::getDBO();
		
		$tabs = Joomdui::getTabs();
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
						
		$query = 'select i.id, i.typeid, count(r.id) as count from #__joomd_item as i join #__joomd_reviews as r on (i.id=r.itemid and i.typeid=r.typeid) group by i.id order by count desc limit 4';
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		$js = '
		
		function drawrnrchart()	{
			
			var rnritems = [["'.JText::_('ITEM').'", "'.JText::_('REVIEWS').'"]';
		
			foreach($items as $item)	{
				$this->_field = new JoomdAppField();
				$firstfield = $this->_field->get_firstfield();
				$value = $this->_field->displayfieldvalue($item->id, $firstfield->id, true);
				$js .= ', ["'.$value.'", '.$item->count.']';
			}
			
			$js .= '];
		
			var rnrdata = google.visualization.arrayToDataTable(rnritems);
					
			var rnroptions = {
			  title: "'.JText::_('ITEM_REVIEWS').'",
			  backgroundColor:"#eee",
			  width:$jd(".jd_cp_block").width()-43
			};
	
			var rnrchart = new google.visualization.PieChart(document.getElementById("rnr_chart_div"));
			rnrchart.draw(rnrdata, rnroptions);
		
		}
		
		if(typeof google !== "undefined")
			google.setOnLoadCallback(drawrnrchart);
			
		$jd(function() {
			$jd( "#cp_rnr_tab" ).tabs({
				event: "click",
				selected: 1,
				fx: { opacity: "toggle" },
				collapsible: true
			});
			
		});
		';
				
		$doc->addScriptDeclaration($js);
		
		$html = '<div id="cp_rnr_tab">';
		
		$html .= '<ul>
			<li><a href="#cp_rnr_tab1">'.JText::_('ITEMS').'</a></li>
			<li><a href="#cp_rnr_tab2">'.JText::_('CHART').'</a></li>
		</ul>';
		$html .= '<div id="cp_rnr_tab1">';
		
		$query = 'select id, name, comment from #__joomd_reviews where name <> "" limit 4';
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		 $html .= '<div class="adminlist class_panel_b ">';
		
		$html .= '<div class="tr_bo_header"><div class="class_panel_h">'.JText::_('TITLE').'</div><div class="class_panel_hd">'.JText::_('COMMENT').'</div><div class="clr"></div></div>';
		
		for($i=0;$i<count($items);$i++)	{
			
			$html .= '<div class="cont_par_box"><div class="left_hit_box" ><a href="index.php?option=com_joomd&view=rnr&layout=form&cid[]='.$items[$i]->id.'">'.$items[$i]->name.'</a></div><div class="right_hit_box width_sx">'.substr($items[$i]->comment, 0, 15).'...</div><div class="clr"></div></div>';
			
		}
		
		$html .= '</div>';
		
		$html .= '</div>';
		
		$html .= '<div id="cp_rnr_tab2"><div id="rnr_chart_div"></div></div>';
		
		$html .= '</div>';
		
		return $html;
		
	}
	
	function getConfig()
	{
		$db =  JFactory::getDBO();
		$query = 'select * from #__joomd_rnrconfig';
		$db->setQuery( $query );
		$config = $db->loadAssoc();
		
		return $config;
		
	}
	
	function config_display()
	{
		
		$db =  JFactory::getDBO();
		
		$query = 'select * from #__joomd_rnrconfig';
		$db->setQuery( $query );
		$config = $db->loadObject();
		$config->access = $config->comment_access;
		$lists['comment_access'] = JHTML::_('list.accesslevel',  $config );
		
		ob_start();
		
		?>
        	
			<table border="0" width="100%">
			<tr>
				<td valign="top" width="35%">
					<fieldset class="adminform">
					<legend><?php echo JText::_( 'GENERALCONFIG' ); ?></legend>
					<table class="admintable">
						<tr>
							<td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_COMMENTENABLE'); ?>"><?php echo JText::_('COMMENTENABLE'); ?></label></td>
							<td><input type="checkbox" name="comment_enable" id="comment_enable" <?php if($config->comment_enable) echo 'checked="checked"'; ?> value="1" /></td>
						</tr>
                        <tr>
							<td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_MODERATECOMMENTS'); ?>"><?php echo JText::_('COMMENTTYPE'); ?></label></td>
							<td>
                            	<select name="comment_type" id="comment_type">
                                	<option value="1" <?php if($config->comment_type==1) echo 'selected="selected"'; ?>><?php echo JText::_('DEFAULT'); ?></option>
                                    <option value="2" <?php if($config->comment_type==2) echo 'selected="selected"'; ?>><?php echo JText::_('FACEBOOK'); ?></option>
                                </select></td>
						</tr>
                        <tr>
							<td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_COMMENTACCESS'); ?>"><?php echo JText::_('COMMENTACCESS'); ?></label></td>
							<td><?php echo $lists['comment_access']; ?></td>
						</tr>
                        <tr>
							<td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_MODERATECOMMENTS'); ?>"><?php echo JText::_('MODERATECOMMENTS'); ?></label></td>
							<td><input type="checkbox" name="moderate" id="moderate" <?php if($config->moderate) echo 'checked="checked"'; ?> value="1" /></td>
						</tr>
                        <!--<tr>
							<td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_AEOTMCOMMENTS'); ?>"><?php echo JText::_('AEOTMCOMMENTS'); ?></table></td>
							<td><input type="checkbox" name="omoderate" id="omoderate" <?php if($config->omoderate) echo 'checked="checked"'; ?> value="1" /></td>
						</tr>-->
					</table>
					</fieldset>
				</td>
			</tr>
			</table>
 
        <?php
		
		$html = ob_get_contents();
		
		ob_clean();
		
		return $html;
		
	}
	
	function config_save($post, $parent)
	{
		
		$db =  JFactory::getDBO();
		
		$post = JRequest::get('post');
		
		$comment_enable	= JRequest::getInt('comment_enable', 0);
		$comment_type	= JRequest::getInt('comment_type', 0);
		$comment_access	= JRequest::getInt('access', 0);
		$moderate		= JRequest::getInt('moderate', 0);
		$omoderate		= JRequest::getInt('omoderate', 0);		
		
		$insert = new stdClass();
		
		$insert->id				= 1;
		$insert->comment_enable	= $comment_enable;
		$insert->comment_type	= $comment_type;
		$insert->comment_access	= $comment_access;
		$insert->moderate		= $moderate;
		$insert->omoderate		= $omoderate;
				
		if(!$db->updateObject('#__joomd_rnrconfig', $insert, 'id'))	{
			$parent->setError($db->stderr());
			return false;
		}
		
		return true;
		
	}
	
	function onAfterDisplay()
	{
		
		$config = JoomdAppRnr::getConfig();
		$user =  JFactory::getUser();
				
		if(!$config['comment_enable'])
			return null;
			
		$db =  JFactory::getDBO();
		
		$type = Joomd::getType();
		$id = JRequest::getInt('id', 0);
		
		$query = 'select i.*, if(u.id, u.name, "'.JText::_('ANONYMOUS').'") as creator from #__joomd_reviews as i left join #__users as u on i.created_by = u.id where i.published = 1 and i.typeid = '.(int)$type->id.' and i.itemid = '.$id.' order by created desc';
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		require(dirname(__FILE__).'/includes/rnr.php');
		
	}
	
	function display_stars($rate=0)
	{
		
		ob_start();
		
		?>
		<div class="item_rating_stars">
          <?php
				$VotingPercentage = $rate*20;
			?>
          <ul class="item_rating_list">
            <li class="item_current_rating" id="item_current_rating" style="width:<?php echo $VotingPercentage; ?>%;"></li>
            <li><a href="javascript:void(0);" rel="1" title="<?php echo JText::_('POOR'); ?>" class="one-star">1</a></li>
            <li><a href="javascript:void(0);" rel="2" title="<?php echo JText::_('AVERAGE'); ?>" class="two-stars">2</a></li>
            <li><a href="javascript:void(0);" rel="3" title="<?php echo JText::_('GOOD'); ?>" class="three-stars">3</a></li>
            <li><a href="javascript:void(0);" rel="4" title="<?php echo JText::_('VERYGOOD'); ?>" class="four-stars">4</a></li>
            <li><a href="javascript:void(0);" rel="5" title="<?php echo JText::_('EXCELLENT'); ?>" class="five-stars">5</a></li>
          </ul>
          <input type="hidden" name="review_rate" id="review_rate" value="<?php echo $rate; ?>" />
          <div class="clr"></div>
        </div>
		
        <?php
		
		$html = ob_get_contents();
		
		ob_end_clean();
        
		return $html;
		
	}
	
	function post_review()
	{
		
		$config = JoomdAppRnr::getConfig();
		$user =  JFactory::getUser();
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		
		if(!$config['comment_enable'])	{
			$obj->error = JText::_('NOTSUPPORTEDREQUEST');
			return $obj;
		}
		
		if(!in_array($config['comment_access'], $user->getAuthorisedViewLevels()))	{
			$obj->error =  JText::_('UNAUTHORIZEACC');
			return $obj;
		}
		
		$post = JRequest::get('post');
		$post['id'] = 0;
		
		$post['published'] = $config['moderate']?0:1;
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomd'.DS.'tables');
		$row =  JTable::getInstance('rnr', 'Table');
		
		if(!$row->bind($post))	{
			$obj->error = $row->getError();
			return $obj;
		}
		
		if(!$row->check())	{
			$obj->error = $row->getError();
			return $obj;
		}
		
		// If there was an error with registration, set the message and display form
		if ( !$row->store() )
		{
			$obj->error = $row->getError();
			return $obj;
		}
		
		$obj->result = 'success';
		
		$obj->msg = $config['moderate']?JText::_('REVIEWPOSTSUCCESS_M_MSG'):JText::_('REVIEWPOSTSUCCESS_MSG');
		
		return $obj;
		
	}
	
}