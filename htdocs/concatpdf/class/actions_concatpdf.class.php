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
 *	\ingroup    concatpdf
 *	\brief      File to control actions
 */
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";


/**
 *	Class to manage hooks for module ConcatPdf
 */
class ActionsConcatPdf
{
	public $db;
	public $error;
	public $errors=array();

	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Complete doc forms (set this->resprint).
	 *
	 * @param	array	$parameters		Array of parameters
	 * @param	object	$object			Object
	 * @return  int 		        	<0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	public function formBuilddocOptions($parameters, &$object)
	{
		global $langs, $user, $conf, $form;

		$langs->load("concatpdf@concatpdf");

		$out='';

		$morefiles=array();

		if ($parameters['modulepart'] == 'propal') {
			$staticpdf=glob($conf->concatpdf->dir_output."/proposals/*.[pP][dD][fF]");
			$modelpdf=glob($conf->concatpdf->dir_output."/proposals/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'order'   || $parameters['modulepart'] == 'commande') {
			$staticpdf=glob($conf->concatpdf->dir_output."/orders/*.[pP][dD][fF]");
			$modelpdf=glob($conf->concatpdf->dir_output."/orders/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'invoice' || $parameters['modulepart'] == 'facture') {
			$staticpdf=glob($conf->concatpdf->dir_output."/invoices/*.[pP][dD][fF]");
			$modelpdf=glob($conf->concatpdf->dir_output."/invoices/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'supplier_proposal') {
			$staticpdf=glob($conf->concatpdf->dir_output."/supplier_proposals/*.[pP][dD][fF]");
			$modelpdf=glob($conf->concatpdf->dir_output."/supplier_proposals/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'supplier_order' || $parameters['modulepart'] == 'commande_fournisseur') {
			$staticpdf=glob($conf->concatpdf->dir_output."/supplier_orders/*.[pP][dD][fF]");
			$modelpdf=glob($conf->concatpdf->dir_output."/supplier_orders/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'supplier_invoice' || $parameters['modulepart'] == 'facture_fournisseur') {
			$staticpdf=glob($conf->concatpdf->dir_output."/supplier_invoices/*.[pP][dD][fF]");
			$modelpdf=glob($conf->concatpdf->dir_output."/supplier_invoices/pdf_*.modules.php");
		}
		if ($parameters['modulepart'] == 'contract' || $parameters['modulepart'] == 'contract') {
			$staticpdf=glob($conf->concatpdf->dir_output."/contracts/*.[pP][dD][fF]");
			$modelpdf=glob($conf->concatpdf->dir_output."/contracts/pdf_*.modules.php");
		}

		$modulepart = $parameters['modulepart'];

		// Defined $preselected value
		$preselected=(isset($object->extraparams['concatpdf'][0])?$object->extraparams['concatpdf'][0]:-1);	// string with preselected string
		if ($preselected == -1 && ! empty($conf->global->CONCATPDF_PRESELECTED_MODELS)) {
			// List of value key into setup -> value for modulepart
			$altkey=array('proposal'=>'propal', 'order'=>'commande', 'invoice'=>'facture', 'supplier_order'=>'commande_fournisseur', 'invoice_order'=>'facture_fournisseur');

			// $conf->global->CONCATPDF_PRESELECTED_MODELS may contains value of preselected model with format
			// propal:model1a,model1b;invoice:model2;...
			$tmparray=explode(';', $conf->global->CONCATPDF_PRESELECTED_MODELS);
			$tmparray2=array();
			foreach ($tmparray as $val) {
				$tmp=explode(':', $val);
				if (! empty($tmp[1])) $tmparray2[$tmp[0]]=$tmp[1];
			}
			foreach ($tmparray2 as $key => $val) {
				if ($modulepart == $key || (array_key_exists($key, $altkey) && $modulepart == $altkey[$key])) $preselected=$val;		// $preselected is 'mytemplate' or 'mytemplate1,mytemplate2'
			}
		}

		if (! empty($staticpdf)) {
			foreach ($staticpdf as $filename) {
				$newfilekey=basename($filename, ".pdf");	// We do not remove extension if it is uppercase .PDF otherwise there is no way to retrieve file name later
				$newfilelabel=$newfilekey;
				if ($preselected && $newfilekey == $preselected) $newfilelabel.=' ('.$langs->trans("Default").')';
				$morefiles[$newfilekey] = $newfilelabel;
			}
		}
		if (! empty($modelpdf)) {
			foreach ($modelpdf as $filename) {
				$newfilekey=basename($filename, ".php");
				$newfilelabel=$newfilekey;
				if ($preselected && $newfilekey == $preselected) $newfilelabel.=' ('.$langs->trans("Default").')';
				$morefiles[$newfilekey] = $newfilelabel;
			}
		}

		if (empty($morefiles)) {
			print "\n".'<!-- No files found for concat parameter[modulepart]='.$parameters['modulepart'].' -->'."\n";
		} else {
			$colspan = (empty($parameters['colspan']) ? 4 : $parameters['colspan']);

			$out.='<tr class="liste_titre">';
			$out.='<td align="left" colspan="'.$colspan.'" class="formdoc">';
			$out.='<div class="valignmiddle inline-block hideonsmartphone">'.$langs->trans("ConcatFile").'</div> ';

			if (!empty($conf->global->CONCATPDF_MULTIPLE_CONCATENATION_ENABLED)) {
				$arraypreselected = explode(',', $preselected);
				foreach($arraypreselected as $tmpkey => $tmpval) {
					$arraypreselected[$tmpkey] = preg_replace('/\.pdf$/i', '', $tmpval);
				}
				$out.='<div class="valignmiddle inline-block minwidth300imp">';
				$out.= $form->multiselectarray('concatpdffile', $morefiles, (! empty($object->extraparams['concatpdf'])?$object->extraparams['concatpdf']:$arraypreselected), 0, 0, 'minwidth100', 1, '95%');
				$out.='</div>';
			} else {
				$preselected = preg_replace('/\.pdf$/i', '', $preselected);
				$out.= '<!-- preselected value is '.$preselected.' (key to set preselected value in CONCATPDF_PRESELECTED_MODELS is '.$parameters['modulepart'].') -->';
				$out.= $form->selectarray('concatpdffile', $morefiles, $preselected, 1, 0, 0);
			}
			$out.='</td></tr>';
		}

		$this->resprints = $out;

		return 0;
	}



	/**
	 * Execute action
	 *
	 * @param	array	$parameters		Array of parameters
	 * @param   Object	$pdfhandler   	PDF builder handler
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	<0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $langs,$conf;
		global $hookmanager;

		$outputlangs=$langs;

		//var_dump($parameters['object']);

		$ret=0; $deltemp=array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		if (! is_object($parameters['object'])) {
			dol_syslog("Trigger afterPDFCreation was called but parameter 'object' was not set by caller.", LOG_WARNING);
			return 0;
		}

		$check='alpha';
		if (! empty($conf->global->CONCATPDF_MULTIPLE_CONCATENATION_ENABLED)) $check='array';

		$concatpdffile = GETPOST('concatpdffile', $check);
		if (! is_array($concatpdffile)) {
			if (! empty($concatpdffile)) $concatpdffile = array($concatpdffile);
			else $concatpdffile = array();
		}


		// Defined $preselected value
		$preselected=(isset($parameters['object']->extraparams['concatpdf'][0])?$parameters['object']->extraparams['concatpdf'][0]:-1);	// string with preselected string

		$formwassubmittedwithemptyselection = (GETPOST('builddoc_generatebutton') && ! GETPOST('concatpdffile'));

		// Includes default models if no model selection
		if (empty($concatpdffile) && ! $formwassubmittedwithemptyselection) {
			//var_dump($conf->global->CONCATPDF_PRESELECTED_MODELS);
			if ($preselected == -1 && ! empty($conf->global->CONCATPDF_PRESELECTED_MODELS)) {
				// List of value key into setup -> value for modulepart
				$altkey=array('proposal'=>'propal', 'order'=>'commande', 'invoice'=>'facture', 'supplier_order'=>'order_supplier', 'supplier_invoice'=>'invoice_supplier');

				// $conf->global->CONCATPDF_PRESELECTED_MODELS may contains value of preselected model with format
				// propal:model1a,model1b;invoice:model2;...
				$tmparray=explode(';', $conf->global->CONCATPDF_PRESELECTED_MODELS);
				$tmparray2=array();
				foreach ($tmparray as $val) {
					$tmp=explode(':', $val);
					if (! empty($tmp[1])) $tmparray2[$tmp[0]]=$tmp[1];
				}
				foreach ($tmparray2 as $key => $val) {
					//var_dump($key.' - '.$altkey[$key].' - '.$val.' - '.$parameters['object']->element);
					if (isset($parameters['object']->element) && ($parameters['object']->element == $key || $parameters['object']->element == $altkey[$key])) {
						$concatpdffile[]=$val;
					}
				}
			} else {
				$concatpdffile = empty($parameters['object']->extraparams['concatpdf']) ? '' : $parameters['object']->extraparams['concatpdf'];
			}
		}

		$element = '';
		if (isset($parameters['object']->element)) {
			if ($parameters['object']->element == 'propal')  $element='proposals';
			if ($parameters['object']->element == 'order'   || $parameters['object']->element == 'commande') $element='orders';
			if ($parameters['object']->element == 'invoice' || $parameters['object']->element == 'facture')  $element='invoices';
			if ($parameters['object']->element == 'proposal_supplier' || $parameters['object']->element == 'supplier_proposal')  $element='supplier_proposals';
			if ($parameters['object']->element == 'order_supplier' || $parameters['object']->element == 'commande_fournisseur')  $element='supplier_orders';
			if ($parameters['object']->element == 'invoice_supplier' || $parameters['object']->element == 'facture_fournisseur')  $element='supplier_invoices';
			if ($parameters['object']->element == 'contract' || $parameters['object']->element == 'contrat')  $element='contracts';
		}

		$filetoconcat1=array($parameters['file']);
		$filetoconcat2=array();

		if (! empty($concatpdffile) && $concatpdffile[0] != -1) {
			foreach ($concatpdffile as $concatfile) {
				// We search which second file to add (or generate it if file to add as a name starting with pdf___)
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
					$filetoconcat2[] = $dir . "/" . $objectref . (preg_match('/\.pdf$/i', $objectref)?'':".pdf");

					$deltemp[] = $dir;
				} else {
					$filetoconcat2[] = $conf->concatpdf->dir_output.'/'.$element.'/'.$concatfile.(preg_match('/\.pdf$/i', $concatfile)?'':".pdf");
				}
			}

			dol_syslog(get_class($this).'::afterPDFCreation '.join(',', $filetoconcat1).' - '.join(',', $filetoconcat2));

			if (! empty($filetoconcat2) && ! empty($concatpdffile) && $concatpdffile != '-1') {
				$filetoconcat = array_merge($filetoconcat1, $filetoconcat2);

				$formatarray = pdf_getFormat();
				$format = array($formatarray['width'], $formatarray['height']);

				// Create empty PDF
				$pdf=pdf_getInstance($format);
				if (class_exists('TCPDF')) {
					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);
				}
				$pdf->SetFont(pdf_getPDFFont($outputlangs));

				if (!empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) {
					$pdf->SetCompression(false);
				}
				//$pdf->SetCompression(false);

				$pagecount = $this->concat($pdf, $filetoconcat);

				if ($pagecount > 0) {
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
				if ($pagecount < 0) {
					return -1;
				}

				// Save selected files into extraparams
				$params['concatpdf'] = $concatpdffile;
				$parameters['object']->extraparams = array_merge($parameters['object']->extraparams, $params);
			}
		} else {
			// Remove extraparams for concatpdf
			if (isset($parameters['object']->extraparams['concatpdf'])) unset($parameters['object']->extraparams['concatpdf']);
		}

		if (is_object($parameters['object']) && method_exists($parameters['object'], 'setExtraParameters')) $result=$parameters['object']->setExtraParameters();

		return $ret;
	}

	/**
	 * Concat PDF files
	 *
	 * @param 	PDF		$pdf    Pdf
	 * @param 	array	$files  Array of files to concat.
	 * @return	int				Number of files
	 */
	public function concat(&$pdf, $files)
	{
		require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
		$pagecount = 0;
		foreach ($files as $file) {
			if (dol_is_file($file)) {	// We ignore file if not found so if ile has been removed we can still generate the PDF.
				$pagecounttmp = $pdf->setSourceFile($file);
				if ($pagecounttmp) {
					for ($i = 1; $i <= $pagecounttmp; $i++) {
						try {
							$tplidx = $pdf->ImportPage($i);
							$s = $pdf->getTemplatesize($tplidx);
							$pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
							$pdf->useTemplate($tplidx);
						} catch (Exception $e) {
							dol_syslog("Error when manipulating some PDF by concatpdf: ".$e->getMessage(), LOG_ERR);
							$this->error = $e->getMessage();
							$this->errors[] = $e->getMessage();
							dol_print_error('', $this->error);  // Remove this when dolibarr is able to report on screen errors reported by this hook.
							return -1;
						}
					}
					$pagecount += $pagecounttmp;
				} else {
					dol_syslog("Error: Can't read PDF content with setSourceFile, for file ".$file, LOG_ERR);
				}
			} else {
				dol_syslog("Error: Can't find PDF file, for file ".$file, LOG_WARNING);
			}
		}

		return $pagecount;
	}
}
