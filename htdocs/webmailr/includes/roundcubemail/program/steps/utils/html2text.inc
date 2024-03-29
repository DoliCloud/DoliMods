<?php
/*

 +-----------------------------------------------------------------------+
 | program/steps/utils/html2text.inc                                     |
 |                                                                       |
 | This file is part of the RoundCube Webmail client                     |
 | Copyright (C) 2005-2010, RoundCube Dev. - Switzerland                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Convert HTML message to plain text                                  |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: html2text.inc,v 1.1 2011/08/01 19:22:16 eldy Exp $

*/

$converter = new html2text($HTTP_RAW_POST_DATA);

header('Content-Type: text/plain; charset=UTF-8');
print trim($converter->get_text());
exit;


