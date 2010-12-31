<?php

$version='$Revision: 1.1 $';
$path=dirname(__FILE__);
//eregi_replace($script_file,'',$_SERVER["PHP_SELF"]);
$url_pdf="https://www.ovh.com/cgi-bin/order/facture.pdf";
$id_ovh=5;
require_once($path."../../../htdocs/master.inc.php");
require_once(DOL_DOCUMENT_ROOT."/user.class.php");
require_once(DOL_DOCUMENT_ROOT.'/fourn/facture/paiementfourn.class.php');
require_once(DOL_DOCUMENT_ROOT.'/fourn/fournisseur.facture.class.php');
require_once(DOL_DOCUMENT_ROOT.'/lib/fourn.lib.php');
require_once(DOL_DOCUMENT_ROOT.'/product.class.php');
require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/html.formfile.class.php");

        $fuser = new User($db);
        $fuser->fetch('sync');
echo "...";
try {
 $soap = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.8.wsdl");

 //login
 $login_soap="XXXX";
 $pass_soap="XXXX";

 $session = $soap->login(login_soap, $pass_soap,"fr", false);
 echo "login successfull\n";


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
    if($num==0 ){ //facture n'existe pas
        $db->begin();
        $result[$i]->info=$soap->billingInvoiceInfo($session, $r->billnum, null,
$r->billingCountry); //on recupere les details
        $r=$result[$i];
        $facfou = new FactureFournisseur($db);

        $facfou->ref           = $r->billnum;
        $facfou->socid         = $id_ovh;
        $facfou->libelle       = "";
        $facfou->date          = strtotime($r->date);
        $facfou->date_echeance = strtotime($r->date);
        $facfou->note_public   = '';

        $facid = $facfou->create($fuser);
        if ($facid > 0)
        {
        foreach($r->info->details as $d)
        {
            $label='<strong>ref :'.$d->service.'</strong><br />'.$d->description.'<br
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
        else {
                $db->rollback();
                echo "ERROR: ".$facfou->error."\n";
        }
    }
    else{
        $row=$db->fetch_array($resql);
        $facid=$row['rowid'];
        $facfou = new FactureFournisseur($db);
        echo "fetching fact $facid ...\n";
        if($facfou->fetch($facid))
        {
        if($facfou->fk_statut ==0)
        {
                $upload_dir =
$conf->fournisseur->facture->dir_output.'/'.get_exdir($facfou->id,2).$facfou->id;

                if (! is_dir($upload_dir)) create_exdir($upload_dir);

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
                /*
                        if (dol_move_uploaded_file($_FILES['userfile']['tmp_name'],
$upload_dir . '/' . $_FILES['userfile']['name'],0) > 0)
                        {
                            $mesg = '<div
class="ok">'.$langs->trans('FileTransferComplete').'</div>';
                            //print_r($_FILES);
                        }
                        else
                        {
                            // Echec transfert (fichier dépassant la limite ?)
                            $mesg = '<div
class="error">'.$langs->trans('ErrorFileNotUploaded').'</div>';
                            // print_r($_FILES);
                        }
                */
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
 $soap->logout($session);
 echo "logout successfull\n";

} catch(SoapFault $fault) {
 echo $fault;
}

?>
