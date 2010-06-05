<?php
/* Copyright (C) 2008-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *    	\file       htdocs/nltechno/statsemailings.php
 *		\ingroup    nltechno
 *		\brief      Page des stats
 *		\version    $Id: statsemailings.php,v 1.18 2010/06/05 15:32:13 eldy Exp $
 *		\author		Laurent Destailleur
 */

include("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/dolgraph.class.php");
require_once DOL_DOCUMENT_ROOT.'/comm/mailing/class/mailing.class.php';

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

if ($_GET["action"] == 'buildemailingchien')
{
	// Cree un emailing brouillon
	$sujet='La Newsletter hebdomadaire de ChiensDeRace.com';
	$body='';

	// Connexion base
	$dbchien = mysql_connect($dbhostchien, $dbuserchien, $dbpasswordchien);
	mysql_select_db($dbdatabasechien,$dbchien);

	// sante
	$sante='';
	$REQUETE="select ID_NEWS, TITRE_NEWS, TEXTE_NEWS from T_NEWS";
	$REQUETE.=" where ID_CATEG = 20 AND (AUTEUR_NEWS ='1040' OR AUTEUR_NEWS='1038') ORDER by ID_NEWS DESC";
	$result = mysql_query("$REQUETE",$dbchien);

	while ($row = mysql_fetch_object($result))
	{
		$ID_NEWS=$row->ID_NEWS;
		$TITRE_NEWS=$row->TITRE_NEWS;
		$TEXTE_NEWS=$row->TEXTE_NEWS;
		$sante=$TITRE_NEWS."<br><br>".$TEXTE_NEWS."<br><a href='http://www.chiensderace.com/news/novel.php?ID=".$ID_NEWS."'>Lire cet article</a><br>";
		break;
	}


	// actualité
	$actualite='';
	$REQUETE="select ID_NEWS, TITRE_NEWS, TEXTE_NEWS from T_NEWS";
	$REQUETE.=" where ID_CATEG = 73 AND (AUTEUR_NEWS ='1040' OR AUTEUR_NEWS='1038') ORDER by ID_NEWS DESC";
	$result = mysql_query("$REQUETE",$dbchien);

	while ($row = mysql_fetch_object($result))
	{
		$ID_NEWS=$row->ID_NEWS;
		$TITRE_NEWS=$row->TITRE_NEWS;
		$TEXTE_NEWS=$row->TEXTE_NEWS;
		$actualite=$TITRE_NEWS."<br><br>".$TEXTE_NEWS."<br><a href='http://www.chiensderace.com/news/novel.php?ID=".$ID_NEWS."'>Lire cet article</a><br>";
		break;
	}

	$race_semaine='';
	$REQUETE="select ID_RACES, LIB_RACES, ORIGINE_RACES from T_RACES";
	$result = mysql_query("$REQUETE",$dbchien);
	$i=0;
	while ($row = mysql_fetch_object($result))
	{
		$ID_RACES[$i]=$row->ID_RACES;
		$LIB_RACES[$i]=$row->LIB_RACES;
		$ORIGINE_RACES[$i]=$row->ORIGINE_RACES;
		$i++;
	}
	$j=rand(0,$i--);
	$race_semaine=$LIB_RACES[$j]." (Origine : ".$ORIGINE_RACES[$j].")<br><br>Découvrez cette race cette semaine avec ChiensDeRace.com.<br><a href='http://www.chiensderace.com/php/fiche_race.php?RACE=".$ID_RACES[$j]."'>Voir la fiche de race</a><br>";

	$file_in='newsletter_type_chien.html';
    $fichier= fopen ($file_in, 'r');
	$lines = file ($file_in);

	foreach ($lines as $line_num => $line)
	{
		// on vire les retour chariots
		$line=trim(preg_replace("/[\n\r]/",'',$line));
		if ($line == '$sante') $line=$sante;
	       	if ($line == '$actualite') $line=$actualite;
	       	if ($line == '$race_semaine') $line=$race_semaine;
		$body.=$line;
	}


    $mil = new Mailing($db);

    $mil->email_from   = 'newsletter@chiensderace.com';
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
        print '<td>Groupe de donnees</td>';
        print '<td align="center">ML_XXX=-1</td>';
        print '<td align="center">ML_XXX=0</td>';
		print '<td align="center">ML_XXX=1</td>';
        print "</tr>\n";

        clearstatcache();

        $listdir=array();
        $listdir[]=$dirmod;
        if (! empty($dirmod2)) $listdir[]=$dirmod2;
        $listtype=array('adresses','personnes');

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
                        print ' - Newsletter '.$type;
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


        $listdir=array();
        $listdir[]=$dirmod;
        if (! empty($dirmod2)) $listdir[]=$dirmod2;
        $listtype=array('forum');

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
                if (eregi("(.*(chiensderace|chatsderace)_forum)\.modules\.php$",$file,$reg))
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

                        // Newsletter

                        print '<tr '.$bc[$var].'>';

                        print '<td>';
                        if (! $obj->picto) $obj->picto='generic';
                        print img_object('',$obj->picto).' '.$obj->getDesc();
                        print ' - Newsletter '.$type;
                        print '</td>';

                        print '<td>&nbsp;</td>';

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


                        // Offres commerciales

                        $var = !$var;
                        print '<tr '.$bc[$var].'>';

                        print '<td>';
                        if (! $obj->picto) $obj->picto='generic';
                        print img_object('',$obj->picto).' '.$obj->getDesc();
                        print ' - Offres commerciales '.$type;
                        print '</td>';

                        print '<td>&nbsp;</td>';

                        $nbofrecipient=$obj->getNbOfRecipients(-2,$type);
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

                        $nbofrecipient=$obj->getNbOfRecipients(2,$type);
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

		print 'Les emails sont definis dans T_ADRESSES (inscription via adresse)+T_PERSONNES (inscription via la box)+FORUM_USERS (incription par forum)<br>';
		print 'Si ML_XXX=-1, a demande explicitement a etre desincrit<br>';
		print 'Si ML_XXX=0,  ne s\'est pas inscrit<br>';
		print 'Si ML_XXX=1,  s\'est inscrit (explicitement ou auto car avant loi optin)<br>';







	print '<br><br>';
	print '<b>Cliquer sur ce bouton pour fabriquer un emailing brouillon chiensderace du moment</b>:<br><br>';
	print '<form action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="buildemailingchien">';
	print '<input type="submit" class="button" value="Generer newsletter brouillon"><br>';
	print '</form>';

$dbann->close();

llxFooter();
?>

