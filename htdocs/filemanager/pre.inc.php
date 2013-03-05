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
		\file   	htdocs/filemanager/pre.inc.php
		\ingroup    compta
		\brief  	Fichier gestionnaire du menu filemanager
		\version	$Id: pre.inc.php,v 1.7 2011/07/04 11:33:10 eldy Exp $
*/

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");


/**
 *
 * @param $head
 * @param $title
 * @param $help_url
 * @param $target
 * @param $disablejs
 * @param $disablehead
 * @param $arrayofjs
 * @param $arrayofcss
 */
function llxHeader($head = '', $title='', $help_url='', $target='', $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='', $notopmenu=0, $noleftmenu=0)
{
	global $db, $user, $conf, $langs;

	top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss);	// Show html headers
	if (empty($notopmenu)) top_menu($head, $title, $target, $disablejs, $disablehead, $arrayofjs, $arrayofcss);	// Show html headers

	$menu = new Menu();

	$numr=0;

	// Entry for each bank account
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
			$menu->add('#',$langs->trans('ErrorModuleSetupNotComplete'),1,0);
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


	if (empty($noleftmenu))
	{
	    left_menu('', $help_url, '', $menu->liste, 1);
	    main_area();
	}
}
?>
