<?php

// French Language Module for joomlaXplorer (translated by Olivier Pariseau and Alexandre PRIETO)
$_VERSION = new JVersion(); 
$GLOBALS["charset"] = "iso-8859-1";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "d/m/Y H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "ERREUR(S)",
	"back"			=> "Page précédente",
	
	// root
	"home"			=> "Le répertoire home n'existe pas, vérifiez vos préférences.",
	"abovehome"		=> "Le répertoire courant n'a pas l'air d'être au-dessus du répertoire home.",
	"targetabovehome"	=> "Le répertoire cible n'a pas l'air d'être au-dessus du répertoire home.",
	
	// exist
	"direxist"		=> "Ce répertoire n'existe pas.",
	//"filedoesexist"	=> "Ce fichier existe déjà.",
	"fileexist"		=> "Ce fichier n'existe pas.",
	"itemdoesexist"		=> "Cet item existe déjà.",
	"itemexist"		=> "Cet item n'existe pas.",
	"targetexist"		=> "Le répertoire cible n'existe pas.",
	"targetdoesexist"	=> "L'item cible existe déjà.",
	
	// open
	"opendir"		=> "Impossible d'ouvrir le répertoire.",
	"readdir"		=> "Impossible de lire le répertoire.",
	
	// access
	"accessdir"		=> "Vous n'êtes pas autorisé a accéder à ce répertoire.",
	"accessfile"		=> "Vous n'êtes pas autorisé a accéder à ce fichier.",
	"accessitem"		=> "Vous n'êtes pas autorisé a accéder à cet item.",
	"accessfunc"		=> "Vous ne pouvez pas utiliser cette fonction.",
	"accesstarget"		=> "Vous n'êtes pas autorisé a accéder au repertoire cible.",
	
	// actions
	"permread"		=> "Lecture des permissions échouée.",
	"permchange"		=> "Changement des permissions échoué.",
	"openfile"		=> "Ouverture du fichier échouée.",
	"savefile"		=> "Sauvegarde du fichier échouée.",
	"createfile"		=> "Création du fichier échouée.",
	"createdir"		=> "Création du répertoire échouée.",
	"uploadfile"		=> "Envoie du fichier échoué.",
	"copyitem"		=> "La copie a échouée.",
	"moveitem"		=> "Le déplacement a échoué.",
	"delitem"		=> "La supression a échouée.",
	"chpass"		=> "Le changement de mot de passe a échoué.",
	"deluser"		=> "La supression de l'usager a échouée.",
	"adduser"		=> "L'ajout de l'usager a échouée.",
	"saveuser"		=> "La sauvegarde de l'usager a échouée.",
	"searchnothing"		=> "Vous devez entrez quelquechose à chercher.",
	
	// misc
	"miscnofunc"		=> "Fonctionalité non disponible.",
	"miscfilesize"		=> "La taille du fichier excède la taille maximale autorisée.",
	"miscfilepart"		=> "L'envoi du fichier n'a pas été complété.",
	"miscnoname"		=> "Vous devez entrer un nom.",
	"miscselitems"		=> "Vous n'avez sélectionné aucuns item(s).",
	"miscdelitems"		=> "Êtes-vous certain de vouloir supprimer ces \"+num+\" item(s)?",
	"miscdeluser"		=> "Êtes-vous certain de vouloir supprimer l'usager '\"+user+\"'?",
	"miscnopassdiff"	=> "Le nouveau mot de passe est indentique au précédent.",
	"miscnopassmatch"	=> "Les mots de passe différent.",
	"miscfieldmissed"	=> "Un champs requis n'a pas été rempli.",
	"miscnouserpass"	=> "Nom d'usager ou mot de passe invalide.",
	"miscselfremove"	=> "Vous ne pouvez pas supprimer votre compte.",
	"miscuserexist"		=> "Ce nom d'usager existe déjà.",
	"miscnofinduser"	=> "Usager non trouvé.",
	"extract_noarchive" => "Ce fichier n'est pas une archive extractible.",
	"extract_unknowntype" => "Type Archive Inconnue"
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "CHANGER LES PERMISSIONS",
	"editlink"		=> "ÉDITER",
	"downlink"		=> "TÉLÉCHARGER",
	"uplink"		=> "DOSSIER PARENT",
	"homelink"		=> "HOME",
	"reloadlink"		=> "RAFRAÎCHIR",
	"copylink"		=> "COPIER",
	"movelink"		=> "DÉPLACER",
	"dellink"		=> "SUPPRIMER",
	"comprlink"		=> "ARCHIVER",
	"adminlink"		=> "ADMINISTRATION",
	"logoutlink"		=> "DÉCONNECTER",
	"uploadlink"		=> "ENVOYER",
	"searchlink"		=> "RECHERCHER",
	"extractlink"		=> "Extractaction de l'archive",
	"jumpbottom"    => "Jump To Bottom",
	"jumptop"       => "Jump To Top",
	"editfile"		=> "Edit File",
	"closewindow"   => "Close Window",
	'chmodlink'		=> 'Changer (chmod) Droits (Répertoire/Fichier(s))', // new mic
	'mossysinfolink'	=> $_VERSION->PRODUCT.' Informations Système ('.$_VERSION->PRODUCT.', Server, PHP, mySQL)', // new mic
	'logolink'		=> 'Aller au site web de joomlaXplorer (nouvelle fenêtre)', // new mic
	
	// list
	"nameheader"		=> "Nom",
	"sizeheader"		=> "Taille",
	"typeheader"		=> "Type",
	"modifheader"		=> "Modifié",
	"permheader"		=> "Perm's",
	"actionheader"		=> "Actions",
	"pathheader"		=> "Chemin",
	
	// buttons
	"btncancel"		=> "Annuler",
	"btnsave"		=> "Sauver",
	"btnchange"		=> "Changer",
	"btnreset"		=> "Réinitialiser",
	"btnclose"		=> "Fermer",
	"btncreate"		=> "Créer",
	"btnsearch"		=> "Chercher",
	"btnupload"		=> "Envoyer",
	"btncopy"		=> "Copier",
	"btnmove"		=> "Déplacer",
	"btnlogin"		=> "Connecter",
	"btnlogout"		=> "Déconnecter",
	"btnadd"		=> "Ajouter",
	"btnedit"		=> "Éditer",
	"btnremove"		=> "Supprimer",
	
	// user messages, new in joomlaXplorer 1.3.0
	'renamelink'		=> "Renommer",
	'confirm_delete_file' => 'Êtes-vous sûr de vouloir supprimer ce fichier? \\n%s',
	'success_delete_file' => 'Fichier supprimé avec succes.',
	'success_rename_file' => 'Le répertoire/fichier %s a été renommé %s.',
	
	
	// actions
	"actdir"		=> "Répertoire",
	"actperms"		=> "Changer les permissions",
	"actedit"		=> "Éditer le fichier",
	"actsearchresults"	=> "Résultats de la recherche",
	"actcopyitems"		=> "Copier le(s) item(s)",
	"actcopyfrom"		=> "Copier de /%s à /%s ",
	"actmoveitems"		=> "Déplacer le(s) item(s)",
	"actmovefrom"		=> "Déplacer de /%s à /%s ",
	"actlogin"		=> "Connecter",
	"actloginheader"	=> "Connecter pour utiliser QuiXplorer",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "Changer le mot de passe",
	"actusers"		=> "Usagers",
	"actarchive"		=> "Archiver le(s) item(s)",
	"actupload"		=> "Envoyer le(s) fichier(s)",
	
	// misc
	"miscitems"		=> "Item(s)",
	"miscfree"		=> "Disponible",
	"miscusername"		=> "Usager",
	"miscpassword"		=> "Mot de passe",
	"miscoldpass"		=> "Ancien mot de passe",
	"miscnewpass"		=> "Nouveau mot de passe",
	"miscconfpass"		=> "Confirmer le mot de passe",
	"miscconfnewpass"	=> "Confirmer le nouveau mot de passe",
	"miscchpass"		=> "Changer le mot de passe",
	"mischomedir"		=> "Répertoire home",
	"mischomeurl"		=> "URL home",
	"miscshowhidden"	=> "Voir les items cachés",
	"mischidepattern"	=> "Cacher pattern",
	"miscperms"		=> "Permissions",
	"miscuseritems"		=> "(nom, répertoire home, Voir les items cachés, permissions, actif)",
	"miscadduser"		=> "Ajouter un usager",
	"miscedituser"		=> "Éditer l'usager '%s'",
	"miscactive"		=> "Actif",
	"misclang"		=> "Langage",
	"miscnoresult"		=> "Aucun résultats.",
	"miscsubdirs"		=> "Rechercher dans les sous-répertoires",
	"miscpermnames"		=> array("Lecture seulement","Modifier","Changement le mot de passe","Modifier & Changer le mot de passe",
					"Administrateur"),
	"miscyesno"		=> array("Oui","Non","O","N"),
	"miscchmod"		=> array("Propriétaire", "Groupe", "Publique"),
	// from here all new by mic
	'miscowner'			=> 'Propriétaire',
	'miscownerdesc'		=> '<strong>Description:</strong><br />Propriétaire (UID) /<br />Groupe (GID)<br />Droits actuels:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>',

	// sysinfo (new by mic)
	'simamsysinfo'		=> $_VERSION->PRODUCT.' Informations Système',
	'sisysteminfo'		=> 'Info Système',
	'sibuilton'			=> 'OS',
	'sidbversion'		=> 'Version Base de Données (MySQL)',
	'siphpversion'		=> 'Version PHP',
	'siphpupdate'		=> 'INFORMATION: <span style="color: red;">La version de PHP que vous utilisez n\'est <strong>plus</strong> d\'actualité!</span><br />Afin de garantir un fonctionnement maximum de '.$_VERSION->PRODUCT.' et addons,<br />Vous devez utiliser au minimum <strong>PHP.Version 4.3</strong>!',
	'siwebserver'		=> 'Webserver',
	'siwebsphpif'		=> 'WebServer - Interface PHP',
	'simamboversion'	=> ' Version'.$_VERSION->PRODUCT,
	'siuseragent'		=> 'Version du Navigateur',
	'sirelevantsettings' => 'Paramètres PHP avancés',
	'sisafemode'		=> 'Safe Mode',
	'sibasedir'			=> 'Ouvrir répertoire de base',
	'sidisplayerrors'	=> 'Erreurs PHP',
	'sishortopentags'	=> 'Short Open Tags',
	'sifileuploads'		=> 'Date Uploads',
	'simagicquotes'		=> 'Magic Quotes',
	'siregglobals'		=> 'Register Globals',
	'sioutputbuf'		=> 'Output Buffer',
	'sisesssavepath'	=> 'Session Savepath',
	'sisessautostart'	=> 'Session auto start',
	'sixmlenabled'		=> 'XML activé',
	'sizlibenabled'		=> 'ZLIB activé',
	'sidisabledfuncs'	=> 'Non enabled functions',
	'sieditor'			=> 'Editeur WYSIWYG',
	'siconfigfile'		=> 'Config file',
	'siphpinfo'			=> 'PHP Info',
	'siphpinformation'	=> 'PHP Information',
	'sipermissions'		=> 'Permissions',
	'sidirperms'		=> 'Permissions Répertoire',
	'sidirpermsmess'	=> 'Pour obtenir un fonctionnement correcte de '.$_VERSION->PRODUCT.' assurez vous que vous possèdez les droits en écriture sur l\'ensemble des répertoires. [chmod 0777]',
	'sionoff'			=> array( 'On', 'Off' ),
	
	'extract_warning' => "Voulez-vous réellement extraire ce fichier Ici?\\nCe fichier écrasera le fichier existant!",
	'extract_success' => "Extraction réussi",
	'extract_failure' => "Extraction échouée",
	
	'overwrite_files' => 'Écraser le(s) fichier(s) existant(s)?',
	"viewlink"		=> "APERÇU",
	"actview"		=> "Aperçu des sources du fichier",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Récursif dans les sous-répertoires?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'	=> 'Vérifier si une version supérieure existe',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'	=>	'Renommer le répertoire ou fichier...',
	'newname'		=>	'Nouveau nom',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'	=>	'Retourner au répertoire après sauvegarde?',
	'line'		=> 	'Ligne',
	'column'	=>	'Colonne',
	'wordwrap'	=>	'Wordwrap: (IE seulement)',
	'copyfile'	=>	'Copier fichier avec ce nom de fichier',
	
	// Bookmarks
	'quick_jump' => 'Saut rapide vers',
	'already_bookmarked' => 'Ce répertoire existe déjà dans le signet',
	'bookmark_was_added' => 'Répertoire ajouté à la liste de signet.',
	'not_a_bookmark' => 'Ce répertoire n\'est pas un signet.',
	'bookmark_was_removed' => 'Ce répertoire à été supprimé de la list de signet.',
	'bookmarkfile_not_writable' => "Échec lors de %s dans le signet.\n Le fichier signet '%s' \nn\'est pas accessible en écriture.",
	
	'lbl_add_bookmark' => 'Ajouter ce répertoire au signet',
	'lbl_remove_bookmark' => 'Supprimer ce répertoire de la liste de signet',
	
	'enter_alias_name' => 'SVP, entrez un alias pour ce signet',
	
	'normal_compression' => 'compression normal',
	'good_compression' => 'compression moyenne',
	'best_compression' => 'compression meilleur',
	'no_compression' => 'pas de compression',
	
	'creating_archive' => 'Création du Fichier Archive...',
	'processed_x_files' => '%s of %s Fichiers traités',
	
	'ftp_header' => 'Authentification FTP Locale',
	'ftp_login_lbl' => 'SVP, entrez un login de connexion pour le serveur FTP',
	'ftp_login_name' => "Nom d'utilisateur FTP",
	'ftp_login_pass' => 'Mot de passe FTP',
	'ftp_hostname_port' => 'Nom du serveur FTP et Port <br />(Le port est optionnel)',
	'ftp_login_check' => 'Test connexion serveur FTP...',
	'ftp_connection_failed' => "Serveur FTP impossible à contacter. \nSVP, vérifiez que le service FTP est lancé sur le serveur.",
	'ftp_login_failed' => "Login FTP incorrect. SVP, Vérifiez le nom et mot de passe utilisateur et réessayez.",
		
	'switch_file_mode' => 'Mode courant: <strong>%s</strong>. Vous pouvez passer en mode %s.',
	'symlink_target' => 'Cible du lien symbolique',
	
	"permchange"		=> "CHMOD réussi:",
	"savefile"		=> "Le fichier est sauvegardé.",
	"moveitem"		=> "Déplacement réussi.",
	"copyitem"		=> "Copie réussi.",
	'archive_name' 		=> "Nom de l'archive",
	'archive_saveToDir' 	=> "Sauvez l'archive dans ce répertoire",
	
	'editor_simple'	=> 'Simple Editor Mode',
	'editor_syntaxhighlight'	=> 'Syntax-Highlighted Mode'
);
?>
