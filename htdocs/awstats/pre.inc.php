<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *		\file 		htdocs/awstats/pre.inc.php
 *		\ingroup    awstats
 *		\brief      File to manage left menu for awstats module
 *		\version    $Id: pre.inc.php,v 1.9 2011/01/16 13:30:09 eldy Exp $
 */

if (! defined('NOCSRFCHECK')) define('NOCSRFCHECK',1);

$res=0;
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");

$user->getrights('awstats');


function llxHeader($head = "", $title="", $help_url='')
{
	global $conf,$langs;
	$langs->load("other");

	top_menu($head, $title);

	$menu = new Menu();

	$menu->add(DOL_URL_ROOT."/awstats/index.php?mainmenu=awstats&idmenu=".$_SESSION["idmenu"], $langs->trans("AWStats"));

	left_menu($menu->liste, $help_url);
}
?>
