<?php
/* Copyright (C) 2001-2002 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: reduc.php,v 1.3 2010/07/19 18:20:19 eldy Exp $
 */

require("../main.inc.php");

llxHeader();

print_titre ("Abonnement de reduction");

print "<table class=\"border\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">";
print "<tr class=\"liste_titre\">";
print '<td>Date</td><td>'.$langs->trans("Description").'</td>';
print "<td align=\"right\"><a href=\"reduc.php?vue=credit\">Montant</a></td>";
print "</tr>\n";


$sql = "SELECT b.rowid, b.date_debut as debut, b.date_fin as fin, b.amount, b.label";
$sql .= " FROM ".MAIN_DB_PREFIX."voyage_reduc as b ";

$result = $db->query($sql);
if ($result) {
	$var=True;
	$num = $db->num_rows();
	$i = 0; $total = 0;

	$sep = 0;

	while ($i < $num) {
		$objp = $db->fetch_object($result);
		$total = $total + $objp->amount;
		$time = time();

		$var=!$var;

		print "<tr $bc[$var]>";
		print "<td>".strftime("%d %b %y",$db->jdate($objp->debut))." au ".strftime("%d %b %y",$db->jdate($objp->fin))."</TD>\n";
		print "<td>".$objp->label."</td>";
		print "<td align=\"right\">".price($objp->amount)."</TD>\n";
		print "</tr>";

		$i++;
	}
	$db->free();
}

print "<tr><td align=\"right\" colspan=\"2\">".$langs->trans("TotalHT").":</td>";
print "<td align=\"right\"><b>".price($total)."</b></td></tr>\n";


print "</table>";


$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2010/07/19 18:20:19 $ r&eacute;vision $Revision: 1.3 $</em>");
?>
