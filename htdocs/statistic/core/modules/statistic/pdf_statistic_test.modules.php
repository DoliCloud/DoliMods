<?php
/* Copyright (C) 2004-2009 Laurent Destailleur          <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin                <regis@dolibarr.fr>
 * Copyright (C) 2008      Raphael Bertrand (Resultic)  <raphael.bertrand@resultic.fr>
 * Copyright (C) 2010      Juanjo Menent			    <jmenent@2byte.es>
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
 *	\file       htdocs/statistics/core/modules/statistic/pdf_statistic_test.modules.php
 *	\ingroup    statistic
 *	\brief      Fichier de la classe permettant de generer les propales au modele Azur
 */
require_once DOL_DOCUMENT_ROOT."/includes/modules/statistic/modules_statistic.php";
require_once DOL_DOCUMENT_ROOT."/product/class/product.class.php";
require_once DOL_DOCUMENT_ROOT."/lib/company.lib.php";
require_once DOL_DOCUMENT_ROOT."/lib/functions2.lib.php";
require_once "../../htdocs/master.inc.php";


/**
 *	\class      pdf_propale_azur
 *	\brief      Classe permettant de generer les propales au modele Azur
 */
class pdf_statistic_test extends ModelePDFStats
{
	var $emetteur;	// Objet societe qui emet


	/**
	 *	Constructeur
	 *	@param  DoliDB  $db		Handler acces base de donnee
	 */
	function pdf_statistic_test($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");

		$this->db = $db;
		$this->name = "azur";
		$this->description = $langs->trans('DocModelAzurDescription');

		// Dimension page pour format A4
		$this->type = 'pdf';
		$this->page_largeur = 210;
		$this->page_hauteur = 297;
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=10;
		$this->marge_droite=10;
		$this->marge_haute=10;
		$this->marge_basse=10;

		// Defini position des colonnes
		$this->posxdesc=$this->marge_gauche+1;
		$this->posxtva=113;
		$this->posxup=126;
		$this->posxqty=145;
		$this->posxdiscount=162;
		$this->postotalht=174;

		$this->tva=array();
		$this->localtax1=array();
		$this->localtax2=array();
		$this->atleastoneratenotnull=0;
		$this->atleastonediscount=0;
	}

