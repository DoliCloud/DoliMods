<?php
/* Copyright (C) 2008-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *   	\file       htdocs/nltechno/statsannonces.php
 *		\ingroup    nltechno
 *		\brief      Page des stats annonces
 *		\version    $Id: statsannonces.php,v 1.7 2009/11/03 12:42:30 eldy Exp $
 *		\author		Laurent Destailleur
 */

include("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/dolgraph.class.php");

// Load config
$CALLFORCONFIG=1;
include_once('index.php');


// Load traductions files
//$langs->load("nltechno");
$langs->load("companies");
$langs->load("other");


// Get parameters
$socid = isset($_GET["socid"])?$_GET["socid"]:'';

// Protection
if (! $user->rights->nltechno->annonces->voir)
{
	accessforbidden();
	exit;
}


/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader();

$form=new Form($db);

$dbann=new DoliDb('mysqli', $dbhostann, $dbuserann, $dbpasswordann, $dbdatabaseann);
if (! $dbann->connected)
{
	dolibarr_print_error($dbann,"Can not connect to server ".$dbhostann." with user ".$dbuserann);
	exit;
}
if (! $dbann->database_selected)
{
	dolibarr_print_error($dbann,"Database ".$dbdatabaseann." can not be selected");
	exit;
}


// Build graph
$WIDTH=800;
$HEIGHT=160;

// Create temp directory
$dir = DOL_DATA_ROOT.'/nltechno/';
$dirtmp = 'temp/';
if (! file_exists($dir.$dirtmp))
{
	if (create_exdir($dir.$dirtmp) < 0)
	{
		$mesg = $langs->trans("ErrorCanNotCreateDir",$dir.$dirtmp);
	}
}


// Loop for each category
if (1 == 1)
{
	$datestart='2008-01-01';
	$listofcateg=array('CATEG_ANICHIENS','CATEG_ANICHATS','CATEG_ROOT');
	foreach ($listofcateg as $categ)
	{
		// Get datas
		$graph_data = array();
		$lastval=array();
		$relativepath=$dirtmp."statsannonces_".$categ.".png";

		$sql = "SELECT ".$dbann->pdate('DATE_STATS')." as d, KEY_STATS, VALUE_STATS";
		$sql.= " FROM T_STATS as s";
		$sql.= " WHERE KEY_STATS in ('".$categ."')";
		$sql.= " AND DATE_STATS >= '".$datestart."'";
		$sql.= " ORDER BY DATE_STATS, KEY_STATS";
		dolibarr_syslog("statsannonces.php sql=".$sql, LOG_DEBUG);
		$result = $dbann->query($sql);
		if ($result)
		{
			$num=$dbann->num_rows($result);
			if ($num)
			{
				$i=0;
				$oldday=0;
				while ($obj = $dbann->fetch_object($result))
				{
					if ($obj->KEY_STATS == $categ)
					{
						$val1=$obj->VALUE_STATS;
					}

					$day=dolibarr_print_date($obj->d,'%d');
					if ($day == '15') $labelx=dolibarr_print_date($obj->d,'%b');
					else $labelx='';

					if ($obj->d != $oldday && $oldday)
					{
						$graph_data[$i]=array($labelx,$val1);
						$i++;
					}

					$oldday=$obj->d;

					$lastval[5]=$lastval[4];
					$lastval[4]=$lastval[3];
					$lastval[3]=$lastval[2];
					$lastval[2]=$lastval[1];
					$lastval[1]=$lastval[0];
					$lastval[0]=$val1;
				}
			}
		}

		//require_once(DOL_DOCUMENT_ROOT."/product.class.php");
		//$product = new Product($db);
		//$result = $product->fetch(1);
		//$graph_data = $product->get_nb_propal($socid);

		$px = new DolGraph();
		$mesg = $px->isGraphKo();
		if (! $mesg)
		{
			$px->SetType('lines');
			$px->SetLegend(array("Nb annonces ".$categ));
			$px->SetLegendWidthMin(180);
			$px->SetWidth($WIDTH);
			$px->SetHeight($HEIGHT);
	//		$px->SetHorizTickIncrement(1);
			$px->SetPrecisionY(0);
	//		$px->SetShading(3);
			//print 'x '.$key.' '.$graphfiles[$key]['file'];

			// Graph
			if (is_array($graph_data))
			{
				$px->SetData($graph_data);
				$px->SetMaxValue($px->GetCeilMaxValue()<0?0:$px->GetCeilMaxValue());
				$px->SetMinValue($px->GetFloorMinValue()>0?0:$px->GetFloorMinValue());
				$result=$px->draw($dir.$relativepath);
				if ($result >= 0) $mesg = $langs->trans("ChartGenerated");
				else $mesg = '<div class="error">'.$px->error.'</div>';
			}
			else
			{
				dolibarr_print_error($db,'Error for calculating graph on key='.$key.' - '.$product->error);
			}
		}

		// Show graph
		$url=DOL_URL_ROOT.'/viewimage.php?modulepart=nltechno&file='.urlencode($relativepath);
		print '<center>';
		print '<img src="'.$url.'" alt="'.$relativepath.'">';
		print '</center>';
		print '<br><table class="border">';
		print '<tr><td>Dernieres valeures</td><td align="right">J-5</td><td align="right">J-4</td><td align="right">J-3</td><td align="right">J-2</td><td align="right">J-1</td></tr>';
		print '<tr>';
		print '<td>Valeur</td>';
		print '<td align="right">'.$lastval[4].'</td>';
		print '<td align="right">'.$lastval[3].'</td>';
		print '<td align="right">'.$lastval[2].'</td>';
		print '<td align="right">'.$lastval[1].'</td>';
		print '<td align="right">'.$lastval[0].'</td>';
		print '</tr>';
		print '</table><br>';
	}
}

