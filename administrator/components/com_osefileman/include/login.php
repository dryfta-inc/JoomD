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

     The Original Code is login.php, released on 2003-03-31.

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
	User Authentication Functions
	
	Have Fun...
------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
require _QUIXPLORER_PATH."/include/fun_users.php";
load_users();
//------------------------------------------------------------------------------

if(isset($_SESSION)) 			$GLOBALS['__SESSION']=&$_SESSION;
elseif(isset($HTTP_SESSION_VARS))	$GLOBALS['__SESSION']=&$HTTP_SESSION_VARS;
else logout();
//------------------------------------------------------------------------------
function login() {
	global $my;
	if(isset($GLOBALS['__SESSION']["s_user"])) {
		if(!activate_user($GLOBALS['__SESSION']["s_user"],$GLOBALS['__SESSION']["s_pass"])) {
			logout();
		}
	} else {
		if(isset($GLOBALS['__POST']["p_pass"])) $p_pass=$GLOBALS['__POST']["p_pass"];
		else $p_pass="";
		
		if(isset($GLOBALS['__POST']["p_user"])) {
			// Check Login
			if(!activate_user(stripslashes($GLOBALS['__POST']["p_user"]), md5(stripslashes($p_pass)))) {
				logout();
			}
			return;
		} else {
			// Ask for Login
			show_header($GLOBALS["messages"]["actlogin"]);
			echo "<br><table width=\"300\"><tr><td colspan=\"2\" class=\"header\" nowrap><b>";
			echo $GLOBALS["messages"]["actloginheader"]."</b></td></tr>\n<form name=\"login\" action=\"";
			echo make_link("login",null,null)."\" method=\"post\">\n";
			echo "<tr><td>".$GLOBALS["messages"]["miscusername"].":</td><td align=\"right\">";
			echo "<input name=\"p_user\" type=\"text\" value=\"".$my->username."\" size=\"25\"></td></tr>\n";
			echo "<tr><td>".$GLOBALS["messages"]["miscpassword"].":</td><td align=\"right\">";
			echo "<input name=\"p_pass\" type=\"password\" size=\"25\"></td></tr>\n";
			echo "<tr><td>".$GLOBALS["messages"]["misclang"].":</td><td align=\"right\">";
			echo "<select name=\"lang\">\n";
			@include _QUIXPLORER_PATH."/languages/_info.php";
			echo "</select></td></tr>\n";
			echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"";
			echo $GLOBALS["messages"]["btnlogin"]."\"></td></tr>\n</form></table><br>\n";
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.login) document.login.p_user.focus();
// -->
</script><?php
			show_footer();
			exit;
		}
	}
}
//------------------------------------------------------------------------------
function logout() {
	$GLOBALS['__SESSION']["s_user"]="";
	$GLOBALS['__SESSION']["s_pass"]="";

	header("location: ".$GLOBALS["script_name"]);
}
//------------------------------------------------------------------------------
?>
