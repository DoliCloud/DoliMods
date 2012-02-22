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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file			htdocs/core/modules/substitutions/functions_numberwords.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains functions for plugin numberwords.
 *	\version		$Id: functions_numberwords.lib.php,v 1.4 2011/06/08 20:47:38 eldy Exp $
 */


/**
 * 		Function called to complete substitution array (before generating on ODT, or a personalized email)
 * 		functions xxx_completesubstitutionarray are called by make_substitutions() if file
 * 		is inside directory htdocs/includes/modules/substitutions
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
 *      Return full text translated to language label for a key. Store key-label in a cache.
 *		@param		langs		Language for output
 * 		@param		number		Number to encode in full text
 * 		@param		isamount	1=It's an amount, 0=it's just a number
 *      @return     string		Label translated in UTF8 (but without entities)
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
	$handle->labelcents='cent';				// By default

	// Overwrite label of currency to ours
	$labelcurrencysing=$langs->transnoentitiesnoconv("CurrencySing".$conf->currency);
	//print "CurrencySing".$conf->currency."=>".$labelcurrencysing;
	if ($labelcurrencysing && $labelcurrencysing != -1 && $labelcurrencysing!='CurrencySing'.$conf->currency) $handle->labelcurrency=$labelcurrencysing;
	else
	{
		$labelcurrency=$langs->transnoentitiesnoconv("Currency".$conf->currency);
		if ($labelcurrency && $labelcurrency !='Currency'.$conf->currency) $handle->labelcurrency=$labelcurrency;
	}
	// Overwrite label of cent to ours
	$labelcurrencycentsing=$langs->transnoentitiesnoconv("CurrencyCentSing".$conf->currency);
	if ($labelcurrencycentsing && $labelcurrencycentsing != -1 &&$labelcurrencycentsing!='CurrencyCentSing'.$conf->currency) $handle->labelcents=$labelcurrencycentsing;
	else
	{
		$labelcurrencycent=$langs->transnoentitiesnoconv("CurrencyCent".$conf->currency);
		if ($labelcurrencycent && $labelcurrencycent !='CurrencyCent'.$conf->currency) $handle->labelcents=$labelcurrencycent;
	}

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

