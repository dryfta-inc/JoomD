<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: default.php 698 2011-06-03 22:33:44Z nikosdion $
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo JText::_('CONFIG_UI_BROWSER_TITLE'); ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<link rel="stylesheet" type="text/css"
	href="<?php echo JURI::base(); ?>../media/com_akeeba/theme/browser.css?<?php echo AKEEBAMEDIATAG?>" />
	<script type="text/javascript">
		function akeeba_browser_useThis()
		{
			var rawFolder = document.forms.adminForm.folderraw.value;
			if( rawFolder == '[SITEROOT]' )
			{
				alert('<?php echo AkeebaHelperEscape::escapeJS(JText::_('CONFIG_UI_ROOTDIR')); ?>');
				rawFolder = '[SITETMP]';
			}
			window.parent.akeeba_browser_callback( rawFolder );
		}
	</script>
</head>
<body>

<?php if(empty($this->folder)): ?>
<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="browser" />
	<input type="hidden" name="format" value="html" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="folder" id="folder" value="" />
	<input type="hidden" name="processfolder" id="processfolder" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
	<?php else: ?>
<div id="controls">
<?php
$image = JURI::base().'../media/com_akeeba/icons/';
$image .= $this->writable ? 'ok_small.png' : 'error_small.png';
?>
<img src="<?php echo $image; ?>"
	style="float: right; position: relative; right: 3px; top: 6px;"
	alt="<?php echo $this->writable ? JText::_('WRITABLE') : JText::_('UNWRITABLE'); ?>"
	title="<?php echo $this->writable ? JText::_('WRITABLE') : JText::_('UNWRITABLE'); ?>" />
	<form action="index.php" method="post" name="adminForm">
		<input type="hidden" name="option" value="com_akeeba" />
		<input type="hidden" name="view" value="browser" />
		<input type="hidden" name="format" value="html" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="text" name="folder" id="folder" value="<?php echo $this->folder; ?>" />
		<input type="hidden" name="folderraw" id="folderraw" value="<?php echo $this->folder_raw ?>"/>
		<button onclick="document.form.adminForm.submit(); return false;"><?php echo JText::_('BROWSER_LBL_GO'); ?></button>
		<button onclick="akeeba_browser_useThis(); return false;"><?php echo JText::_('BROWSER_LBL_USE'); ?></button>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
<div id="akeeba-browser-clear"></div>

<div id="breadcrumbs">
<?php if(count($this->breadcrumbs) > 0): ?>
	<?php $i = 0 ?>
	<?php foreach($this->breadcrumbs as $crumb):
		$link = JURI::base()."index.php?option=com_akeeba&view=browser&folder=".urlencode($crumb['folder']);
		$label = htmlentities($crumb['label']);
		$i++;
		$bull = $i < count($this->breadcrumbs) ? '&bull;' : '';
	?>
	<a href="<?php echo $link ?>"><?php echo $label ?></a><?php echo $bull ?>
	<?php endforeach; ?>
<?php endif; ?>
</div>

<div id="browser">
<?php if(count($this->subfolders) > 0): ?>
	<?php $linkbase = JURI::base()."index.php?option=com_akeeba&view=browser&folder="; ?>
	<a href="<?php echo $linkbase.urlencode($this->parent); ?>"><?php echo JText::_('BROWSER_LBL_GOPARENT') ?></a>
	<?php foreach($this->subfolders as $subfolder): ?>
	<a href="<?php echo $linkbase.urlencode($this->folder.'/'.$subfolder); ?>"><?php echo htmlentities($subfolder) ?></a>
	<?php endforeach; ?>
<?php else: ?>

<?php
if(!$this->exists) {
	echo JText::_('BROWSER_ERR_NOTEXISTS');
} else if(!$this->inRoot) {
	echo JText::_('BROWSER_ERR_NONROOT');
} else if($this->openbasedirRestricted) {
	echo JText::_('BROWSER_ERR_BASEDIR');
} else {
?>
	<?php $linkbase = JURI::base()."index.php?option=com_akeeba&view=browser&folder="; ?>
	<a href="<?php echo $linkbase.urlencode($this->parent); ?>"><?php echo JText::_('BROWSER_LBL_GOPARENT') ?></a>
	<?php
}
?>
<?php endif; ?>
</div>

<?php endif; ?>
</body>
</html>