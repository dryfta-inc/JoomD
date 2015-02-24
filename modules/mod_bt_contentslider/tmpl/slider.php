<?php
/**
 * @package 	mod_bt_contentslider - BT ContentSlider Module
 * @version		1.4
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2011 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$nav_top = (-1)*(int)$params->get( 'navigation_top', 0 );
$nav_right = (-1)*(int)$params->get( 'navigation_right', 0 ) + 5;
?>
<?php if( $totalPages  > 1 ): ?>
<script type="text/javascript">	
	if(typeof(btcModuleIds)=='undefined'){var btcModuleIds = new Array();var btcModuleOpts = new Array();}
	btcModuleIds.push(<?php echo $module->id; ?>);
	btcModuleOpts.push({
			preload: true,
			slideEasing : '<?php echo $params->get('effect', 'easeInQuad'); ?>',
			fadeEasing : '<?php echo $params->get('effect', 'easeInQuad'); ?>',
			effect: '<?php echo $params->get('next_back_effect', 'slider'); ?>, <?php echo $params->get('bullet_effect', 'slider'); ?>',
			preloadImage: 'modules/mod_bt_contentslider/tmpl/images/loading.gif',
			generatePagination: <?php if($params->get( 'butlet', 0 )>0) echo 'true'; else echo 'false'; ?>,
			paginationClass: 'bt_handles',
			generateNextPrev:false,
			prependPagination:true,
			play: <?php echo $auto_start?(int)$params->get('interval', '5000'):0; ?>,						
			hoverPause: <?php if($params->get( 'pause_hover', 1 )>0) echo 'true'; else echo 'false'; ?>,	
			pause: 100,
			slideSpeed : <?php echo (int)$params->get('duration', '1000')?>,
			autoHeight:<?php echo $params->get( 'auto_height', 0 )>0?'true':'false'; ?>,
			fadeSpeed : <?php echo (int)$params->get('duration', '1000')?>			
	});
	</script>
	<?php if(!$butlet) $totalPages = 0; ?>
	<?php if($butlet || $next_back) { ?>
	<style>
		<?php if(abs($nav_top) == 0 &&  trim($params->get('content_title') =="" )){ ?>
		#btcontentslider<?php echo $module->id; ?>{
			padding-top:20px;
		}
		<?php } ?>
		#btcontentslider<?php echo $module->id; ?> .bt_handles{
			top:<?php echo $nav_top + 14 ?>px!important;
			right:<?php echo $nav_right ?>px!important;
		}
		#btcontentslider<?php echo $module->id; ?> a.next{
			top:<?php //echo $nav_top+12 ?>150px!important;
			right:<?php // echo $nav_right + 14 * $totalPages+5 ?>0px!important;
		}
		#btcontentslider<?php echo $module->id; ?> a.prev{
			top:<?php //echo $nav_top + 12 ?>150px!important;
			left:<?php // echo $nav_right + 14 * $totalPages +19?>0px!important;
		}
		#btcontentslider<?php echo $module->id; ?> .bt_handles li{
			background:none;
			padding:0;
			margin:0 1px;
		} 
</style>
<?php } ?>
<?php else: ?>
<script type="text/javascript">	
	(function(){
		jQuery('#btcontentslider<?php echo $module->id; ?>').fadeIn("fast");
	})();
</script>
<?php endif; ?>