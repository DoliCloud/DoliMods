<?php
/* Copyright (C) 2005-2019 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2007      Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/numberwords/admin/numberwords.php
 *	\ingroup    numberwords
 *	\brief      Setup page for numberwords module
 */


// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && @file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && @file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php";

if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("numberwords@numberwords");

$newvaltest='';
$outputlangs=new Translate('', $conf);
$outputlangs->setDefaultLang($langs->defaultlang);

$action=GETPOST('action', 'aZ09');
$value=GETPOST('value', 'nohtml');
$valuetest=GETPOST('valuetest', 'nohtml');
$level=GETPOST('level', 'int');

if (!isModEnabled("numberwords")) {
	print "Error: Module is not enabled\n";
	exit;
}


/*
 * Actions
 */

// Activate a model
if ($action == 'set') {
	$ret = dolibarr_set_const($db, $value, 1, 'chaine', 0, '', $conf->entity);
} elseif ($action == 'del') {
	$ret = dolibarr_del_const($db, $value, $conf->entity);
}

if ($action == 'test') {
	if (trim($valuetest) == '') {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Example")), 'errors');
	} else {
		if ($_POST["lang_id"]) $outputlangs->setDefaultLang($_POST["lang_id"]);

		$object = new StdClass();
		$object->id = 1;
		if ($level) {
			$object->total_ttc=price2num($valuetest);
			$source='__AMOUNT_TEXT__';
		} else {
			$object->number=price2num($valuetest);
			$source='__NUMBER_WORDS__';
		}

		$substitutionarray=array();
		complete_substitutions_array($substitutionarray, $outputlangs, $object);
		$newvaltest=make_substitutions($source, $substitutionarray);
	}
}



/*
 * View
 */

$htmlother=new FormAdmin($db);

llxHeader();

$object = new stdClass();
$object->id = 1;
$object->number = '989';
$object->total_ht = '824.99';
$object->total_ttc = '989.99';
$object->total_tva = '165.00';
$object->multicurrency_total_ht = '824.99';
$object->multicurrency_total_ttc = '989.99';
$object->multicurrency_total_tva = '165.00';
$object->multicurrency_code = $conf->currency;
$substitutionarray=array();
complete_substitutions_array($substitutionarray, $outputlangs, $object);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("NumberWordsSetup"), $linkback, 'setup');

print '<span class="opacitymedium">'.$langs->trans("DescNumberWords").'</span><br>';
print '<br>';


$h=0;
$head[$h][0] = $_SERVER["PHP_SELF"];
$head[$h][1] = $langs->trans("Setup");
$head[$h][2] = 'tabsetup';
$h++;

$head[$h][0] = 'about.php';
$head[$h][1] = $langs->trans("About");
$head[$h][2] = 'tababout';
$h++;

dol_fiche_head($head, 'tabsetup', '', (((float) DOL_VERSION <= 8)?0:-1));


// Mode
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="test">';

print $langs->trans("NUMBERWORDS_USE_CURRENCY_SYMBOL").' ';

// Active
if (! empty($conf->global->NUMBERWORDS_USE_CURRENCY_SYMBOL)) {
	print '<a class="valignmiddle" href="' . $_SERVER["PHP_SELF"] . '?action=del&value=NUMBERWORDS_USE_CURRENCY_SYMBOL&level='.urlencode($level).'&valuetest='.urlencode($valuetest).'&token='.newToken().'">';
	print img_picto($langs->trans("Enabled"), 'switch_on');
	print '</a>';

	print '<br>';

	print $langs->trans("NUMBERWORDS_USE_ADD_SHORTCODE_WITH_SYMBOL").' ';
	// Active
	if (! empty($conf->global->NUMBERWORDS_USE_ADD_SHORTCODE_WITH_SYMBOL)) {
		print '<a class="valignmiddle" href="' . $_SERVER["PHP_SELF"] . '?action=del&value=NUMBERWORDS_USE_ADD_SHORTCODE_WITH_SYMBOL&level='.urlencode($level).'&valuetest='.urlencode($valuetest).'&token='.newToken().'">';
		print img_picto($langs->trans("Enabled"), 'switch_on');
		print '</a>';
	} else {
		print '<a class="valignmiddle" href="' . $_SERVER["PHP_SELF"] . '?action=set&value=NUMBERWORDS_USE_ADD_SHORTCODE_WITH_SYMBOL&level='.urlencode($level).'&valuetest='.urlencode($valuetest).'&token='.newToken().'">' . img_picto($langs->trans("Disabled"), 'switch_off') . '</a>';
	}
} else {
	print '<a class="valignmiddle" href="' . $_SERVER["PHP_SELF"] . '?action=set&value=NUMBERWORDS_USE_CURRENCY_SYMBOL&level='.urlencode($level).'&valuetest='.urlencode($valuetest).'&token='.newToken().'">' . img_picto($langs->trans("Disabled"), 'switch_off') . '</a>';
}
print '<br>';

