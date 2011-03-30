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
 *	\file			htdocs/lib/functionsnumberswords.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains functions for plugin numberwords.
 *	\version		$Id: functions_numberwords.lib.php,v 1.2 2011/03/30 08:58:03 eldy Exp $
 */


/**
 * 		Function called to complete substitution array
 * 		functions xxx_completesubstitutionarray are called by make_substitutions()
 *		@param		substitutionarray	Array with substitution key=>val
 *		@param		langs				Output langs
 *		@param		object				Object to use to get values
 * 		@return		None. The entry parameter $substitutionarray is modified
 */
function numberwords_completesubstitutionarray(&$substitutionarray,$langs,$object)
{
	global $conf;
	if (is_object($object))
	{
		$numbertext=$langs->getLabelFromNumber($object->total_ttc,1);
		$substitutionarray['__TOTAL_TTC_WORDS__']=$numbertext;
		$numbertext=$langs->getLabelFromNumber($object->total_ht,1);
		$substitutionarray['__TOTAL_HT_WORDS__']=$numbertext;
		$numbertext=$langs->getLabelFromNumber(($object->total_vat?$object->total_vat:$object->total_tva),1);
		$substitutionarray['__TOTAL_VAT_WORDS__']=$numbertext;
		$numbertext=$langs->getLabelFromNumber($object->number,0);
		$substitutionarray['__NUMBER_WORDS__']=$numbertext;
	}
}

/**
 *      \brief      Return full text translated to language label for a key. Store key-label in a cache.
 *		\param		langs		Language for output
 * 		\param		number		Number to encode in full text
 * 		\param		isamount	1=It's an amount, 0=it's just a number
 *      \return     string		Label translated in UTF8 (but without entities)
 * 								10 if setDefaultLang was en_US => ten
 * 								123 if setDefaultLang was fr_FR => cent vingt trois
 */
function numberwords_getLabelFromNumber($langs,$number,$isamount=0)
{
	global $conf;

	dol_syslog("numberwords_getLabelFromNumber langs->defaultlang=".$langs->defaultlang." number=".$number." isamount=".$isamount);
	$langs->load("dict");

	$outlang=$langs->defaultlang;	// Output language we want
	$outlangarray=explode('_',$outlang,2);
	// If lang is xx_XX, then we use xx
	if (strtolower($outlangarray[0]) == strtolower($outlangarray[1])) $outlang=$outlangarray[0];

	$numberwords=$number;

	require_once(dirname(__FILE__).'/../../Numbers/Words.php');
	$handle = new Numbers_Words();
	$handle->dir=dirname(__FILE__).'/../../';

	// $outlang = fr_FR, fr_CH, pt_PT ...
	if (! file_exists($handle->dir.'Numbers/Words/lang.'.$outlang.'.php'))
	{
		// We try with short code
		$tmparray=explode('_',$outlang);
		$outlang=$tmparray[0];
	}

	if (! file_exists($handle->dir.'Numbers/Words/lang.'.$outlang.'.php'))
	{
		return "(Error: No rule file into Numbers/Words to convert number to text for language ".$langs->defaultlang.")";
	}

	// Define label on currency and cent in the property of object handle
	$handle->labelcurrency=$conf->monnaie;	// By default (EUR, USD)
	$handle->labelcents='cent';				// By default

	// Overwrite label of currency to ours
	$labelcurrencysing=$langs->transnoentitiesnoconv("CurrencySing".$conf->monnaie);
	//print "CurrencySing".$conf->monnaie."=>".$labelcurrencysing;
	if ($labelcurrencysing && $labelcurrencysing != -1 && $labelcurrencysing!='CurrencySing'.$conf->monnaie) $handle->labelcurrency=$labelcurrencysing;
	else
	{
		$labelcurrency=$langs->transnoentitiesnoconv("Currency".$conf->monnaie);
		if ($labelcurrency && $labelcurrency !='Currency'.$conf->monnaie) $handle->labelcurrency=$labelcurrency;
	}
	// Overwrite label of cent to ours
	$labelcurrencycentsing=$langs->transnoentitiesnoconv("CurrencyCentSing".$conf->monnaie);
	if ($labelcurrencycentsing && $labelcurrencycentsing != -1 &&$labelcurrencycentsing!='CurrencyCentSing'.$conf->monnaie) $handle->labelcents=$labelcurrencycentsing;
	else
	{
		$labelcurrencycent=$langs->transnoentitiesnoconv("CurrencyCent".$conf->monnaie);
		if ($labelcurrencycent && $labelcurrencycent !='CurrencyCent'.$conf->monnaie) $handle->labelcents=$labelcurrencycent;
	}

	// Call method of object handle to make convertion
	if ($isamount)
	{
		//print "currency: ".$conf->monnaie;
		$numberwords=$handle->toCurrency($number, $outlang, $conf->monnaie);
	}
	else
	{
		$numberwords=$handle->toWords($number, $outlang);
	}

	if (empty($handle->error)) return $numberwords;
	else return $handle->error;
}

