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

/**
 * Composant FMPackager - Model FMPackager
 *
 * @category Joomla
 * @package  Joomla.Administrator
 * @author   Folcomedia <contact@folcomedia.fr>
 * @license  GNU General Public License version 2 or later; see LICENSE.txt
 * @link     https://www.folcomedia.fr
 */
class FMPackagerModelFMPackager extends JModelLegacy
{

    private $extensionName = '';
    private $extensionPrefix = '';
    private $extensionSimpleName = '';
    private $extensionType = '';
    private $extensionVersion = '';
    private $extensionGroup = '';

    private $zip = null;
    private $tmpZipFile = '';
    private $tmpEmptyFile = '';

    private $adminExtension = false;
    private $errorsSupported = false;

    private $xmlManifestFile = '';

    /**
     * Récupère la liste des extensions disponibles
     *
     * @return array
     */
    public function getExtensions()
    {

        $lang = JFactory::getLanguage();

        $composants = array();
        foreach (glob(JPATH_ADMINISTRATOR.'/components/*') as $item) {
            if (substr(basename($item), 0, 4) == 'com_') {
                $lang->load(basename($item), JPATH_ADMINISTRATOR);
                array_push($composants, basename($item));
            }
        }

        $modules = array();
        foreach (glob(JPATH_SITE.'/modules/*') as $item) {
            if (substr(basename($item), 0, 4) == 'mod_') {
                $lang->load(basename($item), JPATH_SITE);
                array_push($modules, basename($item));
            }
        }

        $modulesAdmin = array();
        foreach (glob(JPATH_ADMINISTRATOR.'/modules/*') as $item) {
            if (substr(basename($item), 0, 4) == 'mod_') {
                $lang->load(basename($item), JPATH_ADMINISTRATOR);
                array_push($modulesAdmin, basename($item));
            }
        }

        $plugins = array();
        foreach (glob(JPATH_SITE.'/plugins/*') as $item) {
            $group = basename($item);
            if ($group != 'index.html') {
                $items = array();
                foreach (glob($item.'/*') as $item2) {
                    if (basename($item2) != 'index.html') {
                        $lang->load('plg_'.$group.'_'.basename($item2), JPATH_ADMINISTRATOR);
                        array_push($items, basename($item2));
                    }
                }
                array_push($plugins, array('group' => $group, 'items' => $items));
            }
        }

        $templates = array();
        foreach (glob(JPATH_SITE.'/templates/*') as $item) {
            if (basename($item) != 'index.html') {
                $lang->load('tpl_'.basename($item), JPATH_SITE);
                array_push($templates, basename($item));
            }
        }

        $templatesAdmin = array();
        foreach (glob(JPATH_ADMINISTRATOR.'/templates/*') as $item) {
            if (basename($item) != 'index.html') {
                $lang->load('tpl_'.basename($item), JPATH_ADMINISTRATOR);
                array_push($templatesAdmin, basename($item));
            }
        }

        return array(
            'composants' => $composants,
            'modules' => $modules,
            'modulesAdmin' => $modulesAdmin,
            'plugins' => $plugins,
            'templates' => $templates,
            'templatesAdmin' => $templatesAdmin
        );

    }

    /**
     * Déclenche l'affichage d'une erreur
     *
     * @param string $smg - Message à afficher
     *
     * @return void
     */
    private function raiseError($msg)
    {

        exit("FMPackager Error: ".$msg);

    }

    /**
     * Indiquer le nom de l'extension sur laquelle on travaille
     *
     * @param string $extensionName - Le nom de l'extension (ex : 'com_content' ou 'mod_banners')
     * @param string $group         - Groupe (utile pour les plugins)
     *
     * @return void
     */
    public function setExtension($extensionName, $group = '')
    {

        $this->extensionName = $extensionName;
        $this->extensionPrefix = substr($extensionName, 0, 4);

        if (strlen($this->extensionName) < 5 || !in_array($this->extensionPrefix, array('com_', 'mod_', 'plg_', 'tpl_'))) {
            $this->raiseError(JText::_('COM_FMPACKAGER_ERROR_INVALID_EXTENSION_NAME'));
        }

        $this->extensionGroup = $this->extensionPrefix == 'plg_' ? $group : '';

        $this->extensionSimpleName = substr($extensionName, 4);

    }

