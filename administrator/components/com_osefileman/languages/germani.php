<?php

// German Language Module for joomlaXplorer (translated by the QuiX project)
$_VERSION = new JVersion(); 

$GLOBALS["charset"] = "iso-8859-1";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "d.m.Y H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "Fehler",
	"back"			=> "zur&uuml;ck",
	
	// root
	"home"			=> "Das Home-Verzeichnis existiert nicht, kontrollieren sie ihre Einstellungen.",
	"abovehome"		=> "Das aktuelle Verzeichnis darf nicht h&ouml;her liegen als das Home-Verzeichnis.",
	"targetabovehome"	=> "Das Zielverzeichnis darf nicht h&ouml;her liegen als das Home-Verzeichnis.",
	
	// exist
	"direxist"		=> "Dieses Verzeichnis existiert nicht.",
	//"filedoesexist"	=> "Diese Datei existiert bereits.",
	"fileexist"		=> "Diese Datei existiert nicht.",
	"itemdoesexist"		=> "Dieses Objekt existiert bereits.",
	"itemexist"		=> "Dieses Objekt existiert nicht.",
	"targetexist"		=> "Das Zielverzeichnis existiert nicht.",
	"targetdoesexist"	=> "Das Zielobjekt existiert bereits.",
	
	// open
	"opendir"		=> "Kann Verzeichnis nicht &ouml;ffnen.",
	"readdir"		=> "Kann Verzeichnis nicht lesen",
	
	// access
	"accessdir"		=> "Zugriff auf dieses Verzeichnis verweigert.",
	"accessfile"		=> "Zugriff auf diese Datei verweigert.",
	"accessitem"		=> "Zugriff auf dieses Objekt verweigert.",
	"accessfunc"		=> "Zugriff auf diese Funktion verweigert.",
	"accesstarget"		=> "Zugriff auf das Zielverzeichnis verweigert.",
	
	// actions
	"permread"		=> "Rechte lesen fehlgeschlagen.",
	"permchange"		=> "Rechte &auml;ndern fehlgeschlagen.",
	"openfile"		=> "Datei &ouml;ffnen fehlgeschlagen.",
	"savefile"		=> "Datei speichern fehlgeschlagen.",
	"createfile"		=> "Datei anlegen fehlgeschlagen.",
	"createdir"		=> "Verzeichnis anlegen fehlgeschlagen.",
	"uploadfile"		=> "Datei hochladen fehlgeschlagen.",
	"copyitem"		=> "kopieren fehlgeschlagen.",
	"moveitem"		=> "verschieben fehlgeschlagen.",
	"delitem"		=> "l&ouml;schen fehlgeschlagen.",
	"chpass"		=> "Passwort &auml;ndern fehlgeschlagen.",
	"deluser"		=> "Benutzer l&ouml;schen fehlgeschlagen.",
	"adduser"		=> "Benutzer hinzuf&uuml;gen fehlgeschlagen.",
	"saveuser"		=> "Benutzer speichern fehlgeschlagen.",
	"searchnothing"		=> "Sie m&uuml;ssen etwas zum suchen eintragen.",
	
	// misc
	"miscnofunc"		=> "Funktion nicht vorhanden.",
	"miscfilesize"		=> "Datei ist gr&ouml;&szlig;er als die maximale Gr&ouml;&szlig;e.",
	"miscfilepart"		=> "Datei wurde nur zum Teil hochgeladen.",
	"miscnoname"		=> "Sie m&uuml;ssen einen Namen eintragen",
	"miscselitems"		=> "Sie haben keine Objekt(e) ausgew&auml;hlt.",
	"miscdelitems"		=> "Sollen die \"+num+\" markierten Objekt(e) gel&ouml;scht werden?",
	"miscdeluser"		=> "Soll der Benutzer '\"+user+\"' gel&ouml;scht werden?",
	"miscnopassdiff"	=> "Das neue und das heutige Passwort sind nicht verschieden.",
	"miscnopassmatch"	=> "Passw&ouml;rter sind nicht gleich.",
	"miscfieldmissed"	=> "Sie haben ein wichtiges Eingabefeld vergessen auszuf&uuml;llen",
	"miscnouserpass"	=> "Benutzer oder Passwort unbekannt.",
	"miscselfremove"	=> "Sie k&ouml;nnen sich selbst nicht l&ouml;schen.",
	"miscuserexist"		=> "Der Benutzer existiert bereits.",
	"miscnofinduser"	=> "Kann Benutzer nicht finden.",
	"extract_noarchive" 	=> "Dieses Datei ist leider kein Archiv.",
	"extract_unknowntype" 	=> "unbekannter Archivtyp!"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "Rechte &auml;ndern",
	"editlink"		=> "bearbeiten",
	"downlink"		=> "herunterladen",
	"uplink"		=> "H&ouml;her",
	"homelink"		=> "Startseite",
	"reloadlink"		=> "aktualisieren",
	"copylink"		=> "kopieren",
	"movelink"		=> "verschieben",
	"dellink"		=> "l&ouml;schen",
	"comprlink"		=> "archivieren",
	"adminlink"		=> "Administration",
	"logoutlink"		=> "abmelden",
	"uploadlink"		=> "hochladen",
	"searchlink"		=> "suchen",
	"extractlink"		=> "entpacken",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> 'Rechte (chmod) &auml;ndern (Verzeichnisse)/Datei(en))', // new mic
	'mossysinfolink'	=> $_VERSION->PRODUCT.' System Informationen ('.$_VERSION->PRODUCT.', Server, PHP, mySQL)', // new mic
	'logolink'		=> 'Gehe zur joomlaXplorer Webseite (neues Fenster)', // new mic
	
	// list
	"nameheader"		=> "Name",
	"sizeheader"		=> "Gr&ouml;&szlig;e",
	"typeheader"		=> "Typ",
	"modifheader"		=> "Ge&auml;ndert",
	"permheader"		=> "Rechte",
	"actionheader"		=> "Aktionen",
	"pathheader"		=> "Pfad",
	
	// buttons
	"btncancel"		=> "abbrechen",
	"btnsave"		=> "speichern",
	"btnchange"		=> "&auml;ndern",
	"btnreset"		=> "zur&uuml;cksetzen",
	"btnclose"		=> "schlie&szlig;en",
	"btncreate"		=> "anlegen",
	"btnsearch"		=> "suchen",
	"btnupload"		=> "hochladen",
	"btncopy"		=> "kopieren",
	"btnmove"		=> "verschieben",
	"btnlogin"		=> "anmelden",
	"btnlogout"		=> "abmelden",
	"btnadd"		=> "hinzuf&uuml;gen",
	"btnedit"		=> "&auml;ndern",
	"btnremove"		=> "l&ouml;schen",
	
	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'		=> 'umbenennen',
	'confirm_delete_file' 	=> 'Are you sure you want to delete this file? \\n%s',
	'success_delete_file' 	=> 'Item(s) successfully deleted.',
	'success_rename_file' 	=> 'The directory/file %s was successfully renamed to %s.',
	
	
	// actions
	"actdir"		=> "Verzeichnis",
	"actperms"		=> "Rechte &auml;ndern",
	"actedit"		=> "Datei bearbeiten",
	"actsearchresults"	=> "Suchergebnisse",
	"actcopyitems"		=> "Objekt(e) kopieren",
	"actcopyfrom"		=> "kopiere von /%s nach /%s ",
	"actmoveitems"		=> "Objekt(e) verschieben",
	"actmovefrom"		=> "verschiebe von /%s nach /%s ",
	"actlogin"		=> "anmelden",
	"actloginheader"	=> "Melden sie sich an um QuiXplorer zu benutzen",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "Passwort &auml;ndern",
	"actusers"		=> "Benutzer",
	"actarchive"		=> "Objekt(e) archivieren",
	"actupload"		=> "Datei(en) hochladen",
	
	// misc
	"miscitems"		=> "Objekt(e)",
	"miscfree"		=> "Freier Speicher",
	"miscusername"		=> "Benutzername",
	"miscpassword"		=> "Passwort",
	"miscoldpass"		=> "Altes Passwort",
	"miscnewpass"		=> "Neues Passwort",
	"miscconfpass"		=> "Best&auml;tige Passwort",
	"miscconfnewpass"	=> "Best&auml;tige neues Passwort",
	"miscchpass"		=> "&Auml;ndere Passwort",
	"mischomedir"		=> "Home-Verzeichnis",
	"mischomeurl"		=> "Home URL",
	"miscshowhidden"	=> "Versteckte Objekte anzeigen",
	"mischidepattern"	=> "Versteck-Filter",
	"miscperms"		=> "Rechte",
	"miscuseritems"		=> "(Name, Home-Verzeichnis, versteckte Objekte anzeigen, Rechte, aktiviert)",
	"miscadduser"		=> "Benutzer hinzuf&uuml;gen",
	"miscedituser"		=> "Benutzer '%s' &auml;ndern",
	"miscactive"		=> "Aktiviert",
	"misclang"		=> "Sprache",
	"miscnoresult"		=> "Suche ergebnislos.",
	"miscsubdirs"		=> "Suche in Unterverzeichnisse",
	"miscpermnames"		=> array("Nur ansehen","&Auml;ndern","Passwort &auml;ndern","&Auml;ndern & Passwort &auml;ndern","Administrator"),
	"miscyesno"		=> array("Ja","Nein","J","N"),
	"miscchmod"		=> array("Besitzer", "Gruppe", "Publik"),
	
	'miscowner'		=> 'Inhaber',
	'miscownerdesc'		=> '<strong>Erkl&auml;rung:</strong><br />Benutzer (UID) /<br />Gruppe (GID)<br />Aktuelle Besitzerrechte:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>', // new mic

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT.' System Info',
	'sisysteminfo'		=> 'System Info',
	'sibuilton'		=> 'Betriebssystem',
	'sidbversion'		=> 'Datenbankversion (MySQL)',
	'siphpversion'		=> 'PHP Version',
	'siphpupdate'		=> 'HINWEIS: <font style="color: red;">Die verwendete PHP Version ist <strong>nicht</strong> aktuell!</font><br />Um ein ordnungsgem&auml;sses Funktionieren von '.$_VERSION->PRODUCT.' bzw. dessen Erweiterungen zu gew&auml;hrleisten,<br />sollte mindestens <strong>PHP.Version 4.3</strong> eingesetzt werden!',
	'siwebserver'		=> 'Webserver',
	'siwebsphpif'		=> 'WebServer - PHP Schnittstelle',
	'simamboversion'	=> $_VERSION->PRODUCT.' Version',
	'siuseragent'		=> 'Browserversion',
	'sirelevantsettings'	=> 'Wichtige PHP Einstellungen',
	'sisafemode'		=> 'Safe Mode',
	'sibasedir'		=> 'Open basedir',
	'sidisplayerrors'	=> 'PHP Fehleranzeige',
	'sishortopentags'	=> 'Short Open Tags',
	'sifileuploads'		=> 'Datei Uploads',
	'simagicquotes'		=> 'Magic Quotes',
	'siregglobals'		=> 'Register Globals',
	'sioutputbuf'		=> 'Ausgabebuffer',
	'sisesssavepath'	=> 'Session Sicherungspfad',
	'sisessautostart'	=> 'Session auto start',
	'sixmlenabled'		=> 'XML aktiviert',
	'sizlibenabled'		=> 'ZLIB aktiviert',
	'sidisabledfuncs'	=> 'Nicht aktivierte Funktionen',
	'sieditor'		=> 'WYSIWYG Bearbeitung (Editor)',
	'siconfigfile'		=> 'Konfigurationsdatei',
	'siphpinfo'		=> 'PHP Info',
	'siphpinformation'	=> 'PHP Information',
	'sipermissions'		=> 'Rechte',
	'sidirperms'		=> 'Verzeichnisrechte',
	'sidirpermsmess'	=> 'Damit alle Funktionen und Zus&auml;tze einwandfrei arbeiten k&ouml;nnen, sollten folgende Verzeichnisse Schreibrechte [chmod 0777] besitzen',
	'sionoff'		=> array( 'Ein', 'Aus' ),
	
	'extract_warning' 	=> "Soll dieses Datei wirklich entpackt werden? Hier?\\nBeim Entpacken werden evtl. vorhandene Dateien &uuml;berschrieben!",
	'extract_success' 	=> "Das Entpacken des Archivs war erfolgreich.",
	'extract_failure' 	=> "Das Entpacken des Archivs ist fehlgeschlagen.",
	
	'overwrite_files' 	=> 'vorhandene Datei(en) &uuml;berschreiben?',
	"viewlink"		=> "anzeigen",
	"actview"		=> "Zeige Quelltext der Datei",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Auch Unterverzeichnisse mit einbeziehen?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'		=> 'Pr&uuml;fe auf aktuellste Version',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'		=>	'Umbenennen eines Verzeichnisses oder einer Datei...',
	'newname'		=>	'Neuer Name',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'		=>	'zur&uuml;ck zum Verzeichnis nach dem Speichern?',
	'line'			=> 	'Zeile',
	'column'		=>	'Spalte',
	'wordwrap'		=>	'Zeilenumbruch: (IE only)',
	'copyfile'		=>	'Kopiere diese Datei zu folgendem Dateinamen',
	
	// Bookmarks
	'quick_jump'		=> 'Springe zu',
	'already_bookmarked' 	=> 'Dieses Verzeichnis ist bereits als Lesezeichen eingetragen.',
	'bookmark_was_added' 	=> 'Das Verzeichnis wurde als Lesezeichen hinzugef&uuml;gt.',
	'not_a_bookmark' 	=> 'Dieses Verzeichnis ist kein Lesezeichen und kann nicht entfernt werden.',
	'bookmark_was_removed' 	=> 'Das Verzeichnis wurde von der Liste der Lesezeichen entfernt.',
	'bookmarkfile_not_writable' => "Die Aktion (%) ist fehlgeschlagen. Die Lesezeichendatei '%s' \nist nicht beschreibbar.",
	
	'lbl_add_bookmark' 	=> 'F&uuml;ge dieses Verzeichnis als Lesezeichen hinzu',
	'lbl_remove_bookmark' 	=> 'Entferne dieses Verzeichnis von der Liste der Lesezeichen',
	
	'enter_alias_name' 	=> 'Bitte gib einen Aliasnamen f&uuml;r dieses Lesezeichen an',
	
	'normal_compression' 	=> 'normale Kompression, schnell',
	'good_compression' 	=> 'gute Kompression, CPU-intensiv',
	'best_compression' 	=> 'beste Kompression, CPU-intensiv',
	'no_compression' 	=> 'keine Kompression, sehr schnell',
	
	'creating_archive' 	=> 'Das Archiv wird erstellt...',
	'processed_x_files' 	=> 'Es wurden %s von %s Dateien bearbeitet',
	
	'ftp_login_lbl' 	=> 'Please enter the login credentials for the FTP server',
	'ftp_login_name' 	=> 'FTP Benutzername',
	'ftp_login_pass' 	=> 'FTP Passwort',
	'ftp_hostname_port' 	=> 'FTP Hostname und Port <br />(Port ist optional)',
	'ftp_login_check' 	=> '&Uuml;berpr&uuml;fe die FTP Verbindung...',
	'ftp_connection_failed' => "Der FTP Server konnte nicht erreicht werden. \nBitte &uuml;berpr&uuml;fen Sie, da&szlig; der FTP Server auf ihrem System l&auml;ft.",
	'ftp_login_failed' 	=> "Anmeldung am FTP Server fehlgeschlagen. Bitte &uuml;berpr&uuml;fen Sie Benutzername und Passwort und versuchen es nochmal.",
	
	'switch_file_mode' 	=> 'Aktueller Modus: <strong>%s</strong>. Modus wechseln zu: %s.',
	'symlink_target' 	=> 'Ziel des symbolischen Links',
	
	"permchange"		=> "CHMOD Erfolg:",
	"savefile"		=> "Die Datei wurde gespeichert.",
	"moveitem"		=> "Das Verschieben war erfolgreich.",
	"copyitem"		=> "Das Kopieren war erfolgreich.",
	'archive_name' 	=> 'Name des Archivs',
	'archive_saveToDir' 	=> 'Speichere das Archiv in foldendem Verzeichnis',
	
	'editor_simple'	=> 'Einfacher Editormodus',
	'editor_syntaxhighlight'	=> 'Syntax-Hervorhebungsmodus'
);
?>
