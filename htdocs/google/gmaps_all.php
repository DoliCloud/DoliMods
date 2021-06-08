<?php
/* Copyright (C) 2011-2016 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *       \file       htdocs/google/gmaps_all.php
 *       \ingroup    google
 *       \brief      Page to show a map
 *       \author     Laurent Destailleur
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/company.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/contact.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/member.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php";
require_once DOL_DOCUMENT_ROOT."/contact/class/contact.class.php";
dol_include_once("/google/class/googlemaps.class.php");
dol_include_once("/google/includes/GoogleMapAPIv3.class.php");

$langs->loadLangs(array("google@google", "categories"));

// url is:  gmaps.php?mode=thirdparty|contact|member&id=id&max=max


$mode=GETPOST('mode', 'aZ09');
$id = GETPOST('id', 'int');
$MAXADDRESS=GETPOST('max', 'int')?GETPOST('max', 'int'):'25';	// Set packet size to 25 if no forced from url
$address='';
$socid = GETPOST('socid', 'int');

$search_sale=empty($conf->global->GOOGLE_MAPS_FORCE_FILTER_BY_SALE_REPRESENTATIVES)?GETPOST('search_sale'):-1;
$search_tag_customer=GETPOST('search_tag_customer');
$search_tag_supplier=GETPOST('search_tag_supplier');
$search_departement = GETPOST("state_id", "int");
$search_customer = GETPOST('search_customer', 'alpha');
$search_supplier = GETPOST('search_supplier', 'alpha');
$search_status = GETPOST('search_status', 'alpha');

if ($search_status == '') $search_status = '1';

// Load third party
if (empty($mode) || $mode=='thirdparty') {
	include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
	if ($id > 0) {
		$object = new Societe($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1, ', ');
		$url = $object->url;
	}
} elseif ($mode=='contact') {
	include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
	if ($id > 0) {
		$object = new Contact($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1, ', ');
		$url = '';
	}
} elseif ($mode=='member') {
	include_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';
	if ($id > 0) {
		$object = new Adherent($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1, ', ');
		$url = '';
	}
} elseif ($mode=='patient') {
	dol_include_once('/cabinetmed/class/patient.class.php');
	if ($id > 0) {
		$object = new Patient($db);
		$object->id = $id;
		$object->fetch($id);
		$address = $object->getFullAddress(1, ', ');
		$url = '';
	}
} else {
	dol_print_error('', 'Bad value for mode param into url');
	exit;
}


/*
 * View
 */

// Increase limit of time. Works only if we are not in safe mode
$ExecTimeLimit = 600;	// Set it to 0 to not use a forced time limit
if (!empty($ExecTimeLimit)) {
	$err = error_reporting();
	error_reporting(0); // Disable all errors
	//error_reporting(E_ALL);
	@set_time_limit($ExecTimeLimit); // Need more than 240 on Windows 7/64
	error_reporting($err);
}
$MemoryLimit = 0;
if (!empty($MemoryLimit)) {
	@ini_set('memory_limit', $MemoryLimit);
}

$countrytable="c_pays";
$countrylabelfield='libelle';
include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
if (versioncompare(versiondolibarrarray(), array(3,7,-3)) >= 0) {
	$countrytable="c_country";
	$countrylabelfield='label';
}

llxheader();

$form=new Form($db);
$formother = new FormOther($db);
$formcompany = new FormCompany($db);

