<?php
/* Copyright (C) 2011 Laurent Destailleur         <eldy@users.sourceforge.net>
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
 *	\file			htdocs/sellyoursaas/core/substitutions/functions_sellyoursaas.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains functions for plugin sellyoursaas.
 */


/**
 * 		Function called to complete substitution array (before generating on ODT, or a personalized email)
 * 		functions xxx_completesubstitutionarray are called by make_substitutions() if file
 * 		is inside directory htdocs/core/substitutions
 *
 *		@param	array		$substitutionarray	Array with substitution key=>val
 *		@param	Translate	$langs				Output langs
 *		@param	Object		$object				Object to use to get values
 *      @param  Mixed		$parameters       	Add more parameters (useful to pass product lines)
 * 		@return	void							The entry parameter $substitutionarray is modified
 */
function sellyoursaas_completesubstitutionarray(&$substitutionarray,$langs,$object,$parameters=null)
{
	global $conf,$db;

	include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
	include_once DOL_DOCUMENT_ROOT.'/societe/class/companypaymentmode.class.php';
	dol_include_once('/sellyoursaas/class/customeraccount.class.php');

    $langs->load("sellyoursaas@sellyoursaas");

    if ((! empty($parameters['mode'])) && $parameters['mode'] == 'formemail')	// For exemple when called by FormMail::getAvailableSubstitKey()
    {
        if (is_object($object) && get_class($object) == 'Societe')
        {
        	$companypaymentmode = new CompanyPaymentMode($db);
        	$result = $companypaymentmode->fetch(0, null, $object->id, 'card');
        	if ($result >= 0)
        	{
        		$substitutionarray['__CARD_LAST4__']=($companypaymentmode->last_four ? $companypaymentmode->last_four : 'Not Defined');
        	}
        	else dol_print_error($db);
        	$result = $companypaymentmode->fetch(0, null, $object->id, 'paypal');
        	if ($result >= 0)
        	{
        		$substitutionarray['__PAYPAL_START_DATE__']=($companypaymentmode->starting_date ? dol_print_date($companypaymentmode->starting_date, 'dayrfc', 'gmt', $langs) : 'Not Defined');
        		$substitutionarray['__PAYPAL_EXP_DATE__']=($companypaymentmode->ending_date ? dol_print_date($companypaymentmode->ending_date, 'dayrfc', 'gmt', $langs) : 'Not Defined');
        	}
        	else dol_print_error($db);
        }
    }

}

