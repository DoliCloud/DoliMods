<?php
/* Copyright (C) 2009-2015 Regis Houssin  <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/multicompany/admin/multicompany.php
 *	\ingroup    multicompany
 *	\brief      Page d'administration/configuration du module Multi-societe
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once("../class/actions_multicompany.class.php");
require_once("../lib/multicompany.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");

$langs->load("admin");
$langs->load("languages");
$langs->load('multicompany@multicompany');

if (! $user->admin || $user->entity) accessforbidden();

$action=GETPOST('action','alpha');

$object = new ActionsMulticompany($db);

$form=new Form($db);
$formadmin=new FormAdmin($db);
$formcompany=new FormCompany($db);

/*
 * Actions
 */

$object->doActions($action);

//$test = new DaoMulticompany($db);
//$test->deleteEntityRecords(4);

/*
 * View
 */

$extrajs = array('/multicompany/inc/multiselect/js/ui.multiselect.js');
$extracss = array('/multicompany/inc/multiselect/css/ui.multiselect.css');

llxHeader('',$langs->trans("MultiCompanySetup"),'','','','',$extrajs,$extracss);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MultiCompanySetup"),$linkback,'multicompany@multicompany');

print '<br>';

$head = multicompany_prepare_head();
dol_fiche_head($head, 'entities', $object->getTitle(GETPOST("action")));

// Assign template values
$object->assign_values($action);

// Show the template
$object->display();

// Footer
llxFooter();
// Close database handler
$db->close();
?>
