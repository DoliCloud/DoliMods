<?php
/* Copyright (C) 2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/opensurvey/admin/index.php
 *	\ingroup    opensurvey
 *	\brief      Setup page of opensurvey
 */

/**
 * Show header for new member
 *
 * @param 	string		$title				Title
 * @param 	string		$head				Head array
 * @param 	int    		$disablejs			More content into html header
 * @param 	int    		$disablehead		More content into html header
 * @param 	array  		$arrayofjs			Array of complementary js files
 * @param 	array  		$arrayofcss			Array of complementary css files
 * @return	void
 */
function llxHeaderSurvey($title, $head="", $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='')
{
	global $user, $conf, $langs, $mysoc;

	top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss); // Show html headers
	print '<body id="mainbody" class="publicnewmemberform" style="margin-top: 10px;">';

	showlogo();

	print '<div style="margin-left: 50px; margin-right: 50px;">';
}

/**
 * Show footer for new member
 *
 * @return	void
 */
function llxFooterSurvey()
{
	print '</div>';

	printCommonFooter('public');

	dol_htmloutput_events();

	print "</body>\n";
	print "</html>\n";
}


// pour get_server_name()
include_once('fonctions.php');

/**
 * Show logo
 *
 * @return	void
 */
function showlogo()
{
	global $user, $conf, $langs, $mysoc;

	// Print logo
	$urllogo=DOL_URL_ROOT.'/theme/login_logo.png';

	if (! empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small))
	{
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_small);
	}
	elseif (! empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo))
	{
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode($mysoc->logo);
		$width=128;
	}
	elseif (is_readable(DOL_DOCUMENT_ROOT.'/theme/dolibarr_logo.png'))
	{
		$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
	}
	print '<center>';
	print '<img alt="Logo" id="logosubscribe" title="" src="'.$urllogo.'" style="max-width: 120px" /><br>';
	print '<strong>'.$langs->trans("OpenSurvey").'</strong>';
	print '</center><br>';
}

?>