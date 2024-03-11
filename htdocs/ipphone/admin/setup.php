<?php
/* Copyright (C) 2008-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/ipphone/admin/setup.php
 *  \ingroup    ipphone
 *	\brief      Page to setup module ipphone
 *				You configure your phones to call URL
 *				http://mydolibarr/ipphone/public/service.php?search=#SEARCH
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

include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$langs->load("admin");
$langs->load("ipphone@ipphone");


/*
 * Actions
 */

$actionsave=GETPOST('save', 'alpha');

// Sauvegardes parametres
if ($actionsave) {
	$i=0;

	$db->begin();

	$i+=dolibarr_set_const($db, 'IPPHONE_XMLTAG', trim(GETPOST('IPPHONE_XMLTAG', 'alpha')), 'chaine', 0, '', $conf->entity);
	$i+=dolibarr_set_const($db, 'IPPHONE_EXPORTKEY', trim(GETPOST('IPPHONE_EXPORTKEY', 'alpha')), 'chaine', 0, '', $conf->entity);

	if ($i >= 2) {
		$db->commit();
		setEventMessage($langs->trans("SetupSaved"));
	} else {
		$db->rollback();
		setEventMessage($langs->trans("SaveFailed"), 'errors');
	}
}



/*
 * View
 */

$help_url='EN:Module_ThomsonPhoneBook_EN|FR:Module_ThomsonPhoneBook|ES:M&oacute;dulo_ThomsonPhoneBook';

llxHeader('', '', $help_url);

if (!isModEnabled("ipphone")) {
	dol_print_error($db, 'Module was not enabled');
	exit;
}

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans("IPPhoneSetup"), $linkback, 'setup');

print '<form name="agendasetupform" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';

$head = array();
$h = 0;

$head[$h][0] = "setup.php";
$head[$h][1] = $langs->trans("Setup");
$head[$h][2] = 'setup';
$h++;

dol_fiche_head($head, 'setup', '', -1, '');

//print $langs->trans("IPPhoneSetupOtherDesc")."<br>\n";
//print "<br>\n";

print '<table class="noborder centpercent">';

print '<tr class="liste_titre">';
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
//print "<td>".$langs->trans("Examples")."</td>";
print "<td>&nbsp;</td>";
print "</tr>";

print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("TagForXmlFile")."</td>";
print '<td><input required="required" type="text" class="flat" id="IPPHONE_XMLTAG" name="IPPHONE_XMLTAG" value="' . (GETPOSTISSET('IPPHONE_XMLTAG')?GETPOST('IPPHONE_XMLTAG', 'alpha'):(getDolGlobalString('IPPHONE_XMLTAG', 'CiscoIPPhoneDirectory'))) . '">';
print '</td>';
print "<td>CiscoIPPhoneDirectory, YealinkIPPhoneDirectory, ThompsonDirectory</td>";
print "</tr>";


print '<tr class="oddeven">';
print '<td class="fieldrequired">'.$langs->trans("PasswordToallowRead")."</td>";
print '<td><input required="required" type="text" class="flat minwidth300" id="IPPHONE_EXPORTKEY" name="IPPHONE_EXPORTKEY" value="' . (GETPOSTISSET('IPPHONE_EXPORTKEY')?GETPOST('IPPHONE_EXPORTKEY', 'alpha') : getDolGlobalString('IPPHONE_EXPORTKEY')) . '">';
if (! empty($conf->use_javascript_ajax))
	print '&nbsp;'.img_picto($langs->trans('Generate'), 'refresh', 'id="generate_token" class="linkobject"');
print '</td>';
print "<td>&nbsp;</td>";
print "</tr>";

print '</table>';

dol_fiche_end();

print '<div class="center">';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</div>";

print "</form>\n";


print '<br><br>';
print '<span class="opacitymedium">'.$langs->trans("ModuleEnabledUseURL").":</span><br><br>\n";
$url=dol_buildpath('/ipphone/public/service.php', 1);
$url=DOL_MAIN_URL_ROOT.(preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'/', '', $url)).'?format=xml&key=' . getDolGlobalString('IPPHONE_EXPORTKEY');
print 'XML: <a href="'.$url.'">'.$url."</a><br>\n";
$url=dol_buildpath('/ipphone/public/service.php', 1);
$url=DOL_MAIN_URL_ROOT.(preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'/', '', $url)).'?format=csv&key=' . getDolGlobalString('IPPHONE_EXPORTKEY');
print 'CSV: <a href="'.$url.'">'.$url."</a><br>\n";
print '<br>';
print $langs->trans("ItReturnListOfThirdAndContacts")."<br>\n";



if (! empty($conf->use_javascript_ajax)) {
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {
            $("#generate_token").click(function() {
            	$.get( "'.DOL_URL_ROOT.'/core/ajax/security.php", {
            		action: \'getrandompassword\',
            		generic: true
				},
				function(token) {
					$("#IPPHONE_EXPORTKEY").val(token);
				});
            });
    });';
	print '</script>';
}


llxFooter();

$db->close();
