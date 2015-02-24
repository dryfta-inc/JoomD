<?php
/**
 * $ModDesc
 * 
 * @version		$Id: helper.php $Revision
 * @package		modules
 * @subpackage	$Subpackage.
 * @copyright	Copyright (C) Dec 2009 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */
 // no direct access
defined('_JEXEC') or die;	 
?>
<div id="lofasc-<?php echo $module->id; ?>" class="lof-articlessroller" style="height:<?php echo $moduleHeight;?>; width:<?php echo $moduleWidth;?>">
<div class="<?php echo $class;?> lof-container <?php echo $params->get('thumbnail_alignment','');?>">
<?php if( $displayButton && $totalPages  > 1  ) : ?>
    <a class="lof-previous"  href="" onclick="return false;"><?php echo JText::_('Previous');?></a>
    <a class="lof-next" href="" onclick="return false;"><?php echo JText::_('Next');?></a>
<?php endif; ?>
<?php if(  $params->get('navigator_pos','top') && $totalPages  > 1 ) : ?>
    <!-- NAVIGATOR -->    
      <div class="lof-navigator-outer">
            <ul class="lof-navigator lof-bullets">
            <?php foreach( $pages as $key => $row ): ?>
                <li><span><?php echo  $key+1;?></span></li>
             <?php endforeach; ?> 		
            </ul>
        </div>
   <?php endif; ?>


 <!-- MAIN CONTENT of ARTICLESCROLLER MODULE --> 
  <div class="lof-main-wapper" style="height:<?php echo $moduleHeight;?>;width:<?php echo $moduleWidth;?>">
 		<?php foreach( $pages as $key => $list ): ?>
  		<div class="lof-main-item page-<?php echo $key+1;?>">
        		<?php foreach( $list as $i => $row ): ?>
        		 <div class="lof-row" style="width:<?php echo $itemWidth;?>%">
                   <?php require(  $itemLayoutPath ); ?>
				</div>      
                <?php  if( ($i+1) % $maxItemsPerRow == 0 && $i < count($list)-1 ) : ?>
                	<div class="lof-clearfix"></div>
                <?php endif; ?>       
                <?php endforeach; ?>
        </div> 
   		<?php endforeach; ?>
  </div>
 </div> 
  <!-- END MAIN CONTENT of ARTICLESCROLLER MODULE --> 
 </div> 
