<?php
/* Copyright (C) 2012	Juanjo Menent	<jmenent@2byte.es>
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
 *  \file        htdocs/pos/backend/results.php
 *	\brief       Page reporting
 */

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory
require_once(DOL_DOCUMENT_ROOT."/compta/tva/class/tva.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/sociales/class/chargesociales.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/report.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/tax.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");


$langs->load("bills");

// Security check
$socid = GETPOST("socid");
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->pos->stats)
accessforbidden();

// Date range
$year=GETPOST("year");
if (empty($year))
{
    $year_current = strftime("%Y",dol_now());
    $month_current = strftime("%m",dol_now());
    $year_start = $year_current;
} else {
    $year_current = $year;
    $month_current = strftime("%m",dol_now());
    $year_start = $year;
}
$date_start=dol_mktime(0,0,0,$_REQUEST["date_startmonth"],$_REQUEST["date_startday"],$_REQUEST["date_startyear"]);	// Date for local PHP server
$date_end=dol_mktime(23,59,59,$_REQUEST["date_endmonth"],$_REQUEST["date_endday"],$_REQUEST["date_endyear"]);	// Date for local PHP server
// Quarter
if (empty($date_start) || empty($date_end)) // We define date_start and date_end
{
    $q=GETPOST("q")?GETPOST("q"):0;
    if ($q==0)
    {
        // We define date_start and date_end
        $year_end=$year_start;
        $month_start=GETPOST("month")?GETPOST("month"):($conf->global->SOCIETE_FISCAL_MONTH_START?($conf->global->SOCIETE_FISCAL_MONTH_START):1);
        if (! GETPOST('month'))
        {
            if (! GETPOST("year") &&  $month_start > $month_current)
            {
                $year_start--;
                $year_end--;
            }
            $month_end=$month_start-1;
            if ($month_end < 1) $month_end=12;
            else $year_end++;
        }
        else $month_end=$month_start;
        $date_start=dol_get_first_day($year_start,$month_start,false); $date_end=dol_get_last_day($year_end,$month_end,false);
    }
    if ($q==1) { $date_start=dol_get_first_day($year_start,1,false); $date_end=dol_get_last_day($year_start,3,false); }
    if ($q==2) { $date_start=dol_get_first_day($year_start,4,false); $date_end=dol_get_last_day($year_start,6,false); }
    if ($q==3) { $date_start=dol_get_first_day($year_start,7,false); $date_end=dol_get_last_day($year_start,9,false); }
    if ($q==4) { $date_start=dol_get_first_day($year_start,10,false); $date_end=dol_get_last_day($year_start,12,false); }
}
else
{
    // TODO We define q

}

/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('','',$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$html=new Form($db);

$nom=$langs->trans("RapportSales");
//$nom.='<br>('.$langs->trans("SeeReportInInputOutputMode",'<a href="'.$_SERVER["PHP_SELF"].'?year='.$year.(GETPOST("month")>0?'&month='.GETPOST("month"):'').'&modecompta=RECETTES-DEPENSES">','</a>').')';
$period=$html->select_date($date_start,'date_start',0,0,0,'',1,0,1).' - '.$html->select_date($date_end,'date_end',0,0,0,'',1,0,1);
$description=$langs->trans("RulesResult");
$builddate=time();

report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);

// Show report array
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td width="10%">&nbsp;</td><td>&nbsp;</td>';
print "<td align=\"right\">".$langs->trans("AmountHT")."</td>";
print "<td align=\"right\">".$langs->trans("AmountTTC")."</td>";
print "</tr>\n";

/*
 * Tickets clients
 */
print '<tr><td colspan="4">'.$langs->trans("ClientTicket").'</td></tr>';

$sql = "SELECT s.nom, s.rowid as socid, sum(f.total_ht) as amount_ht, sum(f.total_ttc) as amount_ttc";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
$sql.= ", ".MAIN_DB_PREFIX."pos_ticket as f";
$sql.= " WHERE f.fk_soc = s.rowid";
$sql.= " AND f.fk_statut in (1,2,3,4)";
if ($date_start && $date_end) $sql.= " AND f.date_closed >= '".$db->idate($date_start)."' AND f.date_closed <= '".$db->idate($date_end)."'";

$sql.= " AND f.entity = ".$conf->entity;
if ($socid) $sql.= " AND f.fk_soc = ".$socid;
$sql.= " GROUP BY s.nom, s.rowid";
$sql.= " ORDER BY s.nom";


dol_syslog("get customer invoices sql=".$sql);
$result = $db->query($sql);
if ($result) {
    $num = $db->num_rows($result);
    $i = 0;
    $var=true;
    while ($i < $num)
    {
        $objp = $db->fetch_object($result);
        $var=!$var;

        print "<tr $bc[$var]><td>&nbsp;</td>";
        print "<td>".$langs->trans("Tickets")." <a href=\"../backend/liste.php?socid=".$objp->socid."\">$objp->nom</td>\n";

        print "<td align=\"right\">".price($objp->amount_ht)."</td>\n";
        print "<td align=\"right\">".price($objp->amount_ttc)."</td>\n";

        $total_ht = $total_ht + $objp->amount_ht;
        $total_ttc = $total_ttc + $objp->amount_ttc;
        print "</tr>\n";
        $i++;
    }
    $db->free($result);
} else {
    dol_print_error($db);
}

if ($total_ttc == 0)
{
    $var=!$var;
    print "<tr $bc[$var]><td>&nbsp;</td>";
    print '<td colspan="3">'.$langs->trans("None").'</td>';
    print '</tr>';
}

print '<tr class="liste_total">';
print '<td colspan="3" align="right">'.price($total_ht).'</td>';
print '<td colspan="3" align="right">'.price($total_ttc).'</td>';
print '</tr>';

print "</table>";
print '<br>';

llxFooter();

$db->close();
?>