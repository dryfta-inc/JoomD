<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: wizard.php 698 2011-06-03 22:33:44Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

?>

<div id="akeeba-confwiz" style="width: 100%">

<!-- jQuery & jQuery UI detection. Also shows a big, fat warning if they're missing -->
<div id="nojquerywarning" style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;">
	<h1 style="margin: 1em 0; color: red; font-size: 22pt;"><?php echo JText::_('AKEEBA_CPANEL_WARN_ERROR') ?></h1>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_JQ_L1B'); ?></p>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_JQ_L2'); ?></p>
</div>
<script type="text/javascript">
	if(typeof akeeba.jQuery == 'function')
	{
		if(typeof akeeba.jQuery.ui == 'object')
		{
			akeeba.jQuery('#nojquerywarning').css('display','none');
			akeeba.jQuery('#notfixedperms').css('display','none');
		}
	}
</script>

<div id="backup-progress-pane" class="ui-widget" style="display: none">
	<div class="ui-state-highlight" style="padding: 0.3em; margin: 0.3em 0.2em; font-weight: bold;">
			<span class="ui-icon ui-icon-notice" style="float: left;"></span>
			<?php echo JText::_('AKEEBA_WIZARD_INTROTEXT'); ?>
	</div>
	
	<fieldset id="backup-progress-header">
		<legend><?php echo JText::_('AKEEEBA_WIZARD_PROGRESS') ?></legend>
		<div id="backup-progress-content">
			<div id="backup-steps" class="ui-corner-all">
				<div id="step-ajax" class="step-pending"><?php echo JText::_('AKEEBA_CONFWIZ_AJAX'); ?></div>
				<div id="step-minexec" class="step-pending"><?php echo JText::_('AKEEBA_CONFWIZ_MINEXEC'); ?></div>
				<div id="step-directory" class="step-pending"><?php echo JText::_('AKEEBA_CONFWIZ_DIRECTORY'); ?></div>
				<div id="step-dbopt" class="step-pending"><?php echo JText::_('AKEEBA_CONFWIZ_DBOPT'); ?></div>
				<div id="step-maxexec" class="step-pending"><?php echo JText::_('AKEEBA_CONFWIZ_MAXEXEC'); ?></div>
				<div id="step-splitsize" class="step-pending"><?php echo JText::_('AKEEBA_CONFWIZ_SPLITSIZE'); ?></div>
			</div>
			<div id="backup-status" class="ui-corner-all">
				<div id="backup-substep"></div>
			</div>
			<div id="response-timer" class="ui-corner-all">
				<div class="color-overlay" style="display: none"></div>
				<div class="text"></div>
			</div>
		</div>
		<span id="ajax-worker"></span>
	</fieldset>
	
</div>

<div id="error-panel" style="display:none">
	<fieldset>
		<legend><?php echo JText::_('AKEEBA_WIZARD_HEADER_FAILED'); ?></legend>
		<div id="errorframe">
			<p id="backup-error-message">
			TEST ERROR MESSAGE
			</p>
		</div>
	</fieldset>
</div>

<div id="backup-complete" style="display: none">
	<fieldset>
		<legend><?php echo JText::_('AKEEBA_WIZARD_HEADER_FINISHED'); ?></legend>
		<div id="finishedframe">
			<div style="min-height: 32px">
				<div class="ak-icon ak-icon-ok" style="float: left; margin: 0 1em 0 0 !important;"></div>
				<p>
					<?php echo JText::_('AKEEBA_WIZARD_CONGRATS') ?>
				</p>
			</div>
	
			<div class="ak-action-button">
				<div class="ak-icon ak-icon-backup"></div>
				<button onclick="window.location='<?php echo JURI::base() ?>index.php?option=com_akeeba&view=backup'; return false;"><?php echo JText::_('BACKUP'); ?></button>
			</div>
			<div class="ak-action-button">
				<div class="ak-icon ak-icon-configuration"></div>
				<button onclick="window.location='<?php echo JURI::base() ?>index.php?option=com_akeeba&view=config'; return false;"><?php echo JText::_('CONFIGURATION'); ?></button>
			</div>
		</div>
	</fieldset>
</div>

</div>

<script type="text/javascript">
akeeba_ajax_url = 'index.php?option=com_akeeba&view=confwiz&task=ajax';
<?php
	$keys = array('tryajax','tryiframe','cantuseajax','minexectry','cantsaveminexec','saveminexec','cantdetermineminexec',
		'cantfixdirectories','cantdbopt','exectoolow','savingmaxexec','cantsavemaxexec','cantdeterminepartsize','partsize');
	foreach($keys as $key):
?>
akeeba_translations['UI-<?php echo strtoupper($key)?>']="<?php echo JText::_('AKEEBA_WIZARD_UI_'.strtoupper($key)) ?>";
<?php endforeach; ?>
akeeba_confwiz_boot();
</script>