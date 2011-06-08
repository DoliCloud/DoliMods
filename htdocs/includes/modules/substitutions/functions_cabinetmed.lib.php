<?php
/* Copyright (C) 2009 Laurent Destailleur         <eldy@users.sourceforge.net>
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
 *	\file			htdocs/includes/modules/substitutions/functions_cabinetmed.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains functions for plugin cabinetmed.
 *	\version		$Id: functions_cabinetmed.lib.php,v 1.2 2011/06/08 21:13:49 eldy Exp $
 */


/**
 * 		Function called to complete substitution array
 * 		functions xxx_completesubstitutionarray are called by make_substitutions()
 *		@param		substitutionarray	Array with substitution key=>val
 *		@param		langs				Output langs
 *		@param		object				Object to use to get values
 * 		@return		None. The entry parameter $substitutionarray is modified
 */
function cabinetmed_completesubstitutionarray(&$substitutionarray,$langs,$object)
{
	global $conf;
	if (is_object($object))
	{
	    $substitutionarray['aaa']='bbb';
        $substitutionarray['diagnostic_principal']=$object->cons_princ;
        $substitutionarray['examother_conclusion']=$object->cons_princ;
        $substitutionarray['exambio_conclusion']=$object->cons_princ;
        $substitutionarray['treatment']=$object->cons_princ;
	}
}