// Liste annonces par type
if (1 == 1)
{
	$prixarray=array();
	$sql = "SELECT DISTINCT PRIX_ANNONCE as prix";
	$sql.= " FROM T_ANNONCES as a";
	$sql.= " WHERE a.VALID_ANNONCE in ('1','2','3')";
	$sql.= " ORDER BY PRIX_ANNONCE";
	dolibarr_syslog("statsannonces.php sql=".$sql, LOG_DEBUG);
	$result = $dbann->query($sql);
	//print $sql;
	if ($result)
	{
		while ($obj = $dbann->fetch_object($result))
		{
			$prixarray[$obj->prix]=$obj->prix;
		}
	}

	$bytypearray=array();
	$sql = "SELECT ID_CATEG, ID_TYPE, ID_MODE, PRIX_ANNONCE, ID_ORIGINE, COUNT(*) as nb";
	$sql.= " FROM T_ANNONCES as a";
	$sql.= " WHERE VALID_ANNONCE in ('1','2','3')";
	$sql.= " GROUP BY ID_CATEG, ID_TYPE, ID_MODE, PRIX_ANNONCE, ID_ORIGINE";
	$sql.= " HAVING nb > 0";
	$sql.= " ORDER BY ID_CATEG, ID_TYPE, ID_MODE, PRIX_ANNONCE, ID_ORIGINE";
	dolibarr_syslog("statsannonces.php sql=".$sql, LOG_DEBUG);
	$result = $dbann->query($sql);
	//print $sql;
	if ($result)
	{
		while ($obj = $dbann->fetch_object($result))
		{
			$bytypearray[$obj->ID_CATEG.'_'.$obj->ID_TYPE][$obj->ID_MODE][$obj->PRIX_ANNONCE][$obj->ID_ORIGINE]=$obj->nb;
		}
	}

	//var_dump($bytypearray);
	print '<br>';
	print_barre_liste('Nombre d\'annonces par tarif vendu',0,'','','','','',0,'','');

	print '<table width="100%" class="border">'."\n";
	print '<tr class="liste_titre"><td class="liste_titre">IDCATEG_IDTYPE</td><td class="liste_titre">Mode</td>';
	// For each price
	foreach ($prixarray as $val)
	{
		print '<td class="liste_titre">Prix '.$val.' (origine)</td>';
	}
	print '</tr>'."\n";
	$oldkey='';
	$oldcateg='';
	$var=false;
	foreach ($bytypearray as $key => $val)
	{
		// Rupture sur categorie ?
		$tmp=split('_',$key);
		if ($oldcateg && ($tmp[0] != $oldcateg))
		{
			print '<tr class="liste_total"><td>Total categorie '.$oldcateg.'</td><td></td>';
			foreach ($prixarray as $pval)
			{
				print '<td nowrap="nowrap"></td>';
			}
			print '</tr>';
		}
		$oldcateg = $tmp[0];

		$var=!$var;
		foreach(array('STANDARD','GOLD','MIXAD','MIXAD2') as $key2)
		{
			//var_dump($val2);
			print '<tr '.$bc[$var].'><td>'.$key.'</td><td>'.$key2.'</td>';
			// For each price
			foreach ($prixarray as $pval)
			{
				print '<td nowrap="nowrap">';
				if (is_array($val[$key2][$pval]))
				{
					foreach ($val[$key2][$pval] as $pokey => $poval)
					{
						print $val[$key2][$pval][$pokey];
						print ' ('.($pokey?$pokey:'lesbonnesannonces').')';
						print '<br>';
					}
				}
				print '</td>';
			}
			print '</tr>';
		}
	}
	print '<tr class="liste_total"><td>Total categorie '.$oldcateg.'</td><td></td>';
	foreach ($prixarray as $pval)
	{
		print '<td nowrap="nowrap"></td>';
	}
	print '</tr>';
	print '</table>';
}

print '<br>';
$dbann->close();

llxFooter();
?>

