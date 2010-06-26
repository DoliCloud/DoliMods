<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *		\file 		htdocs/google/pre.inc.php
 *		\ingroup    google
 *		\brief      File to manage left menu for google module
 *		\version    $Id: pre.inc.php,v 1.6 2010/06/26 00:58:52 eldy Exp $
 */

define('NOCSRFCHECK',1);

$res=@include("../main.inc.php");
if (! $res) @include("../../../dolibarr/htdocs/main.inc.php");	// Used on dev env only

$user->getrights('google');

/**
 * Enter description here...
 *
 * @param unknown_type $head
 * @param unknown_type $title
 * @param unknown_type $help_url
 */
function llxHeader($head = "", $title="", $help_url='')
{
	global $conf,$langs;
	$langs->load("agenda");

	top_menu($head, $title);

	$menu = new Menu();

	$menu->add(DOL_URL_ROOT."/google/index.php?mainmenu=google&idmenu=".$_SESSION["idmenu"], $langs->trans("Agendas"));

	$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;
	$i=1;
	while ($i <= $MAXAGENDA)
	{
		$paramkey='GOOGLE_AGENDA_NAME'.$i;
		$paramcolor='GOOGLE_AGENDA_COLOR'.$i;
		//print $paramkey;
		if (! empty($conf->global->$paramkey))
		{
			$addcolor=false;
			if (isset($_GET["nocal"]))
			{
				if ($_GET["nocal"] == $i) $addcolor=true;
			}
			else $addcolor=true;

			$link=DOL_URL_ROOT."/google/index.php?mainmenu=google&idmenu=".$_SESSION["idmenu"]."&nocal=".$i;

			$text='';
			$text.='<table class="nobordernopadding">';

			$text.='<tr valign="middle" class="nobordernopadding">';

			// Color of agenda
			$text.='<td style="padding-left: 4px; padding-right: 4px" nowrap="nowrap">';
			$box ='<!-- Box color '.$selected.' -->';
			$box.='<table style="border-collapse: collapse; margin:0px; padding: 0px; border: 1px solid #888888;';
			if ($addcolor) $box.=' background: #'.(preg_replace('/#/','',$conf->global->$paramcolor)).';';
			$box.='" width="12" height="10">';
			$box.='<tr class="nocellnopadd"><td></td></tr>';	// To show box
			$box.='</table>';
			$text.=$box;
			$text.='</td>';

			// Name of agenda
			$text.='<td>';
			$text.='<a class="vsmenu" href="'.$link.'">'.$conf->global->$paramkey.'</a>';
			$text.='</td></tr>';
			$text.='</table>';

			$menu->add_submenu('', $text);
		}
		$i++;
	}

	left_menu($menu->liste, $help_url);
}
?>
