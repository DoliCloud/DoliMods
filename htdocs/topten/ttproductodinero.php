<?php
/*   Copyright (C) 2012 Alexis José Turruella Sánchez
     Desarrollado en el mes de enero de 2012
     Correo electrónico: alexturruella@gmail.com
     Módulo que permite obtener los mejores 10 clientes, producto y facturas del mes año y un rango de fechas
	 Fichero ttproductodinero.php
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
dol_include_once('/topten/class/topten.class.php');


if (!$user->rights->societe->lire)
accessforbidden();
//--------------------------------------------------------------------------------------------------------------------------------------
$langs->load("products");
$langs->load("toptenlang@topten");
//--------------------------------------------------------------------------------------------------------------------------------------
$product_static = new Product($db);

$max=10;
//--------------------------------------------------------------------------------------------------------------------------------------
/*
 * Actions
 */
$TOPT=new TOPTEN($db);
 if(isset($_POST['tipotopten']))
 {
	$tipotopten= $_POST['tipotopten'];
    switch($tipotopten)
	{
		case "ttanual":
		{
			$annoform1 = $_POST['form1anno'];
			$datos=array();
			$datos[0]=$annoform1;
			break;
		}
		case "ttmensual":
		{
			$mes = $_POST['form2mes'];
			$annoform2 = $_POST['form2anno'];
			$datos=array();
			$datos[0]=$mes;
			$datos[1]=$annoform2;
			break;
		}
		case "ttentrefecha":
		{
			//validar que la fecha de inicio debe ser menor que la fecha final
            $fecha_inicial=$_POST['form3fechainicialyear']."-".$_POST['form3fechainicialmonth']."-".$_POST['form3fechainicialday'];
			$fecha_final=$_POST['form3fechafinalyear']."-".$_POST['form3fechafinalmonth']."-".$_POST['form3fechafinalday'];
			$datos=array();
			$datos[0]=$fecha_inicial;
			$datos[1]=$fecha_final;
			break;
		}
		default:
		{
			$mensaje_error='<div class="error">'.$langs->trans("TTOPERINVALID").'</div>';
			break;
		}
	}

 $listatoptenproductos=$TOPT->TTProductoDinero($tipotopten,$datos);
 }

//--------------------------------------------------------------------------------------------------------------------------------------
/*
 * View
 */

$now=dol_now();
$html = new Form($db);

$morejs=array("/topten/js/FusionChartsPastel.js");
llxHeader('',$langs->trans("TTtopten"),'','','','',$morejs,'',0,0);

