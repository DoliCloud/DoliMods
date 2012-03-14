<?php
/*   Copyright (C) 2012 Alexis José Turruella Sánchez
     Desarrollado en el mes de enero de 2012
     Correo electrónico: alexturruella@gmail.com
     Módulo que permite obtener los mejores 10 clientes, producto y facturas del mes año y un rango de fechas
	 Fichero ttindexproducto.php
 */
require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
dol_include_once('/topten/class/topten.class.php');


if (!$user->rights->societe->lire)
accessforbidden();
//--------------------------------------------------------------------------------------------------------------------------------------

$langs->load("toptenlang@topten");
//--------------------------------------------------------------------------------------------------------------------------------------

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
		print img_picto($langs->trans("TTLOSMEJORES"),"log@topten");
		print '</div>';
if($conf->topten->enabled)
{

print "<br>";
print "<br>";
//print '<hr style="color: #DDDDDD;">';
print img_picto('','puce').' '.$langs->trans("TTMENSMEJORPRODUCTODINERO")."<br>";
print '<a href="'.DOL_URL_ROOT.'/topten/ttproductodinero.php">'.$langs->trans("TTProductoDinero").'</a>';
print '<br>';

print "<br>";
//print '<hr style="color: #DDDDDD;">';
print img_picto('','puce').' '.$langs->trans("TTMENSMEJORPRODUCTOCANTIDAD")."<br>";
print '<a href="'.DOL_URL_ROOT.'/topten/ttproductocantidad.php">'.$langs->trans("TTProductoCantidad").'</a>';
print "<br>";
print '<br>';

$db->close();

llxFooter('$Date: 2012/01/28 10:00:00 $ - $Revision: 1.0 $');
}
?>