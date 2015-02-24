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

     The Original Code is fun_admin.php, released on 2003-03-31.

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
	Administrative Functions
	
	Have Fun...
------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function admin($admin, $dir) {			// Change Password & Manage Users Form
	show_header($GLOBALS["messages"]["actadmin"]);
	
	// Javascript functions:
	include _QUIXPLORER_PATH . "/include/js_admin.php";
	
	// Change Password
	echo "<br/><HR width=\"95%\"><TABLE width=\"350\"><tr><td colspan=\"2\" class=\"header\"><B>";
	echo $GLOBALS["messages"]["actchpwd"].":</B></td></tr>\n";
	echo "<FORM name=\"chpwd\" action=\"".make_link("admin",$dir,NULL)."\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action2\" value=\"chpwd\">\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscoldpass"].": </td><td align=\"right\">";
	echo "<input type=\"password\" name=\"oldpwd\" size=\"25\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscnewpass"].": </td><td align=\"right\">";
	echo "<input type=\"password\" name=\"newpwd1\" size=\"25\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscconfnewpass"].": </td><td align=\"right\">";
	echo "<input type=\"password\" name=\"newpwd2\" size=\"25\"></td></tr>\n";
	echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"".$GLOBALS["messages"]["btnchange"];
	echo "\" onClick=\"return check_pwd();\">\n</td></tr></FORM></TABLE>\n";
	
	// Edit / Add / Remove User
	if($admin) {
		echo "<HR width=\"95%\"><TABLE width=\"350\"><tr><td colspan=\"6\" class=\"header\" nowrap>";
		echo "<B>".$GLOBALS["messages"]["actusers"].":</B></td></tr>\n";
		echo "<tr><td colspan=\"5\">".$GLOBALS["messages"]["miscuseritems"]."</td></tr>\n";
		echo "<FORM name=\"userform\" action=\"".make_link("admin",$dir,NULL)."\" method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"action2\" value=\"edituser\">\n";
		$cnt=count($GLOBALS["users"]);
		for($i=0;$i<$cnt;++$i) {
			// Username & Home dir:
			$user=$GLOBALS["users"][$i][0];	if(strlen($user)>15) $user=substr($user,0,12)."...";
			$home=$GLOBALS["users"][$i][2];	if(strlen($home)>30) $home=substr($home,0,27)."...";
			
			echo "<tr><td width=\"1%\"><input TYPE=\"radio\" name=\"user\" value=\"";
			echo $GLOBALS["users"][$i][0]."\"".(($i==0)?" checked":"")."></td>\n";
			echo "<td width=\"30%\">".$user."</td><td width=\"60%\">".$home."</td>\n";
			echo "<td width=\"3%\">".($GLOBALS["users"][$i][4]?$GLOBALS["messages"]["miscyesno"][2]:
				$GLOBALS["messages"]["miscyesno"][3])."</td>\n";
			echo "<td width=\"3%\">".$GLOBALS["users"][$i][6]."</td>\n";
			echo "<td width=\"3%\">".($GLOBALS["users"][$i][7]?$GLOBALS["messages"]["miscyesno"][2]:
				$GLOBALS["messages"]["miscyesno"][3])."</td></tr>\n";
		}
		echo "<tr><td colspan=\"6\" align=\"right\">";
		echo "<input type=\"button\" value=\"".$GLOBALS["messages"]["btnadd"];
		echo "\" onClick=\"javascript:location='".make_link("admin",$dir,NULL)."&action2=adduser';\">\n";
		echo "<input type=\"button\" value=\"".$GLOBALS["messages"]["btnedit"];
		echo "\" onClick=\"javascript:Edit();\">\n";
		echo "<input type=\"button\" value=\"".$GLOBALS["messages"]["btnremove"];
		echo "\" onClick=\"javascript:Delete();\">\n</td></tr></FORM></TABLE>\n";
	}
	
	echo "<HR width=\"95%\"><input type=\"button\" value=\"".$GLOBALS["messages"]["btnclose"];
	echo "\" onClick=\"javascript:location='".make_link("list",$dir,NULL)."';\"><br/><br/>\n";
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.chpwd) document.chpwd.oldpwd.focus();
// -->
</script><?php
}
//------------------------------------------------------------------------------
function changepwd($dir) {			// Change Password
	$pwd=md5(stripslashes($GLOBALS['__POST']["oldpwd"]));
	if($GLOBALS['__POST']["newpwd1"]!=$GLOBALS['__POST']["newpwd2"]) show_error($GLOBALS["error_msg"]["miscnopassmatch"]);
	
	$data=find_user($GLOBALS['__SESSION']["s_user"],$pwd);
	if($data==NULL) show_error($GLOBALS["error_msg"]["miscnouserpass"]);
	
	$data[1]=md5(stripslashes($GLOBALS['__POST']["newpwd1"]));
	if(!update_user($data[0],$data)) show_error($data[0].": ".$GLOBALS["error_msg"]["chpass"]);
	activate_user($data[0],NULL);
	
	header("location: ".make_link("list",$dir,NULL));
}
//------------------------------------------------------------------------------
function adduser($dir) {			// Add User
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"]=="true") {
		$user=stripslashes($GLOBALS['__POST']["user"]);
		if($user=="" || $GLOBALS['__POST']["home_dir"]=="") {
			show_error($GLOBALS["error_msg"]["miscfieldmissed"]);
		}
		if($GLOBALS['__POST']["pass1"]!=$GLOBALS['__POST']["pass2"]) show_error($GLOBALS["error_msg"]["miscnopassmatch"]);
		$data=find_user($user,NULL);
		if($data!=NULL) show_error($user.": ".$GLOBALS["error_msg"]["miscuserexist"]);
		
		$data=array($user,md5(stripslashes($GLOBALS['__POST']["pass1"])),
			stripslashes($GLOBALS['__POST']["home_dir"]),stripslashes($GLOBALS['__POST']["home_url"]),
			$GLOBALS['__POST']["show_hidden"],stripslashes($GLOBALS['__POST']["no_access"]),
			$GLOBALS['__POST']["permissions"],$GLOBALS['__POST']["active"]);
			
		if(!add_user($data)) show_error($user.": ".$GLOBALS["error_msg"]["adduser"]);
		header("location: ".make_link("admin",$dir,NULL));
		return;
	}
	
	show_header($GLOBALS["messages"]["actadmin"].": ".$GLOBALS["messages"]["miscadduser"]);
	
	// Javascript functions:
	include _QUIXPLORER_PATH . "/include/js_admin2.php";
	
	echo "<form name=\"adduser\" action=\"".make_link("admin",$dir,NULL)."&action2=adduser\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"confirm\" value=\"true\"><br/><TABLE width=\"450\">\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscusername"].":</td>\n";
		echo "<td align=\"right\"><input type=\"text\" name=\"user\" size=\"30\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscpassword"].":</td>\n";
		echo "<td align=\"right\"><input type=\"password\" name=\"pass1\" size=\"30\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscconfpass"].":</td>\n";
		echo "<td align=\"right\"><input type=\"password\" name=\"pass2\" size=\"30\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["mischomedir"].":</td>\n";
		echo "<td align=\"right\"><input type=\"text\" name=\"home_dir\" size=\"30\" value=\"";
		echo $GLOBALS["home_dir"]."\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["mischomeurl"].":</td>\n";
		echo "<td align=\"right\"><input type=\"text\" name=\"home_url\" size=\"30\" value=\"";
		echo $GLOBALS["home_url"]."\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscshowhidden"].":</td>";
		echo "<td align=\"right\"><select name=\"show_hidden\">\n";
		echo "<option value=\"0\">".$GLOBALS["messages"]["miscyesno"][1]."</option>";
		echo "<option value=\"1\">".$GLOBALS["messages"]["miscyesno"][0]."</option>\n";
		echo "</select></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["mischidepattern"].":</td>\n";
		echo "<td align=\"right\"><input type=\"text\" name=\"no_access\" size=\"30\" value=\"^\\.ht\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscperms"].":</td><td align=\"right\"><select name=\"permissions\">\n";
		$permvalues = array(0,1,2,3,7);
		for($i=0;$i<count($GLOBALS["messages"]["miscpermnames"]);++$i) {
			echo "<option value=\"".$permvalues[$i]."\">";
			echo $GLOBALS["messages"]["miscpermnames"][$i]."</option>\n";
		}
		echo "</select></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscactive"].":</td>";
		echo "<td align=\"right\"><select name=\"active\">\n";
		echo "<option value=\"1\">".$GLOBALS["messages"]["miscyesno"][0]."</option>";
		echo "<option value=\"0\">".$GLOBALS["messages"]["miscyesno"][1]."</option>\n";
		echo "</select></td></tr>\n";
	echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"".$GLOBALS["messages"]["btnadd"];
		echo "\" onClick=\"return check_pwd();\">\n<input type=\"button\" value=\"";
		echo $GLOBALS["messages"]["btncancel"]."\" onClick=\"javascript:location='";
		echo make_link("admin",$dir,NULL)."';\"></td></tr></FORM></TABLE><br/>\n";
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.adduser) document.adduser.user.focus();
// -->
</script><?php
}
//------------------------------------------------------------------------------
function edituser($dir) {			// Edit User
	$user=stripslashes($GLOBALS['__POST']["user"]);
	$data=find_user($user,NULL);
	if($data==NULL) show_error($user.": ".$GLOBALS["error_msg"]["miscnofinduser"]);
	if($self=($user==$GLOBALS['__SESSION']["s_user"])) $dir="";
	
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"]=="true") {
		$nuser=stripslashes($GLOBALS['__POST']["nuser"]);
		if($nuser=="" || $GLOBALS['__POST']["home_dir"]=="") {
			show_error($GLOBALS["error_msg"]["miscfieldmissed"]);
		}
		if(isset($GLOBALS['__POST']["chpass"]) &&
			$GLOBALS['__POST']["chpass"]=="true")
		{
			if($GLOBALS['__POST']["pass1"]!=$GLOBALS['__POST']["pass2"]) show_error($GLOBALS["error_msg"]["miscnopassmatch"]);
			$pass=md5(stripslashes($GLOBALS['__POST']["pass1"]));
		} else $pass=$data[1];
		
		if($self) $GLOBALS['__POST']["active"]=1;
		
		$data=array($nuser,$pass,stripslashes($GLOBALS['__POST']["home_dir"]),
			stripslashes($GLOBALS['__POST']["home_url"]),$GLOBALS['__POST']["show_hidden"],
			stripslashes($GLOBALS['__POST']["no_access"]),$GLOBALS['__POST']["permissions"],$GLOBALS['__POST']["active"]);
			
		if(!update_user($user,$data)) show_error($user.": ".$GLOBALS["error_msg"]["saveuser"]);
		if($self) activate_user($nuser,NULL);
		
		header("location: ".make_link("admin",$dir,NULL));
		return;
	}
	
	show_header($GLOBALS["messages"]["actadmin"].": ".sprintf($GLOBALS["messages"]["miscedituser"],$data[0]));
	
	// Javascript functions:
	include _QUIXPLORER_PATH . "/include/js_admin3.php";
	
	echo "<FORM name=\"edituser\" action=\"".make_link("admin",$dir,NULL)."&action2=edituser\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"confirm\" value=\"true\"><input type=\"hidden\" name=\"user\" value=\"".$data[0]."\">\n";
	echo "<br/><TABLE width=\"450\">\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscusername"].":</td>\n";
		echo "<td align=\"right\"><input type\"text\" name=\"nuser\" size=\"30\" value=\"";
		echo $data[0]."\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscconfpass"].":</td>\n";
		echo "<td align=\"right\"><input type=\"password\" name=\"pass1\" size=\"30\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscconfnewpass"].":</td>\n";
		echo "<td align=\"right\"><input type=\"password\" name=\"pass2\" size=\"30\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscchpass"].":</td>\n";
		echo "<td align=\"right\"><input type=\"checkbox\" name=\"chpass\" value=\"true\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["mischomedir"].":</td>\n";	
		echo "<td align=\"right\"><input type=\"text\" name=\"home_dir\" size=\"30\" value=\"";
		echo $data[2]."\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["mischomeurl"].":</td>\n";	
		echo "<td align=\"right\"><input type=\"text\" name=\"home_url\" size=\"30\" value=\"";
		echo $data[3]."\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscshowhidden"].":</td>";
		echo "<td align=\"right\"><select name=\"show_hidden\">\n";
		echo "<option value=\"0\">".$GLOBALS["messages"]["miscyesno"][1]."</option>";
		echo "<option value=\"1\"".($data[4]?" selected ":"").">";
		echo $GLOBALS["messages"]["miscyesno"][0]."</option>\n";
		echo "</select></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["mischidepattern"].":</td>\n";
		echo "<td align=\"right\"><input type=\"text\" name=\"no_access\" size=\"30\" value=\"";
		echo $data[5]."\"></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscperms"].":</td><td align=\"right\"><select name=\"permissions\">\n";
		$permvalues = array(0,1,2,3,7);
		for($i=0;$i<count($GLOBALS["messages"]["miscpermnames"]);++$i) {
			echo "<option value=\"".$permvalues[$i]."\"".($permvalues[$i]==$data[6]?" selected ":"").">";
			echo $GLOBALS["messages"]["miscpermnames"][$i]."</option>\n";
		}
		echo "</select></td></tr>\n";
	echo "<tr><td>".$GLOBALS["messages"]["miscactive"].":</td>";
		echo "<td align=\"right\"><select name=\"active\"".($self?" DISABLED ":"").">\n";
		echo "<option value=\"1\">".$GLOBALS["messages"]["miscyesno"][0]."</option>";
		echo "<option value=\"0\"".($data[7]?"":" selected ").">";
		echo $GLOBALS["messages"]["miscyesno"][1]."</option>\n";
		echo "</select></td></tr>\n";
	echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"".$GLOBALS["messages"]["btnsave"];
		echo "\" onclick=\"return check_pwd();\">\n<input type=\"button\" value=\"";
		echo $GLOBALS["messages"]["btncancel"]."\" onClick=\"javascript:location='";
		echo make_link("admin",$dir,NULL)."';\"></td></tr></form></table><br/>\n";
}
//------------------------------------------------------------------------------
function removeuser($dir) {			// Remove User
	$user=stripslashes($GLOBALS['__POST']["user"]);
	if($user==$GLOBALS['__SESSION']["s_user"]) show_error($GLOBALS["error_msg"]["miscselfremove"]);
	if(!remove_user($user)) show_error($user.": ".$GLOBALS["error_msg"]["deluser"]);
	
	header("location: ".make_link("admin",$dir,NULL));
}
//------------------------------------------------------------------------------
function show_admin($dir) {			// Execute Admin Action
	$pwd=(($GLOBALS["permissions"]&2)==2);
	$admin=(($GLOBALS["permissions"]&4)==4);
	
	if(!$GLOBALS["require_login"]) show_error($GLOBALS["error_msg"]["miscnofunc"]);
	if(!$pwd && !$admin) show_error($GLOBALS["error_msg"]["accessfunc"]);
	
	if(isset($GLOBALS['__GET']["action2"])) $action2 = $GLOBALS['__GET']["action2"];
	elseif(isset($GLOBALS['__POST']["action2"])) $action2 = $GLOBALS['__POST']["action2"];
	else $action2="";
	
	switch($action2) {
	case "chpwd":
		changepwd($dir);
	break;
	case "adduser":
		if(!$admin) show_error($GLOBALS["error_msg"]["accessfunc"]);
		adduser($dir);
	break;
	case "edituser":
		if(!$admin) show_error($GLOBALS["error_msg"]["accessfunc"]);
		edituser($dir);
	break;
	case "rmuser":
		if(!$admin) show_error($GLOBALS["error_msg"]["accessfunc"]);
		removeuser($dir);
	break;
	default:
		admin($admin,$dir);
	}
}
//------------------------------------------------------------------------------
?>
