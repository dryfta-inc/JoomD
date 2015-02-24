<?php
// ensure this file is being included by a parent file
defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: fun_extra.php 88 2007-09-18 15:47:39Z soeren $
 * @package joomlaXplorer
 * @copyright soeren 2007
 * @author The joomlaXplorer project (http://joomlacode.org/gf/project/joomlaxplorer/)
 * @author The  The QuiX project (http://quixplorer.sourceforge.net)
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * Alternatively, the contents of this file may be used under the terms
 * of the GNU General Public License Version 2 or later (the "GPL"), in
 * which case the provisions of the GPL are applicable instead of
 * those above. If you wish to allow use of your version of this file only
 * under the terms of the GPL and not to allow others to use
 * your version of this file under the MPL, indicate your decision by
 * deleting  the provisions above and replace  them with the notice and
 * other provisions required by the GPL.  If you do not delete
 * the provisions above, a recipient may use your version of this file
 * under either the MPL or the GPL."
 *
 */

/**
 * THESE ARE NUMEROUS HELPER FUNCTIONS FOR THE OTHER INCLUDE FILES
 */

$GLOBALS['isWindows'] = substr(PHP_OS, 0, 3) == 'WIN';

function make_link($_action,$_dir,$_item=NULL,$_order=NULL,$_srt=NULL,$languages=NULL) {
	// make link to next page
	if($_action=="" || $_action==NULL) $_action="list";
	//if($_dir=="") $_dir=NULL;
	if($_item=="") $_item=NULL;
	if($_order==NULL) $_order=$GLOBALS["order"];
	if($_srt==NULL) $_srt=$GLOBALS["srt"];
	if($languages==NULL) $languages=(isset($GLOBALS["lang"])?$GLOBALS["lang"]:NULL);

	$link=$GLOBALS["script_name"]."?option=com_osefileman&action=".$_action;
	$link.="&dir=".urlencode($_dir);
	if($_item!=NULL) $link.="&item=".urlencode($_item);
	if($_order!=NULL) $link.="&order=".$_order;
	if($_srt!=NULL) $link.="&srt=".$_srt;
	if($languages!=NULL) $link.="&lang=".$languages;

	return $link;
}
//------------------------------------------------------------------------------
function get_abs_dir($dir) {			// get absolute path
	if( nx_isFTPMode() ) {
		if( $dir != '' && $dir[0] != '/' ) {
			$dir = '/'.$dir;
		}
		return $dir;
	}
	$abs_dir=$GLOBALS["home_dir"];

	if($dir!="" && !stristr( $dir, $abs_dir )) $abs_dir.="/".$dir;
	elseif(stristr( $dir, $abs_dir )) $abs_dir = "/".$dir;
	/*else {
		$abs_dir = $dir;
	}*/
	$realpath = realpath($abs_dir);
	/*if( $realpath == '') {
		return $abs_dir;
	}
	else {
		return $realpath;
	}*/

	return $realpath;
}
//------------------------------------------------------------------------------
function get_abs_item($dir, $item) {		// get absolute file+path
	if( is_array( $item )) {
		// FTP Mode
		$abs_item = '/' . get_abs_dir($dir)."/".$item['name'];
		if( get_is_dir($item)) $abs_item.='/';
		return $abs_item;
	}
	return get_abs_dir($dir)."/".$item;
}
/**
 * Returns the LS info array from an ftp directory listing
 *
 * @param unknown_type $dir
 * @param unknown_type $item
 * @return unknown
 */
function get_item_info( $dir, $item ) {
	$ls = getCachedFTPListing( $dir );
	foreach( $ls as $entry ) {
		if( $entry['name'] == $item ) {
			return $entry;
		}
	}
	if( $dir != '') {
		return $dir.'/'.$item;
	}
	return $item;
}
//------------------------------------------------------------------------------
function get_rel_item($dir,$item) {		// get file relative from home
	if($dir!="") return $dir."/".$item;
	else return $item;
}
//------------------------------------------------------------------------------
function get_is_file( $abs_item) {		// can this file be edited?
	if( nx_isFTPMode() && is_array( $abs_item )) {
		return empty($abs_item['is_dir']);
	} elseif( nx_isFTPMode() ) {
		$info = get_item_info( dirname($abs_item), basename($abs_item));
		return empty($info['is_dir']);
	}

	return @is_file($abs_item);
}
//------------------------------------------------------------------------------
function get_is_dir( $abs_item ) {		// is this a directory?
	if( nx_isFTPMode() && is_array( $abs_item )) {
		return !empty($abs_item['is_dir']);
	}
	elseif( nx_isFTPMode() ) {
		$info = get_item_info( dirname( $abs_item), basename( $abs_item ));
		return !empty($info['is_dir']);
	}
	return @is_dir( $abs_item );
}
//------------------------------------------------------------------------------
function parse_file_type( $abs_item ) {		// parsed file type (d / l / -)

	if(@get_is_dir($abs_item)) return "d";
	if(@is_link($abs_item)) return "l";
	return "-";
}
//------------------------------------------------------------------------------
function get_file_perms( $item) {		// file permissions
	if( nx_isFTPMode() ) {
		$perms = decoct( bindec( decode_ftp_rights($item['rights']) ) );
		return $perms;
	}
	return @decoct(@fileperms( $item ) & 0777);
}
//------------------------------------------------------------------------------
function parse_file_perms($mode) {		// parsed file permisions

	if(strlen($mode)<3) return "---------";
	$parsed_mode="";
	for($i=0;$i<3;$i++) {
		// read
		if(($mode{$i} & 04)) $parsed_mode .= "r";
		else $parsed_mode .= "-";
		// write
		if(($mode{$i} & 02)) $parsed_mode .= "w";
		else $parsed_mode .= "-";
		// execute
		if(($mode{$i} & 01)) $parsed_mode .= "x";
		else $parsed_mode .= "-";
	}
	return $parsed_mode;
}

function decode_ftp_rights( $rights) {
	$parsed_mode="";
	for($i=0;$i<9;$i++) {
		// read
		if( $rights[$i] != '-' ) {
			$parsed_mode .= '1';
		}
		else {
			$parsed_mode.= '0';
		}
	}

	return $parsed_mode;
}
//------------------------------------------------------------------------------
function get_file_size( $abs_item) {		// file size
	return @$GLOBALS['nx_File']->filesize( $abs_item );
}
//------------------------------------------------------------------------------
function parse_file_size($size) {		// parsed file size
	if($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 . " GB";
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 . " MB";
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 . " KB";
	} else $size = $size . " Bytes";
	if($size==0) $size="-";

	return $size;
}
//------------------------------------------------------------------------------
function get_file_date( $item) {		// file date
	return @$GLOBALS['nx_File']->filemtime( $item );
}
//------------------------------------------------------------------------------
function parse_file_date($date) {		// parsed file date
	return @date($GLOBALS["date_fmt"],$date);
}
//------------------------------------------------------------------------------
function get_is_image( $abs_item ) {		// is this file an image?
	if(!get_is_file($abs_item)) return false;
	if( isset($abs_item['name'])) {
		$abs_item = $abs_item['name'];
	}
	return @eregi($GLOBALS["images_ext"], $abs_item);
}
//-----------------------------------------------------------------------------
function get_is_editable( $abs_item ) {		// is this file editable?
	if(!get_is_file( $abs_item )) return false;
	if( is_array( $abs_item ) ) {
		 $abs_item = $abs_item['name'];
	}
	foreach($GLOBALS["editable_ext"] as $pat) if(@eregi($pat,$abs_item)) return true;

	return strpos( basename($abs_item), "." ) ? false : true;

}
//-----------------------------------------------------------------------------
function get_mime_type( $abs_item, $query) {	// get file's mimetype

	if(get_is_dir( $abs_item )) {			// directory
		$mime_type	= $GLOBALS["super_mimes"]["dir"][0];
		$image		= $GLOBALS["super_mimes"]["dir"][1];

		if($query=="img") return $image;
		else return $mime_type;
	}
	$extra = $GLOBALS['nx_File']->is_link( $abs_item ) ? ' ('.$GLOBALS['mimes']['symlink'].')' : '';
	if( nx_isFTPMode() && isset($abs_item['name']) ) {
		$abs_item=$abs_item['name'];
	}
				// mime_type
	foreach($GLOBALS["used_mime_types"] as $mime) {
		list($desc,$img,$ext)	= $mime;
		if(@eregi($ext,basename($abs_item) )) {
			$mime_type	= $desc;
			$image		= $img;
			if($query=="img") return $image;
			else return $mime_type . $extra;
		}
	}

	if((function_exists("is_executable") &&
		@is_executable( $abs_item )) ||
		@eregi($GLOBALS["super_mimes"]["exe"][2],$abs_item))
	{						// executable
		$mime_type	= $GLOBALS["super_mimes"]["exe"][0];
		$image		= $GLOBALS["super_mimes"]["exe"][1];
	} else {					// unknown file
		$mime_type	= $GLOBALS["super_mimes"]["file"][0];
		$image		= $GLOBALS["super_mimes"]["file"][1];
	}

	if($query=="img")
	  return $image;
	else
	  return $mime_type . $extra;
}
//------------------------------------------------------------------------------
function get_show_item($dir, $item) {		// show this file?
	if( is_array( $item )) {
		$item = $item['name'];
	}
	//check to see if we are trying to edit .htaccess

	if($item == "." || $item == ".." ) return false;

	if($GLOBALS["no_access"]!="" && @eregi($GLOBALS["no_access"],$item)) return false;

	if($GLOBALS["show_hidden"]==false) {
		$dirs=explode("/",$dir);
		foreach($dirs as $i) if(substr($i,0,1)==".") return false;
	}

	return true;
}
//------------------------------------------------------------------------------
function get_dir_list( $dir='' ) {
	$files = JFolder::files( get_abs_dir( $dir), '.', false, true );
	$dirs =array();
	foreach( $files as $item) {
		$item = str_replace( '\\', '/', $item );
		if( get_is_dir($item)) {
			$index = str_replace( $GLOBALS['home_dir'].$GLOBALS['separator'], '', $item );
			$dirs[$index]= basename($index);
		}
	}
	return $dirs;
}
function get_dir_selects( $dir ) {
	$dirs = explode( "/", $dir );

	$subdirs = get_dir_list();
	if( empty($dirs[0]) ) array_shift($dirs);
	$dirsCopy = $dirs;
	$implode = '';
	$selectedDir = @$dirs[0];
	$dir_links = nx_selectList('dirselect1', $selectedDir, $subdirs, 1, '', 'onchange="theDir=this.options[this.selectedIndex].value;if(theDir!=\'nx_disabled\' ) chDir(theDir);"' );
	$i = 2;
	foreach( $dirs as $directory ) {
	  	if( $directory != "" ) {
			$implode .= $directory;
			$next = next($dirsCopy);
			$subdirs = get_dir_list( $implode );
			if( $next !== false ) {
				$selectedDir .= '/'.$next;
			} else {
				if( sizeof( $subdirs ) > 0) {
					$subdirs = array_merge(Array('nx_disabled' => '-'), $subdirs );
				}
			}
			$dir_links .= ' / ' .nx_selectList('dirselect'.$i++, $selectedDir, $subdirs, 1, '', 'onchange="theDir=this.options[this.selectedIndex].value;if(theDir!=\'nx_disabled\' ) chDir(theDir);"' );
			$implode .= '/';
	  	}
	}

	return $dir_links;
}
//------------------------------------------------------------------------------
function copy_dir($source,$dest) {		// copy dir
	$ok = true;

	if(!@mkdir($dest,0777)) return false;
	$itemlist = JFolder::files( $source, '.', true, true );
	if( empty( $itemlist )) return false;

	foreach( $itemlist as $file ) {
		if(($file==".." || $file==".")) continue;

		$new_dest = str_replace( $source, $dest, $file );
		if(@is_dir($file)) {
			@mkdir($new_dest,0777);
		} else {
			$ok=@copy($file,$new_dest);
		}
	}

	return $ok;
}

//------------------------------------------------------------------------------
function remove($item) {			// remove file / dir

	if( !is_link( $item )) {
		$item = realpath($item);
	}
	$ok = true;
	if( is_link($item) ||  is_file($item))
	  $ok =  unlink($item);
	elseif( is_dir($item)) {

		if(($handle= opendir($item))===false)
		  show_error(basename($item).": ".$GLOBALS["error_msg"]["opendir"]);

		while(($file=readdir($handle))!==false) {
			if(($file==".." || $file==".")) continue;

			$new_item = $item."/".$file;
			if(!file_exists($new_item))
			  show_error(basename($item).": ".$GLOBALS["error_msg"]["readdir"]);
			//if(!get_show_item($item, $new_item)) continue;

			if( is_dir($new_item)) {
				$ok=remove($new_item);
			} else {
				$ok= unlink($new_item);
			}
		}

		closedir($handle);
		$ok=@rmdir($item);
	}
	return $ok;
}
function chmod_recursive($item, $mode) {			// chmod file / dir
	$ok = true;
	if(@is_link($item) || @is_file($item)) {
		$ok=@chmod( $item, $mode );
	}
	elseif(@is_dir($item)) {
		if(($handle=@opendir($item))===false) {

			add_error(basename($item).": ".$GLOBALS["error_msg"]["opendir"]);
			return false;
		}

		while(($file=readdir($handle))!==false) {
			if(($file==".." || $file==".")) continue;

			$new_item = $item."/".$file;
			if(!@file_exists($new_item)) {
				add_error(basename($item).": ".$GLOBALS["error_msg"]["readdir"]);
				continue;
			}
			//if(!get_show_item($item, $new_item)) continue;

			if(@is_dir($new_item)) {
				$ok=chmod_recursive($new_item, $mode);
				if($ok) add_message($GLOBALS['messages']['permchange'].' '.$new_item);
			} else {
				$ok=@chmod($new_item, $mode);
				if($ok) add_message($GLOBALS['messages']['permchange'].' '.$new_item);
			}
		}
		closedir($handle);
		if( is_dir( $item )) {
			$bin = decbin( $mode );
			// when we chmod a directory we must care for the permissions
			// to prevent that the directory becomes not readable (when the "execute bits" are removed)
			$bin = substr_replace( $bin, '1', 2, 1 ); // set 1st x bit to 1
			$bin = substr_replace( $bin, '1', 5, 1 );// set  2nd x bit to 1
			$bin = substr_replace( $bin, '1', 8, 1 );// set 3rd x bit to 1
			$mode = bindec( $bin );
		}
		$ok=@chmod( $item, $mode );
		if($ok) add_message($GLOBALS['messages']['permchange'].' '.$new_item);
	}
	return $ok;
}
//------------------------------------------------------------------------------
function get_max_file_size() {			// get php max_upload_file_size
	return calc_php_setting_bytes( ini_get("upload_max_filesize") );
}
function get_max_upload_limit() {
	return calc_php_setting_bytes( ini_get('post_max_size'));
}

function calc_php_setting_bytes( $value ) {
	if(@eregi("G$",$value)) {
		$value = substr($value,0,-1);
		$value = round($value*1073741824);
	} elseif(@eregi("M$",$value)) {
		$value = substr($value,0,-1);
		$value = round($value*1048576);
	} elseif(@eregi("K$",$value)) {
		$value = substr($value,0,-1);
		$value = round($value*1024);
	}

	return $value;
}
//------------------------------------------------------------------------------
function down_home($abs_dir) {			// dir deeper than home?
	if( nx_isFTPMode() ) {
		return true;
	}
	$real_home = @realpath($GLOBALS["home_dir"]);
	$real_dir = @realpath($abs_dir);

	if($real_home===false || $real_dir===false) {
		if(@eregi("\\.\\.",$abs_dir)) return false;
	} else if(strcmp($real_home,@substr($real_dir,0,strlen($real_home)))) {
		return false;
	}
	return true;
}
//------------------------------------------------------------------------------
function id_browser() {
	$browser=$GLOBALS['__SERVER']['HTTP_USER_AGENT'];

	if(ereg('Opera(/| )([0-9].[0-9]{1,2})', $browser)) {
		return 'OPERA';
	} else if(ereg('MSIE ([0-9].[0-9]{1,2})', $browser)) {
		return 'IE';
	} else if(ereg('OmniWeb/([0-9].[0-9]{1,2})', $browser)) {
		return 'OMNIWEB';
	} else if(ereg('(Konqueror/)(.*)', $browser)) {
		return 'KONQUEROR';
	} else if(ereg('Mozilla/([0-9].[0-9]{1,2})', $browser)) {
		return 'MOZILLA';
	} else {
		return 'OTHER';
	}
}
function nx_isArchive( $file ) {

	$file_info = pathinfo($file);
	$ext = @$file_info["extension"];
	if( $ext == "tar" || $ext == "gz" || $ext == "tgz" || $ext == "zip" || $ext == "bzip2"  || $ext == "bz2" || $ext == "tbz") {
	  return true;
	}
	return false;
}
if( !extension_loaded('posix') ) {
	function posix_geteuid() {
		return false;
	}
	function posix_getpwnam() {

	}
}

//------------------------------------------------------------------------------
/**
 * Checks if the User Agent String identifies the browser as Internet Explorer
 *
 * @return boolean
 */
function nx_isIE() {
	return (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT']));
}

if( !function_exists('mosToolTip')) {
	/**
	* Utility function to provide ToolTips
	* @param string ToolTip text
	* @param string Box title
	* @returns HTML code for ToolTip
	*/
	function mosToolTip( $tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='#', $link=1 ) {

		if ( $width ) {
			$width = ', WIDTH, \''.$width .'\'';
		}
		if ( $title ) {
			$title = ', CAPTION, \''.$title .'\'';
		}
		if ( !$text ) {
			$image 	= JURI::root() . '/administrator/components/com_osefileman/includes/js/ThemeOffice/'. $image;
			$text 	= '<img src="'. $image .'" border="0" />';
		}
		$style = 'style="text-decoration: none; color: #333;"';
		if ( $href ) {
			$style = '';
		}
		else{ $href = "#"; }

		if ( $link ) {
			$tip = "<a href=\"". $href ."\" onmouseover=\"return overlib('" . $tooltip . "'". $title .", BELOW, RIGHT". $width .");\" onmouseout=\"return nd();\" ". $style .">". $text ."</a>";
		} else {
			$tip = "<span onmouseover=\"return overlib('" . $tooltip . "'". $title .", BELOW, RIGHT". $width .");\" onmouseout=\"return nd();\" ". $style .">". $text ."</span>";
		}

		return $tip;
	}
}
/**
 * Prints an HTML dropdown box named $name using $arr to
 * load the drop down.  If $value is in $arr, then $value
 * will be the selected option in the dropdown.
 * @author gday
 * @author soeren
 *
 * @param string $name The name of the select element
 * @param string $value The pre-selected value
 * @param array $arr The array containting $key and $val
 * @param int $size The size of the select element
 * @param string $multiple use "multiple=\"multiple\" to have a multiple choice select list
 * @param string $extra More attributes when needed
 * @return string HTML drop-down list
 */
function nx_selectList($name, $value, $arr, $size=1, $multiple="", $extra="") {
	$html = '';
	if( !empty( $arr ) ) {
		$html = "<select class=\"inputbox\" name=\"$name\" id=\"$name\" size=\"$size\" $multiple $extra>\n";

		while (list($key, $val) = each($arr)) {
			$selected = "";
			if( is_array( $value )) {
				if( in_array( $key, $value )) {
					$selected = "selected=\"selected\"";
				}
			}
			else {
				if(strtolower($value) == strtolower($key) ) {
					$selected = "selected=\"selected\"";
				}
			}
			if( $val == '-') {
				//$selected .= ' disabled="disabled"';
				$val = '- - - - -';
			}
			$html .= "<option value=\"$key\" $selected>$val";
			$html .= "</option>\n";
		}

		$html .= "</select>\n";
	}
	return $html;
}
function nx_scriptTag( $src = '', $script = '') {
	if( $src!='') {
		return '<script type="text/javascript" src="'.$src.'"></script>';
	}
	if( $script != '') {
		return '<script type="text/javascript">'.$script.'</script>';
	}
}
function nx_alertBox( $msg ) {
	return nx_scriptTag('', 'alert( \''. @mysql_escape_string( $msg ) .'\' );' );
}
function nx_docLocation( $url ) {
	return nx_scriptTag('', 'document.location=\''. $url .'\';' );
}
function nx_isXHR() {
	return strtolower(JArrayHelper::getValue($_SERVER,'HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest'
		|| strtolower(JArrayHelper::getValue($_POST,'requestType')) == 'xmlhttprequest';
}
function nx_exit() {
$mainframe=&JFactory::getApplication();

	if( is_callable( array( $mainframe, 'close' ) ) ) {
		$mainframe->close();
	} else {
		session_write_close();
		exit;
	}
}
/**
 * Raise the memory limit when it is lower than the needed value
 *
 * @param string $setLimit Example: 16M
 */
function nx_RaiseMemoryLimit( $setLimit ) {

	$memLimit = @ini_get('memory_limit');

	if( stristr( $memLimit, 'k') ) {
		$memLimit = str_replace( 'k', '', str_replace( 'K', '', $memLimit )) * 1024;
	}
	elseif( stristr( $memLimit, 'm') ) {
		$memLimit = str_replace( 'm', '', str_replace( 'M', '', $memLimit )) * 1024 * 1024;
	}

	if( stristr( $setLimit, 'k') ) {
		$setLimitB = str_replace( 'k', '', str_replace( 'K', '', $setLimit )) * 1024;
	}
	elseif( stristr( $setLimit, 'm') ) {
		$setLimitB = str_replace( 'm', '', str_replace( 'M', '', $setLimit )) * 1024 * 1024;
	}

	if( $memLimit < $setLimitB ) {
		@ini_set('memory_limit', $setLimit );
	}
}
/**
 * Reads a file and sends them in chunks to the browser
 * This should overcome memory problems
 * http://www.php.net/manual/en/function.readfile.php#54295
 *
 * @since 1.4.1
 * @param string $filename
 * @param boolean $retbytes
 * @return mixed
 */
function readFileChunked($filename,$retbytes=true) {
	$chunksize = 1*(1024*1024); // how many bytes per chunk
	$buffer = '';
	$cnt =0;
	// $handle = fopen($filename, 'rb');
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		sleep(1);
		ob_flush();
		flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;
}
//implements file_put_contents function for compartability with mambo on PHP < 4.3
if ( ! function_exists('file_put_contents') ) {
	function file_put_contents ( $filename, $filecont ){
		$handle = fopen( $filename, 'w' );
		if ( is_array($filecont) ) {
			$size = 0;
			foreach ( $filecont as $filestring ) {
				fwrite( $handle, $filestring );
				$size += strlen( $filestring );
			}
			fclose($handle);
			return $size;
		} else {
			fwrite( $handle, $filecont );
			fclose($handle);
			return strlen( $filecont );
		}
	}
}
if ( ! function_exists('scandir') ) {
function scandir($dir,$listDirectories=false, $skipDots=true) {
    $dirArray = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (($file != "." && $file != "..") || $skipDots == true) {
                if($listDirectories == false) { if(is_dir($file)) { continue; } }
                array_push($dirArray,basename($file));
            }
        }
        closedir($handle);
    }
    return $dirArray;
}
}
?>
