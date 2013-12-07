<?php
/* Copyright (C) 2009-2012 Regis Houssin  <regis@dolibarr.fr>
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
 *	\file       htdocs/multiinstance/admin/multiinstance.php
 *	\ingroup    multiinstance
 *	\brief      Page d'administration/configuration du module Multi-societe
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

require_once("../class/actions_multiinstance.class.php");
require_once("../lib/multiinstance.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");

$langs->load("admin");
$langs->load("languages");
$langs->load('multiinstance@multiinstance');

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


/*
 * View
 */

$extrajs = array('/multiinstance/inc/multiselect/js/ui.multiselect.js');
$extracss = array('/multiinstance/inc/multiselect/css/ui.multiselect.css');

llxHeader('',$langs->trans("MultiCompanySetup"),'','','','',$extrajs,$extracss);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MultiCompanySetup"),$linkback,'multiinstance@multiinstance');

print '<br>';

$head = multiinstance_prepare_head();
dol_fiche_head($head, 'entities', $object->getTitle(GETPOST("action")));

// Assign template values
$object->assign_values($action);

// Show errors
dol_htmloutput_errors($object->error,$object->errors);

// Show messages
dol_htmloutput_mesg($object->mesg,'','ok');

// Show the template
$object->display();

// Footer
llxFooter();
// Close database handler
$db->close();
?>
