<?php
/* Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2003,2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *      \file       htdocs/submiteverywhere/admin/submiteverywheresetuppage.php
 *      \ingroup    submiteverywhere
 *      \brief      Page to setup module SubmitEverywhere
 *      \version    $Id: submiteverywheresetuppage.php,v 1.6 2011/03/29 23:17:21 eldy Exp $
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");


$langs->load("admin");
$langs->load("submiteverywhere@submiteverywhere");

if (!$user->admin) accessforbidden();

$mesg='';
$error=0;


/*
 * Action
 */

if ($_POST["action"] == 'add' || $_POST["modify"])
{
    if (! GETPOST('label'))
    {
        $error++;
        $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Label")).'</div>';
    }
    if (! GETPOST('type'))
    {
        $error++;
        $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")).'</div>';
    }

    if (! $error)
    {
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."submitew_targets (label,targetcode,langcode)";
    	$sql.= " VALUES ('".$db->escape(GETPOST('label'))."','".$db->escape(GETPOST('type'))."', '".$db->escape(GETPOST('lang_id'))."')";
        $resql=$db->query($sql);
    	if ($resql)
        {
            $_POST['label']='';
            $_POST['type']='';
        }
        else
        {
            if ($db->lasterrno == 'DB_ERROR_RECORD_ALREADY_EXISTS')
            {
                $langs->load("errors");
                $mesg='<div class="error">'.$langs->trans("ErrorRefAlreadyExists").'</div>';
            }
            else  {
            	dol_print_error($db);
                $error++;
            }
        }
    }
}

if (GETPOST("action")=='delete')
{
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."submitew_targets";
    $sql.= " WHERE rowid = ".GETPOST('id','int',1);
	$resql=$db->query($sql);
	if ($resql)
	{
	}
	else
	{
	    dol_print_error($db);
	    $error++;
	}
}


/*
 * View
 */

$htmladmin=new FormAdmin($db);

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SubmitEveryWhereSetup"), $linkback, 'setup');
print '<br>';

print $langs->trans("DescSubmitEveryWhere").'<br><br>'."\n";

// Form to add entry
print '<form name="externalrssconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

print '<table class="nobordernopadding" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Label").'</td>';
print '<td align="left">'.$langs->trans("TargetType").'</td>';
print '<td align="left">'.$langs->trans("TargetLang").'</td>';
print '</tr>';

print '<tr class="liste_titre">';
// Label
print '<td width="200">';
print '<input type="text" name="label" value="'.($_POST["label"]?$_POST["label"]:'').'">';
print '</td>';
// Type
print '<td width="200" align="left">';
print '<select class="flat" name="type">';
print '<option value="">&nbsp;</option>';
print '<option value="dig">'.$langs->trans("Dig").'</option>';
print '<option value="email">'.$langs->trans("Email").'</option>';
print '<option value="facebook">'.$langs->trans("Facebook").'</option>';
print '<option value="linkedin">'.$langs->trans("LinkedIn").'</option>';
print '<option value="twitter">'.$langs->trans("Twitter").'</option>';
print '<option value="web">'.$langs->trans("GenericWebSite").'</option>';
print '</select>';
print '</td>';
// Language
print '<td align="left">';
print $htmladmin->select_language($langs->defaultlang);
print '</td>';

print '</tr>';

print '<tr><td colspan="3" align="center"><br>';
print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
print '<input type="hidden" name="action" value="add">';
print '</td>';
print '</tr>';

print '</table>';
print '</form>';


print '<br>';


if ($mesg) print $mesg.'<br>';




print_fiche_titre($langs->trans("Targets"),'','');

print '<table class="nobordernopadding" width="100%">';

$sql ="SELECT rowid, label, targetcode, langcode, url, login, pass, comment, position";
$sql.=" FROM ".MAIN_DB_PREFIX."submitew_targets";
$sql.=" ORDER BY label";

dol_syslog("Get list of targets sql=".$sql,LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
	$num =$db->num_rows($resql);
	$i=0;

    print '<tr class="liste_titre">';
    print '<td width="200">'.$langs->trans("Label").'</td>';
    print '<td width="200" align="left">'.$langs->trans("TargetType").'</td>';
    print '<td align="left">'.$langs->trans("TargetLang").'</td>';
    print '<td align="left">'.$langs->trans("Parameters").'</td>';
    print '<td align="left" width="16px">&nbsp;</td>';
    print '</tr>';

	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);

		print "<form name=\"externalrssconfig\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

		$var=!$var;
		print "<tr ".$bc[$var].">";
        print '<td>'.$obj->label.'</td>';
        print '<td>'.$obj->targetcode.'</td>';
        print '<td>'.$obj->langcode.'</td>';
        print '<td>';
        // TODO Add edit parameter area according to type


        print '</td>';
        print '<td><a href="'.$_SERVER["PHP_SELF"].'?action=delete&id='.$obj->rowid.'">'.img_picto($langs->trans("Delete"),'delete').'</a>';
        print "</tr>";

		print "</form>";

		$i++;
	}
}
else
{
	dol_print_error($db);
}

print '</table>'."\n";


$db->close();

llxFooter('$Date: 2011/03/29 23:17:21 $ - $Revision: 1.6 $');
?>
