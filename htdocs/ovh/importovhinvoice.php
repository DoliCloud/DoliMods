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
 *   \file       htdocs/ovh/importovhinvoice.php
 *	 \ingroup    ovh
 *	 \brief		 Page to import OVH invoices
 */

// Include Dolibarr environment
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php');
require_once(DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php');
require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");

$langs->load("bills");
$langs->load("orders");
$langs->load("ovh@ovh");

//eregi_replace($script_file,'',$_SERVER["PHP_SELF"]);
$url_pdf="https://www.ovh.com/cgi-bin/order/facture.pdf";

$action=GETPOST('action');
$excludenullinvoice=GETPOST('excludenullinvoice');
//$idovhsupplier=GETPOST('idovhsupplier');
$idovhsupplier=empty($conf->global->OVH_THIRDPARTY_IMPORT)?'':$conf->global->OVH_THIRDPARTY_IMPORT;

$ovhthirdparty=new Societe($db);
if ($idovhsupplier) $result=$ovhthirdparty->fetch($idovhsupplier);

$fuser = $user;

// For compatibility with 3.2
if (! function_exists('setEventMessage'))
{
	/**
	 *	Set event message in dol_events session
	 *
	 *	@param	mixed	$mesgs			Message string or array
	 *  @param  string	$style      	Which style to use ('mesgs', 'warnings', 'errors')
	 *  @return	void
	 *  @see	dol_htmloutput_events
	 */
	function setEventMessage($mesgs, $style='mesgs')
	{
		if (! in_array((string) $style, array('mesgs','warnings','errors'))) dol_print_error('','Bad parameter for setEventMessage');
		if (! is_array($mesgs))		// If mesgs is a string
		{
			if ($mesgs) $_SESSION['dol_events'][$style][] = $mesgs;
		}
		else						// If mesgs is an array
		{
			foreach($mesgs as $mesg)
			{
				if ($mesg) $_SESSION['dol_events'][$style][] = $mesg;
			}
		}
	}
}

// Init SoapClient
if (! empty($action))
{
	try
	{
		require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
		$params=getSoapParams();
		ini_set('default_socket_timeout', $params['response_timeout']);

		if (empty($conf->global->OVHSMS_SOAPURL))
		{
			print 'Error: '.$langs->trans("ModuleSetupNotComplete")."\n";
			exit;
		}

		//use_soap_error_handler(true);

		$soap = new SoapClient($conf->global->OVHSMS_SOAPURL,$params);

		$language = "en";
		$multisession = false;

		//login
		$session = $soap->login($conf->global->OVHSMS_NICK, $conf->global->OVHSMS_PASS,$language,$multisession);
		dol_syslog("login successfull");

		$result = $soap->billingGetAccessByNic($session);
		dol_syslog("billingGetAccessByNic successfull = ".join(',',$result));
		//print "GetAccessByNic: ".join(',',$result)."<br>\n";
	}
	catch(SoapFault $fault)
	{
		setEventMessage('SoapFault Exception: '.$fault->getMessage().' - '.$fault->getTraceAsString(),'errors');
	}
}


/*
 * Action
 */

if ($action == 'import' && $ovhthirdparty->id > 0)
{
	if (! $error)
	{
		$listofref=$_POST['billnum'];
		$listofbillingcountry=$_POST['billingCountry'];
		//$listofvat=$_POST['vat'];

		if (count($listofref) == 0)
		{
			setEventMessage($langs->trans("NoInvoicesSelected"),'errors');
			$action='refresh';
			$error++;
		}

		if (! $error)
		{
			//billingInvoiceList
			try {
				$result = $soap->billingInvoiceList($session);
			}
			catch(Exception $e)
			{
				echo 'Exception soap->billingInvoiceList: '.$e->getMessage()."\n";
			}
			//echo "billingInvoiceList successfull (".count($result)." ".$langs->trans("Invoices").")\n";

			foreach($listofref as $key => $val)
			{
				$billnum=$val;
				$billingcountry=$listofbillingcountry[$key];
				//$vatrate=$listofvat[$key];

				// Search key into array $result for billnum $billnum
				$keyresult=0;
				foreach($result as $i => $r)
				{
					if ($r->billnum == $billnum)
					{
						$keyresult=$i;
						break;
					}
				}

				//print "We try to create supplier invoice billnum ".$billnum." ".$billingcountry.", key in listofref = ".$key.", key in result ".$keyresult." ...<br>\n";

				// Invoice does not exists
				$db->begin();

				$result[$keyresult]->info=$soap->billingInvoiceInfo($session, $billnum, null, $billingcountry); //on recupere les details
				$r=$result[$keyresult];

				if ($r->info->taxrate < 1) $vatrate=price2num($r->info->taxrate * 100);
				else $vatrate=price2num(($r->info->taxrate - 1) * 100);

				$facfou = new FactureFournisseur($db);

				$facfou->ref           = $billnum;
				$facfou->socid         = $idovhsupplier;
				$facfou->libelle       = "OVH ".$billnum;
				$facfou->date          = dol_stringtotime($r->date,1);
				$facfou->date_echeance = dol_stringtotime($r->date,1);
				$facfou->note_public   = '';

				//var_dump($billnum.' '.$r->date.' '.dol_print_date($facfou->date,'dayhour'));exit;

				$facid = $facfou->create($fuser);
				if ($facid > 0)
				{
					foreach($r->info->details as $d)
					{
						//var_dump($d->start);
						//var_dump($d->end);
						$label='<strong>ref :'.$d->service.'</strong><br>'.$d->description.'<br>';
						if ($d->start && $d->start != '0000-00-00 00:00:00') $label.=$langs->trans("From").' '.dol_print_date(strtotime($d->start),'day');
						if ($d->end && $d->end != '0000-00-00 00:00:00')     $label.=($d->start?' ':'').$langs->trans("To").' '.dol_print_date(strtotime($d->end),'day');
						$amount=$d->baseprice;
						$qty=$d->quantity;
						$price_base='HT';
						$tauxtva=vatrate($vatrate);
						$remise_percent=0;
						$fk_product=null;
						$ret=$facfou->addline($label, $amount, $tauxtva, 0, 0, $qty, $fk_product, $remise_percent, '', '', '', 0, $price_base);
						if ($ret < 0)
						{
							$error++;
							setEventMessage("ERROR: ".$facfou->error, 'errors');
							break;
						}
					}
				}
				else
				{
					$error++;
					setEventMessage("ERROR: ".$facfou->error, 'errors');
				}

				if (! $error)
				{
					//print "Success<br>\n";
					$db->commit();
				}
				else
				{
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

llxHeader('',$langs->trans("OvhInvoiceImportShort"),'');

if (empty($conf->global->OVHSMS_SOAPURL))
{
	$langs->load("errors");
	setEventMessage($langs->trans("ErrorModuleSetupNotComplete"),'errors');
	$mesg='<div class="errors">'.$langs->trans("ErrorModuleSetupNotComplete").'/<div>';
}
if ($ovhthirdparty->id <= 0)
{
	$langs->load("errors");
	setEventMessage($langs->trans("ErrorModuleSetupNotComplete"),'errors');
	$mesg='<div class="errors">'.$langs->trans("ErrorModuleSetupNotComplete").'/<div>';
}

print_fiche_titre($langs->trans("OvhInvoiceImportShort"));

print $langs->trans("OvhInvoiceImportDesc").'<br><br>';
print $langs->trans("OvhSmsNick").': <strong>'.$conf->global->OVHSMS_NICK.'</strong><br>';
print $langs->trans("SupplierToUseForImport").': ';
if ($ovhthirdparty->id > 0) print $ovhthirdparty->getNomUrl(1,'supplier');
else print '<strong>'.$langs->trans("NotDefined").'</strong>';
print '<br><br>';

print '<form name="refresh" action="'.$_SERVER["PHP_SELF"].'" method="POST">';

print '<div class="tabBar">';
print '<table class="notopnoborder"><tr><td>';
print '<input type="checkbox" name="excludenullinvoice"'.((! isset($_POST["excludenullinvoice"]) || GETPOST('excludenullinvoice'))?' checked="true"':'').'"> '.$langs->trans("ExcludeNullInvoices").'<br>';
print '<input type="hidden" name="action" value="refresh">';
print ' <input type="submit" name="import" value="'.$langs->trans("ScanOvhInvoices").'" class="button">';
print '</td></tr></table>';
print'</div>';

print '</form>';
print '<br>';

if ($action == 'refresh')
{
	try {
	    //billingInvoiceList
	    $result = $soap->billingInvoiceList($session);
	    dol_syslog("billingInvoiceList successfull (".count($result)." invoices)");
	    //var_dump($result[0]->date.' '.dol_print_date(dol_stringtotime($r->date,1),'day'));exit;

	    // Set qualified invoices into arrayinvoice
	    $arrayinvoice=array();
	    foreach ($result as $i => $r)
	    {
		    if (! $excludenullinvoice || ! empty($r->totalPriceWithVat))
		    {
	    		$arrayinvoice[]=array('id'=>$r->id, 'billnum'=>$r->billnum, 'date'=>dol_stringtotime($r->date,1), 'vat'=>$r->vat, 'totalPrice'=>$r->totalPrice, 'totalPriceWithVat'=>$r->totalPriceWithVat, 'details'=>$r->details, 'billingCountry'=>$r->billingCountry, 'ordernum'=>$r->ordernum, 'serialized'=>serialize($r));
		    }
	    }

	    $arrayinvoice=dol_sort_array($arrayinvoice,'date');

	    $nbfound=count($arrayinvoice);
	    if (! $nbfound) print $langs->trans("NoRecordFound")."<br><br>\n";
	    else
	    {
	    	print '<strong>'.$nbfound.'</strong> '.$langs->trans("Invoices")."<br><br>\n";

	    	print '<form name="import" action="'.$_SERVER["PHP_SELF"].'" method="POST">';

		    print '<table class="noborder" width="100%">';
	    	print '<tr class="liste_titre">';
	    	print '<td>'.$langs->trans("Invoice").' OVH</td>';
	    	print '<td align="center">'.$langs->trans("Date").'</td>';
	    	print '<td align="right">'.$langs->trans("AmountHT").'</td>';
	    	print '<td align="right">'.$langs->trans("AmountTTC").'</td>';
	    	//print '<td align="right">'.$langs->trans("VATRate").'</td>';
	    	print '<td>'.$langs->trans("Description").'</td>';
	    	print '<td align="right">'.$langs->trans("Action").'</td>';
	    	print '</tr>';

	    	$var=true;
		    foreach ($arrayinvoice as $i => $r)
		    {
		        //$vatrate=vatrate($r['totalPrice'] > 0 ? round(100*$r['vat']/$r['totalPrice'],2) : 0);

	        	$var=!$var;
	        	print '<tr '.$bc[$var].'>';
		        print '<td>'.$r['billnum'].'</td><td align="center">'.dol_print_date($r['date'],'day')."</td>";
		        print '<td align="right">'.price($r['totalPrice']).'</td>';
		        print '<td align="right">'.price($r['totalPriceWithVat']).'</td>';
		        //print '<td align="right">'.vatrate($vatrate).'</td>';
	        	print "<td>";
	            $x=0; $olddomain=''; $oldordernum='';
	            foreach($r['details'] as $detobj)
	            {
	                print $detobj->description;
	                if (! empty($detobj->domain) && $olddomain != $detobj->domain) print ' ('.$detobj->domain.') ';
	                $olddomain=$detobj->domain;
	            	//if (! empty($detobj->ordernum) && $oldordernum != $detobj->ordernum) print ' ('.$langs->trans("Order").': '.$detobj->ordernum.') ';
	            	//$oldordernum=$detobj->ordernum;
	                print "\n";
	                $x++;
	            }
	            if (! empty($r['ordernum'])) print ' ('.$langs->trans("Order").' OVH: '.$r['ordernum'].') ';
	            //if (! empty($r['serialized']))     { print ($x?'<br>':''); print $r['serialized'];	 $x++; }	// No more defined
	            print "</td>\n";

	            print '<td align="right" nowrap="nowrap">';


	            // Search if invoice already exists
	            $facid=0;

	            $sql="SELECT rowid ";
	            $sql.=' FROM '.MAIN_DB_PREFIX.'facture_fourn as f';
	            $sql.=" WHERE facnumber = '".$db->escape($r['billnum'])."' and fk_soc = ".$ovhthirdparty->id;

	            dol_syslog("Seach if invoice exists sql=".$sql);
	            $resql = $db->query($sql);
	            $num=0;
	            if ($resql)
	            {
	                $num=$db->num_rows($resql);
	            }
	            if ($num == 0)
	            {
	                print $langs->trans("NotFound").'. '.$langs->trans("ImportIt");
	                print ' <input class="flat" type="checkbox" name="billnum[]" value="'.$r['billnum'].'">';
	                print '<input type="hidden" name="billingCountry[]" value="'.$r['billingCountry'].'">';
	                //print ' '.$langs->trans("VATRate").' <input class="flat" type="text" name="vat[]" value="'.vatrate($vatrate).'" size="3">';
	            }
	            else
	            {
	                $row=$db->fetch_array($resql);
	                $facid=$row['rowid'];
		            // If invoice exist into Dolibarr database
	                if ($facid > 0)
	                {
	                	$facfou = new FactureFournisseur($db);
	                	$facfou->fetch($facid);

	                	$ref=dol_sanitizeFileName($facfou->ref);
	                    $upload_dir = $conf->fournisseur->facture->dir_output.'/'.get_exdir($facfou->id,2).$ref;
	                    $file_name=($upload_dir."/".$facfou->ref_supplier.".pdf");

	                	if (file_exists($file_name))
	                	{
	                		print $langs->trans("InvoicePDFFoundIntoDolibarr",$facfou->getNomUrl(1))."\n";
	                		//echo "<br>File ".dol_basename($file_name)." also already exists\n";
	                    }
	                	else
	                  {
	                		print $langs->trans("InvoiceFoundIntoDolibarr",$facfou->getNomUrl(1))."\n";
	                  		if (! is_dir($upload_dir)) dol_mkdir($upload_dir);
	                        if (is_dir($upload_dir))
	                        {
	                            $result[$i]->info=$soap->billingInvoiceInfo($session, $r['billnum'], null, $r['billingCountry']); //on recupere les details
	                            $r2=$result[$i];
	                            $url=$url_pdf."?reference=".$r['billnum']."&passwd=".$r2->info->password;
	                            //print "<br>Get ".$url."\n";
                                file_put_contents($file_name,file_get_contents($url));
                                print "<br>".$langs->trans("FileDownloadedAndAttached",basename($file_name))."\n";
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
	    $soap->logout($session);


	    // Submit form to launch import
	    print '<input type="hidden" name="action" value="import">';
	    print '<input type="hidden" id="excludenullinvoicehidden" name="excludenullinvoice" value="'.$excludenullinvoice.'">';
	    print ' <input type="submit" name="import" value="'.$langs->trans("ToImport").'" class="button">';
	    print '</form>';

	}
	catch(SoapFault $fault)
	{
	    echo $fault;
	}

}

print '<br>';

llxFooter();

$db->close();
?>
