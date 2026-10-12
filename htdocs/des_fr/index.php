<?php

// Script to build XML DES files
// Ecriture debut de fichier xml selon
// https://pro.douane.gouv.fr/download/downloadUrl.asp?file=PubliwebBO/fichiers/DocumentationXMLDES.pdf


$patha='des_fr'; // chemin relatif
$path= DOL_DATA_ROOT.'/'.$patha;



/**
 * Write a line into the XML file
 *
 * @param 	string		$fic	File path
 * @param 	string		$line	Line content
 * @return	void
 */
function do_line($fic, $line)
{
	// TODO Write into file
}


/**
 * Write the header of the XML DES declaration file
 *
 * @param 	string		$fic	File path
 * @param 	string		$mo		Period (format 'MM/YYYY')
 * @return	void
 */
function deb_fic($fic, $mo)
{
	$mois = explode('/', $mo);

	$m = $mois[0];

	$y = $mois[1];

	$t[]='<?xml version=\"1.0\" encoding=\"UTF-8\"?>';

	$t[]='<fichier_des>';

	$t[]='<declaration_des>';

	$t[]= '  <num_des>'.substr('0000'.$m, -4).'</num_des>';

	$t[]='  <num_tvaFr>FR77480672302</num_tvaFr>';

	$t[]='  <mois_des>'.$m.'</mois_des>';

	$t[]='  <an_des>'.$y.'</an_des>';

	foreach ($t as $tx) {
		do_line($fic, $tx);
	}
}



/**
 * Write a declaration line into the XML file
 *
 * @param 	string		$fic	File path
 * @param 	int			$num	Line number
 * @param 	string		$mo		Period (format 'MM/YYYY')
 * @param 	string		$tva	Partner VAT number
 * @param 	float		$mnt	Amount
 * @return	void
 */
function lin_fic($fic, $num, $mo, $tva, $mnt)
{
	// xml lines

	$mois = explode('/', $mo);

	$m = $mois[0];

	do_line($fic, '  <ligne_des>');

	do_line($fic, '
 <numlin_des>'.substr('000000'.$num, -6).'</numlin_des>');

	do_line($fic, '     <valeur>'.$mnt.'</valeur>');

	do_line($fic, '     <partner_des>'.$tva.'</partner_des>');

	do_line($fic, '  </ligne_des>');
}



/**
 * Write the footer of the XML DES declaration file
 *
 * @param 	string		$fic	File path
 * @return	void
 */
function fin_fic($fic)
{
	// end of the xml file

	$t[]= "</declaration_des>";

	$t[]='</fichier_des>';

	foreach ($t as $tx) {
		do_line($fic, $tx);
	}
}


/*
 * View
 */

llxHeader();

// Et la boucle principale qui genere la table de visu et un fichier xml
// par mois :

$des ='<table  class="notopnoleftnoright" width="50%">  ';

$tic = '<tr><td width="15%"><b>Client </b></td><td width="15%"><b>Num
 TVA</b></td><td width="15%" align="right"><b>Montant</b></td></tr>';

while ($row = mysql_fetch_assoc($result)) {
	$var=!$var;

	if ($cur != $row['mois']) {
		$i = 1;

		if ($fic != '') {
			fin_fic($fic);
		}

		$cur =  $row['mois'];

		$fica = '/des_'.str_replace('/', '-', $cur);

		$fic = $path.$fica;

		deb_fic($fic, $cur);

		$des .= '<tr><td colspan="3" align = "center"><b><br><a
 href="'.$patha.$fica.'">Mois : '.$cur.'</a></b></td></tr>';

		$des .= $tic;
	}

	lin_fic($fic, $i, $cur, $row['tva'], round($row['mnt']));

	$des .= '<tr
 '.$bc[$var].'><td>'.$row['nom'].'</td><td>'.$row['tva'].'</td><td
 align=right>'.round($row['mnt']).'</tr>';

	$i++;
}

if ($fic != '') {
	fin_fic($fic);
}

$des .= '</table>';

llxFooter();

$db->close();
