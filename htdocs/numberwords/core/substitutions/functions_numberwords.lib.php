<?php
/* Copyright (C) 2009 Laurent Destailleur         <eldy@users.sourceforge.net>
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
 *	\file			htdocs/core/modules/substitutions/functions_numberwords.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains functions for plugin numberwords.
 */


/**
 * 		Function called to complete substitution array (before generating on ODT, or a personalized email)
 * 		functions xxx_completesubstitutionarray are called by make_substitutions() if file
 * 		is inside directory htdocs/core/substitutions
 *
 *		@param	array		$substitutionarray	Array with substitution key=>val
 *		@param	Translate	$langs				Output langs
 *		@param	Object		$object				Object to use to get values
 * 		@return	void							The entry parameter $substitutionarray is modified
 */
function numberwords_completesubstitutionarray(&$substitutionarray,$langs,$object)
{
	global $conf;
	if (is_object($object))
	{
		$numbertext=$langs->getLabelFromNumber($object->total_ttc,1);
		$substitutionarray['__TOTAL_TTC_WORDS__']=$numbertext;    // deprecated
		$substitutionarray['__AMOUNT_TEXT__']=$numbertext;
		$numbertext=$langs->getLabelFromNumber($object->multicurrency_total_ttc,1);
		$substitutionarray['__AMOUNT_CURRENCY_TEXT__']=$numbertext;

		$numbertext=$langs->getLabelFromNumber($object->total_ht,1);
		$substitutionarray['__TOTAL_HT_WORDS__']=$numbertext;    // deprecated
		$substitutionarray['__AMOUNT_WO_TAX_TEXT__']=$numbertext;
		$numbertext=$langs->getLabelFromNumber($object->multicurrency_total_ht,1);
		$substitutionarray['__AMOUNT_CURRENCY_WO_TAX_TEXT__']=$numbertext;

		$numbertext=$langs->getLabelFromNumber(((! empty($object->total_vat))?$object->total_vat:$object->total_tva),1);
		$substitutionarray['__TOTAL_VAT_WORDS__']=$numbertext;    // deprecated
		$substitutionarray['__AMOUNT_VAT_TEXT__']=$numbertext;
		$numbertext=$langs->getLabelFromNumber($object->multicurrency_total_tva,1);
		$substitutionarray['__AMOUNT_CURRENCY_VAT_TEXT__']=$numbertext;

		$numbertext=$langs->getLabelFromNumber((! empty($object->number))?$object->number:'',0);
		$substitutionarray['__NUMBER_WORDS__']=$numbertext;
	}
}

/**
 *  Return full text translated to language label for a key. Store key-label in a cache.
 *
 *	@param		Langs	$langs		Language for output
 * 	@param		int		$number		Number to encode in full text
 * 	@param		int		$isamount	1=It's an amount, 0=it's just a number
 *  @return     string				Label translated in UTF8 (but without entities)
 * 									10 if setDefaultLang was en_US => ten
 * 									123 if setDefaultLang was fr_FR => cent vingt trois
 */
function numberwords_getLabelFromNumber($langs,$number,$isamount=0)
{
	global $conf;

	dol_syslog("numberwords_getLabelFromNumber langs->defaultlang=".$langs->defaultlang." number=".$number." isamount=".$isamount);
	$langs->load("dict");

	$outlang=$langs->defaultlang;	// Output language we want
	$outlangarray=explode('_',$outlang,2);
	// If lang is xx_XX, then we use xx
	if (strtolower($outlangarray[0]) == strtolower($outlangarray[1])
		&& ! in_array($outlang, array('tr_TR','hu_HU'))) $outlang=$outlangarray[0];		// For turkish, we don't use short name.

	$numberwords=$number;

	dol_include_once('/numberwords/includes/Numbers/Words.php');
	$path=dol_buildpath('/numberwords/includes/Numbers/Words.php');
	$handle = new Numbers_Words();
	$handle->dir=dirname(dirname($path)).'/';
	//print $handle->dir;exit;

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
	$handle->labelcurrency=$conf->currency;	// By default (EUR, USD)
	$handle->labelcents='cent';				// By default (s is removed)
    if ($conf->global->MAIN_MAX_DECIMALS_TOT == 3) $handle->labelcents='thousandth'; // (s is removed)

	// Overwrite label of currency with ours
	$labelcurrencysing=$langs->transnoentitiesnoconv("CurrencySing".$conf->currency);
	//print "CurrencySing".$conf->currency."=>".$labelcurrencysing;
	if ($labelcurrencysing && $labelcurrencysing != -1 && $labelcurrencysing != 'CurrencySing'.$conf->currency)
	{
	    $handle->labelcurrencysing=$labelcurrencysing;
	}
	$labelcurrency=$langs->transnoentitiesnoconv("Currency".$conf->currency);
	if ($labelcurrency && $labelcurrency != -1 && $labelcurrency !='Currency'.$conf->currency)
	{
	    $handle->labelcurrency=$labelcurrency;
	}
	if (empty($handle->labelcurrencysing)) $handle->labelcurrencysing=$handle->labelcurrency;
	if (empty($handle->labelcurrency)) $handle->labelcurrency=$handle->labelcurrencysing;

	// Overwrite label of decimals to ours
	//print $langs->transnoentitiesnoconv("Currency".ucfirst($handle->labelcents)."Sing".$conf->currency);
	$labelcurrencycentsing=$langs->transnoentitiesnoconv("Currency".ucfirst($handle->labelcents)."Sing".$conf->currency);
	if ($labelcurrencycentsing && $labelcurrencycentsing != -1 && $labelcurrencycentsing!='Currency'.ucfirst($handle->labelcents).'Sing'.$conf->currency) $handle->labelcents=$labelcurrencycentsing;
	else
	{
		$labelcurrencycent=$langs->transnoentitiesnoconv("Currency".ucfirst($handle->labelcents).$conf->currency);
		if ($labelcurrencycent && $labelcurrencycent !='Currency'.ucfirst($handle->labelcents).$conf->currency) $handle->labelcents=$labelcurrencycent;
	}
	//var_dump($handle->labelcurrency.'-'.$handle->labelcents);
	//var_dump($labelcurrencycentsing.'-'.$labelcurrencycent);

	// Call method of object handle to make convertion
	if ($isamount)
	{
		//print "currency: ".$conf->currency;
		$numberwords=$handle->toCurrency($number, $outlang, $conf->currency);
	}
	else
	{
		$numberwords=$handle->toWords($number, $outlang);
	}

	if (empty($handle->error)) return $numberwords;
	else return $handle->error;
}
