<?php
/* Copyright (C) 2012		Juanjo Menent <jmenent@2byte.es>
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
 */

/**
 *	\file       htdocs/labelprint/product.php
 *	\ingroup    labelprint
 *	\brief      Page to list products to print
 */

$res=@include("../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../main.inc.php");                // For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
dol_include_once("/labelprint/class/labelprint.class.php");

$langs->load("products");
$langs->load("stocks");
$langs->load("labelprint@labelprint");

$action=GETPOST('action','alpha');
$confirm=GETPOST('confirm','alpha');
$id= GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$line= GETPOST('lineid','int');

if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'produit');

$mesg = '';


/*
 *	Actions
 */

// Add product to list
if ($action == 'add')
{
    $object = new Labelprint($db);
    $object->fk_product=$id;
	$object->qty = 1;
	$result = $object->create($user);	  

	if ($result < 0)
    {
    	$mesg='<div class="error">'.$object->error.'</div>';
    }
}

// Print list
if ($action == 'print')
{
    /*$pdf=new pdfLabel();
    $pdf->createPdf();
    /*$res = $pdf->createPdf();
    if ($result)
    {
    	Header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
    	exit;
    }*/
}

// Truncate list to print
if ($action == "confirm_truncate" && $confirm == 'yes')
{	
	$object = new Labelprint($db);
	$result = $object->truncate($user);
	
	if ($result > 0)
    {
    	Header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
        exit;
    }
    else
    {
		$mesg='<div class="error">'.$object->error.'</div>';
    }
}


// Add product to list
if ($action == 'delete')
{
    $object = new Labelprint($db);
	$result = $object->delete($line);
	
	if ($result > 0)
    {
		$mesg = '<font class="ok">'.$langs->trans("LineDeleted").'</font>';
    }
    else
    {
		$mesg='<div class="error">'.$object->error.'</div>';
    }
}

// Add product to list
if ($action == 'updateline')
{
	if(GETPOST('save','alpha')!='')
	{
		$qty = GETPOST('qty','int');
		$price_level = GETPOST('price_level','int');
		
	    $object = new Labelprint($db);
	    $object->fetch($line);
	    $object->qty=$qty;
	    $object->price_level=$price_level;
		$result = $object->update($user);
		
		if ($result > 0)
	    {
			$mesg = '<font class="ok">'.$langs->trans("LineUpdated").'</font>';
	    }
	    else
	    {
			$mesg='<div class="error">'.$object->error.'</div>';
	    }
	}
}

// Generate Barcode
if ($action == 'genbarcode')
{
	$prod_id=GETPOST('prod_id','int');
		
	$object = new Labelprint($db);
	$object->fetch($line);
	$result = $object->generate_barcode($prod_id);

	if ($result > 0)
	{
		$mesg = '<font class="ok">'.$langs->trans("BarcodeGenerated").'</font>';
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
	}
	
}

// Action select position object
if ($action == 'confirm_position' && $confirm != 'yes') { $action=''; }
if ($action == 'confirm_position' && $confirm == 'yes')
{
	$position=GETPOST('position','int');
	$res+=dolibarr_set_const($db,'LAB_START',$position,'chaine',0,'',$conf->entity);
	
	$pdf=new pdfLabel();
	$pdf->createPdf();
}

/*
 * View
 */

$html = new Form($db);

$product = new Product($db);
$result = $product->fetch($id,$ref);

$helpurl='EN:Module_Labels|FR:Module_Labels_FR|ES:M&oacute;dulo_Labels';
llxHeader("","",$helpurl);
dol_include_once('/labelprint/class/utils.class.php');

$form = new Form($db);

$head=product_prepare_head($product, $user);
$titre=$langs->trans("CardProduct".$product->type);
$picto=($product->type==1?'service':'product');
dol_fiche_head($head, 'labelprint', $titre, 0, $picto);

// Confirmation to delete invoice
if ($action == 'truncate')
{
	$text=$langs->trans('ConfirmTruncateList');
	$formconfirm=$html->formconfirm($_SERVER['PHP_SELF'].'?id='.$product->id,$langs->trans('TruncateList'),$text,'confirm_truncate','',0,1);
}

