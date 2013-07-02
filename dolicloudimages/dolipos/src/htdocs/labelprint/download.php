<?php
/* Copyright (C) 2012		Juanjo Menent <jmenent@2byte.es>
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
*/

/**
 *	\file       htdocs/labelprint/download.php
 *	\ingroup    labelprint
 *	\brief      Page to get the list of labels to print
 */

$res=@include("../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../main.inc.php");                   // For "custom" directory

$file = GETPOST('file','alpha');

$gestor = fopen($file, "r");
$buf = fread($gestor, filesize($file));
fclose($gestor);

$len = strlen($buf);

header("Content-Type: application/pdf");
header("Content-Length: ".$len);
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");		
header('Content-Disposition: inline; filename=labels.pdf');

print($buf);
unlink($file);
?>