	/**
	 *	Fonction generant la propale sur le disque
	 *
	 *	@param	    Propale     $propale			Objet propal a generer (ou id si ancienne methode)
	 *	@param		Translate   $outputlangs		Lang object for output language
	 *  @param      timestamp   $date               Date
	 *	@return	    int                     		1=ok, 0=ko
	 */
	function write_file($propale, $outputlangs, $date)
	{
		global $user,$langs,$conf;

		if (! is_object($outputlangs)) $outputlangs=$langs;
		// Force output charset to ISO, because FPDF expect text to be encoded in ISO
		$sav_charset_output=$outputlangs->charset_output;
		$outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("products");

		if ($conf->propale->dir_output) {
				$dir = "../../documents/statistic";
				$file = $dir . "/stat_stock_".$date.".pdf";


			if (! file_exists($dir)) {
				if (create_exdir($dir) < 0) {
					$this->error=$langs->trans("ErrorCanNotCreateDir", $dir);
					return 0;
				}
			}

			if (file_exists($dir)) {
				// Protection et encryption du pdf
				if ($conf->global->PDF_SECURITY_ENCRYPTION) {
					$pdf=new FPDI_Protection('P', 'mm', $this->format);
					$pdfrights = array('print'); // Ne permet que l'impression du document
					$pdfuserpass = ''; // Mot de passe pour l'utilisateur final
					$pdfownerpass = null; // Mot de passe du proprietaire, cree aleatoirement si pas defini
					$pdf->SetProtection($pdfrights, $pdfuserpass, $pdfownerpass);
				} else {
					$pdf=new FPDI('P', 'mm', $this->format);
				}

				$pdf->Open();
				$pagenb=0;
				$pdf->SetDrawColor(128, 128, 128);

				$pdf->SetTitle($outputlangs->convToOutputCharset($propale->ref));
				$pdf->SetSubject($outputlangs->transnoentities("CommercialProposal"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($propale->ref)." ".$outputlangs->transnoentities("CommercialProposal"));
				if ($conf->global->MAIN_DISABLE_PDF_COMPRESSION) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right
				$pdf->SetAutoPageBreak(1, 0);

				// New page
				$pdf->AddPage();
				$pagenb++;
				$this->_pagehead($pdf, $propale, 1, $outputlangs);
				$pdf->SetFont('Arial', '', 9);
				$pdf->MultiCell(0, 4, '', 0, 'J');		// Set interline to 4
				$pdf->SetTextColor(0, 0, 0);

				$tab_top = 90;
				$tab_top_middlepage = 50;
				$tab_top_newpage = 50;
				$tab_height = 110;
				$tab_height_middlepage = 190;
				$tab_height_newpage = 150;

				$iniY = $tab_top + 7;
				$curY = $tab_top + 7;
				$nexY = $tab_top + 7;

				/***********************************************************************************************/
				//Get all products
				$sql = "SELECT * FROM ".MAIN_DB_PREFIX."product WHERE fk_product_type=0";
				$resql=$this->db->query($sql);
				$numProducts = $row =$this->db->num_rows($resql);
				if ($resql) { // Si la requete a fonction�
					for ($l=0;$l<$numProducts;$l++) {
						$rows[] = $this->db->fetch_array($resql);
					}
				}

				$this->marge_gauche = 20;
				$this->marge_droite = 20;
				$tab_top = 25;
				$tab_height *=2;
				$posx = 20;
				$nexY=0;

				// Boucle sur les lignes
				for ($i = 0 ; $i < $numProducts ; $i++) {
					$stockInit = 0;
					$pmpDateDemande =0;
					$lastStock=0;
					$pmpInit=0;
					$sommeQtPrice=0;
					$sommeQt=0;
					$stockDateDemande=0;

					//Get product stock
					$sql = "SELECT * FROM ".MAIN_DB_PREFIX."product_stock WHERE fk_product=".$rows[$i][rowid];
					$resql=$this->db->query($sql);
					if ($resql) { // Si la requete a fonction�
						$lastStock = $this->db->fetch_array($resql);
					}

					//Calcul stock inital
					$sql = "SELECT * FROM ".MAIN_DB_PREFIX."stock_mouvement WHERE fk_product=".$rows[$i][rowid]." ORDER BY rowid DESC";
					$resql=$this->db->query($sql);
					$numMouvements = $row =$this->db->num_rows($resql);
					if ($resql) { // Si la requete a fonction�
						for ($l=0;$l<$numMouvements;$l++) {
							$mouvementStock[] = $this->db->fetch_array($resql);
						}
					}
					$stockInit = intval($lastStock[reel]);
					for ($k=0;$k<$numMouvements;$k++) {
						$stockInit += (intval($mouvementStock[$k][value])*(-1));
					}

					//pmp init
					$pmpInit = $rows[$i][pmp];

					//Requete mouvement date demand�
					$sql = "SELECT * FROM ".MAIN_DB_PREFIX."stock_mouvement WHERE fk_product=".$rows[$i][rowid]." AND tms<\"".$date."-31\" ORDER BY rowid DESC";
					$resql=$this->db->query($sql);
					$numMouvementsDateDemande = $row =$this->db->num_rows($resql);
					if ($resql) { // Si la requete a fonction�
						for ($l=0;$l<$numMouvementsDateDemande;$l++) {
							$mouvementStockDateDemande[] = $this->db->fetch_array($resql);
						}
					}

					$stockDateDemande = $stockInit;
					for ($k=0;$k<$numMouvementsDateDemande;$k++) {
						//Calcul somme Qt� * price
						$sommeQtPrice += $mouvementStockDateDemande[$k][value] * $rows[$i][pmp];

						//Calcul somme Qt�
						$sommeQt += $mouvementStockDateDemande[$k][value];

						//Calcul Stock date demand�
						$stockDateDemande += (intval($mouvementStockDateDemande[$k][value]));
					}
					//Calcul PMP � la date donn�
					if ($stockInit == 0) {
						$stockInit = 1;
					}
					$pmpDateDemande = ($stockInit*$pmpInit + $sommeQtPrice) / ($sommeQt+$stockInit);



					$curY = $nexY;

					// Libelle de la ligne produit
					$pdf->SetFont('Arial', '', 9);   // Dans boucle pour gerer multi-page

					$pdf->SetXY($posx, $tab_top+1+$curY);
					$pdf->MultiCell(29, 4, $outputlangs->convToOutputCharset($rows[$i][ref]), '', 'L');

					//Label
					$pdf->SetXY($posx + 29, $tab_top+1+$curY);
					$pdf->MultiCell(60, 4, $outputlangs->convToOutputCharset($rows[$i][label]), '', 'L');

					//PMP
					$pdf->SetXY($posx + 90, $tab_top+1+$curY);
					$pdf->MultiCell(28, 4, $outputlangs->convToOutputCharset($pmpDateDemande), '', 'R');

					//Qty
					$pdf->SetXY($posx + 123, $tab_top+1+$curY);
					$pdf->MultiCell(15, 4, $outputlangs->convToOutputCharset($stockDateDemande), '', 'R');

					//Total
					$pdf->SetXY($posx + 140, $tab_top+1+$curY);
					$pdf->MultiCell(28, 4, $outputlangs->convToOutputCharset($pmpDateDemande*$stockDateDemande), '', 'R');

					$nexY+=10;


					if ($nexY > 210) {
						if ($pagenb == 1) {
							$this->_tableau($pdf, $tab_top, $tab_height, $nexY, $outputlangs);
							$bottomlasttab=$tab_top + $tab_height + 1;
						} else {
							$this->_tableau($pdf, $tab_top_newpage, $tab_height, $nexY, $outputlangs);
							$bottomlasttab=$tab_top_newpage + $tab_height + 1;
						}

						$this->_pagefoot($pdf, $com, $outputlangs);

						// New page
						$pdf->AddPage();
						$pagenb++;
						$this->_pagehead($pdf, $com, 0, $outputlangs);
						$pdf->SetFont('Arial', '', 9);
						$pdf->MultiCell(0, 3, '', 0, 'J');		// Set interline to 3
						$pdf->SetTextColor(0, 0, 0);

						$nexY = 0;
					}
				}

				/**********************************************************************************************/

				// Show square
				if ($pagenb == 1) {
					$this->_tableau($pdf, $tab_top, $tab_height, $nexY, $outputlangs);
					$bottomlasttab=$tab_top + $tab_height + 1;
				} else {
					$this->_tableau($pdf, $tab_top_newpage, $tab_height, $nexY, $outputlangs);
					$bottomlasttab=$tab_top_newpage + $tab_height + 1;
				}

				// Pied de page
				$this->_pagefoot($pdf, $propale, $outputlangs);
				$pdf->AliasNbPages();

				$pdf->Close();

				$pdf->Output($file);
				if (! empty($conf->global->MAIN_UMASK))
				@chmod($file, octdec($conf->global->MAIN_UMASK));

				// Add external file
				//$pdfConcat = new concat_pdf();
				//$pdfConcat->setFiles(array($file, DOL_DOCUMENT_ROOT."/includes/modules/propale/morefile.pdf"));
				//$pdfConcat->concat();
				//$pdf->AliasNbPages();
				//$pdfConcat->Output($file);

				$outputlangs->charset_output=$sav_charset_output;
				return 1;   // Pas d'erreur
			} else {
				$this->error=$langs->trans("ErrorCanNotCreateDir", $dir);
				return 0;
			}
		} else {
			$this->error=$langs->trans("ErrorConstantNotDefined", "PROP_OUTPUTDIR");
			return 0;
		}

		$this->error=$langs->trans("ErrorUnknown");
		return 0;   // Erreur par defaut
	}

	/**
	 * Affiche la grille des lignes de propales
	 *
	 * @param unknown $pdf             PDF
	 * @param unknown $tab_top         Tab top
	 * @param unknown $tab_height      Tab height
	 * @param unknown $nexY            Nexy
	 * @param unknown $outputlangs     Outputlangs
	 */
	function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs)
	{
		/*global $conf;
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY (10, 10);
		$pdf->MultiCell(108,2, "Jean-Paul",'','L');*/

		global $conf;

		$pdf->SetDrawColor(128, 128, 128);

		$this->marge_gauche = 20;
		$this->marge_droite = 20;
		$tab_top = 20;
		$tab_height *=2;
		$tab_height -= 200;

		// Rect prend une longueur en 3eme et 4eme param
		$pdf->Rect($this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height);
		// line prend une position y en 3eme et 4eme param
		$pdf->line($this->marge_gauche, $tab_top+5, $this->page_largeur-$this->marge_droite, $tab_top+5);

		$pdf->SetFont('Arial', '', 9);
		$posx=20;
		//$pdf->SetXY ($posx, $tab_top+2);
		//$pdf->MultiCell(18,2, $outputlangs->transnoentities("Date"),'','L');

		//$pdf->line($posx-1+20, $tab_top, $posx-1+20, $tab_top + $tab_height);

		$pdf->SetXY($posx, $tab_top+2);
		$pdf->MultiCell(18, 2, $outputlangs->transnoentities("Ref"), '', 'L');

		$pdf->line($posx-1+30, $tab_top, $posx-1+30, $tab_top + $tab_height);

		$pdf->SetXY($posx+29, $tab_top+2);
		$pdf->MultiCell(50, 2, $outputlangs->transnoentities("Libelle"), '', 'L');

		$pdf->line($posx-1+90, $tab_top, $posx-1+90, $tab_top + $tab_height);

		$pdf->SetXY($posx+89, $tab_top+2);
		$pdf->MultiCell(28, 2, $outputlangs->transnoentities("PMP Unitaire"), '', 'L');

		$pdf->line($posx-1+120, $tab_top, $posx-1+120, $tab_top + $tab_height);

		$pdf->SetXY($posx+119, $tab_top+2);
		$pdf->MultiCell(15, 2, $outputlangs->transnoentities("Qty"), '', 'L');

		$pdf->line($posx-1+140, $tab_top, $posx-1+140, $tab_top + $tab_height);

		$pdf->SetXY($posx+139, $tab_top+2);
		$pdf->MultiCell(28, 2, $outputlangs->transnoentities("Total"), '', 'L');
	}