print '<br>';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Type").'</td>';
print '<td>'.$langs->trans("Example").'</td>';
print '<td>'.$langs->trans("Language").'</td>';
print '<td>&nbsp;</td>';
print '<td>'.$langs->trans("Result").'</td>';
print "</tr>\n";

print '<tr class="oddeven"><td width="140">'.$langs->trans("Number").'</td>';
print '<td>'.$object->number.'</td>';
print '<td>'.$outputlangs->defaultlang.'</td>';
print '<td>&nbsp;</td>';
$newval=make_substitutions('__NUMBER_WORDS__', $substitutionarray);
print '<td>'.$newval.'</td></tr>';

print '<tr class="oddeven"><td width="140">'.$langs->trans("Amount").'</td>';
print '<td>'.$object->total_ttc.'</td>';
print '<td>'.$outputlangs->defaultlang.'</td>';
print '<td>&nbsp;</td>';
$newval=make_substitutions('__AMOUNT_TEXT__', $substitutionarray);
print '<td>'.$newval.'</td></tr>';

print '<tr class="oddeven">';
print '<td><select class="flat" name="level">';
print '<option value="0" '.($level=='0'?'SELECTED':'').'>'.$langs->trans("Number").'</option>';
print '<option value="1" '.($level=='1'?'SELECTED':'').'>'.$langs->trans("Amount").'</option>';
print '</select>';
print '</td>';
print '<td><input type="text" name="valuetest" class="flat" value="'.$valuetest.'"></td>';
print '<td>';
print $htmlother->select_language(GETPOST('lang_id', 'alpha')?GETPOST('lang_id', 'alpha'):$langs->defaultlang, 'lang_id');
print '</td>';
print '<td><input type="submit" class="button" value="'.$langs->trans("ToTest").'"></td>';
print '<td><strong>'.$newvaltest.'</strong>';
print '</td>';
print '</tr>';

print '</table>';

print "</form>\n";

dol_fiche_end();

// Warning on accurancy
list($whole, $decimal) = explode('.', $value);
if ($level) {
	if (strlen($decimal) > $conf->global->MAIN_MAX_DECIMALS_TOT) {
		print '<font class="warning">'.$langs->trans("Note").': '.$langs->trans("MAIN_MAX_DECIMALS_TOT").': ' . getDolGlobalString('MAIN_MAX_DECIMALS_TOT').'</font>';
		print ' - <a href="'.DOL_URL_ROOT.'/admin/limits.php">'.$langs->trans("SetupToChange").'</a>';
	} else {
		print '<font class="info">'.$langs->trans("Note").': '.$langs->trans("MAIN_MAX_DECIMALS_TOT").': ' . getDolGlobalString('MAIN_MAX_DECIMALS_TOT').'</font>';
		print ' - <a href="'.DOL_URL_ROOT.'/admin/limits.php">'.$langs->trans("SetupToChange").'</a>';
	}
}
print '<br>';
print '<font class="info">'.$langs->trans("CompanyCurrency").': '.$conf->currency.'</font>';
print ' - <a href="'.DOL_URL_ROOT.'/admin/company.php">'.$langs->trans("SetupToChange").'</a>';


llxFooter();

$db->close();
