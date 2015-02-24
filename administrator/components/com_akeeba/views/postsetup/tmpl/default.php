<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 1.3
 */

defined('_JEXEC') or die('Restricted access');

$disabled = AKEEBA_PRO ? '' : 'disabled = "disabled"';

?>
<div id="akeeba-container" style="width:100%">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="postsetup" />
	<input type="hidden" name="task" id="task" value="save" />
	<?php echo JHTML::_( 'form.token' ); ?>
	
	<p><?php echo JText::_('AKEEBA_POSTSETUP_LBL_WHATTHIS'); ?></p>
	
	<input type="checkbox" id="srp" name="srp" <?php if($this->enablesrp): ?>checked="checked"<?php endif; ?> <?php echo $disabled?> />
	<label for="srp" class="postsetup-main"><?php echo JText::_('AKEEBA_POSTSETUP_LBL_SRP')?></label>
	</br>
	<?php if(AKEEBA_PRO): ?>
	<div class="postsetup-desc"><?php echo JText::_('AKEEBA_POSTSETUP_DESC_SRP');?></div>
	<?php else: ?>
	<div class="postsetup-desc"><?php echo JText::_('AKEEBA_POSTSETUP_NOTAVAILABLEINCORE');?></div>
	<?php endif; ?>
	<br/>

	<input type="checkbox" id="autoupdate" name="autoupdate" <?php if($this->enableautoupdate): ?>checked="checked"<?php endif; ?> <?php echo $disabled?> />
	<label for="autoupdate" class="postsetup-main"><?php echo JText::_('AKEEBA_POSTSETUP_LBL_AUTOUPDATE')?></label>
	</br>
	<?php if(AKEEBA_PRO): ?>
	<div class="postsetup-desc"><?php echo JText::_('AKEEBA_POSTSETUP_DESC_autoupdate');?></div>
	<?php else: ?>
	<div class="postsetup-desc"><?php echo JText::_('AKEEBA_POSTSETUP_NOTAVAILABLEINCORE');?></div>
	<?php endif; ?>
	<br/>
	
	<input type="checkbox" id="confwiz" name="confwiz" <?php if($this->enableconfwiz): ?>checked="checked"<?php endif; ?> />
	<label for="confwiz" class="postsetup-main"><?php echo JText::_('AKEEBA_POSTSETUP_LBL_confwiz')?></label>
	</br>
	<div class="postsetup-desc"><?php echo JText::_('AKEEBA_POSTSETUP_DESC_confwiz');?></div>
	<br/>
	
	<br/>
	<button onclick="this.form.submit(); return false;"><?php echo JText::_('AKEEBA_POSTSETUP_LBL_APPLY');?></button>

</form>
</div>