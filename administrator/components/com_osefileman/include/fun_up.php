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

     The Original Code is fun_up.php, released on 2003-03-31.

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
	File-Upload Functions
	
	Have Fun...
------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function upload_items($dir) {		// upload file
	if(($GLOBALS["permissions"]&01)!=01) show_error($GLOBALS["error_msg"]["accessfunc"]);
	
	// Execute
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"]=="true") {	
		$cnt=count($GLOBALS['__FILES']['userfile']['name']);
		$err=false;
		$err_avaliable=isset($GLOBALS['__FILES']['userfile']['error']);
	
		// upload files & check for errors
		for($i=0;$i<$cnt;$i++) {
			$errors[$i]=NULL;
			$tmp = $GLOBALS['__FILES']['userfile']['tmp_name'][$i];
			$items[$i] = stripslashes($GLOBALS['__FILES']['userfile']['name'][$i]);
			if($err_avaliable) $up_err = $GLOBALS['__FILES']['userfile']['error'][$i];
			else $up_err=(file_exists($tmp)?0:4);
			$abs = get_abs_item($dir,$items[$i]);
		
			if($items[$i]=="" || $up_err==4) continue;
			if($up_err==1 || $up_err==2) {
				$errors[$i]=$GLOBALS["error_msg"]["miscfilesize"];
				$err=true;	continue;
			}
			if($up_err==3) {
				$errors[$i]=$GLOBALS["error_msg"]["miscfilepart"];
				$err=true;	continue;
			}
			if(!@is_uploaded_file($tmp)) {
				$errors[$i]=$GLOBALS["error_msg"]["uploadfile"];
				$err=true;	continue;
			}
			if(@file_exists($abs) && empty( $_REQUEST['overwrite_files'])) {
				$errors[$i]=$GLOBALS["error_msg"]["itemdoesexist"];
				$err=true;	continue;
			}
			
			// Upload
			$ok = @$GLOBALS['nx_File']->move_uploaded_file($tmp, $abs);
			
			if($ok===false || PEAR::isError( $ok )) {
				$errors[$i]=$GLOBALS["error_msg"]["uploadfile"];
				if( PEAR::isError( $ok ) ) $errors[$i].= ' ['.$ok->getMessage().']';
				$err=true;	continue;
			}
			elseif( !nx_isFTPMode()  ) {
				@$GLOBALS['nx_File']->chmod( $abs, 0644 );
			}
		}
		
		if($err) {			// there were errors
			$err_msg="";
			for($i=0;$i<$cnt;$i++) {
				if($errors[$i]==NULL) continue;
				$err_msg .= $items[$i]." : ".$errors[$i]."<BR>\n";
			}
			show_error($err_msg);
		}
		
		header("Location: ".make_link("list",$dir,NULL));
		return;
	}
	
	show_header($GLOBALS["messages"]["actupload"]);
	
	// List
	echo "<br /><form enctype=\"multipart/form-data\" action=\"".make_link("upload",$dir,NULL). "\" method=\"post\">
			<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"". get_max_file_size()."\" />
			<input type=\"hidden\" name=\"confirm\" value=\"true\" />
		<table style=\"width:60%;\" border=\"1\" class=\"adminform\">
			<tr><td class=\"quote\" colspan=\"2\">Maximum File Size = <strong>". ((get_max_file_size() / 1024) / 1024)." MB</strong><br />
				Maximum Upload Limit = <strong>". ((get_max_upload_limit() / 1024) / 1024)." MB</strong>
			</td></tr>
			";
	for($i=0;$i<10;$i++) {
		$class = $i % 2 ? 'row0' : 'row1';
		echo "<tr class=\"$class\"><td colspan=\"2\">";
		echo "<input name=\"userfile[]\" type=\"file\" size=\"50\" class=\"inputbox\" /></td></tr>\n";
	}
	echo "<tr><td colspan=\"2\">
				<input type=\"checkbox\" checked=\"checked\" value=\"1\" name=\"overwrite_files\" id=\"overwrite_files\" /><label for=\"overwrite_files\">".$GLOBALS["messages"]["overwrite_files"]. "</label>
			</td>
			</tr>
			<tr>
				<td width=\"40%\" style=\"text-align:right;\">
					<input type=\"submit\" value=\"".$GLOBALS["messages"]["btnupload"]. "\" class=\"button\" />&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
				<td width=\"60%\" style=\"text-align:left;\">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type=\"button\" value=\"".$GLOBALS["messages"]["btncancel"]."\" class=\"button\" onclick=\"javascript:location='".make_link("list",$dir,NULL)."';\" />
				</td>
			</tr>
		</table>
		</form><br />\n";
	
	return;
}
//------------------------------------------------------------------------------
?>
