<?php
/**
 * controller.php
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

/**
 * Controleur principal
 *
 * @category Joomla
 * @package  Joomla.Administrator
 * @author   Folcomedia <contact@folcomedia.fr>
 * @license  GNU General Public License version 2 or later; see LICENSE.txt
 * @link     https://www.folcomedia.fr
 */
class FMPackagerController extends JControllerLegacy
{

    /**
     * Création du fichier ZIP
     *
     * @return void
     */
    function buildZip()
    {

        $joomlaZip = $this->getModel();
        $input = JFactory::getApplication()->input;

        $joomlaZip->setExtension($input->get('extensionName'), $input->get('extensionGroup'));
        $joomlaZip->setErrorsSupported((boolean) $input->get('ignore_errors'));
        $joomlaZip->setAdminExtension(strpos($input->get('extensionType'), '_admin') !== FALSE);
        $joomlaZip->build();
        $joomlaZip->downloadFile();

    }

}