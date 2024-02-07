<?php
/* Copyright (C) 2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/*
 *	    \file       htdocs/ecotaxdeee/admin/setup.php
 *      \ingroup    ecotaxdeee
 *      \brief      Page more setup
 */

 // Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
dol_include_once("/ecotaxdeee/lib/ecotaxdeee.lib.php");

require_once DOL_DOCUMENT_ROOT.'/ecotaxdeee/class/ecotaxdeee.class.php';

if (!$user->admin) accessforbidden();


$langs->load("admin");
$langs->load("other");
$langs->load("ecotaxdeee@ecotaxdeee");

/*
 * Actions 
 */
$action = GETPOST('action', 'alpha');
$code = GETPOST('codeecotax');
$amount = GETPOST('amount');



if ($action == 'save') {
    $error = 0;
    if (empty($code) || empty($amount)) {
        $error++;
        setEventMessages("Inputs required", null, 'errors');
    }
    
    $ecotax = new Ecotaxdeee($db);
    $ecotax->code = dol_escape_htmltag($code);
    $ecotax->amount = dol_escape_htmltag($amount);
    
    if (!$error) {
        $result = $ecotax->create($user);
       
        if ($result > 0) {
            setEventMessages("record added successfully", null);
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            setEventMessages($ecotax->error, $ecotax->errors, 'errors');
           
        }
    }
}

/*
 * View
 */

$help_url='';
llxHeader('', '', $help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans("EcoTaxDeeSetup"), $linkback, 'setup');

$head=ecotaxdeee_prepare_head();

print dol_get_fiche_head($head, 'tabmoresetup', $langs->trans("EcoTaxDeeMoreSetup"), -1, "");


print '<form name="ecotaxdeeeconfigmore" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="action" value="save">';
print '<input type="hidden" name="token" value="'.newToken().'">';


print "<table class=\"noborder\" width=\"100%\">";

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// for code 
print '<tr class="oddeven">';
print "<td>".$langs->trans("CodeEcotax")."</td>";
print "<td><input type='text' name='codeecotax'/></td>";
print '</tr>';
//Amount
print '<tr class="oddeven">';
print "<td>".$langs->trans("Amount")."</td>";
print "<td><input type='text' name='amount'/></td>";
print '</tr>';

print "</table>";

print '<center>';

print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";


// Page end
print dol_get_fiche_end();

llxFooter();
$db->close();
