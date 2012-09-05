<?php
/*   Copyright (C) 2012 Alexis José Turruella Sánchez
 Desarrollado en el mes de enero de 2012
Correo electrónico: alexturruella@gmail.com
Módulo que permite obtener los mejores 10 clientes, producto y facturas del mes año y un rango de fechas
Fichero ttindexproducto.php
*/
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
dol_include_once('/topten/class/topten.class.php');


if (!$user->rights->societe->lire) accessforbidden();
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
    print '<a href="'.dol_buildpath('/topten/ttproductodinero.php',1).'">'.$langs->trans("TTProductoDinero").'</a>';
    print '<br>';

    print "<br>";
    //print '<hr style="color: #DDDDDD;">';
    print img_picto('','puce').' '.$langs->trans("TTMENSMEJORPRODUCTOCANTIDAD")."<br>";
    print '<a href="'.dol_buildpath('/topten/ttproductocantidad.php',1).'">'.$langs->trans("TTProductoCantidad").'</a>';
    print "<br>";
    print '<br>';


    llxFooter();

}

$db->close();
?>