//On fabrique les onglets
$head=array();
$title='';
$picto='';
$type='';
if (empty($mode) || $mode=='thirdparty') {
	if ($user->societe_id) $socid=$user->societe_id;

	$title=$langs->trans("MapOfThirdparties");
	$picto='company';
	$type='company';
	$sql="SELECT s.rowid as id, s.nom as name, s.address, s.zip, s.town, s.url, s.email, s.phone, s.client as client, s.fk_stcomm as statusprospet,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.".$countrylabelfield." as country,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label, g.tms";
	$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.$countrytable." as c ON s.fk_pays = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	if ($search_sale || (!$user->rights->societe->client->voir && ! $socid)) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	if ($search_departement != '' && $search_departement > 0) $sql.= ", ".MAIN_DB_PREFIX."c_departements as dp";
	$sql.= " WHERE s.entity IN (".getEntity('societe', 1).")";
	if ($search_sale == -1 || (! $user->rights->societe->client->voir && ! $socid))	$sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
	if ($search_sale > 0)          $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$search_sale;
	if ($search_departement != '' && $search_departement > 0) $sql.= " AND s.fk_departement = dp.rowid AND dp.rowid = ".$db->escape($search_departement);
	if ($socid) $sql.= " AND s.rowid = ".$socid;	// protect for external user
	if ($search_tag_customer > 0)           $sql.= " AND s.rowid IN (SELECT fk_soc FROM ".MAIN_DB_PREFIX."categorie_societe as cs WHERE fk_categorie = ".$db->escape($search_tag_customer).")";
	if ($search_tag_supplier > 0)           $sql.= " AND s.rowid IN (SELECT fk_soc FROM ".MAIN_DB_PREFIX."categorie_fournisseur as cs WHERE fk_categorie = ".$db->escape($search_tag_supplier).")";
	if ($search_customer != '' && $search_customer != '-1') {
		$filterclient = '1,2,3';
		if ($search_customer == 2) $filterclient= '2,3';
		if ($search_customer == 1) $filterclient= '1,3';
		$sql.= " AND s.client IN (".$filterclient.")";
	}
	if ($search_supplier != '' && $search_supplier != '-1')               $sql.= " AND s.fournisseur IN (".$db->escape($search_supplier).")";
	if ($search_status != '' && $search_status != '-1') $sql.= " AND s.status = ".(int) $search_status;

	$sql.= " ORDER BY g.tms ASC, s.rowid ASC";
	//print $search_sale.'-'.$sql;
} elseif ($mode=='contact') {
	$title=$langs->trans("MapOfContactsAddresses");
	$picto='contact';
	$type='contact';
	$sql="SELECT s.rowid as id, s.lastname, s.firstname, s.address, s.zip, s.town, '' as url, s.email, s.phone,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.".$countrylabelfield." as country,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label, g.tms";
	$sql.= " FROM ".MAIN_DB_PREFIX."socpeople as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.$countrytable." as c ON s.fk_pays = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	$sql.= " WHERE s.entity IN (".getEntity('societe', 1).")";
	$sql.= " ORDER BY g.tms ASC, s.rowid ASC";
} elseif ($mode=='member') {
	$search_tag_member=GETPOST('search_tag_member');

	$title=$langs->trans("MapOfMembers");
	$picto='user';
	$type='member';
	$sql="SELECT s.rowid as id, s.lastname, s.firstname, s.address, s.zip, s.town, '' as url, s.email, s.phone, s.phone_perso, s.phone_mobile, s.societe,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.".$countrylabelfield." as country,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label, g.tms";
	$sql.= " FROM ".MAIN_DB_PREFIX."adherent as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.$countrytable." as c ON s.country = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	$sql.= " WHERE s.statut = 1";
	$sql.= " AND s.entity IN (".getEntity('adherent', 1).")";
	if ($search_tag_member > 0)           $sql.= " AND s.rowid IN (SELECT fk_member FROM ".MAIN_DB_PREFIX."categorie_member as cs WHERE fk_categorie = ".$db->escape($search_tag_member).")";
	$sql.= " ORDER BY g.tms ASC, s.rowid ASC";
} elseif ($mode=='patient') {
	$search_sale=empty($conf->global->GOOGLE_MAPS_FORCE_FILTER_BY_SALE_REPRESENTATIVES)?GETPOST('search_sale'):-1;
	$search_tag_customer=GETPOST('search_tag_customer');
	$search_tag_supplier=GETPOST('search_tag_supplier');
	$search_departement = GETPOST("state_id", "int");

	$title=$langs->trans("MapOfPatients");
	$picto='user';
	$type='patient';
	$sql="SELECT s.rowid as id, s.nom as name, s.address, s.zip, s.town,";
	$sql.= " c.rowid as country_id, c.code as country_code, c.".$countrylabelfield." as country, s.url, s.phone, s.email,";
	$sql.= " g.rowid as gid, g.fk_object, g.latitude, g.longitude, g.address as gaddress, g.result_code, g.result_label, g.tms";
	$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.$countrytable." as c ON s.fk_pays = c.rowid";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."google_maps as g ON s.rowid = g.fk_object and g.type_object='".$type."'";
	if ($search_sale || (!$user->rights->societe->client->voir && ! $socid)) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
	if ($search_departement != '' && $search_departement > 0) $sql.= ", ".MAIN_DB_PREFIX."c_departements as dp";
	$sql.= " WHERE s.canvas='patient@cabinetmed'";
	$sql.= " AND s.entity IN (".getEntity('societe', 1).")";
	if ($search_sale == -1 || (! $user->rights->societe->client->voir && ! $socid))	$sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
	if ($search_sale > 0)          $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$search_sale;
	if ($search_departement != '' && $search_departement > 0) $sql.= " AND s.fk_departement = dp.rowid AND dp.rowid = ".$db->escape($search_departement);
	if ($socid) $sql.= " AND s.rowid = ".$socid;	// protect for external user
	if ($search_tag_customer > 0)           $sql.= " AND s.rowid IN (SELECT fk_soc FROM ".MAIN_DB_PREFIX."categorie_societe as cs WHERE fk_categorie = ".$search_tag_customer.")";
	if ($search_tag_supplier > 0)           $sql.= " AND s.rowid IN (SELECT fk_soc FROM ".MAIN_DB_PREFIX."categorie_fournisseur as cs WHERE fk_categorie = ".$search_tag_supplier.")";
	$sql.= " ORDER BY g.tms ASC, s.rowid ASC";
}
//print $sql;

