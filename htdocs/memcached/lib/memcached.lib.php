<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file			htdocs/lib/admin.lib.php
 *  \brief			Library of admin functions
 *  \version		$Id: memcached.lib.php,v 1.1 2011/01/16 13:30:09 eldy Exp $
 */

/**
 *  \brief      	Define head array for tabs of security setup pages
 *  \return			Array of head
 *  \version    	$Id: memcached.lib.php,v 1.1 2011/01/16 13:30:09 eldy Exp $
 */
function memcached_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/memcached/admin/memcached.php",1);
	$head[$h][1] = $langs->trans("ServerSetup");
	$head[$h][2] = 'serversetup';
	$h++;

	if (class_exists("Memcache") || class_exists("Memcached"))
	{
		if (empty($dolibarr_memcached_view_disable))	// Hidden variable to add to conf file to disable browsing
		{
			$head[$h][0] = dol_buildpath("/memcached/admin/memcached_stats.php?op=1",1);
			$head[$h][1] = $langs->trans("ServerStatistics");
			$head[$h][2] = 'serverstats';
			$h++;

			$head[$h][0] = dol_buildpath("/memcached/admin/memcached_stats.php?op=2",1);
			$head[$h][1] = $langs->trans("CacheBrowser");
			$head[$h][2] = 'cachebrowser';
			$h++;
		}
	}

	return $head;
}

?>