<?php
/* Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2003,2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
 * Copyright (C) 2005-2011 Regis Houssin        <regis@dolibarr.fr>
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
 *      \file       htdocs/monitoring/probes.php
 *      \ingroup    monitoring
 *      \brief      Page to add probes
 *      \version    $Id: probes.php,v 1.2 2011/03/08 23:52:19 eldy Exp $
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
dol_include_once("/monitoring/lib/monitoring.lib.php"); // We still use old writing to be compatible with old version
dol_include_once("/monitoring/class/monitoring_probes.class.php"); // We still use old writing to be compatible with old version

$langs->load("admin");
$langs->load("monitoring@monitoring");

if (!$user->rights->monitoring->read)
accessforbidden();

$def = array();
$action=GETPOST('action');
$id=GETPOST('id');


/*
 * Actions
 */
if ($action == 'confirm_deleteprobe' && ! $_POST['cancel'])
{
    $probe=new Monitoring_probes($db);
    $result=$probe->fetch($id);

    $db->begin();

    $result=$probe->delete();

    if ($result > 0)
    {
        $db->commit();
        //$mesg='<div class="ok">'.$langs->trans("Success").'</div>';
        header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        $db->rollback();
        dol_print_error($db);
    }
}

if ($action == 'add' || $_POST["modify"])
{
	// Add entry
    $sql = "INSERT INTO ".MAIN_DB_PREFIX."monitoring_probes (title, url, checkkey, frequency, status)";
	$sql.= ' VALUES ("'.$db->escape($_POST['probe_title']).'", "'.$db->escape($_POST["probe_url"]).'",';
	$sql.= ' "'.$db->escape($_POST["probe_checkkey"]).'", "'.$db->escape($_POST["probe_frequency"]).'", 1)';
    $resql=$db->query($sql);

    if ($resql)
    {
        $db->commit();
  		//$mesg='<div class="ok">'.$langs->trans("Success").'</div>';
        header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        $db->rollback();
        dol_print_error($db);
    }
}



/*
 * View
 */

$html=new Form($db);

llxHeader();

$linkback='';
//$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ProbeSetup"), $linkback, 'setup');
print '<br>';

// Formulaire ajout

print_titre($langs->trans("AddProbe"));


print '<form name="addnewprobe" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

print '<table class="nobordernopadding" width="100%">';

print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("Parameters").'</td>';
print '<td>'.$langs->trans("Example").'</td>';
print '</tr>';
print '<tr class="impair">';
print '<td width="100">'.$langs->trans("Title").'</td>';
print '<td><input type="text" name="probe_title" value="" size="64"></td>';
print '<td>My web site</td>';
print '</tr>';

print '<tr class="pair">';
print '<td>'.$langs->trans("URL").'</td>';
print '<td><input type="text" name="probe_url" value="" size="64"></td>';
print '<td>http://mywebsite.com/mylogonpage.php</td>';
print '</tr>';

print '<tr class="impair">';
print '<td>'.$langs->trans("CheckKey").'</td>';
print '<td><input type="text" name="probe_checkkey" value="" size="64"></td>';
print '<td>Welcome</td>';
print '</tr>';

print '<tr class="pair">';
print '<td>'.$langs->trans("Frequency").'</td>';
print '<td><input type="text" name="probe_frequency" value="" size="2"> '.$langs->trans("seconds").'</td>';
print '<td>5</td>';
print '</tr>';

print '<tr><td colspan="3" align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
print '<input type="hidden" name="action" value="add">';
print '</td></tr>';

print '</table>';
print '</form>';

print '<br>';


print_titre($langs->trans("ListOfProbes"));

// Confirmation de la suppression d'une ligne produit
if ($action == 'ask_deleteline')
{
    $ret=$html->form_confirm($_SERVER["PHP_SELF"].'?id='.$_GET["id"], $langs->trans('DeleteProbe'), $langs->trans('ConfirmDeleteProbe'), 'confirm_deleteprobe', '', 'no', 1);
    if ($ret == 'html') print '<br>';
}


print '<table class="nobordernopadding" width="100%">';

print '<tr class="liste_titre">';
print "<td>".$langs->trans("Id")."</td>";
print "<td>".$langs->trans("Title")."</td>";
print "<td>".$langs->trans("URL")."</td>";
print "<td>".$langs->trans("CheckKey")."</td>";
print "<td>".$langs->trans("Frequency")."</td>";
print '<td align="center">'.$langs->trans("Active")."</td>";
print '<td align="center">'.$langs->trans("Reports")."</td>";
print '<td width="80px">&nbsp;</td>';
print '</tr>';


$sql ="SELECT rowid, title, url, checkkey, frequency, status FROM ".MAIN_DB_PREFIX."monitoring_probes";
$sql.=" ORDER BY rowid";

dol_syslog("probes sql=".$sql,LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
	$num =$db->num_rows($resql);
	$i=0;

	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);

		print "<form name=\"externalrssconfig\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

        $var=!$var;
		print "<tr ".$bc[$var].">";
        print "<td>".$obj->rowid."</td>";
		print "<td>".$obj->title."</td>";
        print "<td>".$obj->url."</td>";
        print "<td>".$obj->checkkey."</td>";
        print "<td>".$obj->frequency."</td>";
        print '<td align="center">'.yn($obj->status)."</td>";
        print '<td align="center"><a href="index.php?id='.$obj->rowid.'">'.$langs->trans("Reports")."</a></td>";
        print '<td align="center"><a href="'.$_SERVER["PHP_SELF"].'?id='.$obj->rowid.'&amp;action=ask_deleteline">';
        print img_delete();
        print '</a>';
        print '</td>';
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

llxFooter('$Date: 2011/03/08 23:52:19 $ - $Revision: 1.2 $');
?>