print $formconfirm;

print '<table class="border" width="100%">';

// Ref
print '<tr>';
print '<td width="15%">'.$langs->trans("Ref").'</td><td colspan="2">';
print $html->showrefnav($product,'ref','',1,'ref');
print '</td>';
print '</tr>';

// Label
print '<tr><td>'.$langs->trans("Label").'</td><td>'.$product->libelle.'</td>';

$isphoto=$product->is_photo_available($conf->product->dir_output);

$nblignes=5+($conf->global->PRODUIT_MULTIPRICES*($conf->global->PRODUIT_MULTIPRICES_LIMIT-1))*2;
if ($isphoto)
{
	// Photo
	print '<td valign="middle" align="center" width="30%" rowspan="'.$nblignes.'">';
	print $product->show_photos($conf->product->dir_output,1,1,0,0,0,80);
	print '</td>';
}

print '</tr>';

// MultiPrix
if (! empty($conf->global->PRODUIT_MULTIPRICES))
{
	if (! empty($socid))
	{
		$soc = new Societe($db);
		$soc->id = $socid;
		$soc->fetch($socid);

		print '<tr><td>'.$langs->trans("SellingPrice").'</td>';

		if ($product->multiprices_base_type["$soc->price_level"] == 'TTC')
		{
			print '<td>'.price($product->multiprices_ttc["$soc->price_level"]);
		}
		else
		{
			print '<td>'.price($product->multiprices["$soc->price_level"]);
		}

		if ($product->multiprices_base_type["$soc->price_level"])
		{
			print ' '.$langs->trans($product->multiprices_base_type["$soc->price_level"]);
		}
		else
		{
			print ' '.$langs->trans($product->price_base_type);
		}
		print '</td></tr>';

		// Prix mini
		print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
		if ($product->multiprices_base_type["$soc->price_level"] == 'TTC')
		{
			print price($product->multiprices_min_ttc["$soc->price_level"]).' '.$langs->trans($product->multiprices_base_type["$soc->price_level"]);
		}
		else
		{
			print price($product->multiprices_min["$soc->price_level"]).' '.$langs->trans($product->multiprices_base_type["$soc->price_level"]);
		}
		print '</td></tr>';

		// TVA
		print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($product->multiprices_tva_tx["$soc->price_level"],true).'</td></tr>';
	}
	else
	{
		for ($i=1; $i<=$conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++)
		{
            // TVA
            if ($i == 1) // We show only price for level 1
            {
                 print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($product->multiprices_tva_tx[1],true).'</td></tr>';
            }

            print '<tr><td>'.$langs->trans("SellingPrice").' '.$i.'</td>';

			if ($product->multiprices_base_type["$i"] == 'TTC')
			{
				print '<td>'.price($product->multiprices_ttc["$i"]);
			}
			else
			{
				print '<td>'.price($product->multiprices["$i"]);
			}

			if ($product->multiprices_base_type["$i"])
			{
				print ' '.$langs->trans($product->multiprices_base_type["$i"]);
			}
			else
			{
				print ' '.$langs->trans($product->price_base_type);
			}
			print '</td></tr>';

			// Prix mini
			print '<tr><td>'.$langs->trans("MinPrice").' '.$i.'</td><td>';
			if ($product->multiprices_base_type["$i"] == 'TTC')
			{
				print price($product->multiprices_min_ttc["$i"]).' '.$langs->trans($product->multiprices_base_type["$i"]);
			}
			else
			{
				print price($product->multiprices_min["$i"]).' '.$langs->trans($product->multiprices_base_type["$i"]);
			}
			print '</td></tr>';
		}
	}
}
else
{
    // TVA
    print '<tr><td>'.$langs->trans("VATRate").'</td><td>'.vatrate($product->tva_tx.($product->tva_npr?'*':''),true).'</td></tr>';

    // Price
	print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>';
	if ($product->price_base_type == 'TTC')
	{
		print price($product->price_ttc).' '.$langs->trans($product->price_base_type);
	}
	else
	{
		print price($product->price).' '.$langs->trans($product->price_base_type);
	}
	print '</td></tr>';

	// Price minimum
	print '<tr><td>'.$langs->trans("MinPrice").'</td><td>';
	if ($product->price_base_type == 'TTC')
	{
		print price($product->price_min_ttc).' '.$langs->trans($product->price_base_type);
	}
	else
	{
		print price($product->price_min).' '.$langs->trans($product->price_base_type);
	}
	print '</td></tr>';
}


