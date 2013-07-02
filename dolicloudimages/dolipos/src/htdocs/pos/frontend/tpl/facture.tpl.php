<?php

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
dol_include_once('/pos/backend/class/cash.class.php');
dol_include_once('/rewards/class/rewards.class.php');
global $langs,$db;

$langs->load("main");
$langs->load("pos@pos");
$langs->load("rewards@rewards");
$langs->load("bills");
header("Content-type: text/html; charset=".$conf->file->character_set_client);
$id=GETPOST('id');
?>
<html>
<head>
<title>Print facture</title>

<style type="text/css">

	body {
		font-size: 1.5em;
		position: relative;
	}

	.entete {
/* 		position: relative; */
	}

		.adresse {
/* 			float: left; */
			font-size: 12px;
		}

		.date_heure {
			position: absolute;
			top: 0;
			right: 0;
			font-size: 16px;
		}

		.infos {
			position: relative;
		}


	.liste_articles {
		width: 100%;
		border-bottom: 1px solid #000;
		text-align: center;
	}

		.liste_articles tr.titres th {
			border-bottom: 1px solid #000;
		}

		.liste_articles td.total {
			text-align: right;
		}

	.totaux {
		margin-top: 20px;
		width: 30%;
		float: right;
		text-align: right;
	}

	.lien {
		position: absolute;
		top: 0;
		left: 0;
		display: none;
	}

	@media print {

		.lien {
			display: none;
		}

	}

</style>

</head>

<body>

<div class="entete">
	<div class="logo">
	<?php print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=companylogo&amp;file='.urlencode('/thumbs/'.$mysoc->logo_small).'">'; ?>
	</div>
	<div class="infos">
		<p class="adresse"><?php echo $mysoc->name; ?><br>
		<?php echo $mysoc->idprof1;?><br>
		<?php echo $mysoc->address; ?><br>
		<?php echo $mysoc->zip.' '.$mysoc->town; ?><br><br>
		
		<?php
		
			// Variables
		
			$object=new Facture($db);
			$result=$object->fetch($id,$ref);
						
			$userstatic=new User($db);
			$userstatic->fetch($object->user_valid);
			print $langs->trans("Vendor").': '.$userstatic->nom.'<br><br>';
			
			$client=new Societe($db);
			$client->fetch($object->socid);
			print $client->nom.'<br>';
			print $client->idprof1.'<br>';
			print $client->address.'</p>';
			
			
			// Recuperation et affichage de la date et de l'heure
			$now = dol_now();
			$label=$object->type==0?$langs->trans("InvoiceStandard"):$langs->trans("InvoiceAvoir");
			print '<p class="date_heure" align="right">'.$object->ref."<br>".dol_print_date($object->date_closed,'dayhourtext').'</p><br>';
		?>
	</div>
</div>

<table class="liste_articles">
	<tr class="titres"><th><?php print $langs->trans("Label"); ?></th><th><?php print $langs->trans("Qty")."/".$langs->trans("Price"); ?></th><th><?php print $langs->trans("DiscountLineal"); ?></th><th><?php print $langs->trans("Total"); ?></th></tr>

	<?php
		
		if ($result)
		{
			$object->getLinesArray();
			if (! empty($object->lines))
			{
				$subtotal=0;
				foreach ($object->lines as $line)
				{
					$totalline= $line->qty*$line->subprice;
					echo ('<tr><td align="left">'.$line->libelle.'</td><td align="right">'.$line->qty." * ".price($line->subprice).'</td><td align="right">'.$line->remise_percent.'%</td><td class="total">'.price($line->total_ht).' '.$langs->trans(currency_name($conf->currency)).'</td></tr>'."\n");
					$subtotal+=$totalline;
				}
			}
			else 
			{
				echo ('<p>'.print $langs->trans("ErrNoArticles").'</p>'."\n");
			}
					
		}
		

	?>
</table>

<table class="totaux">
	<?php
		echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalHT").'</th><td nowrap="nowrap">'.price($object->total_ht)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
		echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalVAT").'</th><td nowrap="nowrap">'.price($object->total_tva)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
		if($object->total_localtax1!=0){
			echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalLT1ES").'</th><td nowrap="nowrap">'.price($object->total_localtax1)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
		}
		if($object->total_localtax2!=0){
			echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalLT2ES").'</th><td nowrap="nowrap">'.price($object->total_localtax2)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
		}
		echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalTTC").'</th><td nowrap="nowrap">'.price($object->total_ttc)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
		echo '<tr><td></td></tr>';
		echo '<tr><td></td></tr>';

		$terminal = new Cash($db);
		$sql = 'select fk_cash from '.MAIN_DB_PREFIX.'pos_facture where fk_facture = '.$object->id;
		$resql = $db->query($sql);
		$obj = $db->fetch_object($resql);
		$terminal->fetch($obj>fk_cash);
		if (! empty($conf->rewards->enabled)){
			$rewards = new Rewards($db);
			$points = $rewards->getInvoicePoints($object->id);
		}
		echo '<tr><th nowrap="nowrap">'.$langs->trans("Payment").'</th><td nowrap="nowrap">'.$terminal->select_Paymentname($object->mode_reglement_id)."</td></tr>\n";
		if ($object->type==0)
		{
			$sql = "SELECT SUM(amount) as amount from ".MAIN_DB_PREFIX."paiement_facture WHERE  fk_facture=".$object->id;
			$res=$db->query($sql);
			$objp=$db->fetch_object($res);
			$pay = $objp->amount;
			if (! empty($conf->rewards->enabled)){
				$usepoints= abs($rewards->getInvoicePoints($object->id,1));
				$moneypoints = abs($usepoints*$conf->global->REWARDS_DISCOUNT);//falta fer algo per aci
				echo '<tr><th nowrap="nowrap">'.$langs->trans("CustomerPay").'</th><td nowrap="nowrap">'.price($pay-$moneypoints)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
			}
			else{
				echo '<tr><th nowrap="nowrap">'.$langs->trans("CustomerPay").'</th><td nowrap="nowrap">'.price($pay)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
			}
			$difpayment=$object->total_ttc - $pay;
			if (! empty($conf->rewards->enabled)){
				if ($moneypoints>0){
					echo '<tr><th nowrap="nowrap">'.$langs->trans("RewardsDiscountDesc",$usepoints).'</th><td nowrap="nowrap">'.price($moneypoints)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
				}
			}
			
			if($difpayment<0){
				echo '<tr><th nowrap="nowrap">'.$langs->trans("CustomerRet").'</th><td nowrap="nowrap">'.price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
			}else{
				echo '<tr><th nowrap="nowrap">'.$langs->trans("CustomerDeb").'</th><td nowrap="nowrap">'.price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
			}
		}
		if ($points != 0 && ! empty($conf->rewards->enabled))
		{
			echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalPointsInvoice").'</th><td nowrap="nowrap">'.price($points)." ".$langs->trans('Points')."</td></tr>\n";
			$total_points = $rewards->getCustomerPoints($object->socid);
			echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalDispoPoints").'</th><td nowrap="nowrap">'.dol_print_date($now,'day')." ".price($total_points)." ".$langs->trans('Points')."</td></tr>\n";
		}
	?>
</table>

<script type="text/javascript">

	window.print();

</script>

<a class="lien" href="#" onclick="javascript: window.close(); return(false);">Fermer cette fenetre</a>

</body>