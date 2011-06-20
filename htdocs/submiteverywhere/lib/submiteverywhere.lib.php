<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file			htdocs/submiteverywhere/lib/submiteverywhere.lib.php
 *	\brief			A set of functions for submiteverywhere module
 *	\version		$Id: submiteverywhere.lib.php,v 1.1 2011/06/20 19:34:16 eldy Exp $
 */

/**
 * 	Return img flag of a target type
 * 	@param		targetcode  Type of targe ('email', 'twitter', 'facebook', 'dig', ...)
 * 	@return		string		HTML img string with flag.
 */
function picto_from_targetcode($targetcode)
{
    $ret='';
    if (! empty($targetcode))
    {
        if ($targetcode == 'email') $ret.=img_picto($targetcode,DOL_URL_ROOT.'/theme/common/flags/'.strtolower($tmpcode).'.png','',1);
        else $ret.=img_picto($targetcode,'generic');
    }
    return $ret;
}

?>