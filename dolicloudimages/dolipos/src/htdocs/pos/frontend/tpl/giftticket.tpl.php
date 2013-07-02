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
<h3>
<?php 
print $langs->trans("GiftTicket");
?>
</h3>
<table class="liste_articles">
	<tr class="titres"><th><?php print $langs->trans("Label"); ?></th><th><?php print $langs->trans("Qty")?></th></tr>

	<?php
		
		if ($result)
		{
			$object->getLinesArray();
			if (! empty($object->lines))
			{
				foreach ($object->lines as $line)
				{
					$totalline= $line->qty*$line->subprice;
					echo ('<tr><td align="left">'.$line->libelle.'</td><td align="right">'.$line->qty."</td></tr>\n");
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
		
		$terminal = new Cash($db);
		$terminal->fetch($object->fk_cash);
		
		if ($object->type==0)
		{
			echo '<tr><th nowrap="nowrap">'.$langs->trans("Pago").'</th><td nowrap="nowrap">'.$terminal->select_Paymentname($object->mode_reglement_id)."</td></tr>\n";
			
		}
	?>
</table>


<script type="text/javascript">

	window.print();

</script>

<a class="lien" href="#" onclick="javascript: window.close(); return(false);">Fermer cette fenetre</a>

</body>