<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/utils/spell_pspell.inc                                  |
 |                                                                       |
 | This file is part of the RoundCube Webmail client                     |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Use the Pspell extension to check spelling, returns results         |
 |   compatible with spell_googie.inc.                                   |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Kris Steinhoff <steinhof@umich.edu>                           |
 +-----------------------------------------------------------------------+

 $Id: spell_pspell.inc,v 1.1 2011/08/01 19:22:16 eldy Exp $

*/

if (!extension_loaded('pspell')) {
    raise_error(array(
      'code' => 500,
      'type' => 'php',
      'file' => __FILE__, 'line' => __LINE__,
      'message' => "Pspell extension not available"), true, false);

    header('HTTP/1.1 404 Not Found');
    exit;
}

// read input
$data = file_get_contents('php://input');

// parse data (simplexml_load_string breaks CRLFs)
$left = strpos($data, '<text>');
$right = strrpos($data, '</text>');
$text = substr($data, $left+6, $right-($left+6));
$text = html_entity_decode($text, ENT_QUOTES, RCMAIL_CHARSET);

// tokenize
$words = preg_split('/[ !"#$%&()*+\\,\/\n:;<=>?@\[\]^_{|}-]+|\.[^\w]/', $text, NULL,  PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE );

// init spellchecker
$plink = pspell_new(get_input_value('lang', RCUBE_INPUT_GET), null, null, RCMAIL_CHARSET, PSPELL_FAST);

// send output
$out = '<?xml version="1.0" encoding="'.RCMAIL_CHARSET.'"?><spellresult charschecked="'.mb_strlen($text).'">';

$diff = 0;
foreach ($words as $w) {
    $word = trim($w[0]);
    $pos  = $w[1] - $diff;
    $len  = mb_strlen($word);
    if ($word && $plink && preg_match('/[^0-9\.]/', $word)
	&& !pspell_check($plink, $word)) {
        $suggestions = pspell_suggest($plink, $word);
	if (sizeof($suggestions)>MAX_SUGGESTIONS)
	  $suggestions = array_slice($suggestions, 0, MAX_SUGGESTIONS);

        $out .= '<c o="'.$pos.'" l="'.$len.'">';
        $out .= implode("\t", $suggestions);
        $out .= '</c>';
    }
    $diff += (strlen($word) - $len);
}

$out .= '</spellresult>';

header("Content-Type: text/xml; charset=".RCMAIL_CHARSET);
echo $out;
exit;


