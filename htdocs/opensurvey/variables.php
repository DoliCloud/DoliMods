<?php
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Auteur : Guilhem BORGHESI
//Création : Février 2008
//
//borghesi@unistra.fr
//
//Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
//respectant les principes de diffusion des logiciels libres. Vous pouvez
//utiliser, modifier et/ou redistribuer ce programme sous les conditions
//de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA
//sur le site "http://www.cecill.info".
//
//Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
//pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
//termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
//
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Author : Guilhem BORGHESI
//Creation : Feb 2008
//
//borghesi@unistra.fr
//
//This software is governed by the CeCILL-B license under French law and
//abiding by the rules of distribution of free software. You can  use,
//modify and/ or redistribute the software under the terms of the CeCILL-B
//license as circulated by CEA, CNRS and INRIA at the following URL
//"http://www.cecill.info".
//
//The fact that you are presently reading this means that you have had
//knowledge of the CeCILL-B license and that you accept its terms. You can
//find a copy of this license in the file LICENSE.
//
//==========================================================================


// Define $urlwithroot
//$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
//$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
$urlwithroot=DOL_MAIN_URL_ROOT;						// This is to use same domain name than current
$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',$urlwithroot);


$langs->load("opensurvey@opensurvey");


// Nom du serveur
define('STUDS_URL', preg_replace('/^http(s*):/','',$urlwithouturlroot));

// Répertoire sous lequel se trouve Studs
// Utilise la racine du serveur telle que définie par STUDS_URL si laissé vide
define('STUDS_DIR', '');

// Nom de l'application
define('NOMAPPLICATION', $langs->trans("DolibarrSurvey"));

// adresse mail de l'administrateur de la base
define('ADRESSEMAILADMIN', $mysoc->email);

// nom de la base de donnees
define('BASE', $conf->db->name);

// nom de l'utilisateur de la base
define('USERBASE', $conf->db->user);

// passwd de l'utilisateur de la base
define('USERPASSWD', $conf->db->pass);

// nom du serveur de base de donnees, laisser vide pour utiliser un socket
define('SERVEURBASE', $conf->db->host);

// Type de base de données à utiliser (mysql, postgres, ...)
// http://phplens.com/lens/adodb/docs-adodb.htm#drivers
define('BASE_TYPE', $conf->db->type);

// Langue par défaut de l'application (à choisir dans $ALLOWED_LANGUAGES)
define('LANGUE', $langs->defaultlang);

?>
