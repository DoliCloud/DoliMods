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
 * @author   Piotr Klaban <makler@man.torun.pl>
 * @author   Andrey Demenev <demenev@gmail.com>
 * @license  PHP 3.0 http://www.php.net/license/3_0.txt
 * @version  SVN: $Id: lang.ru.php 302816 2010-08-26 16:02:29Z ifeghali $
 * @link     http://pear.php.net/package/Numbers_Words
 */

/**
 * Class for translating numbers into Russian.
 *
 * @author Andrey Demenev
 * @package Numbers_Words
 */

/**
 * Include needed files
 */
require_once "Numbers/Words.php";

/**
 * Class for translating numbers into Russian.
 *
 * @category Numbers
 * @package  Numbers_Words
 * @author   Piotr Klaban <makler@man.torun.pl>
 * @author   Andrey Demenev <demenev@gmail.com>
 * @license  PHP 3.0 http://www.php.net/license/3_0.txt
 * @link     http://pear.php.net/package/Numbers_Words
 */
class Numbers_Words_ru extends Numbers_Words
{

    // {{{ properties

    /**
     * Locale name
     * @var string
     * @access public
     */
    var $locale = 'ru';

    /**
     * Language name in English
     * @var string
     * @access public
     */
    var $lang = 'Russian';

    /**
     * Native language name
     * @var string
     * @access public
     */
    var $lang_native = 'Ðóññêèé';
    
    /**
     * The word for the minus sign
     * @var string
     * @access private
     */
    var $_minus = 'ìèíóñ'; // minus sign
    
