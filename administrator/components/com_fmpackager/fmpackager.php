<?php
/**
 * fmpackager.php
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

// Chargement jQuery
jimport('joomla.version');
$joomlaVersion = new JVersion();
if ($joomlaVersion->getShortVersion() >= 3) {
    JHtml::_('jquery.framework');
} else {
    JFactory::getDocument()->addScript('components/com_fmpackager/assets/js/jquery-1.11.1.min.js');
}

// Exécution tâche courante
$controller = JControllerLegacy::getInstance('fmpackager');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();