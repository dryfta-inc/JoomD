<?php
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
/**
  * @version     3.0 +
  * @package       Open Source Excellence Security Suite
  * @subpackage    Open Source Excellence CPU
  * @author        Open Source Excellence {@link http://www.opensource-excellence.com}
  * @author        Created on 30-Sep-2010
  * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
  *
  *
  *  This program is free software: you can redistribute it and/or modify
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  (at your option) any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *  GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  *  @Copyright Copyright (C) 2008 - 2010- ... Open Source Excellence
*/
/*------------------------------------------------------------------------------
     The contents of this file are subject to the Mozilla Public License
     Version 1.1 (the "License"); you may not use this file except in
     compliance with the License. You may obtain a copy of the License at
     http://www.mozilla.org/MPL/

     Software distributed under the License is distributed on an "AS IS"
     basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
     License for the specific language governing rights and limitations
     under the License.

     The Original Code is fun_list.php, released on 2003-03-31.

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
	Directory-Listing Functions

	Have Fun...
###################################################################
/* NinjaXplorer
 *
 * $Id: fun_list.php 2 2008-04-20 15:10:20Z uwalter $
 *
 * By Richie Mortimer & Uwe Walter
 * http://www.ninjoomla.com
 * Copyright (C) 2008 ninjforge.com - Get on the Cutting Edge.
 * email: uwe@ninjaforge.com
 * date: December 2008
 * Release: 1.1
 * License : http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * Changelog
 * v1.0
 * Joomla! 1.5 Native
 * v1.1
 * Bugfix: Image view admin folder issue.
 *
 */

/*------------------------------------------------------------------------------*/
require_once( _QUIXPLORER_PATH.'/include/fun_bookmarks.php');
//------------------------------------------------------------------------------
// HELPER FUNCTIONS (USED BY MAIN FUNCTION 'list_dir', SEE BOTTOM)
function make_list($_list1, $_list2) {		// make list of files
	$list = array();

	if($GLOBALS["srt"]=="yes") {
		$list1 = $_list1;
		$list2 = $_list2;
	} else {
		$list1 = $_list2;
		$list2 = $_list1;
	}

	if(is_array($list1)) {
		while (list($key, $val) = each($list1)) {
			$list[$key] = $val;
		}
	}

	if(is_array($list2)) {
		while (list($key, $val) = each($list2)) {
			$list[$key] = $val;
		}
	}

	return $list;
}

