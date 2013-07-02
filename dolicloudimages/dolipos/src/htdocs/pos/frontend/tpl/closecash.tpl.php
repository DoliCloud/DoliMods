<?php




$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/class/ticket.class.php');
dol_include_once('/pos/backend/class/cash.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
global $langs, $db;

$langs->load("main");
$langs->load("pos@pos");
header("Content-type: text/html; charset=".$conf->file->character_set_client);
$id=GETPOST('id');
$terminal=GETPOST('terminal');
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

<?php

		// Cash
		
		$sql = "select fk_user, date_c";
    	$sql .=" from ".MAIN_DB_PREFIX."pos_control_cash";
    	$sql .=" where rowid = ".$id." and fk_cash=".$terminal;
    	$result=$db->query($sql);
		
		if ($result)
		{
			$objp = $db->fetch_object($result);
        	$date = $objp->date_c;
        	$fk_user = $objp->fk_user;
        }

	?>



<div class="entete">
	<div class="logo">
	<?php print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=companylogo&amp;file='.urlencode('/thumbs/'.$mysoc->logo_small).'">'; ?>
	</div>
	<div class="infos">
		<p class="adresse"><?php echo $mysoc->name; ?><br>
		<?php echo $mysoc->address; ?><br>
		<?php echo $mysoc->zip.' '.$mysoc->town; ?></p>
		<?php
			print '<p>'.$langs->trans("CloseCashReport").': '.$id.'<br>';
			$cash = new Cash($db);
			$cash->fetch($terminal);
			print $langs->trans("Terminal").': '.$cash->name.'<br>';
			
			$userstatic=new User($db);
			$userstatic->fetch($fk_user);
			print $langs->trans("User").': '.$userstatic->nom.'</p>';
			print '<p class="date_heure">'.dol_print_date($db->jdate($date),'dayhourtext').'</p>';
		?>
	</div>
</div>
<p><?php print $langs->trans("TicketsCash"); ?></p>
<table class="liste_articles">
	<tr class="titres"><th><?php print $langs->trans("Ticket"); ?></th><th><?php print $langs->trans("Total"); ?></th></tr>

	<?php

		// Cash
		
		$sql = "select t.ticketnumber, t.total_ttc, t.type";
    	$sql .=" from ".MAIN_DB_PREFIX."pos_ticket as t";
    	$sql .=" where t.fk_control = ".$id." and t.fk_cash=".$terminal." and t.fk_mode_reglement=".$cash->fk_modepaycash." and t.fk_statut > 0";
    	
    	$sql .= " union select f.facnumber, f.total_ttc, f.type";
    	$sql .=" from ".MAIN_DB_PREFIX."pos_facture as pf,".MAIN_DB_PREFIX."facture as f ";
    	$sql .=" where pf.fk_control_cash = ".$id." and pf.fk_cash=".$terminal." and f.fk_mode_reglement=".$cash->fk_modepaycash. " and pf.fk_facture = f.rowid and f.fk_statut > 0";
    	
    	$result=$db->query($sql);
		
		if ($result)
		{
			$num = $db->num_rows($result);
			if($num>0)
			{
	            $i = 0;
	            $subtotalcash=0;
	            while ($i < $num)
	            {
	            	$objp = $db->fetch_object($result);
	            	if($objp->type > 0)$objp->total_ttc= $objp->total_ttc * -1;
	            	echo ('<tr><td align="left">'.$objp->ticketnumber.'</td><td align="right">'.price($objp->total_ttc).'</td></tr>'."\n");
	            	$i++;
	            	$subtotalcash+=$objp->total_ttc;
	            }
			}
			else
			{
				echo ('<tr><td align="left">'.$langs->Trans("NoTickets").'</td></tr>'."\n");
			}	
		}

	?>
</table>

<table class="totaux">
	<?php
		echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalCash").'</th><td nowrap="nowrap">'.price($subtotalcash)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
	?>
</table>

<br><br>
<p><?php print $langs->trans("TicketsCreditCard"); ?></p>
<table class="liste_articles">
	<tr class="titres"><th><?php print $langs->trans("Ticket"); ?></th><th><?php print $langs->trans("Total"); ?></th></tr>

	<?php

		// Credit card
		$sql = "select t.ticketnumber, t.total_ttc, t.type";
    	$sql .=" from ".MAIN_DB_PREFIX."pos_ticket as t";
    	$sql .=" where t.fk_control = ".$id." and t.fk_cash=".$terminal." and t.fk_mode_reglement=".$cash->fk_modepaybank. " and t.fk_statut > 0";
    	 
    	$sql .= " union select f.facnumber, f.total_ttc, f.type";
    	$sql .=" from ".MAIN_DB_PREFIX."pos_facture as pf,".MAIN_DB_PREFIX."facture as f ";
    	$sql .=" where pf.fk_control_cash = ".$id." and pf.fk_cash=".$terminal." and f.fk_mode_reglement=".$cash->fk_modepaybank. " and pf.fk_facture = f.rowid and f.fk_statut > 0";
    	 
    	$result=$db->query($sql);
		
		if ($result)
		{
			$num = $db->num_rows($result);
			if($num>0)
			{
	            $i = 0;
	            $subtotalcard=0;
	            while ($i < $num)
	            {
	            	$objp = $db->fetch_object($result);
	            	if($objp->type > 0)$objp->total_ttc= $objp->total_ttc * -1;
	            	echo ('<tr><td align="left">'.$objp->ticketnumber.'</td><td align="right">'.price($objp->total_ttc).'</td></tr>'."\n");
	            	$i++;
	            	$subtotalcard+=$objp->total_ttc;
	            }
			}
			else
			{
				echo ('<tr><td align="left">'.$langs->Trans("NoTickets").'</td></tr>'."\n");
			}	
		}

	?>
</table>

<table class="totaux">
	<?php
		echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalCard").'</th><td nowrap="nowrap">'.price($subtotalcard)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
	?>
</table>
<br><br>
<table class="totaux">
	<?php
		echo '<tr><th nowrap="nowrap">'.$langs->trans("TotalPOS").'</th><td nowrap="nowrap">'.price($subtotalcard+$subtotalcash)." ".$langs->trans(currency_name($conf->currency))."</td></tr>\n";
	?>
</table>

<script type="text/javascript">

	window.print();

</script>

<a class="lien" href="#" onclick="javascript: window.close(); return(false);">Fermer cette fenetre</a>

</body>