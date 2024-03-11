<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Mário Batista        <mariorbatista@gmail.com> ISCTE-UL Moss
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *       \file       htdocs/saftpt/exportsaft.php
 *       \brief      user interface to build saf-t file
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/saftpt/class/html.formsaftpt.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/saftpt/class/saftpt.class.php';


$langs->load("saftpt@saftpt");

// Security check
// Protection if external user
if ($user->societe_id > 0) accessforbidden();

$action=GETPOST('action', 'alpha');
$step=GETPOST("step")?GETPOST("step"):1;

$user_id = $user->id;
$now=dol_now();

$saft = new SaftPt($db);

if ($action == 'create')
{

	$taxexemption = GETPOST('taxexemption_code','alpha');
	$date_ini = dol_mktime(0, 0, 0, GETPOST('date_ini_month'), GETPOST('date_ini_day'), GETPOST('date_ini_year'));
    $date_fim = dol_mktime(23, 59, 59, GETPOST('date_end_month'), GETPOST('date_end_day'), GETPOST('date_end_year'));


	// no start date
    if (empty($date_ini))
    {
		header('Location: exportsaft.php?action=request&error=nodateini');
        exit;
    }

    // no end date
    if (empty($date_fim))
    {
        header('Location: exportsaft.php?action=request&error=nodatefim');
        exit;
    }
	// no tax exemption
    if (empty($taxexemption))
    {
        header('Location: exportsaft.php?action=request&error=notax');
        exit;
    }

    // start date > end date
    if ($date_ini > $date_fim)
    {
        header('Location: exportsaft.php?action=request&error=datefim');
        exit;
    }


	$saft->taxexemption = $taxexemption;
    $saft->date_ini = $date_ini;
    $saft->date_fim = $date_fim;


	$saft->create_file();

	$step=2;

}

/*
 * View
 */

$form = new Form($db);

$canbuild=1; //var control to show build saf-t button

if ($step == 1 || $action == 'request' ) { //option to select the period

	$formtaxexemption = new FormSaftPt($db);

	llxHeader("",$langs->trans("MenuSaftExport"),"");

	$h = 0;

    $head[$h][0] = DOL_URL_ROOT.'/saftpt/exportsaft.php?step=1';
    $head[$h][1] = $langs->trans("Step")." 1";
    $hselected=$h;
    $h++;
	dol_fiche_head($head, $hselected, $langs->trans("MenuSaft"));
	// Security check
	if(!$user->hasRight('saftpt', 'exesaftpt', 'write'))
    {
        $errors[]=$langs->trans('CantCreateSaft');
    }
    else
    {
		//show error mensages
		if (GETPOST('error')) {

            switch(GETPOST('error')) {
                case 'datefim' :
                    $errors[] = $langs->trans('ErrorEndDateS');
                    break;
                case 'nodateini' :
                    $errors[] = $langs->trans('NoDateIniP');
                    break;
                case 'nodatefim' :
                    $errors[] = $langs->trans('NoDateFimP');
                    break;
                case 'notax' :
                    $errors[] = $langs->trans('NoTaxExemption');
                    break;
            }

            dol_htmloutput_mesg('',$errors,'error');
        }
		//check settings
		if(!$saft->country_pt()){
			print $langs->trans('ErrCountryPt').'<br>';
			$canbuild=0;
		}
		if(!$saft->currency_eur()){
			print $langs->trans('ErrCurrencyEur').'<br>';
			$canbuild=0;
		}
		if(!$saft->taxtype_pt()){
			print $langs->trans('ErrTaxType').'<br>';
			$canbuild=0;
		}
		if(!$saft->taxtype_val_pt()){
			print $langs->trans('ErrTaxTypeVal').'<br>';
			$canbuild=0;
		}

		if ($canbuild) {
			print '<table class="notopnoleftnoright" width="100%">';

			print $langs->trans("SaftHit1").'<br>';

			print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="create">';

			$var=true;

			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans("Parameter").'</td>';
			print '<td>'.$langs->trans("Value").'</td>';
			print "</tr>\n";

			$var=!$var;

			print '<tr>';
			print '<td class="fieldrequired">'.$langs->trans("DateSIni").'</td>';
			print '<td>';

			if(!isset($_GET['datep'])) {
				$form->select_date(-1,'date_ini_');
			} else {
				$tmpdate = dol_mktime(0, 0, 0, GETPOST('datepmonth'), GETPOST('datepday'), GETPOST('datepyear'));
				$form->select_date($tmpdate,'date_ini_');
			}
			print '</td>';
			print '</tr>';

			print '<tr>';
			print '<td class="fieldrequired">'.$langs->trans("DateSEnd").'</td>';
			print '<td>';
			if(!isset($_GET['datep'])) {
				$form->select_date(-1,'date_end_');
			} else {
				$tmpdate = dol_mktime(0, 0, 0, GETPOST('datefmonth'), GETPOST('datefday'), GETPOST('datefyear'));
				$form->select_date($tmpdate,'date_end_');
			}
			print '</td>';
			print '</tr>';

			print '<tr '.$bc[$var].'><td>';
			print $langs->trans("TaxExemptionDef").'</td><td>';
			print $formtaxexemption->select_taxexemption($conf->global->TAX_EXEMPTION_REASON,'taxexemption_code');
			//shows the combo list with the VAT exemption code
			if (empty($conf->global->TAX_EXEMPTION_REASON)) print ' '.img_warning($langs->trans("TaxExemptionEmpty"));
			print '</td></tr>';

			print '</table>';

			print '<br>';
			if ($canbuild) {
				print '<div align="center"><input type="submit" class="button" value="'.$langs->trans("BuildSaft").'"></div>';
			}

			print '</form>';

			print '</table>';
		}
	}
    print '</div>';
}

