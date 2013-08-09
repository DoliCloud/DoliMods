<?php
/* Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file			htdocs/google/lib/google.lib.php
 *  \brief			Library of admin functions for google module
 */


/**
 *  Return array of menu entries
 *
 *  @return			Array of head
 */
function googleadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/google/admin/google.php",1);
	$head[$h][1] = $langs->trans("AgendaView");
	$head[$h][2] = 'tabagenda';
	$h++;

    $head[$h][0] = dol_buildpath("/google/admin/google_calsync.php",1);
    $head[$h][1] = $langs->trans("AgendaSync");
    $head[$h][2] = 'tabagendasync';
    $h++;

    //if (! empty($conf->global->MAIN_FEATURES_LEVEL))
    //{
	    $head[$h][0] = dol_buildpath("/google/admin/google_contactsync.php",1);
	    $head[$h][1] = $langs->trans("ContactSync");
	    $head[$h][2] = 'tabcontactsync';
	    $h++;
    //}

    $head[$h][0] = dol_buildpath("/google/admin/google_gmaps.php",1);
    $head[$h][1] = $langs->trans("GMaps");
    $head[$h][2] = 'tabgmaps';
    $h++;

    $head[$h][0] = dol_buildpath("/google/admin/google_ad.php",1);
	$head[$h][1] = $langs->trans("Adsense");
	$head[$h][2] = 'tabadsense';
	$h++;

	include_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
	$dolibarrversionarray=preg_split('/[\.-]/',version_dolibarr());
    $dolibarrversionok=array(3,1,-2);
	if (versioncompare($dolibarrversionarray,$dolibarrversionok) >= 0)
	{
        $head[$h][0] = dol_buildpath("/google/admin/google_an.php",1);
        $head[$h][1] = $langs->trans("Analitycs");
        $head[$h][2] = 'tabanalytics';
        $h++;
	}

	$head[$h][0] = 'about.php';
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'tababout';
	$h++;


    return $head;
}

?>