print_fiche_titre($title, '', '');

dol_fiche_head(array(), 'gmaps', '', 0);


// If the user can view prospects other than his'
if ($user->rights->societe->client->voir && empty($socid)) {
	if (empty($mode) || $mode=='thirdparty' || $mode=='patient' || $mode == 'member') {
		$langs->loadLangs(array("commercial", "companies"));

		print '<form name="formsearch" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="mode" value="'.$mode.'">';

		if ($mode != 'member' && (empty($conf->global->SOCIETE_DISABLE_CUSTOMERS) || empty($conf->global->SOCIETE_DISABLE_PROSPECTS))) {
			print $langs->trans('ProspectCustomer'). ' : ';

			$selected=$search_customer;
			print '<div class="divsearchfield">';
			print '<select class="flat" name="search_customer" id="customerprospect">';
			print '<option value="-1">&nbsp;</option>';
			if (empty($conf->global->SOCIETE_DISABLE_PROSPECTS)) print '<option value="2"'.($selected==2?' selected':'').'>'.$langs->trans('Prospect').'</option>';
			if (empty($conf->global->SOCIETE_DISABLE_CUSTOMERS)) print '<option value="1"'.($selected==1?' selected':'').'>'.$langs->trans('Customer').'</option>';
			print '</select>';
			print '</div>';

			if (! empty($conf->fournisseur->enabled) && ! empty($user->rights->fournisseur->lire)) {
				print '<div class="divsearchfield">'.$langs->trans('Supplier').' : ';
				print $form->selectyesno("search_supplier", $search_supplier, 1, false, 1);
				print '</div>';
			}

			// Status
			print '<div class="divsearchfield">'.$form->editfieldkey('Status', 'search_status', '', $object, 0).' ';
			print $form->selectarray('search_status', array('0'=>$langs->trans('ActivityCeased'), '1'=>$langs->trans('InActivity')), $search_status, 1, 0, 0, '', 0, 0, 0, '', 'maxwidth250');
			print '</div>';
		}

		if ($mode != 'member') {
			print img_picto($langs->trans("ThirdPartiesOfSaleRepresentative"), 'company', 'class="paddingrightonly"');
			print $formother->select_salesrepresentatives($search_sale, 'search_sale', $user, 0, $langs->trans('ThirdPartiesOfSaleRepresentative'), 'maxwidth250');

			if (! empty($conf->global->GOOGLE_MAPS_SEARCH_ON_STATE)) {
				print '<div class="divsearchfield">';
				print $langs->trans("State").': ';
				print $formcompany->select_state($search_departement, 0, 'state_id');
				print '</div>';
			}

			print '<div class="divsearchfield">';
			print img_picto($langs->trans("CustomersCategoriesShort"), 'category', 'class="paddingrightonly"');
			print $formother->select_categories(2, $search_tag_customer, 'search_tag_customer', 0, $langs->trans("CustomersCategoriesShort"), 'maxwidth250');
			print '</div>';

			if (empty($mode) || $mode=='thirdparty') {
				print '<div class="divsearchfield">';
				print img_picto($langs->trans("SuppliersCategoriesShort"), 'category', 'class="paddingrightonly"');
				print $formother->select_categories(1, $search_tag_supplier, 'search_tag_supplier', 0, $langs->trans("SuppliersCategoriesShort"), 'maxwidth250');
				print '</div>';
			}
		} else {
			print '<div class="divsearchfield">';
			print img_picto($langs->trans("MembersCategoriesShort"), 'category', 'class="paddingrightonly"');
			print $formother->select_categories(3, $search_tag_member, 'search_tag_member', 0, $langs->trans("MembersCategoriesShort"), 'maxwidth250');
			print '</div>';
		}

		print '<input type="submit" name="submit_search_sale" value="'.$langs->trans("Search").'" class="button"> &nbsp; &nbsp; &nbsp; ';
		print '</form>';
	}
}


