<?php

// Hungarian Language Module for v2.3 (translated by Jozsef Tamas Herczeg a.k.a LocaLiceR www.joomlandia.hu)
$_VERSION = new JVersion(); 

$GLOBALS["charset"] = "iso-8859-2";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y.m.d. H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "HIBÁ(K)",
	"message"			=> "ÜZENET(EK)",
	"back"			=> "Vissza",
	
	// root
	"home"			=> "A kiindulási könyvtár nem létezik, ellenõrizze a beállításokat.",
	"abovehome"		=> "A jelenlegi könyvtár nem lehet följebb a kiindulási könyvtárnál.",
	"targetabovehome"	=> "A célkönyvtár nem lehet följebb a kiindulási könyvtárnál.",
	
	// exist
	"direxist"		=> "Ez a könyvtár nem létezik.",
	//"filedoesexist"	=> "Ez a fájl már létezik.",
	"fileexist"		=> "Ez a fájl nem létezik.",
	"itemdoesexist"		=> "Ez az elem már létezik.",
	"itemexist"		=> "Ez az elem nem létezik.",
	"targetexist"		=> "A célkönyvtár nem létezik.",
	"targetdoesexist"	=> "A célelem már létezik.",
	
	// open
	"opendir"		=> "Nem nyitható meg a könyvtár.",
	"readdir"		=> "Nem olvasható a könyvtár.",
	
	// access
	"accessdir"		=> "Nem engedélyezett az Ön számára az ehhez a könyvtárhoz való hozzáférés.",
	"accessfile"		=> "Nem engedélyezett az Ön számára az ehhez a fájlhoz való hozzáférés.",
	"accessitem"		=> "Nem engedélyezett az Ön számára az ehhez az elemhez való hozzáférés.",
	"accessfunc"		=> "Ennek a funkciónak a használata nem engedélyezett az Ön számára.",
	"accesstarget"		=> "Nem engedélyezett a célkönyvtárhoz való hozzáférés.",
	
	// actions
	"permread"		=> "Az engedélyek lekérése nem sikerült.",
	"permchange"		=> "Az engedélymódosítás nem sikerült.",
	"openfile"		=> "Nem lehet megnyitni a fájlt.",
	"savefile"		=> "Nem lehet menteni a fájlt.",
	"createfile"		=> "Nem lehet létrehozni a fájlt.",
	"createdir"		=> "Nem lehet létrehozni a könyvtárt.",
	"uploadfile"		=> "A fájl feltöltése nem sikerült.",
	"copyitem"		=> "A másolás nem sikerült.",
	"moveitem"		=> "Az áthelyezés nem sikerült.",
	"delitem"		=> "A törlés nem sikerült.",
	"chpass"		=> "Nem sikerült megváltoztatni a jelszót.",
	"deluser"		=> "A felhasználó eltávolítása nem sikerült.",
	"adduser"		=> "A felhasználó hozzáadása nem sikerült.",
	"saveuser"		=> "A felhasználó mentése nem sikerült.",
	"searchnothing"		=> "Meg kell adnia a keresendõ kulcsszót.",
	
	// misc
	"miscnofunc"		=> "Ez a funkció nem mûködik.",
	"miscfilesize"		=> "A fájl mérete nagyobb a megengedettnél.",
	"miscfilepart"		=> "Csak részben sikerült feltölteni a fájlt.",
	"miscnoname"		=> "Meg kell adnia egy nevet.",
	"miscselitems"		=> "Nem választott ki egy elemet sem.",
	"miscdelitems"		=> "Biztosan törölni akarja ezt a(z) \"+num+\" elemet?",
	"miscdeluser"		=> "Biztosan törölni akarja a következõ felhasználót: '\"+user+\"'?",
	"miscnopassdiff"	=> "Az új jelszó ugyanaz, mint a jelenlegi.",
	"miscnopassmatch"	=> "Eltérõek a jelszavak.",
	"miscfieldmissed"	=> "Kihagyott egy fontos mezõt.",
	"miscnouserpass"	=> "Érvénytelen a felhasználónév vagy a jelszó.",
	"miscselfremove"	=> "Saját magát nem távolíthatja el.",
	"miscuserexist"		=> "A felhasználó már létezik.",
	"miscnofinduser"	=> "Nem található a felhasználó.",
	"extract_noarchive" => "A fájl nem kibontható archívum.",
	"extract_unknowntype" => "Ismeretlen típusú archívum"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "ENGEDÉLYEK MÓDOSÍTÁSA",
	"editlink"		=> "SZERKESZTÉS",
	"downlink"		=> "LETÖLTÉS",
	"uplink"		=> "FEL",
	"homelink"		=> "KIINDULÁSI KÖNYVTÁR",
	"reloadlink"		=> "FRISSÍTÉS",
	"copylink"		=> "MÁSOLÁS",
	"movelink"		=> "ÁTHELYEZÉS",
	"dellink"		=> "TÖRLÉS",
	"comprlink"		=> "ARCHIVÁLÁS",
	"adminlink"		=> "ADMIN",
	"logoutlink"		=> "KILÉPÉS",
	"uploadlink"		=> "FELTÖLTÉS",
	"searchlink"		=> "KERESÉS",
	"extractlink"	=> "Archívum kibontása",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> 'Engedélyek módosítása (chmod) (Könyvtár/Fájl(ok))', // new mic
	'mossysinfolink'	=> $_VERSION->PRODUCT.' Rendszerinformáció ('.$_VERSION->PRODUCT.', Kiszolgáló, PHP, mySQL)', // new mic
	'logolink'		=> 'Ugrás a joomlaXplorer webhelyére (új ablak)', // new mic
	
	// list
	"nameheader"		=> "Név",
	"sizeheader"		=> "Méret",
	"typeheader"		=> "Típus",
	"modifheader"		=> "Módosítva",
	"permheader"		=> "Engedélyek",
	"actionheader"		=> "Mûveletek",
	"pathheader"		=> "Útvonal",
	
	// buttons
	"btncancel"		=> "Mégse",
	"btnsave"		=> "Mentés",
	"btnchange"		=> "Módosítás",
	"btnreset"		=> "Alaphelyzet",
	"btnclose"		=> "Bezárás",
	"btncreate"		=> "Létrehozás",
	"btnsearch"		=> "Keresés",
	"btnupload"		=> "Feltöltés",
	"btncopy"		=> "Másolás",
	"btnmove"		=> "Áthelyezés",
	"btnlogin"		=> "Bejelentkezés",
	"btnlogout"		=> "Kijelentkezés",
	"btnadd"		=> "Hozzáadás",
	"btnedit"		=> "Szerkesztés",
	"btnremove"		=> "Áthelyezés",
	
	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'	=> 'ÁTNEVEZÉS',
	'confirm_delete_file' => 'Biztosan törölni akarja ezt a fájlt? \\n%s',
	'success_delete_file' => 'Az elem(ek) törlése sikerült.',
	'success_rename_file' => 'A(z) %s könyvtár/fájl átnevezése %s névre sikerült.',
	
	// actions
	"actdir"		=> "Könyvtár",
	"actperms"		=> "Engedélyek módosítása",
	"actedit"		=> "Fájl szerkesztése",
	"actsearchresults"	=> "A keresés eredménye",
	"actcopyitems"		=> "Elem(ek) másolása",
	"actcopyfrom"		=> "Másolás a(z) /%s mappából a(z) /%s mappába ",
	"actmoveitems"		=> "Elem(ek) áthelyezése",
	"actmovefrom"		=> "Áthelyezés a(z) /%s mappából a(z) /%s mappába ",
	"actlogin"		=> "Bejelentkezés",
	"actloginheader"	=> "Bejelentkezés a QuiXplorer használatára",
	"actadmin"		=> "Adminisztrálás",
	"actchpwd"		=> "A jelszó megváltoztatása",
	"actusers"		=> "Felhasználók",
	"actarchive"		=> "Elem(ek) archiválása",
	"actupload"		=> "Fájl(ok) feltöltése",
	
	// misc
	"miscitems"		=> "elem",
	"miscfree"		=> "Szabad terület",
	"miscusername"		=> "Felhasználónév",
	"miscpassword"		=> "Jelszó",
	"miscoldpass"		=> "A régi jelszó",
	"miscnewpass"		=> "Az új jelszó",
	"miscconfpass"		=> "A jelszó megerõsítése",
	"miscconfnewpass"	=> "Az új jelszó megerõsítése",
	"miscchpass"		=> "Jelszócsere",
	"mischomedir"		=> "Kiindulási könyvtár",
	"mischomeurl"		=> "Kezdõ URL",
	"miscshowhidden"	=> "A rejtett elemek láthatók",
	"mischidepattern"	=> "Minta elrejtése",
	"miscperms"		=> "Engedélyek",
	"miscuseritems"		=> "(név, kiindulási könyvtár, rejtett elemek megjelenítése, engedélyek, aktív)",
	"miscadduser"		=> "új felhasználó",
	"miscedituser"		=> "'%s' felhasználó módosítása",
	"miscactive"		=> "Aktív",
	"misclang"		=> "Nyelv",
	"miscnoresult"		=> "A keresés eredménytelen.",
	"miscsubdirs"		=> "Keresés az alkönyvtárakban",
	"miscpermnames"		=> array("Csak nézet","Módosítás","Jelszócsere","Módosítás és jelszócsere",
					"Adminisztrátor"),
	"miscyesno"		=> array("Igen","Nem","I","N"),
	"miscchmod"		=> array("Tulajdonos", "Csoport", "Közönség"),

	// from here all new by mic
	'miscowner'			=> 'Tulajdonos',
	'miscownerdesc'		=> '<strong>Leírás:</strong><br />Felhasználó (UID) /<br />Csoport (GID)<br />Jelenlegi engedélyek:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT." rendszerinformáció",
	'sisysteminfo'		=> 'Rendszer',
	'sibuilton'			=> 'Operációs rendszer',
	'sidbversion'		=> 'Adatbázis verziószáma (MySQL)',
	'siphpversion'		=> 'PHP verziószáma',
	'siphpupdate'		=> 'INFORMÁCIÓ: <span style="color: red;">Az Ön által használt PHP verzió <strong>elavult</strong>!</span><br />A Mambo és kiegészítõi valamennyi funkcióinak és szolgáltatásainak biztosításához<br />legalább <strong>PHP 4.3-as verziót</strong> kell használnia!',
	'siwebserver'		=> 'Webkiszolgáló',
	'siwebsphpif'		=> 'Webkiszolgáló - PHP felület',
	'simamboversion'	=> $_VERSION->PRODUCT.' verziószáma',
	'siuseragent'		=> 'Böngészõ verziószáma',
	'sirelevantsettings' => 'Fontos PHP beállítások',
	'sisafemode'		=> 'Biztonságos mód',
	'sibasedir'			=> 'Open basedir',
	'sidisplayerrors'	=> 'PHP hibák',
	'sishortopentags'	=> 'Short Open Tags',
	'sifileuploads'		=> 'Fájlfeltöltés',
	'simagicquotes'		=> 'Mágikus idézõjelek',
	'siregglobals'		=> 'Register Globals',
	'sioutputbuf'		=> 'Kimeneti puffer',
	'sisesssavepath'	=> 'Munkamenet mentési útvonal',
	'sisessautostart'	=> 'Munkamenet automatikus indítása',
	'sixmlenabled'		=> 'XML engedélyezett',
	'sizlibenabled'		=> 'ZLIB engedélyezett',
	'sidisabledfuncs'	=> 'Nem engedélyezett funkciók',
	'sieditor'			=> 'WYSIWYG szerkesztõ',
	'siconfigfile'		=> 'Konfigurációs fájl',
	'siphpinfo'			=> 'PHP',
	'siphpinformation'	=> 'PHP tulajdonságai',
	'sipermissions'		=> 'Engedélyek',
	'sidirperms'		=> 'Könyvtárengedélyek',
	'sidirpermsmess'	=> 'Ha azt akarja, hogy a '.$_VERSION->PRODUCT.' összes funkciója és szolgáltatása megfelelõen mûködjön, akkor írhatóvá kell tennie a következõ könyvtárakat [chmod 0777]',
	'sionoff'			=> array( 'Be', 'Ki' ),
	
	'extract_warning' => "Valóban ki akarja bontani ezt a fájlt? Ide?\\nFelül fogja írni a létezõ fájlokat, ha nem körültekintõen használja!",
	'extract_success' => "A kibontás sikerült",
	'extract_failure' => "A kibontás nem sikerült",
	
	'overwrite_files' => 'A létezõ fájlok felülírása',
	"viewlink"		=> "NÉZET",
	"actview"		=> "A fájl forrásának megtekintése",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Végrehajtás az alkönyvtárakon is?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'	=> 'Új verzió ellenõrzése',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'	=>	'Fájl vagy könyvtár átnevezése...',
	'newname'		=>	'Új név',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'	=>	'Visszatérés a könyvtárhoz mentés után?',
	'line'		=> 	'Sor',
	'column'	=>	'Oszlop',
	'wordwrap'	=>	'Sortördelés: (csak IE)',
	'copyfile'	=>	'A fájl másolása ezen a néven',
	
	// Bookmarks
	'quick_jump' => 'Gyors ugrás ide:',
	'already_bookmarked' => 'Ehhez a könyvtárhoz már helyezett el könyvjelzõt',
	'bookmark_was_added' => 'Ez a könyvtár hozzáadásra került a könyvjelzõkbe.',
	'not_a_bookmark' => 'Ez a könyvtár nem könyvjelzõ.',
	'bookmark_was_removed' => 'Ez a könyvtár eltávolításra került a könyvjelzõk közül.',
	'bookmarkfile_not_writable' => "Nem sikerült %s a könyvjelzõt.\n A '%s' könyvjelzõfájl \nnem írható.",
	
	'lbl_add_bookmark' => 'Könyvjelzõ készítése ehhez a könyvtárhoz',
	'lbl_remove_bookmark' => 'A könyvtár könyvjelzõjének eltávolítása',
	
	'enter_alias_name' => 'Kérjük, hogy írja be a könyvtár aliasnevét',
	
	'normal_compression' => 'normál tömörítés',
	'good_compression' => 'jó tömörítés',
	'best_compression' => 'legjobb tömörítés',
	'no_compression' => 'nincs tömörítés',
	
	'creating_archive' => 'Archív fájl létrehozása...',
	'processed_x_files' => 'Feldolgozva %s a(z) %s fájlból',
	
	'ftp_header' => 'Helyi FTP hitelesítés',
	'ftp_login_lbl' => 'Kérjük, hogy írja be az FTP kiszolgáló hitelesítési adatait',
	'ftp_login_name' => 'FTP felhasználónév',
	'ftp_login_pass' => 'FTP jelszó',
	'ftp_hostname_port' => 'Az FTP kiszolgáló neve és portja <br />(A port elhagyható)',
	'ftp_login_check' => 'FTP kapcsolat ellenõrzése...',
	'ftp_connection_failed' => "Nem lehet kapcsolódni az FTP kiszolgálóhoz. \nKérjük, ellenõrizze, hogy mûködik-e az FTP kiszolgáló a szerveren.",
	'ftp_login_failed' => "Sikertelen az FTP bejelentkezés. Kérjük, hogy ellenõrizze a felhasználónevet és a jelszót, majd próbálja újra.",
		
	'switch_file_mode' => 'Jelenlegi mód: <strong>%s</strong>. Átváltás %s módba.',
	'symlink_target' => 'A szimbolikus hivatkozás célja',
	
	"permchange"		=> "CHMOD siker:",
	"savefile"		=> "A fájl mentése kész.",
	"moveitem"		=> "Az áthelyezés sikerült.",
	"copyitem"		=> "A másolás sikerült.",
	'archive_name' 	=> 'Az archívumfájl neve',
	'archive_saveToDir' 	=> 'Az archívum mentése a könyvtárban',
	
	'editor_simple'	=> 'Egyszerû szerkesztõ mód',
	'editor_syntaxhighlight'	=> 'Szintaxis-kiemeléses mód'
);
?>
