<?php
/* Copyright (C) 2011 Regis Houssin  <regis.houssin@capnetworks.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *	\file       multicompany/lib/multicompany.lib.php
 *	\brief      Ensemble de fonctions de base pour le module Multi-Company
 * 	\ingroup	multicompany
 */


function multicompany_prepare_head()
{
	global $langs, $conf;
	$langs->load('multicompany@multicompany');

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/multicompany/admin/multicompany.php",1);
	$head[$h][1] = $langs->trans("Entities");
	$head[$h][2] = 'entities';
	$h++;

	$head[$h][0] = dol_buildpath("/multicompany/admin/options.php",1);
	$head[$h][1] = $langs->trans("Options");
	$head[$h][2] = 'options';
	$h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'multicompany');

	return $head;
}

?>