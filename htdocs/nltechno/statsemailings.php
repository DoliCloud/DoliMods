<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
    	\file       htdocs/nltechno/statsemailings.php
		\ingroup    nltechno
		\brief      Page des stats
		\version    $Id: statsemailings.php,v 1.5 2008/05/22 01:11:59 eldy Exp $
		\author		Laurent Destailleur
*/

include("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/dolgraph.class.php");
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/mailing.class.php';

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
if (! $user->rights->nltechno->emailings->voir)
{
	accessforbidden();
	exit;
}

$dirmod=DOL_DOCUMENT_ROOT."/includes/modules/mailings";
if (defined('DOL_DOCUMENT_ROOT_BIS')) $dirmod2=DOL_DOCUMENT_ROOT_BIS."/includes/modules/mailings";

$mesg = '';


/*
 * 	Actions
 */

if ($_GET["action"] == 'buildemailing')
{
	// Cree un emailing brouillon
	$sujet='';
	$body='';
		
	
	// TODO A faire: Lire base des news et races et fabriquer variable sujet et body (en html)
	
		
	
	
    $mil = new Mailing($db);

    $mil->email_from   = 'noreply@monserver.com';
    $mil->titre        = $sujet;
    $mil->sujet        = $sujet;
    $mil->body         = $body;

    $result = $mil->create($user);
    if ($result >= 0)
    {
        Header("Location: ".DOL_URL_ROOT.'/comm/mailing/fiche.php?id='.$mil->id);
        exit;
    }
    else
    {
        $msg=$mil->error;
    }
}

/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader();

$form=new Form($db);

if ($msg) print $msg.'<br>';


$dbann=new DoliDb('mysqli', $dbhostchien, $dbuserchien, $dbpasswordchien, $dbdatabasechien);
if (! $dbann->connected)
{
	dolibarr_print_error($dbann,"Can not connect to server ".$dbhostchien." with user ".$dbuserchien);
	exit;
}
if (! $dbann->database_selected)
{
	dolibarr_print_error($dbann,"Database ".$dbdatabasechien." can not be selected");
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
	

// Get datas
$graph_data = array();
$lastval=array();
$relativepath=$dirtmp."statsannonces.png".$categ;



        print '<table class="noborder" width="100%">';
        print '<tr class="liste_titre">';
        print '<td>Groupe de données</td>';
        print '<td align="center">ML_XXX=-1</td>';
        print '<td align="center">ML_XXX=0</td>';
		print '<td align="center">ML_XXX=1</td>';
        print "</tr>\n";
        
        clearstatcache();
        
        $listdir=array();
        $listdir[]=$dirmod;
        if (! empty($dirmod2)) $listdir[]=$dirmod2;
        $listtype=array('personnes','adresses');
        
        foreach ($listtype as $type)
        {
	        foreach ($listdir as $dir)
	        {
		        $handle=opendir($dir);
		        
		        $var=True;
		        while (($file = readdir($handle))!==false)
		        {
		            if (substr($file, 0, 1) <> '.' && substr($file, 0, 3) <> 'CVS')
		            {
		                if (eregi("(.*(chiensderace|chatsderace))\.modules\.php$",$file,$reg))
		                {
		                    $modulename=$reg[1];
		        			if ($modulename == 'example') continue;
		        			
		                    // Chargement de la classe
		                    $file = $dir."/".$modulename.".modules.php";
		                    $classname = "mailing_".$modulename;
		                    require_once($file);
		        
		                    $obj = new $classname($db);
		
		                    $qualified=1;
		                    foreach ($obj->require_module as $key)
		                    {
		                        if (! $conf->$key->enabled || (! $user->admin && $obj->require_admin))
		                        {
		                            $qualified=0;
		                            //print "Les prérequis d'activation du module mailing ne sont pas respectés. Il ne sera pas actif";
		                            break;
		                        }
		                    }
		                    
		                    // Si le module mailing est qualifié
		                    if ($qualified)
		                    {
		                        $var = !$var;
		                        print '<tr '.$bc[$var].'>';
		                        
		                        print '<td>';
		                        if (! $obj->picto) $obj->picto='generic';
		                        print img_object('',$obj->picto).' '.$obj->getDesc();
		                        print ' - '.$type;
		                        print '</td>';
		            
		                        /*
		                        print '<td width=\"100\">';
		                        print $modulename;
		                        print "</td>";
		                        */
		                        $nbofrecipient=$obj->getNbOfRecipients(-1,$type);
		                        print '<td align="center">';
		                        if ($nbofrecipient >= 0)
		                        {
		                        	print $nbofrecipient;
		                        }
		                        else
		                        {
		                        	print $langs->trans("Error").' '.img_error($obj->error);
		                        }
		                        print '</td>';
		                        
		                        $nbofrecipient=$obj->getNbOfRecipients(0,$type);
		                        print '<td align="center">';
		                        if ($nbofrecipient >= 0)
		                        {
		                        	print $nbofrecipient;
		                        }
		                        else
		                        {
		                        	print $langs->trans("Error").' '.img_error($obj->error);
		                        }
		                        print '</td>';
		                        
		                        $nbofrecipient=$obj->getNbOfRecipients(1,$type);
		                        print '<td align="center">';
		                        if ($nbofrecipient >= 0)
		                        {
		                        	print $nbofrecipient;
		                        }
		                        else
		                        {
		                        	print $langs->trans("Error").' '.img_error($obj->error);
		                        }
		                        print '</td>';
		                        
		                        print "</tr>\n";
		                    }
		                }
		            }
		        }
		        closedir($handle);
	        }
        }
        
        print '</table>';
		print '<br>';
		
		print 'Les emails sont défini dans T_ADRESSES (inscription via adresse)+T_PERSONNES (inscription via la box)<br>';
		print 'Si ML_XXX=-1, a demandé explicitement à etre désincrit<br>';
		print 'Si ML_XXX=0, n\'a pas choisi l\'optin<br>';
		print 'Si ML_XXX=1, a choisi optin ou s\'est inscrit avant loi optin<br>';
		
/*
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
	print '<tr><td>Dernières valeures</td><td align="right">J-5</td><td align="right">J-4</td><td align="right">J-3</td><td align="right">J-2</td><td align="right">J-1</td></tr>';
	print '<tr>';
	print '<td>Valeur</td>';
	print '<td align="right">'.$lastval[4].'</td>';
	print '<td align="right">'.$lastval[3].'</td>';
	print '<td align="right">'.$lastval[2].'</td>';
	print '<td align="right">'.$lastval[1].'</td>';
	print '<td align="right">'.$lastval[0].'</td>';
	print '</tr>';
	print '</table><br>';
*/

	print '<br><br>';
	print 'Cliquer sur ce bouton pour fabriquer un emailing brouillon du moment';
	print '<form action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="buildemailing">';
	print '<input type="submit" class="button" value="Générer brouillon mailing du moment"><br>';
	print '</form>';
		
$dbann->close();

llxFooter();
?>

