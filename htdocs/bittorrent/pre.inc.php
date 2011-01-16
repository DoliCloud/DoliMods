<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *		\file 		htdocs/bittorrent/pre.inc.php
 *		\ingroup    bittorrent
 *		\brief      File to manage left menu for bittorrent module
 *		\version    $Id: pre.inc.php,v 1.4 2011/01/16 14:38:46 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");

global $website_url;
require_once ("./config.php");


$user->getrights('bittorrent');


function llxHeader($head = "", $title="", $help_url='')
{
	global $conf,$langs;
	$langs->load("other");

	top_menu($head, $title);

	$menu = new Menu();

	left_menu($menu->liste, $help_url);
}
?>
