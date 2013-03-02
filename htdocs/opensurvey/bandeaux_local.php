<?php

/**
 * Show header for new member
 *
 * @param 	string		$title				Title
 * @param 	string		$head				Head array
 * @param 	int    		$disablejs			More content into html header
 * @param 	int    		$disablehead		More content into html header
 * @param 	array  		$arrayofjs			Array of complementary js files
 * @param 	array  		$arrayofcss			Array of complementary css files
 * @return	void
 */
function llxHeaderSurvey($title, $head="", $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='')
{
	global $user, $conf, $langs, $mysoc;
	top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss); // Show html headers
	print '<body id="mainbody" class="publicnewmemberform" style="margin-top: 10px;">';

	// Print logo
	$urllogo=DOL_URL_ROOT.'/theme/login_logo.png';

	if (! empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small))
	{
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_small);
	}
	elseif (! empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo))
	{
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode($mysoc->logo);
		$width=128;
	}
	elseif (is_readable(DOL_DOCUMENT_ROOT.'/theme/dolibarr_logo.png'))
	{
		$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
	}
	print '<center>';
	print '<img alt="Logo" id="logosubscribe" title="" src="'.$urllogo.'" style="max-width: 50%" class="half" /><br>';
	print '<strong>'.$langs->trans("OpenSurvey").'</strong>';
	print '</center><br>';

	print '<div style="margin-left: 50px; margin-right: 50px;">';
}

/**
 * Show footer for new member
 *
 * @return	void
 */
function llxFooterSurvey()
{
	print '</div>';

	printCommonFooter('public');

	print "</body>\n";
	print "</html>\n";
}




// pour get_server_name()
include_once('fonctions.php');

//le logo
function logo ()
{
	if(defined('LOGOBANDEAU')) {
		echo '<div class="logo"><img src="'.get_server_name().LOGOBANDEAU.'" height="74" alt="logo"></div>'."\n";
	}
}


#le bandeau principal
function bandeau_tete()
{
	echo '<div class="bandeau">'.NOMAPPLICATION.'</div>'."\n";
}


// bandeaux de titre
function bandeau_titre($titre)
{
	echo '<div class="bandeautitre">'. $titre .'</div>'."\n";
}


function liste_lang()
{
	global $ALLOWED_LANGUAGES;

	$str = '';
	foreach ($ALLOWED_LANGUAGES as $k => $v ) {
		$str .= '<a href="' . $_SERVER['PHP_SELF'] . '?lang=' . $k . '">' . $v . '</a>' . "\n" ;
	}

	return $str;
}


#Les sous-bandeaux contenant les boutons de navigation
function sous_bandeau()
{
	echo '<div class="sousbandeau">' .
		'<a href="' . get_server_name() . 'index.php">'. _("Home") .'</a>' .
		'<a href="' . getUrlSondage('aqg259dth55iuhwm').'">'. _("Example") .'</a>' .
		'<a href="' . get_server_name() . 'contacts.php">'. _("Contact") .'</a>' .
		//'<a href="' . get_server_name() . 'sources/sources.php">'. _("Sources") .'</a>' . //not implemented
	'<a href="' . get_server_name() . 'apropos.php">'. _("About") .'</a>' .
	'<a href="' . get_server_name() . 'admin/index.php">'. _("Admin") .'</a>' .
	'<span class="sousbandeau sousbandeaulangue">' .
	liste_lang() . '</span>'.
	'</div>' . "\n";
}


function sous_bandeau_admin()
{
	echo '<div class="sousbandeau">' .
		'<a href="' . get_server_name() . 'index.php">'. _("Home") .'</a>';

	if(is_readable('logs_studs.txt')) {
		echo '<a href="' . get_server_name() . 'logs_studs.txt">'. _("Logs") .'</a>';
	}

	echo '<a href="' . get_server_name() . '../scripts/nettoyage_sondage.php">'. _("Cleaning") .'</a>' .
		'<span class="sousbandeau sousbandeaulangue">' .
		liste_lang() . '</span>'.
		'</div>'."\n";
}


function sous_bandeau_choix()
{
	echo '<div class="sousbandeau">' .
		'<a href="' . get_server_name() . 'index.php">'. _("Home") .'</a>' .
		'</div>'."\n";
}


#les bandeaux de pied
function sur_bandeau_pied()
{
	echo '<div class="surbandeaupied"></div>'."\n";
}


function bandeau_pied()
{
	echo '<div class="bandeaupied">'. _("Universit&eacute; de Strasbourg. Creation: Guilhem BORGHESI. 2008-2009") .'</div>'."\n";
}


function bandeau_pied_mobile()
{
	echo '<div class="surbandeaupiedmobile"></div>'."\n" .
		'<div class="bandeaupiedmobile">'. _("Universit&eacute; de Strasbourg. Creation: Guilhem BORGHESI. 2008-2009") .'</div>'."\n";
}

?>