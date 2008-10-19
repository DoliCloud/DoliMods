<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
    	\file       htdocs/google/index.php
		\ingroup    google
		\brief      Main google area page
		\version    $Id: index.php,v 1.3 2008/10/19 19:59:13 eldy Exp $
		\author		Laurent Destailleur
*/

include("./pre.inc.php");

// Load traductions files
$langs->load("google");
$langs->load("companies");
$langs->load("other");

// Load permissions
$user->getrights('google');

// Get parameters
$socid = isset($_GET["socid"])?$_GET["socid"]:'';

// Protection quand utilisateur externe
if ($user->societe_id > 0)
{
    $action = '';
    $socid = $user->societe_id;
}

$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($_REQUEST["action"] == 'add')
{
	$myobject=new Skeleton_class($db);
	$myobject->prop1=$_POST["field1"];
	$myobject->prop2=$_POST["field2"];
	$result=$myobject->create($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$myobject->error;
	}
}





/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader();

$form=new Form($db);


// Define parameters
$bgcolor='FFFFFF';
$color_file = DOL_DOCUMENT_ROOT."/theme/".$conf->theme."/graph-color.php";
if (is_readable($color_file))
{
	include_once($color_file);
	if (! empty($theme_bgcolor)) $bgcolor=dechex($theme_bgcolor[0]).dechex($theme_bgcolor[1]).dechex($theme_bgcolor[2]);
}

$frame ='<iframe src="http://www.google.com/calendar/embed?';
$frame.='showTitle=0';
$frame.='&amp;height=600';
$frame.='&amp;wkst=2';
$frame.='&amp;bgcolor=%23'.$bgcolor;

	
$i=1;
while ($i <= $MAXAGENDA)
{
	//$src  =array('eldy10%40gmail.com','5car0sbosqr5dt08157ro5vkuuiv8oeo%40import.calendar.google.com','french__fr%40holiday.calendar.google.com','sjm1hvsrbqklca6ju6hlcj1vdgvatuh0%40import.calendar.google.com');
	//$color=array('A32929','7A367A','B1365F','0D7813');

	$paramname='GOOGLE_AGENDA_NAME'.$i;
	$paramsrc='GOOGLE_AGENDA_SRC'.$i;
	$paramcolor='GOOGLE_AGENDA_COLOR'.$i;
	if (! empty($conf->global->$paramname))
	{
		if (isset($_GET["nocal"]))
		{
			if ($_GET["nocal"] == $i) 
			{
				$frame.='&amp;src='.$conf->global->$paramsrc;
				$frame.='&amp;color=%23'.$conf->global->$paramcolor;
			}
		}
		else
		{
			$frame.='&amp;src='.$conf->global->$paramsrc;
			$frame.='&amp;color=%23'.$conf->global->$paramcolor;
		}
	}

	$i++;
}
$frame.='&amp;ctz='.urlencode($conf->global->GOOGLE_AGENDA_TIMEZONE);
$frame.='" style=" border-width:0 " ';
$frame.='width="800" ';
$frame.='height="600" ';
$frame.='frameborder="0" scrolling="no">';
$frame.='</iframe>';

print $frame;

// End of page
$db->close();

llxFooter('$Date: 2008/10/19 19:59:13 $ - $Revision: 1.3 $');
?>
