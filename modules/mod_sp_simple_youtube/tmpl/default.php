<?php
/*------------------------------------------------------------------------
# mod_sp_simple_youtube - Youtube Module by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php if ($youtube_id) { ?>
<iframe title="Simple youtube module by JoomShaper.com" id="sp-simple-youtube<?php echo $uniqid ?>" type="text/html" width="<?php echo $width ?>" height="<?php echo $height ?>" src="http://www.youtube.com/embed/<?php echo $youtube_id ?>?wmode=Opaque" frameborder="0" allowFullScreen></iframe>
<?php } else { ?>
	<p>Please enter youtube id.</p>
<?php } ?>	
