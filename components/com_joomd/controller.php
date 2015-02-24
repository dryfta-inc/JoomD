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
 
jimport('joomla.application.component.controller');
 
class JoomdController extends JController
{
   
   var $_typeid=null;
   
    function display($cachable = false, $urlparams = false)
    {
        parent::display();
    }
	
	function captcha_display()
	{
		require_once(JPATH_SITE.'/components/com_joomd/libraries/elements/joomdcaptcha/joomdcaptcha.php');
		
		$c = new Joomdcaptcha();
		$c->onJDCaptchaDisplay();

	}
	
	function edit()
	{
		
		ob_start();
		
		parent::display();
		
		$body = ob_get_contents();
		
		ob_end_clean();
				
		$document = JFactory::getDocument();
		
		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();
		
		$head = '';
		
	/*	//removed because of the slow response of javascript in edit view
		foreach($document->_styleSheets as $k=>$v)	{
			
			$head .= $tab.'<link rel="stylesheet" href="'.$k.'" type="'.$v['mime'].'" />'.$lnEnd;
		}
	*/	
		foreach($document->_style as $k=>$v)	{
			
			$head .= $tab.'<style type="'.$k.'">'.$v.'</style>'.$lnEnd;
		}
	/*	
		$scripts = array('includes/js/joomla.javascript.js', 'media/system/js/mootools.js', 'tiny_mce.js');
		
		foreach($document->_scripts as $k=>$v)	{
			if(!(strpos($k, $scripts[0]) or strpos($k, $scripts[1]) or strpos($k, $scripts[2])))
				$head .= $tab.'<script type="'.$v.'" src="'.$k.'" />'.$lnEnd;
	
		}
	*/	
		foreach($document->_custom as $custom) {
			$head .= $tab.$custom.$lnEnd;
		}
		
	
		foreach($document->_script as $k=>$v)
			$head .= $tab.'<script type="'.$k.'">'.$v.'</script>'.$lnEnd;
			
		$html = $head.$body;
		
		return $html;
		
	}
	
	protected function checkAccess()
	{
				
		return true;
		
	}
		
	//to display social sharing buttons etc
	function afterdisplay()
	{
		
		
	}
	
	//to signup for the newsletter
	function newsletter_signup()
	{
		
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$model = $this->getModel('category');
		
		$obj = $model->newsletter_signup();
		
		echo json_encode($obj);
		
	}
	
	function app_task()
	{
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		
		$action = JRequest::getVar('action', '');
		
		list($app, $func) = explode('-', $action);
		
		if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
			require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
				
			$class = "JoomdApp".ucfirst($app);
			
			if(class_exists($class))	{
			
				$class = new $class;
				
				if(method_exists($class, $func))	{
					
					$obj = $class->$func();
					
				}
				else
					$obj->error = JText::_('NOTSUPPORTEDREQUEST');
			
			}
			else
				$obj->error = JText::_('NOTSUPPORTEDREQUEST');
			
		}
		else
			$obj->error = JText::_('APPNOTFOUND');
			
		echo json_encode($obj);
		
	}
	
	function mod_task()
	{
		JRequest::checkToken() or jexit( '{"result":"error", "error":"'.JText::_('INVALIDTOKEN').'"}' );
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		
		$action = JRequest::getVar('action', '');
		
		list($mod, $func) = explode('-', $action);
		
		if(!empty($mod) and is_file(JPATH_ROOT.DS.'modules'.DS.'mod_'.$mod.DS.'helper.php'))	{
				
			require_once(JPATH_ROOT.DS.'modules'.DS.'mod_'.$mod.DS.'helper.php');
				
			$class = "mod".$mod."Helper";
			
			if(class_exists($class))	{
			
				$class = new $class;
				
				if(method_exists($class, $func))	{
					
					$lang = JFactory::getLanguage();
					$lang->load('mod_'.$mod);
					
					$title = JRequest::getVar('title', '');
					
					$module = JModuleHelper::getModule($mod, $title );					
					
					// Get module parameters
					$params = new JRegistry;
					$params->loadString($module->params);
					
					$obj = $class->$func($params);
					
				}
				else
					$obj->error = JText::_('NOTSUPPORTEDREQUEST');
			
			}
			else
				$obj->error = JText::_('NOTSUPPORTEDREQUEST');
			
		}
		else
			$obj->error = JText::_('MODULENOTFOUND');
			
		echo json_encode($obj);
		
	}
	
	//rss feed url
	function rss()
	{
		
		$post = JRequest::get('get');
		unset($post['task']);
		
		$lang = JFactory::getLanguage();
		
		$tag = $lang->get('tag');
		
		$type = Joomd::getType();
		
		$model = $this->getModel($type->app);
		
		$items = $model->getItems();
		
		require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/field/app_field.php');
		
		$_field = new JoomDAppField();
		
		$firstfield = $_field->get_firstfield(array('published'=>1, 'type'=>1));
		
		$uri = JURI::getInstance();
		$uri->setQuery($post);
		
		$url = JURI::root().substr(JRoute::_('index.php'.$uri->toString(array('query', 'fragment'))), strlen(JURI::base(true)) + 1);
		
		header('Content-Type: application/rss+xml; charset=utf-8');
		
		ob_start();
		
	?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?php echo $type->name; ?></title>
        <link><?php echo $url; ?></link>
        <description><?php echo $type->descr; ?></description>
        <language><?php echo $tag; ?></language>
        <copyright><?php echo JURI::root(); ?></copyright>
        
        <?php for($i=0;$i<count($items);$i++):	?>
        
		<item>
			<title><?php echo $_field->displayfieldvalue($items[$i]->id, $firstfield->id, array('short'=>true)); ?></title>
			<link><?php echo JURI::root().substr(JRoute::_('index.php?option=com_joomd&view='.$type->app.'&layout=detail&typeid='.$items[$i]->typeid.'&id='.$items[$i]->id), strlen(JURI::base(true)) + 1); ?></link>
        </item>
        
        <?php	endfor;	?>
        
    </channel>
</rss>
        
	<?php
		
		$xml = ob_get_contents();
		
		ob_end_clean();
		
		jexit($xml);
		
	}

}
