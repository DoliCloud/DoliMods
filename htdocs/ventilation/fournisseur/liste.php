<?php
/* Copyright (C) 2002-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2004      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/ventilation/fournisseur/liste.php
 *	\ingroup    compta
 *	\brief      Page de ventilation des lignes de facture
 */

// Dolibarr environment
$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

$langs->load("bills");
$langs->load("ventilation@ventilation");

if (!$user->rights->facture->lire) accessforbidden();
if (!$user->rights->compta->ventilation->creer) accessforbidden();
/*
 * Securite acces client
 */
if ($user->societe_id > 0) accessforbidden();


llxHeader('','Ventilation');

if($_POST["action"] == 'ventil')
  {
  print '<div><font color="red">Debut du traitement... </font></div>';
  if($_POST['codeventil'] && $_POST["mesCasesCochees"])
  {
    print '<div><font color="red">'.count($_POST["mesCasesCochees"])." Lignes selectionnees</font></div>";
    $mesLignesCochees=$_POST['mesCasesCochees'];
    $mesCodesVentilChoisis = $_POST['codeventil'];
    $cpt = 0;
    foreach($mesLignesCochees as $maLigneCochee)
      {
      //print '<div><font color="red">id selectionnee : '.$monChoix."</font></div>";
      $maLigneCourante = split("_", $maLigneCochee);
      $monId = $maLigneCourante[0];
      $monNumLigne = $maLigneCourante[1];
      $monCompte = $mesCodesVentilChoisis[$monNumLigne];

      $sql = " UPDATE ".MAIN_DB_PREFIX."facture_fourn_det";
      $sql .= " SET fk_code_ventilation = ".$monCompte;
      $sql .= " WHERE rowid = ".$monId;

      if($db->query($sql))
      {
            print '<div><font color="green"> Ligne de facture '.$monId.' ventilee <b>avec succes</b> dans le compte : '.$monCompte.'</font></div>';
      }
      else
      {
           print '<div><font color="red">Erreur BD : Ligne de facture '.$monId.' nom ventilee dans le compte : '.$monCompte.'<br/> <pre>'.$sql.'</pre></font></div>';
      }

      $cpt++;

      }
    }
    else
    {
      print '<div><font color="red">Aucune ligne � ventiler</font></div>';
    }
    print '<div><font color="red">Traitement termine </font></div>';
  }
/* Liste des comptes
*/

$sqlCompte = "SELECT rowid, numero, intitule";
$sqlCompte .= " FROM ".MAIN_DB_PREFIX."compta_compte_generaux";
$sqlCompte .= " ORDER BY numero ASC";

$resultCompte = $db->query($sqlCompte);
$cgs = array();
$cgn = array();
if ($resultCompte)
{
  $numCompte = $db->num_rows($resultCompte);
  $iCompte = 0;

  while ($iCompte < $numCompte)
    {
      $rowCompte = $db->fetch_row($resultCompte);
      $cgs[$rowCompte[0]] = $rowCompte[1] . ' ' . $rowCompte[2];
      $cgn[$rowCompte[1]] = $rowCompte[0];
      $iCompte++;
    }
}



/*
 * Lignes de factures
 *
 */
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = GETPOST('limit')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page ;

$sql = "SELECT f.ref, f.rowid as facid, l.fk_product, l.description, l.total_ht as price, l.rowid, l.fk_code_ventilation, ";
$sql.= " p.rowid as product_id, p.ref as product_ref, p.label as product_label, p.fk_product_type as type, p.accountancy_code_buy as code_buy";
$sql .= " FROM ".MAIN_DB_PREFIX."facture_fourn as f";
$sql .= " , ".MAIN_DB_PREFIX."facture_fourn_det as l";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON p.rowid = l.fk_product";
$sql .= " WHERE f.rowid = l.fk_facture_fourn AND f.fk_statut > 0 AND fk_code_ventilation = 0";
$sql .= " ORDER BY l.rowid DESC ".$db->plimit($limit+1,$offset);

$result = $db->query($sql);
if ($result)
{
  $num_lignes = $db->num_rows($result);
  $i = 0;

  print_barre_liste("Lignes de facture &agrave; ventiler",$page,"liste.php","",$sortfield,$sortorder,'',$num_lignes);

  print '<form action="liste.php" method="post">'."\n";
  print '<input type="hidden" name="action" value="ventil">';

  print '<table class="noborder" width="100%">';
  print '<tr class="liste_titre"><td>Facture</td>';
  print '<td align="left">'.$langs->trans("Ref").'</td>';
  print '<td align="left">'.$langs->trans("Label").'</td>';
  print '<td>'.$langs->trans("Description").'</td>';
  print '<td align="right">'.$langs->trans("Amount").'</td>';
  print '<td align="right">'.$langs->trans("Compte").'</td>';
  print '<td align="center">'.$langs->trans("IntoAccount").'</td>';
  print '<td align="center">'.$langs->trans("Ventilate").'</td>';
  print "</tr>\n";


	$form = new Form($db);


  $var=True;
  while ($i < min($num_lignes, $limit))
    {
      $objp = $db->fetch_object($result);
      $var=!$var;
      print "<tr $bc[$var]>";

      print '<td><a href="'.DOL_URL_ROOT.'/fourn/facture/card.php?facid='.$objp->facid.'">'.$objp->facnumber.'</a></td>';

      print '<td><a href="'.DOL_URL_ROOT.'/product/card.php?id='.$objp->product_id.'">'.$objp->product_ref.'</a></td>';
      print '<td>'.dol_trunc($objp->product_label,24).'</td>';

      print '<td>'.stripslashes(nl2br($objp->description)).'</td>';

      print '<td align="right">';
      print price($objp->price);
      print '</td>';

      print '<td align="right">';
      print $objp->code_buy;
      print '</td>';

		//Colonne choix du compte
		print '<td align="center">';
		print $form->selectarray("codeventil[]",$cgs, $cgn[$objp->code_buy]);
		print '</td>';
		//Colonne choix ligne a ventiler
		print '<td align="center">';
		print '<input type="checkbox" name="mesCasesCochees[]" value="'.$objp->rowid."_".$i.'"'.($objp->code_buy?"checked":"").'/>';
		print '</td>';


      print "</tr>";
      $i++;
    }

print '<tr><td colspan="8">&nbsp;</td></tr><tr><td colspan="8" align="center"><input type="submit" class="butAction" value="'.$langs->trans("Ventiler").'"></td></tr>';

print "</table>";

	print '</form>';
}
else
{
  print $db->error();
}
$db->close();

llxFooter();
?>
