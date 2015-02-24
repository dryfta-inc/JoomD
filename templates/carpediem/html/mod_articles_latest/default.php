<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   3.2.16 February 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

// no direct access
defined('_JEXEC') or die;

    

 ?>
<ul class="latestnews">
<?php foreach ($list as $item) :  ?>
	<li>
		<?php
                   //jexit(print_r($item));  
               preg_match('|src="(.*)"|U', $item->introtext , $match);
               
                             //   print_r($match);
           
                $mat = substr_replace($match[0], '/', 0, 0);
                 
                                //  echo '</br>'.$mat;
                 
                                  ?>
                                  
              <?php if($mat) {?><div class="img-mod_latest"> <img <?php echo $mat ?>  border="0"  /></div> <?php }?>                  
        <a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
      <span class="author_name">  <?php echo ' By : '.$item->author; ?> </span>, <span class="created_date"><?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $item->item->created, JText::_('DATE_FORMAT_LC3'))); ?></span>
        
        <a class="readmore" href="<?php echo $item->link; ?>">Read more</a>
        <div class="clr"></div>
	</li>
<?php endforeach; ?>
</ul>