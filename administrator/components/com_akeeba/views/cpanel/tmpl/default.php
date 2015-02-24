<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 1.3
 *
 * The main page of the Akeeba Backup component is where all the fun takes place :)
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

AEPlatform::getInstance()->load_version_defines();
$lang =& JFactory::getLanguage();
$icons_root = JURI::base().'components/com_akeeba/assets/images/';

jimport('joomla.html.pane');
JHTML::_('behavior.mootools');
$pane =& JPane::getInstance('Sliders');

$script = <<<ENDSCRIPT
window.addEvent( 'domready' ,  function() {
    $('btnchangelog').addEvent('click', showChangelog);
});

function showChangelog()
{
    SqueezeBox.fromElement(
        $('akeeba-changelog'), {
            handler: 'adopt',
            size: {
                x: 550,
                y: 500
            }
        }
    );
}
ENDSCRIPT;
JFactory::getDocument()->addScriptDeclaration($script,'text/javascript');

?>

<?php if(!$this->schemaok): ?>
<div style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;" id="notfixedperms">
	<h1 style="margin: 1em 0; color: red; font-size: 22pt;"><?php echo JText::_('CPANEL_SCHEMAERROR_TITLE') ?></h1>
	<p><?php echo JText::_('CPANEL_SCHEMAERROR_BODY') ?></p>
</div>
<?php
	return;
	endif;
?>

<div id="akeeba-container" style="width:100%">

<?php if(!$this->fixedpermissions): ?>
<div style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;" id="notfixedperms">
	<h1 style="margin: 1em 0; color: red; font-size: 22pt;"><?php echo JText::_('AKEEBA_CPANEL_WARN_WARNING') ?></h1>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L1') ?></p>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L2') ?></p>
	<ol>
		<li><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L3A') ?></li>
		<li><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L3B') ?></li>
	</ol>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L4') ?></p>
</div>
<?php endif; ?>

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