// Fill array of contacts
$addresses = array();
$adderrors = array();
$googlemaps = new Googlemaps($db);
$countgeoencoding=0;
$countgeoencodedok=0;
$countgeoencodedall=0;

// Loop
dol_syslog("Search addresses sql=".$sql);
$resql=$db->query($sql);
if ($resql) {
	$num=$db->num_rows($resql);
	$i=0;
	while ($i < $num) {
		$obj=$db->fetch_object($resql);
		if (empty($obj->country_code)) $obj->country_code=$mysoc->country_code;

		$error='';

		$addresstosearch=dol_format_address($obj, 1, " ");
		$address=dol_format_address($obj, 1, ", ");	// address to show

		$object=new stdClass();
		$object->id=$obj->id;
		$object->name=($obj->name?$obj->name:($obj->societe?$obj->societe:($obj->lastname.' '.$obj->firstname)));
		$object->latitude = $obj->latitude;
		$object->longitude = $obj->longitude;
		$object->address = $address;
		$object->url = $obj->url;
		$object->email = $obj->email;
		$object->phone = $obj->phone;
		$object->client = $obj->client;
		$object->statusprospet = $obj->statusprospet;

		$geoencodingtosearch=false;
		if ($obj->gaddress != $addresstosearch) $geoencodingtosearch=true;
		elseif ((empty($object->latitude) || empty($object->longitude))
			&& (empty($obj->result_code) || in_array($obj->result_code, array('OK','OVER_QUERY_LIMIT','REQUEST_DENIED')))) $geoencodingtosearch=true;

		if ($geoencodingtosearch && (empty($MAXADDRESS) || $countgeoencoding < $MAXADDRESS)) {
			// Google limit usage of API to 5 requests per second
			if ($countgeoencoding && ($countgeoencoding % 5 == 0)) {
				dol_syslog("Add a delay of 1");
				sleep(1);
			}

			$countgeoencoding++;

			$point = geocoding($addresstosearch);

			if (! is_array($point) && $point == 'ZERO_RESULTS') {
				// Try with a degraded address (if address is only a zip or "lieu-dit")
				$degradedaddresstosearch = dol_format_address($obj, 1, " ", '', true);

				$object->result_on_degraded_address = 1;
				$point = geocoding($degradedaddresstosearch);
			}

			if (is_array($point)) {
				$object->latitude=$point['lat'];
				$object->longitude=$point['lng'];

				// Update/insert database
				$googlemaps->id=$obj->gid;
				$googlemaps->latitude=$object->latitude;
				$googlemaps->longitude=$object->longitude;
				$googlemaps->address=$addresstosearch;
				$googlemaps->fk_object=$obj->id;
				$googlemaps->type_object=$type;
				$googlemaps->result_code='OK';
				$googlemaps->result_label='';

				if ($googlemaps->id > 0) $result=$googlemaps->update();
				else $result=$googlemaps->create($user);
				if ($result < 0) dol_print_error('', $googlemaps->error);

				$countgeoencodedok++;
				$countgeoencodedall++;
			} else {
				$error=$point;

				// Update/insert database
				$googlemaps->id=$obj->gid;
				$googlemaps->latitude=$object->latitude;
				$googlemaps->longitude=$object->longitude;
				$googlemaps->address=$addresstosearch;
				$googlemaps->fk_object=$obj->id;
				$googlemaps->type_object=$type;
				if ($error == 'ZERO_RESULTS') {
					$error='Address not complete or unknown';
					$googlemaps->result_code='ZERO_RESULTS';
					$googlemaps->result_label=$error;
				} elseif ($error == 'OVER_QUERY_LIMIT') {
					$error='Quota reached';
					$googlemaps->result_code='OVER_QUERY_LIMIT';
					$googlemaps->result_label=$error;
				} else {
					$googlemaps->result_code=$error;
					$googlemaps->result_label='Geoencoding failed: '.$error;
				}

				if ($googlemaps->id > 0) $result=$googlemaps->update();
				else $result=$googlemaps->create($user);
				if ($result < 0) dol_print_error('', $googlemaps->error);

				$object->error_code=$googlemaps->result_code;
				$object->error=$googlemaps->result_label;
				$adderrors[]=$object;

				$countgeoencodedall++;
			}
		} else {
			if ($obj->result_code == 'OK') {	// A success
				$countgeoencodedok++;
				$countgeoencodedall++;
			} elseif (! empty($obj->result_code)) {	// An error
				$error=$obj->result_label;
				$object->error_code=$obj->result_code;
				$object->error=$error;
				$adderrors[]=$object;

				$countgeoencodedall++;
			} else // No geoencoding done yet
			{
			}
		}

		if (! $error) {
			$addresses[]=$object;
		}

		$i++;
	}

	// Summary of data represented
	print '<div class="resultgeoencoding" style="padding-top: 8px;">';
	if ($num > $countgeoencodedall) print '<span class="opacitymedium hideonsmartphone">'.$langs->trans("OnlyXAddressesAmongYWereGeoencoded", $MAXADDRESS, $countgeoencodedok).'</span><br>'."\n";
	print $langs->trans("CountGeoTotal", $num, ($num-$countgeoencodedall), ($countgeoencodedall-$countgeoencodedok), $countgeoencodedok).'<br>'."\n";
	print '</div>';
	if ($num > $countgeoencodedall) {
		$param='';
		if ($search_customer != '' && $search_customer != '-1') $param.='&search_customer='.urlencode($search_customer);
		if ($search_supplier != '' && $search_supplier != '-1') $param.='&search_supplier='.urlencode($search_supplier);
		if ($search_sale != '' && $search_sale != '-1') $param.='&search_sale='.urlencode($search_sale);
		if ($search_tag_customer != '' && $search_tag_customer != '-1') $param.='&search_tag_customer='.urlencode($search_tag_customer);
		if ($search_tag_supplier != '' && $search_tag_supplier != '-1') $param.='&search_tag_supplier='.urlencode($search_tag_supplier);
		if ($search_departement != '' && $search_departement != '-1') $param.='&search_departement='.urlencode($search_departement);

		print $langs->trans("ClickHereToIncludeXMore").': &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=1'.$param.'">'.$langs->trans("By1").'</a> &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=25'.$param.'">'.$langs->trans("By25").'</a> &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=50'.$param.'">'.$langs->trans("By50").'</a> &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=100'.$param.'">'.$langs->trans("By100").'</a> &nbsp;';
		print ' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?mode='.$mode.'&max=250'.$param.'">'.$langs->trans("By250").'</a> &nbsp;';
		//,min($num-$countgeoencodedall,$MAXADDRESS)).'</a>';
		print '<br>';
	}
	print '<br>'."\n";
} else {
	dol_print_error($db);
}

