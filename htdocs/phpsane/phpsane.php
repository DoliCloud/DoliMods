<?php
/* Copyright (C) 2004-2007 Laurent Destailleur <eldy@users.sourceforge.net>
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

/**	    \file       htdocs/phpsane/phpsane.php
        \ingroup    phpsane
		\brief      Page generant 2 frames, une pour le menu Dolibarr, l'autre pour l'affichage de PHPSane
		\author	    Laurent Destailleur
		\version    $Id: phpsane.php,v 1.3 2010/07/19 18:20:21 eldy Exp $
*/

$res=@include("../main.inc.php");
if (! $res) $res=@include("../../main.inc.php");	// If pre.inc.php is called by jawstats
if (! $res) $res=@include("../../../dolibarr/htdocs/main.inc.php");		// Used on dev env only
if (! $res) $res=@include("../../../../dolibarr/htdocs/main.inc.php");	// Used on dev env only

/*if (empty($conf->global->PHPWEBCALENDAR_URL))
{
	llxHeader();
	print '<div class="error">Module Webcalendar was not configured properly.</div>';
	llxFooter('$Date: 2010/07/19 18:20:21 $ - $Revision: 1.3 $');
}
*/

$mainmenu=isset($_GET["mainmenu"])?$_GET["mainmenu"]:"";
$leftmenu=isset($_GET["leftmenu"])?$_GET["leftmenu"]:"";

print "
<html>
<head>
<title>Dolibarr frame for PHPSane</title>
</head>

<frameset rows=\"".$heightforframes.",*\" border=0 framespacing=0 frameborder=0>
    <frame name=\"barre\" src=\"".DOL_URL_ROOT."/phpsane/phpsanetop.php?mainmenu=".$mainmenu."&leftmenu=".$leftmenu."\" noresize scrolling=\"NO\" noborder>
    <frame name=\"main\" src=\"".DOL_URL_ROOT."/includes/phpsane/phpSane/phpsane.php\">
    <noframes>
    <body>

    </body>
    </noframes>
</frameset>

<noframes>
<body>
	<br><center>
	Malheureusement, votre navigateur est trop vieux pour visualiser cette zone.<br>
	Il vous faut un navigateur g�rant les frames.<br>
	</center>
</body>
</noframes>

</html>
";


?>
