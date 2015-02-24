<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		3.2.20 June 19, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureSystemMessages extends GantryFeature {
    var $_feature_name = 'systemmessages';
	
	function render($position="") {
	    $app =& JFactory::getApplication(); 
	    
//	    /* dummy msgs for testing */
//	    $app->enqueueMessage('This is a message');
//	    $app->enqueueMessage('This is a error', 'error');
//	    $app->enqueueMessage('This is a notice', 'notice');
	    
	    $msgs = $app->getMessageQueue();
	    
	    ob_start();
	    if (sizeof($msgs) > 0 ) :
	    ?>
		<div class="clear"></div>
		<jdoc:include type="message" />
		<?php
		endif;
	    return ob_get_clean();
	}
}