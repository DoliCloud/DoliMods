<?php
/* Copyright (C) 2001-2007	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2014	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2019 		Massaoud Bouzenad		<massaoud@dzprod.net>
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
 */

/**
 * \file htdocs/verifystock/index.php
 * \ingroup product
 * \brief Page to verify products stock infos
 */

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

require_once DOL_DOCUMENT_ROOT . '/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

if (! empty($conf->categorie->enabled))
	require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';



$langs->load("products");

$action 		= GETPOST('action');
$display 		= GETPOST('display');			// Mode Affichage Toutes les lignes ou seulement les erreurs.

$fk_entrepot	= GETPOST('fk_product', 'int');
$fk_soc 		= GETPOST('fk_soc', 'int');
$stockDate		= GETPOST('stockDate');

$transmit		= GETPOST('transmit');



// Security check
$fieldvalue = (! empty($id) ? $id : (! empty($ref) ? $ref : ''));
$fieldtype = (! empty($ref) ? 'ref' : 'rowid');
if ($user->societe_id)
	$socid = $user->societe_id;

$result = restrictedArea($user, 'produit|service', $fieldvalue, 'product&product', '', '', $fieldtype);



/**
 * Actions
 */

if( ! empty($action))
{
	# Construction du tableau fk_product -> ref
	$req = "SELECT ref, rowid FROM ".MAIN_DB_PREFIX."product";
	$res = $db->query($req);

	if($res)
	{
		$num = $db->num_rows($res);
		$i = 0;

		if($num < 1)
			$err_msg = "Aucun produit enregistré!";
		else
		{
			while($i < $num)
			{
				$obj = $db->fetch_object($res);
				$fk_product 	= $obj->rowid;
				$ref 		 	= $obj->ref;
				$prod_ref[$fk_product] = $ref;

				$i++;
			}
		}

	}
}

if($action == 'verifyStock')
{
	# Recalcul des données stock à partir des mouvements de stocks.
	$req = "SELECT fk_product, fk_entrepot, SUM(value) AS stock FROM `".MAIN_DB_PREFIX."stock_mouvement` GROUP BY fk_product, fk_entrepot";
	$res = $db->query($req);

	if($res)
	{
		$num = $db->num_rows($res);
		$i = 0;

		if($num < 1)
			$err_msg = "Aucun mouvement de stock détecté!";
		else
		{
			while($i < $num)
			{
				$obj = $db->fetch_object($res);
				$fk_product 	= $obj->fk_product;
				$fk_entrepot 	= $obj->fk_entrepot;
				$stock 			= $obj->stock;

				// Pour llx_product_stock.reel
				$stock_depot_calc[$fk_product][$fk_entrepot] = $stock;

				// Pour llx_product.stock
				$stock_global_calc[$fk_product] += $stock;

				$i++;
			}
		}
	}
	else
	{
		$err_msg = "DB Error: ".$db->errno;
	}

	# Récupération des données de stocks enregistrées.
	$req = "SELECT rowid, stock FROM `".MAIN_DB_PREFIX."product`";
	$res = $db->query($req);

	if($res)
	{
		$num = $db->num_rows($res);
		$i = 0;

		if($num < 1)
			$err_msg = "Impossible de récupérer les données de stock!";
		else
		{
			while($i < $num)
			{
				$obj = $db->fetch_object($res);
				$fk_product 	= $obj->rowid;
				$stock 			= $obj->stock;

				// Données enregistrées
				$stock_global[$fk_product] += $stock;

				$i++;
			}
		}

	}


	# Comparaison:
	$status = "OK";
	if(is_array($stock_global) AND is_array($stock_global_calc))
	{
		foreach($stock_global AS $fk_product => $stock)
		{
			if($stock_global_calc[$fk_product] != $stock)
				$status = "KO";
		}
	}
	else
	{
		$status = 'ko';
		$err_msg = "Impossible de reconstruire les données de stock!";
	}

	$bilan = ($status == "OK")?'<p class="ok"><i class="fa fa-check-circle" style="margin-right: 20px; font-size: 1.2em;"></i>'.$langs->trans('stockDataOk971635').'</p>':'<p class="ko"><i class="fa fa-exclamation-triangle" style="margin-right: 20px; font-size: 1.2em;"></i>'.$langs->trans('stockDataFail971635').'</p>';
}