<div id="cpanel">
	<div class="ak_cpanel_modules" id="ak_cpanel_modules">
	
		<fieldset>
			<legend><?php echo JText::_('CPANEL_LABEL_STATUSSUMMARY')?></legend>
			<div>
				<?php echo $this->statuscell ?>

				<?php $quirks = AEUtilQuirks::get_quirks(); ?>
				<?php if(!empty($quirks)): ?>
				<h4 class="ui-widget-header ui-corner-tl">
					<?php echo JText::_('CPANEL_LABEL_STATUSDETAILS'); ?>
				</h4>
				<div class="ui-widget-content ui-corner-br">
					<?php echo $this->detailscell ?>
				</div>
				<?php endif; ?>

				<?php if(!defined('AKEEBA_PRO')) { $show_donation = 1; } else { $show_donation = (AKEEBA_PRO != 1); } ?>
				<p class="ak_version">
					<?php echo JText::_('AKEEBA').' '.($show_donation?'Core':'Professional ').' '.AKEEBA_VERSION.' ('.AKEEBA_DATE.')' ?>
				</p>
				<!-- CHANGELOG :: BEGIN -->
				<a href="#" id="btnchangelog">CHANGELOG</a>
				<div style="display:none;">
					<div id="akeeba-changelog">
						<?php
						require_once dirname(__FILE__).'/coloriser.php';
						echo AkeebaChangelogColoriser::colorise(JPATH_COMPONENT_ADMINISTRATOR.'/CHANGELOG.php');
						?>
					</div>
				</div>
				<!-- CHANGELOG :: END -->
				<?php if($show_donation): ?>
				<div style="text-align: center;">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="10903325">
						<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online." style="border: none !important;">
						<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
				<?php endif; ?>
			</div>
		</fieldset>

		<fieldset>
			<legend><?php echo JText::_('BACKUP_STATS') ?></legend>
			<div><?php echo $this->statscell ?></div>
		</fieldset>

	</div>

	<div class="ak_cpanel_main_container">
		<fieldset>
			<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-formstyle-reset">
				<input type="hidden" name="option" value="com_akeeba" />
				<input type="hidden" name="view" value="cpanel" />
				<input type="hidden" name="task" value="switchprofile" />
				<?php echo JHTML::_( 'form.token' ); ?>
				<p>
					<?php echo JText::_('CPANEL_PROFILE_TITLE'); ?>: #<?php echo $this->profileid; ?>
					<?php echo JHTML::_('select.genericlist', $this->profilelist, 'profileid', 'onchange="document.forms.adminForm.submit()"', 'value', 'text', $this->profileid); ?>
					<button onclick="this.form.submit(); return false;"><?php echo JText::_('CPANEL_PROFILE_BUTTON'); ?></button>
				</p>
			</form>
		</fieldset>
	
		<fieldset>
			<legend><?php echo JText::_('CPANEL_HEADER_BASICOPS'); ?></legend>
			<?php foreach($this->icondefs['operations'] as $icon): ?>
			<div class="icon">
				<a href="<?php echo 'index.php?option=com_akeeba'.
					(is_null($icon['view']) ? '' : '&amp;view='.$icon['view']).
					(is_null($icon['task']) ? '' : '&amp;task='.$icon['task']); ?>">
				<div class="ak-icon ak-icon-<?php echo $icon['icon'] ?>">&nbsp;</div>
				<span><?php echo $icon['label']; ?></span>
				</a>
			</div>
			<?php endforeach; ?>
			
			<?php if(AKEEBA_JVERSION == '15'): ?>
			<div class="icon">
				<a href="index.php?option=com_akeeba&view=acl">
					<div class="ak-icon ak-icon-acl">&nbsp;</div>
					<span><?php echo JText::_('AKEEBA_ACL_TITLE'); ?></span>
				</a>
			</div>
			<?php endif; ?>
			
			<div class="icon">
				<?php if(AKEEBA_JVERSION == '15'): ?>
				<a href="index.php?option=com_config&controller=component&component=com_akeeba&path="
				<?php else: ?>
				<a href="index.php?option=com_config&view=component&component=com_akeeba&path=&tmpl=component"
				<?php endif; ?>
					class="modal"
					rel="{handler: 'iframe', size: {x: 660, y: 500}}">
					<div class="ak-icon ak-icon-componentparams">&nbsp;</div>
					<span><?php echo JText::_('CPANEL_LABEL_COMPONENTCONFIG'); ?></span>
				</a>
			</div>

			<?php echo LiveUpdate::getIcon(); ?>			
		</fieldset>

		<?php if(!empty($this->icondefs['inclusion'])): ?>
		<fieldset>
			<legend><?php echo JText::_('CPANEL_HEADER_INCLUSION'); ?></legend>
			<?php foreach($this->icondefs['inclusion'] as $icon): ?>
			<div class="icon">
				<a href="<?php echo 'index.php?option=com_akeeba'.
					(is_null($icon['view']) ? '' : '&amp;view='.$icon['view']).
					(is_null($icon['task']) ? '' : '&amp;task='.$icon['task']); ?>">
				<div class="ak-icon ak-icon-<?php echo $icon['icon'] ?>">&nbsp;</div>
				<span><?php echo $icon['label']; ?></span>
				</a>
			</div>
			<?php endforeach; ?>
		</fieldset>
		<?php endif; ?>

		<fieldset>
			<legend><?php echo JText::_('CPANEL_HEADER_EXCLUSION'); ?></legend>
			<?php foreach($this->icondefs['exclusion'] as $icon): ?>
			<div class="icon">
				<a href="<?php echo 'index.php?option=com_akeeba'.
					(is_null($icon['view']) ? '' : '&amp;view='.$icon['view']).
					(is_null($icon['task']) ? '' : '&amp;task='.$icon['task']); ?>">
				<div class="ak-icon ak-icon-<?php echo $icon['icon'] ?>">&nbsp;</div>
				<span><?php echo $icon['label']; ?></span>
				</a>
			</div>
			<?php endforeach; ?>
		</fieldset>

	</div>
</div>

<div class="ak_clr"></div>

<p style="height: 6em">
	<?php echo JText::sprintf('COPYRIGHT', date('Y')); ?><br/>
	<?php echo JText::_('LICENSE'); ?>
	<?php if(AKEEBA_PRO != 1): ?>
	<br/>If you use Akeeba Backup Core, please post a rating and a review at the <a href="http://extensions.joomla.org/extensions/access-a-security/site-security/backup/1606">Joomla! Extensions Directory</a>.
	<?php endif; ?>	
	<br/><br/>
	<strong><?php echo JText::_('TRANSLATION_CREDITS')?></strong>: 
	<em><?php echo JText::_('TRANSLATION_LANGUAGE') ?></em> &bull;
	<a href="<?php echo JText::_('TRANSLATION_AUTHOR_URL') ?>"><?php echo JText::_('TRANSLATION_AUTHOR') ?></a>
</p>

</div>