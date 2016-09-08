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

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

include_once(DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php');

$langs->load("ipphone@ipphone");


/*
 * Actions
 */

$actionsave=GETPOST('save','alpha');

// Sauvegardes parametres
if ($actionsave)
{
    $i=0;

    $db->begin();

    $i+=dolibarr_set_const($db,'IPPHONE_EXPORTKEY',trim(GETPOST('IPPHONE_EXPORTKEY','alpha')),'chaine',0,'',$conf->entity);

    if ($i >= 1)
    {
        $db->commit();
        setEventMessage($langs->trans("SetupSaved"));
    }
    else
    {
        $db->rollback();
        setEventMessage($langs->trans("SaveFailed"), 'errors');
    }
}



/*
 * View
 */

$help_url='EN:Module_ThomsonPhoneBook_EN|FR:Module_ThomsonPhoneBook|ES:M&oacute;dulo_ThomsonPhoneBook';

llxHeader('','',$help_url);

if (empty($conf->ipphone->enabled))
{
	dol_print_error($db,'Module was not enabled');
    exit;
}



$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("IPPhoneSetup"),$linkback,'setup');
print '<br>';


print '<form name="agendasetupform" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

$head=array();
dol_fiche_head($head, '', '', 0, '');

//print $langs->trans("IPPhoneSetupOtherDesc")."<br>\n";
//print "<br>\n";

print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
//print "<td>".$langs->trans("Examples")."</td>";
print "<td>&nbsp;</td>";
print "</tr>";

print "<tr class=\"impair\">";
print '<td class="fieldrequired">'.$langs->trans("PasswordToallowRead")."</td>";
print '<td><input required="required" type="text" class="flat" id="IPPHONE_EXPORTKEY" name="IPPHONE_EXPORTKEY" value="' . (GETPOST('IPPHONE_EXPORTKEY','alpha')?GETPOST('IPPHONE_EXPORTKEY','alpha'):$conf->global->IPPHONE_EXPORTKEY) . '" size="40">';
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



print "Module is enabled. To use it, you must setup your phone to call following URL:<br><br>\n";
$url=dol_buildpath('/ipphone/public/service.php',1);
$url=DOL_MAIN_URL_ROOT.(preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'/', '', $url)).'?key='.$conf->global->IPPHONE_EXPORTKEY;
print '<a href="'.$url.'">'.$url."<br>\n";




if (! empty($conf->use_javascript_ajax))
{
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
