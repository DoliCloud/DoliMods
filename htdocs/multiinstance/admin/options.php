<?php
/* Copyright (C) 2011-2012 Regis Houssin  <regis@dolibarr.fr>
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
 *  \file       multiinstance/admin/parameters.php
 *  \ingroup    multiinstance
 *  \brief      Page d'administration/configuration du module Multi-Company
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once("../class/actions_multiinstance.class.php");
require_once("../lib/multiinstance.lib.php");

$langs->load("admin");
$langs->load('multiinstance@multiinstance');

// Security check
if (! $user->admin || $user->entity) accessforbidden();


$action=GETPOST('action');


/*
 * Action
 */
if (preg_match('/set_(.*)/',$action,$reg))
{
    $code=$reg[1];
    if (dolibarr_set_const($db, $code, 1, 'chaine', 0, '', 0) > 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

if (preg_match('/del_(.*)/',$action,$reg))
{
    $code=$reg[1];
    if (dolibarr_del_const($db, $code, 0) > 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

/*
 * View
 */

llxHeader('',$langs->trans("MultiCompanySetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MultiCompanySetup"),$linkback,'multiinstance@multiinstance');

print '<br>';

$head = multiinstance_prepare_head();
dol_fiche_head($head, 'options', $langs->trans("ModuleSetup"));

$form=new Form($db);
$var=true;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";
print '</tr>';

/*
 * Formulaire parametres divers
 */

// Use cookie
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("EnableCookieLogin").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	print ajax_constantonoff('MULTICOMPANY_COOKIE_ENABLED','',0);
}
else
{
	if($conf->global->MULTICOMPANY_COOKIE_ENABLED == 0)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MULTICOMPANY_COOKIE_ENABLED">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else if($conf->global->MULTICOMPANY_COOKIE_ENABLED == 1)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MULTICOMPANY_COOKIE_ENABLED">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

// Login page combobox activation
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("HideLoginCombobox").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	print ajax_constantonoff('MULTICOMPANY_HIDE_LOGIN_COMBOBOX','',0);
}
else
{
	if($conf->global->MULTICOMPANY_HIDE_LOGIN_COMBOBOX == 0)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MULTICOMPANY_HIDE_LOGIN_COMBOBOX">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else if($conf->global->MULTICOMPANY_HIDE_LOGIN_COMBOBOX == 1)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MULTICOMPANY_HIDE_LOGIN_COMBOBOX">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

// Enable global sharings
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("EnableGlobalSharings").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	$input = array('showhide' => array('shareproduct','sharethirdparty','sharecategory'));
	print ajax_constantonoff('MULTICOMPANY_SHARINGS_ENABLED',$input,0);
}
else
{
	if($conf->global->MULTICOMPANY_SHARINGS_ENABLED == 0)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MULTICOMPANY_SHARINGS_ENABLED">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else if($conf->global->MULTICOMPANY_SHARINGS_ENABLED == 1)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MULTICOMPANY_SHARINGS_ENABLED">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

// Share products/services
$var=!$var;
print '<tr id="shareproduct" '.$bc[$var].(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) ? ' style="display:none;"' : '').'>';
print '<td>'.$langs->trans("ShareProductsAndServices").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	print ajax_constantonoff('MULTICOMPANY_PRODUCT_SHARING_ENABLED','',0);
}
else
{
	if($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED == 0)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MULTICOMPANY_PRODUCT_SHARING_ENABLED">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else if($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED == 1)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MULTICOMPANY_PRODUCT_SHARING_ENABLED">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

// Share thirparties and contacts
$var=!$var;
print '<tr id="sharethirdparty" '.$bc[$var].(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) ? ' style="display:none;"' : '').'>';
print '<td>'.$langs->trans("ShareThirdpartiesAndContacts").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	print ajax_constantonoff('MULTICOMPANY_SOCIETE_SHARING_ENABLED','',0);
}
else
{
	if($conf->global->MULTICOMPANY_SOCIETE_SHARING_ENABLED == 0)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MULTICOMPANY_SOCIETE_SHARING_ENABLED">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else if($conf->global->MULTICOMPANY_SOCIETE_SHARING_ENABLED == 1)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MULTICOMPANY_SOCIETE_SHARING_ENABLED">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

// Share categories
$var=!$var;
print '<tr id="sharecategory" '.$bc[$var].(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) ? ' style="display:none;"' : '').'>';
print '<td>'.$langs->trans("ShareCategories").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	print ajax_constantonoff('MULTICOMPANY_CATEGORY_SHARING_ENABLED','',0);
}
else
{
	if($conf->global->MULTICOMPANY_CATEGORY_SHARING_ENABLED == 0)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MULTICOMPANY_CATEGORY_SHARING_ENABLED">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else if($conf->global->MULTICOMPANY_CATEGORY_SHARING_ENABLED == 1)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MULTICOMPANY_CATEGORY_SHARING_ENABLED">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

/* Mode de gestion des droits :
 * Mode Off : mode Off : pyramidale. Les droits et les groupes sont gérés dans chaque entité : les utilisateurs appartiennent au groupe de l'entity pour obtenir leurs droits
 * Mode On : mode On : transversale : Les groupes ne peuvent appartenir qu'a l'entity = 0 et c'est l'utilisateur qui appartient à tel ou tel entity
 */
/*
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("GroupModeTransversal").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if ($conf->use_javascript_ajax)
{
	print ajax_constantonoff('MULTICOMPANY_TRANSVERSE_MODE');
}
else
{
	if($conf->global->MULTICOMPANY_TRANSVERSE_MODE == 0)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MULTICOMPANY_TRANSVERSE_MODE">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else if($conf->global->MULTICOMPANY_TRANSVERSE_MODE == 1)
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MULTICOMPANY_TRANSVERSE_MODE">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';
*/
print '</table>';

// Footer
llxFooter();
// Close database handler
$db->close();
?>
