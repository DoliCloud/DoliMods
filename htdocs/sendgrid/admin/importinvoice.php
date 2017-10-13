<?php

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

dol_include_once('/sendgrid/class/sendgrid.class.php');
dol_include_once("/sendgrid/lib/sendgrid.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php');
require_once(DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php');
require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(NUSOAP_PATH.'/nusoap.php');     // Include SOAP


$langs->load("sendgrid@sendgrid");
$langs->load("admin");
$langs->load("companies");
$langs->load("sms");

$error=0;

$action = GETPOST('action','aZ09');

// Protection if external user
if ($user->societe_id > 0) accessforbidden();

$endpoint = empty($conf->global->SENDGRID_ENDPOINT)?'sendgrid-eu':$conf->global->SENDGRID_ENDPOINT;



/*
 * Actions
 */

if ($action == 'setvalue' && $user->admin)
{
    $result1=dolibarr_set_const($db, "SENDGRID_THIRDPARTY_IMPORT",GETPOST("SENDGRID_THIRDPARTY_IMPORT"),'chaine',0,'',$conf->entity);
    $result2=dolibarr_set_const($db, "SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID",GETPOST("SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID"),'chaine',0,'',$conf->entity);
    if ($result1 >= 0 && $result2 >= 0)
    {
        $mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
    }
    else
    {
        dol_print_error($db);
    }
}




/*
 * View
 */

$form=new Form($db);

$WS_DOL_URL = $conf->global->SENDGRIDSMS_SOAPURL;
dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);

$login = $conf->global->SENDGRIDSMS_NICK;
$password = $conf->global->SENDGRID_SMS_PASS;

$logindol=$user->login;



$morejs = '';
llxHeader('', $langs->trans('SendgridSmsSetup'), '', '', '', '', $morejs, '', 0, 0);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans('SendgridSmsSetup'),$linkback,'setup');

$head=sendgridadmin_prepare_head();

dol_htmloutput_mesg($mesg);



// Formulaire d'ajout de compte SMS qui sera valable pour tout Dolibarr
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';


    $var=true;

    dol_fiche_head($head, 'getinvoices', $langs->trans("Sendgrid"), -1);

    if (empty($conf->global->SENDGRID_OLDAPI) && (empty($conf->global->SENDGRIDAPPKEY) || empty($conf->global->SENDGRIDAPPSECRET) || empty($conf->global->SENDGRIDCONSUMERKEY)))
    {
        echo '<div class="warning">'.$langs->trans("SendgridAuthenticationPartNotConfigured").'</div>';
    }

    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans("Parameter").'</td>';
    print '<td>'.$langs->trans("Value").'</td>';
    print '<td>&nbsp;</td>';
    print "</tr>\n";

/*
    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("UserMakingImport").'</td><td>';
    print '<input size="64" type="text" name="SENDGRID_USER_LOGIN" value="'.$logindol.'">';
    print '<td>';
    print '</td></tr>';
*/

    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("SupplierToUseForImport").'</td><td>';
    print $form->select_company($conf->global->SENDGRID_THIRDPARTY_IMPORT,'SENDGRID_THIRDPARTY_IMPORT','s.fournisseur = 1',1,'supplier');
    print '<td>';
    print '</td></tr>';

    if ($conf->product->enable || $conf->service->enabled)
    {
        $var=!$var;
        print '<tr '.$bc[$var].'><td>';
        print $langs->trans("ProductGenericToUseForImport").'</td><td>';
        print $form->select_produits($conf->global->SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID, 'SENDGRID_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID');
        print '<td>';
        print $langs->trans("KeepEmptyToSaveLinesAsFreeLines");
        print '</td></tr>';
    }

    print '</table>';

    dol_fiche_end();

    print '<div class="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></div>';


print '</form>';


