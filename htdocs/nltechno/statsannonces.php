<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
    	\file       htdocs/nltechno/statsannonces.php
		\ingroup    nltechno
		\brief      Page des stats annonces
		\version    $Id: statsannonces.php,v 1.1 2008/05/03 23:57:10 eldy Exp $
		\author		Laurent Destailleur
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

// Protection quand utilisateur externe
if (! $user->admin)
{
	access_forbidden();
	exit;
}


/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader();

$form=new Form($db);

$dbann=new DoliDb('mysqli', $dbhost, $dbuser, $dbpassword, $dbdatabase);
if (! $dbann->connected)
{
	dolibarr_print_error($dbann,"Can not connect to server ".$dbhost." with user ".$dbuser);
	exit;
}
if (! $dbann->database_selected)
{
	dolibarr_print_error($dbann,"Database ".$dbdatabase." can not be selected");
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
$datestart='2008-01-01';
$listofcateg=array('CATEG_ANICHIENS','CATEG_ANICHATS','CATEG_ROOT');
foreach ($listofcateg as $categ)
{
	// Get datas
	$graph_data = array();
	$relativepath=$dirtmp."statsannonces.png".$categ;
	
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
}

$dbann->close();

llxFooter();
?>

