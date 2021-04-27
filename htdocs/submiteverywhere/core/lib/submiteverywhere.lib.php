<?php
/* Copyright (C) 2011-201 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file			htdocs/submiteverywhere/lib/submiteverywhere.lib.php
 *	\brief			A set of functions for submiteverywhere module
 *	\version		$Id: submiteverywhere.lib.php,v 1.1 2011/06/20 19:34:16 eldy Exp $
 */

/**
 * 	Return img flag of a target type
 *
 * 	@param	string		$targetcode  Type of targe ('email', 'twitter', 'facebook', 'dig', ...)
 * 	@return	string		HTML img string with flag.
 */
function picto_from_targetcode($targetcode)
{
	$ret='';
	if (! empty($targetcode)) {
		if (in_array($targetcode, array('email','digg','facebook','googleplus','linkedin','twitter','viadeo','web'))) $ret.=img_picto($targetcode, strtolower($targetcode).'@submiteverywhere');
		else $ret.=img_picto($targetcode, 'object_generic');
	}
	return $ret;
}

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to shoc
 */
function submitew_prepare_head($object)
{
	global $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/submitew/card.php")."?id=".$object->id;
	$head[$h][1] = $langs->trans("MailCard");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/submitew/target.php")."?id=".$object->id;
	$head[$h][1] = $langs->trans("MailRecipients");
	$head[$h][2] = 'targets';
	$h++;

	$head[$h][0] = dol_buildpath("/submitew/info.php")."?id=".$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	return $head;
}
