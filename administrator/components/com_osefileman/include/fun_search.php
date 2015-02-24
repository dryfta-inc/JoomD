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

     The Original Code is fun_search.php, released on 2003-03-31.

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
	File-Search Functions
	
	Have Fun...
------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function find_item($dir,$pat,&$list,$recur) {	// find items
	$handle=@opendir(get_abs_dir($dir));
	if($handle===false) return;		// unable to open dir
	
	while(($new_item=readdir($handle))!==false) {
		if(!@file_exists(get_abs_item($dir, $new_item))) continue;
		if(!get_show_item($dir, $new_item)) continue;
		
		// match?
		if(@eregi($pat,$new_item)) $list[]=array($dir,$new_item);
		
		// search sub-directories
		if(get_is_dir($dir, $new_item) && $recur) {
			find_item(get_rel_item($dir,$new_item),$pat,$list,$recur);
		}
	}
	
	closedir($handle);
}
//------------------------------------------------------------------------------
function make_list($dir,$item,$subdir) {	// make list of found items
	// convert shell-wildcards to PCRE Regex Syntax
	$pat="^".str_replace("?",".",str_replace("*",".*",str_replace(".","\.",$item)))."$";
	
	// search
	find_item($dir,$pat,$list,$subdir);
	if(is_array($list)) sort($list);
	return $list;
}
//------------------------------------------------------------------------------
function print_table($list) {			// print table of found items
	if(!is_array($list)) return;
	
	$cnt = count($list);
	for($i=0;$i<$cnt;++$i) {
		$dir = $list[$i][0];	$item = $list[$i][1];
		$s_dir=$dir;	if(strlen($s_dir)>65) $s_dir=substr($s_dir,0,62)."...";
		$s_item=$item;	if(strlen($s_item)>45) $s_item=substr($s_item,0,42)."...";
		$link = "";	$target = "";
		
		if(get_is_dir($dir,$item)) {
			$img = "dir.png";
			$link = make_link("list",get_rel_item($dir, $item),NULL);
		} else {
			$img = get_mime_type($dir, $item, "img");
			//if(get_is_editable($dir,$item) || get_is_image($dir,$item)) {
				$link = $GLOBALS["home_url"]."/".get_rel_item($dir, $item);
				$target = "_blank";
			//}
		}
		
		echo "<tr><td>" . "<img border=\"0\" width=\"22\" height=\"22\" ";
		echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/" . $img . "\" alt=\"\">&nbsp;";
		/*if($link!="")*/ echo"<a href=\"".$link."\" target=\"".$target."\">";
		//else echo "<a>";
		echo $s_item."</a></td><td><a href=\"" . make_link("list",$dir,null)."\"> /";
		echo $s_dir."</a></td></tr>\n";
	}
}
//------------------------------------------------------------------------------
function search_items($dir) {			// search for item
	if(isset($GLOBALS['__POST']["searchitem"])) {
		$searchitem=stripslashes($GLOBALS['__POST']["searchitem"]);
		$subdir=(isset($GLOBALS['__POST']["subdir"]) && $GLOBALS['__POST']["subdir"]=="y");
		$list=make_list($dir,$searchitem,$subdir);
	} else {
		$searchitem=NULL;
		$subdir=true;
	}
	
	$msg=$GLOBALS["messages"]["actsearchresults"];
	if($searchitem!=NULL) $msg.=": (/" . get_rel_item($dir, $searchitem).")";
	show_header($msg);
	
	// Search Box
	echo "<br><table><form name=\"searchform\" action=\"".make_link("search",$dir,null);
	echo "\" method=\"post\">\n<tr><td><input name=\"searchitem\" type=\"text\" size=\"25\" value=\"";
	echo $searchitem."\"><INPUT type=\"submit\" value=\"".$GLOBALS["messages"]["btnsearch"];
	echo "\">&nbsp;<input type=\"button\" value=\"".$GLOBALS["messages"]["btnclose"];
	echo "\" onClick=\"javascript:location='".make_link("list",$dir,NULL);
	echo "';\"></td></tr><tr><td><input type=\"checkbox\" name=\"subdir\" value=\"y\"";
	echo ($subdir?" checked>":">").$GLOBALS["messages"]["miscsubdirs"]."</td></tr></form></table>\n";
	
	// Results
	if($searchitem!=NULL) {
		echo "<table width=\"95%\"><tr><td colspan=\"2\"><hr></td></tr>\n";
		if(count($list)>0) {
			// table header
			echo "<tr>\n<td width=\"42%\" class=\"header\"><b>".$GLOBALS["messages"]["nameheader"];
			echo "</b></td>\n<td width=\"58%\" class=\"header\"><b>".$GLOBALS["messages"]["pathheader"];
			echo "</b></td></tr>\n<tr><td colspan=\"2\"><hr></td></tr>\n";
	
			// make & print table of found items
			print_table($list);

			echo "<tr><td colspan=\"2\"><hr></td></tr>\n<tr><td class=\"header\">".count($list)." ";
			echo $GLOBALS["messages"]["miscitems"].".</td><td class=\"header\"></td></tr>\n";
		} else {
			echo "<tr><td>".$GLOBALS["messages"]["miscnoresult"]."</td></tr>";
		}
		echo "<tr><td colspan=\"2\"><hr></td></tr></table>\n";
	}
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.searchform) document.searchform.searchitem.focus();
// -->
</script><?php
}
//------------------------------------------------------------------------------
?>
