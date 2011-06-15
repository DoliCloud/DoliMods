<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file        htdocs/compta/resultat/index.php
 *       \brief       Page reporting resultat
 *       \version     $Id: compta.php,v 1.6 2011/06/15 00:48:02 eldy Exp $
 */


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/report.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/date.lib.php");


$year_start=isset($_GET["year_start"])?$_GET["year_start"]:$_POST["year_start"];
$year_current = strftime("%Y",time());
$nbofyear=3;
if (! $year_start) {
	$year_start = $year_current - ($nbofyear-1);
	$year_end = $year_current;
}
else {
	$year_end=$year_start + ($nbofyear-1);
}

// Define modecompta ('CREANCES-DETTES' or 'RECETTES-DEPENSES')
$modecompta = $conf->compta->mode;
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

llxHeader();

$html=new Form($db);
$htmlother=new FormOther($db);

// Affiche en-tete du rapport
$nom=$langs->trans("CabinetMedAnnualSummaryInputOutput");
//$nom.='<br>('.$langs->trans("SeeReportInDueDebtMode",'<a href="'.$_SERVER["PHP_SELF"].'?year_start='.$year_start.'&modecompta=CREANCES-DETTES">','</a>').')';
$period="$year_start - $year_end";
if ($user->rights->societe->client->voir || $socid)
{
    $period.='<br>';
    $period.=$langs->trans('SalesRepresentatives'). ': ';
    $period.=$htmlother->select_salesrepresentatives($search_sale,'search_sale',$user);
}
$periodlink=($year_start?"<a href='".$_SERVER["PHP_SELF"]."?year_start=".($year_start-1)."&search_sale=".$search_sale."'>".img_previous()."</a> <a href='".$_SERVER["PHP_SELF"]."?year_start=".($year_start+1)."&search_sale=".$search_sale."'>".img_next()."</a>":"");
$description=$langs->trans("CabinetMedRulesResultInOut");
$builddate=time();

report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);


/*
 * Factures clients
 */
$subtotal_ht = 0;
$subtotal_ttc = 0;
$encaiss_chq = $encaiss_esp = $encaiss_tie = $encaiss_car = array();
$sql  = "SELECT f.datecons, f.fk_user_creation, SUM(f.montant_cheque) as montant_cheque, SUM(f.montant_espece) as montant_espece, SUM(f.montant_tiers) as montant_tiers, SUM(f.montant_carte) as montant_carte";
$sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_cons as f";
$sql.= " WHERE 1 = 1";
if ($search_sale) $sql.= " AND f.fk_user_creation = ".$search_sale;
if ($socid) $sql.= " AND f.fk_soc = $socid";
$sql.= " GROUP BY f.datecons, f.fk_user_creation";
$sql.= " ORDER BY f.datecons";
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
        $d=dol_print_date($db->jdate($row->datecons),'%Y-%m-%d');
		$dm=dol_print_date($db->jdate($row->datecons),'%Y-%m');
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
 * Show result array
 */

$totentrees=array();
$totsorties=array();