$gmap = new GoogleMapAPI();
$gmap->setDivId('test1');
$gmap->setDirectionDivId('route');
$gmap->setEnableWindowZoom(true);
$gmap->setEnableAutomaticCenterZoom(true);
$gmap->setDisplayDirectionFields(false);
$gmap->setClusterer(empty($conf->global->GOOGLE_NOCLUSTERER));                  // For high number or record, we should use clusterer
$gmap->setSize('100%', '500px');
$gmap->setZoom(11);
$gmap->setLang($user->lang);
$gmap->setDefaultHideMarker(false);

// Convert array of addresses into the output gmap string
$gmap->addArrayMarker($addresses, $langs, $mode);


$gmap->generate();
echo $gmap->getGoogleMap();


dol_fiche_end();


// If no addresses
if (count($addresses) == 0 && count($adderrors) == 0) print $langs->trans("NoAddressDefined").'<br><br>';


// Show error
if (count($adderrors)) {
	if (empty($mode) || $mode=='thirdparty') $objectstatic=new Societe($db);
	elseif ($mode=='contact') $objectstatic=new Contact($db);
	elseif ($mode=='member') $objectstatic=new Adherent($db);
	elseif ($mode=='patient') $objectstatic=new Patient($db);

	print $langs->trans("FollowingAddressCantBeLocalized", ($countgeoencodedall-$countgeoencodedok)).':<br>'."\n";
	foreach ($adderrors as $object) {
		$objectstatic->id=$object->id;
		$objectstatic->name=$object->name;	// Here $object is an array an 'name' is already a formatted string with firstname and lastname
		$objectstatic->ref=$object->name;	// Here $object is an array an 'name' is already a formatted string with firstname and lastname
		print $langs->trans("Name").": ".$objectstatic->getNomUrl(1).", ".$langs->trans("Address").": ".$object->address." -> ".$object->error." (error code = ".$object->error_code.")<br>\n";
	}
}


