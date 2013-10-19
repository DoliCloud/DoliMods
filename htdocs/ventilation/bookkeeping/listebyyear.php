<?PHP
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005      Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2013 Florian Henry	  <florian.henry@open-concept.pro>
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
 *
 * $Id: liste.php,v 1.12 2011/07/31 22:23:31 eldy Exp $
 */

/**
        \file       htdocs/compta/param/comptes/liste.php
        \ingroup    compta
        \brief      Onglet de gestion de parametrages des ventilations
        \version    $Revision: 1.12 $
*/

// Dolibarr environment
$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");



$year=$_GET["year"];
if ($year == 0 )
{
  $year_current = strftime("%Y",time());
  $year_start = $year_current;
} else {
  $year_current = $year;
  $year_start = $year;
}

llxHeader('',$langs->trans("Bookkeeping"));

$textprevyear="<a href=\"listebyyear.php?year=" . ($year_current-1) . "\">".img_previous()."</a>";
$textnextyear=" <a href=\"listebyyear.php?year=" . ($year_current+1) . "\">".img_next()."</a>";


print_fiche_titre("Grand Livre $textprevyear ".$langs->trans("Year")." $year_start $textnextyear");


/*
 * Mode Liste
 *
 *
 *
 */

$sql = "SELECT bk.rowid, bk.doc_date, bk.doc_type, bk.doc_ref, bk.code_tiers, bk.numero_compte , bk.label_compte, bk.debit , bk.credit, bk.montant , bk.sens ";
$sql .= " FROM ".MAIN_DB_PREFIX."bookkeeping as bk";
//$sql .= " WHERE bk.doc_date >= '".$db->idate(dol_get_first_day($y,1,false))."'";
//$sql .= "  AND bk.doc_date <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " ORDER BY bk.doc_date ";

$resql = $db->query($sql);
if ($resql)
{
  $num = $db->num_rows($resql);
  $i = 0;

 

  print '<table class="liste">';
  print '<tr class="liste_titre">';
  print_liste_field_titre($langs->trans("Doctype"));
  print_liste_field_titre($langs->trans("Docdate"));
  print_liste_field_titre($langs->trans("Docref"));
  print_liste_field_titre($langs->trans("Account"));
  print_liste_field_titre($langs->trans("Code_tiers"));
  print_liste_field_titre($langs->trans("Labelcompte"));
  print_liste_field_titre($langs->trans("Debit"));
  print_liste_field_titre($langs->trans("Credit"));
  print_liste_field_titre($langs->trans("Amount"));
  print_liste_field_titre($langs->trans("Sens"));
  print "</tr>\n";

  $var=True;

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object($resql);
      $var=!$var;

      print "<tr $bc[$var]>";

      print '<td><a href="./fiche.php?action=update&id='.$obj->rowid.'">';
	    print img_edit();
	    print '</a>&nbsp;'.$obj->doc_type.'</td>'."\n";
	    print '<td>'.dol_print_date($db->jdate($obj->doc_date)).'</td>';
      print '<td>'.$obj->doc_ref.'</td>';
      print '<td>'.$obj->numero_compte.'</td>';
      print '<td>'.$obj->code_tiers.'</td>';
      print '<td>'.$obj->label_compte.'</td>';
      print '<td>'.$obj->debit.'</td>';
      print '<td>'.$obj->credit.'</td>';
      print '<td>'.$obj->montant.'</td>';
      print '<td>'.$obj->sens.'</td>';
      
      /*
      print '<td align="right" width="100">';
      print '</td>';
      */
      print "</tr>\n";
      $i++;
    }
  print "</table>";
  $db->free($resql);
}
else
{
  dol_print_error($db);
}

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2011/07/31 22:23:31 $ r&eacute;vision $Revision: 1.12 $</em>");
?>
