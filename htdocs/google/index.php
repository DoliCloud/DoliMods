<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *    	\file       htdocs/google/index.php
 *		\ingroup    google
 *		\brief      Main google area page
 *		\version    $Id: index.php,v 1.9 2011/08/29 07:45:09 eldy Exp $
 *		\author		Laurent Destailleur
 */

include("./pre.inc.php");
/*$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
*/
require_once(DOL_DOCUMENT_ROOT."/core/lib/agenda.lib.php");

// Load traductions files
$langs->load("google@google");
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


/*
 * Actions
 */

// Define $urlwithroot
//$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
//$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
$urlwithroot=DOL_MAIN_URL_ROOT;						// This is to use same domain name than current
$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',$urlwithroot);

// You must allow Dolibarr to login to
$client_id=$conf->global->GOOGLE_AGENDA_CLIENT_ID;
$client_secret=$conf->global->GOOGLE_AGENDA_CLIENT_SECRET;
$redirect_uri=$urlwithouturlroot.dol_buildpath('/google/index.php',1);		// Must be an url without parameters
$url='https://accounts.google.com/o/oauth2/auth?client_id='.$client_id.'&redirect_uri='.urlencode($redirect_uri).'&scope=https://www.google.com/calendar/feeds/&response_type=code';	// Values for scope are here: https://developers.google.com/gdata/faq?hl=fr#AuthScopes

$auth_code = GETPOST("code");


//print DOL_URL_ROOT.' '.DOL_MAIN_URL_ROOT.' '.$redirect_uri;exit;


if (! empty($client_id))		// If we setup to use the oauth login
{
	// Ask token (possible only if inside an oauth google session)
	if (empty($_SESSION['google_oauth_token']) || $auth_code)		// We are not into a google session (oauth_token empty) or we come from a redirect of Google auth page
	{
		if (empty($auth_code))	// If we are not coming from oauth page, we make a redirect to it
		{
			//print 'We are not coming from an oauth page and are not logged into google oauth, so we redirect to it';
			header("Location: ".$url);
			exit;
		}

		$fields=array(
		'code'=>  urlencode($auth_code),
		'client_id'=>  urlencode($client_id),
		'client_secret'=>  urlencode($client_secret),
		'redirect_uri'=>  urlencode($redirect_uri),
		'grant_type'=>  urlencode('authorization_code')
		);
		$post = '';
		foreach($fields as $key=>$value) {
			$post .= $key.'='.$value.'&';
		}
		$post = rtrim($post,'&');

		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,'https://accounts.google.com/o/oauth2/token');
		curl_setopt($curl,CURLOPT_POST,5);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
		$result = curl_exec($curl);
		curl_close($curl);

		$response =  json_decode($result);

		$_SESSION['google_oauth_token']=$response->access_token;
	}


	$oauth_token = $_SESSION['google_oauth_token'];
}



/*
 * View
 */

llxHeader('','Google',"EN:Module_GoogleEn|FR:Module_Google|ES:Modulo_Google");

$form=new Form($db);


$head = calendars_prepare_head('');

dol_fiche_head($head, 'gcal', $langs->trans('Events'), 0, 'calendar');

$finaltext='';

$found=0;
$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;

$i=1;
while ($i <= $MAXAGENDA)
{
    $paramname='GOOGLE_AGENDA_NAME'.$i;
    $paramcolor='GOOGLE_AGENDA_COLOR'.$i;
    //print $paramname;
    if (! empty($conf->global->$paramname))
    {
        $found++;
    }
    $i++;
}

