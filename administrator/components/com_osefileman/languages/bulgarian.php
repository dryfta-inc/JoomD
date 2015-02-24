<?php

// Bulgarian Language Module for v2.3 (translated by the Ivo Apostolov)
$_VERSION = new JVersion(); 

$GLOBALS["charset"] = "windows-1251";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "������",
	"back"			=> "�����",

	// root
	"home"			=> "���� ������ ����������.",
	"abovehome"		=> "���������� ���������� �� ���� �� ���� ��� ���������.",
	"targetabovehome"	=> "��������� ���������� �� ���� �� ���� ��� ���������.",

	// exist
	"direxist"		=> "������������ �� ����������.",
	//"filedoesexist"	=> "This file already exists.",
	"fileexist"		=> "����� �� ����������.",
	"itemdoesexist"		=> "���� ��� ����� �����.",
	"itemexist"		=> "������ �� ����������.",
	"targetexist"		=> "������������ �� ����������.",
	"targetdoesexist"	=> "�������� ����� ���� ����������.",

	// open
	"opendir"		=> "�� � �������� �� ���� �������� ������������.",
	"readdir"		=> "�� � �������� �� ���� ��������� ������������.",

	// access
	"accessdir"		=> "������ ������ �� ���� ����������.",
	"accessfile"		=> "������ ������ �� ���� ����.",
	"accessitem"		=> "������ ������ �� ���� �����.",
	"accessfunc"		=> "������ ������ �� ���������� ���� �������.",
	"accesstarget"		=> "������ ������ �� ��������� ����������.",

	// actions
	"permread"		=> "������ ��� ������� �� �������.",
	"permchange"	=> "������ ��� ����� �� �������.",
	"openfile"		=> "������ ��� ���������� �� �����.",
	"savefile"		=> "������ ��� ������ �� �����.",
	"createfile"	=> "������ ��� ����������� �� ����.",
	"createdir"		=> "������ ��� ����������� �� ����������.",
	"uploadfile"	=> "������ ��� ��������� �� ����.",
	"copyitem"		=> "������ ��� ��������.",
	"moveitem"		=> "������ ��� �����������.",
	"delitem"		=> "������ ��� ���������.",
	"chpass"		=> "������ ��� ����� �� ������.",
	"deluser"		=> "������ ��� ��������� �� ����������.",
	"adduser"		=> "������ ��� �������� �� ����������.",
	"saveuser"		=> "������ ��� ������ �� ����������.",
	"searchnothing"	=> "�������� �������� �� �������.",

	// misc
	"miscnofunc"		=> "��������� �� � �������.",
	"miscfilesize"		=> "������ � ��� ����������� ����.",
	"miscfilepart"		=> "������ �� ����� ��������.",
	"miscnoname"		=> "������ �� �������� ���.",
	"miscselitems"		=> "�� ��� ������� ����.",
	"miscdelitems"		=> "������� �� ��� � ����������� �� ���� \"+num+\" ������?",
	"miscdeluser"		=> "������� �� ��� � ����������� �� ����������� '\"+user+\"'?",
	"miscnopassdiff"	=> "������ ������ �� �� ��������� �� �������.",
	"miscnopassmatch"	=> "�������� �� ��������.",
	"miscfieldmissed"	=> "���������� ��� ������������ ����.",
	"miscnouserpass"	=> "��������������� ��� ��� �������� �� ������.",
	"miscselfremove"	=> "�� ������ �� �������� ���� ��.",
	"miscuserexist"		=> "���� ��� ����� ����������.",
	"miscnofinduser"	=> "�� ���� �� ���� ������ ����������.",
	"extract_noarchive" => "������ �� ���� �� ���� ������������.",
	"extract_unknowntype" => "���������� ��� �� ������"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "����� �� �����",
	"editlink"		=> "��������",
	"downlink"		=> "�������",
	"uplink"		=> "������",
	"homelink"		=> "������",
	"reloadlink"		=> "������������",
	"copylink"		=> "��������",
	"movelink"		=> "�����������",
	"dellink"		=> "���������",
	"comprlink"		=> "����������",
	"adminlink"		=> "��������������",
	"logoutlink"		=> "�����",
	"uploadlink"		=> "�������",
	"searchlink"		=> "�������",
	"extractlink"	=> "�������������",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> '����� �� �������', // new mic
	'mossysinfolink'	=> $_VERSION->PRODUCT.' �������� ���������� ('.$_VERSION->PRODUCT.', ������, PHP, MySQL)', // new mic
	'logolink'		=> '������� � ����� �� joomlaXplorer', // new mic

	// list
	"nameheader"		=> "���",
	"sizeheader"		=> "������",
	"typeheader"		=> "���",
	"modifheader"		=> "�����������",
	"permheader"		=> "�����",
	"actionheader"		=> "��������",
	"pathheader"		=> "���",

	// buttons
	"btncancel"		=> "�����",
	"btnsave"		=> "�����",
	"btnchange"		=> "�������",
	"btnreset"		=> "�������",
	"btnclose"		=> "�������",
	"btncreate"		=> "������",
	"btnsearch"		=> "�����",
	"btnupload"		=> "����",
	"btncopy"		=> "�������",
	"btnmove"		=> "��������",
	"btnlogin"		=> "����",
	"btnlogout"		=> "�����",
	"btnadd"		=> "������",
	"btnedit"		=> "����������",
	"btnremove"		=> "������",
	
	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'	=> '�����������',
	'confirm_delete_file' => '������� �� ��� � ����������� �� ���� ����? \\n%s',
	'success_delete_file' => '������ �� ������� �������.',
	'success_rename_file' => '������������/����� %s �� ������������ �� %s.',
	
	// actions
	"actdir"		=> "����������",
	"actperms"		=> "����� �� �������",
	"actedit"		=> "�������� �� ����",
	"actsearchresults"	=> "��������� �� �������",
	"actcopyitems"		=> "�������� �� ������",
	"actcopyfrom"		=> "������� �� /%s � /%s ",
	"actmoveitems"		=> "����������� �� ������",
	"actmovefrom"		=> "�������� �� /%s � /%s ",
	"actlogin"		=> "����",
	"actloginheader"	=> "���� �� ���������� �� �������� �������",
	"actadmin"		=> "�������������",
	"actchpwd"		=> "����� �� ������",
	"actusers"		=> "�����������",
	"actarchive"		=> "���������� �� ��������",
	"actupload"		=> "������� �� �������",

	// misc
	"miscitems"		=> "������",
	"miscfree"		=> "��������",
	"miscusername"		=> "����������",
	"miscpassword"		=> "������",
	"miscoldpass"		=> "����� ������",
	"miscnewpass"		=> "���� ������",
	"miscconfpass"		=> "�������� ������",
	"miscconfnewpass"	=> "�������� ������ ������",
	"miscchpass"		=> "����� ������",
	"mischomedir"		=> "������� ����������",
	"mischomeurl"		=> "������� �����",
	"miscshowhidden"	=> "������ �������� ������",
	"mischidepattern"	=> "����� ���������",
	"miscperms"		=> "�����",
	"miscuseritems"		=> "(���, ������� ����������, ������ �������� ������, �����, ��������)",
	"miscadduser"		=> "������ ����������",
	"miscedituser"		=> "���������� ����������� '%s'",
	"miscactive"		=> "���������",
	"misclang"		=> "����",
	"miscnoresult"		=> "���� ���������.",
	"miscsubdirs"		=> "������� � ���������������",
	"miscpermnames"		=> array("���� �������","������������","����� �� ������","������������ & ����� �� ������",
					"�������������"),
	"miscyesno"		=> array("��","��","�","�"),
	"miscchmod"		=> array("����������", "�����", "����������"),

	// from here all new by mic
	'miscowner'			=> '����������',
	'miscownerdesc'		=> '<strong>��������:</strong><br />��������� (UID) /<br />����� (GID)<br />�������� �����:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT." ������� ����������",
	'sisysteminfo'		=> '�������� ����������',
	'sibuilton'			=> '����������� �������',
	'sidbversion'		=> '������ �� MySQL',
	'siphpversion'		=> '������ �� PHP',
	'siphpupdate'		=> '����������: <span style="color: red;">�������� �� PHP ����� �������� <strong>�� �</strong> ��������!</span><br />�� �� ����������� ������ �������������� �� Joomla! ������ �� ��������,<br /> ������� <strong>������ �� PHP 4.3</strong>!',
	'siwebserver'		=> '��� ������',
	'siwebsphpif'		=> '��� ������ - PHP ���������',
	'simamboversion'	=> $_VERSION->PRODUCT.' ������',
	'siuseragent'		=> '������ �� ��������',
	'sirelevantsettings' => '����� PHP ���������',
	'sisafemode'		=> '������� �����',
	'sibasedir'			=> '�������� ������� ����������',
	'sidisplayerrors'	=> 'PHP ������',
	'sishortopentags'	=> '���� �������� �������',
	'sifileuploads'		=> '������� �� �������',
	'simagicquotes'		=> '��������� ������',
	'siregglobals'		=> '������������ �� ��������',
	'sioutputbuf'		=> '������� �����',
	'sisesssavepath'	=> '����� �� ���� �� �������',
	'sisessautostart'	=> '����������� ��������� �� �������',
	'sixmlenabled'		=> '��������� �� XML',
	'sizlibenabled'		=> '��������� �� ZLIB',
	'sidisabledfuncs'	=> '��������� �������',
	'sieditor'			=> 'WYSIWYG ��������',
	'siconfigfile'		=> '���� � ���������',
	'siphpinfo'			=> 'PHP ����������',
	'siphpinformation'	=> 'PHP ����������',
	'sipermissions'		=> '�����',
	'sidirperms'		=> '����� ����� ����������',
	'sidirpermsmess'	=> '�� �� ��� �������, �� ������ ������� �� '.$_VERSION->PRODUCT.' ������� ��������, �������� ���������� ������ �� �� � ����� [chmod 0777]',
	'sionoff'			=> array( '�������', '��������' ),
	
	'extract_warning' => "������� �� ��� � ��������������� �� ���� ����? ���?\\n���������� �� ����������� ���� ������� ����� ���������, ��� ������� �� ��������!",
	'extract_success' => "�������������� � �������",
	'extract_failure' => "������ ��� ���������������",
	
	'overwrite_files' => 'Overwrite existing file(s)?',
	"viewlink"		=> "VIEW",
	"actview"		=> "Showing source of file",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Recurse into subdirectories?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'	=> 'Check for latest version',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'	=>	'Rename a directory or file...',
	'newname'		=>	'New Name',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'	=>	'Return to directory after saving?',
	'line'		=> 	'Line',
	'column'	=>	'Column',
	'wordwrap'	=>	'Wordwrap: (IE only)',
	'copyfile'	=>	'Copy file into this filename',
	
	// Bookmarks
	'quick_jump' => 'Quick Jump To',
	'already_bookmarked' => 'This directory is already bookmarked',
	'bookmark_was_added' => 'This directory was added to the bookmark list.',
	'not_a_bookmark' => 'This directory is not a bookmark.',
	'bookmark_was_removed' => 'This directory was removed from the bookmark list.',
	'bookmarkfile_not_writable' => "Failed to %s the bookmark.\n The Bookmark File '%s' \nis not writable.",
	
	'lbl_add_bookmark' => 'Add this Directory as Bookmark',
	'lbl_remove_bookmark' => 'Remove this Directory from the Bookmark List',
	
	'enter_alias_name' => 'Please enter the alias name for this bookmark',
	
	'normal_compression' => 'normal compression',
	'good_compression' => 'good compression',
	'best_compression' => 'best compression',
	'no_compression' => 'no compression',
	
	'creating_archive' => 'Creating Archive File...',
	'processed_x_files' => 'Processed %s of %s Files',
	
	'ftp_header' => 'Local FTP Authentication',
	'ftp_login_lbl' => 'Please enter the login credentials for the FTP server',
	'ftp_login_name' => 'FTP User Name',
	'ftp_login_pass' => 'FTP Password',
	'ftp_hostname_port' => 'FTP Server Hostname and Port <br />(Port is optional)',
	'ftp_login_check' => 'Checking FTP connection...',
	'ftp_connection_failed' => "The FTP server could not be contacted. \nPlease check that the FTP server is running on your server.",
	'ftp_login_failed' => "The FTP login failed. Please check the username and password and try again.",
		
	'switch_file_mode' => 'Current mode: <strong>%s</strong>. You could switch to %s mode.',
	'symlink_target' => 'Target of the Symbolic Link',
	
	"permchange"		=> "CHMOD Success:",
	"savefile"		=> "The File was saved.",
	"moveitem"		=> "Moving succeeded.",
	"copyitem"		=> "Copying succeeded.",
	'archive_name' 	=> 'Name of the Archive File',
	'archive_saveToDir' 	=> 'Save the Archive in this directory',
	
	'editor_simple'	=> 'Simple Editor Mode',
	'editor_syntaxhighlight'	=> 'Syntax-Highlighted Mode'
);
?>
