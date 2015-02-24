<?php
/**
 * view.html.php
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

jimport('joomla.application.component.view');

/**
 * Composant FMPackager - View FMPackager
 *
 * @category Joomla
 * @package  Joomla.Administrator
 * @author   Folcomedia <contact@folcomedia.fr>
 * @license  GNU General Public License version 2 or later; see LICENSE.txt
 * @link     https://www.folcomedia.fr
 */
class FMPackagerViewFMPackager extends JViewLegacy
{

    /**
     * Affichage de la vue
     *
     * @return void
     */
    public function display($tpl = NULL)
    {

        // Paramètres page
        $this->option = JRequest::getString('option');
        $this->view = JRequest::getString('view', 'fmpackager');

        // Données de la page
        $this->extensions = $this->getModel()->getExtensions();

        // Affichage
        $this->toolbar();
        parent::display();

    }

    /**
     * Affichage de la barre d'outil
     *
     * @return void
     */
    private function toolbar()
    {

        $joomlaVersion = new JVersion();
        JToolBarHelper::title(JText::_('COM_FMPACKAGER'), $joomlaVersion->getShortVersion() >= 3 ? 'briefcase' : 'install');

    }

}