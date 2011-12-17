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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
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

$langs->load("bills");

$year_start=GETPOST("year_start");
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
$socid =GETPOST("socid");
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

$sql = "SELECT f.rowid as cid, f.datecons, f.fk_user, f.typevisit, f.codageccam, f.montant_cheque as montant_cheque, f.montant_espece as montant_espece, f.montant_tiers as montant_tiers, f.montant_carte as montant_carte,";
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
        $consult[$rowid] = array('date'=>$db->jdate($row->datecons), 'name'=>$row->name, 'fk_user'=>$row->fk_user, 'type'=>$row->typevisit, 'codageccam'=>$row->codageccam);
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

$array_selected = array('date'=>1, 'cid'=>1, 'name'=>1);
if ($conf->global->CABINETMED_ADDTYPECCAM)
{
    $array_selected['type']=1;
    $array_selected['codageccam']=1;
}
$array_selected['montant_cheque']=1;
$array_selected['montant_carte']=1;
$array_selected['montant_espece']=1;
$array_selected['montant_tiers']=1;
$array_export_fields = array('cid'=>'ID', 'name'=>'Name', 'date'=>'Date', 'type'=>'Type', 'codageccam'=>'CCAM', 'montant_cheque'=>'Cheque', 'montant_carte'=>'CreditCard', 'montant_espece'=>'Cash', 'montant_tiers'=>'Other');
$objexport->array_export_fields[0]=$array_export_fields;
$objexport->array_export_alias[0]=$array_alias;

// Open file
$result=$objmodel->open_file($outputfile, $outputlangs);

// Genere en-tete
$objmodel->write_header($outputlangs);

// Genere ligne de titre
$objmodel->write_title($array_export_fields,$array_selected,$outputlangs);

$objmodel->workbook->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objmodel->workbook->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objmodel->workbook->getActiveSheet()->getColumnDimension('C')->setWidth(32);
$objmodel->workbook->getActiveSheet()->getColumnDimension('J')->setWidth(12);

// Write records
$olddate=0;
$nbact=0;
$nbccam=0;
$i=1;
foreach($consult as $rowid => $val)
{
    $objp=(object) array();
    if ($i > 1 && ($olddate != $consult[$rowid]['date']))    // Break on day
    {
        $objp->date=$langs->trans("Total");
        $objp->type=$nbact;
        $objp->codageccam=$nbccam;
        $objp->montant_cheque=$encaiss_chq[$d]?$encaiss_chq[$d]:'';
        $objp->montant_espece=$encaiss_esp[$d]?$encaiss_esp[$d]:'';
        $objp->montant_tiers =$encaiss_tie[$d]?$encaiss_tie[$d]:'';
        $objp->montant_carte =$encaiss_car[$d]?$encaiss_car[$d]:'';

        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getBorders()->applyFromArray(array(
                 'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                     'color' => array('rgb' => '808080')
             )));
        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getFont()->setBold(true);
        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getFont()->getColor()->applyFromArray( array('rgb' => '303040') );
        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getFill()->applyFromArray(array(
                     'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                     'rotation'   => 0,
                     'startcolor' => array('rgb' => 'CCCCCC'),
                     'endcolor'   => array('argb' => 'FFFFFFFF')
            ));
        //$objmodel->workbook->getActiveSheet()->getStyle($i+1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objmodel->workbook->getActiveSheet()->getStyle('J'.($i+1))->getFont()->getColor()->applyFromArray( array('rgb' => '303070') );
        $objmodel->workbook->getActiveSheet()->SetCellValueByColumnAndRow(9, $i+1, $encaiss_chq[$d]+$encaiss_esp[$d]+$encaiss_tie[$d]+$encaiss_car[$d]);
        $objmodel->write_record($array_selected,$objp,$outputlangs);
        $i++;

        //$coord=$objmodel->workbook->getActiveSheet()->getCellByColumnAndRow(1, $i+1)->getCoordinate();
        //$this->workbook->getActiveSheet()->getStyle($coord)->getNumberFormat()->setFormatCode('yyyy-mm-dd');

        $objp=(object) array();
        $objmodel->write_record(array(),$objp,$outputlangs);
        $i++;

        $olddate=$consult[$rowid]['date'];
        $nbact=0;
        $nbccam=0;
    }

    $objp->date=dol_print_date($consult[$rowid]['date'],'day');
    $objp->cid=$rowid;
    $objp->name=$consult[$rowid]['name'];
    $objp->fk_user=$consult[$rowid]['fk_user'];
    $objp->type=$consult[$rowid]['type'];
    $objp->codageccam=$consult[$rowid]['codageccam'];
    $objp->montant_cheque=$encaiss_chq[$rowid]?$encaiss_chq[$rowid]:'';
    $objp->montant_espece=$encaiss_esp[$rowid]?$encaiss_esp[$rowid]:'';
    $objp->montant_tiers =$encaiss_tie[$rowid]?$encaiss_tie[$rowid]:'';
    $objp->montant_carte =$encaiss_car[$rowid]?$encaiss_car[$rowid]:'';
    //f.rowid as cid, f.datecons, f.fk_user, f.typevisit, f.montant_cheque as montant_cheque, f.montant_espece as montant_espece, f.montant_tiers as montant_tiers, f.montant_carte as montant_carte

    if ($objp->type != 'CCAM') $nbact++;
    else $nbccam++;

    $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':I'.($i+1))->getBorders()->applyFromArray(
         array(
             'allborders' => array(
                 'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                 'color' => array(
                     'rgb' => '808080'
                 )
             )
         )
    );
    $objmodel->write_record($array_selected,$objp,$outputlangs);
    $i++;

    $d=dol_print_date($consult[$rowid]['date'],'%Y-%m-%d');
    $dm=dol_print_date($consult[$rowid]['date'],'%Y-%m');
}

$objp=(object) array();
if ($i != 0)    // Break on day
{
    $objp->date=$langs->trans("Total");
    $objp->type=$nbact;
    $objp->codageccam=$nbccam;
    $objp->montant_cheque=$encaiss_chq[$d]?$encaiss_chq[$d]:'';
    $objp->montant_espece=$encaiss_esp[$d]?$encaiss_esp[$d]:'';
    $objp->montant_tiers =$encaiss_tie[$d]?$encaiss_tie[$d]:'';
    $objp->montant_carte =$encaiss_car[$d]?$encaiss_car[$d]:'';

        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getBorders()->applyFromArray(array(
                 'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                     'color' => array('rgb' => '808080')
             )));
        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getFont()->setBold(true);
        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getFont()->getColor()->applyFromArray( array('rgb' => '303040') );
        $objmodel->workbook->getActiveSheet()->getStyle('A'.($i+1).':J'.($i+1))->getFill()->applyFromArray(array(
                     'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                     'rotation'   => 0,
                     'startcolor' => array('rgb' => 'CCCCCC'),
                     'endcolor'   => array('argb' => 'FFFFFFFF')
            ));
        //$objmodel->workbook->getActiveSheet()->getStyle($i+1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objmodel->workbook->getActiveSheet()->getStyle('J'.($i+1))->getFont()->getColor()->applyFromArray( array('rgb' => '303070') );
        $objmodel->workbook->getActiveSheet()->SetCellValueByColumnAndRow(9, $i+1, $encaiss_chq[$d]+$encaiss_esp[$d]+$encaiss_tie[$d]+$encaiss_car[$d]);
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
