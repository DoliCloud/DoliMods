<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/utils/spell.inc                                         |
 |                                                                       |
 | This file is part of the RoundCube Webmail client                     |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Invoke the configured or default spell checking engine.             |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Kris Steinhoff <steinhof@umich.edu>                           |
 +-----------------------------------------------------------------------+

 $Id: spell.inc,v 1.1 2011/08/01 19:22:16 eldy Exp $

*/

// max. number of suggestions for one word
define('MAX_SUGGESTIONS', 10);

$tiny = !empty($_GET['tiny']) ? 'html_' : '';

if ($spell_engine = $RCMAIL->config->get('spellcheck_engine', 'googie')) {
    include('spell_'.$tiny.$spell_engine.'.inc');
}

header('HTTP/1.1 404 Not Found');
exit;