llxfooter();

$db->close();



/**
 * Geocoding an address (address -> lat,lng)
 * Use API v3.
 * See API doc: https://developers.google.com/maps/documentation/geocoding/#api_key
 * To create a key:
 * Visit the APIs console at https://code.google.com/apis/console and log in with your Google Account.
 * Click "Enable an API" or the Services link from the left-hand menu in the APIs Console, then activate the Geocoding API service.
 * Once the service has been activated, your API key is available from the API Access page, in the Simple API Access section. Geocoding API applications use the Key for server apps.
 *
 * @param 	string 	$address 	An address
 * @return 	mixed				Array(lat, lng) if OK, error message string if KO
 */
function geocoding($address)
{
	global $conf;

	$encodeAddress = urlencode(withoutSpecialChars($address));
	// URL to geoencode
	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$encodeAddress;
	if (! empty($conf->global->GOOGLE_API_SERVERKEY)) $url.="&key=".$conf->global->GOOGLE_API_SERVERKEY;

	ini_set("allow_url_open", "1");
	$response = googlegetURLContent($url, 'GET');

	if ($response['curl_error_no']) {
		$returnstring=$response['curl_error_no'].' '.$response['curl_error_msg'];
		echo "<!-- geocoding : failure to geocode : ".dol_escape_htmltag($encodeAddress)." => " . dol_escape_htmltag($returnstring) . " -->\n";
		return $returnstring;
	}

	$data = json_decode($response['content']);
	//$data = json_decode($response['content'], false, 0, JSON_BIGINT_AS_STRING);
	if ($data->status == "OK") {
		$return=array();
		$return['lat']=$data->results[0]->geometry->location->lat;
		$return['lng']=$data->results[0]->geometry->location->lng;
		return $return;
	} elseif (in_array($data->status, array("OVER_QUERY_LIMIT", "ZERO_RESULTS", "INVALID_RESULT", "REQUEST_DENIED"))) {
		$returnstring=$data->status;
		echo "\n<!-- geocoding : called url : ".dol_escape_htmltag($url)." -->\n";
		echo "<!-- geocoding : failure to geocode : ".dol_escape_htmltag($encodeAddress)." => " . dol_escape_htmltag($returnstring) . " -->\n";
		return $returnstring;
	} else {
		$returnstring='Failed to json_decode result '.$response['content'];
		echo "\n<!-- geocoding : called url : ".dol_escape_htmltag($url)." -->\n";
		echo "<!-- geocoding : failure to geocode : ".dol_escape_htmltag($encodeAddress)." => " . dol_escape_htmltag($returnstring) . " -->\n";
		return $returnstring;
	}
}