    /**
     * The sufixes for exponents (singular)
     * Names partly based on:
     * http://home.earthlink.net/~mrob/pub/math/largenum.html
     * http://mathforum.org/dr.math/faq/faq.large.numbers.html
     * http://www.mazes.com/AmericanNumberingSystem.html
     * @var array
     * @access private
     */
    var $_exponent = array(
        0 => '',
        6 => 'ìèëëèîí',
        9 => 'ìèëëèàðä',
       12 => 'òðèëëèîí',
       15 => 'êâàäðèëëèîí',
       18 => 'êâèíòèëëèîí',
       21 => 'ñåêñòèëëèîí',
       24 => 'ñåïòèëëèîí',
       27 => 'îêòèëëèîí',
       30 => 'íîíèëëèîí',
       33 => 'äåöèëëèîí',
       36 => 'óíäåöèëëèîí',
       39 => 'äóîäåöèëëèîí',
       42 => 'òðåäåöèëëèîí',
       45 => 'êâàòóîðäåöèëëèîí',
       48 => 'êâèíäåöèëëèîí',
       51 => 'ñåêñäåöèëëèîí',
       54 => 'ñåïòåíäåöèëëèîí',
       57 => 'îêòîäåöèëëèîí',
       60 => 'íîâåìäåöèëëèîí',
       63 => 'âèãèíòèëëèîí',
       66 => 'óíâèãèíòèëëèîí',
       69 => 'äóîâèãèíòèëëèîí',
       72 => 'òðåâèãèíòèëëèîí',
       75 => 'êâàòóîðâèãèíòèëëèîí',
       78 => 'êâèíâèãèíòèëëèîí',
       81 => 'ñåêñâèãèíòèëëèîí',
       84 => 'ñåïòåíâèãèíòèëëèîí',
       87 => 'îêòîâèãèíòèëëèîí',
       90 => 'íîâåìâèãèíòèëëèîí',
       93 => 'òðèãèíòèëëèîí',
       96 => 'óíòðèãèíòèëëèîí',
       99 => 'äóîòðèãèíòèëëèîí',
       102 => 'òðåòðèãèíòèëëèîí',
       105 => 'êâàòîðòðèãèíòèëëèîí',
       108 => 'êâèíòðèãèíòèëëèîí',
       111 => 'ñåêñòðèãèíòèëëèîí',
       114 => 'ñåïòåíòðèãèíòèëëèîí',
       117 => 'îêòîòðèãèíòèëëèîí',
       120 => 'íîâåìòðèãèíòèëëèîí',
       123 => 'êâàäðàãèíòèëëèîí',
       126 => 'óíêâàäðàãèíòèëëèîí',
       129 => 'äóîêâàäðàãèíòèëëèîí',
       132 => 'òðåêâàäðàãèíòèëëèîí',
       135 => 'êâàòîðêâàäðàãèíòèëëèîí',
       138 => 'êâèíêâàäðàãèíòèëëèîí',
       141 => 'ñåêñêâàäðàãèíòèëëèîí',
       144 => 'ñåïòåíêâàäðàãèíòèëëèîí',
       147 => 'îêòîêâàäðàãèíòèëëèîí',
       150 => 'íîâåìêâàäðàãèíòèëëèîí',
       153 => 'êâèíêâàãèíòèëëèîí',
       156 => 'óíêâèíêàãèíòèëëèîí',
       159 => 'äóîêâèíêàãèíòèëëèîí',
       162 => 'òðåêâèíêàãèíòèëëèîí',
       165 => 'êâàòîðêâèíêàãèíòèëëèîí',
       168 => 'êâèíêâèíêàãèíòèëëèîí',
       171 => 'ñåêñêâèíêàãèíòèëëèîí',
       174 => 'ñåïòåíêâèíêàãèíòèëëèîí',
       177 => 'îêòîêâèíêàãèíòèëëèîí',
       180 => 'íîâåìêâèíêàãèíòèëëèîí',
       183 => 'ñåêñàãèíòèëëèîí',
       186 => 'óíñåêñàãèíòèëëèîí',
       189 => 'äóîñåêñàãèíòèëëèîí',
       192 => 'òðåñåêñàãèíòèëëèîí',
       195 => 'êâàòîðñåêñàãèíòèëëèîí',
       198 => 'êâèíñåêñàãèíòèëëèîí',
       201 => 'ñåêññåêñàãèíòèëëèîí',
       204 => 'ñåïòåíñåêñàãèíòèëëèîí',
       207 => 'îêòîñåêñàãèíòèëëèîí',
       210 => 'íîâåìñåêñàãèíòèëëèîí',
       213 => 'ñåïòàãèíòèëëèîí',
       216 => 'óíñåïòàãèíòèëëèîí',
       219 => 'äóîñåïòàãèíòèëëèîí',
       222 => 'òðåñåïòàãèíòèëëèîí',
       225 => 'êâàòîðñåïòàãèíòèëëèîí',
       228 => 'êâèíñåïòàãèíòèëëèîí',
       231 => 'ñåêññåïòàãèíòèëëèîí',
       234 => 'ñåïòåíñåïòàãèíòèëëèîí',
       237 => 'îêòîñåïòàãèíòèëëèîí',
       240 => 'íîâåìñåïòàãèíòèëëèîí',
       243 => 'îêòîãèíòèëëèîí',
       246 => 'óíîêòîãèíòèëëèîí',
       249 => 'äóîîêòîãèíòèëëèîí',
       252 => 'òðåîêòîãèíòèëëèîí',
       255 => 'êâàòîðîêòîãèíòèëëèîí',
       258 => 'êâèíîêòîãèíòèëëèîí',
       261 => 'ñåêñîêòîãèíòèëëèîí',
       264 => 'ñåïòîêòîãèíòèëëèîí',
       267 => 'îêòîîêòîãèíòèëëèîí',
       270 => 'íîâåìîêòîãèíòèëëèîí',
       273 => 'íîíàãèíòèëëèîí',
       276 => 'óííîíàãèíòèëëèîí',
       279 => 'äóîíîíàãèíòèëëèîí',
       282 => 'òðåíîíàãèíòèëëèîí',
       285 => 'êâàòîðíîíàãèíòèëëèîí',
       288 => 'êâèííîíàãèíòèëëèîí',
       291 => 'ñåêñíîíàãèíòèëëèîí',
       294 => 'ñåïòåííîíàãèíòèëëèîí',
       297 => 'îêòîíîíàãèíòèëëèîí',
       300 => 'íîâåìíîíàãèíòèëëèîí',
       303 => 'öåíòèëëèîí'
        );

    /**
     * The array containing the teens' :) names
     * @var array
     * @access private
     */
    var $_teens = array(
        11=>'îäèííàäöàòü',
        12=>'äâåíàäöàòü',
        13=>'òðèíàäöàòü',
        14=>'÷åòûðíàäöàòü',
        15=>'ïÿòíàäöàòü',
        16=>'øåñòíàäöàòü',
        17=>'ñåìíàäöàòü',
        18=>'âîñåìíàäöàòü',
        19=>'äåâÿòíàäöàòü'
        );

