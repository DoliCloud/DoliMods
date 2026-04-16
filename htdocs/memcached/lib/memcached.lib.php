<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file			htdocs/memcached/lib/memcached.lib.php
 *  \brief			Library of memcached functions
 */

/**
 *	Define head array for tabs of security setup pages
 *
 *	@return			Array of head
 */
function memcached_prepare_head()
{
	global $langs, $conf, $user;
	global $dolibarr_memcached_view_setup,$dolibarr_memcached_view_disable;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/memcached/admin/memcached.php", 1);
	$head[$h][1] = $langs->trans("ServerSetup");
	$head[$h][2] = 'serversetup';
	$h++;

	if (class_exists("Memcache") || class_exists("Memcached")) {
		if (empty($dolibarr_memcached_view_setup)) {	// Hidden variable to add to conf file to disable setup
			$head[$h][0] = dol_buildpath("/memcached/admin/memcached_stats.php?op=1", 1);
			$head[$h][1] = $langs->trans("ServerStatistics");
			$head[$h][2] = 'serverstats';
			$h++;
		}

		if (empty($dolibarr_memcached_view_setup) && empty($dolibarr_memcached_view_disable)) {	// Hidden variable to add to conf file to disable setup or disable cache browsing
			$head[$h][0] = dol_buildpath("/memcached/admin/memcached_stats.php?op=2", 1);
			$head[$h][1] = $langs->trans("CacheBrowser");
			$head[$h][2] = 'cachebrowser';
			$h++;
		}
	}

	$head[$h][0] = 'about.php';
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'tababout';
	$h++;

	return $head;
}
