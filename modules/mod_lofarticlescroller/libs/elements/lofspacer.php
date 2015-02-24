<?php 
/**
 * $ModDesc
 * 
 * @version   $Id: $file.php $Revision
 * @package   modules
 * @subpackage  $Subpackage.
 * @copyright Copyright (C) November 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */
 
// no direct access
defined('_JEXEC') or die;
/**
 * Get a collection of categories
 */
class JFormFieldLofspacer extends JFormField {
	
	/*
	 * Category name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'fgroup'; 	
	
	/**
	 * fetch Element 
	 */
	protected function getInput(){		
		if (!defined ('LOF_LOADMEDIACONTROL')) {
			define ('LOF_LOADMEDIACONTROL', 1);
			$uri = str_replace(DS,"/",str_replace( JPATH_SITE, JURI::base (), dirname(__FILE__) ));
			$uri = str_replace("/administrator/", "", $uri);			
			JHTML::stylesheet($uri."/media/".'form.css');
			JHTML::script($uri."/media/".'form.js');
		}
		
		
		if( $this->title=='end_form'){
			?>
            	<script type="text/javascript">
					var panels = $$("#module-form .pane-sliders  > .panel").fade("out").removeClass("panel").addClass("lof-panel");
					var div = new Element("div", {"class":"lof-wrapper"});
					var container = new Element("div", {"class":"lof-container"});
					container.innerHTML='<fieldset class="fs-form"><legend><?php echo JText::_("Module Setting")?></legend><div class="lof-toolbars"></div><div class="lof-fscontainer"></div></legend></fieldset>';
					var _toolbar = container.getElement(".lof-toolbars");
					var _container = container.getElement(".lof-fscontainer");
					$$("#module-form .pane-sliders").adopt(  div.adopt(container) );
					new LofForm(panels, _toolbar, _container );
				</script>
            <?php
		}
//    $text   = (string)$this->element['text']?(string)$this->element['text']:'';
 ///   return '<div class="lof-header">'.JText::_($text).'</div>';
	}		
	function getLabel(){
		return ;	
	}
}

?>