// Status (to sell)
print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')'.'</td><td>';
print $product->getLibStatut(2,0);
print '</td></tr>';

print "</table>";

print "</div>";

$formquestionposition=array(
		'text' => $langs->trans("ConfirmPosition"),
		array('type' => 'text', 'name' => 'position','label' => $langs->trans("HowManyPos"), 'value' => $conf->global->LAB_START, 'size'=>5)
);


/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */

$sql = 'SELECT DISTINCT l.rowid id, l.qty, l.fk_user user_id, l.price_level, ';
$sql.= ' p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type';
$sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
$sql.= ' JOIN '.MAIN_DB_PREFIX.'labelprint as l';
$sql.= ' WHERE l.fk_product=p.rowid';

$result = $db->query($sql) ;

if ($result)
{
	$num = $db->num_rows($result);
}

//if (empty($_GET["action"]) || $_GET["action"]=='delete')
//{
	print "<div class=\"tabsAction\">";

	if ($user->rights->produit->creer || $user->rights->service->creer)
	{
		if( $num) print '<a class="butActionDelete" href="product.php?id='.$product->id.'&amp;action=truncate">'.$langs->trans("Truncate").'</a>';
		print '<a class="butAction" href="product.php?id='.$product->id.'&amp;action=add">'.$langs->trans("AddToPrint").'</a>';
		
		if( $num) print '<span id="action-position" class="butAction">'.$langs->trans('PrintLabels').'</span>'."\n";
		print $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$product->id,$langs->trans('SelectPosition'),$langs->trans('ConfirmSelectPosition'),'confirm_position',$formquestionposition,'yes','action-position',170,400);
	}

	print "</div>";
//}

print '<br>';

print '<table class="noborder" width="100%">';

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Ref").'</td>';
print '<td align="center">'.$langs->trans("Label").'</td>';
if ($conf->barcode->enabled) print '<td align="right">'.$langs->trans("BarCode").'</td>';
print '<td align="right">'.$langs->trans("SellingPrice").'</td>';
if (!empty($conf->global->PRODUIT_MULTIPRICES)) print '<td align="right">'.$langs->trans("PriceLevel").'</td>';
if ($conf->stock->enabled && $user->rights->stock->lire && $type != 1) print '<td align="right">'.$langs->trans("PhysicalStock").'</td>';
print '<td align="right">'.$langs->trans("QtyToPrint").'</td>';
print '<td align="right">'.$langs->trans("AddedBy").'</td>';
if ($user->rights->produit->creer && $action != 'editline') print '<td align="right">&nbsp;</td>';
if ($user->rights->produit->creer && $action != 'editline') print '<td align="right">&nbsp;</td>';
print '</tr>';