if ($conf->use_javascript_ajax)
{
    print "\n".'<script type="text/javascript" language="javascript">';
    print 'var openedId=\'\';
    jQuery(document).ready(function () {
    jQuery(\'.starthidden\').hide();
    // Enable this to allow personalized setup
    jQuery(".imgtoexpand").click(function() {
        var currentId = jQuery(this).attr(\'id\').substring(4);
        jQuery(\'.starthidden\').hide();
        if (openedId != currentId)
        {
            jQuery(\'.month_\'+currentId).show();
            openedId = currentId;
        } else openedId=\'\';
    });
    });';
    print '</script>'."\n";
}


print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td rowspan="2">'.$langs->trans("Month").'</td>';

for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	print '<td align="center" colspan="6">'.$annee.'</td>';
}
print '</tr>';
print '<tr class="liste_titre">';
for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
//	print '<td align="right">'.$langs->trans("Outcome").'</td>';
	print '<td align="right">'.$langs->trans("PaymentTypeShortCHQ").'</td>';
    print '<td align="right">'.$langs->trans("PaymentTypeShortCB").'</td>';
	print '<td align="right">'.$langs->trans("PaymentTypeShortLIQ").'</td>';
    print '<td align="right">'.$langs->trans("PaymentTypeThirdParty").'</td>';
    print '<td class="liste_total" align="right"><strong>'.$langs->trans("Total").'</strong></td>';
    print '<td width="6px"></td>';
}
print '</tr>';

$var=True;

// Loop on each month
$nb_mois_decalage = $conf->global->SOCIETE_FISCAL_MONTH_START?($conf->global->SOCIETE_FISCAL_MONTH_START-1):0;
for ($mois = 1+$nb_mois_decalage ; $mois <= 12+$nb_mois_decalage ; $mois++)
{
	$mois_modulo = $mois;
	if($mois>12) {$mois_modulo = $mois-12;}
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print "<td>";
	print '<table class="nobordernopadding"><tr valign="middle"><td width="24px">';
	print img_picto_common($langs->trans("Expand"),'treemenu/plustop3.gif','class="imgtoexpand" id="img_'.$mois_modulo.'"').'</td><td>';
	print dol_print_date(dol_mktime(12,0,0,$mois_modulo,1,$annee),"%B");
	print '</td></tr></table>';
	print "</td>";
	for ($annee = $year_start ; $annee <= $year_end ; $annee++)
	{
		$annee_decalage=$annee;
		if($mois>12) {$annee_decalage=$annee+1;}
		$case = strftime("%Y-%m",dol_mktime(12,0,0,$mois_modulo,1,$annee_decalage));

		/*print '<td align="right">&nbsp;';
		if ($decaiss_ttc[$case] != 0)
		{
			print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">'.price($decaiss_ttc[$case]).'</a>';
			$totsorties[$annee]+=$decaiss_ttc[$case];
		}
		print "</td>";*/

		print '<td align="right">';
		if ($encaiss_chq[$case] != 0)
		{
			//print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
			print price($encaiss_chq[$case]);
			//print '</a>';
			$totentrees_chq[$annee]+=$encaiss_chq[$case];
		}
		print "</td>";
        print '<td align="right">';
        if ($encaiss_car[$case] != 0)
        {
            //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
            print price($encaiss_car[$case]);
            //print '</a>';
            $totentrees_car[$annee]+=$encaiss_car[$case];
        }
        print "</td>";
		print '<td align="right">';
        if ($encaiss_esp[$case] != 0)
        {
            //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
            print price($encaiss_esp[$case]);
            //print '</a>';
            $totentrees_esp[$annee]+=$encaiss_esp[$case];
        }
        print "</td>";
        print '<td align="right">';
        if ($encaiss_tie[$case] != 0)
        {
            //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
            print price($encaiss_tie[$case]);
            //print '</a>';
            $totentrees_tie[$annee]+=$encaiss_tie[$case];
        }
        print "</td>";
        print '<td align="right" class="liste_total"><strong>';
        //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
        print price($encaiss_chq[$case]+$encaiss_esp[$case]+$encaiss_car[$case]+$encaiss_tie[$case]);
        //print '</a>';
        $totentrees[$annee]+=$encaiss_tie[$case];
        print "</strong></td>";
        print '<td style="border-right: 1px solid #BBBBBB;"></td>';
	}

	print '</tr>';

	$tmp=dol_get_last_day($annee,$mois_modulo,false);
    $tmparray=dol_getdate($tmp,true);
    $dayendmonth=$tmparray['mday'];
	if ($dayendmonth <= 28) $dayendmonth=29;
    if ($dayendmonth > 31) $dayendmonth=31;

	$var2=$var;
	for ($day=1; $day <= $dayendmonth; $day++)
	{
        $var2=!$var2;
        print '<tr class="starthidden month_'.$mois_modulo.($var2?' pair':' impair').'">';
        print "<td> &nbsp; &nbsp; &nbsp; &nbsp; ".dol_print_date(dol_mktime(12,0,0,$mois_modulo,$day,$annee),"%d");
        //print ' '.dol_print_date(dol_mktime(12,0,0,$mois_modulo,$day,$annee),"%m");
        print "</td>";

	    for ($annee2 = $year_start ; $annee2 <= $year_end ; $annee2++)
        {
            $annee_decalage2=$annee2;
            if($mois>12) {$annee_decalage2=$annee2+1;}
            $case2 = strftime("%Y-%m-%d",dol_mktime(12,0,0,$mois_modulo,$day,$annee_decalage2));

            print '<td align="right">';
            if ($encaiss_chq[$case2] != 0)
            {
                //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
                print price($encaiss_chq[$case2]);
                //print '</a>';
                //$totentrees_chq[$annee]+=$encaiss_chq[$case2];
            }
            print "</td>";
            print '<td align="right">';
            if ($encaiss_car[$case2] != 0)
            {
                //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
                print price($encaiss_car[$case2]);
                //print '</a>';
                //$totentrees_car[$annee]+=$encaiss_car[$case2];
            }
            print "</td>";
            print '<td align="right">';
            if ($encaiss_esp[$case2] != 0)
            {
                //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
                print price($encaiss_esp[$case2]);
                //print '</a>';
                //$totentrees_esp[$annee]+=$encaiss_esp[$case];
            }
            print "</td>";
            print '<td align="right">';
            if ($encaiss_tie[$case2] != 0)
            {
                //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
                print price($encaiss_tie[$case2]);
                //print '</a>';
                //$totentrees_tie[$annee]+=$encaiss_tie[$case];
            }
            print "</td>";
            print '<td align="right" class="liste_total"><strong>';
            //print '<a href="clientfourn.php?year='.$annee_decalage.'&month='.$mois_modulo.'">';
            print price($encaiss_chq[$case2]+$encaiss_esp[$case2]+$encaiss_car[$case2]+$encaiss_tie[$case2]);
            //print '</a>';
            //$totentrees[$annee]+=$encaiss_tie[$case2];
            print "</strong></td>";
            print '<td style="border-right: 1px solid #BBBBBB;"></td>';
        }
        print '</tr>';
	}

	print '<tr class="liste_titre" style="height: 4px !important;"><td colspan="19"></td></tr>';
}

// Total
$var=!$var;
$nbcols=0;
print '<tr class="liste_total"><td>'.$langs->trans("TotalTTC").'</td>';
for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	$nbcols+=2;
	//print '<td align="right">'.(isset($totsorties[$annee])?price($totsorties[$annee]):'&nbsp;').'</td>';
	print '<td class="liste_total" align="right">'.(isset($totentrees_chq[$annee])?price($totentrees_chq[$annee]):'&nbsp;').'</td>';
    print '<td class="liste_total" align="right">'.(isset($totentrees_car[$annee])?price($totentrees_car[$annee]):'&nbsp;').'</td>';
	print '<td class="liste_total" align="right">'.(isset($totentrees_esp[$annee])?price($totentrees_esp[$annee]):'&nbsp;').'</td>';
    print '<td class="liste_total" align="right">'.(isset($totentrees_tie[$annee])?price($totentrees_tie[$annee]):'&nbsp;').'</td>';
    print '<td class="liste_total" align="right"><strong>'.price($totentrees_chq[$annee]+$totentrees_esp[$annee]+$totentrees_car[$annee]+$totentrees_tie[$annee]).'</strong></td>';
    print '<td style="border-right: 1px solid #BBBBBB;"></td>';
}
print "</tr>\n";

print "</table>";

$db->close();

llxFooter('$Date: 2011/06/15 00:48:02 $ - $Revision: 1.6 $');

?>
