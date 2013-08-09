<?php



$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/class/ticket.class.php');
dol_include_once('/pos/backend/class/cash.class.php');
dol_include_once('/pos/backend/class/place.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once (DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
global $langs;

$langs->load("main");
$langs->load("pos@pos");
header("Content-type: text/html; charset=".$conf->file->character_set_client);
$id=GETPOST('id');
?>
<html>
<head>
<title>Print ticket</title>

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
	
	.note{
		float: right;
		font-size: 12px;
		width: 100%;
		text-align: center;
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
		<?php echo $mysoc->address; ?><br>
		<?php echo $mysoc->zip.' '.$mysoc->town; ?><br><br>
		
		<?php
		
			// Variables
		
			$object=new Ticket($db);
			$result=$object->fetch($id,$ref);
			
			$userstatic=new User($db);
			$userstatic->fetch($object->user_close);
			print $langs->trans("Vendor").': '.$userstatic->nom;?><br><br>
			<?php if(!empty($object->fk_place))
			{
				$place = new Place($db);
				$place->fetch($object->fk_place);
				print $langs->trans("Place").': '.$place->name."</p>";
			}
			
			// Recuperation et affichage de la date et de l'heure
			$now = dol_now();
			$label=$object->ref;
			$facture = new Facture($db);
			if($object->fk_facture){
				$facture->fetch($object->fk_facture);
				$label=$facture->ref;
			}
			
			print '<p class="date_heure" align="right">'.$label."<br>".dol_print_date($object->date_closed,'dayhourtext').'</p><br>';
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
					echo ('<tr><td align="left">'.$line->libelle.'</td><td align="right">'.$line->qty." * ".price($line->price).'</td><td align="right">'.$line->remise_percent.'%</td><td class="total">'.price($totalline).' '.$langs->trans(currency_name($conf->currency)).'</td></tr>'."\n");
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
		/*if($object->remise_percent>0)
		{
			echo '<tr><th nowrap="nowrap">'.$langs->trans("Subtotal").'</th><td nowrap="nowrap">'.price($subtotal)."</td></tr>\n";
			echo '<tr><th nowrap="nowrap">'.$langs->trans("DiscountGlobal").'</th><td nowrap="nowrap">'.$object->remise_percent."%</td></tr>\n";
		}*/
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
		$terminal->fetch($object->fk_cash);
		
		if ($object->type==0)
		{
			echo '<tr><th nowrap="nowrap">'.$langs->trans("Payment").'</th><td nowrap="nowrap">'.$terminal->select_Paymentname($object->mode_reglement_id)."</td></tr>\n";
			if($object->mode_reglement_id==$terminal->fk_modepaycash)
			{
				echo '<tr><th nowrap="nowrap">'.$langs->trans("CustomerPay").'</th><td nowrap="nowrap">'.price($object->customer_pay)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
				$difpayment=$object->total_ttc - $object->customer_pay;
				if($difpayment<0)
					echo '<tr><th nowrap="nowrap">'.$langs->trans("CustomerRet").'</th><td nowrap="nowrap">'.price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
				else
					echo '<tr><th nowrap="nowrap">'.$langs->trans("CustomerDeb").'</th><td nowrap="nowrap">'.price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
			}
		}
	?>
</table>

<div class="note"><p><?php print $conf->global->POS_PREDEF_MSG; ?> </p></div>

<script type="text/javascript">

	window.print();

</script>

<a class="lien" href="#" onclick="javascript: window.close(); return(false);">Fermer cette fenetre</a>

</body>