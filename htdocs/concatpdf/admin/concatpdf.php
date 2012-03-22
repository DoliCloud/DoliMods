<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	    \file       htdocs/concatpdf/admin/concatpdf.php
 *      \ingroup    concatpdf
 *      \brief      Page to setup module ConcatPdf
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");

// Use on dev env only
preg_match('/^\/([^\/]+)\//', dirname($_SERVER["SCRIPT_NAME"]), $regs);
$realpath = readlink($_SERVER['DOCUMENT_ROOT'].'/'.$regs[1]);
if (! $res && file_exists($realpath."main.inc.php")) $res=@include($realpath."main.inc.php");

if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');


if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("concatpdf@concatpdf");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");

$outputdir_invoices=$conf->concatpdf->dir_output.'/invoices';
$outputdir_orders=$conf->concatpdf->dir_output.'/orders';
$outputdir_proposals=$conf->concatpdf->dir_output.'/proposals';



/*
 * Actions
 */

// None


/**
 * View
 */

$formfile=new FormFile($db);

llxHeader('','ConcatPdf',$linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ConcatPdfSetup"),$linkback,'setup');
print '<br>';

clearstatcache();

print $langs->trans("ConcatPDfTakeFileFrom").'<br>';
$langs->load("propal"); $langs->load("orders"); $langs->load("bills");
print '* '.$langs->trans("ConcatPDfTakeFileFrom2",$langs->transnoentitiesnoconv("Proposals"),$outputdir_invoices).'<br>';
print '* '.$langs->trans("ConcatPDfTakeFileFrom2",$langs->transnoentitiesnoconv("Orders"),$outputdir_orders).'<br>';
print '* '.$langs->trans("ConcatPDfTakeFileFrom2",$langs->transnoentitiesnoconv("Invoices"),$outputdir_proposals).'<br>';
print '<br>';

print $langs->trans("ConcatPDfPutFileManually");
print '<br><br><br>';


$listoffiles=dol_dir_list($outputdir_proposals,'files');
if (count($listoffiles)) print $formfile->showdocuments('concatpdf','proposals',$outputdir_proposals,$_SERVER["PHP_SELF"],0,$user->admin,'',0,0,0,0,0,'',$langs->trans("PathDirectory").' '.$outputdir_proposals);
else
{
    print '<div class="titre">'.$langs->trans("PathDirectory").' '.$outputdir_proposals.' :</div>';
    print $langs->trans("NoPDFFileFound").'<br>';
}

print '<br><br>';

$listoffiles=dol_dir_list($outputdir_orders,'files');
if (count($listoffiles)) print $formfile->showdocuments('concatpdf','orders',$outputdir_orders,$_SERVER["PHP_SELF"],0,$user->admin,'',0,0,0,0,0,'',$langs->trans("PathDirectory").' '.$outputdir_orders);
else
{
    print '<div class="titre">'.$langs->trans("PathDirectory").' '.$outputdir_orders.' :</div>';
    print $langs->trans("NoPDFFileFound").'<br>';
}

print '<br><br>';

$listoffiles=dol_dir_list($outputdir_invoices,'files');
if (count($listoffiles)) print $formfile->showdocuments('concatpdf','invoices',$outputdir_invoices,$_SERVER["PHP_SELF"],0,$user->admin,'',0,0,0,0,0,'',$langs->trans("PathDirectory").' '.$outputdir_invoices);
else
{
    print '<div class="titre">'.$langs->trans("PathDirectory").' '.$outputdir_invoices.' :</div>';
    print $langs->trans("NoPDFFileFound").'<br>';
}

print '<br>';

llxFooter();

if (is_object($db)) $db->close();
?>
