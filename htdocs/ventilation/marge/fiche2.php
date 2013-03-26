<?PHP
/* Copyright (C) 2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005 Simon TOSSER <simon@kornog-computing.com>
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
 *
 */

/**
 *      \file       htdocs/compta/ventilation/fournisseur/fiche.php
 *      \ingroup    compta
 *      \brief      Page fiche ventilation
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");

$langs->load("bills");

if (array_key_exists("id", $_GET)) {
	$currentId = $_GET["id"];
} else {
	print "pas d'id";
}


/*
 * Actions/*


/**

if ($_POST["action"] == 'ventil' && $user->rights->compta->ventilation->creer)
{
  $sql = "INSERT INTO ".MAIN_DB_PREFIX."ventilation_achat (";
  $sql. = "fk_facture_fourn_det";
  $sql. = ", qty" 	
  $sql. = ", fk_facture"
  $sql.= ") VALUES (";
  $sql.= "$currentId ;
  $sql.= ", qty";
  $sql.= ", fk_facture")"; 		


  $db->query($sql);



if ($cancel == $langs->trans("Cancel"))
{
  $action = '';
}


*/

llxHeader("","","Fiche Marge Commerciale");


/*
 *
 *
 */

$sql = "SELECT facnumber, fk_soc, note";
$sql .= " FROM ".MAIN_DB_PREFIX."facture ";
$sql .= " ORDER BY rowid DESC";

$result = $db->query($sql);
if ($result)
{
  $num = $db->num_rows($result);
  $i = 0; 
  
  while ($i < $num)
    {
      $row = $db->fetch_row($result);
      $cgs[$row[0]] = $row[1] . ' ' . $row[2];
      $i++;
    }
}

/*
 * Cr�ation
 *
 */
$form = new Form($db);
$facture_static=new Facture($db);

if($_GET["id"])
{
    $sql = "SELECT f.facnumber, f.rowid as facid, l.fk_product, l.description, l.price,";
    $sql .= " l.qty, l.rowid, l.tva_tx, l.remise_percent, l.subprice,";
    $sql .= " ".$db->pdate("l.date_start")." as date_start, ".$db->pdate("l.date_end")." as date_end,";
    $sql .= " l.fk_code_ventilation ";
    $sql .= " FROM ".MAIN_DB_PREFIX."facturedet as l";
    $sql .= " , ".MAIN_DB_PREFIX."facture as f";
    $sql .= " WHERE f.rowid = l.fk_facture AND f.fk_statut = 1 AND l.rowid = ".$_GET["id"];

    $result = $db->query($sql);

    if ($result)
    {
        $num_lignes = $db->num_rows($result);
        $i = 0;

        if ($num_lignes)
        {

            $objp = $db->fetch_object($result);


            if($objp->fk_code_ventilation == 0)
            {
                print '<form action="fiche.php?id='.$_GET["id"].'" method="post">'."\n";
                print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
                print '<input type="hidden" name="action" value="ventil">';
            }


            print_fiche_titre("Ventilation");

            print '<table class="border" width="100%">';

			// Ref facture
            print '<tr><td>'.$langs->trans("Invoice").'</td>';
			$facture_static->ref=$objp->facnumber;
			$facture_static->id=$objp->facid;
			print '<td>'.$facture_static->getNomUrl(1).'</td>';
            print '</tr>';

            print '<tr><td width="20%">Ligne</td>';
            print '<td>'.nl2br($objp->description).'</td></tr>';
            print '<tr><td width="20%">Ventiler dans le compte :</td><td>';

            if($objp->fk_code_ventilation == 0)
            {
                print $form->select_array("codeventil",$cgs, $objp->fk_code_ventilation);
            }
            else
            {
                print $cgs[$objp->fk_code_ventilation];
            }

            print '</td></tr>';

            if($objp->fk_code_ventilation == 0)
            {
                print '<tr><td>&nbsp;</td><td><input type="submit" class="button" value="'.$langs->trans("Ventiler").'"></td></tr>';
            }
            print '</table>';
            print '</form>';
        }
        else
        {
            print $db->lasterror();
        }
    }
    else
    {
        print $db->lasterror();
    }
}
else
{
    print "Error ID incorrect";
}

$db->close();

llxFooter();
?>
