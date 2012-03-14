<?php
/*   Copyright (C) 2012 Alexis José Turruella Sánchez
 Desarrollado en el mes de enero de 2012
Correo electrónico: alexturruella@gmail.com
Módulo que permite obtener los mejores 10 clientes, producto y facturas del mes año y un rango de fechas
Fichero index.php
*/
require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
dol_include_once('/topten/class/topten.class.php');


if (!$user->rights->societe->lire)
accessforbidden();
//--------------------------------------------------------------------------------------------------------------------------------------
$langs->load("companies");
$langs->load("products");
$langs->load('bills');
$langs->load("toptenlang@topten");
//--------------------------------------------------------------------------------------------------------------------------------------
$thirdparty_static = new Societe($db);
$TOPT=new TOPTEN($db,1);
$mejorclientedinero=$TOPT->TTClienteDinero('ttmensual',array(0=>date('m',strtotime('now')),1=>date('Y',strtotime('now'))));
$mejorclientefactura=$TOPT->TTClienteFactura('ttmensual',array(0=>date('m',strtotime('now')),1=>date('Y',strtotime('now'))));

$mejorproductodinero=$TOPT->TTProductoDinero('ttmensual',array(0=>date('m',strtotime('now')),1=>date('Y',strtotime('now'))));
$mejorproductocantidad=$TOPT->TTProductoCantidadVendida('ttmensual',array(0=>date('m',strtotime('now')),1=>date('Y',strtotime('now'))));

$mejorfacturadinero=$TOPT->TTFacturaDinero('ttmensual',array(0=>date('m',strtotime('now')),1=>date('Y',strtotime('now'))));
$mejorfacturaproductos=$TOPT->TTFacturaTotalProductosUnidades('ttmensual',array(0=>date('m',strtotime('now')),1=>date('Y',strtotime('now'))));

$thirdparty_static = new Societe($db);
$product_static = new Product($db);
$facturestatic = new Facture($db);
//--------------------------------------------------------------------------------------------------------------------------------------
/*
 * Actions
*/


//--------------------------------------------------------------------------------------------------------------------------------------
/*
 * View
*/

$now=dol_now();
$html = new Form($db);
$formfile = new FormFile($db);
$companystatic=new Societe($db);

$morejs=array("/topten/js/FusionChartsPastel.js");
llxHeader('',$langs->trans("TTLOSMEJORMENSUAL"),'','','','',$morejs,'',0,0);

print_fiche_titre($langs->trans("TTLOSMEJORMENSUAL"));
print '<div>';
print img_picto("Los 10 mejores ","log@topten");
print '</div>';

