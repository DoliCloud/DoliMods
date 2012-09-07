<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-Francois FERRY  <jfefe@aternatik.fr>
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
require_once(DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");

$langs->load("bills");

//eregi_replace($script_file,'',$_SERVER["PHP_SELF"]);
$url_pdf="https://www.ovh.com/cgi-bin/order/facture.pdf";

$action=GETPOST('action');
$idovhsupplier=GETPOST('idovhsupplier');
$excludenullinvoice=GETPOST('excludenullinvoice');

$fuser = $user;


/*
 * Action
 */

if ($action == 'import' )
{
	if ($idovhsupplier <= 0)
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Supplier")),'errors');
		$error++;
	}

	if (! $error)
	{
		$ovhthirdparty=new Societe($db);
		$result=$ovhthirdparty->fetch($idovhsupplier);
		if ($result <= 0)
		{
			dol_print_error('',"Failed to get thirdparty to use to link OVH invoices");
			exit;
		}
	}

}



/*
 *	View
 */

$form=new Form($db);

llxHeader('',$langs->trans("OvhInvoiceImportShort"),'');

print_fiche_titre($langs->trans("OvhInvoiceImportShort"));


if ($action != 'refresh')
{
	print '<form name="refresh" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="refresh">';
	print ' <input type="submit" name="import" value="'.$langs->trans("ScanOvhInvoices").'" class="button">';
	print '</br>';
}
else
{

	try {
	    require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
	    $params=getSoapParams();
	    ini_set('default_socket_timeout', $params['response_timeout']);

	    if (empty($conf->global->OVHSMS_SOAPURL))
	    {
	        print 'Error: '.$langs->trans("ModuleSetupNotComplete")."\n";
	        exit;
	    }
	    echo "Wait...";

	    $soap = new SoapClient($conf->global->OVHSMS_SOAPURL,$params);

	    $language = "en";
	    $multisession = false;

	    //login
	    $session = $soap->login($conf->global->OVHSMS_NICK, $conf->global->OVHSMS_PASS,$language,$multisession);
	    print "login successfull\n";


	    $result = $soap->billingGetAccessByNic($session);
	    echo "billingGetAccessByNic successfull\n";
	    print join(',',$result);
	    print "\n";

	    //billingInvoiceList
	    $result = $soap->billingInvoiceList($session);
	    echo "billingInvoiceList successfull (".count($result)." ".$langs->trans("Invoices").")\n";
	    foreach ($result as $i => $r)
	    {
	        $vatrate=vatrate($r->totalPrice > 0?round(100*$r->vat/$r->totalPrice,2):0);
	        if ($excludenullinvoice && empty($r->totalPriceWithVat))
	        {
	            print 'Discard OVH invoice '.$r->billnum." (".$r->date." - ".$langs->trans("Total").'='.$r->totalPriceWithVat." - ".$langs->trans("VatRate").'='.$vatrate.")\n";
	        }
	        else
	        {
	            print 'Process OVH invoice '.$r->billnum." (".$r->date." - ".$langs->trans("Total").'='.$r->totalPriceWithVat." - ".$langs->trans("VatRate").'='.$vatrate.")\n";

	            foreach($r->details as $detobj)
	            {
	                print " ".$detobj->description."\n";
	            }

	            $sql="SELECT rowid ";
	            $sql.=' FROM '.MAIN_DB_PREFIX.'facture_fourn as f';
	            $sql.=" WHERE facnumber = '".$db->escape($r->billnum)."' and fk_soc = ".$ovhthirdparty->id;

	            dol_syslog("Seach if invoice exists sql=".$sql);
	            $resql = $db->query($sql);
	            $num=0;
	            if ($resql)
	            {
	                $num=$db->num_rows($resql);
	            }
	            if ($num == 0)
	            {
	                print "-> Not found into Dolibarr. Create it [y/N] ? ";

	                if ($input == 'y')
	                {
	                    print "We try to create supplier invoice ".$r->billnum."... ";
	                    //facture n'existe pas
	                    $db->begin();
	                    $result[$i]->info=$soap->billingInvoiceInfo($session, $r->billnum, null, $r->billingCountry); //on recupere les details
	                    $r=$result[$i];

	                    $facfou = new FactureFournisseur($db);

	                    $facfou->ref           = $r->billnum;
	                    $facfou->socid         = $id_ovh;
	                    $facfou->libelle       = "OVH ".$r->billnum;
	                    $facfou->date          = strtotime($r->date);
	                    $facfou->date_echeance = strtotime($r->date);
	                    $facfou->note_public   = '';

	                    $facid = $facfou->create($fuser);
	                    if ($facid > 0)
	                    {
	                        foreach($r->info->details as $d)
	                        {
	                            $label='<strong>ref :'.$d->service.'</strong><br>'.$d->description.'<br>';
	                            if ($d->start) $label.=$langs->trans("From").' '.date('d/m/Y',strtotime($d->start));
	                            if ($d->end)   $label.=($d->start?' ':'').$langs->trans("To").' '.date('d/m/Y',strtotime($d->end));
	                            $amount=$d->baseprice;
	                            $qty=$d->quantity;
	                            $price_base='HT';
	                            $tauxtva=$vatrate;
	                            $remise_percent=0;
	                            $fk_product=null;
	                            $ret=$facfou->addline($label, $amount, $tauxtva, 0, 0, $qty, $fk_product, $remise_percent, '', '', '', 0, $price_base);
	                            if ($ret < 0) $nberror++;
	                            if ($nberror)
	                            {
	                                $db->rollback();
	                                echo "ERROR: ".$facfou->error."\n";
	                            }
	                            else
	                            {
	                                print "Success\n";
	                                $db->commit();
	                            }
	                        }
	                    }
	                    else {
	                        $db->rollback();
	                        echo "ERROR: ".$facfou->error."\n";
	                    }
	                }
	            }
	            else
	            {
	                $row=$db->fetch_array($resql);
	                $facid=$row['rowid'];
	                print "-> Invoice found into Dolibarr with id=".$facid."\n";
	                $facfou = new FactureFournisseur($db);
	            }

	            if ($facid > 0)
	            {
	                if ($facfou->fetch($facid))
	                {
	                    print "Try to get OVH document\n";
	                    if ($facfou->fk_statut == 0)
	                    {
	                        $ref=dol_sanitizeFileName($facfou->ref);
	                        $upload_dir = $conf->fournisseur->facture->dir_output.'/'.get_exdir($facfou->id,2).$ref;

	                        if (! is_dir($upload_dir)) dol_mkdir($upload_dir);

	                        if (is_dir($upload_dir))
	                        {
	                            $result[$i]->info=$soap->billingInvoiceInfo($session, $r->billnum, null,
	                            $r->billingCountry); //on recupere les details
	                            $r=$result[$i];
	                            $url=$url_pdf."?reference=".$r->billnum."&passwd=".$r->info->password;
	                            $file_name=($upload_dir."/".$facfou->ref_supplier.".pdf");
	                            print "Get ".$url."\n";
	                            if(file_exists($file_name))
	                            {
	                                echo "File ".$file_name." already exists\n";
	                            }
	                            else
	                            {
	                                file_put_contents($file_name,file_get_contents($url));
	                                print "File ".$file_name." saved as joined file for supplier invoice ".$r->billnum."\n";
	                            }
	                        }
	                    }
	                    //$facfou->set_valid($fuser);
	                }
	                else
	                {
	                    echo "Failed to get invoice $facid \n";
	                }
	                //print_r($facfou);
	            }

	        }
	    }


	    //logout
	    $soap->logout($session);
	}
	catch(SoapFault $fault)
	{
	    echo $fault;
	}


	// Submit form to launch import
	print '<form name="refresh" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="import">';

	print $langs->trans("SupplierToUseForImport").': '.$form->select_company(GETPOST('idovhsupplier'),'refresh','',1,'supplier');
	print ' <input type="submit" name="import" value="'.$langs->trans("Import").'" class="button">';
	print '</form>';

}

llxFooter();

$db->close();

?>
