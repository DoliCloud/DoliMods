<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *		\file 		htdocs/sellyoursaas/pre.inc.php
 *		\ingroup    nltechno
 *		\brief      File to manage left menu for NLTechno module
 */


$user->getrights('nltechno');


function llxHeader($head = "", $title="", $help_url='')
{
	global $conf,$langs;
	$langs->load("agenda");

    print '<body id="mainbody">' . "\n";

	top_menu($head, $title);

	require_once DOL_DOCUMENT_ROOT.'/core/class/menu.class.php';
	$menu = new Menu();

	left_menu($menu->liste, $help_url);
}