if($conf->topten->enabled)
{

    print '<table  width="100%" class="notopnoleftnoright">';
    print '<tr>';
    print '<td width="45%">';
    //---------------------TABLA UNO CLIENTE-----------------------
    print '<table  width="100%" class="notopnoleftnoright">';
    print '<tr class="liste_titre">';
    print '<td width="70%">';
    print $langs->trans("Company");
    print '</td>';
    print '<td>';
    print $langs->trans("TTTOTALGASTADO");
    print '</td>';
    print '</tr>';
    for($i=0;$i<sizeof($mejorclientedinero);$i++)
    {
        $var=!$var;
        print "<tr $bc[$var]>";
        $thirdparty_static->id=$mejorclientedinero[$i][1]->id;
        $thirdparty_static->nom=$mejorclientedinero[$i][1]->nom;
        $thirdparty_static->client=$mejorclientedinero[$i][1];
        print '<td nowrap="nowrap">'.$thirdparty_static->getNomUrl(1,'',30).'</td>';
        print '<td>'.price($mejorclientedinero[$i][0]->total_gastado).'</td>';
        print '</tr>';
    }
    print '<tr class="liste_titre">';
    print '<td colspan="2">';
    print $langs->trans("TTClienteDinero");
    print '</td>';
    print '</tr>';

    print '</table>';
    print '</br>';
    print '</td>';

    print '<td align="center">';

    print img_picto("El cliente número 1 ","N1Cliente@topten");


    print '</td>';

    print '<td width="45%">';
    //---------------------TABLA DOS CLIENTE-----------------------
    print '<table  width="100%" class="notopnoleftnoright">';
    print '<tr class="liste_titre">';
    print '<td width="70%">';
    print $langs->trans("Company");
    print '</td>';
    print '<td>';
    print $langs->trans("TTCANTFACTURAS");
    print '</td>';
    print '</tr>';

    for($i=0;$i<sizeof($mejorclientefactura);$i++)
    {
        $var=!$var;
        print "<tr $bc[$var]>";
        $thirdparty_static->id=$mejorclientefactura[$i][1]->id;
        $thirdparty_static->nom=$mejorclientefactura[$i][1]->nom;
        $thirdparty_static->client=$mejorclientefactura[$i][1];
        print '<td nowrap="nowrap">'.$thirdparty_static->getNomUrl(1,'',30).'</td>';
        print '<td>'.($mejorclientefactura[$i][0]->cantidad_facturas).'</td>';
        print '</tr>';
    }

    print '<tr class="liste_titre">';
    print '<td colspan="2">';
    print $langs->trans("TTClienteFactura");
    print '</td>';
    print '</tr>';

    print '</table>';
    print '</br>';
    print '</td>';
    print '</tr>';

    //---------------------------------------------------------------------------------------------------
    print '<tr>';
    print '<td>';
    //---------------------TABLA UNO PRODUCTO-----------------------
    print '<table  width="100%" class="notopnoleftnoright">';
    print '<tr class="liste_titre">';
    print '<td width="70%">';
    print $langs->trans("Product");
    print '</td>';
    print '<td>';
    print $langs->trans("TTImporte");
    print '</td>';
    print '</tr>';

    for($i=0;$i<sizeof($mejorproductodinero);$i++)
    {
        $var=!$var;
        print "<tr $bc[$var]>";
        $product_static->id=$mejorproductodinero[$i][1]->id;
        $product_static->ref=$mejorproductodinero[$i][1]->ref;
        $product_static->type=$mejorproductodinero[$i][1]->fk_product_type;

        print '<td nowrap="nowrap">'.$product_static->getNomUrl(1,'',30).'</td>';
        print '<td>'.price($mejorproductodinero[$i][0]->dinero_recaudado).'</td>';
        print '</tr>';
    }

    print '<tr class="liste_titre">';
    print '<td colspan="2">';
    print $langs->trans("TTProductoDinero");
    print '</td>';
    print '</tr>';

    print '</table>';
    print '</br>';
    print '</td>';

    print '<td align="center">';
    print img_picto("El producto número 1 ","N1Producto@topten");
    print '</td>';

    print '<td>';
    //---------------------TABLA DOS PRODUCTO-----------------------
    print '<table  width="100%" class="notopnoleftnoright">';
    print '<tr class="liste_titre">';
    print '<td width="70%">';
    print $langs->trans("Product");
    print '</td>';
    print '<td>';
    print $langs->trans("TTCantidadVendida");
    print '</td>';
    print '</tr>';

    for($i=0;$i<sizeof($mejorproductocantidad);$i++)
    {
        $var=!$var;
        print "<tr $bc[$var]>";
        $product_static->id=$mejorproductocantidad[$i][1]->id;
        $product_static->ref=$mejorproductocantidad[$i][1]->ref;
        $product_static->type=$mejorproductocantidad[$i][1]->fk_product_type;

        print '<td nowrap="nowrap">'.$product_static->getNomUrl(1,'',30).'</td>';
        print '<td>'.($mejorproductocantidad[$i][0]->cantidad_vendida).'</td>';
        print '</tr>';
    }

    print '<tr class="liste_titre">';
    print '<td colspan="2">';
    print $langs->trans("TTProductoCantidad");
    print '</td>';
    print '</tr>';

    print '</table>';
    print '</br>';
    print '</td>';
    print '</tr>';

    //---------------------------------------------------------------------------------------------------
    print '<tr>';
    print '<td>';
    //---------------------TABLA UNO FACTURA-----------------------
    print '<table  width="100%" class="notopnoleftnoright">';
    print '<tr class="liste_titre">';
    print '<td width="70%">';
    print $langs->trans("Invoice");
    print '</td>';
    print '<td>';
    print $langs->trans("TTImporte");
    print '</td>';
    print '</tr>';

    for($i=0;$i<sizeof($mejorfacturadinero);$i++)
    {
        $var=!$var;
        print "<tr $bc[$var]>";
        $facturestatic->id=$mejorfacturadinero[$i][1]->id;
        $facturestatic->ref=$mejorfacturadinero[$i][1]->ref;
        $facturestatic->type=$mejorfacturadinero[$i][1]->type;

        print '<td nowrap="nowrap">'.$facturestatic->getNomUrl(1,'',30).'</td>';
        print '<td>'.price($mejorfacturadinero[$i][0]->importe).'</td>';
        print '</tr>';
    }

    print '<tr class="liste_titre">';
    print '<td colspan="2">';
    print $langs->trans("TTFacturaDinero");
    print '</td>';
    print '</tr>';

    print '</table>';
    print '</br>';
    print '</td>';
    print '<td align="center">';
    print img_picto("La factura número 1 ","N1Factura@topten");
    print '</td>';
    print '<td>';
    //---------------------TABLA DOS FACTURA-----------------------
    print '<table  width="100%" class="notopnoleftnoright">';
    print '<tr class="liste_titre">';
    print '<td width="70%">';
    print $langs->trans("Invoice");
    print '</td>';
    print '<td>';
    print $langs->trans("TTCANTPRODUCTO");
    print '</td>';
    print '</tr>';

    for($i=0;$i<sizeof($mejorfacturaproductos);$i++)
    {
        $var=!$var;
        print "<tr $bc[$var]>";
        $facturestatic->id=$mejorfacturaproductos[$i][1]->id;
        $facturestatic->ref=$mejorfacturaproductos[$i][1]->ref;
        $facturestatic->type=$mejorfacturaproductos[$i][1]->type;

        print '<td nowrap="nowrap">'.$facturestatic->getNomUrl(1,'',30).'</td>';
        print '<td>'.($mejorfacturaproductos[$i][0]->suma_productos).'</td>';
        print '</tr>';
    }

    print '<tr class="liste_titre">';
    print '<td colspan="2">';
    print $langs->trans("TTFacturaProducto");
    print '</td>';
    print '</tr>';

    print '</table>';
    print '</br>';
    print '</td>';
    print '</tr>';

    print '</table>';

    llxFooter();
}

$db->close();
?>