print_fiche_titre('<a href="'.DOL_URL_ROOT.'/topten/index.php">'.$langs->trans("TTtopten").'</a> -> '.$langs->trans("TTProducto").' '.$langs->trans("TTProductoDinero"));
print img_picto($langs->trans("TTtoptenPRODUCTOS"),"prod@topten");
if($conf->topten->enabled)
{
print '<table  width="100%" class="notopnoleftnoright">';

print '<tr>';
print '<td>';

// Buscar cantidad de registros de facturas por cliente
print '<table class="notopnoleftnoright" width="100%" border="0">';

$var=!$var;
print '<tr '.$bc[$var].'>';

print '<td colspan="2" align="center">';

print '</center>';
print '</form>';
//print img_picto("texto","edit");

print '</td>';
print '</tr>';
print '</table>';


print '<table width="100%" class="noborder" border="0" cellspacing="0" cellpadding="0">';
 	print ' <tr align="center" class="liste_titre">';

		print '<td width="33%">'.$langs->trans("TTANUAL").'</td>';
		print '<td width="33%">'.$langs->trans("TTMENSUAL").'</td>';
		print '<td>'.$langs->trans("TTENTREFECHA").'</td>';

	 print '</tr>';
	 print '<tr align="center">';

	    //anual---------------------------------------------------------
	   print '<td>';
	   $claseactivada=($tipotopten=='ttanual'?'title="activado"':'');
	   print '<fieldset '.$claseactivada.'>';
	   print '<form action="" name="form1" method="post">';
       print '<input type="hidden" value="ttanual" name="tipotopten"/>';
	   $TOPT->select_year($annoform1,'form1anno',0,4,0);
	   print '<hr>';
	   print '<input type="submit" class="button" name="boton" value="'.$langs->trans("TTVERANUAL").'" />';
	   print '</form>';
	   print '</fieldset>';
	   print '</td>';

	   //mensual---------------------------------------------------------
	   print '<td>';
	   $claseactivada=($tipotopten=='ttmensual'?'title="activado"':'');
	   print '<fieldset '.$claseactivada.'>';
	   print '<form action="" name="form2" method="post">';
       print '<input type="hidden" value="ttmensual" name="tipotopten"/>';
	   print $TOPT->select_month($mes,'form2mes');
	   $TOPT->select_year($annoform2,'form2anno',0,4,0);
	   print '<hr>';
	   print '<input type="submit" class="button" name="boton" value="'.$langs->trans("TTVERMENSUAL").'" />';
	   print '</form>';
	   print '</fieldset>';
	   print '</td>';

	    //entre fechas---------------------------------------------------------
	   print '<td>';
	   $claseactivada=($tipotopten=='ttentrefecha'?'title="activado"':'');
	   print '<fieldset '.$claseactivada.'>';
	   print '<form action="" name="form3" method="post">';
       print '<input type="hidden" value="ttentrefecha" name="tipotopten"/>';
	   $html->select_date($fecha_inicial,'form3fechainicial',0,0,0,"fecha",1);
	   print ' '.$langs->trans("TTHASTA").' ';
	   $html->select_date($fecha_final,'form3fechafinal',0,0,0,"fecha",1,1);
	   print '<hr>';
	   print '<input type="submit" class="button" name="boton" value="'.$langs->trans("TTVERRANGOFECHA").'" />';
	   print '</form>';
	   print '</fieldset>';
	   print '</td>';

	  print '</tr>';
    print '</table>';
//-----------------------el resultado de la búsqueda-------------------------------
print $langs->trans("TTtopten");
   print '<table width="100%" class="noborder" border="0" cellspacing="0" cellpadding="0">';
 	print ' <tr class="liste_titre">';

		print '<td width="2%">'.$langs->trans("TTNUMTOP").'</td>';
		print '<td width="30%">'.$langs->trans("Product").'</td>';
		print '<td width="18%">'.$langs->trans("TTImporte").'</td>';
		print '<td width="25%">'.$langs->trans("Country").'</td>';
		print '<td >'.$langs->trans("TTESTADOPRODUCTO").'</td>';

	 print '</tr>';
	 $cantidadobtenida=sizeof($listatoptenproductos);
	 $faltantepara10=9-sizeof($listatoptenproductos);
	 for($i=0;$i<$cantidadobtenida;$i++)
	 {
	 $var=!$var;
	 print "<tr $bc[$var]>";

		    $product_static->id=$listatoptenproductos[$i][1]->id;
			$product_static->ref=$listatoptenproductos[$i][1]->ref;
			$product_static->type=$listatoptenproductos[$i][1]->fk_product_type;

		print '<td >'.($i+1).'</td>';
		print '<td class="nowrap">'.$product_static->getNomUrl(1,'',30).'</td>';
		print '<td >'.price($listatoptenproductos[$i][0]->dinero_recaudado).'</td>';
		print '<td >';
		//var_dump($listatoptenproductos[$i][1]);
		$codigo_pais=$listatoptenproductos[$i][1]->country_code;

		if ($codigo_pais)
         {
         $img=picto_from_langcode($codigo_pais);
         print $img?$img.' ':'';
         print getCountry($codigo_pais,1);
        }
		print '</td>';

		print '<td >'.$product_static->LibStatut($listatoptenproductos[$i][1],5,0).'</td>';

	 print '</tr>';
	 }
	 //-----------------mostra hasta el 10 por si no existen un top ten
	 for($i=$cantidadobtenida;$i<10;$i++)
	 {
	 $var=!$var;
	 print "<tr $bc[$var]>";
		print '<td >'.($i+1).'</td>';
		print '<td class="nowrap"> </td>';
		print '<td > </td>';
		print '<td > </td>';
		print '<td > </td>';
	 print '</tr>';
	 }
   print '</table>';

if(isset($mensaje_error))
 print $mensaje_error;

$db->close();

llxFooter();
}
?>