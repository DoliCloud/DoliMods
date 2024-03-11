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
 *		@param	Translate	$outlangs			Output langs
 *		@param	Object		$object				Object to use to get values
 * 		@return	void							The entry parameter $substitutionarray is modified
 */
function numberwords_completesubstitutionarray(&$substitutionarray, $outlangs, $object)
{
	global $conf;

	if (is_object($object) && ($object->id > 0 || $object->specimen)) {	// We do not add substitution entries if object is not instantiated (->id not > 0)
		$numbertext=$outlangs->getLabelFromNumber((isset($object->total_ttc) ? $object->total_ttc : ''), 1);
		//$substitutionarray['__TOTAL_TTC_WORDS__']=$numbertext;    	// deprecated
		$substitutionarray['__AMOUNT_TEXT__'] = $numbertext;

		if (isset($object->multicurrency_total_ttc)) {
			$numbertext = $outlangs->getLabelFromNumber((isset($object->multicurrency_total_ttc) ? $object->multicurrency_total_ttc : ''), $object->multicurrency_code);
		} else {
			$numbertext = '';
		}
		$substitutionarray['__AMOUNT_MULTICURRENCY_TEXT__'] = (!empty($object->multicurrency_code) ? $numbertext : '');

		$numbertext=$outlangs->getLabelFromNumber((isset($object->total_ht) ? $object->total_ht : ''), 1);
		//$substitutionarray['__TOTAL_HT_WORDS__']=$numbertext;    	// deprecated
		//$substitutionarray['__AMOUNT_WO_TAX_TEXT__']=$numbertext;	// deprecated
		$substitutionarray['__AMOUNT_EXCL_TAX_TEXT__'] = $numbertext;

		if (isset($object->multicurrency_total_ht)) {
			$numbertext = $outlangs->getLabelFromNumber((isset($object->multicurrency_total_ht) ? $object->multicurrency_total_ht : ''), $object->multicurrency_code);
		} else {
			$numbertext = '';
		}
		//$substitutionarray['__AMOUNT_CURRENCY_WO_TAX_TEXT__']=$numbertext;
		$substitutionarray['__AMOUNT_MULTICURRENCY_EXCL_TAX_TEXT__'] = (!empty($object->multicurrency_code) ? $numbertext : '');

		$numbertext=$outlangs->getLabelFromNumber((isset($object->total_vat) ? $object->total_vat : $object->total_tva), 1);
		//$substitutionarray['__TOTAL_VAT_WORDS__']=$numbertext;    	// deprecated
		$substitutionarray['__AMOUNT_VAT_TEXT__'] = $numbertext;

		if (isset($object->multicurrency_total_tva)) {
			$numbertext = $outlangs->getLabelFromNumber((isset($object->multicurrency_total_tva) ? $object->multicurrency_total_tva : ''), $object->multicurrency_code);
		} else {
			$numbertext = '';
		}
		$substitutionarray['__AMOUNT_MULTICURRENCY_VAT_TEXT__'] = (!empty($object->multicurrency_code) ? $numbertext : '');

		// Use number words for property ->number of object with __NUMBER_WORDS__
		$numbertext=$outlangs->getLabelFromNumber((isset($object->number) ? $object->number : ''), 0);
		$substitutionarray['__NUMBER_WORDS__'] = $numbertext;
	}
}

/**
 *  Return full text translated to language label for a key. Store key-label in a cache.
 *
 *	@param		Translate	$outlangs	Language for output
 * 	@param		int|string	$number		Number to encode in full text
 *  @param      string	    $isamount	''=it's just a number, '1'=It's an amount (default currency), 'currencycode'=It's an amount (foreign currency)
 *  @return     string				    Label translated in UTF8 (but without entities)
 * 									    10 if setDefaultLang was en_US => ten
 * 									    123 if setDefaultLang was fr_FR => cent vingt trois
 */
