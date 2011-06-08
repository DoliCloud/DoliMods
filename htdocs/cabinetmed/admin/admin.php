<?php
/* Copyright (C) 2003-2004 Rodolphe Quiedeville         <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur          <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Eric Seigne                  <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2009 Regis Houssin                <regis@dolibarr.fr>
 * Copyright (C) 2008      Raphael Bertrand (Resultic)  <raphael.bertrand@resultic.fr>
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
 *      \file       htdocs/admin/facture.php
 *      \ingroup    facture
 *      \brief      Page to setup invoice module
 *      \version    $Id: admin.php,v 1.3 2011/06/08 16:42:54 eldy Exp $
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
include_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
include_once("../lib/cabinetmed.lib.php");

$langs->load("admin");
$langs->load("companies");
$langs->load("bills");
$langs->load("other");
$langs->load("errors");

if (!$user->admin)
accessforbidden();

$typeconst=array('yesno','texte','chaine');


/*
 * Actions
 */

if ($_POST["action"] == 'updateMask')
{
    $maskconstinvoice=$_POST['maskconstinvoice'];
    $maskconstcredit=$_POST['maskconstcredit'];
    $maskinvoice=$_POST['maskinvoice'];
    $maskcredit=$_POST['maskcredit'];
    if ($maskconstinvoice) dolibarr_set_const($db,$maskconstinvoice,$maskinvoice,'chaine',0,'',$conf->entity);
    if ($maskconstcredit)  dolibarr_set_const($db,$maskconstcredit,$maskcredit,'chaine',0,'',$conf->entity);
}

if ($_GET["action"] == 'specimen')
{
    $modele=$_GET["module"];

    $facture = new Facture($db);
    $facture->initAsSpecimen();

    // Load template
    $dir = dol_buildpath("/includes/modules/cabinetmed/doc/");
    $file = "pdf_".$modele.".modules.php";
    if (file_exists($dir.$file))
    {
        $classname = "pdf_".$modele;
        require_once($dir.$file);

        $obj = new $classname($db);

        if ($obj->write_file($facture,$langs) > 0)
        {
            header("Location: ".DOL_URL_ROOT."/document.php?modulepart=cabinetmed&file=SPECIMEN.pdf");
            return;
        }
        else
        {
            $mesg='<div class="error">'.$obj->error.'</div>';
            dol_syslog($obj->error, LOG_ERR);
        }
    }
    else
    {
        $mesg='<div class="error">'.$langs->trans("ErrorModuleNotFound").'</div>';
        dol_syslog($langs->trans("ErrorModuleNotFound"), LOG_ERR);
    }
}

// define constants for models generator that need parameters
if ($_POST["action"] == 'setModuleOptions')
{
    $post_size=count($_POST);
    for($i=0;$i < $post_size;$i++)
    {
        if (array_key_exists('param'.$i,$_POST))
        {
            $param=$_POST["param".$i];
            $value=$_POST["value".$i];
            if ($param) dolibarr_set_const($db,$param,$value,'chaine',0,'',$conf->entity);
        }
    }
}

if ($_GET["action"] == 'set')
{
    $type='invoice';
    $sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
    $sql.= " VALUES ('".$db->escape($_GET["value"])."','".$type."',".$conf->entity.", ";
    $sql.= ($_GET["label"]?"'".$db->escape($_GET["label"])."'":'null').", ";
    $sql.= (! empty($_GET["scandir"])?"'".$db->escape($_GET["scandir"])."'":"null");
    $sql.= ")";
    if ($db->query($sql))
    {

    }
}

if ($_GET["action"] == 'del')
{
    $type='invoice';
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
    $sql.= " WHERE nom = '".$_GET["value"]."'";
    $sql.= " AND type = '".$type."'";
    $sql.= " AND entity = ".$conf->entity;

    if ($db->query($sql))
    {

    }
}

if ($_GET["action"] == 'setdoc')
{
    $db->begin();

    if (dolibarr_set_const($db, "CABINETMED_ADDON_PDF",$_GET["value"],'chaine',0,'',$conf->entity))
    {
        $conf->global->FACTURE_ADDON_PDF = $_GET["value"];
    }

    // On active le modele
    $type='invoice';

    $sql_del = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
    $sql_del.= " WHERE nom = '".$db->escape($_GET["value"])."'";
    $sql_del.= " AND type = '".$type."'";
    $sql_del.= " AND entity = ".$conf->entity;
    dol_syslog("facture.php ".$sql_del);
    $result1=$db->query($sql_del);

    $sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity, libelle, description)";
    $sql.= " VALUES ('".$_GET["value"]."', '".$type."', ".$conf->entity.", ";
    $sql.= ($_GET["label"]?"'".$db->escape($_GET["label"])."'":'null').", ";
    $sql.= (! empty($_GET["scandir"])?"'".$_GET["scandir"]."'":"null");
    $sql.= ")";
    dol_syslog("facture.php ".$sql);
    $result2=$db->query($sql);
    if ($result1 && $result2)
    {
        $db->commit();
    }
    else
    {
        dol_syslog("facture.php ".$db->lasterror(), LOG_ERR);
        $db->rollback();
    }
}