    /**
     * Active / désactive les erreurs que l'on peut corriger
     *
     * @param boolean $bool - Ignorer les erreurs oui / non
     */
    public function setErrorsSupported($bool)
    {

        $this->errorsSupported = $bool;

    }

    /**
     * Permet d'indiquer que l'extension se trouve en admin (utilisé pour les modules et templates)
     *
     * @param boolean $admin - Il s'agit d'un module de l'administration
     *
     * @return void
     */
    public function setAdminExtension($admin)
    {

        $this->adminExtension = $admin;

    }

    /**
     * Crée une instance de ZIP et lance la compilation du zip selon le type d'extension
     *
     * @return boolean - Compilation OK
     */
    public function build()
    {

        libxml_use_internal_errors(true);

        $this->tmpZipFile = tempnam(sys_get_temp_dir(), 'ZIP');
        $this->tmpEmptyFile = tempnam(sys_get_temp_dir(), 'TMP');

        $this->zip = new ZipArchive();
        $this->zip->open($this->tmpZipFile);

        switch ($this->extensionPrefix) {
            case 'com_' :
                $this->buildComponent();
                break;
            case 'mod_' :
                $this->buildModule();
                break;
            case 'tpl_' :
                $this->buildTemplate();
                break;
            case 'plg_' :
                $this->buildPlugin();
                break;
            default :
                $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_EXTENSION_TYPE_NOT_SUPPORTED', $this->extensionPrefix));
                break;
        }

        return $this->zip->close();

    }

