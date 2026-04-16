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
 *	\file			htdocs/sendgrid/lib/sendgrid.lib.php
 *  \brief			Library of admin functions for OVH module
 *  \version		$Id: sendgrid.lib.php,v 1.1 2011/03/05 17:35:16 eldy Exp $
 */



/**
 *  Define head array for tabs of sendgrid tools setup pages
 *
 *  @return			Array of head
 */
function sendgridadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/sendgrid/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Authentication");
	$head[$h][2] = 'common';
	$h++;

	$head[$h][0] = dol_buildpath("/sendgrid/admin/importinvoice.php", 1);
	$head[$h][1] = $langs->trans("GetInvoices");
	$head[$h][2] = 'getinvoices';
	$h++;

	$head[$h][0] = 'about.php';
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'tababout';
	$h++;

	return $head;
}


/**
 *  Define head array for tabs of sendgrid tools setup pages
 *
 *  @return			Array of head
 */
function sendgridsysadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/sendgrid/sendgrid_listinfoserver.php", 1).'?mode=publiccloud';
	$head[$h][1] = $langs->trans("OvhPublicCloud");
	$head[$h][2] = 'publiccloud';
	$h++;

	$head[$h][0] = dol_buildpath("/sendgrid/sendgrid_listinfoserver.php", 1).'?mode=dedicated';
	$head[$h][1] = $langs->trans("OvhDedicated");
	$head[$h][2] = 'dedicated';
	$h++;

	return $head;
}
