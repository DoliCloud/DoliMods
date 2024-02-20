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
 * or see http://www.gnu.org/
 */

/**
 *	\file			htdocs/ecotaxdeee/lib/ecotaxdeee.lib.php
 *  \brief			Library of admin functions for ecotaxdeee module
 */


/**
 *  Return array of menu entries
 *
 *  @return			Array of head
 */
function ecotaxdeee_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = 'index.php';
	$head[$h][1] = $langs->trans("Setup");
	$head[$h][2] = 'tabsetup';
	$h++;

	$active_code = (!getDolGlobalString('SET_CODE_FOR_ECOTAXDEEE') ? false : true);
	if ($active_code) {
		$head[$h][0] = 'setup.php';
		$head[$h][1] = $langs->trans("CodeAndAmountTable");
		$head[$h][2] = 'tabmoresetup';
		$h++;
	}

        $head[$h][0] = 'about.php';
        $head[$h][1] = $langs->trans("About");
        $head[$h][2] = 'tababout';
        $h++;

	return $head;
}
