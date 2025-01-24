<?php
/* Copyright (C) 2011-2025	Laurent Destailleur	<eldy@users.sourceforge.net>
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
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";

/**
 *	Class to manage hooks for module ConcatPdf
 */
class ActionsConcatPdf
{
	public $db;
	public $error;
	public $errors=array();

	// For Hookmanager return
	public $resprints;
	public $results = array();

	public $tpls;


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
		global $langs, $conf, $form;

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
		//$preselected = (isset($object->extraparams['concatpdf'][0]) ? $object->extraparams['concatpdf'][0] : -1);	// string with the saved preselected string or -1
		$arraypreselected = (isset($object->extraparams['concatpdf']) ? $object->extraparams['concatpdf'] : -1);	// array with the saved preselected string or -1

		// Get $arraydefaultselection
		$arraydefaultselection = array();
		if (getDolGlobalString('CONCATPDF_PRESELECTED_MODELS')) {
			// List of value key into setup -> value for modulepart
			$altkey=array('proposal'=>'propal', 'order'=>'commande', 'invoice'=>'facture', 'supplier_order'=>'commande_fournisseur', 'invoice_order'=>'facture_fournisseur');

			// getDolGlobalString('CONCATPDF_PRESELECTED_MODELS') may contains value of preselected model with format
			// propal:model1a,model1b;invoice:model2;...
			$tmparray=explode(';', getDolGlobalString('CONCATPDF_PRESELECTED_MODELS'));
			$tmparray2=array();
			foreach ($tmparray as $val) {
				$tmp=explode(':', $val);
				if (! empty($tmp[1])) {
					$tmparray2[$tmp[0]]=$tmp[1];
				}
			}
			// Extract the string with preselected template for the object type
			foreach ($tmparray2 as $key => $val) {
				if ($modulepart == $key || (array_key_exists($key, $altkey) && $modulepart == $altkey[$key])) {
					$arraydefaultselection = explode(',', $val);		// $preselected is 'mytemplate' or 'mytemplate1,mytemplate2'
					break;
				}
			}
		}
		// Here, $arraydefaultselection is array('mytemplate') or array('mytemplate1', 'mytemplate2')

		if (!is_array($arraypreselected)) {
			$arraypreselected = $arraydefaultselection;
		}
		if (!is_array($arraypreselected)) {
			$arraypreselected = array();
		}

