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

$config = $this->config;

if($config->social->get('share') or $config->social->get('fblike') or $config->social->get('tweet') or $config->social->get('gplus'))	{
	
?>

<div id="jd_social">
            
    <!-- AddThis Button BEGIN -->
    <div class="addthis_toolbox addthis_default_style ">
    <?php if($config->social->get('fblike'))	{	?>
    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
    <?php	}	?>
    <?php if($config->social->get('tweet'))	{	?>
    <a class="addthis_button_tweet"></a>
    <?php	}	?>
    <?php if($config->social->get('gplus'))	{	?>
    <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
    <?php	}	?>
    <?php if($config->social->get('share'))	{	?>
    <a class="addthis_counter addthis_pill_style"></a>
    <?php	}	?>
    </div>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4f7c20ae7f1af32c"></script>
    <!-- AddThis Button END -->

</div>

<?php	}	?>