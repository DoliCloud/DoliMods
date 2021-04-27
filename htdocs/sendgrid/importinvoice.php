<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-Francois FERRY  <jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 */

/**
 *   \file       htdocs/sendgrid/importsendgridinvoice.php
 *	 \ingroup    sendgrid
 *	 \brief		 Page to import SendGrid invoices
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

require_once DOL_DOCUMENT_ROOT."/user/class/user.class.php";
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/geturl.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";


$langs->loadLangs(array("bills", "orders", "sendgrid@sendgrid"));

//$url_pdf="https://www.sendgrid.com/cgi-bin/order/facture.pdf";

$endpoint = empty($conf->global->SENDGRID_ENDPOINT)?'https://api.sendgrid.com/v3/billing':$conf->global->SENDGRID_ENDPOINT;


$action=GETPOST('action', 'aZ09');
$excludenullinvoice=GETPOST('excludenullinvoice');
//$idsendgridsupplier=GETPOST('idsendgridsupplier');
$idsendgridsupplier=empty($conf->global->SENDGRID_THIRDPARTY_IMPORT)?'':$conf->global->SENDGRID_THIRDPARTY_IMPORT;

$sendgridthirdparty=new Societe($db);
if ($idsendgridsupplier) $result=$sendgridthirdparty->fetch($idsendgridsupplier);

$fuser = $user;

$now = dol_now();
$datefrom = dol_mktime(0, 0, 0, GETPOST('datefrommonth'), GETPOST('datefromday'), GETPOST('datefromyear'));
if (! $datefrom) $datefrom = dol_time_plus_duree($now, -6, 'm');

// Init client if we must do an action (list invoice or import it)
if (! empty($action)) {
	try {
		require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

		$params=getSoapParams();
		ini_set('default_socket_timeout', $params['response_timeout']);

		if (empty($conf->global->SENDGRIDAPPKEY)) {
			print 'Error: '.$langs->trans("ModuleSetupNotComplete")."\n";
			exit;
		}

		 //$conn = new Api($conf->global->SENDGRIDAPPKEY, $conf->global->SENDGRIDAPPSECRET, $endpoint, $conf->global->SENDGRIDCONSUMERKEY);
	} catch (SoapFault $fault) {
		setEventMessage('SoapFault Exception: '.$fault->getMessage().' - '.$fault->getTraceAsString(), 'errors');
	} catch (Exception $e) {
		setEventMessage('Exception: '.$e->getMessage().' - '.$e->getTraceAsString(), 'errors');
	}
}



/*
 * Action
 */