		// Define $morefile, the list of possible files to concat (changing the label)
		if (! empty($staticpdf)) {	// array of default file to select, defined into setup
			foreach ($staticpdf as $filename) {
				$newfilekey=basename($filename, ".pdf");	// We do not remove extension if it is uppercase .PDF otherwise there is no way to retrieve file name later
				$newfilelabel=$newfilekey;

				if (!empty($arraydefaultselection) && in_array($newfilekey, $arraydefaultselection)) {
					$newfilelabel.=' ('.$langs->trans("Default").')';
				}
				$morefiles[$newfilekey] = $newfilelabel;
			}
		}
		if (! empty($modelpdf)) {
			foreach ($modelpdf as $filename) {
				$newfilekey=basename($filename, ".php");
				$newfilelabel=$newfilekey;
				if (!empty($arraydefaultselection) && in_array($newfilekey, $arraydefaultselection)) {
					$newfilelabel.=' ('.$langs->trans("Default").')';
				}
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

			if (getDolGlobalString('CONCATPDF_MULTIPLE_CONCATENATION_ENABLED')) {
				foreach($arraypreselected as $tmpkey => $tmpval) {
					$arraypreselected[$tmpkey] = preg_replace('/\.pdf$/i', '', $tmpval);
				}
				$out.='<div class="valignmiddle inline-block minwidth300imp">';
				$out.= $form->multiselectarray('concatpdffile', $morefiles, $arraypreselected, 0, 0, 'minwidth100', 1, '95%');
				$out.='</div>';
			} else {
				$preselected = preg_replace('/\.pdf$/i', '', reset($arraypreselected));
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

		$ret=0; $deltemp=array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		if (! is_object($parameters['object'])) {
			dol_syslog("Trigger afterPDFCreation was called but parameter 'object' was not set by caller.", LOG_WARNING);
			return 0;
		}

		$check='alpha';
		if (! empty($conf->global->CONCATPDF_MULTIPLE_CONCATENATION_ENABLED)) {
			$check='array';
		}

		$concatpdffile = GETPOST('concatpdffile', $check);
		if (! is_array($concatpdffile)) {
			if (! empty($concatpdffile)) {
				$concatpdffile = array($concatpdffile);
			} else {
				$concatpdffile = array();
			}
		}

		// Defined $preselected value
		$preselected=(isset($parameters['object']->extraparams['concatpdf'][0])?$parameters['object']->extraparams['concatpdf'][0]:-1);	// string with preselected string

		$formwassubmittedwithemptyselection = (GETPOST('builddoc_generatebutton') && ! GETPOST('concatpdffile'));

		// Includes default models if no model selection
		if (empty($concatpdffile) && ! $formwassubmittedwithemptyselection) {
			//var_dump(getDolGlobalString('CONCATPDF_PRESELECTED_MODELS'));
			if ($preselected == -1 && getDolGlobalString('CONCATPDF_PRESELECTED_MODELS')) {

				// List of value key into setup -> value for modulepart
				$altkey=array('proposal'=>'propal', 'order'=>'commande', 'invoice'=>'facture', 'supplier_order'=>'order_supplier', 'supplier_invoice'=>'invoice_supplier');

				// getDolGlobalString('CONCATPDF_PRESELECTED_MODELS') may contains value of preselected model with format
				// proposal:model1a,model1b;invoice:model2;...
				$tmparray=explode(';', getDolGlobalString('CONCATPDF_PRESELECTED_MODELS'));
				$tmparray2=array();
				foreach ($tmparray as $val) {
					$tmp=explode(':', $val);
					if (! empty($tmp[1])) {
						$tmparray2[$tmp[0]]=$tmp[1];
					}
				}
				foreach ($tmparray2 as $key => $val) {	// for example: $key is 'proposal' and value is 'model1a,model1b'
					//var_dump($key.' - '.$altkey[$key].' - '.$val.' - '.$parameters['object']->element);
					if (isset($parameters['object']->element) && ($parameters['object']->element == $key || $parameters['object']->element == $altkey[$key])) {
						$tmpval = explode(',', $val);
						foreach($tmpval as $val2) {
							$concatpdffile[] = preg_replace('/\.pdf$/i', '', $val2);
						}
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
			$hidedetails = $hidedesc = $hideref = 0;
			foreach ($concatpdffile as $concatfile) {
				// We search which second file to add (or generate it if file to add as a name matching pdf__...modules)
				if (preg_match('/^pdf_(.*)+\.modules/', $concatfile)) {
					// We will generate the file to concat
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
				//$pdf->SetAutoPageBreak(1, 0);
				if (class_exists('TCPDF')) {
					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);
				}
				$pdf->SetFont(pdf_getPDFFont($outputlangs));

				if (getDolGlobalString('MAIN_DISABLE_PDF_COMPRESSION')) {
					$pdf->SetCompression(false);
				}
				//$pdf->SetCompression(false);

				if (getDolGlobalString('CONCATPDF_MIXED_CONCATENATION_ENABLED')) {
					$pagecount = $this->concatMixed($pdf, reset($filetoconcat1), $filetoconcat2);
				} else {
					$pagecount = $this->concat($pdf, $filetoconcat);
				}

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
				if (is_object($parameters['object'])) {
					$parameters['object']->extraparams = array_merge(is_array($parameters['object']->extraparams) ? $parameters['object']->extraparams : array(), $params);
				}
			}
		} else {
			// Remove extraparams for concatpdf
			if (isset($parameters['object']->extraparams['concatpdf'])) {
				unset($parameters['object']->extraparams['concatpdf']);
			}
		}

		if (is_object($parameters['object']) && method_exists($parameters['object'], 'setExtraParameters')) {
			$parameters['object']->setExtraParameters();
		}

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
			if ($file == '/home/ldestailleur/git/dolibarr_16.0/documents/concatpdf/invoices/FA1803-0795.pdf') {
				continue;
			}
			if (dol_is_file($file)) {	// We ignore file if not found so if file has been removed we can still generate the PDF.
				$pagecounttmp = $pdf->setSourceFile($file);
				if ($pagecounttmp) {
					for ($i = 1; $i <= $pagecounttmp; $i++) {
						try {
							$tplidx = $pdf->ImportPage($i);

							// TODO Read /Annot to get links and save same after useTemplate
							/*
							$pageno = $i;
							$tpl =& $pdf->tpls[$tplidx];
							$parser =& $tpl['parser'];
							var_dump($parser);exit;

							if (isset($parser->pages[$pageno - 1][1][1]['/Annots'])) {
								$annots = $parser->pages[$pageno - 1][1][1]['/Annots'];

								$annots = $this->resolve($parser, $annots);
								var_dump($annots);
								$links = array();
								foreach ($annots[1] as $annot) if ($annot[0] == PDF_TYPE_DICTIONARY) {
									// all links look like:  << /Type /Annot /Subtype /Link /Rect [...] ... >>
									if ($annot[1]['/Type'][1] == '/Annot' && $annot[1]['/Subtype'][1] == '/Link') {
										$rect = $annot[1]['/Rect'];
										if ($rect[0] == PDF_TYPE_ARRAY && count($rect[1]) == 4) {
											$x = $rect[1][0][1]; $y = $rect[1][1][1];
											$x2 = $rect[1][2][1]; $y2 = $rect[1][3][1];
											$w = $x2 - $x; $h = $y2 - $y;
											$h = -$h;
										}
										if (isset($annot[1]['/A'])) {
											$A = $annot[1]['/A'];

											if ($A[0] == PDF_TYPE_DICTIONARY && isset($A[1]['/S'])) {
												$S = $A[1]['/S'];

												//  << /Type /Annot ... /A << /S /URI /URI ... >> >>
												if ($S[1] == '/URI' && isset($A[1]['/URI'])) {
													$URI = $A[1]['/URI'];

													if (is_string($URI[1])) {
														$uri = str_replace("\\000", '', trim($URI[1]));
														if (!empty($uri)) {
															$links[] = array($x, $y, $w, $h, $uri);
														}
													}

													//  << /Type /Annot ... /A << /S /GoTo /D [%d 0 R /Fit] >> >>
												} else if ($S[1] == '/GoTo' && isset($A[1]['/D'])) {
													$D = $A[1]['/D'];
													if ($D[0] == PDF_TYPE_ARRAY && count($D[1]) > 0 && $D[1][0][0] == PDF_TYPE_OBJREF) {
														$target_pageno = $this->findPageNoForRef($parser, $D[1][0]);
														if ($target_pageno >= 0) {
															$links[] = array($x, $y, $w, $h, $target_pageno);
														}
													}
												}
											}

										} else if (isset($annot[1]['/Dest'])) {
											$Dest = $annot[1]['/Dest'];

											//  << /Type /Annot ... /Dest [42 0 R ...] >>
											if ($Dest[0] == PDF_TYPE_ARRAY && $Dest[0][1][0] == PDF_TYPE_OBJREF) {
												$target_pageno = $this->findPageNoForRef($parser, $Dest[0][1][0]);
												if ($target_pageno >= 0) {
													$links[] = array($x, $y, $w, $h, $target_pageno);
												}
											}
										}
									}
								}
								$tpl['links'] = $links;
							}
							*/

							$s = $pdf->getTemplatesize($tplidx);
							$pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
							$pdf->useTemplate($tplidx);


							// apply links from the template
							/*
							$tpl =& $this->tpls[$tplidx];
							if (isset($tpl['links'])) {
								foreach ($tpl['links'] as $link) {
									// $link[4] is either a string (external URL) or an integer (page number)
									if (is_int($link[4])) {
										$l = $this->AddLink();
										$this->SetLink($l, 0, $link[4]);
										$link[4] = $l;
									}
									$pdf->PageLinks[$this->page][] = $link;
								}
							}
							*/
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

	/**
	 * Concat PDF files in mixed mode (only on back pages)
	 *
	 * @param 	PDF		$pdf    Pdf
	 * @param 	array	$files  Array of files to concat.
	 * @return	int				Number of files
	 */
	function concatMixed(&$pdf, $mainpdf, $files)
	{
		$totalcgupagecount = $pagecount = 0;
		$cgupage = [];
		foreach ($files as $file) {
			if (dol_is_file($file)) {	// We ignore file if not found so if ile has been removed we can still generate the PDF.
				$pagecounttmp = $pdf->setSourceFile($file);
				for ($i = 1; $i <= $pagecounttmp; $i++) {
					$totalcgupagecount++;
					$cgupage[$totalcgupagecount] = $file;
				}
			}
		}

		$currentcgupagenum = 1;
		$pagecounttmp = $pdf->setSourceFile($mainpdf);
		if ($pagecounttmp) {
			for ($i = 1; $i <= $pagecounttmp; $i++) {
				try {
					//front
					$tplidx = $pdf->ImportPage($i);
					$s = $pdf->getTemplatesize($tplidx);
					$pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
					$pdf->useTemplate($tplidx);

					//back
					if($currentcgupagenum < $totalcgupagecount) {
						$pdf->setSourceFile($cgupage[$currentcgupagenum]);
						$tplidx = $pdf->ImportPage($currentcgupagenum);
						$s = $pdf->getTemplatesize($tplidx);
						$pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
						$pdf->useTemplate($tplidx);
						$currentcgupagenum++;
					}
					//return to front for next turn
					$pdf->setSourceFile($mainpdf);
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

		//add end of cgu if number page > main page
		for ($i = $currentcgupagenum; $i <= $totalcgupagecount; $i++) {
			$pdf->setSourceFile($cgupage[$currentcgupagenum]);
			$tplidx = $pdf->ImportPage($currentcgupagenum);
			$s = $pdf->getTemplatesize($tplidx);
			$pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
			$pdf->useTemplate($tplidx);
		}

		return $pagecount;
	}


	// Add methods to complete the import and useTemplate

	// default maxdepth prevents an infinite recursion on malformed PDFs (not theoretical, actually found in the wild)
	function resolve(&$parser, $smt, $maxdepth=10) {
		if ($maxdepth == 0)
			return $smt;

			if ($smt[0] == PDF_TYPE_OBJREF) {
				$result = $this->pdf_resolve_object($parser->c, $smt, true, $parser);
				return $this->resolve($parser, $result, $maxdepth-1);

			} else if ($smt[0] == PDF_TYPE_OBJECT) {
				return $this->resolve($parser, $smt[1], $maxdepth-1);

			} else if ($smt[0] == PDF_TYPE_ARRAY) {
				$result = array();
				foreach ($smt[1] as $item) {
					$result[] = $this->resolve($parser, $item, $maxdepth-1);
				}
				$smt[1] = $result;
				return $smt;

			} else if ($smt[0] == PDF_TYPE_DICTIONARY) {
				$result = array();
				foreach ($smt[1] as $key => $item) {
					$result[$key] = $this->resolve($parser, $item, $maxdepth-1);
				}
				$smt[1] = $result;
				return $smt;

			} else {
				return $smt;
			}
	}


	/**
	 * Resolve an object
	 *
	 * @param object $c pdf_context
	 * @param array $obj_spec The object-data
	 * @param boolean $encapsulate Must set to true, cause the parsing and fpdi use this method only without this para
	 */
	function pdf_resolve_object(&$c, $obj_spec, $encapsulate = true, $parser) {
		// Exit if we get invalid data
		if (!is_array($obj_spec)) {
			$ret = false;
			return $ret;
		}

		if ($obj_spec[0] == PDF_TYPE_OBJREF) {
			//var_dump($c);

			// This is a reference, resolve it
			if (isset($parser->xref['xref'][$obj_spec[1]][$obj_spec[2]])) {

				// Save current file position
				// This is needed if you want to resolve
				// references while you're reading another object
				// (e.g.: if you need to determine the length
				// of a stream)

					$old_pos = ftell($c->file);

					// Reposition the file pointer and
					// load the object header.
					$c->reset($parser->xref['xref'][$obj_spec[1]][$obj_spec[2]]);

					$header = $parser->pdf_read_value($c);

					if ($header[0] != PDF_TYPE_OBJDEC || $header[1] != $obj_spec[1] || $header[2] != $obj_spec[2]) {
						$toSearchFor = $obj_spec[1] . ' ' . $obj_spec[2] . ' obj';
						if (preg_match('/' . $toSearchFor . '/', $c->buffer)) {
							$c->offset = strpos($c->buffer, $toSearchFor) + strlen($toSearchFor);
							// reset stack
							$c->stack = array();
						} else {
							$parser->error("Unable to find object ({$obj_spec[1]}, {$obj_spec[2]}) at expected location");
						}
					}

					// If we're being asked to store all the information
					// about the object, we add the object ID and generation
					// number for later use
					$result = array();
					$parser->actual_obj =& $result;
					if ($encapsulate) {
						$result = array (
							PDF_TYPE_OBJECT,
							'obj' => $obj_spec[1],
							'gen' => $obj_spec[2]
						);
					}

					// Now simply read the object data until
					// we encounter an end-of-object marker
					while(1) {
						$value = $parser->pdf_read_value($c);
						if ($value === false || count($result) > 4) {
							// in this case the parser coudn't find an endobj so we break here
							break;
						}

						if ($value[0] == PDF_TYPE_TOKEN && $value[1] === 'endobj') {
							break;
						}

						$result[] = $value;
					}

					$c->reset($old_pos);

					if (isset($result[2][0]) && $result[2][0] == PDF_TYPE_STREAM) {
						$result[0] = PDF_TYPE_STREAM;
					}

					return $result;
			}
		} else {
			return $obj_spec;
		}
	}

   /**
    * findPageNoForRef
    *
    * @param  mixed    $parser     Parser
    * @param  string   $pageRef    Page Ref
    * @return int                  Return <0 if error
    */
	function findPageNoForRef(&$parser, $pageRef) {
		$ref_obj = $pageRef[1]; $ref_gen = $pageRef[2];

		foreach ($parser->pages as $index => $page) {
			$page_obj = $page['obj']; $page_gen = $page['gen'];
			if ($page_obj == $ref_obj && $page_gen == $ref_gen) {
				return $index + 1;
			}
		}

		return -1;
	}

}