// after build saf-t file
if ($step == 2 ) {

	$sortfield = GETPOST("sortfield",'alpha');
	$sortorder = GETPOST("sortorder",'alpha');
	if(!$sortorder) $sortorder='desc';
	if(!$sortfield) $sortfield='name';

	llxHeader("",$langs->trans("MenuSaftExport"),"");

	$h = 0;

    $head[$h][0] = DOL_URL_ROOT.'/saftpt/exportsaft.php?step=1';
    $head[$h][1] = $langs->trans("Step")." 1";
    $hselected=$h;
    $h++;
	$head[$h][0] = DOL_URL_ROOT.'/saftpt/exportsaft.php?step=1';
    $head[$h][1] = $langs->trans("Step")." 2";
    $hselected=$h;
    $h++;

	dol_fiche_head($head, $hselected, $langs->trans("MenuSaft"));

	print $langs->trans("PeriodOf").' <b>'.dol_print_date($date_ini, '%Y-%m-%d'). '</b> '. $langs->trans("PeriodTo"). ' <b>'.dol_print_date($date_fim, '%Y-%m-%d').'</b><br>';
	print $langs->trans("TaxExemptionDef").': <b>'.$saft->taxexemption.'</b><br>';
	print $langs->trans("FileSaft").': <b>'.$saft->filexml.'</b><br>';

	$formfile = new FormFile($db);

	print '</div>';

	$filearray=dol_dir_list($conf->saftpt->dir_output.'/xml','files',0,'','',$sortfield,(strtolower($sortorder)=='asc'?SORT_ASC:SORT_DESC),1);
	$result=$formfile->list_of_documents($filearray,null,'saftpt','',1,'xml/',0,0,($langs->trans("NoSaftFileAvailable").'<br>'.$langs->trans("ToBuildBackupFileClickHere",DOL_URL_ROOT.'/saftpt/exportsaft.php')),0,$langs->trans("PreviousDumpFiles"));
}

llxFooter();

$db->close();
?>
