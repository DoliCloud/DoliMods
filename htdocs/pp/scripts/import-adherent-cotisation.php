#!/usr/bin/php
<?php
/* Copyright (C) 2013 Laurent Destailleur <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 *
 * Import des adhérents ou cotisation depuis extract SQL
 * Example: C:\Program Files\wamp\logs>"C:\Program Files\wamp\bin\php\php5.2.9-2\php.exe" C:\dev_MTD\workspaces\WorkspaceDolibarr\nltechno\scripts\partipirate\import-adherent-cotisation.php
 */

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
	echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
	exit;
}

// Global variables
$version='1.0';
$error=0;

// Include Dolibarr environment
$res=0;
if (! $res && file_exists($path."../../master.inc.php")) $res=@include $path."../../master.inc.php";
if (! $res && file_exists($path."../../htdocs/master.inc.php")) $res=@include $path."../../htdocs/master.inc.php";
if (! $res && file_exists("../master.inc.php")) $res=@include "../master.inc.php";
if (! $res && file_exists("../../master.inc.php")) $res=@include "../../master.inc.php";
if (! $res && file_exists("../../../master.inc.php")) $res=@include "../../../master.inc.php";
if (! $res && preg_match('/\/nltechno([^\/]*)\//', $_SERVER["PHP_SELF"], $reg)) $res=@include $path."../../../dolibarr".$reg[1]."/htdocs/master.inc.php"; // Used on dev env only
if (! $res && preg_match('/\/nltechno([^\/]*)\//', $_SERVER["PHP_SELF"], $reg)) $res=@include "../../../dolibarr".$reg[1]."/htdocs/master.inc.php"; // Used on dev env only
if (! $res) die("Failed to include master.inc.php file\n");
require_once DOL_DOCUMENT_ROOT ."/adherents/class/adherent.class.php";
require_once DOL_DOCUMENT_ROOT ."/adherents/class/cotisation.class.php";
require_once DOL_DOCUMENT_ROOT ."/core/lib/company.lib.php";

error_reporting(E_ALL);


/*
 *	MAIN
 */

$langs->load("main");
$memberstatic=new Adherent($db);
$subscriptionstatic=new Cotisation($db);

$mode = isset($argv[1])?$argv[1]:'';
$userid = isset($argv[2])?$argv[2]:'';
$membertype = isset($argv[3])?$argv[3]:'';
$file = isset($argv[4])?$argv[4]:'';
if (strlen(trim($file)) == 0 || strlen(trim($userid)) == 0) {
	print "Usage:  php import-adherent-cotisation.php [test|confirm] <login_dolibarr> <membertyperef> <filename>\n";
	exit;
}

// Load object user
$user = new User($db);
$result = $user->fetch('', $userid);
if ($user->id == 0) {
	print "Identifiant utilisateur Dolibarr incorrect : $userid\n";
	exit;
}

// Open input file
$filehandle=fopen($file, 'r');
if (empty($filehandle)) {
	print 'Failed to open file '.$file."\n";
	exit;
}

$now=dol_now();
$nowstring=dol_print_date($now, 'dayhourrfc');
print "Import fichier ".$file." - mode ".$mode." - ".$nowstring."\n";

$db->begin();

