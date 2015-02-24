<?php
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
/*------------------------------------------------------------------------------
     The contents of this file are subject to the Mozilla Public License
     Version 1.1 (the "License"); you may not use this file except in
     compliance with the License. You may obtain a copy of the License at
     http://www.mozilla.org/MPL/

     Software distributed under the License is distributed on an "AS IS"
     basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
     License for the specific language governing rights and limitations
     under the License.

------------------------------------------------------------------------------*/
/*------------------------------------------------------------------------------
Author: Mambo Dev Team
	  Soeren

Comment:
	Have Fun...
------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------


/**
* @package joomlaXplorer
*/
HTML_admin_misc::system_info( );

class HTML_admin_misc
{
    function get_php_setting($val)
    {
        $r =  (ini_get($val) == '1' ? 1 : 0);
        return $r ? JText::_( 'ON' ) : JText::_( 'OFF' ) ;
    }

    function get_server_software()
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            return $_SERVER['SERVER_SOFTWARE'];
        } else if (($sf = getenv('SERVER_SOFTWARE'))) {
            return $sf;
        } else {
            return JText::_( 'n/a' );
        }
    }

    function system_info( )
    {
        $mainframe=&JFactory::getApplication();
		if (VERSIONJ16==true)
		{
        $mainframe->redirect('index.php?option=com_admin&view=sysinfo');
		}
		else
		{
		$mainframe->redirect('index.php?option=com_admin&task=sysinfo');
		}
		/*
        $compobase = JPATH_SITE.DS."administrator".DS."components".DS."com_admin";

        //Load switcher behavior
        JHTML::_('behavior.switcher');

        $db =& JFactory::getDBO();

        $contents = '';
        ob_start();
        //require_once($compobase.DS.'tmpl'.DS.'navigation.php');
        $contents = ob_get_contents();
        ob_clean();

        $document =& JFactory::getDocument();
        $document->setBuffer($contents, 'modules', 'submenu');
        ?>
        <form action="index.php" method="post" name="adminForm">

        <div id="config-document">
            <div id="page-site">
                <table class="noshow">
                <tr>
                    <td>
                        <?php require_once($compobase.DS.'views/sysinfo/tmpl'.DS.'default_system.php'); ?>
                    </td>
                </tr>
                </table>
            </div>

            <div id="page-phpsettings">
                <table class="noshow">
                <tr>
                    <td>
                        <?php require_once($compobase.DS.'tmpl'.DS.'sysinfo_phpsettings.php'); ?>
                    </td>
                </tr>
                </table>
            </div>

            <div id="page-config">
                <table class="noshow">
                <tr>
                    <td>
                        <?php require_once($compobase.DS.'tmpl'.DS.'sysinfo_config.php'); ?>
                    </td>
                </tr>
                </table>
            </div>

            <div id="page-directory">
                <table class="noshow">
                <tr>
                    <td>
                        <?php require_once($compobase.DS.'tmpl'.DS.'sysinfo_directory.php'); ?>
                    </td>
                </tr>
                </table>
            </div>

            <div id="page-phpinfo">
                <table class="noshow">
                <tr>
                    <td>
                        <?php require_once($compobase.DS.'tmpl'.DS.'sysinfo_phpinfo.php'); ?>
                    </td>
                </tr>
                </table>
            </div>
        </div>

        <div class="clr"></div>
        <?php
        */
    }
}
function writableCell( $folder, $relative=1, $text='', $visible=1 )
{
    $writeable        = '<b><font color="green">'. JText::_( 'Writable' ) .'</font></b>';
    $unwriteable    = '<b><font color="red">'. JText::_( 'Unwritable' ) .'</font></b>';

    echo '<tr>';
    echo '<td class="item">';
    echo $text;
    if ( $visible ) {
        echo $folder . '/';
    }
    echo '</td>';
    echo '<td >';
    if ( $relative ) {
        echo is_writable( "../$folder" )    ? $writeable : $unwriteable;
    } else {
        echo is_writable( "$folder" )        ? $writeable : $unwriteable;
    }
    echo '</td>';
    echo '</tr>';
}
