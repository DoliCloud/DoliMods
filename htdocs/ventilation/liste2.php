<?php
/* Copyright (C) 2002-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2004-2006 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * 		\file       htdocs/compta/ventilation/liste.php
 * 		\ingroup    compta
 * 		\brief      Page de ventilation des lignes de facture
 */
$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


$langs->load("compta");
$langs->load("bills");
$langs->load("main");
$langs->load("ventilation@ventilation");

if (!$user->rights->facture->lire) accessforbidden();
if (!$user->rights->compta->ventilation->creer) accessforbidden();

// Securite acces client
if ($user->societe_id > 0) accessforbidden();


llxHeader('',$langs->trans("Ventilation"));

if($_POST["action"] == 'ventil')
{
	print '<div><font color="red">'.$langs->trans("Processing").'...</font></div>';
	if($_POST['codeventil'] && $_POST["mesCasesCochees"])
	{
		print '<div><font color="red">'.count($_POST["mesCasesCochees"]).' '.$langs->trans("SelectedLines").'</font></div>';
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
  
			$sql = " UPDATE ".MAIN_DB_PREFIX."facturedet";
			$sql .= " SET fk_code_ventilation = ".$monCompte;
			$sql .= " WHERE rowid = ".$monId;

			if($db->query($sql))
			{
				print '<div><font color="green">'.$langs->trans("Line of invoice").' '.$monId.' '.$langs->trans("VentilatedinAccount").' : '.$monCompte.'</font></div>';
			}
			else 
			{
				print '<div><font color="red">'.$langs->trans("ErrorDB").' : '.$langs->trans("Line of invoice").' '.$monId.' '.$langs->trans("NotVentilatedinAccount").' : '.$monCompte.'<br/> <pre>'.$sql.'</pre></font></div>';
			}
  
			$cpt++; 
  
		}
	}
	else
	{
		print '<div><font color="red">'.$langs->trans("AnyLineVentilate").'</font></div>';
	}
	print '<div><font color="red">'.$langs->trans("EndProcessing").'</font></div>';
}

/* 
 * Liste des comptes
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
$limit = $conf->liste_limit;
$offset = $limit * $page ;

$sql = "SELECT f.facnumber, f.rowid as facid, l.fk_product, l.description, l.total_ht, l.rowid, l.fk_code_ventilation,";
$sql.= " p.rowid as product_id, p.ref as product_ref, p.label as product_label, p.fk_product_type as type, p.accountancy_code_sell as code_sell";
$sql.= " FROM ".MAIN_DB_PREFIX."facture as f";
$sql.= " , ".MAIN_DB_PREFIX."facturedet as l";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON p.rowid = l.fk_product";
$sql.= " WHERE f.rowid = l.fk_facture AND f.fk_statut > 0 AND fk_code_ventilation = 0";
$sql.= " ORDER BY l.rowid DESC ".$db->plimit($limit+1,$offset);

$result = $db->query($sql);
if ($result)
{
	$num_lignes = $db->num_rows($result);
	$i = 0;
	print_barre_liste($langs->trans("InvoiceLines"),$page,"liste.php","",$sortfield,$sortorder,'',$num_lignes);


	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans("Invoice").'</td>';
	print '<td>'.$langs->trans("Ref").'</td>';
	print '<td>'.$langs->trans("Label").'</td>';
	print '<td>'.$langs->trans("Description").'</td>';
	print '<td align="right">'.$langs->trans("Amount").'</td>';
	print '<td align="right">'.$langs->trans("Account").'</td>';
	print '<td align="center">'.$langs->trans("IntoAccount").'</td>';
  print '<td align="center">'.$langs->trans("Ventilate").'</td>';
	print '</tr>';

	$facture_static=new Facture($db);
	$product_static=new Product($db);
  	$form = new Form($db);

	print '<form action="liste2.php" method="post">'."\n";
	print '<input type="hidden" name="action" value="ventil">';

	$var=True;
	while ($i < min($num_lignes, $limit))
	{
		$objp = $db->fetch_object($result);
		$var=!$var;
		print "<tr $bc[$var]>";

		// Ref facture
		$facture_static->ref=$objp->facnumber;
		$facture_static->id=$objp->facid;
		print '<td>'.$facture_static->getNomUrl(1).'</td>';

		// Ref produit
		$product_static->ref=$objp->product_ref;
		$product_static->id=$objp->product_id;
		$product_static->type=$objp->type;
		print '<td>';
		if ($product_static->id) print $product_static->getNomUrl(1);
		else print '&nbsp;';
		print '</td>';
		
		print '<td>'.dol_trunc($objp->product_label,24).'</td>';
		print '<td>'.nl2br(dol_trunc($objp->description,32)).'</td>';

		print '<td align="right">';
		print price($objp->total_ht);
		print '</td>';
		
		print '<td align="right">';
		print $objp->code_sell;
		print '</td>';	
		

		//Colonne choix du compte
		print '<td align="center">';
		print $form->selectarray("codeventil[]",$cgs, $cgn[$objp->code_sell]);
		print '</td>';
		//Colonne choix ligne a ventiler
		print '<td align="center">';
		print '<input type="checkbox" name="mesCasesCochees[]" value="'.$objp->rowid."_".$i.'"'.($objp->code_sell?"checked":"").'/>';
		print '</td>';

		print '</tr>';
		$i++;
	}

	print '<tr><td colspan="8">&nbsp;</td></tr><tr><td colspan="8" align="center"><input type="submit" class="butAction" value="'.$langs->trans("Ventilate").'"></td></tr>';

	print '</table>';
	print '</form>';
}
else
{
	print $db->error();
}
$db->close();

llxFooter();
?>