function numberwords_getLabelFromNumber($outlangs, $number, $isamount = '')
{
	global $conf;

	$currencycode = $conf->currency;   // default value
	if ($isamount && (string) $isamount != '1') {
		$currencycode = $isamount;
	}

	dol_syslog("numberwords_getLabelFromNumber langs->defaultlang=".$outlangs->defaultlang." number=".$number." isamount=".$isamount);

	if (is_null($number)) {
		return '';
	}

	$outlangs->load("dict");

	$outlang=$outlangs->defaultlang;	// Output language we want
	$outlangarray=explode('_', $outlang, 2);
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
	if (! file_exists($handle->dir.'Numbers/Words/lang.'.$outlang.'.php')) {
		// We try with short code
		$tmparray=explode('_', $outlang);
		$outlang=$tmparray[0];
	}

	if (! file_exists($handle->dir.'Numbers/Words/lang.'.$outlang.'.php')) {
		return "(Error: No rule file into Numbers/Words to convert number to text for language ".$outlangs->defaultlang.")";
	}

	// Define label on currency and cent in the property of object handle
	$handle->labelcurrency=$currencycode;	// By default (EUR, USD)
	$handle->labelcents='cent';				// By default (s is removed)
	if (getDolGlobalInt('MAIN_MAX_DECIMALS_TOT') == 3) $handle->labelcents='thousandth'; // (s is removed)

	// Overwrite label of currency with ours
	$labelcurrencysing=$outlangs->transnoentitiesnoconv("CurrencySing".$currencycode);
	//print "CurrencySing".$currencycode."=>".$labelcurrencysing;
	if ($labelcurrencysing && $labelcurrencysing != -1 && $labelcurrencysing != 'CurrencySing'.$currencycode) {
		$handle->labelcurrencysing=$labelcurrencysing;
	}
	$labelcurrency=$outlangs->transnoentitiesnoconv("Currency".$currencycode);
	if ($labelcurrency && $labelcurrency != -1 && $labelcurrency != 'Currency'.$currencycode) {
		$handle->labelcurrency=$labelcurrency;
	}
	if (empty($handle->labelcurrencysing)) $handle->labelcurrencysing=$handle->labelcurrency;
	if (empty($handle->labelcurrency)) $handle->labelcurrency=$handle->labelcurrencysing;

	// Overwrite label of decimals to ours
	//print $outlangs->transnoentitiesnoconv("Currency".ucfirst($handle->labelcents)."Sing".$currencycode);
	$transforsingnotfound=false;
	$savlabelcents=$handle->labelcents;
	$labelcurrencycentsing=$outlangs->transnoentitiesnoconv("Currency".ucfirst($savlabelcents)."Sing".$currencycode);
	if ($labelcurrencycentsing && $labelcurrencycentsing != -1 && $labelcurrencycentsing != 'Currency'.ucfirst($savlabelcents).'Sing'.$currencycode) {
		$handle->labelcents=$labelcurrencycentsing;
	} else {
		$transforsingnotfound=true;
	}

	$decimal = 0;
	if (strpos($number, '.') !== false) {
		list($whole, $decimal) = explode('.', $number);
	}
	//var_dump($number.'->'.$decimal);
	if ($decimal > 1 || $transforsingnotfound) {
		$labelcurrencycent=$outlangs->transnoentitiesnoconv("Currency".ucfirst($savlabelcents).$currencycode);
		if ($labelcurrencycent && $labelcurrencycent != 'Currency'.ucfirst($savlabelcents).$currencycode) {
			$handle->labelcents=preg_replace('/s$/', '', $labelcurrencycent);  // The s is added by the toCurrency() method.
		}
		//var_dump("Currency".ucfirst($handle->labelcents).$currencycode);
	}
	//var_dump($handle->labelcurrency.'-'.$handle->labelcents);
	//var_dump($labelcurrencycentsing.'-'.$labelcurrencycent);

	// Call method of object handle to make convertion
	if ($isamount && empty($conf->global->NUMBERWORDS_USE_CURRENCY_SYMBOL)) {
		//print "currency: ".$currencycode;
		$numberwords=$handle->toCurrency($number, $outlang, $currencycode);

		$numberwords = preg_replace('/(\s+cfa)(\sBEAC|\sBCEAO|s)/i', '\1', $numberwords);   // Replace 'Francs cfas' with 'Francs cfa'
	} elseif ($isamount && ! empty($conf->global->NUMBERWORDS_USE_CURRENCY_SYMBOL)) {
		$cursymbolbefore='';
		$cursymbolafter='';
		$listofcurrenciesbefore=array('USD','GBP','AUD','HKD','MXN','PEN','CNY');      // List of currency where currency is before text
		$listoflanguagesbefore=array('nl_NL');                                         // List of language code that use the currency before, whatever is currency
		if (in_array($currencycode, $listofcurrenciesbefore) || in_array($outlangs->defaultlang, $listoflanguagesbefore)) {
			$cursymbolbefore.=$outlangs->getCurrencySymbol($currencycode);
		} else {
			$tmpcur=$outlangs->getCurrencySymbol($currencycode);
			$cursymbolafter.=($tmpcur == $currencycode ? ' '.$tmpcur : $tmpcur);
		}
		if ($cursymbolbefore && (! empty($conf->global->NUMBERWORDS_USE_ADD_SHORTCODE_WITH_SYMBOL) || ! empty($conf->global->MAIN_CURRENCY_ADD_SHORTCODE_WITH_SYMBOL))) {
			$cursymbolbefore=substr($currencycode, 0, 2).$cursymbolbefore;
		}
		if ($cursymbolafter && (! empty($conf->global->NUMBERWORDS_USE_ADD_SHORTCODE_WITH_SYMBOL) || ! empty($conf->global->MAIN_CURRENCY_ADD_SHORTCODE_WITH_SYMBOL))) {
			$cursymbolafter=substr($currencycode, 0, 2).$cursymbolafter;
		}
		if (in_array($currencycode, $listofcurrenciesbefore) || in_array($outlangs->defaultlang, $listoflanguagesbefore)) {
			$numberwords = $handle->toCurrency($number, $outlang);
			$numberwords = preg_replace('/'.preg_quote($labelcurrency).'s? /i', $outlangs->transnoentitiesnoconv("and").' ', $numberwords);
			$numberwords = preg_replace('/'.preg_quote($labelcurrency).'s?/i', '', $numberwords);
			$numberwords = $cursymbolbefore.' '.$numberwords;
		} else {
			/*var_dump('eee');
			var_dump($outlang);
			var_dump($currencycode);
			var_dump($outlangs->trans('Currency'.$currencycode));*/
			$numberwords = preg_replace('/(Dollars?|Euros?|Yens?|'.preg_quote($outlangs->trans('Currency'.$currencycode), '/').'?)/i', $cursymbolafter, $handle->toCurrency($number, $outlang));
		}
		//$numberwords=$cursymbolbefore.($cursymbolbefore?' ':'').$texttouse.($cursymbolafter?' ':'').$cursymbolafter;
	} else {
		$numberwords=$handle->toWords($number, $outlang);
	}

	if (empty($handle->error)) return $numberwords;
	else return $handle->error;
}
