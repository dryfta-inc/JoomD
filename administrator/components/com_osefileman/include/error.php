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

     The Original Code is error.php, released on 2003-02-21.

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
	Error Reporting File

	Have Fun...
------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function add_message($message, $type='general') {
	$_SESSION['nx_message'][$type][] = $message;
}
function empty_messages() {
	$_SESSION['nx_message'] = array();
}
function count_messages() {
	$count = 0;
	if( empty( $_SESSION['nx_message'] )) {
		return 0;
	}
	foreach( $_SESSION['nx_message'] as $type ) {
		if( !empty( $type ) && is_array( $type )) {
			$count += sizeof( $type );
		}
	}
	return $count;
}
function add_error($error, $type='general') {
	$_SESSION['nx_error'][$type][] = $error;
}
function empty_errors() {
	$_SESSION['nx_error'] = array();
}
function count_errors() {
	$count = 0;

	if( empty( $_SESSION['nx_error'] )) {
		return 0;
	}
	foreach( $_SESSION['nx_error'] as $type ) {
		if( !empty( $type ) && is_array( $type )) {
			$count += sizeof( $type );
		}
	}
	return $count;
}
function show_error($error,$extra=NULL) {		// show error-message
$mainframe=&JFactory::getApplication();

	$msg = $error;
	if($extra != NULL) {
		$msg .= " - ".$extra;
	}
	add_error( $msg );

	if( empty( $_GET['error'] )) {
		session_write_close();
		$mainframe->redirect( make_link("show_error", $GLOBALS["dir"]).'&error=1&extra='.urlencode( $extra ));
	}
	else {
		show_header($GLOBALS["error_msg"]["error"]);
		$errors = count_errors();
		$messages = count_messages();
		echo '<div class="quote">';
		if( $errors ) {
			echo '<a href="#errors">'. $errors.' '.$GLOBALS["error_msg"]["error"].'</a><br />';
		}
		if( $messages ) {
			echo '<a href="#messages">'.$messages .' '.$GLOBALS["error_msg"]["message"].'</a><br />';
		}
		echo "</div>\n";

		if( !empty( $_SESSION['nx_message'])) {
			echo "<a href=\"".str_replace('&dir=', '&ignore=', make_link("list", '' ))."\">[ "
					.$GLOBALS["error_msg"]["back"]." ]</a>";

			echo "<div class=\"nx_message\"><a name=\"messages\"></a>
					<h3>".$GLOBALS["error_msg"]["message"].":</strong>"."</h3>\n";

			foreach ( $_SESSION['nx_message'] as $msgtype ) {
				foreach ( $msgtype as $message ) {
					echo $message ."\n<br/>";
				}
				echo '<br /><hr /><br />';
			}
			empty_messages();

			if( !empty( $_REQUEST['extra'])) echo " - ".htmlspecialchars(urldecode($_REQUEST['extra']), ENT_QUOTES );
			echo "</div>\n";
		}

		if( !empty($_SESSION['nx_error'] )) {
			echo "<div class=\"nx_error\"><a name=\"errors\"></a>
				<h3>".$GLOBALS["error_msg"]["error"].":</strong>"."</h3>\n";

			foreach ( $_SESSION['nx_error'] as $errortype ) {
				foreach ( $errortype as $error ) {
					echo $error ."\n<br/>";
				}
				echo '<br /><hr /><br />';
			}
			empty_errors();
		}
		echo "<a href=\"".str_replace('&dir=', '&ignore=', make_link("list", '' ))."\">".$GLOBALS["error_msg"]["back"]."</a>";
		if( !empty( $_REQUEST['extra'])) echo " - ".htmlspecialchars(urldecode($_REQUEST['extra']), ENT_QUOTES );
		echo "</div>\n";
		defined('JXPLORER_NOEXEC') or define('JXPLORER_NOEXEC', 1 );
	}
}
//------------------------------------------------------------------------------
?>
