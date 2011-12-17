<?php
/* Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file			htdocs/ovh/lib/ovh.lib.php
 *  \brief			Library of admin functions for OVH module
 *  \version		$Id: ovh.lib.php,v 1.1 2011/03/05 17:35:16 eldy Exp $
 */


/**
 *  \brief      	Define head array for tabs of ovh tools setup pages
 *  \return			Array of head
 *  \version    	$Id: ovh.lib.php,v 1.1 2011/03/05 17:35:16 eldy Exp $
 */
function ovhadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/ovh/admin/ovh_setup.php",1);
	$head[$h][1] = $langs->trans("Sms");
	$head[$h][2] = 'sms';
	$h++;

	$head[$h][0] = dol_buildpath("/ovh/admin/ovh_click2dial.php",1);
	$head[$h][1] = $langs->trans("Click2Dial");
	$head[$h][2] = 'click2dial';
	$h++;


    return $head;
}

?>