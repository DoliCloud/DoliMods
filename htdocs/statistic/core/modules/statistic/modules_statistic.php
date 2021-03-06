<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *  \file       htdocs/includes/modules/statistic/modules_statistic.php
 *  \ingroup    propale
 *  \brief      Fichier contenant la classe mere de generation des statistiques en PDF
 *	\version    $Id: modules_statistic.php,v 1 Gerem
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php";   // Requis car utilise dans les classes qui heritent


/**
 *	\class      ModelePDFPropales
 *	\brief      Classe mere des modeles de statistique
 */
class ModelePDFStats
{
	var $error='';


	/**
	 *      Return list of active generation modules
	 *
	 * 		@param    DoliDB	$db		Database handler
	 */
	function liste_modeles($db)
	{
		global $conf;

		$type='stat';
		$liste=array();

		include_once DOL_DOCUMENT_ROOT.'/lib/functions2.lib.php';
		$liste=getListOfModels($db, $type, '');

		return $liste;
	}
}


/**
 *	\class      ModeleNumRefPropales
 *	\brief      Classe mere des modeles de numerotation des references de propales
 */
class ModeleNumRefStats
{
	var $error='';

	/**     \brief     	Return if a module can be used or not
	 *      	\return		boolean     true if module can be used
	 */
	function isEnabled()
	{
		return true;
	}

	/**     \brief      Renvoi la description par defaut du modele de numerotation
	 *      \return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		$langs->load("statistic");
		return $langs->trans("NoDescription");
	}

	/**     \brief      Renvoi un exemple de numerotation
	 *      \return     string      Example
	 */
	function getExample()
	{
		global $langs;
		$langs->load("statistic");
		return $langs->trans("NoExample");
	}

	/**     \brief      Test si les numeros deja en vigueur dans la base ne provoquent pas de
	 *                  de conflits qui empechera cette numerotation de fonctionner.
	 *      \return     boolean     false si conflit, true si ok
	 */
	function canBeActivated()
	{
		return true;
	}

	/**     \brief      Renvoi prochaine valeur attribuee
	 *      \return     string      Valeur
	 */
	function getNextValue()
	{
		global $langs;
		return $langs->trans("NotAvailable");
	}

	/**     \brief      Renvoi version du module numerotation
	 *      	\return     string      Valeur
	 */
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'development') return $langs->trans("VersionDevelopment");
		if ($this->version == 'experimental') return $langs->trans("VersionExperimental");
		if ($this->version == 'dolibarr') return DOL_VERSION;
		return $langs->trans("NotAvailable");
	}
}


/**
 *  Cree une propale sur disque en fonction du modele de PROPALE_ADDON_PDF
 *
 *  @param	    DB       $db  			    objet base de donnee
 *  @param	    int      $id				id de la propale a creer
 *  @param	    date     $date			    force le modele a utiliser ('' to not force)
 *  @param	    string   $modele			force le modele a utiliser ('' to not force)
 *  @param		Langs    $outputlangs	    objet lang a utiliser pour traduction
 *  @return     int         	            0 si KO, 1 si OK
 */
function statistic_pdf_create($db, $id, $date, $modele, $outputlangs)
{
	global $langs;
	$langs->load("statistic");

	$dir = DOL_DOCUMENT_ROOT."/includes/modules/statistic/";
	$modelisok=0;

	// Positionne modele sur le nom du modele de propale a utiliser
	$file = "pdf_statistic_".$modele.".modules.php";
	if ($modele && file_exists($dir.$file)) $modelisok=1;

	// Si model pas encore bon
	if (! $modelisok) {
		if ($conf->global->PROPALE_ADDON_PDF) $modele = $conf->global->PROPALE_ADDON_PDF;
		$file = "pdf_statistic_".$modele.".modules.php";
		if (file_exists($dir.$file)) $modelisok=1;
	}

	// Si model pas encore bon
	if (! $modelisok) {
		/*$liste=array();
		$model=new ModelePDFStats();
		$liste=$model->liste_modeles($db);
		$modele=key($liste);       */ // Renvoie premiere valeur de cle trouve dans le tableau
		$modele="test";
		$file = "pdf_statistic_".$modele.".modules.php";
		if (file_exists($dir.$file)) $modelisok=1;
	}


	// Charge le modele
	if ($modelisok) {
		$classname = "pdf_statistic_".$modele;
		require_once $dir.$file;

		$obj = new $classname($db);

		// We save charset_output to restore it because write_file can change it if needed for
		// output format that does not support UTF8.
		$sav_charset_output=$outputlangs->charset_output;

		if ($obj->write_file($id, $outputlangs, $date) > 0) {
			$outputlangs->charset_output=$sav_charset_output;
			// on supprime l'image correspondant au preview
			statistic_delete_preview($db, $id);
			return 1;
		} else {
			$outputlangs->charset_output=$sav_charset_output;
			dol_syslog("modules_propale::propale_pdf_create error");
			dol_print_error($db, $obj->error);
			return 0;
		}
	} else {
		if (! $conf->global->PROPALE_ADDON_PDF) {
			print $langs->trans("Error")." ".$langs->trans("Error_PROPALE_ADDON_PDF_NotDefined");
		} else {
			print $langs->trans("Error")." ".$langs->trans("ErrorFileDoesNotExists", $dir.$file);
		}
		return 0;
	}
}

/**
 *  Supprime l'image de previsualitation, pour le cas de regeneration de propal
 *
 *  @param	   DoliDB   $db  		objet base de donnee
 *  @param	   int      $propalid	id des stats a effacer
 *  @param     string   $statref    reference des stats si besoin
 */
function statistic_delete_preview($db, $propalid, $statref = 'test')
{
	global $langs,$conf;

		$dir = "../../documents/statistic/" . $statref ;

		$file = $dir . "/" . $statref . ".pdf.png";
		$multiple = $file . ".";

	if ( file_exists($file) && is_writable($file) ) {
		if ( ! dol_delete_file($file, 1) ) {
			$this->error=$langs->trans("ErrorFailedToOpenFile", $file);
			return 0;
		}
	} else {
		for ($i = 0; $i < 20; $i++) {
			$preview = $multiple.$i;

			if ( file_exists($preview) && is_writable($preview) ) {
				if ( ! unlink($preview) ) {
					$this->error=$langs->trans("ErrorFailedToOpenFile", $preview);
					return 0;
				}
			}
		}
	}

	return 1;
}
