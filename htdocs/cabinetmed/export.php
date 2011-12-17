<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *       \file        htdocs/cabinetmed/export.php
 *       \brief       Page reporting resultat
 *       \version     $Id: compta.php,v 1.7 2011/08/24 00:03:03 eldy Exp $
 */


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/report.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");


$year_start=isset($_GET["year_start"])?$_GET["year_start"]:$_POST["year_start"];
$year_current = strftime("%Y",time());
$nbofyear=3;
if (! $year_start)
{
	$year_start = $year_current - ($nbofyear-1);
	$year_end = $year_current;
}
else
{
	$year_end=$year_start + ($nbofyear-1);
}

// Define modecompta ('CREANCES-DETTES' or 'RECETTES-DEPENSES')
$modecompta = $conf->global->COMPTA_MODE;
if ($_GET["modecompta"]) $modecompta=$_GET["modecompta"];
$search_sale=GETPOST('search_sale');


// Security check
$socid = isset($_REQUEST["socid"])?$_REQUEST["socid"]:'';
if ($user->societe_id > 0) $socid = $user->societe_id;
//if (!$user->rights->cabinetmed->lire)
//accessforbidden();

if (!$user->rights->cabinetmed->read) accessforbidden();



/*
 * View
 */

if ($contenttype)       header('Content-Type: '.$contenttype.($outputencoding?'; charset='.$outputencoding:''));
if ($attachment) 		header('Content-Disposition: attachment; filename="'.$shortfilename.'"');


/*
 * Sums
 */
$subtotal_ht = 0;
$subtotal_ttc = 0;
$encaiss_chq = $encaiss_esp = $encaiss_tie = $encaiss_car = array();

$sql = "SELECT f.rowid as cid, f.datecons, f.fk_user, f.typevisit, f.montant_cheque as montant_cheque, f.montant_espece as montant_espece, f.montant_tiers as montant_tiers, f.montant_carte as montant_carte,";
$sql.= " s.nom as name";
$sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_cons as f, ".MAIN_DB_PREFIX."societe as s";
$sql.= " WHERE f.fk_soc = s.rowid";
if ($search_sale) $sql.= " AND f.fk_user = ".$search_sale;
if ($socid) $sql.= " AND f.fk_soc = ".$socid;
$sql.= " GROUP BY f.datecons, f.fk_user";
$sql.= " ORDER BY f.datecons, f.rowid";
//print $sql;

//print $sql;
dol_syslog("get consultations sql=".$sql);
$result=$db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	while ($i < $num)
	{
		$row = $db->fetch_object($result);
        $rowid=$row->cid;
		$d=dol_print_date($db->jdate($row->datecons),'%Y-%m-%d');
		$dm=dol_print_date($db->jdate($row->datecons),'%Y-%m');
        $consult[$rowid] = array('name'=>$row->name, 'fk_user'=>$row->fk_user, 'type'=>$row->typevisit, 'date'=>$db->jdate($row->datecons));
        $encaiss_chq[$rowid] += $row->montant_cheque;
		$encaiss_esp[$rowid] += $row->montant_espece;
        $encaiss_tie[$rowid] += $row->montant_tiers;
        $encaiss_car[$rowid] += $row->montant_carte;
		$encaiss_chq[$dm] += $row->montant_cheque;
        $encaiss_esp[$dm] += $row->montant_espece;
        $encaiss_tie[$dm] += $row->montant_tiers;
        $encaiss_car[$dm] += $row->montant_carte;
        $encaiss_chq[$d] += $row->montant_cheque;
        $encaiss_esp[$d] += $row->montant_espece;
        $encaiss_tie[$d] += $row->montant_tiers;
        $encaiss_car[$d] += $row->montant_carte;
        $i++;
	}
	$db->free($result);
}
else {
	dol_print_error($db);
}



/*
 * Build file
 */

$model='excel2007';

// Creation de la classe d'export du model ExportXXX
$dir = DOL_DOCUMENT_ROOT . "/core/modules/export/";
$file = "export_".$model.".modules.php";
$classname = "Export".$model;
require_once($dir.$file);
$objmodel = new $classname($db);

$dirname=$conf->cabinetmed->dir_temp;
$filename='export_'.$user->id.'.xlsx';
$outputfile=$dirname."/".$filename;
dol_mkdir($dirname);
$outputlangs=dol_clone($langs);

$array_selected = array('f.rowid'=>1, 's.name'=>1, 'f.datecons'=>1, /*'f.fk_user'=>1,*/ 'f.typevisit'=>1, 'f.montant_cheque'=>1, 'f.montant_espece'=>1, 'f.montant_tiers'=>1, 'f.montant_carte'=>1);
$array_export_fields = array('f.rowid'=>'ID', 's.name'=>'Name', 'f.datecons'=>'Date', 'f.typevisit'=>'Type', 'f.fk_user'=>'User', 'f.montant_cheque'=>'Cheque', 'f.montant_espece'=>'Cash', 'f.montant_carte'=>'CreditCard', 'f.montant_tiers'=>'Other');
$array_alias = array('f.rowid'=>'cid', 's.name'=>'name', 'f.datecons'=>'datecons', 'f.fk_user'=>'fk_user');
$objexport->array_export_fields[0]=$array_export_fields;
$objexport->array_export_alias[0]=$array_alias;

// Open file
$result=$objmodel->open_file($outputfile, $outputlangs);

// Genere en-tete
$objmodel->write_header($outputlangs);

// Genere ligne de titre
$objmodel->write_title($array_export_fields,$array_selected,$outputlangs);

// Write records
foreach($consult as $rowid => $val)
{
    $objp=(object) array();
    $objp->cid=$rowid;
    $objp->name=$consult[$rowid]['name'];
    $objp->fk_user=$consult[$rowid]['fk_user'];
    $objp->type=$consult[$rowid]['type'];
    $objp->date=$consult[$rowid]['date'];

    //f.rowid as cid, f.datecons, f.fk_user, f.typevisit, f.montant_cheque as montant_cheque, f.montant_espece as montant_espece, f.montant_tiers as montant_tiers, f.montant_carte as montant_carte

    $objmodel->write_record($array_selected,$objp,$outputlangs);
}

// Genere en-tete
$objmodel->write_footer($outputlangs);

// Close file
$objmodel->close_file();


$db->close();


// Output file
$contentype=dol_mimetype($outputfile);
$attachment=1;

if ($contenttype)       header('Content-Type: '.$contenttype.($outputencoding?'; charset='.$outputencoding:''));
if ($attachment) 		header('Content-Disposition: attachment; filename="'.$filename.'"');

// Ajout directives pour resoudre bug IE
//header('Cache-Control: Public, must-revalidate');
//header('Pragma: public');

// Clean parameters
$result=readfile($outputfile);
if (! $result) print 'File '.$outputfile.' was empty.';

?>
