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
 *      \version    $Id: probes.php,v 1.9 2011/04/13 16:30:48 eldy Exp $
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
if ($action == 'confirm_deleteprobe' && ! GETPOST('cancel'))
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

if ($action == 'modify' && ! GETPOST('cancel'))
{
    $probe=new Monitoring_probes($db);
    $result=$probe->fetch($id);
    $probe->title=GETPOST('probe_title');
    $probe->url=GETPOST('probe_url');
    $probe->useproxy=GETPOST('probe_useproxy');
    $probe->checkkey=GETPOST('probe_checkkey');
    $probe->maxvalue=GETPOST('probe_maxvalue');
    $probe->frequency=GETPOST('probe_frequency');
    $probe->useproxy=GETPOST('probe_useproxy');
    $probe->active=GETPOST('probe_active');

    $db->begin();

    $result=$probe->update();

    if ($result > 0)
    {
        $db->commit();
        //$mesg='<div class="ok">'.$langs->trans("Success").'</div>';
        header("Location: ".$_SERVER["PHP_SELF"].'?id='.$id);
        exit;
    }
    else
    {
        $db->rollback();
        $mesg='<div class="error">'.$probe->error.'</div>';
        $action='edit';
    }
}

if ($action == 'add' && ! GETPOST('cancel'))
{
    $probe=new Monitoring_probes($db);

    $probe->title=GETPOST('probe_title');
    $probe->url=GETPOST('probe_url');
    $probe->useproxy=GETPOST('probe_useproxy');
    $probe->checkkey=GETPOST('probe_checkkey');
    $probe->maxvalue=GETPOST('probe_maxvalue');
    $probe->frequency=GETPOST('probe_frequency');
    $probe->useproxy=GETPOST('probe_useproxy');
    $probe->active=GETPOST('probe_active');

    $db->begin();

    $result=$probe->create();

    if ($result > 0)
    {
        $db->commit();
        //$mesg='<div class="ok">'.$langs->trans("Success").'</div>';
        header("Location: ".$_SERVER["PHP_SELF"].'?id='.$id);
        exit;
    }
    else
    {
        $db->rollback();
        $mesg='<div class="error">'.$probe->error.'</div>';
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

if ($mesg) print $mesg;


if ($action != 'edit')
{
    $var=false;

    // Formulaire ajout
    print_titre($langs->trans("AddProbe"));

    print '<form name="addnewprobe" action="'.$_SERVER["PHP_SELF"].'" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

    print '<table class="nobordernopadding" width="100%">';

    print '<tr class="liste_titre">';
    print '<td colspan="2">'.$langs->trans("Parameters").'</td>';
    print '<td>'.$langs->trans("Example").'</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td width="200">'.$langs->trans("Title").'</td>';
    print '<td><input type="text" name="probe_title" value="" size="64"></td>';
    print '<td>My web site</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("URL").'</td>';
    print '<td><input type="text" name="probe_url" value="" size="64"></td>';
    print '<td>http://mywebsite.com/mylogonpage.php</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("UseProxy").'</td>';
    print '<td>';
    print $html->selectyesno('probe_useproxy',isset($_POST['probe_useproxy'])?$_POST['probe_useproxy']:0,1);
    print '</td>';
    print '<td>'.
    $html->textwithhelp(img_help(),$langs->trans("SetToYesToUseGlobalProxySetup"));
    print '</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("CheckKey").'</td>';
    print '<td><input type="text" name="probe_checkkey" value="" size="64"></td>';
    print '<td>Welcome</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("MaxValue").'</td>';
    print '<td><input type="text" name="probe_maxvalue" value="" size="2"></td>';
    print '<td>1000</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("Frequency").'</td>';
    print '<td><input type="text" name="probe_frequency" value="" size="2"> '.$langs->trans("seconds").'</td>';
    print '<td>5</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("Activable").'</td>';
    print '<td>';
    print $html->selectyesno('probe_active',isset($_POST['probe_active'])?$_POST['probe_active']:0,1);
    print '</td>';
    print '<td>&nbsp;</td>';
    print '</tr>';

    print '<tr><td colspan="3" align="center">';
    if ($user->rights->monitoring->create)
    {
        print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
        print '<input type="hidden" name="action" value="add">';
    }
    else
    {
        print '<input type="submit" class="button" disabled="disabled" value="'.$langs->trans("Add").'">';
    }
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
    print "<td>".$langs->trans("Proxy")."</td>";
    print "<td>".$langs->trans("CheckKey")."</td>";
    print "<td>".$langs->trans("MaxValue")."</td>";
    print "<td>".$langs->trans("Frequency")."</td>";
    print '<td align="center">'.$langs->trans("Activable")."</td>";
    //print '<td align="center">'.$langs->trans("Reports")."</td>";
    print '<td width="80px">&nbsp;</td>';
    print '</tr>';


    $sql ="SELECT rowid, title, url, useproxy, checkkey, maxvalue, frequency, status, active";
    $sql.=" FROM ".MAIN_DB_PREFIX."monitoring_probes";
    $sql.=" ORDER BY rowid";

    dol_syslog("probes sql=".$sql,LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
    	$num =$db->num_rows($resql);
    	$i=0;
    	$var=true;

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
            print "<td>".yn($obj->useproxy)."</td>";
            print "<td>".$obj->checkkey."</td>";
            print "<td>".$obj->maxvalue."</td>";
            print "<td>".$obj->frequency."</td>";
            print '<td align="center">'.yn($obj->active)."</td>";
            /*print '<td align="center">';
            if ($obj->active)
            {
                print '<a href="index.php?id='.$obj->rowid.'">'.$langs->trans("Reports").'</a>';
            }
            print '</td>';*/
            print '<td align="right">';
            if ($user->rights->monitoring->create)
            {
                print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$obj->rowid.'&amp;action=edit">';
                print img_edit();
                print '</a>';
                print '&nbsp;';
                print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$obj->rowid.'&amp;action=ask_deleteline">';
                print img_delete();
                print '</a>';
            }
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
}
else
{
    $probe=new Monitoring_probes($db);
    $result=$probe->fetch($id);

    print_titre($langs->trans("EditProbe"));


    print '<form name="editprobe" action="'.$_SERVER["PHP_SELF"].'" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="id" value="'.$id.'">';

    print '<table class="nobordernopadding" width="100%">';

    print '<tr class="liste_titre">';
    print '<td colspan="2">'.$langs->trans("Parameters").'</td>';
    print '<td>'.$langs->trans("Example").'</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td width="200">'.$langs->trans("Title").'</td>';
    print '<td><input type="text" name="probe_title" value="'.($_POST['probe_title']?$_POST['probe_title']:$probe->title).'" size="64"></td>';
    print '<td>My web site</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("URL").'</td>';
    print '<td><input type="text" name="probe_url" value="'.($_POST['probe_url']?$_POST['probe_url']:$probe->url).'" size="64"></td>';
    print '<td>http://mywebsite.com/mylogonpage.php</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("UseProxy").'</td>';
    print '<td>';
    print $html->selectyesno('probe_useproxy',isset($_POST['probe_useproxy'])?$_POST['probe_useproxy']:$probe->useproxy,1);
    print '</td>';
    print '<td>'.
    $html->textwithhelp(img_help(),$langs->trans("SetToYesToUseGlobalProxySetup"));
    print '</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("CheckKey").'</td>';
    print '<td><input type="text" name="probe_checkkey" value="'.($_POST['probe_checkkey']?$_POST['probe_checkkey']:$probe->checkkey).'" size="64"></td>';
    print '<td>Welcome</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("MaxValue").'</td>';
    print '<td><input type="text" name="probe_maxvalue" value="'.($_POST['probe_maxvalue']?$_POST['probe_maxvalue']:$probe->maxvalue).'" size="2"></td>';
    print '<td>1000</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("Frequency").'</td>';
    print '<td><input type="text" name="probe_frequency" value="'.($_POST['probe_frequency']?$_POST['probe_frequency']:$probe->frequency).'" size="2"> '.$langs->trans("seconds").'</td>';
    print '<td>5</td>';
    print '</tr>';

    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("Activable").'</td>';
    print '<td>';
    print $html->selectyesno('probe_active',isset($_POST['probe_active'])?$_POST['probe_active']:$probe->active,1);
    print '</td>';
    print '<td>&nbsp;</td>';
    print '</tr>';

    print '<tr><td colspan="3" align="center">';
    if ($user->rights->monitoring->create)
    {
        print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
        print ' &nbsp; &nbsp; ';
        print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
    }
    print '<input type="hidden" name="action" value="modify">';
    print '</td></tr>';

    print '</table>';
    print '</form>';

}


$db->close();

llxFooter('$Date: 2011/04/13 16:30:48 $ - $Revision: 1.9 $');
?>
