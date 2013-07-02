<?php
/* Copyright (C) 2011-2012	   Juanjo Menent   	   <jmenent@2byte.es>
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
 */

//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1'); // Uncomment creates pb to relogon after a disconnect
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');

$res=@include("../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/class/cash.class.php');

// This destroy tag that say "Point of Sale session is on".

global $db;

$terminal = new Cash($db);
$terminal->fetch($_SESSION['TERMINAL_ID']);
$terminal->set_unused($user);		
unset($_SESSION['uid']);
unset($_SESSION['TERMINAL_ID']);

header ('Location: '.dol_buildpath('/pos/frontend/index.php',1));
exit;

?>