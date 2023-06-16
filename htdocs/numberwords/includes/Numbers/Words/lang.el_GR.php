<?php
/**
 * Numbers_Words
 *
 * PHP version 4
 *
 * Copyright (c) 1997-2006 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available at through the world-wide-web at
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @category Numbers
 * @package  Numbers_Words
 * @author   Nick Fragoulis
 * @license  PHP 3.0 http://www.php.net/license/3_0.txt
 * 
 */

/**
 *
 * Class for translating numbers into Greek.
 * @author Nick Fragoulis
 * 
 */

/**
 * Include needed files
 */
require_once "Numbers/Words.php";

/**
 * 
 * Class for translating numbers into Greek.
 * @category Numbers
 * @author Nick Fragoulis
 * 
 */
class Numbers_Words_el_GR extends Numbers_Words
{

	/**
	 * Locale name
	 * @var string
	 * @access public
	 */
	var $locale = 'el_GR';

	/**
	 * Language name in English
	 * @var string
	 * @access public
	 */
	var $lang = 'Greek';

	/**
	 * Native language name
	 * @var string
	 * @access public
	 */
	var $lang_native = 'Ελληνικά';

	/**
	 * The word for the minus sign
	 * @var string
	 * @access private
	 */
	var $_minus = 'Μείον'; // minus sign

	/**
	 * The sufixes for exponents (singular and plural)
	 * @var array
	 * @access private
	 */
	var $_exponent = array(
		0 => array(''),
		3 => array('Χίλια', 'Χιλιάδες'),
		6 => array('Εκατομμύριο', 'Εκατομμύρια'),
		9 => array('Δισεκατομμύριο', 'Δισεκατομμύρια'),
		);

	/**
	 * The array containing the digits (indexed by the digits themselves).
	 * @var array
	 * @access private
	 */
	var $_digits = array(
		0 => '', 'Ένα', 'Δύο', 'Τρία', 'Τέσσερα',
		'Πέντε', 'Έξι', 'Επτά', 'Οκτώ', 'Εννιά'
	);
	
    	private static $_proper_pronunciation = [
		'Εκατό '           => 'Εκατόν ',
        	' Ένα Χιλιάδες'    => ' Μία Χιλιάδες',
		'Τρία Χιλιάδες'    => 'Τρείς Χιλιάδες',
		'Τέσσερα Χιλιάδες' => 'Τέσσερις Χιλιάδες',
		'Εκατόν Χιλιάδες'  => 'Εκατό Χιλιάδες',
	];
    
    	private static $_hundreds_plural = [
        	'Διακόσια'   => 'Διακόσιες',
        	'Τριακόσια'  => 'Τριακόσιες',
        	'Τετρακόσια' => 'Τετρακόσιες',
        	'Πεντακόσια' => 'Πεντακόσιες',
        	'Εξακόσια'   => 'Εξακόσιες',
        	'Επτακόσια'  => 'Επτακόσιες',
        	'Οκτακόσια'  => 'Οκτακόσιες',
        	'Εννιακόσια' => 'Εννιακόσιες',
        ];    
    
	/**
	 * The word separator
	 * @var string
	 * @access private
	 */
	var $_sep = ' ';

	/**
	 * The default currency name
	 * @var string
	 * @access public
	 */
	var $def_currency = 'EUR'; // Ευρώ


