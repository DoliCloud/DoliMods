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
 * \file htdocs/ventilation/bookkeeping/liste.php
 * \ingroup compta
 * \brief Onglet de gestion de parametrages des ventilations
 * \version $Revision: 1.12 $
 */

// Dolibarr environment
$res = @include ("../main.inc.php");
if (! $res && file_exists ( "../main.inc.php" )) $res = @include ("../main.inc.php");
if (! $res && file_exists ( "../../main.inc.php" )) $res = @include ("../../main.inc.php");
if (! $res && file_exists ( "../../../main.inc.php" )) $res = @include ("../../../main.inc.php");

require_once ("../compta/class/html.formventilation.class.php");
require_once ("../compta/class/bookkeeping.class.php");

if (! $res) die ( "Include of main fails" );



$page = GETPOST ( "page" );
$sortorder = GETPOST ( "sortorder" );
$sortfield = GETPOST ( "sortfield" );
$action = GETPOST ( 'action', 'alpha' );

if ($sortorder == "") $sortorder = "ASC";
if ($sortfield == "") $sortfield = "bk.rowid";

$offset = $conf->liste_limit * $page;

$formventilation = new FormVentilation ( $db );

/*
 * Action
 */
if ($action == 'delbookkeeping') {

	$import_key = GETPOST ( 'importkey', 'alpha' );

	if (! empty ( $import_key )) {
		$object = new BookKeeping ( $db );
		$result = $object->delete_by_importkey ( $import_key );
		Header("Location: liste.php");
		if ($result < 0) {
			setEventMessage ( $object->errors, 'errors' );
		}
	}
} // export csv
else if ($action == 'export_csv') {

	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment;filename=export_csv.csv');

	$object = new BookKeeping ( $db );
	$result = $object->export_bookkeping ('ebp');
	if ($result < 0) {
		setEventMessage ( $object->errors, 'errors' );
	}

	foreach($object->linesexport as $line) {
		print $line->id.',';
		print '"'.dol_print_date($line->doc_date,'%d%m%Y').'",';
		print '"'.$line->code_journal.'",';
		print '"'.$line->numero_compte.'",';
		print '"'.substr($line->code_journal,0,2).'",';
		print '"'.substr($line->doc_ref,0,40).'",';
		print '"'.$line->num_piece.'",';
		print '"'.$line->montant.'",';
		print '"'.$line->sens.'",';
		print '"'.dol_print_date($line->doc_date,'%d%m%Y').'",';
		print '"'.$conf->currency.'",';
		print "\n";
	}
}