/*
if ($action == 'preimport')
{
    $fuser = new User($db);
    $result=$fuser->fetch('',$logindol);
    if ($result <= 0)
    {
        print "Bad login user to use\n";
        exit;
    }

    try {
        require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
        $params=getSoapParams();
        ini_set('default_socket_timeout', $params['response_timeout']);

        $soap = new SoapClient($WS_DOL_URL,$params);
        try {
            $language="en";
            $multisession=false;

            //login
            $session = $soap->login($conf->global->SENDGRIDSMS_NICK, $conf->global->SENDGRIDSMS_PASS, $language, $multisession);
            //if ($session) print '<div class="ok">'.$langs->trans("SendgridSmsLoginSuccessFull").'</div><br>';
            if (! $session) print '<div class="error">Error login did not return a session id</div><br>';

            //logout
            //$soap->logout($session);
            //  echo "logout successfull\n";

        }
        catch(Exception $e)
        {
            print '<div class="error">';
            print 'Error '.$e->getMessage().'<br>';
            print 'If this is an error to connect to SENDGRID host, check your firewall does not block port required to reach SENDGRID manager (for example port 1664).<br>';
            print '</div>';
        }


        $result = $soap->billingGetAccessByNic($session);
        echo "billingGetAccessByNic successfull\n";
        print_r($result); // your code here ...
        //billingInvoiceList
        $result = $soap->billingInvoiceList($session);
        echo "billingInvoiceList successfull\n";
        foreach ($result as $i=> $r)
        {
            $sql="SELECT rowid ";
            $sql.=' FROM '.MAIN_DB_PREFIX.'facture_fourn as f';
            $sql.=" WHERE facnumber like '".$r->billnum."'";
            $resql = $db->query($sql);
            $num=0;
            if ($resql)
            {
                $num=$db->num_rows($resql);
            }
            if ($num ==0)
            {
                //facture n'existe pas
                $db->begin();
                $result[$i]->info=$soap->billingInvoiceInfo($session, $r->billnum, null, $r->billingCountry); //on recupere les details
                $r=$result[$i];
                $facfou = new FactureFournisseur($db);

                $facfou->ref           = $r->billnum;
                $facfou->socid         = $id_sendgrid;
                $facfou->libelle       = "";
                $facfou->date          = strtotime($r->date);
                $facfou->date_echeance = strtotime($r->date);
                $facfou->note_public   = '';

                $facid = $facfou->create($fuser);
                if ($facid > 0)
                {
                    foreach($r->info->details as $d)
                    {
                        $label='<strong>ref :'.$d->service.'</strong><br>'.$d->description.'<br
    > >';
                        if($d->start)
                        $label.='<i>du '.date('d/m/Y',strtotime($d->start));
                        if($d->end)
                        $label.=' au '.date('d/m/Y',strtotime($d->end));
                        $amount=$d->baseprice;
                        $qty=$d->quantity;
                        $price_base='HT';
                        $tauxtva=19.6;
                        $remise_percent=0;
                        $fk_product=null;
                        $ret=$facfou->addline($label, $amount, $tauxtva, $qty, $fk_product,
                        $remise_percent, '', '', '', 0, $price_base);
                        if ($ret < 0) $nberror++;
                        if ($nberror)
                        {
                            $db->rollback();
                            echo "ERROR: ".$facfou->error."\n";
                        }
                        else
                        {
                            $db->commit();
                        }
                    }
                }
                else
                {
                    $db->rollback();
                    echo "ERROR: ".$facfou->error."\n";
                }
            }
            else
            {
                $row=$db->fetch_array($resql);
                $facid=$row['rowid'];
                $facfou = new FactureFournisseur($db);
                echo "fetching fact $facid ...\n";
                if($facfou->fetch($facid))
                {
                    if($facfou->fk_statut == 0)
                    {
                        $ref=dol_sanitizeFileName($facfou->ref);
                        $upload_dir = $conf->fournisseur->facture->dir_output.'/'.get_exdir($facfou->id,2,0,0,$facfou,'invoice_supplier').$ref;

                        if (! is_dir($upload_dir)) dol_mkdir($upload_dir);

                        if (is_dir($upload_dir))
                        {
                            $result[$i]->info=$soap->billingInvoiceInfo($session, $r->billnum, null,
                            $r->billingCountry); //on recupere les details
                            $r=$result[$i];
                            $url=$url_pdf."?reference=".$r->billnum."&passwd=".$r->info->password;
                            $file_name=($upload_dir."/".$facfou->ref_supplier.".pdf");
                            echo "$url \n";
                            if(file_exists($file_name))
                            {
                                echo "file $file_name exists !!\n";
                            }
                            else{

                                file_put_contents($file_name,file_get_contents($url));
                            }
                            //print_r($r->info);
                        }
                    }
                    $facfou->set_valid($fuser);
                }
                else{
                    echo "Imposible d'obtenir la facture $facid \n";
                }
                //print_r($facfou);
            }
        }



        //logout
        if (! empty($conf->global->SENDGRID_OLDAPI)) $soap->logout($session);
        echo "logout successfull\n";

    } catch(SoapFault $fault) {
        echo $fault;
    }
}

dol_fiche_end();
*/

llxFooter();

$db->close();

