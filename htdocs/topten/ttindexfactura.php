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
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

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
    print img_picto('','puce').' '.$langs->trans("TTMENSMEJORFACTURADINERO")."<br>";
    print '<a href="'.dol_buildpath('/topten/ttfacturadinero.php',1).'">'.$langs->trans("TTFacturaDinero").'</a>';
    print '<br>';

    print "<br>";
    //print '<hr style="color: #DDDDDD;">';
    print img_picto('','puce').' '.$langs->trans("TTMENSMEJORFACTURAPRODUCTO")."<br>";
    print '<a href="'.dol_buildpath('/topten/ttfacturaproducto.php',1).'">'.$langs->trans("TTFacturaProducto").'</a>';
    print "<br>";
    print '<br>';


    llxFooter();
}

$db->close();
?>