else {

llxHeader ( '', 'Compta - Grand Livre' );

/*
 * Mode Liste
 *
 *
 *
 */

	$sql = "SELECT bk.rowid, bk.doc_date, bk.doc_type, bk.doc_ref, bk.code_tiers, bk.numero_compte , bk.label_compte, bk.debit , bk.credit, bk.montant , bk.sens , bk.code_journal , bk.piece_num ";

	$sql .= " FROM " . MAIN_DB_PREFIX . "bookkeeping as bk";

	if (dol_strlen ( trim ( GETPOST ( "search_doc_type" ) ) )) {

		$sql .= " WHERE bk.doc_type LIKE '%" . GETPOST ( "search_doc_type" ) . "%'";

		if (dol_strlen ( trim ( GETPOST ( "search_doc_ref" ) ) )) {
			$sql .= " AND bk.doc_ref LIKE '%" . GETPOST ( "search_doc_ref" ) . "%'";
		}
	}
	if (dol_strlen ( trim ( GETPOST ( "search_doc_ref" ) ) )) {
		$sql .= " WHERE bk.doc_ref LIKE '%" . GETPOST ( "search_doc_ref" ) . "%'";
	}
	if (dol_strlen ( trim ( GETPOST ( "search_compte" ) ) )) {
		$sql .= " WHERE bk.numero_compte LIKE '%" . GETPOST ( "search_compte" ) . "%'";
	}

	if (dol_strlen ( trim ( GETPOST ( "search_tiers" ) ) )) {
		$sql .= " WHERE bk.code_tiers LIKE '%" . GETPOST ( "search_tiers" ) . "%'";
	}

	$sql .= " ORDER BY $sortfield $sortorder " . $db->plimit ( $conf->liste_limit + 1, $offset );

	dol_syslog ( "bookkeping:liste:create sql=" . $sql, LOG_DEBUG );
	$resql = $db->query ( $sql );
	if ($resql) {
		$num = $db->num_rows ( $resql );
		$i = 0;

		print_barre_liste ( "Grand Livre", $page, "liste.php", "", $sortfield, $sortorder, '', $num );

		print '<form name="add" action="' . $_SERVER ["PHP_SELF"] . '" method="POST">';
		print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
		print '<input type="hidden" name="action" value="delbookkeeping">';

		print $formventilation->select_bookkeeping_importkey ( 'importkey', GETPOST ( 'importkey' ) );

		print '<div class="inline-block divButAction"><input type="submit" class="butAction" value="' . $langs->trans ( "DelBookKeeping" ) . '" /></div>';

		print '</form>';

		print '<a href="./fiche.php?action=create" class="butAction">Nouveau mouvement comptable</a>';


		print '<form name="add" action="' . $_SERVER ["PHP_SELF"] . '" method="POST">';
		print '<input type="hidden" name="token" value="' . $_SESSION ['newtoken'] . '">';
		print '<input type="hidden" name="action" value="export_csv">';
		print '<input type="submit" class="button" style="float: right;" value="Export CSV" />';
		print '</form>';

		print "<table class=\"noborder\" width=\"100%\">";
		print '<tr class="liste_titre">';
		print_liste_field_titre ( $langs->trans ( "Doctype" ), "liste.php", "bk.doc_type" );
		print_liste_field_titre ( $langs->trans ( "Docdate" ), "liste.php", "bk.doc_date" );
		print_liste_field_titre ( $langs->trans ( "Docref" ), "liste.php", "bk.doc_ref" );
		print_liste_field_titre ( $langs->trans ( "Numerocompte" ), "liste.php", "bk.numero_compte" );
		print_liste_field_titre ( $langs->trans ( "Code_tiers" ), "liste.php", "bk.code_tiers" );
		print_liste_field_titre ( $langs->trans ( "Labelcompte" ), "liste.php", "bk_label_compte" );
		print_liste_field_titre ( $langs->trans ( "Debit" ), "liste.php", "bk.debit" );
		print_liste_field_titre ( $langs->trans ( "Credit" ), "liste.php", "bk.credit" );
		print_liste_field_titre ( $langs->trans ( "Amount" ), "liste.php", "bk.montant" );
		print_liste_field_titre ( $langs->trans ( "Sens" ), "liste.php", "bk.sens" );
		print_liste_field_titre ( $langs->trans ( "Codejournal" ), "liste.php", "bk.code_journal" );
		print "</tr>\n";

		print '<tr class="liste_titre">';
		print '<form action="liste.php" method="GET">';
		print '<td><input type="text" name="search_doc_type" value="' . $_GET ["search_doc_type"] . '"></td>';
		print '<td>&nbsp;</td>';
		print '<td><input type="text" name="search_doc_refe" value="' . $_GET ["search_doc_ref"] . '"></td>';
		print '<td><input type="text" name="search_compte" value="' . $_GET ["search_compte"] . '"></td>';
		print '<td><input type="text" name="search_tiers" value="' . $_GET ["search_tiers"] . '"></td>';
		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td>&nbsp;</td>';
		print '<td align="right">';
		print '<input type="image" class="liste_titre" name="button_search" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/search.png" value="' . dol_escape_htmltag ( $langs->trans ( "Search" ) ) . '" title="' . dol_escape_htmltag ( $langs->trans ( "Search" ) ) . '">';
		print '</td>';
		print '</form>';
		print '</tr>';

		$var = True;

		while ( $i < min ( $num, $conf->liste_limit ) ) {
			$obj = $db->fetch_object ( $resql );
			$var = ! $var;

			print "<tr $bc[$var]>";

			print '<td><a href="./fiche.php?piece_num=' . $obj->piece_num . '">';
			print img_edit ();
			print '</a>&nbsp;' . $obj->doc_type . '</td>' . "\n";
			print '<td>' . dol_print_date ( $db->jdate ( $obj->doc_date ), 'day' ) . '</td>';
			print '<td>' . $obj->doc_ref . '</td>';
			print '<td>' . $obj->numero_compte . '</td>';
			print '<td>' . $obj->code_tiers . '</td>';
			print '<td>' . $obj->label_compte . '</td>';
			print '<td>' . $obj->debit . '</td>';
			print '<td>' . $obj->credit . '</td>';
			print '<td>' . $obj->montant . '</td>';
			print '<td>' . $obj->sens . '</td>';
			print '<td>' . $obj->code_journal . '</td>';
			print "</tr>\n";
			$i ++;
		}
		print "</table>";
		$db->free ( $resql );
	} else {
		dol_print_error ( $db );
	}

	llxFooter ( '' );
}

$db->close ();
?>
