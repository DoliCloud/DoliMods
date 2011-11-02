<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2011      Dimitri Mouillard <dmouillard@teclib.com>
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
 *   	\file       pre.inc.php
 *		\ingroup    congespayes
 *		\brief      Load files and menus.
 *		\version    $Id: pre.inc.php,v 1.00 2011/09/15 11:00:00 dmouillard Exp $
 *		\author		dmouillard@teclib.com <Dimitri Mouillard>
 *		\remarks	   Load files and menus.
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
dol_include_once("/congespayes/class/congespayes.class.php");

if(!in_array('congespayes', $conf->modules)) {
   $langs->load("congespayes@congespayes");
   llxHeader('','Congés Payés');
   print '<div class="tabBar">';
      print '<span style="color: #FF0000;">'.$langs->trans('NotActiveModCP').'</span>';
   print '</div>';
   llxFooter();
   exit();
}

$verifConf.= "SELECT value";
$verifConf.= " FROM ".MAIN_DB_PREFIX."congespayes_config";
$verifConf.= " WHERE name = 'userGroup'";

$result = $db->query($verifConf);
$obj = $db->fetch_object($result);

if($obj->value == NULL) {
   $langs->load("congespayes@congespayes");
   llxHeader('',$langs->trans('CPTitreMenu'));
   print '<div class="tabBar">';
      print '<span style="color: #FF0000;">'.$langs->trans('NotConfigModCP').'</span>';
   print '</div>';
   llxFooter();
   exit();
}

$langs->load("user");
$langs->load("other");
$langs->load("congespayes@congespayes");

function llxHeader($title)
{
  global $user, $conf, $langs;

  top_htmlhead('',$title);
  top_menu($head);

  $menu = new Menu();

	$menu->add("/congespayes/index.php?mainmenu=",$langs->trans("CPTitreMenu"));
	if($user->rights->congespayes->create_edit_read) {
	   $menu->add("/congespayes/fiche.php?mainmenu=&action=request",$langs->trans("MenuAddCP"),2);
	}
   if($user->rights->congespayes->define_conges) {
      $menu->add("/congespayes/define_congespayes.php?mainmenu=",$langs->trans("MenuConfCP"),2);
   }
   if($user->rights->congespayes->view_log) {
      $menu->add("/congespayes/view_log.php?mainmenu=",$langs->trans("MenuLogCP"),2);
   }
   if($user->rights->congespayes->view_log) {
      $menu->add("/congespayes/month_report.php?mainmenu=",$langs->trans("MenuReportMonth"),2);
   }

   if(in_array('employees', $conf->modules) && $user->rights->employees->module_access) {
      $menu->add("/employees/index.php",$langs->trans("Menu_Title_EMPLOYEE"));
      $menu->add("/employees/index.php",$langs->trans("Menu_List_EMPLOYEE"),2);
      $menu->add("/employees/fiche.php?action=create",$langs->trans("Menu_Add_EMPLOYEE"),2);
      $menu->add("/employees/hire.php?action=create",$langs->trans("Menu_Add_HIRE"),2);
      $menu->add("/employees/salary.php?action=create",$langs->trans("Menu_Add_SALARY"),2);
      $menu->add("/employees/job.php?action=create",$langs->trans("Menu_Add_JOB"),2);
      $menu->add("/employees/disease.php?action=create",$langs->trans("Menu_Add_DISEASE"),2);
      $menu->add("/employees/month_report_disease.php",$langs->trans("Menu_Report_Disease"),2);
      $menu->add("/employees/set_hire_type.php",$langs->trans("Menu_Set_Hire_type"),2);

      if(!isset($_SESSION['employees_passphrase'])){
         $menu->add("/employees/store_secure.php",$langs->trans("Menu_Store_Secure"),2);
      }
   }

  left_menu($menu->liste);
}

?>