/**
 * Remove accentued characters
 *
 * @param string $str		The string to treat
 * @param string $replaceBy	The replacement character
 * @return string
 */
function withoutSpecialChars($str, $replaceBy = '_')
{
	$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
	$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	return $str;
	/*
		$accents = "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ";
	$sansAccents = "AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy";
	$text = strtr($text, $accents, $sansAccents);
	$text = preg_replace('/([^.a-z0-9]+)/i', $replaceBy, $text);
	return $text;*/
}


/**
 * Function get content from an URL (use proxy if proxy defined)
 *
 * @param	string	$url 			URL to call.
 * @param	string	$postorget		'post' = POST, 'get='GET'
 * @param   string  $param          Params
 * @return	array					returns an associtive array containing the response from the server.
 */
function googlegetURLContent($url, $postorget = 'GET', $param = '')
{
	//declaring of global variables
	global $conf, $langs;
	$USE_PROXY=empty($conf->global->MAIN_PROXY_USE)?0:$conf->global->MAIN_PROXY_USE;
	$PROXY_HOST=empty($conf->global->MAIN_PROXY_HOST)?0:$conf->global->MAIN_PROXY_HOST;
	$PROXY_PORT=empty($conf->global->MAIN_PROXY_PORT)?0:$conf->global->MAIN_PROXY_PORT;
	$PROXY_USER=empty($conf->global->MAIN_PROXY_USER)?0:$conf->global->MAIN_PROXY_USER;
	$PROXY_PASS=empty($conf->global->MAIN_PROXY_PASS)?0:$conf->global->MAIN_PROXY_PASS;

	dol_syslog("getURLContent postorget=".$postorget." URL=".$url." param=".$param);

	//setting the curl parameters.
	$ch = curl_init();

	/*print $API_Endpoint."-".$API_version."-".$PAYPAL_API_USER."-".$PAYPAL_API_PASSWORD."-".$PAYPAL_API_SIGNATURE."<br>";
	 print $USE_PROXY."-".$gv_ApiErrorURL."<br>";
	print $nvpStr;
	exit;*/
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Dolibarr googlegeturlcontent function');

	// $conf->global->GOOGLE_SSLVERSION should be set to 1 to use TLSv1 by default or change to TLSv1.2 in module configuration
	if (isset($conf->global->GOOGLE_SSLVERSION)) curl_setopt($ch, CURLOPT_SSLVERSION, $conf->global->GOOGLE_SSLVERSION);

	//turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, empty($conf->global->MAIN_USE_CONNECT_TIMEOUT)?5:$conf->global->MAIN_USE_CONNECT_TIMEOUT);
	curl_setopt($ch, CURLOPT_TIMEOUT, empty($conf->global->MAIN_USE_RESPONSE_TIMEOUT)?5:$conf->global->MAIN_USE_RESPONSE_TIMEOUT);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($postorget == 'POST') curl_setopt($ch, CURLOPT_POST, 1);
	else curl_setopt($ch, CURLOPT_POST, 0);

	//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
	if ($USE_PROXY) {
		dol_syslog("getURLContent set proxy to ".$PROXY_HOST. ":" . $PROXY_PORT." - ".$PROXY_USER. ":" . $PROXY_PASS);
		//curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); // Curl 7.10
		curl_setopt($ch, CURLOPT_PROXY, $PROXY_HOST. ":" . $PROXY_PORT);
		if ($PROXY_USER) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $PROXY_USER. ":" . $PROXY_PASS);
	}

	//setting the nvpreq as POST FIELD to curl
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

	//getting response from server
	$response = curl_exec($ch);

	$rep=array();
	$rep['content']=$response;
	$rep['curl_error_no']='';
	$rep['curl_error_msg']='';

	//dol_syslog("getURLContent response=".$response);

	if (curl_errno($ch)) {
		// moving to display page to display curl errors
		$rep['curl_error_no']=curl_errno($ch);
		$rep['curl_error_msg']=curl_error($ch);

		dol_syslog("getURLContent curl_error array is ".join(',', $rep));
	} else {
		//closing the curl
		curl_close($ch);
	}

	return $rep;
}