if($action == 'getPastStock')
{
	# On convertit le format de la date:
	list($day, $month, $year) = explode("/", $stockDate);
	if(checkdate($month, $day, $year))
	{
		$date = $year."-".$month."-".$day;
		$req = "SELECT fk_product, SUM(value) AS stock FROM ".MAIN_DB_PREFIX."stock_mouvement WHERE date(datem) < '{$date}' GROUP BY fk_product";
		$res = $db->query($req);

		if($res)
		{
			$num = $db->num_rows($res);
			$i = 0;

			if($num < 1)
				$err_msg = "Impossible de reconstruire les stocks, aucun mouvement détecté!";
			else
			{
				while($i < $num)
				{
					$obj = $db->fetch_object($res);
					$fk_product 	= $obj->fk_product;
					$stock 			= $obj->stock;

					//State of stock at requested date
					$stock_to_date[$fk_product] = $stock;

					$i++;
				}
			}


			$bilan = '<p class="ok">'.$langs->trans('pastStockLevel971635').' '.$stockDate.'</p>';

			# Si demande de téléchargement du fichier etat du stock recalcule
			if($transmit == 'file')
			{
				$eol = "\r\n";
				$filename = $langs->trans('stockLevelFilename971635').'_'.$stockDate.".csv";
				$f = fopen('php://memory', 'w');
				fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
				fwrite($f, $eol);

				// fputcsv($f, array_keys($journal[0]), ';'); no headers for koala.

				# En-tête:
				fputcsv($f, array('ID', $langs->trans('codeArt971635'), $langs->trans('stockCalc971635')));
				if("\n" != $eol && 0 === fseek($f, -1, SEEK_CUR)) {
					    fwrite($f, $eol);
					}
			    // loop over the input array
			    foreach ($prod_ref as $fk_product => $ref) {
			    	$stock_calc = price2num((float) $stock_to_date[$fk_product], 'MS');

			    	$line = array($fk_product, $ref, $stock_calc);

			        // generate csv lines from the inner arrays
			        fputcsv($f, $line);
			        if("\n" != $eol && 0 === fseek($f, -1, SEEK_CUR)) {
					    fwrite($f, $eol);
					}

			    }
			    // reset the file pointer to the start of the file
			    fseek($f, 0);
			    // tell the browser it's going to be a csv file
			    header('Content-Type: application/csv; charset=UTF-8');
			    // tell the browser we want to save it instead of displaying it
			    header('Content-Disposition: attachment; filename="'.$filename.'";');
			    // make php send the generated csv lines to the browser
			    fpassthru($f);

			    // Enregistrement de l'édition du journal en base:

			    die('');
			}
		}
		else
		{
			// Log error;
		}
	}

}

/*
 * View
 */

$htmlother=new FormOther($db);
$form=new Form($db);

# $title = "Vérification des données stock produits";
$title = $langs->trans("CheckStock");

llxHeader('', $title);

print load_fiche_titre($langs->trans("MainpageTitle971635"));


?>
<style type="text/css">
/*
.button {
	margin: auto;
	padding: 4px;
	width: auto;
	height: 30px;
	font-size: 1em;
	background: #70BDC2;
	border-radius: 4px;
	color: #FFF;
	border: 1px #F1F1F1 solid;
	cursor: pointer;
}
*/
.red {
	background: #D81939;
}

.ok {
	background: #82C91E;
	border: 2px solid #4CAF50;
	border-radius: 6px;
	padding: 2px;
	color: #FFF;
	font-size: 1.4em;
}