$i=1;
if ($found > 0)
{
    while ($i <= $MAXAGENDA)
    {
        $paramname='GOOGLE_AGENDA_NAME'.$i;
        $paramsrc='GOOGLE_AGENDA_SRC'.$i;
        $paramcolor='GOOGLE_AGENDA_COLOR'.$i;

        $addcolor=false;
        if (isset($_GET["nocal"]))
        {
            if ($_GET["nocal"] == $i) $addcolor=true;
        }
        else $addcolor=true;

        $text='';
        if (! empty($conf->global->$paramname))
        {
            $link=dol_buildpath("/google/index.php",1)."?mainmenu=agenda&idmenu=".$_SESSION["idmenu"]."&nocal=".$i;

            $text='';
            $text.='<table class="nobordernopadding">';
            $text.='<tr valign="middle" class="nobordernopadding">';

            // Color of agenda
            if ($found > 1)
            {
                $text.='<td style="padding-left: 4px; padding-right: 4px" nowrap="nowrap">';
                $box ='<!-- Box color '.$selected.' -->';
                $box.='<a class="legendagenda" href="'.$link.'">';
                $box.='<table style="margin:0px; padding: 0px; border: 1px solid #888888;';
                if ($addcolor) $box.=' background: #'.(preg_replace('/#/','',$conf->global->$paramcolor)).';';
                $box.='" width="12" height="10">';
                $box.='<tr class="nocellnopadd"><td></td></tr>';    // To show box
                $box.='</table>';
                $box.='</a>';
                $text.=$box;
                $text.='</td>';
            }

            // Name of agenda
            $text.='<td>';
            if ($found == 1) $text.=$langs->trans("Name").': '.$conf->global->$paramname.' ('.$langs->trans("GoogleIDAgenda").': '.$conf->global->$paramsrc.')';
            else $text.='<a class="vsmenu" href="'.$link.'">'.$conf->global->$paramname.'</a> ('.$conf->global->$paramsrc.')';
            $text.='</td></tr>';

            $text.='</table>';
        }

        $finaltext.=$text;
        $i++;
    }
}
if ($found > 1)
{
    $link=dol_buildpath("/google/index.php",1)."?mainmenu=agenda&idmenu=".$_SESSION["idmenu"];

    $text='';
    $text.='<table class="nobordernopadding">';
    $text.='<tr valign="middle" class="nobordernopadding" style="height: 14px">';

    // Color of agenda
    $text.='<td style="padding-left: 4px; padding-right: 4px;" nowrap="nowrap">';
    $box ='<!-- Box color '.$selected.' -->';
    $box.='<a class="legendagenda" href="'.$link.'">';
    $box.='<table style="margin:0px; padding: 0px; border: 1px solid #888888;';
    if ($addcolor) $box.=' background: #'.(preg_replace('/#/','','#FFFFFF')).';';
    $box.='" width="12" height="10">';
    $box.='<tr class="nocellnopadd"><td></td></tr>';    // To show box
    $box.='</table>';
    $box.='</a>';
    $text.=$box;
    $text.='</td>';

    // Name of agenda
    $text.='<td>';
    $text.='<a class="vsmenu" href="'.$link.'"><strong>'.$langs->trans("All").'</strong></a>';
    $text.='</td></tr>';

    $text.='</table>';

    $finaltext=$text.$finaltext;
}

print $finaltext;

dol_fiche_end();


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
// Define first day of week (wkst=1 for sunday, wkst=2 for monday, ...)
//var_dump($conf->global->MAIN_START_WEEK);
$frame.='&amp;wkst='.($conf->global->MAIN_START_WEEK+1);
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
				$frame.='&amp;src='.urlencode($conf->global->$paramsrc);
				$frame.='&amp;color='.urlencode('#'.preg_replace('/#/','',$conf->global->$paramcolor));
			}
		}
		else
		{
			$frame.='&amp;src='.urlencode($conf->global->$paramsrc);
			$frame.='&amp;color='.urlencode('#'.preg_replace('/#/','',$conf->global->$paramcolor));
		}
	}

	$i++;
}

// Add number of weeks (only if first day is monday)
if ($conf->global->MAIN_START_WEEK == 1)
{
	$frame.='&amp;src='.urlencode('e_2_fr#weeknum@group.v.calendar.google.com');
}

$frame.='&amp;ctz='.urlencode($conf->global->GOOGLE_AGENDA_TIMEZONE);
$frame.='" style=" border-width:0 " ';
$frame.='width="100%" ';
$frame.='height="600" ';
$frame.='frameborder="0" scrolling="no">';
$frame.='</iframe>';

print $frame;



if (empty($client_id))		// If we setup to use the oauth login
{
	print $langs->trans("DueToGoogleLimitYouNeedToLogin");
}



// End of page

llxFooter();

$db->close();
?>