if ($action == 'import' && $sendgridthirdparty->id > 0) {
	if (! $error) {
		$listofref=$_POST['billnum'];
		$listofbillingcountry=$_POST['billingCountry'];
		//$listofvat=$_POST['vat'];

		if (count($listofref) == 0) {
			setEventMessage($langs->trans("NoInvoicesSelected"), 'errors');
			$action='refresh';
			$error++;
		}

		if (! $error) {
			//billingInvoiceList
			if (! empty($conf->global->SENDGRID_OLDAPI)) {
				try {
					$result = $soap->billingInvoiceList($session);

					file_put_contents(DOL_DATA_ROOT . "/dolibarr_sendgrid_billingInvoiceList.xml", $soap->__getLastResponse());
					@chmod(DOL_DATA_ROOT . "/dolibarr_sendgrid_billingInvoiceList.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
				} catch (Exception $e) {
					echo 'Exception soap->billingInvoiceList: '.$e->getMessage()."\n";
				}
				//echo "billingInvoiceList successfull (".count($result)." ".$langs->trans("Invoices").")\n";
			} else {
				$result=array();
			}

			foreach ($listofref as $key => $val) {
				$billnum=$val;
				$keyresult=0;
				if (! empty($conf->global->SENDGRID_OLDAPI)) {
					$billingcountry=$listofbillingcountry[$key];
					//$vatrate=$listofvat[$key];

					// Search key into array $result for billnum $billnum
					foreach ($result as $i => $r) {
						if ($r->billnum == $billnum) {
							$keyresult=$i;
							break;
						}
					}
				}

				//print "We try to create supplier invoice billnum ".$billnum." ".$billingcountry.", key in listofref = ".$key.", key in result ".$keyresult." ...<br>\n";

				// Invoice does not exists
				$db->begin();

				if (! empty($conf->global->SENDGRID_OLDAPI)) {
					$result[$keyresult]->info=$soap->billingInvoiceInfo($session, $billnum, null, $billingcountry); //on recupere les details

					file_put_contents(DOL_DATA_ROOT . "/dolibarr_sendgrid_billingInvoiceInfo.xml", $soap->__getLastResponse());
					@chmod(DOL_DATA_ROOT . "/dolibarr_sendgrid_billingInvoiceInfo.xml", octdec(empty($conf->global->MAIN_UMASK)?'0664':$conf->global->MAIN_UMASK));
				} else {
					$r = $conn->get('/me/bill/'.$billnum);
					$r2 = $conn->get('/me/bill/'.$val.'/details');
					$description='';
					$details=array();
					$pos=0;
					foreach ($r2 as $key2 => $val2) {
						$r2d = $conn->get('/me/bill/'.$val.'/details/'.$val2);
						$description.=$r2d['description']."<br>\n";
						$details[$pos]['billId']=$billnum;
						$details[$pos]['billDetailId']=$r2d['billDetailId'];
						$details[$pos]['description']=$r2d['description'];
						$details[$pos]['totalPrice']=$r2d['totalPrice']['value'];
						$details[$pos]['periodStart']=$r2d['periodStart'];
						$details[$pos]['periodEnd']=$r2d['periodEnd'];
						$details[$pos]['domain']=$r2d['domain'];
						$details[$pos]['unitPrice']=$r2d['unitPrice']['value'];
						$details[$pos]['quantity']=$r2d['quantity'];
						$pos++;
					}
					$result[$keyresult]=array(
						'id'=>$r['billId'],
						'billnum'=>$r['billId'],
						'date'=>dol_stringtotime($r['date'], 1),
						'vat'=>$r['tax']['value'],
						'totalPrice'=>$r['priceWithoutTax']['value'],
						'totalPriceWithVat'=>$r['priceWithTax']['value'],
						'currency'=>$r['priceWithTax']['currencyCode'],
						'description'=>$description,
						'details'=>$details,
						'billingCountry'=>'???',
						'ordernum'=>$r['orderId'],
						'serialized'=>'???',
						'url'=>$r['url'],
						'pdfUrl'=>$r['pdfUrl']
					);
				}

				$r=$result[$keyresult];

				$vatrate=0;
				if (! empty($conf->global->SENDGRID_OLDAPI)) {
					if ($r->info->taxrate < 1) $vatrate=price2num($r->info->taxrate * 100);
					else $vatrate=price2num(($r->info->taxrate - 1) * 100);
				} else {
					if ($r['totalPrice'] > 0) $vatrate=round($r['vat'] * 100 / $r['totalPrice'], 2);    // a vat rate is on 2 digits
					//var_dump($r);
					//var_dump($vatrate);exit;
				}

				$facfou = new FactureFournisseur($db);

				$facfou->ref_supplier  = $billnum;
				$facfou->socid         = $idsendgridsupplier;
				$facfou->libelle       = "SENDGRID ".$billnum;
				if (! empty($conf->global->SENDGRID_OLDAPI)) {
					$facfou->date          = dol_stringtotime($r->date, 1);
					$facfou->date_echeance = dol_stringtotime($r->date, 1);
				} else {
					$facfou->date          = is_numeric($r['date'])?$r['date']:dol_stringtotime($r['date'], 1);
					$facfou->date_echeance = is_numeric($r['date'])?$r['date']:dol_stringtotime($r['date'], 1);
				}
				$facfou->note_public   = '';

				//var_dump($billnum.' - '.$facfou->date.' - '.dol_print_date($facfou->date,'dayhour'));exit;

				$facid = $facfou->create($fuser);
				if ($facid > 0) {
					if (! empty($conf->global->SENDGRID_OLDAPI)) {
						foreach ($r->info->details as $d) {
							//var_dump($d->start);
							//var_dump($d->end);
							$label='<strong>ref :'.$d->service.'</strong><br>'.$d->description.'<br>';
							if ($d->start && $d->start != '0000-00-00' && $d->start != '0000-00-00 00:00:00') $label.=$langs->trans("From").' '.dol_print_date(strtotime($d->start), 'day');
							if ($d->end && $d->end != '0000-00-00' && $d->end != '0000-00-00 00:00:00')     $label.=($d->start?' ':'').$langs->trans("To").' '.dol_print_date(strtotime($d->end), 'day');
							$amount=$d->baseprice;
							$qty=$d->quantity;
							$price_base='HT';
							$tauxtva=vatrate($vatrate);
							$remise_percent=0;
							$fk_product=($conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID > 0 ? $conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID : null);
							$ret=$facfou->addline($label, $amount, $tauxtva, 0, 0, $qty, $fk_product, $remise_percent, '', '', '', 0, $price_base);
							if ($ret < 0) {
								$error++;
								setEventMessage("ERROR: ".$facfou->error, 'errors');
								break;
							}
						}
					} else {
						foreach ($r['details'] as $d) {
							$vatrate=$vatrate;
							//var_dump($d->start);
							//var_dump($d->end);
							$label='<strong>ref :'.$d['billDetailId'].'</strong><br>'.$d['description'].'<br>';
							if ($d['domain']) $label.=$d['domain'].'<br>';
							if ($d['periodStart'] && $d['periodStart'] != '0000-00-00' && $d['periodStart'] != '0000-00-00 00:00:00') $label.=$langs->trans("From").' '.dol_print_date(strtotime($d['periodStart']), 'day');
							if ($d['periodEnd'] && $d['periodEnd'] != '0000-00-00' && $d['periodEnd'] != '0000-00-00 00:00:00')     $label.=($d['periodStart']?' ':'').$langs->trans("To").' '.dol_print_date(strtotime($d['periodEnd']), 'day');
							$amount=$d['unitPrice'];
							$qty=$d['quantity'];
							$price_base='HT';
							$tauxtva=vatrate($vatrate);
							$remise_percent=0;
							$fk_product=($conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID > 0 ? $conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID : null);
							$ret=$facfou->addline($label, $amount, $tauxtva, 0, 0, $qty, $fk_product, $remise_percent, '', '', '', 0, $price_base);
							if ($ret < 0) {
								$error++;
								setEventMessage("ERROR: ".$facfou->error, 'errors');
								break;
							}
						}
					}
				} else {
					$error++;
					setEventMessage("ERROR: ".$facfou->error, 'errors');
				}

				if (! $error) {
					//print "Success<br>\n";
					$db->commit();
				} else {
					$db->rollback();
				}
			}

			if (! $error) $action='refresh';
		}
	}
}


/*
 *	View
 */

$form=new Form($db);

llxHeader('', $langs->trans("SendgridInvoiceImportShort"), '');

if (empty($conf->global->SENDGRIDAPPKEY)) {
	$langs->load("errors");
	setEventMessage($langs->trans("ErrorModuleSetupNotComplete"), 'errors');
	$mesg='<div class="errors">'.$langs->trans("ErrorModuleSetupNotComplete").'/<div>';
}

if ($sendgridthirdparty->id <= 0) {
	$langs->load("errors");
	setEventMessage($langs->trans("ErrorModuleSetupNotComplete"), 'errors');
	$mesg='<div class="errors">'.$langs->trans("ErrorModuleSetupNotComplete").'/<div>';
}


print_fiche_titre($langs->trans("SendgridInvoiceImportShort"));

print $langs->trans("SendgridInvoiceImportDesc").'<br><br>';

//print $form->select_produits($conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID, 'SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID');
//print $langs->trans("OvhSmsNick").': <strong>'.$conf->global->SENDGRIDSMS_NICK.'</strong><br>';

// Thirdparty to import on
print $langs->trans("SupplierToUseForImport").': ';
if ($sendgridthirdparty->id > 0) print $sendgridthirdparty->getNomUrl(1, 'supplier');
else print '<strong>'.$langs->trans("NotDefined").'</strong>';
print '<br>';
// Product to import on
print $langs->trans("ProductGenericToUseForImport").': ';
if ($conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID > 0) {
	$producttmp=new Product($db);
	$producttmp->fetch($conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID);
	print $producttmp->getNomUrl(1);
} else {
	print '<strong>'.$langs->trans("NoneLabelOnOvhLineWillBeUsed").'</strong>';
}

print '<br><br>';

print '<form name="refresh" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
if ((float) DOL_VERSION >= 11.0) {
	print '<input type="hidden" name="token" value="'.newToken().'">';
} else {
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
}

print '<div class="tabBar">';
print '<table class="notopnoborder"><tr><td>';
print '<input type="checkbox" name="excludenullinvoice"'.((! isset($_POST["excludenullinvoice"]) || GETPOST('excludenullinvoice'))?' checked="true"':'').'"> '.$langs->trans("ExcludeNullInvoices").'<br>';
print $langs->trans("FromThe").': ';
print $form->selectDate($datefrom, 'datefrom');
print '<br>';
print '<input type="hidden" name="action" value="refresh">';
print ' <input type="submit" name="import" value="'.$langs->trans("ScanSendgridInvoices").'" class="button">';
print '</td></tr></table>';
print'</div>';

print '</form>';
print '<br>';

if ($action == 'refresh') {
	try {
		$arrayinvoice=array();

		try {
			$addheaders = array('Authorization: Bearer '.$conf->global->SENDGRIDAPPKEY, 'Content-Type: application/json');
			$endpoint = 'https://api.sendgrid.com/v3/download/billing/invoices/';
			$result = getURLContent($endpoint, 'GET', '', 1, $addheaders);
		} catch (Exception $e) {
			echo 'Exception : '.$e->getMessage()."\n";
		}
		var_dump($result);
		$i=0;
		foreach ($result as $key => $val) {
			var_dump($val);
			$r = $conn->get('/me/bill/'.$val);
			if (! $excludenullinvoice || ! empty($r['priceWithoutTax']['value'])) {
				$r2 = $conn->get('/me/bill/'.$val.'/details');
				$description='';
				foreach ($r2 as $key2 => $val2) {
					$r2d = $conn->get('/me/bill/'.$val.'/details/'.$val2);
					//var_dump($r2d['description']);
					$description.=$r2d['description']."<br>\n";
				}
				$arrayinvoice[]=array('id'=>$r['billId'], 'billnum'=>$r['billId'], 'date'=>dol_stringtotime($r['date'], 1), 'vat'=>$r['tax']['value'], 'totalPrice'=>$r['priceWithoutTax']['value'], 'totalPriceWithVat'=>$r['priceWithTax']['value'], 'currency'=>$r['priceWithTax']['currencyCode'], 'description'=>$description, 'billingCountry'=>'???', 'ordernum'=>$r['orderId'], 'serialized'=>'???', 'url'=>$r['url'], 'pdfUrl'=>$r['pdfUrl']);

				$i++;
			}

			//if ($i > 5) break;
		}

		$arrayinvoice=dol_sort_array($arrayinvoice, 'date', (empty($conf->global->SENDGRID_IMPORT_SORTORDER)?'desc':$conf->global->SENDGRID_IMPORT_SORTORDER));

		$nbfound=count($arrayinvoice);
		if (! $nbfound) print $langs->trans("NoRecordFound")."<br><br>\n";
		else {
			print '<form name="import" action="'.$_SERVER["PHP_SELF"].'" method="POST">';

			print '<div><div style="float:left;"><strong>'.$nbfound.'</strong> '.$langs->trans("Invoices")."</div>\n";

			// Submit form to launch import
			print '<div style="float: right;">';
			print '<input type="hidden" name="action" value="import">';
			print '<input type="hidden" name="datefromday" value="'.dol_print_date($datefrom, '%d').'">';
			print '<input type="hidden" name="datefrommonth" value="'.dol_print_date($datefrom, '%m').'">';
			print '<input type="hidden" name="datefromyear" value="'.dol_print_date($datefrom, '%Y').'">';
			print '<input type="hidden" id="excludenullinvoicehidden" name="excludenullinvoice" value="'.$excludenullinvoice.'">';
			print ' <input type="submit" name="import" value="'.$langs->trans("ToImport").'" class="button">';
			print '</div>';

			print '</div><div style="clear: both"></div><br>';

			print '<table class="noborder" width="100%">';
			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans("Invoice").' SENDGRID</td>';
			print '<td class="center">'.$langs->trans("Date").'</td>';
			print '<td align="right">'.$langs->trans("AmountHT").'</td>';
			print '<td align="right">'.$langs->trans("AmountTTC").'</td>';
			print '<td align="right">'.$langs->trans("Currency").'</td>';
			//print '<td align="right">'.$langs->trans("VATRate").'</td>';
			print '<td>'.$langs->trans("Description").'</td>';
			print '<td align="right">'.$langs->trans("Action").'</td>';
			print '</tr>';

			foreach ($arrayinvoice as $i => $r) {
				//$vatrate=vatrate($r['totalPrice'] > 0 ? round(100*$r['vat']/$r['totalPrice'],2) : 0);

				print '<tr class="oddeven">';
				print '<td>'.$r['billnum'].'</td><td align="center">'.dol_print_date($r['date'], 'day')."</td>";
				print '<td align="right">'.price($r['totalPrice']).'</td>';
				print '<td align="right">'.price($r['totalPriceWithVat']).'</td>';
				print '<td align="right">'.$r['currency'].'</td>';
				//print '<td align="right">'.vatrate($vatrate).'</td>';
				print "<td>";
				$x=0; $olddomain=''; $oldordernum='';
				if (! empty($r['details'])) {
					foreach ($r['details'] as $detobj) {
						print $detobj->description;
						if (! empty($detobj->domain) && $olddomain != $detobj->domain) print ' ('.$detobj->domain.') ';
						$olddomain=$detobj->domain;
						//if (! empty($detobj->ordernum) && $oldordernum != $detobj->ordernum) print ' ('.$langs->trans("Order").': '.$detobj->ordernum.') ';
						//$oldordernum=$detobj->ordernum;
						print "\n";
						$x++;
					}
				}
				if (! empty($r['description'])) print $r['description'];
				if (! empty($r['ordernum'])) print ' ('.$langs->trans("Order").' SENDGRID: '.$r['ordernum'].') ';
				//if (! empty($r['serialized']))     { print ($x?'<br>':''); print $r['serialized'];	 $x++; }	// No more defined
				if (! empty($r['url'])) print ' (<a target="sendgridinvoice" href="'.$r['url'].'">'.$langs->trans("Link").' SENDGRID</a>) ';
				print "</td>\n";

				print '<td align="right" nowrap="nowrap">';


				// Search if invoice already exists
				$facid=0;

				$version=preg_split('/[\.-]/', DOL_VERSION);
				if (versioncompare($version, array(3,4,-3)) >= 0) {	// For dolibarr 3.4.*
					$sql="SELECT rowid ";
					$sql.=' FROM '.MAIN_DB_PREFIX.'facture_fourn as f';
					$sql.=" WHERE ref_supplier = '".$db->escape($r['billnum'])."' and fk_soc = ".$sendgridthirdparty->id;
				} else {
					$sql="SELECT rowid ";
					$sql.=' FROM '.MAIN_DB_PREFIX.'facture_fourn as f';
					if ((float) DOL_VERSION < 10) $sql.=" WHERE facnumber = '".$db->escape($r['billnum'])."' and fk_soc = ".$sendgridthirdparty->id;
					else $sql.=" WHERE ref = '".$db->escape($r['billnum'])."' and fk_soc = ".$sendgridthirdparty->id;
				}
				dol_syslog("Seach if invoice exists sql=".$sql);
				$resql = $db->query($sql);
				$num=0;
				if ($resql) {
					$num=$db->num_rows($resql);
				}
				if ($num == 0) {
					print $langs->trans("NotFound").'. '.$langs->trans("ImportIt");
					print ' <input class="flat" type="checkbox" name="billnum[]" value="'.$r['billnum'].'">';
					print '<input type="hidden" name="billingCountry[]" value="'.$r['billingCountry'].'">';
					//print ' '.$langs->trans("VATRate").' <input class="flat" type="text" name="vat[]" value="'.vatrate($vatrate).'" size="3">';
				} else {
					$row=$db->fetch_array($resql);
					$facid=$row['rowid'];
					// If invoice exist into Dolibarr database
					if ($facid > 0) {
						$facfou = new FactureFournisseur($db);
						$facfou->fetch($facid);

						$ref=dol_sanitizeFileName($facfou->ref);
						$upload_dir = $conf->fournisseur->facture->dir_output.'/'.get_exdir($facfou->id, 2, 0, 0, $facfou, 'invoice_supplier').$ref;
						//var_dump($upload_dir);
						$file_name=($upload_dir."/".$facfou->ref_supplier.".pdf");
						$file_name_bis=($upload_dir."/".$facfou->ref.'_'.$facfou->ref_supplier.".pdf");

						$file_name_to_use = (empty($conf->global->MAIN_DISABLE_SUGGEST_REF_AS_PREFIX) ? $file_name_bis : $file_name);

						if (file_exists($file_name) || file_exists($file_name_bis)) {
							print $langs->trans("InvoicePDFFoundIntoDolibarr")." ".$facfou->getNomUrl(1)."\n";
							//echo "<br>File ".dol_basename($file_name)." also already exists\n";
						} else {
							print $langs->trans("InvoiceFoundIntoDolibarr", $facfou->getNomUrl(1))."\n";
							if (! is_dir($upload_dir)) dol_mkdir($upload_dir);
							if (is_dir($upload_dir)) {
								if (! empty($conf->global->SENDGRID_OLDAPI)) {
									$result[$i]->info=$soap->billingInvoiceInfo($session, $r['billnum'], null, $r['billingCountry']); //on recupere les details
									$r2=$result[$i];
									$url=$url_pdf."?reference=".$r['billnum']."&passwd=".$r2->info->password;
								} else {
									$url = $r['pdfUrl'];
								}

								//print "<br>Get ".$url."\n";
								file_put_contents($file_name_to_use, file_get_contents($url));
								print "<br>".$langs->trans("FileDownloadedAndAttached", basename($file_name_to_use))."\n";
							}
						}
						//$facfou->set_valid($fuser);
					}
				}
				print '</td>';

				print "</tr>";
			}

			print '<table><br>';
		}


		//logout
		if (! empty($conf->global->SENDGRID_OLDAPI)) $soap->logout($session);

		print '</form>';
	} catch (SoapFault $fault) {
		echo $fault;
	}
}

print '<br>';

llxFooter();

$db->close();