    /**
     * The array containing the tens' names
     * @var array
     * @access private
     */
    var $_tens = array(
        2=>'äâàäöàòü',
        3=>'òðèäöàòü',
        4=>'ñîðîê',
        5=>'ïÿòüäåñÿò',
        6=>'øåñòüäåñÿò',
        7=>'ñåìüäåñÿò',
        8=>'âîñåìüäåñÿò',
        9=>'äåâÿíîñòî'
        );

    /**
     * The array containing the hundreds' names
     * @var array
     * @access private
     */
    var $_hundreds = array(
        1=>'ñòî',
        2=>'äâåñòè',
        3=>'òðèñòà',
        4=>'÷åòûðåñòà',
        5=>'ïÿòüñîò',
        6=>'øåñòüñîò',
        7=>'ñåìüñîò',
        8=>'âîñåìüñîò',
        9=>'äåâÿòüñîò'
        );

    /**
     * The array containing the digits 
     * for neutral, male and female
     * @var array
     * @access private
     */
    var $_digits = array(
        array('íîëü', 'îäíî', 'äâà', 'òðè', '÷åòûðå', 'ïÿòü', 'øåñòü', 'ñåìü', 'âîñåìü', 'äåâÿòü'),
        array('íîëü', 'îäèí', 'äâà', 'òðè', '÷åòûðå', 'ïÿòü', 'øåñòü', 'ñåìü', 'âîñåìü', 'äåâÿòü'),
        array('íîëü', 'îäíà', 'äâå', 'òðè', '÷åòûðå', 'ïÿòü', 'øåñòü', 'ñåìü', 'âîñåìü', 'äåâÿòü')
    );

    /**
     * The word separator
     * @var string
     * @access private
     */
    var $_sep = ' ';