//------------------------------------------------------------------------------
function make_tables($dir, &$dir_list, &$file_list, &$tot_file_size, &$num_items)
{						// make table of files in dir
	// make tables & place results in reference-variables passed to function
	// also 'return' total filesize & total number of items
	$homedir = realpath($GLOBALS['home_dir']);
	$tot_file_size = $num_items = 0;
	// Open directory

	$handle = @$GLOBALS['nx_File']->opendir(get_abs_dir($dir));

	if($handle===false && $dir=="") {
	  	$handle = @$GLOBALS['nx_File']->opendir($homedir . $GLOBALS['separator']);
	}

	if($handle===false)
	  show_error($dir.": ".$GLOBALS["error_msg"]["opendir"]);

	// Read directory
	while(($new_item = @$GLOBALS['nx_File']->readdir($handle))!==false) {

		if( is_array( $new_item ))  {
			$abs_new_item = $new_item;
		} else {
			$abs_new_item = get_abs_item($dir, $new_item);
		}

		if ($new_item == "." || $new_item == "..") continue;

		if(!@$GLOBALS['nx_File']->file_exists($abs_new_item)) //show_error($dir."/$abs_new_item: ".$GLOBALS["error_msg"]["readdir"]);
		if(!get_show_item($dir, $new_item)) continue;

		$new_file_size = @$GLOBALS['nx_File']->filesize($abs_new_item);
		$tot_file_size += $new_file_size;
		$num_items++;
		$new_item_name = $new_item;
		if( nx_isFTPMode() ) {
			$new_item_name = $new_item['name'];
		}

		if(get_is_dir( $abs_new_item)) {

			if($GLOBALS["order"]=="mod") {
				$dir_list[$new_item_name] =
					@$GLOBALS['nx_File']->filemtime($abs_new_item);
			} else {	// order == "size", "type" or "name"

				$dir_list[$new_item_name] = $new_item;
			}
		} else {
			if($GLOBALS["order"]=="size") {
				$file_list[$new_item_name] = $new_file_size;
			} elseif($GLOBALS["order"]=="mod") {
				$file_list[$new_item_name] =
					@$GLOBALS['nx_File']->filemtime($abs_new_item);
			} elseif($GLOBALS["order"]=="type") {
				$file_list[$new_item_name] =
					get_mime_type( $abs_new_item, "type");
			} else {	// order == "name"
				$file_list[$new_item_name] = $new_item;
			}
		}
	}
	@$GLOBALS['nx_File']->closedir($handle);

	// sort
	if(is_array($dir_list)) {
		if($GLOBALS["order"]=="mod") {
			if($GLOBALS["srt"]=="yes") arsort($dir_list);
			else asort($dir_list);
		} else {	// order == "size", "type" or "name"
			if($GLOBALS["srt"]=="yes") ksort($dir_list);
			else krsort($dir_list);
		}
	}

	// sort
	if(is_array($file_list)) {
		if($GLOBALS["order"]=="mod") {
			if($GLOBALS["srt"]=="yes") arsort($file_list);
			else asort($file_list);
		} elseif($GLOBALS["order"]=="size" || $GLOBALS["order"]=="type") {
			if($GLOBALS["srt"]=="yes") asort($file_list);
			else arsort($file_list);
		} else {	// order == "name"
			if($GLOBALS["srt"]=="yes") ksort($file_list);
			else krsort($file_list);
		}
	}
}
//------------------------------------------------------------------------------
function print_table($dir, $list, $allow) {	// print table of files
	global $dir_up;
	if(!is_array($list)) return;
	if( $dir != "" || strstr( $dir, _QUIXPLORER_PATH ) ) {
	  echo "<tr class=\"row1\">
	  			<td>&nbsp;</td>
	  			<td valign=\"baseline\">
	  				<a href=\"".make_link("list",$dir_up,NULL)."\">
	  				<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/arrow_turn_left.png\" alt=\"".$GLOBALS["messages"]["uplink"]."\" title=\"".$GLOBALS["messages"]["uplink"]."\"/>&nbsp;&nbsp;..</a>
	  			</td>
	  			<td>&nbsp;</td>
	  			<td>&nbsp;</td>
	  			<td>&nbsp;</td>
	  			<td>&nbsp;</td>
	  			<td>&nbsp;</td>";
	  if( extension_loaded( "posix" )) {
	  	echo "<td>&nbsp;</td>";
	  }
	  echo "</tr>";
	}
	$i = 0;
	$toggle = false;

	while(list($item,$info) = each($list)){
		// link to dir / file
		if( is_array( $info )) {
			$abs_item=$info;
			if( extension_loaded('posix')) {
				$user_info = posix_getpwnam( $info['user']);
				$file_info['uid'] = $user_info['uid'];
				$file_info['gid'] = $user_info['gid'];
			}
		} else {
			$abs_item=get_abs_item($dir,$item);
			$file_info = @stat( $abs_item );
		}

		$is_writable = @$GLOBALS['nx_File']->is_writable( $abs_item );
		$is_chmodable = @$GLOBALS['nx_File']->is_chmodable( $abs_item );
		$is_readable =@$GLOBALS['nx_File']->is_readable( $abs_item );
		$is_deletable = @$GLOBALS['nx_File']->is_deletable( $abs_item );

		$target="";
		$extra="";
		if(@$GLOBALS['nx_File']->is_link($abs_item))  {
			$extra=" -> ".@readlink($abs_item);
		}
		if(@get_is_dir($abs_item, '')) {
			$link = make_link("list",get_rel_item($dir, $item),NULL);
		} else {
			if(get_is_editable($abs_item) && $is_writable ) {
				$link = make_link( 'edit', $dir, $item);
			}
			elseif( $is_readable )  {
				if( strstr(get_abs_dir( $dir ), JPATH_SITE ) && !$GLOBALS['nx_File']->is_link($abs_item)) {
					$link = $GLOBALS["home_url"]."/".get_rel_item($dir, $item);

					// uwalter: Bugfix - image view admin folder issue.
					if (! preg_match('/index\.php/', $link)) {
						//$link = preg_replace('/(.+?)\/administrator(.*)/', '$1$2', $link);
						$link = preg_replace('/\/administrator/', '', $link);
					}
					$target = '_blank';
				} else {
					$link = make_link('download', $dir, $item );
				}
			}
		}

		if( nx_isIE() ) {
			echo '<tr onmouseover="style.backgroundColor=\'#D8ECFF\';" onmouseout="style.backgroundColor=\'#EAECEE\';" bgcolor=\'#EAECEE\'>';
		}
		else {
			$toggle = ($toggle) ? '1' : '0';
			echo "<tr class=\"row$toggle\">";
			$toggle = !$toggle;
		}
		echo "<td><input type=\"checkbox\" id=\"item_$i\" name=\"selitems[]\" value=\"";
		echo urlencode($item)."\" onclick=\"javascript:Toggle(this);\" /></td>\n";
	// Icon + Link
		echo "<td nowrap=\"nowrap\" align=\"left\">";
		if($is_readable) {
			echo"<a href=\"".$link."\" target=\"".$target."\">";
		}
		//else echo "<<>";
		echo "<img border=\"0\" width=\"16\" height=\"16\" ";

		echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/".get_mime_type($abs_item, "img")."\" alt=\"\" />&nbsp;";
		$s_item=$item;	if(strlen($s_item)>50) $s_item=substr($s_item,0,47)."...";
		echo htmlspecialchars($s_item . $extra );
		if( $is_readable ) {
			echo "</a>";	// ...$extra...
		}
		echo "</td>\n";
	// Size
		echo "<td>".parse_file_size(get_file_size( $abs_item))."</td>\n";
	// type
		echo "<td>".get_mime_type( $abs_item, "type")."</td>\n";
	// modified
		echo "<td>".parse_file_date( get_file_date($abs_item) )."</td>\n";
	// permissions
		echo "<td>";

		if($allow && $is_chmodable) {
			echo "<a href=\"".make_link("chmod",$dir,$item)."\" title=\"";
			echo $GLOBALS["messages"]["permlink"]."\">";
		}

		$perms = get_file_perms( $abs_item );
		if( strlen($perms)>3) {
			$perms = substr( $perms, 2 );
		}
		echo '<strong>'.$perms.'</strong><br />'
			.parse_file_type($dir,$item)
			.parse_file_perms( $perms )
			;
		if($allow && $is_chmodable ) {
			echo "</a>";
		}
		echo "</td>\n";

		// Owner
		error_reporting( E_ALL );
		if( extension_loaded( "posix" )) {
			echo "<td>\n";
			if(function_exists('posix_getpwuid'))
			{
			$user_info = posix_getpwuid( $file_info["uid"] );
			$group_info = posix_getgrgid($file_info["gid"] );
			echo $user_info["name"]. " (".$file_info["uid"].") /<br/>";
			echo $group_info["name"]. " (".$file_info["gid"].")";
			}
			else
			{
				echo "UID: (".$file_info["uid"].") /<br/>";
			    echo "GID: (".$file_info["gid"].")";
			}
			echo "</td>\n";
		}
		// actions
		echo "<td style=\"white-space:nowrap;\">\n";

		// Rename
		// A file that could be deleted can also be renamed
		if($allow && $is_deletable) {
			echo "<a href=\"".make_link("rename",$dir,$item)."\">";
			echo "<img border=\"0\" width=\"16\" height=\"16\" ";
			echo "src=\""._QUIXPLORER_URL."/images/_rename.gif\" alt=\"".$GLOBALS["messages"]["renamelink"]."\" title=\"";
			echo $GLOBALS["messages"]["renamelink"]."\" /></a>\n";
		}
		else {
			echo "<img border=\"0\" width=\"16\" height=\"16\" ";
			echo "src=\""._QUIXPLORER_URL."/images/_rename_.gif\" alt=\"".$GLOBALS["messages"]["renamelink"]."\" title=\"";
			echo $GLOBALS["messages"]["renamelink"]."\" />\n";
		}

		// EDIT

		if(get_is_editable($abs_item)) {

			if($allow && $is_writable) {
				echo "<a href=\"".make_link("edit",$dir,$item)."\">";
				echo "<img border=\"0\" width=\"16\" height=\"16\" ";
				echo "src=\""._QUIXPLORER_URL."/images/page_white_edit.png\" alt=\"".$GLOBALS["messages"]["editlink"]."\" title=\"";
				echo $GLOBALS["messages"]["editlink"]."\" /></a>\n";
			}
			else {
				echo "<img border=\"0\" width=\"16\" height=\"16\" ";
				echo "src=\""._QUIXPLORER_URL."/images/page_white_edit.png\" alt=\"".$GLOBALS["messages"]["editlink"]."\" title=\"";
				echo $GLOBALS["messages"]["editlink"]."\" />\n";
			}
		} else {
			// Extract Link
			if( nx_isArchive( $item ) && !nx_isFTPMode() ) {
			  echo "<a ";
			  echo "onclick=\"javascript: ClearAll();if( confirm('". ($GLOBALS["messages"]["extract_warning"]) ."') ) { return true } else { return false;}\" ";
			  echo "href=\"".make_link("extract",$dir,$item)."\" title=\"".$GLOBALS["messages"]["extractlink"]."\">";
			  echo "<img border=\"0\" width=\"22\" height=\"20\" ";
			  echo "src=\""._QUIXPLORER_URL."/images/_extract.png\" alt=\"".$GLOBALS["messages"]["extractlink"];
			  echo "\" title=\"".$GLOBALS["messages"]["extractlink"]."\" /></a>\n";
			}
			else {
			  echo "<img border=\"0\" width=\"16\" height=\"16\" ";
			  echo "src=\""._QUIXPLORER_URL."/images/_.gif\" alt=\"\" />\n";
			}
		}
		// VIEW
		if( get_is_editable($abs_item) && $GLOBALS['nx_File']->is_readable( $abs_item ) && get_is_file( $abs_item)) {
			$link = make_link("view",$dir,$item).'&tmpl=component';
			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=750,height=580,directories=no,location=no,screenX=100,screenY=100';
			echo "<a href=\"".$link."\" onclick=\"window.open('$link','win2','$status'); return false;\" title=\"". $GLOBALS["messages"]["viewlink"]."\">";
			echo "<img border=\"0\" width=\"16\" height=\"16\" ";
			echo "src=\""._QUIXPLORER_URL."/images/page_white_code_red.png\" alt=\"".$GLOBALS["messages"]["viewlink"]."\" /></a>\n";
		}
		// DOWNLOAD / Extract
		if(get_is_file( $abs_item )) {
			if($allow) {
				echo "<a href=\"".make_link("download",$dir,$item)."\" title=\"".$GLOBALS["messages"]["downlink"]."\">";
				echo "<img border=\"0\" width=\"16\" height=\"16\" ";
				echo "src=\""._QUIXPLORER_URL."/images/_download.png\" alt=\"".$GLOBALS["messages"]["downlink"];
				echo "\" title=\"".$GLOBALS["messages"]["downlink"]."\" /></a>\n";
			} else if(!$allow) {
				echo "<td><img border=\"0\" width=\"16\" height=\"16\" ";
				echo "src=\""._QUIXPLORER_URL."/images/_download_.png\" alt=\"".$GLOBALS["messages"]["downlink"];
				echo "\" title=\"".$GLOBALS["messages"]["downlink"]."\" />\n";
			}
		} else {
			echo "<img border=\"0\" width=\"16\" height=\"16\" ";
			echo "src=\""._QUIXPLORER_URL."/images/_.gif\" alt=\"\" />\n";
		}
		// DELETE
		if(get_is_file( $abs_item)) {
			if($allow && $GLOBALS['nx_File']->is_deletable( $abs_item )) {
				$confirm_msg = sprintf($GLOBALS["messages"]["confirm_delete_file"], $item );
				echo "<a name=\"link_item_$i\" href=\"#link_item_$i\" title=\"".$GLOBALS["messages"]["dellink"]."\"
				onclick=\"javascript: ClearAll(); document.getElementById('item_$i').checked = true; if( confirm('". $confirm_msg ."') ) { document.selform.do_action.value='delete'; document.selform.submit(); } else {   document.getElementById('item_$i').checked = false; return false;}\">";
				echo "<img border=\"0\" width=\"16\" height=\"16\" ";
				echo "src=\""._QUIXPLORER_URL."/images/page_white_delete.png\" alt=\"".$GLOBALS["messages"]["dellink"];
				echo "\" title=\"".$GLOBALS["messages"]["dellink"]."\" /></a>\n";
			}
			else {
				echo "<img border=\"0\" width=\"16\" height=\"16\" ";
				echo "src=\""._QUIXPLORER_URL."/images/page_white_delete.png\" alt=\"".$GLOBALS["messages"]["dellink"];
				echo "\" title=\"".$GLOBALS["messages"]["dellink"]."\" />\n";
			}
		} else {
			echo "<img border=\"0\" width=\"16\" height=\"16\" ";
			echo "src=\""._QUIXPLORER_URL."/images/_.gif\" alt=\"\" />\n";
		}
		echo "</td></tr>\n";
		$i++;
	}
}
//------------------------------------------------------------------------------
// MAIN FUNCTION
function list_dir($dir) {			// list directory contents
	global $dir_up, $_VERSION;
	//print_r('<br/>$dir: '.$dir);
	?>
	<script type="text/javascript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js"></script>
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
	<?php

	$allow=($GLOBALS["permissions"]&01)==01;
	$admin=((($GLOBALS["permissions"]&04)==04) || (($GLOBALS["permissions"]&02)==02));

	$dir_up = dirname($dir);
	//print_r('<br/>$dir_up: '.$dir_up);
	if($dir_up==".") $dir_up = "";

	if(!get_show_item($dir_up,basename($dir))) show_error($dir." : ".$GLOBALS["error_msg"]["accessdir"]);

	// make file & dir tables, & get total filesize & number of items
	$dir_list = array();
	$file_list = array();
	$tot_file_size = 0;
	$num_items = 0;
	make_tables($dir, $dir_list, $file_list, $tot_file_size, $num_items);

	$dirs = explode( "/", $dir );
	$implode = "";
	$dir_links = "<a href=\"".make_link( "list", "", null )."\">..</a>/";
	foreach( $dirs as $directory ) {
	  if( $directory != "" ) {
		$implode .= $directory."/";
		$dir_links .= "<a href=\"".make_link( "list", $implode, null )."\">$directory</a>/";
	  }
	}
	show_header($GLOBALS["messages"]["actdir"].": ".$dir_links);

	// Javascript functions:
	include _QUIXPLORER_PATH."/include/javascript.php";

	// Sorting of items
	$images = "&nbsp;<img width=\"10\" height=\"10\" border=\"0\" align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/";
	if($GLOBALS["srt"]=="yes") {
		$_srt = "no";	$images .= "_arrowup.gif\" alt=\"^\">";
	} else {
		$_srt = "yes";	$images .= "_arrowdown.gif\" alt=\"v\">";
	}

	// Toolbar
	echo "<br><table width=\"95%\"><tr><td><table class='toolbar'><tr>\n";

	// PARENT DIR
	echo "<td width='10px'>";
	if( $dir != "" ) {
	  echo "<a href=\"".make_link("list",$dir_up,NULL)."\">";
	  echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/arrow_turn_left.png\" ";
	  echo "alt=\"".$GLOBALS["messages"]["uplink"]."\" title=\"".$GLOBALS["messages"]["uplink"]."\"></a>";
	}
	echo "</td>\n";
	// HOME DIR
	echo "<td width='10px'><a href=\"".make_link("list",NULL,NULL)."\">";
	echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/home.png\" ";
	echo "alt=\"".$GLOBALS["messages"]["homelink"]."\" title=\"".$GLOBALS["messages"]["homelink"]."\"></a></td>\n";
	// RELOAD
	echo "<td width='10px'><a href=\"javascript:location.reload();\"><img border=\"0\" width=\"16\" height=\"16\" ";
	echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/arrow_refresh.png\" alt=\"".$GLOBALS["messages"]["reloadlink"];
	echo "\" title=\"".$GLOBALS["messages"]["reloadlink"]."\"></A></td>\n";
	// SEARCH
	if( !nx_isFTPMode() ) {
		echo "<td  width='10px'><a href=\"".make_link("search",$dir,NULL)."\">";
		echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/zoom.png\" ";
		echo "alt=\"".$GLOBALS["messages"]["searchlink"]."\" title=\"".$GLOBALS["messages"]["searchlink"];
		echo "\"></a></td>\n";
	}
	echo "<td width='3px'><img src=\"images/menu_divider.png\" height=\"22\" width=\"2\" border=\"0\" alt=\"|\" /></td>";

	// Joomla Sysinfo
	echo "<td width='10px'><a href=\"".make_link("sysinfo",$dir,NULL)."\">";
	echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/server_chart.png\" ";
	echo "alt=\"" . $GLOBALS['messages']['mossysinfolink'] . "\" title=\"" .$GLOBALS['messages']['mossysinfolink'] . "\"></a></td>\n";

	echo "<td width='3px'><img src=\"images/menu_divider.png\" height=\"22\" width=\"2\" border=\"0\" alt=\"|\" /></td>";

	if($allow) {
		// COPY
		echo "<td  width='10px'><a href=\"javascript:Copy();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/page_copy.png\" alt=\"".$GLOBALS["messages"]["copylink"];
		echo "\" title=\"".$GLOBALS["messages"]["copylink"]."\"></a></td>\n";
		// MOVE
		echo "<td width='10px'><a href=\"javascript:Move();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/folder_go.png\" alt=\"".$GLOBALS["messages"]["movelink"];
		echo "\" title=\"".$GLOBALS["messages"]["movelink"]."\"></A></td>\n";
		// DELETE
		echo "<td width='10px'><a href=\"javascript:Delete();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/delete.png\" alt=\"".$GLOBALS["messages"]["dellink"];
		echo "\" title=\"".$GLOBALS["messages"]["dellink"]."\"></A></td>\n";
		// CHMOD
		echo "<td width='10px'><a href=\"javascript:Chmod();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/chmod.png\" alt=\"chmod\" title=\"" . $GLOBALS['messages']['chmodlink'] . "\"></a></td>\n";
		// UPLOAD
		if(ini_get("file_uploads")) {
			echo "<td width='10px'><a href=\"".make_link("upload",$dir,NULL)."\">";
			echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
			echo "src=\""._QUIXPLORER_URL."/images/_upload.gif\" alt=\"".$GLOBALS["messages"]["uploadlink"];
			echo "\" title=\"".$GLOBALS["messages"]["uploadlink"]."\"></A></td>\n";
		} else {
			echo "<td width='10px'><img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
			echo "src=\""._QUIXPLORER_URL."/images/_upload_.gif\" alt=\"".$GLOBALS["messages"]["uploadlink"];
			echo "\" title=\"".$GLOBALS["messages"]["uploadlink"]."\"></td>\n";
		}
		// ARCHIVE
		if( ($GLOBALS["zip"] || $GLOBALS["tar"] || $GLOBALS["tgz"]) && !nx_isFTPMode() ) {
			echo "<td width='10px'><a href=\"javascript:Archive();\"><img border=\"0\" width=\"16\" height=\"16\" ";
			echo "align=\"absmiddle\" src=\""._QUIXPLORER_URL."/images/compress.png\" alt=\"".$GLOBALS["messages"]["comprlink"];
			echo "\" title=\"".$GLOBALS["messages"]["comprlink"]."\"></a></td>\n";
		}
	} else {
		// COPY
		echo "<td width='10px'><img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
		echo "src=\""._QUIXPLORER_URL."/images/_copy_.gif\" alt=\"".$GLOBALS["messages"]["copylink"]."\" title=\"";
		echo $GLOBALS["messages"]["copylink"]."\"></td>\n";
		// MOVE
		echo "<td width='10px'><img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
		echo "src=\""._QUIXPLORER_URL."/images/_move_.gif\" alt=\"".$GLOBALS["messages"]["movelink"]."\" title=\"";
		echo $GLOBALS["messages"]["movelink"]."\"></td>\n";
		// DELETE
		echo "<td width='10px'><img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
		echo "src=\""._QUIXPLORER_URL."/images/_delete_.gif\" alt=\"".$GLOBALS["messages"]["dellink"]."\" title=\"";
		echo $GLOBALS["messages"]["dellink"]."\"></td>\n";
		// UPLOAD
		echo "<td width='10px'><img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
		echo "src=\""._QUIXPLORER_URL."/images/_upload_.gif\" alt=\"".$GLOBALS["messages"]["uplink"];
		echo "\" title=\"".$GLOBALS["messages"]["uplink"]."\"></td>\n";
	}

	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'filescan'.DS.'filescan.php'))
	{
	echo "<td id='filescanerToolbar'>";
	echo "<button id=\"cfgupdate\" class='button' onClick='DBInitialize()'>".JText::_('Initialise DB with the selected directory')."</button>";
	echo "</td>";
	}
	// ADMIN & LOGOUT
	if($GLOBALS["require_login"]) {
		echo "<td width='10px'>::</td>";
		// ADMIN
		if($admin) {
			echo "<td width='10px'><a href=\"".make_link("admin",$dir,NULL)."\">";
			echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
			echo "src=\""._QUIXPLORER_URL."/images/_admin.gif\" alt=\"".$GLOBALS["messages"]["adminlink"]."\" title=\"";
			echo $GLOBALS["messages"]["adminlink"]."\"></A></td>\n";
		}
		// LOGOUT
		echo "<td width='10px'><a href=\"".make_link("logout",NULL,NULL)."\">";
		echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\" ";
		echo "src=\""._QUIXPLORER_URL."/images/_logout.gif\" alt=\"".$GLOBALS["messages"]["logoutlink"]."\" title=\"";
		echo $GLOBALS["messages"]["logoutlink"]."\"></a></td>\n";
	}

	// Create File / Dir

	if($allow && @$GLOBALS['nx_File']->is_writable( get_abs_dir( $dir)) ) {
		echo "<td align=\"right\">
				<form action=\"".make_link("mkitem",$dir,NULL)."\" method=\"post\" name=\"mkitemform\">\n
				<table><tr><td>
					<select name=\"mktype\" onchange=\"checkMkitemForm(this.options[this.selectedIndex])\">
						<option value=\"file\">".$GLOBALS["mimes"]["file"]."</option>
						<option value=\"dir\">".$GLOBALS["mimes"]["dir"]."</option>";
		if( !nx_isFTPMode() && !$GLOBALS['isWindows']) {
			echo "			<option value=\"symlink\">".$GLOBALS["mimes"]["symlink"]."</option>\n";
		}
		echo "		</select>
					<input name=\"symlink_target\" type=\"hidden\" size=\"25\" title=\"{$GLOBALS['messages']['symlink_target']}\" value=\"".JPATH_BASE."\" />
					<input name=\"mkname\" type=\"text\" size=\"15\" title=\"{$GLOBALS['messages']['nameheader']}\" />
					<input type=\"submit\" value=\"".$GLOBALS["messages"]["btncreate"]."\" />
					</td></tr>
					<tr><td id=\"quick_jumpto\">".list_bookmarks( $dir )."</td></tr>
				</table>
				<script type=\"text/javascript\">function checkMkitemForm( el ) { if( el.value =='symlink' ) document.mkitemform.symlink_target.type='text'; else document.mkitemform.symlink_target.type='hidden';} </script>
				</form>
			  </td>\n";
	}
	else {
		echo "<td align=\"right\">
				<table><tr><td id=\"quick_jumpto\">".list_bookmarks( $dir )."</td></tr></table>
			 </td>";
	}

	echo "</tr></table>\n";

	// End Toolbar


	// Begin Table + Form for checkboxes
	echo "<form name=\"selform\" method=\"post\" action=\"".make_link("post",$dir,null)."\">
	<input type=\"hidden\" name=\"do_action\" /><input type=\"hidden\" name=\"first\" value=\"y\" />
	<table class=\"adminlist\" width=\"95%\">\n";

	if( extension_loaded( "posix" )) {
	  	$owner_info = '<th width="15%" class="title">' . $GLOBALS['messages']['miscowner'] . '&nbsp;';
	  	if( nx_isFTPMode() ) {
	  		$my_user_info = posix_getpwnam( $_SESSION['ftp_login'] );
	  		$my_group_info = posix_getgrgid( $my_user_info['gid'] );
	  	} else {
	  		if (function_exists('posix_getpwuid'))
	  		{
			$my_user_info = posix_getpwuid( posix_geteuid() );
			$my_group_info = posix_getgrgid(posix_getegid() );
	  		}
	  		else
	  		{
	  		$my_user_info = "UID: ".posix_geteuid();
			$my_group_info = "GID: ".posix_getegid();
	  		}
	  	}
		$owner_info .= mosTooltip( mysql_escape_string( sprintf( $GLOBALS['messages']['miscownerdesc'],  $my_user_info['name'], $my_user_info['uid'], $my_group_info['name'], $my_group_info['gid'] ))); // new [mic]

	  	$owner_info .= "</th>\n";
	  	$colspan=8;
	}
	else {
	  $owner_info = "";
	  $colspan = 7;
	}
	// Table Header
	echo "<tr>
	<th width=\"2%\" class=\"title\">
		<input type=\"checkbox\" name=\"toggleAllC\" onclick=\"javascript:ToggleAll(this);\" />
	</th>
	<th width=\"34%\" class=\"title\">\n";
	if($GLOBALS["order"]=="name") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"name",$new_srt)."\">".$GLOBALS["messages"]["nameheader"];
	if($GLOBALS["order"]=="name") echo $images; echo '</a>';
	echo "</th>
	<th width=\"10%\" class=\"title\">";
	if($GLOBALS["order"]=="size") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"size",$new_srt)."\">".$GLOBALS["messages"]["sizeheader"];
	if($GLOBALS["order"]=="size") echo $images;
	echo "</a></th>
	<th width=\"14%\" class=\"title\">";
	if($GLOBALS["order"]=="type") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"type",$new_srt)."\">".$GLOBALS["messages"]["typeheader"];
	if($GLOBALS["order"]=="type") echo $images;
	echo "</a></th>
	<th width=\"14%\" class=\"title\">";
	if($GLOBALS["order"]=="mod") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"mod",$new_srt)."\">".$GLOBALS["messages"]["modifheader"];
	if($GLOBALS["order"]=="mod") echo $images;
	echo "</a></th>
	<th width=\"2%\" class=\"title\">".$GLOBALS["messages"]["permheader"]."\n";
	echo "</th>";
	echo $owner_info;
	echo "<th width=\"10%\" class=\"title\">".$GLOBALS["messages"]["actionheader"]."</th>

	</tr>\n";

	// make & print Table using lists
	print_table($dir, make_list($dir_list, $file_list), $allow);

	// print number of items & total filesize
	echo "<tr><td colspan=\"$colspan\"><hr/></td></tr><tr>\n<td class=\"title\"></td>";
	echo "<td class=\"title\">".$num_items." ".$GLOBALS["messages"]["miscitems"]." (";

	if(function_exists("disk_free_space")) {
		$size = disk_free_space($GLOBALS['home_dir']. $GLOBALS['separator']);
		$free=parse_file_size($size);
	}
	elseif(function_exists("diskfreespace")) {
		$size = diskfreespace($GLOBALS['home_dir'] . $GLOBALS['separator']);
		$free=parse_file_size($size);
	}
	else $free = "?";

	echo $GLOBALS["messages"]["miscfree"].": ".$free.")</td>\n";
	echo "<td class=\"title\">".parse_file_size($tot_file_size)."</td>\n";
	for($i=0;$i<($colspan-3);++$i) echo"<td class=\"title\"></td>";
	echo "</tr>\n<tr><td colspan=\"$colspan\"><hr/></td></tr></table>
		</form>";

?><script type="text/javascript"><!--
	// Uncheck all items (to avoid problems with new items)
	var ml = document.selform;
	var len = ml.elements.length;
	for(var i=0; i<len; ++i) {
		var e = ml.elements[i];
		if(e.name == "selitems[]" && e.checked == true) {
			e.checked=false;
		}
	}
	opacity('nx_logo', 10, 60, 2000);
// --></script>

<?php
}
//------------------------------------------------------------------------------
?>
