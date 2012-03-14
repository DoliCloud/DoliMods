<?php
/*   Copyright (C) 2012 Alexis José Turruella Sánchez
     Desarrollado en el mes de enero de 2012
     Correo electrónico: alexturruella@gmail.com
     Módulo que permite obtener los mejores 10 clientes, producto y facturas del mes año y un rango de fechas
	 Fichero ttclientefactura.php
 */
require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
dol_include_once('/topten/class/topten.class.php');


if (!$user->rights->societe->lire)
accessforbidden();
//--------------------------------------------------------------------------------------------------------------------------------------
$langs->load("companies");
$langs->load("toptenlang@topten");
//--------------------------------------------------------------------------------------------------------------------------------------
$thirdparty_static = new Societe($db);

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

 $listatoptenclientes=$TOPT->TTClienteFactura($tipotopten,$datos);
 }

//--------------------------------------------------------------------------------------------------------------------------------------
/*
 * View
 */

$now=dol_now();
$html = new Form($db);

$morejs=array("/topten/js/FusionChartsPastel.js");
llxHeader('',$langs->trans("TTtopten"),'','','','',$morejs,'',0,0);

print_fiche_titre('<a href="'.DOL_URL_ROOT.'/topten/index.php">'.$langs->trans("TTtopten").'</a> -> '.$langs->trans("TTCliente").' '.$langs->trans("TTClienteFactura"));
print img_picto($langs->trans("TTtoptenCLIENTES"),"clien@topten");
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
		print '<td width="30%">'.$langs->trans("Company").'</td>';
		print '<td width="18%">'.$langs->trans("TTCANTFACTURAS").'</td>';
		print '<td width="25%">'.$langs->trans("Country").'</td>';
		print '<td >'.$langs->trans("EMail").'</td>';

	 print '</tr>';
	 $cantidadobtenida=sizeof($listatoptenclientes);
	 $faltantepara10=9-sizeof($listatoptenclientes);
	 for($i=0;$i<$cantidadobtenida;$i++)
	 {
	 $var=!$var;
	 print "<tr $bc[$var]>";

		    $thirdparty_static->id=$listatoptenclientes[$i][1]->id;
			$thirdparty_static->nom=$listatoptenclientes[$i][1]->nom;
			$thirdparty_static->client=$listatoptenclientes[$i][1];

		print '<td >'.($i+1).'</td>';
		print '<td nowrap="nowrap">'.$thirdparty_static->getNomUrl(1,'',30).'</td>';
		print '<td >'.($listatoptenclientes[$i][0]->cantidad_facturas).'</td>';
		print '<td >';
		//var_dump($listatoptenclientes[$i][1]);
		$codigo_pais=$listatoptenclientes[$i][1]->pays_code;

		if ($codigo_pais)
         {
         $img=picto_from_langcode($codigo_pais);
         print $img?$img.' ':'';
         print getCountry($codigo_pais,1);
        }
		print '</td>';

		print '<td >'.dol_print_email($listatoptenclientes[$i][1]->email,0,$listatoptenclientes[$i]->id,'AC_EMAIL').'</td>';

	 print '</tr>';
	 }
	 //-----------------mostra hasta el 10 por si no existen un top ten
	 for($i=$cantidadobtenida;$i<10;$i++)
	 {
	 $var=!$var;
	 print "<tr $bc[$var]>";
		print '<td >'.($i+1).'</td>';
		print '<td nowrap="nowrap"> </td>';
		print '<td > </td>';
		print '<td > </td>';
		print '<td > </td>';
	 print '</tr>';
	 }
   print '</table>';

if(isset($mensaje_error))
 print $mensaje_error;

$db->close();

llxFooter('$Date: 2012/01/28 10:00:00 $ - $Revision: 1.0 $');
}
?>