    /**
     * The currency names (based on the below links,
     * informations from central bank websites and on encyclopedias)
     *
     * @var array
     * @link http://www.jhall.demon.co.uk/currency/by_abbrev.html World currencies
     * @link http://www.rusimpex.ru/Content/Reference/Refinfo/valuta.htm Foreign currencies names
     * @link http://www.cofe.ru/Finance/money.asp Currencies names
     * @access private
     */
    var $_currency_names = array(
      'ALL' => array(
                array(1, 'ëåê', 'ëåêà', 'ëåêîâ'), 
                array(2, 'êèíäàðêà', 'êèíäàðêè', 'êèíäàðîê')
               ),
      'AUD' => array(
                array(1, 'àâñòðàëèéñêèé äîëëàð', 'àâñòðàëèéñêèõ äîëëàðà', 'àâñòðàëèéñêèõ äîëëàðîâ'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               ),
      'BGN' => array(
                array(1, 'ëåâ', 'ëåâà', 'ëåâîâ'), 
                array(2, 'ñòîòèíêà', 'ñòîòèíêè', 'ñòîòèíîê')
               ),
      'BRL' => array(
                array(1, 'áðàçèëüñêèé ðåàë', 'áðàçèëüñêèõ ðåàëà', 'áðàçèëüñêèõ ðåàëîâ'), 
                array(1, 'ñåíòàâî', 'ñåíòàâî', 'ñåíòàâî')
               ),
      'BYR' => array(
                array(1, 'áåëîðóññêèé ðóáëü', 'áåëîðóññêèõ ðóáëÿ', 'áåëîðóññêèõ ðóáëåé'), 
                array(2, 'êîïåéêà', 'êîïåéêè', 'êîïååê')
               ),
      'CAD' => array(
                array(1, 'êàíàäñêèé äîëëàð', 'êàíàäñêèõ äîëëàðà', 'êàíàäñêèõ äîëëàðîâ'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               ),
      'CHF' => array(
                array(1, 'øâåéöàðñêèé ôðàíê', 'øâåéöàðñêèõ ôðàíêà', 'øâåéöàðñêèõ ôðàíêîâ'),
                array(1, 'ñàíòèì', 'ñàíòèìà', 'ñàíòèìîâ')
               ),
      'CYP' => array(
                array(1, 'êèïðñêèé ôóíò', 'êèïðñêèõ ôóíòà', 'êèïðñêèõ ôóíòîâ'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               ),
      'CZK' => array(
                array(2, '÷åøñêàÿ êðîíà', '÷åøñêèõ êðîíû', '÷åøñêèõ êðîí'),
                array(1, 'ãàëèðæ', 'ãàëèðæà', 'ãàëèðæåé')
               ),
      'DKK' => array(
                array(2, 'äàòñêàÿ êðîíà', 'äàòñêèõ êðîíû', 'äàòñêèõ êðîí'),
                array(1, 'ýðå', 'ýðå', 'ýðå')
               ),
      'EEK' => array(
                array(2, 'ýñòîíñêàÿ êðîíà', 'ýñòîíñêèõ êðîíû', 'ýñòîíñêèõ êðîí'),
                array(1, 'ñåíòè', 'ñåíòè', 'ñåíòè')
               ),
      'EUR' => array(
                array(1, 'åâðî', 'åâðî', 'åâðî'),
                array(1, 'åâðîöåíò', 'åâðîöåíòà', 'åâðîöåíòîâ')
               ),
      'CYP' => array(
                array(1, 'ôóíò ñòåðëèíãîâ', 'ôóíòà ñòåðëèíãîâ', 'ôóíòîâ ñòåðëèíãîâ'),
                array(1, 'ïåíñ', 'ïåíñà', 'ïåíñîâ')
               ),
      'CAD' => array(
                array(1, 'ãîíêîíãñêèé äîëëàð', 'ãîíêîíãñêèõ äîëëàðà', 'ãîíêîíãñêèõ äîëëàðîâ'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               ),
      'HRK' => array(
                array(2, 'õîðâàòñêàÿ êóíà', 'õîðâàòñêèõ êóíû', 'õîðâàòñêèõ êóí'),
                array(2, 'ëèïà', 'ëèïû', 'ëèï')
               ),
      'HUF' => array(
                array(1, 'âåíãåðñêèé ôîðèíò', 'âåíãåðñêèõ ôîðèíòà', 'âåíãåðñêèõ ôîðèíòîâ'),
                array(1, 'ôèëëåð', 'ôèëëåðà', 'ôèëëåðîâ')
               ),
      'ISK' => array(
                array(2, 'èñëàíäñêàÿ êðîíà', 'èñëàíäñêèõ êðîíû', 'èñëàíäñêèõ êðîí'),
                array(1, 'ýðå', 'ýðå', 'ýðå')
               ),
      'JPY' => array(
                array(2, 'èåíà', 'èåíû', 'èåí'),
                array(2, 'ñåíà', 'ñåíû', 'ñåí')
               ),
      'LTL' => array(
                array(1, 'ëèò', 'ëèòà', 'ëèòîâ'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               ),
      'LVL' => array(
                array(1, 'ëàò', 'ëàòà', 'ëàòîâ'),
                array(1, 'ñåíòèì', 'ñåíòèìà', 'ñåíòèìîâ')
               ),
      'MKD' => array(
                array(1, 'ìàêåäîíñêèé äèíàð', 'ìàêåäîíñêèõ äèíàðà', 'ìàêåäîíñêèõ äèíàðîâ'),
                array(1, 'äåíè', 'äåíè', 'äåíè')
               ),
      'MTL' => array(
                array(2, 'ìàëüòèéñêàÿ ëèðà', 'ìàëüòèéñêèõ ëèðû', 'ìàëüòèéñêèõ ëèð'),
                array(1, 'ñåíòèì', 'ñåíòèìà', 'ñåíòèìîâ')
               ),
      'NOK' => array(
                array(2, 'íîðâåæñêàÿ êðîíà', 'íîðâåæñêèõ êðîíû', 'íîðâåæñêèõ êðîí'),
                array(0, 'ýðå', 'ýðå', 'ýðå')
               ),
      'PLN' => array(
                array(1, 'çëîòûé', 'çëîòûõ', 'çëîòûõ'),
                array(1, 'ãðîø', 'ãðîøà', 'ãðîøåé')
               ),
      'ROL' => array(
                array(1, 'ðóìûíñêèé ëåé', 'ðóìûíñêèõ ëåé', 'ðóìûíñêèõ ëåé'),
                array(1, 'áàíè', 'áàíè', 'áàíè')
               ),
       // both RUR and RUR are used, I use RUB for shorter form
      'RUB' => array(
                array(1, 'ðóáëü', 'ðóáëÿ', 'ðóáëåé'),
                array(2, 'êîïåéêà', 'êîïåéêè', 'êîïååê')
               ),
      'RUR' => array(
                array(1, 'ðîññèéñêèé ðóáëü', 'ðîññèéñêèõ ðóáëÿ', 'ðîññèéñêèõ ðóáëåé'),
                array(2, 'êîïåéêà', 'êîïåéêè', 'êîïååê')
               ),
      'SEK' => array(
                array(2, 'øâåäñêàÿ êðîíà', 'øâåäñêèõ êðîíû', 'øâåäñêèõ êðîí'),
                array(1, 'ýðå', 'ýðå', 'ýðå')
               ),
      'SIT' => array(
                array(1, 'ñëîâåíñêèé òîëàð', 'ñëîâåíñêèõ òîëàðà', 'ñëîâåíñêèõ òîëàðîâ'),
                array(2, 'ñòîòèíà', 'ñòîòèíû', 'ñòîòèí')
               ),
      'SKK' => array(
                array(2, 'ñëîâàöêàÿ êðîíà', 'ñëîâàöêèõ êðîíû', 'ñëîâàöêèõ êðîí'),
                array(0, '', '', '')
               ),
      'TRL' => array(
                array(2, 'òóðåöêàÿ ëèðà', 'òóðåöêèõ ëèðû', 'òóðåöêèõ ëèð'),
                array(1, 'ïèàñòð', 'ïèàñòðà', 'ïèàñòðîâ')
               ),
      'UAH' => array(
                array(2, 'ãðèâíà', 'ãðèâíû', 'ãðèâåí'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               ),
      'USD' => array(
                array(1, 'äîëëàð ÑØÀ', 'äîëëàðà ÑØÀ', 'äîëëàðîâ ÑØÀ'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               ),
      'YUM' => array(
                array(1, 'þãîñëàâñêèé äèíàð', 'þãîñëàâñêèõ äèíàðà', 'þãîñëàâñêèõ äèíàðîâ'),
                array(1, 'ïàðà', 'ïàðà', 'ïàðà')
               ),
      'ZAR' => array(
                array(1, 'ðàíä', 'ðàíäà', 'ðàíäîâ'),
                array(1, 'öåíò', 'öåíòà', 'öåíòîâ')
               )
    );

    /**
     * The default currency name
     * @var string
     * @access public
     */
    var $def_currency = 'RUB'; // Russian rouble

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in Russian language
     *
     * @param integer $num    An integer between -infinity and infinity inclusive :)
     *                        that need to be converted to words
     * @param integer $gender Gender of string, 0=neutral, 1=male, 2=female.
     *                        Optional, defaults to 1.
     *
     * @return string  The corresponding word representation
     *
     * @access protected
     * @author Andrey Demenev <demenev@on-line.jar.ru>
     * @since  Numbers_Words 0.16.3
     */
    function _toWords($num, $options = array()) 
    {
        $dummy  = null;
        $gender = 1;

        /**
         * Loads user options
         */
        extract($options, EXTR_IF_EXISTS);

        return $this->_toWordsWithCase($num, $dummy, $gender);
    }

    /**
     * Converts a number to its word representation
     * in Russian language and determines the case of string.
     *
     * @param integer $num    An integer between -infinity and infinity inclusive :)
     *                        that need to be converted to words
     * @param integer &$case  A variable passed by reference which is set to case
     *                        of the word associated with the number
     * @param integer $gender Gender of string, 0=neutral, 1=male, 2=female.
     *                        Optional, defaults to 1.
     *
     * @return string  The corresponding word representation
     *
     * @access private
     * @author Andrey Demenev <demenev@on-line.jar.ru>
     */
    function _toWordsWithCase($num, &$case, $gender = 1)
    {
        $ret  = '';
        $case = 3;
      
        $num = trim($num);
      
        $sign = "";
        if (substr($num, 0, 1) == '-') {
            $sign = $this->_minus . $this->_sep;
            $num  = substr($num, 1);
        }

        while (strlen($num) % 3) {
            $num = '0' . $num;
        }

        if ($num == 0 || $num == '') {
            $ret .= $this->_digits[$gender][0];
        } else {
            $power = 0;

            while ($power < strlen($num)) {
                if (!$power) {
                    $groupgender = $gender;
                } elseif ($power == 3) {
                    $groupgender = 2;
                } else {
                    $groupgender = 1;
                }

                $group = $this->_groupToWords(substr($num, -$power-3, 3), $groupgender, $_case);
                if (!$power) {
                    $case = $_case;
                }

                if ($power == 3) {
                    if ($_case == 1) {
                        $group .= $this->_sep . 'òûñÿ÷à';
                    } elseif ($_case == 2) {
                        $group .= $this->_sep . 'òûñÿ÷è';
                    } else {
                        $group .= $this->_sep . 'òûñÿ÷';
                    }
                } elseif ($group && $power>3 && isset($this->_exponent[$power])) {
                    $group .= $this->_sep . $this->_exponent[$power];
                    if ($_case == 2) {
                        $group .= 'à';
                    } elseif ($_case == 3) {
                        $group .= 'îâ';
                    }
                }

                if ($group) {
                    $ret = $group . $this->_sep . $ret;
                }

                $power += 3;
            }
        }

        return $sign . $ret;
    }

    // }}}
    // {{{ _groupToWords()

    /**
     * Converts a group of 3 digits to its word representation
     * in Russian language.
     *
     * @param integer $num    An integer between -infinity and infinity inclusive :)
     *                        that need to be converted to words
     * @param integer $gender Gender of string, 0=neutral, 1=male, 2=female.
     * @param integer &$case  A variable passed by reference which is set to case
     *                        of the word associated with the number
     *
     * @return string  The corresponding word representation
     *
     * @access private
     * @author Andrey Demenev <demenev@on-line.jar.ru>
     */
    function _groupToWords($num, $gender, &$case)
    {
        $ret  = '';        
        $case = 3;

        if ((int)$num == 0) {
            $ret = '';
        } elseif ($num < 10) {
            $ret = $this->_digits[$gender][(int)$num];
            if ($num == 1) {
                $case = 1;
            } elseif ($num < 5) {
                $case = 2; 
            } else {
                $case = 3;
            }

        } else {
            $num = str_pad($num, 3, '0', STR_PAD_LEFT);

            $hundreds = (int)$num{0};
            if ($hundreds) {
                $ret = $this->_hundreds[$hundreds];
                if (substr($num, 1) != '00') {
                    $ret .= $this->_sep;
                }

                $case = 3;
            }

            $tens = (int)$num{1};
            $ones = (int)$num{2};
            if ($tens || $ones) {
                if ($tens == 1 && $ones == 0) {
                    $ret .= 'äåñÿòü';
                } elseif ($tens == 1) {
                    $ret .= $this->_teens[$ones+10];
                } else {
                    if ($tens > 0) {
                        $ret .= $this->_tens[(int)$tens];
                    }

                    if ($ones > 0) {
                        $ret .= $this->_sep
                                . $this->_digits[$gender][$ones];

                        if ($ones == 1) {
                            $case = 1;
                        } elseif ($ones < 5) {
                            $case = 2;
                        } else {
                            $case = 3;
                        }
                    }
                }
            }
        }

        return $ret;
    }
    // }}}
    // {{{ toCurrencyWords()

    /**
     * Converts a currency value to its word representation
     * (with monetary units) in Russian language
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
     * @author Andrey Demenev <demenev@on-line.jar.ru>
     */
    function toCurrencyWords($int_curr, $decimal, $fraction = false, $convert_fraction = true)
    {
        $int_curr = strtoupper($int_curr);
        if (!isset($this->_currency_names[$int_curr])) {
            $int_curr = $this->def_currency;
        }

        $curr_names = $this->_currency_names[$int_curr];

        $ret  = trim($this->_toWordsWithCase($decimal, $case, $curr_names[0][0]));
        $ret .= $this->_sep . $curr_names[0][$case];

        if ($fraction !== false) {
            if ($convert_fraction) {
                $ret .= $this->_sep . trim($this->_toWordsWithCase($fraction, $case, $curr_names[1][0]));
            } else {
                $ret .= $this->_sep . $fraction;
            }

            $ret .= $this->_sep . $curr_names[1][$case];
        }
        return $ret;
    }
    // }}}

}
