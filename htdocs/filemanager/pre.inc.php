<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copytight (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
		\file   	htdocs/filemanager/pre.inc.php
		\ingroup    compta
		\brief  	Fichier gestionnaire du menu filemanager
		\version	$Id: pre.inc.php,v 1.2 2010/08/20 16:42:31 eldy Exp $
*/

$res=@include("../main.inc.php");
if (! $res) @include("../../../dolibarr/htdocs/main.inc.php");  // Used on dev env only


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

		if ($numr == 0)	$menu->add_submenu('#','NoModuleSetup',1,0);

		while ($i < $numr)
		{
			$objp = $db->fetch_object($resql);
			$menu->add($_SERVER["PHP_SELF"].'?leftmenu=filemanager&id='.$objp->rowid,$objp->rootlabel,0,1);
/*
			$menu->add_submenu(DOL_URL_ROOT."/compta/bank/annuel.php?account=".$objp->rowid ,$langs->trans("IOMonthlyReporting"));
			$menu->add_submenu(DOL_URL_ROOT."/compta/bank/graph.php?account=".$objp->rowid ,$langs->trans("Graph"));
			if ($objp->courant != 2) $menu->add_submenu(DOL_URL_ROOT."/compta/bank/releve.php?account=".$objp->rowid ,$langs->trans("AccountStatements"));
*/
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}
	$db->free($resql);


	if (empty($noleftmenu)) left_menu('', $help_url, '', $menu->liste);
}
?>
