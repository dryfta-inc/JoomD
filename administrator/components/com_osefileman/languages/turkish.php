<?php

// Turkish Language for joomlaXplorer (Translated by Sinan Ata, Tolga Þancý'(cumla.blogspot.com))
$_VERSION = new JVersion();

$GLOBALS["charset"] = "iso-8859-9";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "HATA(LAR)",
	"back"			=> "Geri Gel",
	
	// root
	"home"			=> "Ana klasör oluþturulmamýþ ayarlarýnýzý kontrol edin.",
	"abovehome"		=> "Bu klasör ana klasörün üstünde olmamalýdýr.",
	"targetabovehome"	=> "Hedef klasör ana klasörün üstünde olmamalýdýr.",
	
	// exist
	"direxist"		=> "Bu klasör oluþturulamadý.",
	//"filedoesexist"	=> "Bu dosya zaten var.",
	"fileexist"		=> "Bu dosya oluþturulamadý.",
	"itemdoesexist"		=> "Bu öðe zaten var.",
	"itemexist"		=> "Bu öðe oluþturulamadý.",
	"targetexist"		=> "Hedef klasör oluþturulmamýþ.",
	"targetdoesexist"	=> "Hedef madde zaten oluþturumuþ.",
	
	// open
	"opendir"		=> "Klasör Açýlamaz.",
	"readdir"		=> "Klasör Okunamaz.",
	
	// access
	"accessdir"		=> "Bu klasöre ulaþmak için izinleriniz yetersiz.",
	"accessfile"		=> "Bu dosyaya ulaþmak için izinleriniz yetersiz.",
	"accessitem"		=> "Bu öðeye ulaþmak için izinleriniz yetersiz.",
	"accessfunc"		=> "Bu fonksiyonu kullanmak için izinleriniz yetersiz.",
	"accesstarget"		=> "Hedef klasöre ulaþmak için izinleriniz yetersiz.",
	
	// actions
	"permread"		=> "Ýzin gösterimi baþarýsýz.",
	"permchange"		=> "Ýzin deðiþtirme baþarýsýz.",
	"openfile"		=> "Dosya açýlmasý baþarýsýz.",
	"savefile"		=> "Dosya kaydý baþarýsýz.",
	"createfile"		=> "Dosya oluþumu baþarýsýz.",
	"createdir"		=> "Klasör oluþumu baþarýsýz.",
	"uploadfile"		=> "Dosya yüklemesi baþarýsýz.",
	"copyitem"		=> "Kopyalama baþarýsýz.",
	"moveitem"		=> "Taþýma baþarýsýz.",
	"delitem"		=> "Silme baþarýsýz.",
	"chpass"		=> "Þifre deðiþtirme baþarýsýz.",
	"deluser"		=> "Kullanýcý kaldýrma baþarýsýz.",
	"adduser"		=> "Kullanýcý ekleme baþarýsýz.",
	"saveuser"		=> "Kullanýcý deðiþiklik kaydý baþarýsýz.",
	"searchnothing"		=> "Aramak için bir deðer girmelisiniz.",
	
	// misc
	"miscnofunc"		=> "Fonksiyon kullanýlabilir.",
	"miscfilesize"		=> "Dosya maksimum boyutu aþtý.",
	"miscfilepart"		=> "Dosyanýn yalnýzca bir kýsmý yüklenebildi.",
	"miscnoname"		=> "Bir isim girmelisiniz.",
	"miscselitems"		=> "Hiçbir öðe(ler) seçmediniz.",
	"miscdelitems"		=> " \"+num+\" öðelerini silmek istediðinizden eminmisiniz?",
	"miscdeluser"		=> "'\"+user+\"' kullanýcýsýný silmek istediðinizden eminmisiniz?",
	"miscnopassdiff"	=> "Yeni þifre eskisinden farklý deðil.",
	"miscnopassmatch"	=> "Þifreler eþleþmiyor.",
	"miscfieldmissed"	=> "Gerekli bir boþluðu atladýnýz.",
	"miscnouserpass"	=> "Kullanýcý adý yada þifreniz yanlýþ.",
	"miscselfremove"	=> "Kendinizi silemezsiniz.",
	"miscuserexist"		=> "Kullanýcý zaten var.",
	"miscnofinduser"	=> "Kullanýcý bulunamadý.",
	"extract_noarchive" => "The File is no extractable Archive.",
	"extract_unknowntype" => "Unknown Archive Type"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "Ýzinleri Deðiþtir",
	"editlink"		=> "Deðiþtir",
	"downlink"		=> "Aþaðý",
	"uplink"		=> "Yukarý",
	"homelink"		=> "Baþa Dön",
	"reloadlink"		=> "Yenile",
	"copylink"		=> "Kopyala",
	"movelink"		=> "Taþý",
	"dellink"		=> "Sil",
	"comprlink"		=> "Depola",
	"adminlink"		=> "Yönetici",
	"logoutlink"		=> "Çýkýþ",
	"uploadlink"		=> "Yükle",
	"searchlink"		=> "Ara",
	"extractlink"	=> "Extract Archive",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> 'Change (chmod) Rights (Folder/File(s))', // new mic
	'mossysinfolink'	=> $_VERSION->PRODUCT.' System Information ('.$_VERSION->PRODUCT.', Server, PHP, mySQL)', // new mic
	'logolink'		=> 'Go to the joomlaXplorer Website (new window)', // new mic
	
	// list
	"nameheader"		=> "Ýsim",
	"sizeheader"		=> "Boyut",
	"typeheader"		=> "Tip",
	"modifheader"		=> "Deðiþtirme",
	"permheader"		=> "Ýzinler",
	"actionheader"		=> "Hareketler",
	"pathheader"		=> "Yol",
	
	// buttons
	"btncancel"		=> "Çýkýþ",
	"btnsave"		=> "Kaydet",
	"btnchange"		=> "Deðiþtir",
	"btnreset"		=> "Sýfýrla",
	"btnclose"		=> "Kapat",
	"btncreate"		=> "Oluþtur",
	"btnsearch"		=> "Ara",
	"btnupload"		=> "Yükle",
	"btncopy"		=> "Kopyala",
	"btnmove"		=> "Taþý",
	"btnlogin"		=> "Giriþ",
	"btnlogout"		=> "Çýkýþ",
	"btnadd"		=> "Ekle",
	"btnedit"		=> "Deðiþtir",
	"btnremove"		=> "Kaldýr",
	
	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'	=> 'YENÝDEN ADLANDIR',
	'confirm_delete_file' => 'Bu dosyayý silmek istediðinize eminmisiniz? \\n%s',
	'success_delete_file' => 'Öðe(ler) baþarýyla silindi.',
	'success_rename_file' => 'Dizin/dosya %s baþarýyla yeniden atlandýrýldý  %s.',
	
	// actions
	"actdir"		=> "Klasör",
	"actperms"		=> "Ýzinleri Deðiþtir",
	"actedit"		=> "Dosyayý Deðiþtir",
	"actsearchresults"	=> "Arama Sonuçlarý",
	"actcopyitems"		=> "öðe(ler)i kopyala",
	"actcopyfrom"		=> " /%s dan /%s ya ",
	"actmoveitems"		=> "öðe(ler)i taþý",
	"actmovefrom"		=> " /%s dan /%s ya taþý ",
	"actlogin"		=> "Giriþ",
	"actloginheader"	=> "QuiXplorer kullanýmý için giriþ yap",
	"actadmin"		=> "Yönetim",
	"actchpwd"		=> "Þifre Deðiþtir",
	"actusers"		=> "Kullanýcýlar",
	"actarchive"		=> "öðe(ler)i Yedekle",
	"actupload"		=> "Dosya(lar)i Yükle",
	
	// misc
	"miscitems"		=> "öðe(ler)",
	"miscfree"		=> "Serbest",
	"miscusername"		=> "Kullanýcý Adý",
	"miscpassword"		=> "Þifre",
	"miscoldpass"		=> "Eski Þifre",
	"miscnewpass"		=> "Yeni Þifre",
	"miscconfpass"		=> "Þifreyi Onayla",
	"miscconfnewpass"	=> "Yeni Þifeyi Onayla",
	"miscchpass"		=> "Þifre Deðiþtir",
	"mischomedir"		=> "Ana Klasör",
	"mischomeurl"		=> "Baþ URL",
	"miscshowhidden"	=> "Gizli öðeleri Göster",
	"mischidepattern"	=> "Resim Gizle",
	"miscperms"		=> "Ýzinler",
	"miscuseritems"		=> "(isim, ana klasör, gizli öðeleri göster, izinler, Aktif)",
	"miscadduser"		=> "Kullanýcý ekle",
	"miscedituser"		=> "'%s' kullanýcýsýný deðiþtir",
	"miscactive"		=> "Aktif",
	"misclang"		=> "Dil",
	"miscnoresult"		=> "Hiç sonuç yok.",
	"miscsubdirs"		=> "Alt kategorileri de ara",
	"miscpermnames"		=> array("Sadece bakýlabilir","Modifiye","Þifre deðiþtir","Modifiye & Þifre Deðiþtir",
					"Yönetici"),
	"miscyesno"		=> array("Evet","Hayýr","E","H"),
	"miscchmod"		=> array("Sahip", "Grup", "Halk"),
	// from here all new by mic
	'miscowner'			=> 'Sahip',
	'miscownerdesc'		=> '<strong>Açýklama:</strong><br />User (UID) /<br />Group (GID)<br />Current rights:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT.' Sistem bilgisi',
	'sisysteminfo'		=> 'Sistem Bilgisi',
	'sibuilton'			=> 'Ýþletim Sistemi',
	'sidbversion'		=> 'Veritabaný Versiyonu (MySQL)',
	'siphpversion'		=> 'PHP Versiyonu',
	'siphpupdate'		=> 'Bilgi: <span style="color: red;">The PHP version you use is <strong>not</strong> actual!</span><br />To guarantee all functions and features of '.$_VERSION->PRODUCT.' and addons,<br />you should use as minimum <strong>PHP.Version 4.3</strong>!',
	'siwebserver'		=> 'Webserver',
	'siwebsphpif'		=> 'WebServer - PHP Interface',
	'simamboversion'	=> $_VERSION->PRODUCT.' Version',
	'siuseragent'		=> 'Tarayýcý Versiyonu',
	'sirelevantsettings' => 'Önemli PHP Ayarlarý',
	'sisafemode'		=> 'Safe Mode',
	'sibasedir'			=> 'Open basedir',
	'sidisplayerrors'	=> 'PHP Hatalarý',
	'sishortopentags'	=> 'Short Open Tags',
	'sifileuploads'		=> 'Datei Uploads',
	'simagicquotes'		=> 'Magic Quotes',
	'siregglobals'		=> 'Register Globals',
	'sioutputbuf'		=> 'Output Buffer',
	'sisesssavepath'	=> 'Session Savepath',
	'sisessautostart'	=> 'Session auto start',
	'sixmlenabled'		=> 'XML enabled',
	'sizlibenabled'		=> 'ZLIB enabled',
	'sidisabledfuncs'	=> 'Non enabled functions',
	'sieditor'			=> 'WYSIWYG Editor',
	'siconfigfile'		=> 'Config file',
	'siphpinfo'			=> 'PHP Info',
	'siphpinformation'	=> 'PHP Information',
	'sipermissions'		=> 'Ýzinler',
	'sidirperms'		=> 'Directory permissions',
	'sidirpermsmess'	=> 'To be shure that all functions and features of '.$_VERSION->PRODUCT.' are working correct, following folders should have permission to write [chmod 0777]',
	'sionoff'			=> array( 'On', 'Off' ),
	
	'extract_warning' => "Gerçetten bu dosyayý çýkartmak istiyormusunuz? Buraya?\\nThis will overwrite existing files when not used carefully!",
	'extract_success' => "Extraction was successful",
	'extract_failure' => "Extraction failed",
	
	'overwrite_files' => 'Varolan dosyanýn üzerine yaz?',
	"viewlink"		=> "GÖSTER",
	"actview"		=> "Kaynak dosyasýný göster",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Recurse into subdirectories?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'	=> 'Son sürümü kontrol et',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'	=>	'Dizini yada dosyayý yenidien adlandýr...',
	'newname'		=>	'Yeni Ýsim',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'	=>	'Kaydettikten sonra dizine geri dön?',
	'line'		=> 	'Line',
	'column'	=>	'Column',
	'wordwrap'	=>	'Wordwrap: (IE only)',
	'copyfile'	=>	'Copy file into this filename',
	
	// Bookmarks
	'quick_jump' => 'Hýzlý Atlama',
	'already_bookmarked' => 'Bu dizin zaten yer imlerinde',
	'bookmark_was_added' => 'Bu dizin yer imleri listesine eklendi.',
	'not_a_bookmark' => 'Bu dizin yer imlerinde deðil.',
	'bookmark_was_removed' => 'Bu dizin yer imleri listesinden çýkartýldý.',
	'bookmarkfile_not_writable' => "Failed to %s the bookmark.\n The Bookmark File '%s' \nis not writable.",
	
	'lbl_add_bookmark' => 'Bu dizini yerimlerine ekle',
	'lbl_remove_bookmark' => 'Bu dizini yer imleri listesinden çýkart',
	
	'enter_alias_name' => 'Lütfen bu yer imi için takma ad girin',
	
	'normal_compression' => 'normal sýkýþtýrma',
	'good_compression' => 'iyi sýkýþtýrma',
	'best_compression' => 'en iyi sýkýþtýrma',
	'no_compression' => 'sýkýþtýrma yok',
	
	'creating_archive' => 'Arþiv dosyasý oluþtur...',
	'processed_x_files' => 'Processed %s of %s Files',
	
	'ftp_header' => 'Local FTP Authentication',
	'ftp_login_lbl' => 'Please enter the login credentials for the FTP server',
	'ftp_login_name' => 'FTP Kullanýcý Adý',
	'ftp_login_pass' => 'FTP Þifresi',
	'ftp_hostname_port' => 'FTP Server Hostname and Port <br />(Port is optional)',
	'ftp_login_check' => 'FTP baðlantýsý Kontrol ediliyor...',
	'ftp_connection_failed' => "The FTP server could not be contacted. \nPlease check that the FTP server is running on your server.",
	'ftp_login_failed' => "The FTP login failed. Please check the username and password and try again.",
		
	'switch_file_mode' => 'Þimdiki mod: <strong>%s</strong>. Dilerseniz %s moduna geçebilirsiniz.',
	'symlink_target' => 'Target of the Symbolic Link',
	
	"permchange"		=> "CHMOD Success:",
	"savefile"		=> "Dosya kaydedildi.",
	"moveitem"		=> "Taþýma Tamamlandý.",
	"copyitem"		=> "Kopyalama Tamamlandý.",
	'archive_name' 	=> 'Arþiv dosyasý ismi',
	'archive_saveToDir' 	=> 'Arþivi bu dizine kaydet',
	
	'editor_simple'	=> 'Basit Editör Modu',
	'editor_syntaxhighlight'	=> 'Syntax-Highlighted Mode'
);
?>