.ko {
	background: #F1A489;
	border: 2px solid #F25544;
	border-radius: 6px;
	padding: 2px;
	color: #FFF;
	font-size: 1.4em;
}
</style>
<script>
  $( function() {
  		$( "#stockDate" ).datepicker();
      	$( "#stockDate" ).datepicker( "option", "dateFormat",'dd/mm/yy');
  } );
</script>
<div style="display: flex; flex-direction: column; justify-content: flex-start;">
	<div style="display: flex; justify-content: flex-start; align-items: center; max-height: 40px; margin-bottom: 20px;">
		<div style="display: flex; justify-content: flex-start; align-items: center;">
			<i class="fa fa-list-alt" style="font-size: 1.6em; margin-right: 20px;"></i><a href="?action=verifyStock" class="button"><?=$langs->trans('launchCheck971635')?></a>
		</div>
		<div style="display: flex; justify-content: flex-start; align-items: center; margin-left: 40px;">
			<i class="fa fa-history" style="font-size: 1.6em; margin-right: 20px;"></i>
			<div>
			<form name="getpastStock" action="" method="GET">
				<input type="hidden" name="action" value="getPastStock" />
				<label for="date_field">Date</label>
          		<input class="date" type="text" id="stockDate" name="stockDate" value="">
          		<input type="submit" class="button" value="<?=$langs->trans('getPastStock971635')?>" />
          	</form>
          	</div>
		</div>
	</div>
	<?php
	echo $bilan;

	if($action == 'verifyStock' AND $status == 'KO')
	{
		# On affiche les listing des erreurs:
		?>
		<div>
			<table class="noborder">
				<tbody>
				<tr class="liste_titre">
					<th>ID</th><th><?=$langs->trans('product971635')?></th><th><?=$langs->trans('stockInBase971635')?></th><th><?=$langs->trans('stockCalc971635')?></th><th>Diff.</th><th>Status</th>
				</tr>
				<?php
				foreach($stock_global AS $fk_product => $stock)
				{
					$ref 		= $prod_ref[$fk_product];
					$stock_calc = price2num((float) $stock_global_calc[$fk_product], 'MS');
					$diff 		= price2num((float) ($stock_calc - $stock), 'MS');
					$hilite 	= ($diff)?' style="background: #FF0000; color: #FFF;"':'';

					$status 	= (empty($hilite))?'<i class="fa fa-check" style="font-size: 1.4em;"></i>':'<i class="fa fa-exclamation-triangle" style="font-size: 1.4em;"></i>';

					# Mode d'affichage TOUT ou only Errors
					/*
					if(ABS($diff) == 0)
						continue;
						*/

				?>
				<tr<?=$hilite?>>
					<td><?=$fk_product?></td><td><?=$ref?></td><td><?=$stock?></td><td><?=$stock_calc?></td><td style="text-align: center;"><?=$diff?></td><td><?=$status?></td>
				</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div>

		<?php
	}
	elseif($action == 'getPastStock')
	{
	?>
	<div style="display: flex; align-items: center; justify-content: flex-start;">
		<i class="fa fa-download" style="font-size: 1.6em; margin-right: 20px;"></i>
		<span>
			<a href="?action=getPastStock&transmit=file&stockDate=<?=urlencode($stockDate)?>"><?=$langs->trans('dlStockFile971635')?></a>
		</span>
	</div>
	<div>
		<table class="noborder">
			<tbody>
			<tr class="liste_titre">
				<th>ID</th><th>Article</th><th>Stock calculé</th>
			</tr>
			<?php
			foreach($prod_ref AS $fk_product => $ref)
			{
				$stock_calc = price2num((float) $stock_to_date[$fk_product], 'MS');
			?>
			<tr<?=$hilite?>>
				<td><?=$fk_product?></td><td><?=$ref?></td><td><?=$stock_calc?></td>
			</tr>
			<?php
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
	}
	?>

</div>


<?php
dol_htmloutput_mesg($err_msg);

llxFooter();
$db->close();