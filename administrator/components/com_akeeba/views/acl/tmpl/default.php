<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: default.php 681 2011-06-01 08:50:04Z nikosdion $
 * @since 3.2.1
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="acl" />
	<input type="hidden" name="task" id="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('AKEEBA_ACL_USERNAME')?>
				</th>
				<th>
					<?php echo JText::_('AKEEBA_ACL_USERGROUP')?>
				</th>
				<th width="100">
					<?php echo JText::_('AKEEBA_ACL_PERM_BACKUP')?>
				</th>
				<th width="100">
					<?php echo JText::_('AKEEBA_ACL_PERM_DOWNLOAD')?>
				</th>
				<th width="100">
					<?php echo JText::_('AKEEBA_ACL_PERM_CONFIGURE')?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php $m = 1; ?>
		<?php foreach($this->userlist as $user):?>
		<?php $m = 1 - $m; ?>
			<tr class="row<?php echo $m ?>" id="user<?php echo (int)$user['id']; ?>">
				<td><strong><?php echo $this->escape($user['username']) ?></strong></td>
				<td><?php echo $this->escape($user['usertype']) ?></td>
				<td align="center">
					<a href="index.php?option=com_akeeba&view=acl&task=toggle&axo=backup&id=<?php echo $user['id'] ?>">
					<?php if($user['backup']) :?>
						<img src="images/tick.png" width="16" height="16" border="0" alt="<?php echo JText::_('Yes'); ?>">
					<?php else: ?>
						<img src="images/publish_x.png" width="16" height="16" border="0" alt="<?php echo JText::_('Yes'); ?>">
					<?php endif; ?>
				</td>
				<td align="center">
					<a href="index.php?option=com_akeeba&view=acl&task=toggle&axo=download&id=<?php echo $user['id'] ?>">
					<?php if($user['download']) :?>
						<img src="images/tick.png" width="16" height="16" border="0" alt="<?php echo JText::_('Yes'); ?>">
					<?php else: ?>
						<img src="images/publish_x.png" width="16" height="16" border="0" alt="<?php echo JText::_('Yes'); ?>">
					<?php endif; ?>
				</td>
				<td align="center">
					<a href="index.php?option=com_akeeba&view=acl&task=toggle&axo=configure&id=<?php echo $user['id'] ?>">
					<?php if($user['configure']) :?>
						<img src="images/tick.png" width="16" height="16" border="0" alt="<?php echo JText::_('Yes'); ?>">
					<?php else: ?>
						<img src="images/publish_x.png" width="16" height="16" border="0" alt="<?php echo JText::_('Yes'); ?>">
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</form>