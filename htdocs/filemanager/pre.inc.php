<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copytight (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *		\file   	htdocs/filemanager/pre.inc.php
 *		\ingroup    filemanager
 *		\brief  	File for menu of module filemanager
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");


/**
 * llxHeader
 *
 * @param 	string		$head			Head
 * @param 	string		$title			Title
 * @param 	string		$help_url		Help url
 * @param 	string		$target			Target
 * @param 	int			$disablejs		Disablejs
 * @param 	int			$disablehead	Disablehead
 * @param 	array		$arrayofjs		Array of js
 * @param 	array		$arrayofcss		Array of css
 * @param	string		$morequerystring	Query string to add to the link "print" to get same parameters (use only if autodetect fails)
 * @return	void
 */
function llxHeader($head = '', $title='', $help_url='', $target='', $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='', $morequerystring='')
{
	global $db, $user, $conf, $langs;

	top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss);	// Show html headers

	// top menu and left menu area
	if (empty($conf->global->MAIN_HIDE_TOP_MENU))
	{
		top_menu($head, $title, $target, $disablejs, $disablehead, $arrayofjs, $arrayofcss, $morequerystring);
	}


	require_once DOL_DOCUMENT_ROOT.'/core/class/menu.class.php';
    $menu=new Menu();

	$numr=0;

	// Entry for each bank config
	$sql = "SELECT rowid, rootlabel, rootpath";
	$sql.= " FROM ".MAIN_DB_PREFIX."filemanager_roots";
	$sql.= " WHERE entity = ".$conf->entity;
	$sql.= " ORDER BY position";

	$resql = $db->query($sql);
	if ($resql)
	{
		$numr = $db->num_rows($resql);
		$i = 0;

		if ($numr == 0)
		{
			$langs->load("errors");
			$menu->add('#',$langs->trans('ErrorModuleSetupNotComplete'),0,0);
		}

		while ($i < $numr)
		{
			$objp = $db->fetch_object($resql);
			$menu->add('/filemanager/index.php?leftmenu=filemanager&id='.$objp->rowid,$objp->rootlabel,0,1);
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}
	$db->free($resql);


	if (empty($conf->global->MAIN_HIDE_LEFT_MENU))
	{
		left_menu('', $help_url, '', $menu->liste, 1, $title);
	}

    main_area($title);
}