if ($result)
{
	$num = $db->num_rows($result);
	if ($num > 0)
	{
		
		$product_static=new Product($db);
		
		$var=True;
		$i = 0;
		while ($i < $num)
		{
			$objp = $db->fetch_object($result);
			$var=!$var;
			print "<tr $bc[$var]>";
				
			// Ref
			print '<td nowrap="nowrap">';
			$product_static->id = $objp->rowid;
			$product_static->ref = $objp->ref;
			$product_static->type = $objp->fk_product_type;
			print $product_static->getNomUrl(1,'',24);
			print "</td>";
			
			// Label
			print '<td>'.dol_trunc($objp->label,40).'</td>';
			
			// Barcode
			if ($conf->barcode->enabled)
			{
				if($objp->barcode){
					print '<td align="right">'.$objp->barcode.'</td>';
				}
				else if($conf->global->PRODUIT_DEFAULT_BARCODE_TYPE == 2){
					print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&prod_id='.$objp->rowid.'&lineid='.$objp->id.'&action=genbarcode">'.$langs->trans("GenerateBarcode").'</a></td>';
				}
				else{print '<td align="right"></td>';}
			}
			
			// Sell price
			if (empty($conf->global->PRODUIT_MULTIPRICES))
			{
			    print '<td align="right">';
    			if ($objp->price_base_type == 'TTC') print price($objp->price_ttc).' '.$langs->trans("TTC");
    			else print price($objp->price).' '.$langs->trans("HT");
    			print '</td>';
			}
			else{
				$product_static->fetch($objp->rowid);
				print '<td align="right">';
				if ($product_static->multiprices_base_type[$objp->price_level] == 'TTC') print price($product_static->multiprices_ttc[$objp->price_level]).' '.$langs->trans("TTC");
				else print price($product_static->multiprices[$objp->price_level]).' '.$langs->trans("HT");
				print '</td>';
			}
			
			// Price level
			if (!empty($conf->global->PRODUIT_MULTIPRICES)){
				if ($action == 'editline' && $user->rights->produit->creer && $line == $objp->id)
				{
					print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'" method="post">';
					
					print '<td align="right">';
					
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="updateline">';
					print '<input type="hidden" name="id" value="'.$product->id.'">';
					print '<input type="hidden" name="lineid" value="'.$line.'">';
						
					print '<input class="flat" type="text" size="2" name="price_level" value="'.$objp->price_level.'"> ';
					print '</td>';
				}
				else{
					print '<td align="right">';
					print $objp->price_level;
					print '</td>';
				}
			}
			
			
			// Show stock
			if ($conf->stock->enabled && $user->rights->stock->lire && $type != 1)
			{
				if ($objp->fk_product_type != 1)
				{
					$product_static->id = $objp->rowid;
					$product_static->load_stock();
					print '<td align="right">';
                    if ($product_static->stock_reel < $objp->seuil_stock_alerte) print img_warning($langs->trans("StockTooLow")).' ';
    				print $product_static->stock_reel;
					print '</td>';
				}
				else
				{
					print '<td>&nbsp;</td>';
				}
			}
			
			// Qty
			if ($action == 'editline' && $user->rights->produit->creer && $line == $objp->id)
			{
				print '<td align="right">';

				if (empty($conf->global->PRODUIT_MULTIPRICES)){
					print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$product->id.'" method="post">';
						
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="updateline">';
					print '<input type="hidden" name="id" value="'.$product->id.'">';
					print '<input type="hidden" name="lineid" value="'.$line.'">';
						
					print '<input type="hidden" name="price_level" value="1"> ';
						
				}
				
                print '<input class="flat" type="text" size="2" name="qty" value="'.$objp->qty.'"> ';
                print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
                print '<br><input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
                
                print '</td>';
                print '</form>';
			}
			else
				print '<td align="right">'.$objp->qty.'</td>';
			
			// User
			//print '<td align="right"><a href="'.DOL_URL_ROOT.'/user/fiche.php?id='.$objp->user_id.'">'.img_object($langs->trans("ShowUser"),'user').' '.$objp->login.'</a></td>';
			//User
			$userstatic=new User($db);
	        $userstatic->fetch($objp->user_id); 
	        print '<td align="right">'.$userstatic->getNomUrl(1).'</td>';
	        
	        
			// Actions
			if ($user->rights->produit->creer && $action != 'editline')
			{
				print '<td align="right">';
				print '<a href="'.$_SERVER["PHP_SELF"].'?action=editline&amp;id='.$product->id.'&amp;lineid='.$objp->id.'">';
				print img_edit();
				print '</a>';
				print '</td>';
			}
			
			if ($user->rights->produit->creer && $action != 'editline')
			{
				print '<td align="right">';
				print '<a href="'.$_SERVER["PHP_SELF"].'?action=delete&amp;id='.$product->id.'&amp;lineid='.$objp->id.'">';
				print img_delete();
				print '</a>';
				print '</td>';
			}

			print "</tr>";
			$i++;
		}
		$db->free($result);
		print "</table>";
		print "<br>";
	}
}
if ($mesg) print "<br>$mesg<br>";
$db->close();
?>