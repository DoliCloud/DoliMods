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
 *   \file       htdocs/compta/ventilation/affaire/liste.php
 *   \ingroup    compta
 *   \brief      Page de ventilation des affaires par facture client
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

$langs->load("bills");

if (!$user->rights->facture->lire) accessforbidden();
if (!$user->rights->compta->ventilation->creer) accessforbidden();
/*
 * Securite acces client
 */
if ($user->societe_id > 0) accessforbidden();


llxHeader('','Ventilation');

/*
 * factures client a ventiler
 *
 */
$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page ;

$sql = "SELECT fd.fk_facture_fourn as facnumber, fd.fk_product as product, fd.description as description, fd.total_ht as total, fd.fk_code_ventilation as ven, fd.rowid as facid";
$sql .= " FROM ".MAIN_DB_PREFIX."facture_fourn_det as fd ";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."ventilation_achat as va ";
$sql .= " ON fd.rowid = va.fk_facture_fourn_det";
$sql .= " WHERE va.fk_facture_fourn_det IS NULL ";
$sql .= " AND fd.fk_code_ventilation = 5 ";
$sql .= " ORDER BY fd.rowid DESC ".$db->plimit($limit+1,$offset);

$result = $db->query($sql);
if ($result)
{
  $num_lignes = $db->num_rows($result);
  $i = 0; 
  
  print_barre_liste("Lignes de facture à ventiler",$page,"liste.php","",$sortfield,$sortorder,'',$num_lignes);

  print '<table class="noborder" width="100%">';
  print '<tr class="liste_titre"><td>Facture Fournisseur</td>';
  print '<td>product</td>';
  print '<td>description</td>';
  print '<td>total</td>';
  print '<td>ventilation</td>';
  print '<td>fk facture fourn det</td>';
  print '<td align="right">&nbsp;</td>';
  print '<td>&nbsp;</td>';
  print "</tr>\n";

  $var=True;
  while ($i < min($num_lignes, $limit))
    {
      $objp = $db->fetch_object($result);
      $var=!$var;
      print "<tr $bc[$var]>";
      
      print '<td><a href="'.DOL_URL_ROOT.'/fourn/facture/fiche.php?facid='.$objp->facnumber.'">'.$objp->facnumber.'</a></td>';
      
      print '<td align="center">'.$objp->product.'</td>';
      
      print '<td>'.stripslashes(nl2br($objp->description)).'</td>';                       

      print '<td align="left">';
      print price($objp->total);
      print '</td>';
      
      print '<td align="center">'.$objp->ven.'</td>';
      
      print '<td align="center">'.$objp->facid.'</td>';

      print '<td align="right"><a href="fiche2.php?id='.$objp->facid.'">';
      print img_edit();
      print '</a></td>';

      print "</tr>";
      $i++;
    }
    
print "</table>";
  
  
  
}
else
{
  print $db->lasterror();
}
$db->close();

llxFooter();
?>
