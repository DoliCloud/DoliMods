<?php
/* Copyright (C) 2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file			htdocs/lib/google.lib.php
 *  \brief			Library of admin functions for google module
 *  \version		$Id: google.lib.php,v 1.4 2011/01/16 14:26:45 eldy Exp $
 */


/**
 *  \brief      	Define head array for tabs of google tools setup pages
 *  \return			Array of head
 *  \version    	$Id: google.lib.php,v 1.4 2011/01/16 14:26:45 eldy Exp $
 */
function googleadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/google/admin/google.php",1);
	$head[$h][1] = $langs->trans("Agenda");
	$head[$h][2] = 'agenda';
	$h++;

	$head[$h][0] = dol_buildpath("/google/admin/google2.php",1);
	$head[$h][1] = $langs->trans("Adsense");
	$head[$h][2] = 'adsense';
	$h++;

	return $head;
}

?>