if ($_POST["action"] == 'update' || $_POST["action"] == 'add')
{
    if (! dolibarr_set_const($db, $_POST["constname"],$_POST["constvalue"],$typeconst[$_POST["consttype"]],0,isset($_POST["constnote"])?$_POST["constnote"]:'',$conf->entity));
    {
        dol_print_error($db);
    }
}

if ($_GET["action"] == 'delete')
{
    if (! dolibarr_del_const($db, $_GET["rowid"],$conf->entity));
    {
        dol_print_error($db);
    }
}


/*
 * View
 */

llxHeader("",$langs->trans("CabinetMedSetup"),'');

$html=new Form($db);


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("CabinetMedSetup"),$linkback,'setup');
print '<br>';


/*
$h = 0;

$head[$h][0] = DOL_URL_ROOT."/admin/facture.php";
$head[$h][1] = $langs->trans("Invoices");
$hselected=$h;
$h++;

dol_fiche_head($head, $hselected, $langs->trans("ModuleSetup"));
*/


/*
 *  Document templates generators
 */
//print '<br>';
print_titre($langs->trans("CabinetMedPDFModules"));

// Load array def with activated templates
$def = array();
$sql = "SELECT nom";
$sql.= " FROM ".MAIN_DB_PREFIX."document_model";
$sql.= " WHERE type = 'cabinetmed'";
$sql.= " AND entity = ".$conf->entity;
$resql=$db->query($sql);
if ($resql)
{
    $i = 0;
    $num_rows=$db->num_rows($resql);
    while ($i < $num_rows)
    {
        $array = $db->fetch_array($resql);
        array_push($def, $array[0]);
        $i++;
    }
}
else
{
    dol_print_error($db);
}

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="60">'.$langs->trans("Default").'</td>';
print '<td align="center" width="32" colspan="2">'.$langs->trans("Infos").'</td>';
print "</tr>\n";

clearstatcache();


$var=true;
foreach ($conf->file->dol_document_root as $dirroot)
{
    foreach (array('','/doc') as $valdir)
    {
        $dir = $dirroot . "/includes/modules/facture".$valdir;

        if (is_dir($dir))
        {
            $handle=opendir($dir);
            if (is_resource($handle))
            {
                while (($file = readdir($handle))!==false)
                {
                    $filelist[]=$file;
                }
                closedir($handle);
                //sort($filelist);
                //var_dump($filelist);

                foreach($filelist as $file)
                {
                    if (preg_match('/\.modules\.php$/i',$file) && preg_match('/^(pdf_|doc_)/',$file))
                    {
                        if (file_exists($dir.'/'.$file))
                        {
                            $name = substr($file, 4, dol_strlen($file) -16);
                            $classname = substr($file, 0, dol_strlen($file) -12);

                            require_once($dir.'/'.$file);
                            $module = new $classname($db);

                            $modulequalified=1;
                            if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) $modulequalified=0;
                            if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) $modulequalified=0;

                            if ($modulequalified)
                            {
                                $var = !$var;
                                print '<tr '.$bc[$var].'><td width="100">';
                                print (empty($module->name)?$name:$module->name);
                                print "</td><td>\n";
                                if (method_exists($module,'info')) print $module->info($langs);
                                else print $module->description;
                                print '</td>';

                                // Active
                                if (in_array($name, $def))
                                {
                                    print "<td align=\"center\">\n";
                                    if ($conf->global->FACTURE_ADDON_PDF != "$name")
                                    {
                                        print '<a href="'.$_SERVER["PHP_SELF"].'?action=del&amp;value='.$name.'">';
                                        print img_picto($langs->trans("Enabled"),'on');
                                        print '</a>';
                                    }
                                    else
                                    {
                                        print img_picto($langs->trans("Enabled"),'on');
                                    }
                                    print "</td>";
                                }
                                else
                                {
                                    print "<td align=\"center\">\n";
                                    print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;value='.$name.'&amp;scandir='.$module->scandir.'&amp;label='.urlencode($module->name).'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
                                    print "</td>";
                                }

                                // Defaut
                                print "<td align=\"center\">";
                                if ($conf->global->FACTURE_ADDON_PDF == "$name")
                                {
                                    print img_picto($langs->trans("Default"),'on');
                                }
                                else
                                {
                                    print '<a href="'.$_SERVER["PHP_SELF"].'?action=setdoc&amp;value='.$name.'&amp;scandir='.$module->scandir.'&amp;label='.urlencode($module->name).'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
                                }
                                print '</td>';

                                // Info
                                $htmltooltip =    ''.$langs->trans("Name").': '.$module->name;
                                $htmltooltip.='<br>'.$langs->trans("Type").': '.($module->type?$module->type:$langs->trans("Unknown"));
                                if ($module->type == 'pdf')
                                {
                                    $htmltooltip.='<br>'.$langs->trans("Height").'/'.$langs->trans("Width").': '.$module->page_hauteur.'/'.$module->page_largeur;
                                }
                                $htmltooltip.='<br><br><u>'.$langs->trans("FeaturesSupported").':</u>';
                                $htmltooltip.='<br>'.$langs->trans("Logo").': '.yn($module->option_logo,1,1);
                                $htmltooltip.='<br>'.$langs->trans("PaymentMode").': '.yn($module->option_modereg,1,1);
                                $htmltooltip.='<br>'.$langs->trans("PaymentConditions").': '.yn($module->option_condreg,1,1);
                                $htmltooltip.='<br>'.$langs->trans("Escompte").': '.yn($module->option_escompte,1,1);
                                $htmltooltip.='<br>'.$langs->trans("CreditNote").': '.yn($module->option_credit_note,1,1);
                                $htmltooltip.='<br>'.$langs->trans("MultiLanguage").': '.yn($module->option_multilang,1,1);
                                $htmltooltip.='<br>'.$langs->trans("WatermarkOnDraftInvoices").': '.yn($module->option_draft_watermark,1,1);


                                print '<td align="center">';
                                print $html->textwithpicto('',$htmltooltip,1,0);
                                print '</td>';

                                // Preview
                                print '<td align="center">';
                                if ($module->type == 'pdf')
                                {
                                    print '<a href="'.$_SERVER["PHP_SELF"].'?action=specimen&module='.$name.'">'.img_object($langs->trans("Preview"),'bill').'</a>';
                                }
                                else
                                {
                                    print img_object($langs->trans("PreviewNotAvailable"),'generic');
                                }
                                print '</td>';

                                print "</tr>\n";
                            }
                        }
                    }
                }
            }
        }
    }
}
print '</table>';


