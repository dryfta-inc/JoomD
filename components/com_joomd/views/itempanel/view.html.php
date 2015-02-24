<?php
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
 
class JoomdViewItempanel extends JView
{
	
    function display($tpl = null)
    {
		$mainframe =  JFactory::getApplication();
		
		$layout = JRequest::getCmd('layout', '');
		
		$document	=  JFactory::getDocument();
		$params =  $mainframe->getParams();
		
		$this->jdapp = Joomd::getApp();
		
		$menus		=  $mainframe->getMenu();
		$menu    	= $menus->getActive();
		
		$type = Joomd::getType();
		$this->assignRef('type', $type);
		
		$field = new JoomdAppField();
		
		$this->assignRef('field', $field);
		
		$config = Joomd::getConfig('item');
		$this->assignRef('config', $config);
		
		$user = Joomd::getUser('item');
		$this->assignRef('user', $user);
		
		$toolbar = Joomd::getToolbar();
        $this->assignRef('toolbar', $toolbar);
		
		$multiselect = Joomdui::getMultiselect();
		$this->assignRef('multiselect', $multiselect);
		
		$cats = $this->get('Cats');
		$this->assignRef( 'cats', $cats );
		
		$cparams =  $this->get('Params');
		$this->assignRef('cparams', $cparams);
		
		$abase = JRequest::getInt('abase', 0);
		
		if($layout == 'form')	{
					
			$item =  $this->get('Item');
			$isNew		= ($item->id < 1);
			
			joomdui::createform(array('formname'=>'listform', 'list'=>'.itemlist', 'editform'=>'editform', 'edittable'=>'edittable'));
			
			$this->pane = Joomdui::getAccordion();
						
			$pagetitle = $isNew?JText::_( 'NEWITEM' ):JText::_( 'EDITITEM' );
			
			if (is_object($menu)) {
				if ($params->get( 'page_title')) {
					$pagetitle = $params->get( 'page_title', $pagetitle);
				}
			}
			
			$params->set('page_title',	$pagetitle);
			
			$this->assignRef('toolbarTitle', $toolbar->evtitle( $params->get('page_title'), 'item' ));
			
			if(!$abase)
				$toolbar->cancel();
			
			if(!$user->get('guest') and $abase)
				$toolbar->apply();
			
			$toolbar->save();
			
			$this->assignRef('item', $item);
			
			$notice = $this->get('Notice');
			$this->assignRef('notice', $notice);

		}
		
		else	{
						
			$pagetitle = JText::_('YOOURENTEIES');
			
			if (is_object($menu)) {
				
				if ($params->get( 'page_title')) {
					$pagetitle = $params->get( 'page_title', $pagetitle);
				}
			}
			
			$params->set('page_title', $pagetitle);
			
			$toolbar->title( $params->def('page_title'), 'item' );
			$toolbar->add();
        	$toolbar->delete();
			
			$items = $this->get('Items');
			
			$firstfield =  $field->get_firstfield();
			$this->assignRef('firstfield', $firstfield);
			
			Joomdui::createlist($cparams, array('formname'=>'listform', 'list'=>'.itemlist tbody', 'sortable'=>false));
			
			$lists['state']	= JHTML::_('jdgrid.state',  $cparams->filter_state );
			$lists['search']= JHTML::_('jdgrid.search',  $cparams->filter_search );
			
			//language filter start
			$lists['language'] = '<select id="filter_language" class="inputbox" onchange="filterlist(this);" size="1" name="filter_language">';
			$lists['language'] .= '<option value="">'.JText::_( 'SELECT_LANGUAGE' ).'</option>';
			$lists['language'] .= JHTML::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' );
			$lists['language'] .= '</select>';
			//language filter end
			
			//creating the list of categories 
			$lists['cat'] = '<select name="filter_cat" id="filter_cat" class="inputbox" onchange="filterlist(this);"><option value="">'.JText::_( 'SELCATEGORY' ).'</option>';
						
			for($i=0;$i<count($cats);$i++)	{
				$lists['cat'] .= '<option value="'.$cats[$i]->id.'"';
				if($cats[$i]->id==$cparams->filter_cat)
					$lists['cat'] .= ' selected="selected"';
				$lists['cat'] .= '>'.$cats[$i]->treename.'</option>';
			}
			
			$lists['cat'] .= '</select>';
			
			// Table ordering.
			$lists['order_Dir'] = $cparams->filter_order_Dir;
			$lists['order']     = $cparams->filter_order;

			$this->assignRef( 'items', $items );
		
		}
		
		$document->setTitle( $params->def('page_title') );
		
		$params->set('page_title', $mainframe->get('JComponentTitle'));
		
		$this->assignRef( 'params', $params );
		$this->assignRef( 'lists', $lists );
		
		$theme = Joomd::get('Theme');
		$this->addTemplatePath('components/com_joomd/templates/'.$theme.'/itempanel');
		
		parent::display($tpl);

    }
}
