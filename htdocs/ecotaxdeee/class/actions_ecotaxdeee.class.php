<?php
/* Copyright (C) 2011-2014	Laurent Destailleur	<eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/ecotaxdeee/class/actions_ecotaxdeee.class.php
 *	\ingroup    ecotaxdeee
 *	\brief      File to control actions
 */
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";


/**
 *	Class to manage hooks for module PartiPirate
 */
class ActionsEcotaxdeee
{
	var $db;
	var $error;
	var $errors=array();

	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Execute action
	 *
	 * @param	array	$parameters		Array of parameters
	 * @param   Object	$pdfhandler  	PDF builder handler
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	<0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $langs,$conf,$user;
		global $hookmanager;

		// If not text to add, we leave
		if (empty($conf->global->ECOTAXDEEE_DOC_FOOTER)) return 0;

		// If his is not a document we need ecotax
		if (! in_array($parameters['object']->element, array('propal','order','invoice','propale','commande','facture'))) return 0;

		// If we build a document we don't want ecotax on, we leave
		$element='';
		if (($parameters['object']->element == 'propal' || $parameters['object']->element == 'propale') && empty($conf->global->ECOTAXDEEE_USE_ON_PROPOSAL)) return 0;
		elseif (($parameters['object']->element == 'invoice' || $parameters['object']->element == 'facture') && empty($conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE)) return 0;
		elseif (($parameters['object']->element == 'order' || $parameters['object']->element == 'order') && empty($conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_ORDER)) return 0;

		// If there is no ecotax lines.
		$noecotax=1;
		foreach ($parameters['object']->lines as $key => $line) {
			if ($line->special_code == 2) {
				$noecotax=0;
			}
		}
		if ($noecotax) {
			return 0;
		}

		$outputlangs=$parameters['outputlangs'];
		$concatpdffile = 'tmpecotaxdeee'.(empty($user->id)?'':'_'.$user->id);
		$file=$conf->ecotaxdeee->dir_temp.'/'.$concatpdffile.'.pdf';
		dol_mkdir($conf->ecotaxdeee->dir_temp);

		$ret=0; $deltemp=array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		// Get properties of PDF $file
		$type = 'pdf';
		$formatarray=pdf_getFormat();
		$page_largeur = $formatarray['width'];
		$page_hauteur = $formatarray['height'];
		$format = array($page_largeur,$page_hauteur);
		$marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;

		// Generate new file and save it name into concatpdffile
		// into dir $conf->ecotaxdeee->dir_temp.'/'.$concatpdffile.'.pdf'

		// Create pdf instance
		$pdf=pdf_getInstance($format);
		$default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
		$heightforinfotot = 50;	// Height reserved to output the info and total part
		$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
		$heightforfooter = $marge_basse + 8;	// Height reserved to output the footer (value include bottom margin)
		$pdf->SetAutoPageBreak(1, 0);

		if (class_exists('TCPDF')) {
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
		}
		$pdf->SetFont(pdf_getPDFFont($outputlangs));
		// Set path to the background PDF File
		if (empty($conf->global->MAIN_DISABLE_FPDI) && ! empty($conf->global->MAIN_ADD_PDF_BACKGROUND)) {
			$pagecount = $pdf->setSourceFile($conf->mycompany->dir_output.'/' . getDolGlobalString('MAIN_ADD_PDF_BACKGROUND'));
			$tplidx = $pdf->importPage(1);
		}

		$pdf->Open();
		if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);
		$pdf->SetMargins($marge_gauche, $marge_haute, $marge_droite);   // Left, Top, Right

		// New page
		$pdf->AddPage();
		if (! empty($tplidx)) $pdf->useTemplate($tplidx);

		$pdf->writeHTMLCell($page_largeur - $marge_gauche - $marge_droite, $page_hauteur - $marge_haute - $marge_basse, $marge_gauche, $marge_haute, $conf->global->ECOTAXDEEE_DOC_FOOTER);

		$pdf->Close();

		$pdf->Output($file, 'F');

		unset($pdf);
		// Annexe file was generated

		$filetoconcat1=array($parameters['file']);
		$filetoconcat2=array($file);
		//var_dump($parameters['object']->element); exit;
		//var_dump($concatpdffile);
		dol_syslog(get_class($this).'::afterPDFCreation '.$filetoconcat1.' - '.$filetoconcat2);

		$filetoconcat = array_merge($filetoconcat1, $filetoconcat2);

		// Create empty PDF
		$pdf=pdf_getInstance($format);
		if (class_exists('TCPDF')) {
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
		}
		$pdf->SetFont(pdf_getPDFFont($outputlangs));

		if ($conf->global->MAIN_DISABLE_PDF_COMPRESSION) $pdf->SetCompression(false);
		//$pdf->SetCompression(false);

		$pagecount = $this->concat($pdf, $filetoconcat);

		if ($pagecount) {
			$pdf->Output($filetoconcat1[0], 'F');
			if (! empty($conf->global->MAIN_UMASK)) {
				@chmod($file, octdec($conf->global->MAIN_UMASK));
			}
			if (! empty($deltemp)) {
				// Delete temp files
				foreach ($deltemp as $dirtemp) {
					dol_delete_dir_recursive($dirtemp);
				}
			}
		}

		return $ret;
	}

	/**
	 * concat
	 * @param unknown_type $pdf    Pdf
	 * @param unknown_type $files  files
	 */
	function concat(&$pdf, $files)
	{
		foreach ($files as $file) {
			$pagecount = $pdf->setSourceFile($file);
			for ($i = 1; $i <= $pagecount; $i++) {
				$tplidx = $pdf->ImportPage($i);
				$s = $pdf->getTemplatesize($tplidx);
				$pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
				$pdf->useTemplate($tplidx);
			}
		}

		return $pagecount;
	}
}
