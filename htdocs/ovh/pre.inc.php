<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-Francois FERRY  <jfefe@aternatik.fr>
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
 *  \file       htdocs/ovhsms/pre.inc.php
 *  \brief      File to manage left menu by ovhsms
 *  \version    $Id: pre.inc.php,v 1.1 2010/12/04 01:32:57 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=@include("../main.inc.php");
if (! $res) @include("../../../dolibarr/htdocs/main.inc.php");  // Used on dev env only

$user->getrights('ovh');


/**
 *	\brief		Function called by page to show menus (top and left)
 *  \param		head				Text to show as head line
 * 	\param		title				Not used
 * 	\param      helppagename    	Name of a help page ('' by default).
 * 				Syntax is: 			For a wiki page: EN:EnglishPage|FR:FrenchPage|ES:SpanishPage
 * 									For other external page: http://server/url
 */
function llxHeader($head = '', $title='', $help_url='')
{
	global $user, $conf, $langs;

	top_menu($head, $title);

	$menu = new Menu();

	// Create default menu.

	// No code here is required if you already added menu entries in
	// the module descriptor (recommanded).
	// If not you must manually add menu entries here (not recommanded).
	/*
	$langs->load("mylangfile");
	$menu->add(DOL_URL_ROOT."/mylink.php", $langs->trans("MyMenuLabel"));
	}
	*/

	left_menu($menu->liste, $help_url);
}
?>
