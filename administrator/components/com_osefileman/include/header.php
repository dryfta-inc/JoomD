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

     The Original Code is header.php, released on 2003-02-07.

     The Initial Developer of the Original Code is The QuiX project.

     Alternatively, the contents of this file may be used under the terms
     of the GNU General Public License Version 2 or later (the "GPL"), in
     which case the provisions of the GPL are applicable instead of
     those above. If you wish to allow use of your version of this file only
     under the terms of the GPL and not to allow others to use
     your version of this file under the MPL, indicate your decision by
     deleting  the provisions above and replace  them with the notice and
     other provisions required by the GPL.  If you do not delete
     the provisions above, a recipient may use your version of this file
     under either the MPL or the GPL."
------------------------------------------------------------------------------*/
/*------------------------------------------------------------------------------
Author: The QuiX project
	quix@free.fr
	http://www.quix.tk
	http://quixplorer.sourceforge.net

Comment:
	QuiXplorer Version 2.3
	Header File

	Have Fun...
-------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
require("request_uri.php"); //Fix for IIS

function show_header($title) {
	$url = str_replace( '&dir=', '&ignore=', $_SERVER['REQUEST_URI'] );
	echo "<link rel=\"stylesheet\" href=\""._QUIXPLORER_URL."/style/style.css\" type=\"text/css\" />\n";
	
	echo "<div id='filemanlogo'>";
	echo "<a href=\"".$GLOBALS['nx_home']."\" target=\"_blank\" title=\"oseFileman Project\">" .
		 "<img border=\"0\" align=\"absmiddle\" id=\"nx_logo\" style=\"filter:alpha(opacity=10);-moz-opacity:.10;opacity:.10;\" onmouseover=\"opacity('nx_logo', 60, 99, 500);\" onmouseout=\"opacity('nx_logo', 100, 60, 500);\" ";
	echo "src=\""._QUIXPLORER_URL."/images/osefileman.png\" align=\"right\" alt=\"" . $GLOBALS['messages']['logolink'] . "\"></a>";
	echo "<div style='margin-left: 40px; '><small>Version ".$GLOBALS['nx_version']."</small></div>";
	echo "<div style='float:right'><img src='components/com_osefileman/images/compat_15_native.png'/><img src='components/com_osefileman/images/compat_16_native.png' /></div>";
	echo "</div>";

	echo "<div align=\"center\">\n";

}
//------------------------------------------------------------------------------
?>