/*
 *  Modes de reglement
 *
 */
print '<br>';
print_titre($langs->trans("SuggestedPaymentModesIfNotDefinedInInvoice"));

print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

print '<table class="noborder" width="100%">';
$var=True;

print '<tr class="liste_titre">';
print '<td>';
print '<input type="hidden" name="action" value="setribchq">';
print $langs->trans("PaymentMode").'</td>';
print '<td align="right"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
print "</tr>\n";
$var=!$var;
print '<tr '.$bc[$var].'>';
print "<td>".$langs->trans("SuggestPaymentByRIBOnAccount")."</td>";
print "<td>";
if ($conf->banque->enabled)
{
    $sql = "SELECT rowid, label";
    $sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
    $sql.= " WHERE clos = 0";
    $sql.= " AND courant = 1";
    $sql.= " AND entity = ".$conf->entity;
    $resql=$db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num > 0) {
            print "<select name=\"rib\">";
            print '<option value="0">'.$langs->trans("DoNotSuggestPaymentMode").'</option>';
            while ($i < $num)
            {
                $row = $db->fetch_row($resql);

                print '<option value="'.$row[0].'"';
                print $conf->global->FACTURE_RIB_NUMBER == $row[0] ? ' selected="selected"':'';
                print '>'.$row[1].'</option>';

                $i++;
            }
            print "</select>";
        } else {
            print "<i>".$langs->trans("NoActiveBankAccountDefined")."</i>";
        }
    }
}
else
{
    print $langs->trans("BankModuleNotActive");
}
print "</td></tr>";
$var=!$var;
print '<tr '.$bc[$var].'>';
print "<td>".$langs->trans("SuggestPaymentByChequeToAddress")."</td>";
print "<td>";
print '<select name="chq">';
print '<option value="0">'.$langs->trans("DoNotSuggestPaymentMode").'</option>';
print '<option value="-1"'.($conf->global->FACTURE_CHQ_NUMBER?' selected="selected"':'').'>'.$langs->trans("MenuCompanySetup").' ('.($mysoc->name?$mysoc->name:$langs->trans("NotDefined")).')</option>';

$sql = "SELECT rowid, label";
$sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
$sql.= " WHERE clos = 0";
$sql.= " AND courant = 1";
$sql.= " AND entity = ".$conf->entity;
$var=True;
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    $i = 0;
    while ($i < $num)
    {
        $var=!$var;
        $row = $db->fetch_row($resql);

        print '<option value="'.$row[0].'"';
        print $conf->global->FACTURE_CHQ_NUMBER == $row[0] ? ' selected="selected"':'';
        print '>'.$langs->trans("OwnerOfBankAccount",$row[1]).'</option>';

        $i++;
    }
}
print "</select>";
print "</td></tr>";
print "</table>";
print "</form>";


/*
 *  Repertoire
 */
print '<br>';
print_titre($langs->trans("PathToDocuments"));

print "<table class=\"noborder\" width=\"100%\">\n";
print "<tr class=\"liste_titre\">\n";
print "  <td>".$langs->trans("Name")."</td>\n";
print "  <td>".$langs->trans("Value")."</td>\n";
print "</tr>\n";
print "<tr ".$bc[false].">\n  <td width=\"140\">".$langs->trans("PathDirectory")."</td>\n  <td>".$conf->facture->dir_output."</td>\n</tr>\n";
print "</table>\n";


//dol_fiche_end();


$db->close();

llxFooter('$Date: 2011/06/08 16:42:54 $ - $Revision: 1.3 $');
?>
