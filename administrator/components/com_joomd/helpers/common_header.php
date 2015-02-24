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

defined('_JEXEC') or die('Direct Access to this location is not allowed.');	?>

  <div id="toolbar-box">
    <div class="m">
      <?php echo $this->toolbar->render(); ?>
      <?php echo $this->toolbarTitle; ?>
    </div>
  </div>
 <div class="popoup">
 <div id="system-message-container">
  <dl id="system-message">
  <dt class="error">Error</dt>
  <dd class="error message">
  <?php if (!empty( $this->errors)) : ?>
        <ul>
        <?php 
          foreach ($this->errors as $error) : 
            echo '<li>' . $error . '</li>';
          endforeach;
        ?>    
        </ul>
    <?php endif; ?>
  </dd>

  <dt class="message">Message</dt>
  <dd class="message message">
  </dd>
  
  <?php
  	if(isset($this->notice))	{
		if(!empty($this->notice))	{
			$doc =  JFactory::getDocument();
			$js = '$jd(function(){$jd(".popoup #system-message").show();});';
			$doc->addScriptDeclaration($js);
			echo '<dt class="notice">Notice</dt><dd class="notice message"><ul><li>'.$this->notice.'</li></ul></dd>';
		}
	}
	
  ?>
  
  </dl>
</div>