    /**
     * Propose le zip en téléchargement
     *
     * @return void
     */
    public function downloadFile()
    {

        $filename = basename($this->extensionName);
        if (!empty($this->extensionGroup)) {
            // Cas des plugins
            $filename = substr($filename, 0, 4).$this->extensionGroup.'_'.substr($filename, 4);
        }
        if (!empty($this->extensionVersion)) {
            // On a la version
            $filename .= '-'.$this->extensionVersion;
        }
        $filename .= '.zip';

        $tampon = ob_get_clean();
        if (!empty($tampon) && !$this->errorsSupported) {
            // Erreur innatendue
            $this->raiseError($tampon);
        } else {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"".$filename."\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".filesize($this->tmpZipFile));
            readfile($this->tmpZipFile);
            exit();
        }

    }

    /**
     * Compile un composant
     *
     * @return boolean - Compilation OK
     */
    private function buildComponent()
    {

        // Test si le composant existe et qu'il y a un fichier XML
        $adminPath = 'administrator/components/'.$this->extensionName;
        if (!is_dir(JPATH_ROOT.'/'.$adminPath)) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_FOLDER_NOT_FOUND', $adminPath));
        }
        $publicPath = 'components/'.$this->extensionName;

        $xmlManifestFile = $adminPath.'/'.$this->extensionSimpleName.'.xml';
        $xmlManifestFileName = $this->extensionSimpleName;
        if (is_file(JPATH_ROOT.'/'.$xmlManifestFile)) {
            // Fichier manifest standard OK
        } elseif (is_file(JPATH_ROOT.'/'.$adminPath.'/manifest.xml')) {
            $xmlManifestFile = $adminPath.'/manifest.xml';
            $xmlManifestFileName = 'manifest';
        } else {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_NOT_FOUND', $xmlManifestFile));
        }

        // Analyse du XML pour savoir comment construire le ZIP
        $xml = trim(file_get_contents(JPATH_ROOT.'/'.$xmlManifestFile));
        try {
            $data = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_CORRUPTED', $xmlManifestFile));
        }

        // Récupération de la version
        if (!empty($data->version)) {
            $this->extensionVersion = (string) $data->version;
        }

        // Ajout du manifest XML
        $this->zip->addFile(JPATH_ROOT.'/'.$xmlManifestFile, $xmlManifestFileName.'.xml');

        // Fichier scriptfile
        if (!empty($data->scriptfile)) {
            $this->zip->addFile(JPATH_SITE.'/'.$adminPath.'/'.$data->scriptfile, $data->scriptfile);
        }

        // Ajout des fichiers partie publique
        if (!empty($data->files)) {
            $siteFolder = empty($data->files['folder']) ? '' : $data->files['folder'].'/';
            foreach ($data->files->filename as $file) {
                $fullPath = JPATH_ROOT.'/'.$publicPath.'/'.$file;
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $siteFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $siteFolder.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $publicPath.'/'.$file));
                }
            }
            foreach ($data->files->folder as $folder) {
                $fullPath = JPATH_ROOT.'/'.$publicPath.'/'.$folder;
                if (is_dir($fullPath)) {
                    $this->addFolderToZip($fullPath, $this->zip, $siteFolder.$folder);
                } elseif ($this->errorsSupported) {
                    $this->zip->addEmptyDir($siteFolder.$folder);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FOLDER_NOT_FOUND', $publicPath.'/'.$folder));
                }
            }
        }

        // Ajout des fichiers langue partie publique
        if (!empty($data->languages)) {
            $languageFolder = empty($data->languages['folder']) ? '' : $data->languages['folder'].'/';
            foreach ($data->languages->language as $file) {
                $fullPath = JPATH_ROOT.'/language'.'/'.$file['tag'].'/'.basename($file);
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $languageFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $languageFolder.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $file));
                }
            }
        }

        // Ajout des fichiers partie admin
        if (!empty($data->administration->files)) {
            $adminFolder = empty($data->administration->files['folder']) ? '' : $data->administration->files['folder'].'/';
            foreach ($data->administration->files->filename as $file) {
                $fullPath = JPATH_ROOT.'/'.$adminPath.'/'.$file;
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $adminFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $adminFolder.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $adminPath.'/'.$file));
                }
            }
            foreach ($data->administration->files->folder as $folder) {
                $fullPath = JPATH_ROOT.'/'.$adminPath.'/'.$folder;
                if (is_dir($fullPath)) {
                    $this->addFolderToZip($fullPath, $this->zip, $adminFolder.$folder);
                } elseif ($this->errorsSupported) {
                    $this->zip->addEmptyDir($adminFolder.$folder);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FOLDER_NOT_FOUND', $adminPath.'/'.$folder));
                }
            }
            if (is_dir($adminPath.'/sql')) {
                $this->addFolderToZip($adminPath.'/sql', $this->zip, $adminFolder.'sql');
            }
        }

        // Ajout des fichiers langue partie admin
        if (!empty($data->administration->languages)) {
            $languageAdminFolder = empty($data->administration->languages['folder']) ? '' : $data->administration->languages['folder'].'/';
            foreach ($data->administration->languages->language as $file) {
                $fullPath = JPATH_ROOT.'/administrator/language'.'/'.$file['tag'].'/'.basename($file);
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $languageAdminFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $languageAdminFolder.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', 'administrator/'.$file));
                }
            }
        }

        // Ajout des fichiers / dossiers media
        if (!empty($data->media)) {
            $mediaFolder = empty($data->media['folder']) ? '' : $data->media['folder'].'/';
            $destination = empty($data->media['destination']) ? '' : $data->media['destination'].'/';
            foreach ($data->media->folder as $folder) {
                $fullPath = JPATH_ROOT.'/media/'.$destination.$folder;
                if (is_dir($fullPath)) {
                    $this->addFolderToZip($fullPath, $this->zip, $mediaFolder.$folder);
                } elseif ($this->errorsSupported) {
                    $this->zip->addEmptyDir($mediaFolder.$folder);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FOLDER_NOT_FOUND', 'media/'.$folder));
                }
            }
            foreach ($data->media->filename as $file) {
                $fullPath = JPATH_ROOT.'/media/'.$destination.$file;
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $mediaFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $mediaFolder.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', 'media/'.$file));
                }
            }
        }

        return true;

    }

    /**
     * Compile un module
     *
     * @return boolean - Compilation OK
     */
    private function buildModule()
    {

        // Test si le module existe et qu'il y a un fichier XML
        $path = ($this->adminExtension ? 'administrator/' : '').'modules/'.$this->extensionName;
        if (!is_dir(JPATH_ROOT.'/'.$path)) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_FOLDER_NOT_FOUND', $path));
        }

        $xmlManifestFile = $path.'/'.$this->extensionName.'.xml';
        if (!is_file(JPATH_ROOT.'/'.$xmlManifestFile)) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_NOT_FOUND', $xmlManifestFile));
        }

        // Analyse du XML pour savoir comment construire le ZIP
        $xml = trim(file_get_contents(JPATH_ROOT.'/'.$xmlManifestFile));
        try {
            $data = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_CORRUPTED', $xmlManifestFile));
        }

        // Récupération de la version
        if (!empty($data->version)) {
            $this->extensionVersion = (string) $data->version;
        }

        // Ajout du manifest XML
        $this->zip->addFile(JPATH_ROOT.'/'.$xmlManifestFile, $this->extensionName.'.xml');

        // Fichier scriptfile
        if (!empty($data->scriptfile)) {
            $this->zip->addFile(JPATH_SITE.'/'.$path.'/'.$data->scriptfile, $data->scriptfile);
        }

        // Ajout des fichiers partie publique
        if (!empty($data->files)) {
            foreach ($data->files->filename as $file) {
                $fullPath = JPATH_ROOT.'/'.$path.'/'.$file;
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $path.'/'.$file));
                }
            }
            foreach ($data->files->folder as $folder) {
                $fullPath = JPATH_ROOT.'/'.$path.'/'.$folder;
                if (is_dir($fullPath)) {
                    $this->addFolderToZip($fullPath, $this->zip, $folder);
                } elseif ($this->errorsSupported) {
                    $this->zip->addEmptyDir($folder);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FOLDER_NOT_FOUND', $path.'/'.$folder));
                }
            }
        }

        // Ajout des fichiers langue partie publique
        if (!empty($data->languages)) {
            $languageFolder = empty($data->languages['folder']) ? '' : $data->languages['folder'].'/';
            foreach ($data->languages->language as $file) {
                $fullPath = JPATH_ROOT.'/language'.'/'.$file['tag'].'/'.basename($file);
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $languageFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $languageFolder.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $file));
                }
            }
        }

        return true;

    }

    /**
     * Compile un template
     *
     * @return boolean - Compilation OK
     */
    private function buildTemplate()
    {

        // Test si le template existe et qu'il y a un fichier XML
        $path = ($this->adminExtension ? 'administrator/' : '').'templates/'.$this->extensionSimpleName;
        if (!is_dir(JPATH_ROOT.'/'.$path)) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_FOLDER_NOT_FOUND', $path));
        }

        $xmlManifestFile = $path.'/templateDetails.xml';
        if (!is_file(JPATH_ROOT.'/'.$xmlManifestFile)) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_NOT_FOUND', $xmlManifestFile));
        }

        // Analyse du XML pour savoir comment construire le ZIP
        $xml = trim(file_get_contents(JPATH_ROOT.'/'.$xmlManifestFile));
        try {
            $data = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_CORRUPTED', $xmlManifestFile));
        }

        // Récupération de la version
        if (!empty($data->version)) {
            $this->extensionVersion = (string) $data->version;
        }

        // Ajout du manifest XML
        $this->zip->addFile(JPATH_ROOT.'/'.$xmlManifestFile, 'templateDetails.xml');

        // Fichier scriptfile
        if (!empty($data->scriptfile)) {
            $this->zip->addFile(JPATH_SITE.'/'.$path.'/'.$data->scriptfile, $data->scriptfile);
        }

        // Ajout des fichiers
        if (!empty($data->files)) {
            foreach ($data->files->filename as $file) {
                $fullPath = JPATH_ROOT.'/'.$path.'/'.$file;
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $path.'/'.$file));
                }
            }
            foreach ($data->files->folder as $folder) {
                $fullPath = JPATH_ROOT.'/'.$path.'/'.$folder;
                if (is_dir($fullPath)) {
                    $this->addFolderToZip($fullPath, $this->zip, $folder);
                } elseif ($this->errorsSupported) {
                    $this->zip->addEmptyDir($folder);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FOLDER_NOT_FOUND', $path.'/'.$folder));
                }
            }
        }

        // Ajout des fichiers langue
        if (!empty($data->languages)) {
            $languageFolder = empty($data->languages['folder']) ? '' : $data->languages['folder'].'/';
            foreach ($data->languages->language as $file) {
                $fullPath = ($this->adminExtension ? JPATH_ADMINISTRATOR : JPATH_ROOT).'/language/'.$file['tag'].'/'.basename($file);
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $languageFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $languageFolder.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $file));
                }
            }
        }

        return true;

    }

    /**
     * Compile un plugin
     *
     * @return boolean - Compilation OK
     */
    private function buildPlugin()
    {

        // On vérifie que l'on ai bien un groupe
        if (empty($this->extensionGroup)) {
            $this->raiseError(JText::_('COM_FMPACKAGER_ERROR_PLG_WITHOUT_GROUP'));
        }

        // Test si le module existe et qu'il y a un fichier XML
        $path = 'plugins/'.$this->extensionGroup.'/'.$this->extensionSimpleName;
        if (!is_dir(JPATH_ROOT.'/'.$path)) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_FOLDER_NOT_FOUND', $path));
        }

        $xmlManifestFile = $path.'/'.$this->extensionSimpleName.'.xml';
        if (!is_file(JPATH_ROOT.'/'.$xmlManifestFile)) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_NOT_FOUND', $xmlManifestFile));
        }

        // Analyse du XML pour savoir comment construire le ZIP
        $xml = trim(file_get_contents(JPATH_ROOT.'/'.$xmlManifestFile));
        try {
            $data = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_MANIFEST_FILE_CORRUPTED', $xmlManifestFile));
        }

        // Récupération de la version
        if (!empty($data->version)) {
            $this->extensionVersion = (string) $data->version;
        }

        // Ajout du manifest XML
        $this->zip->addFile(JPATH_ROOT.'/'.$xmlManifestFile, $this->extensionSimpleName.'.xml');

        // Fichier scriptfile
        if (!empty($data->scriptfile)) {
            $this->zip->addFile(JPATH_SITE.'/'.$path.'/'.$data->scriptfile, $data->scriptfile);
        }

        // Ajout des fichiers partie publique
        if (!empty($data->files)) {
            foreach ($data->files->filename as $file) {
                $fullPath = JPATH_ROOT.'/'.$path.'/'.$file;
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $path.'/'.$file));
                }
            }
            foreach ($data->files->folder as $folder) {
                $fullPath = JPATH_ROOT.'/'.$path.'/'.$folder;
                if (is_dir($fullPath)) {
                    $this->addFolderToZip($fullPath, $this->zip, $folder);
                } elseif ($this->errorsSupported) {
                    $this->zip->addEmptyDir($folder);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FOLDER_NOT_FOUND', $path.'/'.$folder));
                }
            }
        }

        // Ajout des fichiers langue partie admin
        if (!empty($data->languages)) {
            $languageFolder = empty($data->languages['folder']) ? '' : $data->languages['folder'].'/';
            foreach ($data->languages->language as $file) {
                $fullPath = JPATH_ADMINISTRATOR.'/language'.'/'.$file['tag'].'/'.basename($file);
                if (is_file($fullPath)) {
                    $this->zip->addFile($fullPath, $languageFolder.$file);
                } elseif ($this->errorsSupported) {
                    $this->zip->addFile($this->tmpEmptyFile, $languageFolder.'/'.$file);
                } else {
                    $this->raiseError(JText::sprintf('COM_FMPACKAGER_ERROR_XML_FILE_NOT_FOUND', $file));
                }
            }
        }

        return true;

    }

    /**
     * Ajoute un dossier complet à une archive ZIP
     * Librement adapté du code de D.Jann (http://php.net/manual/fr/ziparchive.addemptydir.php)
     *
     * @param string     $dir    - Répertoire local à ajouter
     * @param ZipArchive $zip    - Référence vers l'archive ZIP
     * @param string     $zipdir - Chemin à utiliser à l'intérieur du zip
     *
     * @return void
     */
    private function addFolderToZip($dir, $zipArchive, $zipdir = '')
    {

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                if (!empty($zipdir)) {
                    $zipArchive->addEmptyDir($zipdir);
                }
                while (($file = readdir($dh)) !== false) {
                    if (!is_file($dir.'/'.$file)) {
                        if (($file !== ".") && ($file !== "..")) {
                            $this->addFolderToZip($dir.'/'.$file, $zipArchive, $zipdir."/".$file);
                        }
                    } else {
                        $zipArchive->addFile($dir."/".$file, $zipdir."/".$file);
                    }
                }
            }
        }
    }

}
