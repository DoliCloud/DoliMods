<?php
/* Copyright (C) 2011 Regis Houssin  <regis@dolibarr.fr>
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

/**
 *  \file       /google/test.php
 *  \ingroup    google
 *  \brief      Page to google api
 *  \version    $Id: test.php,v 1.1 2011/06/14 15:44:28 hregis Exp $
 */

$res=@include("../../main.inc.php");								// For "custom" directory
if (! $res) $res=@include("../main.inc.php");						// For root directory
if (! $res) @include("../../../../dolibarr/htdocs/main.inc.php");	// Used on dev env only

require_once('./lib/google_calendar.lib.php');

llxheader();

$user = 'xxxxxx@gmail.com';
$pwd = 'xxxxxx';

$client = getClientLoginHttpClient($user, $pwd);

//outputCalendarList($client);

outputCalendar($client);


llxfooter();

?>