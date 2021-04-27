<?php
/* Copyright (C) 2011-2013	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Regis Houssin		<regis.houssin@capnetworks.com>
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
 *	\file       htdocs/concatpdf/class/actions_concatpdf.class.php
 *	\ingroup    societe
 *	\brief      File to control actions
 */
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";


/**
 *	Class to manage hooks for module Partipirate
 */
class ActionsPartiPirate
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
	 * Complete doc forms
	 *
	 * @param	array	$parameters		Array of parameters
	 * @param	object	$object			Object
	 * @return	string					HTML content to add by hook
	 */
	function formBuilddocOptions($parameters, &$object)
	{
		global $langs, $user, $conf;

		$langs->load("concatpdf@concatpdf");
		$form=new Form($this->db);

		$out='';

		$morefiles=array();

		if ($parameters['modulepart'] == 'propal') {
			$staticpdf=glob($conf->concatpdf->dir_output."/proposals/*.pdf");
			$modelpdf=glob($conf->concatpdf->dir_output."/proposals/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'order'   || $parameters['modulepart'] == 'commande') {
			$staticpdf=glob($conf->concatpdf->dir_output."/orders/*.pdf");
			$modelpdf=glob($conf->concatpdf->dir_output."/orders/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'invoice' || $parameters['modulepart'] == 'facture') {
			$staticpdf=glob($conf->concatpdf->dir_output."/invoices/*.pdf");
			$modelpdf=glob($conf->concatpdf->dir_output."/invoices/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'supplier_order' || $parameters['modulepart'] == 'commande_fournisseur') {
			$staticpdf=glob($conf->concatpdf->dir_output."/supplier_orders/*.pdf");
			$modelpdf=glob($conf->concatpdf->dir_output."/supplier_orders/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'supplier_invoice' || $parameters['modulepart'] == 'facture_fournisseur') {
			$staticpdf=glob($conf->concatpdf->dir_output."/supplier_invoices/*.pdf");
			$modelpdf=glob($conf->concatpdf->dir_output."/supplier_invoices/pdf_*.modules.php");
		}

		if (! empty($staticpdf)) {
			foreach ($staticpdf as $filename) {
				$morefiles[] = basename($filename, ".pdf");
			}
		}
		if (! empty($modelpdf)) {
			foreach ($modelpdf as $filename) {
				$morefiles[] = basename($filename, ".php");
			}
		}
		if (empty($morefiles)) print "\n".'<!-- No files found for concat parameter[modulepart]='.$parameters['modulepart'].' -->'."\n";
		else {
			$out.='<tr class="liste_titre">';
			$out.='<td align="left" colspan="4" valign="top" class="formdoc">';
			$out.=$langs->trans("ConcatFile").' ';

			if (! empty($conf->global->MAIN_USE_JQUERY_MULTISELECT) && ! empty($conf->global->CONCATPDF_MULTIPLE_CONCATENATION_ENABLED)) {
				$out.='</td></tr>';

				$out.='<tr><td id="selectconcatpdf" colspan="4" valign="top">';
				$out.= $form->multiselectarray('concatpdffile', $morefiles, (! empty($object->extraparams['concatpdf'])?$object->extraparams['concatpdf']:''), 0, 1, '', 1);
			} else {
				$out.= $form->selectarray('concatpdffile', $morefiles, (isset($object->extraparams['concatpdf'][0])?$object->extraparams['concatpdf'][0]:-1), 1, 0, 1);
			}
			$out.='</td></tr>';
		}

		return $out;
	}



	/**
	 * Execute action
	 *
	 * @param	array	$parameters		Array of parameters
	 * @param   Object	$object    	    Deprecated. This field is nto used
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	<0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	function afterPDFCreation($parameters, &$object, &$action)
	{
		global $langs,$conf;
		global $hookmanager;

		$outputlangs=$langs;

		$ret=0; $deltemp=array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		$check='alpha';
		if (! empty($conf->global->MAIN_USE_JQUERY_MULTISELECT) && ! empty($conf->global->CONCATPDF_MULTIPLE_CONCATENATION_ENABLED)) {
			$check='array';
		}
		$concatpdffile = GETPOST('concatpdffile', $check);
		if (! is_array($concatpdffile) && ! empty($concatpdffile)) $concatpdffile = array($concatpdffile);

		$element='';
		if ($parameters['object']->element == 'propal')  $element='proposals';
		if ($parameters['object']->element == 'order'   || $parameters['object']->element == 'commande') $element='orders';
		if ($parameters['object']->element == 'invoice' || $parameters['object']->element == 'facture')  $element='invoices';
		if ($parameters['object']->element == 'order_supplier' || $parameters['object']->element == 'commande_fournisseur')  $element='supplier_orders';
		if ($parameters['object']->element == 'invoice_supplier' || $parameters['object']->element == 'facture_fournisseur')  $element='supplier_invoices';

		$filetoconcat1=array($parameters['file']);
		$filetoconcat2=array();
		//var_dump($parameters['object']->element); exit;
		//var_dump($concatpdffile);
		if (! empty($concatpdffile) && $concatpdffile[0] != -1) {
			foreach ($concatpdffile as $concatfile) {
				if (preg_match('/^pdf_(.*)+\.modules/', $concatfile)) {
					require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";

					$file = $conf->concatpdf->dir_output.'/'.$element.'/'.$concatfile.'.php';
					$classname = str_replace('.modules', '', $concatfile);
					require_once $file;
					$obj = new $classname($this->db);

					// We save charset_output to restore it because write_file can change it if needed for
					// output format that does not support UTF8.
					$sav_charset_output=$outputlangs->charset_output;
					// Change the output dir
					$srctemplatepath = $conf->concatpdf->dir_temp;
					// Generate pdf
					$obj->write_file($parameters['object'], $outputlangs, $srctemplatepath, $hidedetails, $hidedesc, $hideref, $hookmanager);
					// Restore charset output
					$outputlangs->charset_output=$sav_charset_output;

					$objectref = dol_sanitizeFileName($parameters['object']->ref);
					$dir = $conf->concatpdf->dir_temp . "/" . $objectref;
					$filetoconcat2[] = $dir . "/" . $objectref . ".pdf";

					$deltemp[] = $dir;
				} else {
					$filetoconcat2[] = $conf->concatpdf->dir_output.'/'.$element.'/'.$concatfile.'.pdf';
				}
			}

			dol_syslog(get_class($this).'::afterPDFCreation '.$filetoconcat1.' - '.$filetoconcat2);

			if (! empty($filetoconcat2) && ! empty($concatpdffile) && $concatpdffile != '-1') {
				$filetoconcat = array_merge($filetoconcat1, $filetoconcat2);

				// Create empty PDF
				$pdf=pdf_getInstance();
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

				// Save selected files into extraparams
				$params['concatpdf'] = $concatpdffile;
				$parameters['object']->extraparams = array_merge($parameters['object']->extraparams, $params);
			}
		} else {
			// Remove extraparams for concatpdf
			unset($parameters['object']->extraparams['concatpdf']);
		}

		if (is_object($parameters['object']) && method_exists($parameters['object'], 'setExtraParameters')) $result=$parameters['object']->setExtraParameters();

		return $ret;
	}

	/**
	 *
	 * @param unknown_type $pdf        Pdf
	 * @param unknown_type $files      Files
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
