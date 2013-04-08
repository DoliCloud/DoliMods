<?php
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Auteur : Guilhem BORGHESI
//Création : Février 2008
//
//borghesi@unistra.fr
//
//Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
//respectant les principes de diffusion des logiciels libres. Vous pouvez
//utiliser, modifier et/ou redistribuer ce programme sous les conditions
//de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA
//sur le site "http://www.cecill.info".
//
//Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
//pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
//termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
//
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Author : Guilhem BORGHESI
//Creation : Feb 2008
//
//borghesi@unistra.fr
//
//This software is governed by the CeCILL-B license under French law and
//abiding by the rules of distribution of free software. You can  use,
//modify and/ or redistribute the software under the terms of the CeCILL-B
//license as circulated by CEA, CNRS and INRIA at the following URL
//"http://www.cecill.info".
//
//The fact that you are presently reading this means that you have had
//knowledge of the CeCILL-B license and that you accept its terms. You can
//find a copy of this license in the file LICENSE.
//
//==========================================================================

define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
dol_include_once("/opensurvey/class/opensurveysondage.class.php");
include_once('../fonctions.php');



/*
 * Actions
*/

if(!isset($_GET['numsondage']) || ! preg_match(";^[\w\d]{16}$;i", $_GET['numsondage']))
{
	header('Location: studs.php');
	exit;
}


/*
 * View
 */

$now=dol_now();

$object=new Opensurveysondage($db);
$object->fetch(0,GETPOST('numsondage'));

$nbcolonnes=substr_count($object->sujet,',')+1;
$toutsujet=explode(",",$object->sujet);
#$toutsujet=str_replace("°","'",$toutsujet);

// affichage des sujets du sondage
$input.=";";
for ($i=0;$toutsujet[$i];$i++) {
	if ($object->format=="D"||$object->format=="D+") {
		$input.=''.dol_print_date($toutsujet[$i],'dayhour').';';
	} else {
		$input.=''.$toutsujet[$i].';';
	}
}

$input.="\r\n";

if (strpos($object->sujet,'@') !== false) {
	$input.=";";
	for ($i=0;$toutsujet[$i];$i++) {
		$heures=explode("@",$toutsujet[$i]);
		$input.=''.$heures[1].';';
	}

	$input.="\r\n";
}


$sql='SELECT nom, reponses FROM '.MAIN_DB_PREFIX."opensurvey_user_studs WHERE id_sondage='" . $_GET['numsondage'] . "' ORDER BY id_users";
$resql=$db->query($sql);
if ($resql)
{
	$num=$db->num_rows($resql);
	$i=0;
	while ($i < $num)
	{
		$obj=$db->fetch_object($resql);

		// Le nom de l'utilisateur
		$nombase=str_replace("°","'",$obj->nom);
		$input.=$nombase.';';

		//affichage des resultats
		$ensemblereponses=$obj->reponses;
		for ($k=0;$k<$nbcolonnes;$k++)
		{
			$car=substr($ensemblereponses,$k,1);
			if ($car=="1")
			{
				$input.='OK;';
				$somme[$k]++;
			}
			else if ($car=="2")
			{
				$input.='KO;';
				$somme[$k]++;
			}
			else
			{
				$input.=';';
			}
		}

		$input.="\r\n";
		$i++;
	}
}


$filesize = strlen( $input );
$filename=$_GET["numsondage"]."_".dol_print_date($now,'%Y%m%d%H%M').".csv";

header( 'Content-Type: text/csv; charset=utf-8' );
header( 'Content-Length: '.$filesize );
header( 'Content-Disposition: attachment; filename="'.$filename.'"' );
header( 'Cache-Control: max-age=10' );
echo $input;

exit;
?>