	/**
	 *  Affiche en-tete propale
	 *
	 *  @param      PDF      $pdf     		Objet PDF
	 *  @param      Propale  $object			Objet propale
	 *  @param      int      $showaddress     0=no, 1=yes
	 *  @param      Translate  $outputlangs		Objet lang cible
	 *  @return    void
	 */
	function _pagehead(&$pdf, $object, $showaddress, $outputlangs)
	{
		global $conf,$langs;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");

		pdf_pagehead($pdf, $outputlangs, $pdf->page_hauteur);

		//Affiche le filigrane brouillon - Print Draft Watermark
		if ($object->statut==0 && (! empty($conf->global->PROPALE_DRAFT_WATERMARK)) ) {
			$watermark_angle=atan($this->page_hauteur/$this->page_largeur);
			$watermark_x=5;
			$watermark_y=$this->page_hauteur-25;  //Set to $this->page_hauteur-50 or less if problems
			$watermark_width=$this->page_hauteur;
			$pdf->SetFont('Arial', 'B', 50);
			$pdf->SetTextColor(255, 192, 203);
			//rotate
			$pdf->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', cos($watermark_angle), sin($watermark_angle), -sin($watermark_angle), cos($watermark_angle), $watermark_x*$pdf->k, ($pdf->h-$watermark_y)*$pdf->k, -$watermark_x*$pdf->k, -($pdf->h-$watermark_y)*$pdf->k));
			//print watermark
			$pdf->SetXY($watermark_x, $watermark_y);
			$pdf->Cell($watermark_width, 25, $outputlangs->convToOutputCharset($conf->global->PROPALE_DRAFT_WATERMARK), 0, 2, "C", 0);
			//antirotate
			$pdf->_out('Q');
		}

		//Prepare la suite
		$pdf->SetTextColor(0, 0, 60);
		$pdf->SetFont('Arial', 'B', 13);

		$posy=$this->marge_haute;

		$pdf->SetXY($this->marge_gauche, $posy);
	}

	/**
	 *   	Show footer of page
	 *
	 *   	@param      PDF        $pdf     		PDF factory
	 * 		@param		Object     $object			Object invoice
	 *      @param      Langs      $outputlangs		Object lang for output
	 * 		@remarks	Need this->emetteur object
	 */
	function _pagefoot(&$pdf, $object, $outputlangs)
	{
		return pdf_pagefoot($pdf, $outputlangs, 'PROPALE_FREE_TEXT', $this->emetteur, $this->marge_basse, $this->marge_gauche, $this->page_hauteur, $object);
	}
}