$nbline=0;
$nbignored=0;
$nbmemberadded=0;
$nbmemberupdated=0;
$nbmemberfailed=0;
$nbsubadded=0;
$nbsubupdated=0;
$nbsubfailed=0;
while (($buffer = fgets($filehandle, 4096)) !== false) {
	$error=0;

	$nbline++;
	print "Process line ".$nbline.": ";
	if ($nbline == 1) {
		print "Title of fields. Ignored.\n";
		$nbignored++;
		continue;
	}

	$fields=explode(';', $buffer);
	if (count($fields) < 10) {
		print "Not correct number of fields on this line\n";
		$nbignored++;
		continue;
	}

	$typepayment=array('paiement en_ligne'=>'CB');

	$datepaiement=$fields[0];
	$member_ref_ext=$fields[1];
	$sub_ref_ext=$fields[2];
	$sub_status=$fields[3];
	$reference=$fields[7];
	$lastname=$fields[9];
	$firstname=$fields[10];
	$address=$fields[11];
	$zip=$fields[12];
	$town=$fields[13];
	$country=($fields[14]!="NULL"?$fields[14]:'');
	$address2=$fields[15];
	$zip2=$fields[16];
	$town2=$fields[17];
	$country2=($fields[18]!="NULL"?$fields[18]:'');
	$email=$fields[19];
	$pseudoforum=$fields[20];
	$inscritforum=$fields[21];
	$majeur=$fields[22];
	$phone=$fields[23];
	$renew=$fields[24];
	//MLgenerale;MLdiscussion;MLconsultations;MLCRconseils;AdhesionSL;AdhesionSL_nom;
	//PayementType;APAYER_referenceBancaire;bordereau;montantCotisation;commentaires;IdentiteVerif;IdentiteVerifNote;APAYER_montantDon;adhesion_reference;APAYER_status3DS;APAYER_vld;identifiantClePGP;urlClePGP;accepteRiStatut;declarationHonneur;optinStat;dateCreation
	$paymenttype_label=$fields[31];
	$paymenttype_code=$typepayment[$paymenttype_label];
	$paymentamount=$fields[34];
	$comment=$fields[35];
	$idpgp=$fields[42];
	$urlpgp=$fields[43];
	$declahonneur=$fields[44];
	$option=$fields[45];
	$datecrea=dol_stringtotime($fields[46], 1);

	$member_name=dolGetFirstLastname($firstname, $lastname);
	print $member_name." (".$buffer.")\n";

	// Check parameters
	if (empty($paymenttype_code)) {
		print 'Do not understand field "'.$paymenttype_label.'" as payment type'."\n";
		$nbmemberfailed++;
		continue;
	}

	$memberfound=0;
	$subfound=0;

	// Search member
	$memberstatic->id=0;
	$res=$memberstatic->fetch(0, '', '', $member_ref_ext);
	if ($res < 0) $memberstatic->fetch_name($firstname, $lastname);

	if ($memberstatic->id) {
		$memberfound=1;

		// Update member
		$memberstatic->xx='ee';
		$memberstatic->email=$email;
		$memberstatic->note=$comment;
		$memberstatic->address=$address;
		$memberstatic->zip=$zip;
		$memberstatic->town=$town;
		$tmparray=getCountry('', 'all', $db, '', 0, ($country?$country:$country2));
		$memberstatic->country_code=$tmparray['code'];
		$memberstatic->country_id=$tmparray['id'];

		$res=$memberstatic->update($user);
		if ($res >= 0) { $error++; $nbmemberupdated++; print "Record update success\n"; } else { $error++; $nbmemberfailed++; print "Record update failed: ".$memberstatic->errorsToString()."\n"; }
	} else {
		// Add warning if renew was checked
		if ($renew) {
			print "Warning: Line should be a renew but member ".$member_name." was not found\n";
		}

		// Add member
		$memberstatic->ref_ext=$member_ref_ext;
		$memberstatic->lastname=$lastname;
		$memberstatic->firstname=$firstname;
		$memberstatic->address=$address;
		$memberstatic->zip=$zip;
		$memberstatic->town=$town;
		$tmparray=getCountry('', 'all', $db, '', 0, ($country?$country:$country2));
		$memberstatic->country_code=$tmparray['code'];
		$memberstatic->country_id=$tmparray['id'];
		$memberstatic->zip=$zip;
		$memberstatic->email=$email;
		$memberstatic->import_key=$nowstring;
		$memberstatic->note=$comment;
		$memberstatic->datec=$datecrea;
		$memberstatic->typeid=$membertype;
		$res=$memberstatic->create($user);
		if ($res >= 0) { $error++; $nbmemberadded++; print "Record creation success\n"; } else { $error++; $nbmemberfailed++; print "Record creation failed: ".$memberstatic->errorsToString()."\n"; }
	}

	if (! $error && preg_match('/accept/', $sub_status)) {
		if ($memberfound) {
			$subscriptions=$memberstatic->fetch_subscriptions();
			foreach ($subscriptions as $val) {
				if ($val->dateh == $datecrea) $subfound++;
			}
		}

		// Add subscription
		if (! $subfound) {
			$subscriptionstatic->datec=$datepaiement;
			$subscriptionstatic->fk_adherent=$memberstatic->id;
			$subscriptionstatic->amount=$paymentamount;
		}
	}
}
if (!feof($filehandle)) {
	echo "Erreur: fgets() a échoué\n";
}

// Close input file
fclose($filehandle);

if ($mode == 'confirm') $db->commit();
else $db->rollback();


print 'Resume: '.$nbline." lines, ".$nbignored." ignored\n";
print "Adherents: ".$nbmemberadded." added, ".$nbmemberupdated." updated, ".$nbmemberfailed." failed\n";
print "Adhesions: ".$nbmemberadded." added, ".$nbmemberupdated." updated, ".$nbmemberfailed." failed\n";
