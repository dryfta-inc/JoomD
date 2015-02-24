<?php

// English Language Module for v2.3 (translated by the QuiX project)
$_VERSION = new JVersion();

$GLOBALS["charset"] = "iso-8859-1";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"		=> "BŁĄD / BŁĘDY",
	"message"		=> "INFORMACJE",
	"back"		=> "Wstecz",

	// root
	"home" 		=> "Folder główny nie istnieje. Sprawdź ustawienia.",
	"abovehome"		=> "Folder bieżący nie może znajdować się powyżej folderu głównego.",
	"targetabovehome"	=> "Folder docelowy nie może znajdować się powyżej folderu głównego.",

	// exist
	"direxist"		=> "Folder nie istnieje.",
	//"filedoesexist"	=> "Plik już istnieje.",
	"fileexist"		=> "Plik nie istnieje.",
	"itemdoesexist"	=> "Element już istnieje.",
	"itemexist"		=> "Element nie istnieje.",
	"targetexist"	=> "Folder docelowy nie istnieje.",
	"targetdoesexist"	=> "Element docelowy już istnieje.",

	// open
	"opendir"		=> "Nie można otworzyć folderu.",
	"readdir"		=> "Nie można odczytać folderu.",

	// access
	"accessdir"		=> "Nie masz uprawnień dostępu do tego folderu.",
	"accessfile"	=> "Nie masz uprawnień dostępu do tego pliku.",
	"accessitem"	=> "Nie masz uprawnień dostępu do tego elementu.",
	"accessfunc"	=> "Nie masz uprawnień do wykorzystania tej funkcji.",
	"accesstarget"	=> "Nie masz uprawnień dostępu do folderu docelowego.",

	// actions
	"permread"		=> "Nie udało się odczytać uprawnień.",
	"permchange"	=> "CHMOD - niepowodzenie:",
	"openfile"		=> "Nie udało się otworzyć pliku.",
	"savefile"		=> "Nie udało się zapisać pliku.",
	"createfile"	=> "Nie udało się utworzyć pliku.",
	"createdir"		=> "Nie udało się utworzyć folderu.",
	"uploadfile"	=> "Nie udało się pobrać pliku.",
	"copyitem"		=> "Nie udało się skopiować.",
	"moveitem"		=> "Nie udało się przenieść.",
	"delitem"		=> "Nie udało się usunąć.",
	"chpass"		=> "Nie udało się zmienić hasła.",
	"deluser"		=> "Nie udało się usunąć użytkownika.",
	"adduser"		=> "Nie udało się dodać użytkownika.",
	"saveuser"		=> "Nie udało się zapisać użytkownika.",
	"searchnothing"	=> "Podaj dane do wyszukiwania.",

	// misc
	"miscnofunc"	=> "Funkcja niedostępna.",
	"miscfilesize"	=> "Rozmiar pliku przekracza dozwolone maksimum.",
	"miscfilepart"	=> "Plik pobrano tylko częściowo.",
	"miscnoname"	=> "Musisz podać nazwę.",
	"miscselitems"	=> "Nic nie wybrałeś.",
	"miscdelitems"	=> "Czy jesteś pewien, że chcesz usunąć \"+num+\" elementów?",
	"miscdeluser"	=> "Czy jesteś pewien, że chcesz usunąć '\"+user+\"' użytkowników?",
	"miscnopassdiff"	=> "Nowe hasło nie różni się od starego.",
	"miscnopassmatch"	=> "Hasła nie są jednakowe.",
	"miscfieldmissed"	=> "Pominąłeś ważne pole.",
	"miscnouserpass"	=> "Nazwa użytkownika lub hasło są nieprawidłowe.",
	"miscselfremove"	=> "Nie możesz usunąć siebie.",
	"miscuserexist"	=> "Użytkownik już istnieje.",
	"miscnofinduser"	=> "Nie można znaleźć użytkownika.",
	"extract_noarchive" => "Plik nie jest plikiem archiwum.",
	"extract_unknowntype" => "Nieznany typ archiwum"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "Zmień uprawnienia",
	"editlink"		=> "Edytuj",
	"downlink"		=> "Pobierz",
	"uplink"		=> "W górę",
	"homelink"		=> "Folder główny",
	"reloadlink"	=> "Odśwież",
	"copylink"		=> "Kopiuj",
	"movelink"		=> "Przenieś",
	"dellink"		=> "Usuń",
	"comprlink"		=> "Archiwizuj",
	"adminlink"		=> "Administrator",
	"logoutlink"	=> "Wyloguj",
	"uploadlink"	=> "Pobierz",
	"searchlink"	=> "Szukaj",
	"extractlink"	=> "Wypakuj",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> 'Zmień uprawnienia plików / folderów (chmod) ', // new mic
	'mossysinfolink'	=> ' Informacja o systemie ('.$_VERSION->PRODUCT.', Serwer, PHP, mySQL)', // new mic
	'logolink'		=> 'Przejdź do strony joomlaXplorer (otwiera nowe okno)', // new mic

	// list
	"nameheader"	=> "Nazwa",
	"sizeheader"	=> "Rozmiar",
	"typeheader"	=> "Typ",
	"modifheader"	=> "Data modyfikacji",
	"permheader"	=> "Uprawnienia",
	"actionheader"	=> "Akcje",
	"pathheader"	=> "Ścieżka",

	// buttons
	"btncancel"		=> "Anuluj",
	"btnsave"		=> "Zapisz",
	"btnchange"		=> "Zmień",
	"btnreset"		=> "Resetuj",
	"btnclose"		=> "Zamknij",
	"btncreate"		=> "Utwórz",
	"btnsearch"		=> "Szukaj",
	"btnupload"		=> "Pobierz",
	"btncopy"		=> "Kopiuj",
	"btnmove"		=> "Przenieś",
	"btnlogin"		=> "Zaloguj",
	"btnlogout"		=> "Wyloguj",
	"btnadd"		=> "Dodaj",
	"btnedit"		=> "Edytuj",
	"btnremove"		=> "Usuń",
	
	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'	=> 'Zmień nazwę',
	'confirm_delete_file' => 'Czy na pewno chcesz usunąć ten plik? \\n%s',
	'success_delete_file' => 'Usunięto.',
	'success_rename_file' => 'Nazwę folderu / pliku %s zmieniono na %s.',
	
	// actions
	"actdir"		=> "Folder",
	"actperms"		=> "Zmień uprawnienia",
	"actedit"		=> "Edytuj plik",
	"actsearchresults"	=> "Wyniki wyszukiwania",
	"actcopyitems"	=> "Kopiuj",
	"actcopyfrom"	=> "Kopiuj z /%s do /%s ",
	"actmoveitems"	=> "Przenieś",
	"actmovefrom"	=> "Przenieś z /%s do /%s ",
	"actlogin"		=> "Zaloguj",
	"actloginheader"	=> "Załoguj do QuiXplorer",
	"actadmin"		=> "Administracja",
	"actchpwd"		=> "Zmień hasło",
	"actusers"		=> "Użytkownicy",
	"actarchive"	=> "Archiwizuj",
	"actupload"		=> "Pobierz pliki",

	// misc
	"miscitems"		=> "elementów",
	"miscfree"		=> "wolne",
	"miscusername"	=> "Użytkownik",
	"miscpassword"	=> "Hasło",
	"miscoldpass"	=> "Stare hasło",
	"miscnewpass"	=> "Nowe hasło",
	"miscconfpass"	=> "Potwierdź hasło",
	"miscconfnewpass"	=> "Potwierdź nowe hasło",
	"miscchpass"	=> "Zmień hasło",
	"mischomedir"	=> "Folder główny",
	"mischomeurl"	=> "Strona główna",
	"miscshowhidden"	=> "Pokaż elementy ukryte",
	"mischidepattern"	=> "Ukryj szablon",
	"miscperms"		=> "Uprawnienia",
	"miscuseritems"	=> "(nazwa, folder główny, pokaż elementy ukryte, uprawnienia, aktywny)",
	"miscadduser"	=> "dodaj użytkownika",
	"miscedituser"	=> "edytuj użytkownika '%s'",
	"miscactive"	=> "Aktywny",
	"misclang"		=> "Język",
	"miscnoresult"	=> "Brak wyników.",
	"miscsubdirs"	=> "Szukaj w folderach podrzędnych",
	"miscpermnames"	=> array("Tylko podgląd","Modikacja","Zmiana hasła","Modyfikacja i zmiana hasła", "Administrator"),
	"miscyesno"		=> array("Tak","Nie","T","N"),
	"miscchmod"		=> array("Właściciel", "Grupa", "Publiczny"),

	// from here all new by mic
	'miscowner'		=> 'Właściciel',
	'miscownerdesc'		=> '<strong>Opis:</strong><br />Użytkownik (UID) /<br />Grupa (GID)<br />Obecne uprawnienia:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT." Informacja o systemie",
	'sisysteminfo'		=> 'Informacje o systemie',
	'sibuilton'			=> 'System operacyjny',
	'sidbversion'		=> 'Wersja bazy danych (MySQL)',
	'siphpversion'		=> 'Wersja PHP',
	'siphpupdate'		=> 'INFORMACJA: <span style="color: red;">Wersja PHP, której używasz, <strong>nie jest</strong> aktualna!</span><br />Aby mieć możliwość korzystania z wszystkich funkcji Joomli i dodatków,<br />powinieneś korzystać <strong>z wersji 4.3</strong> lub nowszej!',
	'siwebserver'		=> 'Serwer WWW',
	'siwebsphpif'		=> 'PHP działa na',
	'simamboversion'	=> 'Wersja ' . $_VERSION->PRODUCT,
	'siuseragent'		=> 'Klient użytkownika',
	'sirelevantsettings' => 'Ważne ustawienia PHP',
	'sisafemode'		=> 'Tryb bezpieczny',
	'sibasedir'			=> 'Ścieżka open_basedir',
	'sidisplayerrors'	=> 'Wyświetlaj informacje o błędach',
	'sishortopentags'	=> 'Krótkie znaczniki',
	'sifileuploads'		=> 'Wczytywanie plików',
	'simagicquotes'		=> 'Magiczne cytaty',
	'siregglobals'		=> 'Rejestracja zmiennych globalnych',
	'sioutputbuf'		=> 'Buforowanie wyjściowe',
	'sisesssavepath'	=> 'Ścieżka zapisu sesji',
	'sisessautostart'	=> 'Autostart sesji',
	'sixmlenabled'		=> 'Obsługa XML',
	'sizlibenabled'		=> 'Obsługa ZLIB',
	'sidisabledfuncs'	=> 'Funkcje nieaktywne',
	'sieditor'			=> 'Edytor WYSIWYG',
	'siconfigfile'		=> 'Plik konfiguracyjny',
	'siphpinfo'			=> 'Informacje o PHP',
	'siphpinformation'	=> 'Informacje o PHP',
	'sipermissions'		=> 'Uprawnienia',
	'sidirperms'		=> 'Uprawnienia folderu',
	'sidirpermsmess'	=> 'Aby wszystkie funkcje '.$_VERSION->PRODUCT.' działały prawidłowo, następujące foldery powinny mieć możliwość zapisu [chmod 0777]',
	'sionoff'			=> array( 'Włączone', 'Wyłączone' ),
	
	'extract_warning' => "Czy na pewno chcesz rozpakować ten plik? \\nOperacja może spowodować nadpisanie istniejących plików!",
	'extract_success' => "Rozpakowano",
	'extract_failure' => "Rozpakowanie zakończone niepowodzeniem",
	
	'overwrite_files' => 'Nadpisać istniejące pliki?',
	"viewlink"		=> "Podgląd",
	"actview"		=> "Zawartość pliku",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Przekierować do podfolderów?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'	=> 'Sprawdź aktualność wersji',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'	=>	'Zmiana nazwy pliku lub folderu...',
	'newname'		=>	'Nowa nazwa',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'	=>	'Powrócić do folderu po zakończeniu zapisu?',
	'line'	=> 	'Wiersz',
	'column'	=>	'Kolumna',
	'wordwrap'	=>	'Zawijanie wierszy: (tylko IE)',
	'copyfile'	=>	'Skopiuj plik pod tą nazwą',
	
	// Bookmarks
	'quick_jump' => 'Skocz do',
	'already_bookmarked' => 'Folder już jest zapisany w zakładkach',
	'bookmark_was_added' => 'Folder dopisano do zakładek.',
	'not_a_bookmark' => 'Folder nie jest zapisany w zakładkach.',
	'bookmark_was_removed' => 'Folder usunięto z zakładek.',
	'bookmarkfile_not_writable' => "Nie dopisano %s do zakładek.\n Plik zakładek '%s' \nnie jest zapisywalny.",
	
	'lbl_add_bookmark' => 'Dopisz ten folder do zakładek',
	'lbl_remove_bookmark' => 'Usuń ten folder z zakładek',
	
	'enter_alias_name' => 'Podaj nazwę (alias) dla tej zakładki',
	
	'normal_compression' => 'normalna kompresja',
	'good_compression' => 'dobra kompresja',
	'best_compression' => 'najlepsza kompresja',
	'no_compression' => 'bez kompresji',
	
	'creating_archive' => 'Trwa tworzenie pliku archiwum...',
	'processed_x_files' => 'Przetworzono %s plików z %s',
	
	'ftp_header' => 'Lokalna autoryzacja FTP',
	'ftp_login_lbl' => 'Podaj dane logowania dla serwera FTP',
	'ftp_login_name' => 'Użytkownik FTP',
	'ftp_login_pass' => 'Hasło FTP',
	'ftp_hostname_port' => 'Nazwa serwera i port (opcjonalnie) FTP',
	'ftp_login_check' => 'Nawiązywanie połączenia FTP...',
	'ftp_connection_failed' => "Nie nawiązano połączenia. \nSprawdź, czy serwer FTP działa.",
	'ftp_login_failed' => "Nie można zalogować użytkownika FTP. Sprawdź nazwę użytkownika i hasło i spróbuj ponownie.",
		
	'switch_file_mode' => 'Obecny tryb: <strong>%s</strong>. Możesz się przełączyć do trybu %s.',
	'symlink_target' => 'Adres docelowy dla odnośnika',
	
	"permchange"	=> "CHMOD - powodzenie:",
	"savefile"		=> "Pliku nie zapisano.",
	"moveitem"		=> "Przeniesiono.",
	"copyitem"		=> "Skopiowano.",
	'archive_name' 	=> 'Nazwa pliku archiwum',
	'archive_saveToDir' 	=> 'Zapisz archiwum w tym folderze',
	
	'editor_simple'	=> 'Prosty edytor',
	'editor_syntaxhighlight'	=> 'Podkreślanie składni'
);
?>

