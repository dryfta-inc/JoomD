<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 1.3
 */

defined('_JEXEC') or die('Restricted access');

?>
<div id="akeeba-container" style="width:100%">

<div id="dialog" title="<?php echo JText::_('DBFILTER_ERROR_TITLE') ?>">
</div>

<fieldset>
	<legend><?php echo JText::_('CPANEL_PROFILE_TITLE'); ?>: #<?php echo $this->profileid; ?></legend>
	<?php echo $this->profilename; ?>
</fieldset>

<fieldset>
		<legend><?php echo JText::_('DBFILTER_LABEL_ROOTDIR') ?></legend>
		<?php echo $this->root_select; ?>

		&nbsp;&bull;&nbsp;
		
		<button onclick="dbfilter_exclude_noncms(); return false;"><?php echo JText::_('DBFILTER_LABEL_EXCLUDENONCORE'); ?></button>
		<button onclick="dbfilter_nuke(); return false;"><?php echo JText::_('DBFILTER_LABEL_NUKEFILTERS'); ?></button>
		
</fieldset>

<fieldset>
	<legend><?php echo JText::_('DBFILTER_LABEL_TABLES'); ?></legend>
	<div id="tables"></div>
</fieldset>

<script type="text/javascript">
/**
 * Callback function for changing the active root in Database Table filters
 */
function akeeba_active_root_changed()
{
	(function($){
		var data = new Object;
		data.root = $('#active_root').val();
		dbfilter_load(data);
	})(akeeba.jQuery);
}

akeeba.jQuery(document).ready(function($){
	// Set the AJAX proxy URL
	akeeba_ajax_url = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=dbef&task=ajax') ?>';
	// Set the media root
	akeeba_ui_theme_root = '<?php echo $this->mediadir ?>';
	// Create the dialog
	$("#dialog").dialog({
		autoOpen: false,
		closeOnEscape: false,
		height: 200,
		width: 300,
		hide: 'slide',
		modal: true,
		position: 'center',
		show: 'slide'
	});
	// Create an AJAX error trap
	akeeba_error_callback = function( message ) {
		var dialog_element = $("#dialog");
		dialog_element.html(''); // Clear the dialog's contents
		dialog_element.dialog('option', 'title', '<?php echo AkeebaHelperEscape::escapeJS(JText::_('CONFIG_UI_AJAXERRORDLG_TITLE')) ?>');
		$(document.createElement('p')).html('<?php echo AkeebaHelperEscape::escapeJS(JText::_('CONFIG_UI_AJAXERRORDLG_TEXT')) ?>').appendTo(dialog_element);
		$(document.createElement('pre')).html( message ).appendTo(dialog_element);
		dialog_element.dialog('open');
	};
	// Push translations
	akeeba_translations['UI-ROOT'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('FILTERS_LABEL_UIROOT')) ?>';
	akeeba_translations['UI-ERROR-FILTER'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('FILTERS_LABEL_UIERRORFILTER')) ?>';
	<?php
			$filters = array('tables', 'tabledata');
			foreach($filters as $type)
			{
				echo "\takeeba_translations['UI-FILTERTYPE-".strtoupper($type)."'] = '".
					AkeebaHelperEscape::escapeJS(JText::_('DBFILTER_TYPE_'.strtoupper($type))).
					"';\n";
			}

			$table_types = array('misc', 'table', 'view', 'procedure', 'function', 'trigger');
			foreach($table_types as $type)
			{
				echo "\takeeba_translations['UI-TABLETYPE-".strtoupper($type)."'] = '".
					AkeebaHelperEscape::escapeJS(JText::_('DBFILTER_TABLE_'.strtoupper($type))).
					"';\n";
			}
	?>

	// Bootstrap the page display
	var data = JSON.parse('<?php echo AkeebaHelperEscape::escapeJS($this->json,"'"); ?>');
	dbfilter_render(data);
});
</script>

</div>