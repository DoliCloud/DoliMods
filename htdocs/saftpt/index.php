<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Mário Batista        <mariorbatista@gmail.com> ISCTE-UL Moss
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *       \file       htdocs/saftpt/index.php
 *       \brief      Home page for saf-t module
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

$langs->load("saftpt@saftpt");

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;

$action=GETPOST('action','alpha');
$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');

if(!$sortorder) $sortorder='desc'; 
if(!$sortfield) $sortfield='name'; 
/*
 * Actions
 */

if ($action == 'delete')
{
	$file=$conf->saftpt->dir_output.'/'.GETPOST('urlfile');
    $ret=dol_delete_file($file, 1);
    if ($ret) setEventMessage($langs->trans("SaftWasRemoved", GETPOST('urlfile')));
    else setEventMessage($langs->trans("ErrorFailToDeleteSaft", GETPOST('urlfile')), 'errors');
    $action='';
}

/*
 * View
 */

 llxHeader("",$langs->trans("MenuSaft"),"");

$text=$langs->trans("MenuSaft");

print_fiche_titre($text);

// Show description of content
print $langs->trans("SaftDesc").'<br><br>';
print $langs->trans("SaftDesc2").'<br><br>';
print $langs->trans("SaftDesc3").'<br><br>';


$formfile = new FormFile($db);

$filearray=dol_dir_list($conf->saftpt->dir_output.'/xml','files',0,'','',$sortfield,(strtolower($sortorder)=='asc'?SORT_ASC:SORT_DESC),1);
$result=$formfile->list_of_documents($filearray,null,'saftpt','',1,'xml/',1,0,($langs->trans("NoSaftFileAvailable").'<br>'.$langs->trans("ToBuildBackupFileClickHere",DOL_URL_ROOT.'/saftpt/exportsaft.php')),0,$langs->trans("PreviousDumpFiles"));


llxFooter();

$db->close();
?>
