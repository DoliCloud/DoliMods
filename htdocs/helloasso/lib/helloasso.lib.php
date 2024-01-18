<?php
/* Copyright (C) 2023 Alice Adminson <myemail@mycompany.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    helloasso/lib/helloasso.lib.php
 * \ingroup helloasso
 * \brief   Library files with common functions for HelloAsso
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function helloassoAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("helloasso@helloasso");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/helloasso/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/helloasso/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/helloasso/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@helloasso:/helloasso/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@helloasso:/helloasso/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'helloasso@helloasso');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'helloasso@helloasso', 'remove');

	return $head;
}

/**
 * Connect to helloasso database
 *
 * @return array|int An array with the token_type and the access_token defined if OK or -1 if KO 
 */

function doConnectionHelloasso()
{
	global $langs, $conf;

	$result = array();

	$helloassourl = "api.helloasso.com";

	//Verify if Helloasso module is in test mode
	if (getDolGlobalInt("HELLOASSO_LIVE")) {
		$client_id = getDolGlobalString("HELLOASSO_CLIENT_ID");
		$client_id_secret = getDolGlobalString("HELLOASSO_CLIENT_SECRET");
	} else{
		$client_id = getDolGlobalString("HELLOASSO_TEST_CLIENT_ID");
		$client_id_secret = getDolGlobalString("HELLOASSO_TEST_CLIENT_SECRET");
		$helloassourl = "api.helloasso-sandbox.com";
	}

	$ret = getURLContent("https://".urlencode($helloassourl)."/oauth2/token", 'POST', 'grant_type=client_credentials&client_id='.$client_id.'&client_secret='.$client_id_secret, 1, array('content-type: application/x-www-form-urlencoded'));

	if ($ret["http_code"] == 200) {
		$jsondata = $ret["content"];
		$json = json_decode($jsondata);
		$result = array("token_type" => $json->token_type, "access_token" => $json->access_token);
	} else {
		$result = -1;
	}
	return $result;
}
