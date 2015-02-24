<?php

// Italiano Language Module for v2.3 (translated by the TTi joomla.it)
$_VERSION = new JVersion(); 

$GLOBALS["charset"] = "utf-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "ERRORE(I)",
	"back"			=> "Indietro",

	// root
	"home"			=> "La cartella principale non esiste, controllare la configurazione.",
	"abovehome"		=> "Questa cartella non pu&#242; essere fuori dalla cartella principale.",
	"targetabovehome"	=> "La cartella di destinazione non pu&#242; risiedere fuori dalla cartella principale.",

	// exist
	"direxist"		=> "Questa cartella non esiste.",
	//"filedoesexist"	=> "Questo file esiste gi&#224;.",
	"fileexist"		=> "Questo file non esiste.",
	"itemdoesexist"		=> "Questo elemento esiste gi&#224;.",
	"itemexist"		=> "Questo elemento non esiste.",
	"targetexist"		=> "La cartella di destinazione non esiste.",
	"targetdoesexist"	=> "Elemento di destinazione esiste gi&#224;.",

	// open
	"opendir"		=> "Impossibile aprire la cartella.",
	"readdir"		=> "Impossibile leggere nella cartella.",

	// access
	"accessdir"		=> "Non sei autorizzato ad accedere a questa cartella.",
	"accessfile"		=> "Non sei autorizzato ad accedere a questo file.",
	"accessitem"		=> "Non sei autorizzato ad accedere a questo elemento.",
	"accessfunc"		=> "Non sei autorizzato ad utilizzare questa funzione.",
	"accesstarget"		=> "Non sei autorizzato ad accedere alla cartella di destinazione.",

	// actions
	"permread"		=> "Richiesta permessi fallita.",
	"permchange"		=> "Modifica permessi fallita.",
	"openfile"		=> "Apertura del file fallita.",
	"savefile"		=> "Salvataggio del file fallito.",
	"createfile"		=> "Creazione del file fallita.",
	"createdir"		=> "Creazione della cartella fallita.",
	"uploadfile"		=> "Caricamento del file fallito.",
	"copyitem"		=> "Copia fallita.",
	"moveitem"		=> "Spostamento fallito.",
	"delitem"		=> "Rimozione fallita.",
	"chpass"		=> "Modifica della password fallita.",
	"deluser"		=> "Rimozione dell&#180;utente fallita.",
	"adduser"		=> "Inserimento dell&#180;utente fallito.",
	"saveuser"		=> "Salvataggio dell&#180;utente fallito.",
	"searchnothing"		=> "&#200; necessario impostare un criterio di ricerca.",

	// misc
	"miscnofunc"		=> "Funzione non disponibile.",
	"miscfilesize"		=> "Il file supera le dimensioni massime.",
	"miscfilepart"		=> "File caricato solo parzialmente.",
	"miscnoname"		=> "Necessario inserire un nome.",
	"miscselitems"		=> "Non è stato selezionato un elemento(i).",
	"miscdelitems"		=> "Siamo sicuri di voler rimuovere questi \"+num+\" elemento(i)?",
	"miscdeluser"		=> "Siamo sicuri di voler rimuovere questo utente '\"+user+\"'?",
	"miscnopassdiff"	=> "Nuova password identica a quella in uso.",
	"miscnopassmatch"	=> "Le password non coincidono.",
	"miscfieldmissed"	=> "Non impostato un campo importante.",
	"miscnouserpass"	=> "Utente o password errati.",
	"miscselfremove"	=> "Impossibile rimuovere la propria utenza.",
	"miscuserexist"		=> "Utente già esistente.",
	"miscnofinduser"	=> "Impossibile trovare questo utente.",
	"extract_noarchive" => "Il file non è un file archivio estraibile.",
	"extract_unknowntype" => "Tipo archivio sconosciuto"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "Modifica dei permessi",
	"editlink"		=> "Modifica",
	"downlink"		=> "Scarica",
	"uplink"		=> "Precedente",
	"homelink"		=> "Pagina Principale",
	"reloadlink"		=> "Ricarica",
	"copylink"		=> "Copia",
	"movelink"		=> "Sposta",
	"dellink"		=> "Cancella",
	"comprlink"		=> "Archivia",
	"adminlink"		=> "Amministra",
	"logoutlink"		=> "Esci",
	"uploadlink"		=> "Carica",
	"searchlink"		=> "Cerca",
	"extractlink"	=> "Estrai archivio",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> 'Modifica (chmod) Diritti (Cartella/File)', // new mic
	'mossysinfolink'	=> $_VERSION->PRODUCT.' Informazioni di sistema ('.$_VERSION->PRODUCT.', Server, PHP, mySQL)', // new mic
	'logolink'		=> 'Visita il sito web ufficiale joomlaXplorer (nuova finestra)', // new mic

	// list
	"nameheader"		=> "Nome",
	"sizeheader"		=> "Dimensione",
	"typeheader"		=> "Tipo",
	"modifheader"		=> "Modificato",
	"permheader"		=> "Permessi",
	"actionheader"		=> "Azioni",
	"pathheader"		=> "Percorso",

	// buttons
	"btncancel"		=> "Annulla",
	"btnsave"		=> "Salva",
	"btnchange"		=> "Modifica",
	"btnreset"		=> "Resetta",
	"btnclose"		=> "Chiudi",
	"btncreate"		=> "Crea",
	"btnsearch"		=> "Cerca",
	"btnupload"		=> "Carica",
	"btncopy"		=> "Copia",
	"btnmove"		=> "Sposta",
	"btnlogin"		=> "Entra",
	"btnlogout"		=> "Esci",
	"btnadd"		=> "Aggiungi",
	"btnedit"		=> "Modifica",
	"btnremove"		=> "Rimuovi",

	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'	=> 'Rinomina',
	'confirm_delete_file' => 'Sei certo di voler cancellare questo file? \\n%s',
	'success_delete_file' => 'Elemento(i) correttamente cancellato.',
	'success_rename_file' => 'Cartella/file %s rinomina correttamente in %s.',

	// actions
	"actdir"		=> "Cartella",
	"actperms"		=> "Modifica permessi",
	"actedit"		=> "Modifica file",
	"actsearchresults"	=> "Risultati della ricerca",
	"actcopyitems"		=> "Copia elemento(i)",
	"actcopyfrom"		=> "Copia da /%s a /%s ",
	"actmoveitems"		=> "Sposta elemento(i)",
	"actmovefrom"		=> "Sposta da /%s a /%s ",
	"actlogin"		=> "Entra",
	"actloginheader"	=> "Entra per utilizzare QuiXplorer",
	"actadmin"		=> "Amministrazione",
	"actchpwd"		=> "Modifica password",
	"actusers"		=> "Utenti",
	"actarchive"		=> "Archivio elemento(i)",
	"actupload"		=> "Caricamento file(s)",

	// misc
	"miscitems"		=> "Elemento(i)",
	"miscfree"		=> "Disponibili",
	"miscusername"		=> "Utente",
	"miscpassword"		=> "Password",
	"miscoldpass"		=> "Vecchia password",
	"miscnewpass"		=> "Nuova password",
	"miscconfpass"		=> "Conferma password",
	"miscconfnewpass"	=> "Conferma nuova password",
	"miscchpass"		=> "Modifica password",
	"mischomedir"		=> "Cartella principale",
	"mischomeurl"		=> "Home URL",
	"miscshowhidden"	=> "Mostrare gli elementi nascosti",
	"mischidepattern"	=> "Nascondere il percorso",
	"miscperms"		=> "Permessi",
	"miscuseritems"		=> "(nome, cartella principale, mostrare gli elementi nascosti, permessi, attivo)",
	"miscadduser"		=> "aggiungi utente",
	"miscedituser"		=> "modifica utente '%s'",
	"miscactive"		=> "Attivo",
	"misclang"		=> "Lingua",
	"miscnoresult"		=> "Nessun risultato trovato.",
	"miscsubdirs"		=> "Ricerca sotto cartella",
	"miscpermnames"		=> array("Sola lettura","Modifica","Modifica password","Modifica e sostituzione password",
					"Amministratore"),
	"miscyesno"		=> array("Si","No","S","N"),
	"miscchmod"		=> array("Proprietario", "Gruppo", "Pubblico"),

	// from here all new by mic
	'miscowner'			=> 'Proprietario',
	'miscownerdesc'		=> '<strong>Descrizione:</strong><br />Utente (UID) /<br />Gruppo (GID)<br />Diritti correnti:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT." Info Sistema",
	'sisysteminfo'		=> 'Info Sistema',
	'sibuilton'			=> 'Sitema opertivo',
	'sidbversion'		=> 'Versione Database (MySQL)',
	'siphpversion'		=> 'Versione PHP',
	'siphpupdate'		=> 'INFORMAZIONI: <span style="color: red;">La versione PHP version utilizzata <strong>non &#232;</strong> acggiornata!</span><br />Per garantire il corretto funzionamento di tutte le funzioni di Joomla e degli addon,<br />dovete almeno possedere la versione <strong>PHP 4.3</strong>!',
	'siwebserver'		=> 'Server web',
	'siwebsphpif'		=> 'Server web - Interfaccia PHP',
	'simamboversion'	=> $_VERSION->PRODUCT.' Versione',
	'siuseragent'		=> 'Versione Browser',
	'sirelevantsettings' => 'Importanti Settaggi PHP',
	'sisafemode'		=> 'Safe Mode',
	'sibasedir'			=> 'Open basedir',
	'sidisplayerrors'	=> 'Errori PHP',
	'sishortopentags'	=> 'Short Open Tags',
	'sifileuploads'		=> 'File Uploads',
	'simagicquotes'		=> 'Magic Quotes',
	'siregglobals'		=> 'Register Globals',
	'sioutputbuf'		=> 'Output Buffer',
	'sisesssavepath'	=> 'Session Savepath',
	'sisessautostart'	=> 'Session auto start',
	'sixmlenabled'		=> 'XML enabled',
	'sizlibenabled'		=> 'ZLIB enabled',
	'sidisabledfuncs'	=> 'Funzioni non abilitate',
	'sieditor'			=> 'Editor WYSIWYG',
	'siconfigfile'		=> 'File Config',
	'siphpinfo'			=> 'Info PHP',
	'siphpinformation'	=> 'Informazioni PHP',
	'sipermissions'		=> 'Permessi',
	'sidirperms'		=> 'Permessi cartella',
	'sidirpermsmess'	=> 'Per funzionare correttamente tutte le funzioni e le caratteristiche di '.$_VERSION->PRODUCT.' devono ottenere i permessi di scrittura settati [chmod 0777] alle cartelle',
	'sionoff'			=> array( 'Attivo', 'Disattivato' ),

	'extract_warning' => "Voi estrarre questo file? Qui?\\nQuesta operazione sovrascrive i file esistenti e va usata con attenzione!",
	'extract_success' => "Estrazione eseguita correttamente",
	'extract_failure' => "Estrazione fallita",
	
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
