<?php
/**
 * default.php
 *
 * php version 5
 *
 * @category  Joomla
 * @package   Joomla.Administrator
 * @author    Folcomedia <contact@folcomedia.fr>
 * @copyright 2014 Folcomedia
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 * @link      https://www.folcomedia.fr
 */
defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&view='.$this->view) ?>" method="post" name="adminForm" id="adminForm" target="_blank">
    <div style="text-align:center">
        <div style="max-width:550px; margin:10px auto">
            <?php echo JText::_('COM_FMPACKAGER_EXPLICATIONS') ?>
        </div>
        <br />
        <div id="extensionTypeContainer">
            <select name="extensionType" id="extensionType">
                <option value="">- <?php echo JText::_('COM_FMPACKAGER_EXTENSION_TYPE') ?> -</option>
                <?php foreach (array('com', 'mod', 'mod_admin', 'plg', 'tpl', 'tpl_admin') as $type) : ?>
                    <option value="<?php echo $type ?>"><?php echo JText::_('COM_FMPACKAGER_TYPE_'.$type) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div id="extensionGroupContainer">
            <select name="extensionGroup" id="extensionGroup">
                <option value="">- <?php echo JText::_('COM_FMPACKAGER_EXTENSION_GROUP') ?> -</option>
                <?php foreach ($this->extensions['plugins'] as $item) : ?>
                    <option value="<?php echo $item['group'] ?>"><?php echo $item['group'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div id="extensionNameContainer">
            <select name="extensionName" id="extensionName">
                <option value="">- <?php echo JText::_('COM_FMPACKAGER_EXTENSION_NAME') ?> -</option>
                <?php foreach ($this->extensions['composants'] as $item) : ?>
                    <option data-type="com" value="<?php echo $item ?>"><?php echo JText::_($item).' ('.$item.')' ?></option>
                <?php endforeach ?>
                <?php foreach ($this->extensions['modules'] as $item) : ?>
                    <option data-type="mod" value="<?php echo $item ?>"><?php echo JText::_($item).' ('.$item.')' ?></option>
                <?php endforeach ?>
                <?php foreach ($this->extensions['modulesAdmin'] as $item) : ?>
                    <option data-type="mod_admin" value="<?php echo $item ?>"><?php echo JText::_($item).' ('.$item.')' ?></option>
                <?php endforeach ?>
                <?php foreach ($this->extensions['plugins'] as $item) : ?>
                    <?php foreach ($item['items'] as $item2) : ?>
                        <option data-type="plg" data-group="<?php echo $item['group'] ?>" value="plg_<?php echo $item2 ?>"><?php echo JText::_('plg_'.$item['group'].'_'.$item2).' ('.$item2.')' ?></option>
                    <?php endforeach ?>
                <?php endforeach ?>
                <?php foreach ($this->extensions['templates'] as $item) : ?>
                    <option data-type="tpl" value="tpl_<?php echo $item ?>"><?php echo JText::_($item).' (tpl_'.$item.')' ?></option>
                <?php endforeach ?>
                <?php foreach ($this->extensions['templatesAdmin'] as $item) : ?>
                    <option data-type="tpl_admin" value="tpl_<?php echo $item ?>"><?php echo JText::_($item).' (tpl_'.$item.')' ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <br />
        <div>
            <label class="checkbox inline">
                <input name="ignore_errors" type="checkbox" value="1" />
                <?php echo JText::_('COM_FMPACKAGER_IGNORE_ERRORS') ?>
            </label><br />
            <?php echo JText::_('COM_FMPACKAGER_IGNORE_ERRORS2') ?>
        </div>
        <br />
        <div>
            <button type="submit" class="btn"><?php echo JText::_('COM_FMPACKAGER_DOWNLOAD_ZIP') ?></button>
        </div>
    </div>
    <input type="hidden" name="task" value="buildZip" />
    <?php echo JHTML::_('form.token') ?>
</form>

<script type="text/javascript">
    var FMPackagerClone = null;
    jQuery(document).ready(function($) {
        /////// Clone de la liste
        FMPackagerClone = $('#extensionName').clone().removeAttr('id').removeAttr('name');
        /////// Modification des propositions lors des choix dans les listes
        $('#extensionGroupContainer, #extensionNameContainer').hide();
        $('#extensionType, #extensionGroup').change(function() {
            var type = $('#extensionType').val();
            var group = $('#extensionGroup').val();
            // Affiche les listes déroulantes qu'il faut
            if (type == '') {
                $('#extensionNameContainer').slideUp();
                $('#extensionGroupContainer').slideUp();
                $('#extensionGroup').val('');
            } else if(type == 'plg') {
                $('#extensionGroupContainer').slideDown();
                if (group == '') {
                    $('#extensionNameContainer').slideUp();
                } else {
                    $('#extensionNameContainer').slideDown();
                }
            } else {
                $('#extensionGroupContainer').slideUp();
                $('#extensionNameContainer').slideDown();
            }
            // Affiche les options qu'il faut
            $('#extensionName option').remove();
            $('#extensionName').append(FMPackagerClone.find('option').clone());
            $.each($('#extensionName').find('option'), function(idx, opt) {
                if ($(opt).data('type') != undefined && $(opt).data('type') != type) {
                    $(opt).remove();
                } else if (type == 'plg' && group != '' && $(opt).data('group') != undefined && $(opt).data('group') != group) {
                    $(opt).remove();
                }
            });
        }).trigger('change');
    });
</script>
