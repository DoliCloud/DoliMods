<?php
/* Copyright (C) 2003 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *		\file 		htdocs/postnuke/pre.inc.php
 *		\ingroup    postnuke
 *		\brief      File to manage left menu for postnuke module
 *		\version    $Id: pre.inc.php,v 1.5 2011/03/29 23:17:21 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");


function llxHeader($head = "", $title="", $help_url = "")
{
	global $user, $conf, $langs;

	/*
	 *
	 *
	 */
	top_menu($head, $title);

	$menu = new Menu();

	$menu->add("/boutique/livre/", $langs->trans("Livres"));

	$menu->add("/boutique/auteur/", $langs->trans("Auteurs"));

	$menu->add("/boutique/editeur/", $langs->trans("Editeurs"));

	$menu->add("/product/categorie/", $langs->trans("Categories"));

	$menu->add("/product/promotion/", $langs->trans("Promotions"));

	$menu->add("/postnuke/index.php", $langs->trans("Editorial"));

	left_menu($menu->liste);
}
?>
