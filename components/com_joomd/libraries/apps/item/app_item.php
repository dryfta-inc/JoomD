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
defined('_JEXEC') or die();

require_once(JPATH_SITE.'/components/com_joomd/libraries/app.php');

class JoomdAppItem extends JoomdApp	{
	
	
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
		
		$lang->load('app_item', JDPATH_BASE);
		
		$loaded = true;
		
		return true;
		
	}
	
	function getConfig()
	{
		$query = 'select * from #__joomd_iconfig';
		$this->_db->setQuery( $query );
		$item = $this->_db->loadAssoc();
		
		$registry = new JRegistry;
		$registry->loadString($item['config']);
		$item['config'] = $registry;
		
		$registry = new JRegistry;
		$registry->loadString($item['acl']);
		$item['acl'] = $registry;
		
		$registry = new JRegistry;
		$registry->loadString($item['listconfig']);
		$item['listconfig'] = $registry;
		
		$registry = new JRegistry;
		$registry->loadString($item['detailconfig']);
		$item['detailconfig'] = $registry;
		
		return $item;
		
	}
	
	function panel_display()
	{
		
		$doc = JFactory::getDocument();
		
		$this->_field = new JoomdAppField();
				
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$query = 'select id, typeid, hits from #__joomd_item order by hits desc limit 4';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		$js = '
		
		function drawentrychart()	{
			
			var entryitems = [["'.JText::_('ITEMS').'", "'.JText::_('HITS').'"]';
		
			foreach($items as $item)	{
				$this->_field->setType($item->typeid);
				$firstfield = $this->_field->get_firstfield();
				$value = $this->_field->displayfieldvalue($item->id, $firstfield->id, array('short'=>true));
				$js .= ', ["'.$value.'", '.$item->hits.']';
			}
			
			$js .= '];
		
			var entrydata = google.visualization.arrayToDataTable(entryitems);
					
			var entryoptions = {
			  title: "'.JText::_('ITEM_HITS').'",
			  backgroundColor:"#eee",
			  width:$jd(".jd_cp_block").width()-43
			};
	
			var entrychart = new google.visualization.PieChart(document.getElementById("entry_chart_div"));
			entrychart.draw(entrydata, entryoptions);
		
		}
		
		if(typeof google !== "undefined")
			google.setOnLoadCallback(drawentrychart);
			
		$jd(function() {
			$jd( "#cp_entry_tab" ).tabs({
				event: "click",
				selected: 1,
				fx: { opacity: "toggle" },
				collapsible: true
			});
		});
		';
				
		$doc->addScriptDeclaration($js);
		
		$html = '<div id="cp_entry_tab">';
		
		$html .= '<ul>
			<li><a href="#cp_entry_tab1">'.JText::_('ITEMS').'</a></li>
			<li><a href="#cp_entry_tab2">'.JText::_('CHART').'</a></li>
		</ul>';
		$html .= '<div id="cp_entry_tab1">';
		
		$html .= '<div class="adminlist class_panel_b">';
		
		$html .= '<div class="tr_bo_header"><div class="class_panel_h">'.JText::_('ITEMS').'</div><div class="class_panel_hd">'.JText::_('HITS').'</div><div class="clr"></div></div>';
		
		for($i=0;$i<count($items);$i++)	{
			
			$this->_field->setType($items[$i]->typeid);
			$firstfield = $this->_field->get_firstfield();
			
			$value = $this->_field->displayfieldvalue($items[$i]->id, $firstfield->id, array('short'=>true));
			
			$html .= '<div class="cont_par_box"><div class="left_hit_box" ><a href="index.php?option=com_joomd&view=item&layout=form&cid[]='.$items[$i]->id.'">'.$value.'</a></div><div class="right_hit_box">'.$items[$i]->hits.'</div><div class="clr"></div></div>';
			
		}
		
		$html .= '</div>';
		
		$html .= '</div>';
		
		$html .= '<div id="cp_entry_tab2"><div id="entry_chart_div"></div></div>';
		
		$html .= '</div>';
		
		return $html;
		
	}
	
	function config_display()
	{
				
		$query = 'select * from #__joomd_iconfig';
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		$registry = new JRegistry;
		$registry->loadString($item->config);
		$item->config = $registry;
		
		$registry = new JRegistry;
		$registry->loadString($item->acl);
		$item->acl = $registry;
		
		$registry = new JRegistry;
		$registry->loadString($item->listconfig);
		$item->listconfig = $registry;
		
		$registry = new JRegistry;
		$registry->loadString($item->detailconfig);
		$item->detailconfig = $registry;
		
		ob_start();
		
		?>
        	
            <table border="0" width="100%">
            <tr>
            	<td valign="top" width="35%">
                	<fieldset class="adminform">
					<legend><?php echo JText::_( 'USERCONFIG' ); ?></legend>
                	<table class="admintable">
                    	<tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_NOTIFY'); ?>"><?php echo JText::_('NOTIFY_ITEM_OWNER'); ?></label></td>
                            <td><input type="checkbox" name="config[notify]" id="confignotify" <?php if($item->config->get('notify')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_MODERATE_ITEMS'); ?>"><?php echo JText::_('MODERATE_ITEMS'); ?></label></td>
                            <td><input type="checkbox" name="config[moderate]" id="configmoderate" <?php if($item->config->get('moderate')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_ITEM_LIST'); ?>"><?php echo JText::_('ITEM_LIST'); ?></label></td>
                            <td><?php echo JHtml::_('access.assetgrouplist', 'config[list]', $item->config->get('list'), 'class="inputbox"') ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_ITEM_DETAIL'); ?>"><?php echo JText::_('ITEM_DETAIL'); ?></label></td>
                            <td><?php echo JHtml::_('access.assetgrouplist', 'config[detail]', $item->config->get('detail'), 'class="inputbox"') ?></td>
                        </tr>
                    </table>
                    </fieldset>
                </td>
                <td valign="top" rowspan="2">
                	<fieldset class="adminform">
					<legend><?php echo JText::_( 'LIST_LAYOUT' ); ?></legend>
                	<table class="admintable">
                    	<tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_HEADER'); ?>"><?php echo JText::_('SHOWHEAD'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[header]" id="listconfigheader" <?php if($item->listconfig->get('header')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MORE_BUTTON'); ?>"><?php echo JText::_('MOREINFOBUTT'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[more]" id="listconfigmore" <?php if($item->listconfig->get('more')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_RSS_FEED_BUTTON'); ?>"><?php echo JText::_('RSS_FEED_BUTTON'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[rss]" id="listconfigrss" <?php if($item->listconfig->get('rss')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_ADD_BUTTON'); ?>"><?php echo JText::_('SHOWADDBUTTON'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[add]" id="listconfigadd" <?php if($item->listconfig->get('add')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CONTACT_FORM'); ?>"><?php echo JText::_('DISPLAYCONTACT_FORM'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[contact]" id="listconfigcontact" <?php if($item->listconfig->get('contact')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_REPORT_ITEM'); ?>"><?php echo JText::_('DISPLAYREPORT_ITEM'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[report]" id="listconfigreport" <?php if($item->listconfig->get('report')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_SAVE_ITEM'); ?>"><?php echo JText::_('DISPLAYSAVE_ITEM'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[save]" id="listconfigsave" <?php if($item->listconfig->get('save')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_HITS'); ?>"><?php echo JText::_('DISPLAYHITS'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[hits]" id="listconfighits" <?php if($item->listconfig->get('hits')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CREATION_DATE'); ?>"><?php echo JText::_('DISPLAYCREATION_DATE'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[created]" id="listconfigcreated" <?php if($item->listconfig->get('created')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_AUTHOR'); ?>"><?php echo JText::_('DISPLAYAUTHOR'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[author]" id="listconfigauthor" <?php if($item->listconfig->get('author')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_DATE'); ?>"><?php echo JText::_('DISPLAYMODIFIED_DATE'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[modified]" id="listconfigmodified" <?php if($item->listconfig->get('modified')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_BY'); ?>"><?php echo JText::_('DISPLAYMODIFIED_BY'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[modified_by]" id="listconfigmodified_by" <?php if($item->listconfig->get('modified_by')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_PRINT_ICON'); ?>"><?php echo JText::_('PRINTI'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[print]" id="listconfigprint" <?php if($item->listconfig->get('print')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_EMAIL_ICON'); ?>"><?php echo JText::_('EMAILI'); ?></label></td>
                            <td><input type="checkbox" name="listconfig[email]" id="listconfigemail" <?php if($item->listconfig->get('email')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                    </table>
                    </fieldset>
                </td>
                <td valign="top" rowspan="2">
                	<fieldset class="adminform">
					<legend><?php echo JText::_( 'DETAIL_LAYOUT' ); ?></legend>
                	<table class="admintable">
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_ADD_BUTTON'); ?>"><?php echo JText::_('SHOWADDBUTTON'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[add]" id="detailconfigadd" <?php if($item->detailconfig->get('add')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CONTACT_FORM'); ?>"><?php echo JText::_('DISPLAYCONTACT_FORM'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[contact]" id="detailconfigcontact" <?php if($item->detailconfig->get('contact')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_REPORT_ITEM'); ?>"><?php echo JText::_('DISPLAYREPORT_ITEM'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[report]" id="detailconfigreport" <?php if($item->detailconfig->get('report')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_SAVE_ITEM'); ?>"><?php echo JText::_('DISPLAYSAVE_ITEM'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[save]" id="detailconfigsave" <?php if($item->detailconfig->get('save')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_HITS'); ?>"><?php echo JText::_('DISPLAYHITS'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[hits]" id="detailconfighits" <?php if($item->detailconfig->get('hits')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_CREATION_DATE'); ?>"><?php echo JText::_('DISPLAYCREATION_DATE'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[created]" id="detailconfigcreated" <?php if($item->detailconfig->get('created')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_AUTHOR'); ?>"><?php echo JText::_('DISPLAYAUTHOR'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[author]" id="detailconfigauthor" <?php if($item->detailconfig->get('author')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_DATE'); ?>"><?php echo JText::_('DISPLAYMODIFIED_DATE'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[modified]" id="detailconfigmodified" <?php if($item->detailconfig->get('modified')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_MODIFIED_BY'); ?>"><?php echo JText::_('DISPLAYMODIFIED_BY'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[modified_by]" id="detailconfigmodified_by" <?php if($item->detailconfig->get('modified_by')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_PRINT_ICON'); ?>"><?php echo JText::_('PRINTI'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[print]" id="detailconfigprint" <?php if($item->detailconfig->get('print')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_ITEM_EMAIL_ICON'); ?>"><?php echo JText::_('EMAILI'); ?></label></td>
                            <td><input type="checkbox" name="detailconfig[email]" id="detailconfigemail" <?php if($item->detailconfig->get('email')) echo 'checked="checked"'; ?> value="1" /></td>
                        </tr>
                    </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
            	<td valign="top">
                	<fieldset class="adminform">
					<legend><?php echo JText::_( 'ACL_SETTINGS' ); ?></legend>
                	<table class="admintable">
                    	<tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_ADD_ITEM'); ?>"><?php echo JText::_('ADD_ITEM'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[addaccess][]', $item->acl->get('addaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_EDIT_OWN_ITEM'); ?>"><?php echo JText::_('EDIT_OWN_ITEM'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[editaccess][]', $item->acl->get('editaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_EDIT_ALL_ITEM'); ?>"><?php echo JText::_('EDIT_ALL_ITEM'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[editall][]', $item->acl->get('editall'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_EDIT_OWN_STATE'); ?>"><?php echo JText::_('EDIT_OWN_STATE'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[stateaccess][]', $item->acl->get('stateaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_EDIT_ALL_STATE'); ?>"><?php echo JText::_('EDIT_ALL_STATE'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[stateall][]', $item->acl->get('stateall'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_DELETE_OWN_ITEM'); ?>"><?php echo JText::_('DELETE_OWN_ITEM'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[deleteaccess][]', $item->acl->get('deleteaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_DELETE_ALL_ITEM'); ?>"><?php echo JText::_('DELETE_ALL_ITEM'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[deleteall][]', $item->acl->get('deleteall'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_FEATURE_OWN_ITEM'); ?>"><?php echo JText::_('FEATURE_OWN_ITEM'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[featureaccess][]', $item->acl->get('featureaccess'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                        <tr>
                            <td class="key"><label class="hasTip" title="<?php echo JText::_('TT_FEATURE_ALL_ITEM'); ?>"><?php echo JText::_('FEATURE_ALL_ITEM'); ?></label></td>
                            <td><?php echo JHtml::_('access.usergroup', 'acl[featureall][]', $item->acl->get('featureall'), 'class="multiple" multiple="multiple" size="5"', false) ?></td>
                        </tr>
                    </table>
                    </fieldset>
                </td>
            </tr>
            </table>
        <?php
		
		$html = ob_get_contents();
		
		ob_end_clean();
		
		return $html;
		
	}
	
	function config_save($post, $parent)
	{
				
		$post = JRequest::get('post');

		
		$config	= JRequest::getVar('config', array(), 'post', 'array');
		$acl	= JRequest::getVar('acl', array(), 'post', 'array');
		$listconfig	= JRequest::getVar('listconfig', array(), 'post', 'array');
		$detailconfig	= JRequest::getVar('detailconfig', array(), 'post', 'array');
		
		$insert = new stdClass();
		
		$insert->id				= 1;
		$insert->config			= json_encode($config);
		$insert->acl			= json_encode($acl);
		$insert->listconfig		= json_encode($listconfig);
		$insert->detailconfig	= json_encode($detailconfig);
		
		if(!$this->_db->updateObject('#__joomd_iconfig', $insert, 'id'))	{
			$parent->setError($this->_db->stderr());
			return false;
		}
		
		return true;
		
	}
	
}