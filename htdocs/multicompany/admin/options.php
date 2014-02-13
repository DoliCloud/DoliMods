<?php
/* Copyright (C) 2011-2013 Regis Houssin  <regis.houssin@capnetworks.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *  \file       multicompany/admin/parameters.php
 *  \ingroup    multicompany
 *  \brief      Page d'administration/configuration du module Multi-Company
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require '../lib/multicompany.lib.php';
if (! class_exists('ActionsMulticompany')) {
	require '../class/actions_multicompany.class.php';
}

$langs->load("admin");
$langs->load('multicompany@multicompany');

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
print_fiche_titre($langs->trans("MultiCompanySetup"),$linkback,'multicompany@multicompany');

print '<br>';

$head = multicompany_prepare_head();
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
print ajax_constantonoff('MULTICOMPANY_COOKIE_ENABLED', '', 0);
print '</td></tr>';

// Login page combobox activation
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("HideLoginCombobox").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
print ajax_constantonoff('MULTICOMPANY_HIDE_LOGIN_COMBOBOX', '', 0);
print '</td></tr>';

// Enable global sharings
if (! empty($conf->societe->enabled) || ! empty($conf->product->enabled) || ! empty($conf->service->enabled) || ! empty($conf->categorie->enabled))
{
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans("EnableGlobalSharings").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	$input = array(
			'alert' => array(
					'set' => array(
							'info' => true,
							'yesButton' => $langs->trans('Ok'),
							'title' => $langs->trans('GlobalSharings'),
							'content' => img_info().' '.$langs->trans('GlobalSharingsInfo')
					)
			),
			'showhide' => array(
					'#shareproduct',
					'#sharethirdparty',
					'#sharecategory',
					'#sharebank'
			),
			'hide' => array(
					'#shareproduct',
					'#shareproductprice',
					'#sharestock',
					'#sharethirdparty',
					'#shareagenda',
					'#sharecategory',
					'#sharebank'
			),
			'del' => array(
					'MULTICOMPANY_PRODUCT_SHARING_ENABLED',
					'MULTICOMPANY_PRODUCTPRICE_SHARING_ENABLED',
					'MULTICOMPANY_STOCK_SHARING_ENABLED',
					'MULTICOMPANY_SOCIETE_SHARING_ENABLED',
					'MULTICOMPANY_AGENDA_SHARING_ENABLED',
					'MULTICOMPANY_CATEGORY_SHARING_ENABLED',
					'MULTICOMPANY_BANK_ACCOUNT_SHARING_ENABLED'
			)
	);
	print ajax_constantonoff('MULTICOMPANY_SHARINGS_ENABLED', $input, 0);
	print '</td></tr>';
}

// Share thirparties and contacts
if (! empty($conf->societe->enabled))
{
	$var=!$var;
	print '<tr id="sharethirdparty" '.$bc[$var].(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) ? ' style="display:none;"' : '').'>';
	print '<td>'.$langs->trans("ShareThirdpartiesAndContacts").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	$input = array(
			'showhide' => array(
					'#shareagenda'
			),
			'del' => array(
					'MULTICOMPANY_AGENDA_SHARING_ENABLED'
			)
	);
	print ajax_constantonoff('MULTICOMPANY_SOCIETE_SHARING_ENABLED', $input, 0);
	print '</td></tr>';
}

// Share agenda
if (! empty($conf->agenda->enabled) && ! empty($conf->societe->enabled))
{
	if (!empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) && !empty($conf->global->MULTICOMPANY_SOCIETE_SHARING_ENABLED))
		$var=!$var;
	$display=(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) || empty($conf->global->MULTICOMPANY_SOCIETE_SHARING_ENABLED) ? ' style="display:none;"' : '');
	print '<tr id="shareagenda" '.$bc[$var].$display.'>';
	print '<td>'.$langs->trans("ShareAgenda").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	print ajax_constantonoff('MULTICOMPANY_AGENDA_SHARING_ENABLED', '', 0);
	print '</td></tr>';
}

// Share products/services
if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
{
	$var=!$var;
	print '<tr id="shareproduct" '.$bc[$var].(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) ? ' style="display:none;"' : '').'>';
	print '<td>'.$langs->trans("ShareProductsAndServices").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	$input = array(
			'showhide' => array(
					'#shareproductprice',
					'#sharestock'
			),
			'del' => array(
					'MULTICOMPANY_PRODUCTPRICE_SHARING_ENABLED',
					'MULTICOMPANY_STOCK_SHARING_ENABLED'
			)
	);
	print ajax_constantonoff('MULTICOMPANY_PRODUCT_SHARING_ENABLED', $input, 0);
	print '</td></tr>';

	if (!empty($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED))
		$var=!$var;
	print '<tr id="shareproductprice" '.$bc[$var].(empty($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED) ? ' style="display:none;"' : '').'>';
	print '<td>'.$langs->trans("ShareProductsAndServicesPrices").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	print ajax_constantonoff('MULTICOMPANY_PRODUCTPRICE_SHARING_ENABLED', '', 0);
	print '</td></tr>';
}

// Share stock
if (! empty($conf->stock->enabled) && (! empty($conf->product->enabled) || ! empty($conf->service->enabled)))
{
	$var=!$var;
	$display=(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) || empty($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED) ? ' style="display:none;"' : '');
	print '<tr id="sharestock" '.$bc[$var].$display.'>';
	print '<td>'.$langs->trans("ShareStock").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	print ajax_constantonoff('MULTICOMPANY_STOCK_SHARING_ENABLED', '', 0);
	print '</td></tr>';
}

// Share categories
if (! empty($conf->categorie->enabled))
{
	$var=!$var;
	print '<tr id="sharecategory" '.$bc[$var].(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) ? ' style="display:none;"' : '').'>';
	print '<td>'.$langs->trans("ShareCategories").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	print ajax_constantonoff('MULTICOMPANY_CATEGORY_SHARING_ENABLED', '', 0);
	print '</td></tr>';
}

// Share bank
if (! empty($conf->banque->enabled))
{
	$var=!$var;
	print '<tr id="sharebank" '.$bc[$var].(empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED) ? ' style="display:none;"' : '').'>';
	print '<td>'.$langs->trans("ShareBank").'</td>';
	print '<td align="center" width="20">&nbsp;</td>';

	print '<td align="center" width="100">';
	print ajax_constantonoff('MULTICOMPANY_BANK_ACCOUNT_SHARING_ENABLED', '', 0);
	print '</td></tr>';
}


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
print ajax_constantonoff('MULTICOMPANY_TRANSVERSE_MODE');
print '</td></tr>';
*/
print '</table>';

// Footer
llxFooter();
// Close database handler
$db->close();
?>