	/**
	 * Converts a number to its word representation
	 * in Greek language
	 *
	 * @param integer $num       An integer between -infinity and infinity inclusive :)
	 *                           that need to be converted to words
	 * @param integer $power     The power of ten for the rest of the number to the right.
	 *                           Optional, defaults to 0.
	 * @param integer $powsuffix The power name to be added to the end of the return string.
	 *                           Used internally. Optional, defaults to ''.
	 *
	 * @return string  The corresponding word representation
	 *
	 * @access protected
	 * @author Piotr Klaban <makler@man.torun.pl>
	 * @since  Numbers_Words 0.16.3
	 */
	function _toWords($num, $power = 0, $powsuffix = '')
	{
		// The return string;
		$ret = '';

		// add a the word for the minus sign if necessary
		if (substr($num, 0, 1) == '-') {
			$ret = $this->_sep . $this->_minus;
			$num = substr($num, 1);
		}


		// strip excessive zero signs
		$num = preg_replace('/^0+/', '', $num);

		if (strlen($num) > 6) {
			$current_power = 6;
			// check for highest power
			if (isset($this->_exponent[$power])) {
				// convert the number above the first 6 digits
				// with it's corresponding $power.
				$snum = substr($num, 0, -6);
				$snum = preg_replace('/^0+/', '', $snum);
				if ($snum !== '') {
					$ret .= $this->_toWords($snum, $power + 6);
				}
			}
			$num = substr($num, -6);
			if ($num == 0) {
				return $ret;
			}
		} elseif ($num == 0 || $num == '') {
			return(' '.$this->_digits[0].' ');
			$current_power = strlen($num);
		} else {
			$current_power = strlen($num);
		}

		// See if we need "thousands"
		$thousands = floor($num / 1000);
		if ($thousands == 1) {
			$ret .= $this->_sep . 'Χίλια' . $this->_sep;
		} elseif ($thousands > 1) {
			$ret .= $this->_toWords($thousands, 3) . $this->_sep;

		}

		// values for digits, tens and hundreds
		$h = floor(($num / 100) % 10);
		$t = floor(($num / 10) % 10);
		$d = floor($num % 10);

		
		// ten, twenty etc.
		switch ($h) {
			case 9:
				$ret .= $this->_sep . 'Εννιακόσια';
			break;

			case 8:
				$ret .= $this->_sep . 'Οκτακόσια';
			break;

			case 7:
				$ret .= $this->_sep . 'Επτακόσια';
			break;

			case 6:
				$ret .= $this->_sep . 'Εξακόσια';
			break;

			case 5:
				$ret .= $this->_sep . 'Πεντακόσια';
			break;

			case 4:
				$ret .= $this->_sep . 'Τετρακόσια';
			break;

			case 3:
				$ret .= $this->_sep . 'Τριακόσια';
			break;

			case 2:
				$ret .= $this->_sep . 'Διακόσια';
			break;

			case 1:
				$ret .= $this->_sep . 'Εκατό';
			break;
		}

		switch ($t) {
			case 9:
				$ret .= $this->_sep . 'Ενενήντα';
			break;
					
			case 8:
				$ret .= $this->_sep . 'Ογδόντα';
			break;
					
			case 7:
				$ret .= $this->_sep . 'Εβδομήντα';
			break;
					
			case 6:
				$ret .= $this->_sep . 'Εξήντα';
			break;
					
			case 5:
				$ret .= $this->_sep . 'Πενήντα';
			break;

			case 4:
				$ret .= $this->_sep . 'Σαράντα';
			break;

			case 3:
				$ret .= $this->_sep . 'Τριάντα';
			break;

			case 2:
				$ret .= $this->_sep . 'Είκοσι';
			break;

			case 1:
				switch ($d) {
					case 0:
						$ret .= $this->_sep . 'Δέκα';
					break;

					case 1:
						$ret .= $this->_sep . 'Έντεκα';
					break;

					case 2:
						$ret .= $this->_sep . 'Δώδεκα';
					break;
					
					case 3:
						$ret .= $this->_sep . 'Δεκατρία';
					break;
					
					case 4:
						$ret .= $this->_sep . 'Δεκατέσσερα';
					break;
					
					case 5:
						$ret .= $this->_sep . 'Δεκαπέντε';
					break;

					case 6:
						$ret .= $this->_sep . 'Δεκαέξι';
					break;

					case 7:
						$ret .= $this->_sep . 'Δεκαεπτά';
					break;

					case 8:
						$ret .= $this->_sep . 'Δεκαοκτώ';
					break;

					case 9:
						$ret .= $this->_sep . 'Δεκαεννέα';
					break;
				}
			break;
		}

		// add digits only if it is a multiple of 10 and not 1x or 2x
		if ($t != 1 && $d > 0) { // add digits only in <0>,<1,9> and <21,inf>
			// add minus sign between [2-9] and digit
			if ($t > 1) {
				$ret .= ' ' . $this->_digits[$d];
			} else {
				$ret .= $this->_sep . $this->_digits[$d];
			}
		}

		if ($power > 0) {
			if (isset($this->_exponent[$power])) {
				$lev = $this->_exponent[$power];
			}

			if (!isset($lev) || !is_array($lev)) {
				return null;
			}

			// if it's only one use the singular suffix
			if (($d == 1) and ($t == 0) and ($h == 0)) {
				$suffix = $lev[0];
			} else {
				$suffix = $lev[1];
			}
			if ($num != 0) {
				$ret .= $this->_sep . $suffix;
			}
		}
			$ret = strtr($ret, self::$_proper_pronunciation);
			if ($power > 1)  {
			$ret = strtr($ret, self::$_hundreds_plural);
			}
		return $ret;
	}



	/**
	 * Converts a currency value to its word representation
	 * (with monetary units) in Greek language
	 *
	 * @param integer $int_curr         An international currency symbol
	 *                                  as defined by the ISO 4217 standard (three characters)
	 * @param integer $decimal          A money total amount without fraction part (e.g. amount of dollars)
	 * @param integer $fraction         Fractional part of the money amount (e.g. amount of cents)
	 *                                  Optional. Defaults to false.
	 * @param integer $convert_fraction Convert fraction to words (left as numeric if set to false).
	 *                                  Optional. Defaults to true.
	 *
	 * @return string  The corresponding word representation for the currency
	 *
	 * @access public
	 * @author Piotr Klaban <makler@man.torun.pl>
	 * @since  Numbers_Words 0.13.1
	 */
	function toCurrencyWords($int_curr, $decimal, $fraction = false, $convert_fraction = true)
	{
		$int_curr = strtoupper($int_curr);
		if (!isset($this->_currency_names[$int_curr])) {
			$int_curr = $this->def_currency;
		}
		$curr_names = $this->_currency_names[$int_curr];

		$ret = trim($this->_toWords($decimal));
		$lev = ($decimal == 1) ? 0 : 1;
		if ($lev > 0) {
			if (count($curr_names[0]) > 1) {
				$ret .= $this->_sep . $curr_names[0][$lev];
			} else {
				$ret .= $this->_sep . $curr_names[0][0] . '';
			}
		} else {
			$ret .= $this->_sep . $curr_names[0][0];
		}

		if ($fraction !== false) {
			if ($convert_fraction) {
				$ret .= $this->_sep . trim($this->_toWords($fraction));
			} else {
				$ret .= $this->_sep . $fraction;
			}
			$lev = ($fraction == 1) ? 0 : 1;
			if ($lev > 0) {
				if (count($curr_names[1]) > 1) {
					$ret .= $this->_sep . $curr_names[1][$lev];
				} else {
					$ret .= $this->_sep . $curr_names[1][0] . '';
				}
			} else {
				$ret .= $this->_sep . $curr_names[1][0];
			}
		}
		return $ret;
	}

}
