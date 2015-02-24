<?php
// version 1.6.1 by Markku Suominen, dated 13.7.2007
/// admin@joomlaportal.fi
// Finnish Language Module for joomlaXplorer (translated by Jarmo Keränen)
// modifications and translation of new strings by Markku Suominen 10.11.2005
// Finnish Joomla translation team, http://www.joomlaportal.fi, admin@joomlaportal.fi
$_VERSION = new JVersion(); 

$GLOBALS["charset"] = "iso-8859-1";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "j.n.Y H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "virheet",
	"message"			=> "viestit",
	"back"			=> "Palaa",
	
	// root
	"home"			=> "Kotihakemistoa ei ole, tarkista asetuksesi.",
	"abovehome"		=> "Nykyinen hakemisto ei saa olla kotihakemiston yläpuolella.",
	"targetabovehome"	=> "Kohdehakemisto ei saa olla kotihakemiston yläpuolella.",
	
	// exist
	"direxist"		=> "Hakemistoa ei ole.",
	//"filedoesexist"	=> "This file already exists.",
	"fileexist"		=> "Tiedostoa ei ole.",
	"itemdoesexist"		=> "Nimike on jo olemassa.",
	"itemexist"		=> "Nimike ei ole olemassa.",
	"targetexist"		=> "Kohdehakemistoa ei ole.",
	"targetdoesexist"	=> "Kohdenimike on jo olemassa.",
	
	// open
	"opendir"		=> "Hakemistoa ei voi avata.",
	"readdir"		=> "Hakemistoa ei voi lukea.",
	
	// access
	"accessdir"		=> "Sinulla ei ole valtuuksia tähän hakemistoon.",
	"accessfile"		=> "Sinulla ei ole valtuuksia tähän tiedostoon.",
	"accessitem"		=> "Sinulla ei ole valtuuksia tähän nimikkeeseen.",
	"accessfunc"		=> "Sinulla ei ole valtuuksia tähän toimintoon.",
	"accesstarget"		=> "Sinulla ei ole valtuuksia kohdehakemistoon.",
	
	// actions
	"permread"		=> "Käyttöoikeuksien luku epäonnistui.",
	"permchange"		=> "Käyttäoikeuksien muutos epäonnistui.",
	"openfile"		=> "Tiedoston avaaminen epäonnistui.",
	"savefile"		=> "Tiedoston tallennus epäonnistui.",
	"createfile"		=> "Tiedoston luonti epäonnistui.",
	"createdir"		=> "Hakemiston luonti epäonnistui.",
	"uploadfile"		=> "Tiedoston vienti palvelimelle epäonnistui.",
	"copyitem"		=> "Kopiointi epäonnistui.",
	"moveitem"		=> "Siirto epäonnistui.",
	"delitem"		=> "Poisto epäonnistui.",
	"chpass"		=> "Salasanan vaihto epäonnistui.",
	"deluser"		=> "Käyttäjän poisto epäonnistui.",
	"adduser"		=> "Käyttäjän lisäys epäonnistui.",
	"saveuser"		=> "Käyttäjän tallennus epäonnistui.",
	"searchnothing"		=> "Sinun pitää antaa jotain etsittävää.",
	
	// misc
	"miscnofunc"		=> "Toiminto ei ole käytettävissä.",
	"miscfilesize"		=> "Tiedosto koko ylittää suurimman sallitun arvon.",
	"miscfilepart"		=> "Tiedoston vienti palvelimelle onnistui vain osittain.",
	"miscnoname"		=> "Anna nimi.",
	"miscselitems"		=> "Et ole valinnut yhtään nimikettä.",
	"miscdelitems"		=> "Haluatko varmasti poistaa nämä \"+num+\" nimikettä?",
	"miscdeluser"		=> "Haluatko varmasti poistaa käyttäjän '\"+user+\"'?",
	"miscnopassdiff"	=> "Uusi salasana ei eroa nykyisestä.",
	"miscnopassmatch"	=> "Salasanat eivät täsmää.",
	"miscfieldmissed"	=> "Ohitit tärkeän kentän.",
	"miscnouserpass"	=> "Käyttäjänimi tai salasana on väärä.",
	"miscselfremove"	=> "Et voi poistaa omaa tunnustasi.",
	"miscuserexist"		=> "Käyttäjä on jo olemassa.",
	"miscnofinduser"	=> "Käyttäjää ei löydy.",
	"extract_noarchive" => "Tiedoston tyyppi ei ole sellainen joka voidaan purkaa.",
	"extract_unknowntype" => "Tuntematon arkistointimuoto"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "Muuta oikeuksia",
	"editlink"		=> "Muokkaa",
	"downlink"		=> "Lataa",
	"uplink"		=> "Ylös",
	"homelink"		=> "Juurihakemisto",
	"reloadlink"	=> "Päivitä",
	"copylink"		=> "Kopioi",
	"movelink"		=> "Siirrä",
	"dellink"		=> "Poista",
	"comprlink"		=> "Arkistoi",
	"adminlink"		=> "Hallinta",
	"logoutlink"	=> "Poistu",
	"uploadlink"	=> "Vie palvelimelle",
	"searchlink"	=> "Etsi",
	"extractlink"	=> "Pura arkistotiedosto",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> 'Muuta (chmod) oikeudet (kansio/tiedosto(t))', // new mic
	'mossysinfolink'	=> $_VERSION->PRODUCT.' Järjestelmätiedot ('.$_VERSION->PRODUCT.', palvelin, PHP, mySQL)', // new mic
	'logolink'		=> 'Siirry joomlaXplorer sivustolle(uusi ikkuna)', // new mic
	
	// list
	"nameheader"		=> "Nimi",
	"sizeheader"		=> "Koko",
	"typeheader"		=> "Tyyppi",
	"modifheader"		=> "Muutettu",
	"permheader"		=> "Oikeudet",
	"actionheader"		=> "Toiminnot",
	"pathheader"		=> "Polku",
	
	// buttons
	"btncancel"		=> "Peru",
	"btnsave"		=> "Tallenna",
	"btnchange"		=> "Muuta",
	"btnreset"		=> "Peru",
	"btnclose"		=> "Sulje",
	"btncreate"		=> "Luo",
	"btnsearch"		=> "Etsi",
	"btnupload"		=> "Vie palvelimelle",
	"btncopy"		=> "Kopioi",
	"btnmove"		=> "Siirrä",
	"btnlogin"		=> "Kirjaudu",
	"btnlogout"		=> "Poistu",
	"btnadd"		=> "Lisää",
	"btnedit"		=> "Muokka",
	"btnremove"		=> "Poista",
	
	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'	=> 'Nimeä',
	'confirm_delete_file' => 'Haluatko varmasti poistaa tiedoston? \\n%s',
	'success_delete_file' => 'Nimike(ttä) poistettu .',
	'success_rename_file' => 'Hakemisto/tiedosto  %s nimettiin, uusi nimi on %s.',
	
	// actions
	"actdir"		=> "Hakemisto",
	"actperms"		=> "Muuta oikeuksia",
	"actedit"		=> "Muokkaa tiedostoa",
	"actsearchresults"	=> "Haun tulokset",
	"actcopyitems"		=> "Kopioi nimikkeet",
	"actcopyfrom"		=> "Kopioi kohteesta /%s kohteeseen /%s ",
	"actmoveitems"		=> "Siirrä nimikkeet",
	"actmovefrom"		=> "Siirrä kohteesta /%s kohteeseen /%s ",
	"actlogin"		=> "Kirjaudu",
	"actloginheader"	=> "Kirjaudu käyttääksesi joomlaXploreria",
	"actadmin"		=> "Hallinta",
	"actchpwd"		=> "Muuta salasana",
	"actusers"		=> "Käyttäjät",
	"actarchive"		=> "Arkistoi nimikkeet",
	"actupload"		=> "Vie tiedostot palvelimelle",
	
	// misc
	"miscitems"		=> "Nimikettä",
	"miscfree"		=> "Vapaana",
	"miscusername"		=> "Käyttäjänimi",
	"miscpassword"		=> "Salasana",
	"miscoldpass"		=> "Vanha salasana",
	"miscnewpass"		=> "Uusi salasana",
	"miscconfpass"		=> "Vahvista salasana",
	"miscconfnewpass"	=> "Vahvista uusi salasana",
	"miscchpass"		=> "Muuta salasana",
	"mischomedir"		=> "Kotihakemisto",
	"mischomeurl"		=> "Koti URL",
	"miscshowhidden"	=> "Näytä piilotetut nimikkeet",
	"mischidepattern"	=> "Piilota kuvio",
	"miscperms"		=> "Oikeudet",
	"miscuseritems"		=> "(nimi, kotihakemisto, näytä piilotetut nimikkeet, oikeudet, aktiivi)",
	"miscadduser"		=> "lisää käyttäjä",
	"miscedituser"		=> "muokkaa käyttäjää '%s'",
	"miscactive"		=> "Aktiivi",
	"misclang"		=> "Kieli",
	"miscnoresult"		=> "Ei tuloksia.",
	"miscsubdirs"		=> "Etsi alahakemistoista",
	"miscpermnames"		=> array("Vain katselu","Muokkaa","Muuta salasana","Muokkaa ja muuta salasana",
					"Hallinta"),
	"miscyesno"		=> array("Kyllä","Ei","K","E"),
	"miscchmod"		=> array("Omistaja", "Ryhmä", "Julkinen"),

	// from here all new by mic
	'miscowner'			=> 'Omistaja',
	'miscownerdesc'		=> '<strong>Kuvaus:</strong><br />Käyttäjä (UID) /<br />Ryhmä (GID)<br />Nykyiset oikeudet:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT.' Järjestelmän tiedot',
	'sisysteminfo'		=> 'Järjestelmän tiedot',
	'sibuilton'			=> 'Käyttöjärjestelmä',
	'sidbversion'		=> 'Tietokannan versio (MySQL)',
	'siphpversion'		=> 'PHP versio',
	'siphpupdate'		=> 'TIETOJA: <span style="color: red;">Käyttämäsi PHP-versio <strong>ei ole </strong> riittävän uusi</span><br />Käyttääksesi tuotteen toimintoja '.$_VERSION->PRODUCT.' ja lisäosia,<br />vanhin hyväksytty versio on <strong>PHP 4.3</strong>!',
	'siwebserver'		=> 'Web-palvelin',
	'siwebsphpif'		=> 'Web-palvelin - PHP rajapinta',
	'simamboversion'	=> $_VERSION->PRODUCT.' versio',
	'siuseragent'		=> 'Selaimen versio',
	'sirelevantsettings' => 'Tärkeät PHP-asetukset',
	'sisafemode'		=> 'Safe Mode',
	'sibasedir'			=> 'Open basedir',
	'sidisplayerrors'	=> 'PHP-virheet',
	'sishortopentags'	=> 'Short Open Tags',
	'sifileuploads'		=> 'Tiedostojen lataaminen palvelimelle',
	'simagicquotes'		=> 'Magic Quotes',
	'siregglobals'		=> 'Register Globals',
	'sioutputbuf'		=> 'Output Buffer',
	'sisesssavepath'	=> 'Istunnon tallennuspolku',
	'sisessautostart'	=> 'Istunnon automaattinen käynnistys',
	'sixmlenabled'		=> 'XML käytössä',
	'sizlibenabled'		=> 'ZLIB käytössä',
	'sidisabledfuncs'	=> 'Estetyt funktiot',
	'sieditor'			=> 'WYSIWYG-editori',
	'siconfigfile'		=> 'Asetustiedosto',
	'siphpinfo'			=> 'PHP-tiedot',
	'siphpinformation'	=> 'PHP-tiedot',
	'sipermissions'		=> 'Käyttöoikeudet',
	'sidirperms'		=> 'Hakemiston käyttöoikeudet',
	'sidirpermsmess'	=> 'Jotta kaikki tuotteen toiminnot ja funktiot toimivat oikein '.$_VERSION->PRODUCT.' , seuraavien kansioihin tulee voida kirjoittaa [chmod 0777]',
	'sionoff'			=> array( 'Pois', 'Päällä' ),
	
	'extract_warning' => "Haluatko purkaa tiedoston tähän hakemistoon? \\nKäytä toimintoa varoen, sillä olemassa olevat tiedostot ylikirjoitetaan arkistotiedoston tiedostoilla.",
	'extract_success' => "Tiedoston purkaminen onnistui",
	'extract_failure' => "Purkaminen epäonnistui",
	
	'overwrite_files' => 'Korvaa nykyiset tiedostot?',
	"viewlink"		=> "NÄYTÄ",
	"actview"		=> "Näytetään tiedoston sisältö",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Kohdista toiminto myös alihakemistoihin?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'	=> 'Tarkista viimeisin versio',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'	=>	'Nimeä tiedosto tai hakemisto...',
	'newname'		=>	'Uusi nimi',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'	=>	'Palaa hakemistoon tallentamisen jälkeen?',
	'line'		=> 	'Rivi',
	'column'	=>	'Sarake',
	'wordwrap'	=>	'Rivitä: (vain IE)',
	'copyfile'	=>	'Kopioi tiedosto tälle nimelle',
	
	// Bookmarks
	'quick_jump' => 'Siirry kohteeseen',
	'already_bookmarked' => 'Hakemisto on jo kirjanmerkitty',
	'bookmark_was_added' => 'Hakemisto lisättiin kirjanmerkkien listaan.',
	'not_a_bookmark' => 'Hakemisto ei ole kirjanmerkki.',
	'bookmark_was_removed' => 'Hakemisto poistettiin kirjanmerkkien listasta.',
	'bookmarkfile_not_writable' => "Kirjanmerkkiin liittyvä toiminto %s epäonnistui.\n Kirjanmerkkitiedosto '%s' \n on kirjoitussuojattu.",
	
	'lbl_add_bookmark' => 'Lisää hakemisto kirjanmerkkeihin',
	'lbl_remove_bookmark' => 'Poista hakemisto kirjanmerkkilistasta',
	
	'enter_alias_name' => 'Anna kirjanmerkin alias',
	
	'normal_compression' => 'normaali pakkaus',
	'good_compression' => 'parempi pakkaus',
	'best_compression' => 'paras pakkaus',
	'no_compression' => 'ei pakkausta',
	
	'creating_archive' => 'Luodaan arkistotiedosto ...',
	'processed_x_files' => 'Käytö läpi %s / %s tiedostoa',
	
	'ftp_header' => 'Paikallinen FTP-autentikointi',
	'ftp_login_lbl' => 'Anna FTP-palvelimen vaatimat kirjatumistiedot',
	'ftp_login_name' => 'FTP käyttäjätunnus',
	'ftp_login_pass' => 'FTP salasana',
	'ftp_hostname_port' => 'FTP-palvelimen nimi ja portti<br />(portti on valinnainen)',
	'ftp_login_check' => 'Tarkistetaan FTP-yhteyttä...',
	'ftp_connection_failed' => "FTP-palvelimeen ei saada yhteyttä. \nTarkista että FTP-palvelin on toiminnassa.",
	'ftp_login_failed' => "FTP-kirjautuminen ei onnistunut. Tarkista käyttäjätunnus ja salasana ja yritä uudelleen.",
		
	'switch_file_mode' => 'Nykyinen tila: <strong>%s</strong>. Voit vaihtaa tilaan %s.',
	'symlink_target' => 'Symbolisen linkin kohde',
	
	"permchange"		=> "CHMOD-toiminto onnistui:",
	"savefile"		=> "Tiedosto tallennettiin.",
	"moveitem"		=> "Siirto onnistui.",
	"copyitem"		=> "Kopiointi onnistui.",
	'archive_name' 	=> 'Arkistotiedoston nimi',
	'archive_saveToDir' 	=> 'Tallenna arkistotiedosto tähän hakemistoon',
	
	'editor_simple'	=> 'Yksinkertainen editori -tila',
	'editor_syntaxhighlight'	=> 'Syntaksin korostus -tila'
);
?>