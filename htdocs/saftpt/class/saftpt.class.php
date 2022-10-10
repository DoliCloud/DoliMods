<?php
/* Copyright (C) 2014      Mário Batista            <mariorbatista@gmail.com> ISCTE-UL Moss
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
 *	\file       htdocs/saftpt/class/saftpt.class.php
 *	\ingroup    saftpt
 *	\brief      File of class to build Saf-t file (PT)
 */


/**
 *	Class to build Saf-t file (PT)
 */

class SaftPt
{
	var $db;
	var $taxexemption='';
	var $date_ini='';
    var $date_fim='';
	var $outputfile='';
	var $filexml='';

	//xml file
	var $doc;
	var $audit;
	var $master;

	/**
	 *    Constructor
	 *
	 *    @param  	DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		$this->db=$db;
	}

	/**
	 *    check if company country is PT-Portugal
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	function country_pt()
    {
		global $conf;

		$res=-1;
		$countrypt=explode(":", ($conf->global->MAIN_INFO_SOCIETE_COUNTRY?$conf->global->MAIN_INFO_SOCIETE_COUNTRY:'PT' ));
		if($countrypt[1]=='PT') $res=1;
		return $res;
    }

	/**
	 *    check if company currency is EUR
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	function currency_eur()
    {
		global $conf;

		$res=-1;
		if($conf->global->MAIN_MONNAIE=='EUR') $res=1;
		return $res;
    }

	/**
	 *    check if exist tax type wirhout classification
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	function taxtype_pt()
    {
		$res=1;
		$sql = "SELECT t.taux ";
        $sql.= "FROM ".MAIN_DB_PREFIX."c_tva as t WHERE t.taux>0 and t.fk_pays IN(SELECT rowid FROM ".MAIN_DB_PREFIX."c_pays WHERE code ='PT' ) AND t.taux NOT IN (SELECT c.code FROM ".MAIN_DB_PREFIX."c_taxtype as c)";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if($num>0) $res=-1;
		}
		else {
			dol_print_error($this->db);
		}
		return $res;
    }

	/**
	 *    check if the value of tax type is correct: RED INT NOR ISE
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	function taxtype_val_pt()
    {
		$res=1;
		$sql = "SELECT t.label ";
        $sql.= "FROM ".MAIN_DB_PREFIX."c_taxtype as t WHERE t.label NOT IN ('RED','INT','NOR','ISE')";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if($num>0) $res=-1;
		}
		else {
			dol_print_error($this->db);
		}
		return $res;
    }


	/**
	 *    method to create saf-t file
	 *
	 */
	function create_file()
    {
        global $conf;
		$outputdir  = $conf->saftpt->dir_output.'/xml';
		dol_mkdir($outputdir); //cria pasta
		$file='Saft_103_'.dol_print_date($this->date_ini, '%Y%m%d').'_'.dol_print_date($this->date_fim, '%Y%m%d').'.xml';
		$this->outputfile = $outputdir.'/'.$file;
		$res=$this->create_xml();
		if ($res) $this->filexml=$file;
    }


	/**
	 *    build saf-t file
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	private function create_xml()
    {
        $this->doc = new DOMDocument('1.0', 'WINDOWS-1252');
		$this->doc->preserveWhiteSpace = false;
		$this->doc->formatOutput = true;
		$this->audit = $this->doc->createElement( 'AuditFile' );
		$this->audit->setAttribute( "xmlns", "urn:OECD:StandardAuditFile-Tax:PT_1.03_01" );
		$this->audit->setAttribute( "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");

		$res=$this->saft_header();
		if (!$res) return -1;
		$this->master = $this->doc->createElement( 'MasterFiles' );
		$res=$this->saft_customer();
		if (!$res) return -1;
		$res=$this->saft_product();
		if (!$res) return -1;
		$res=$this->saft_tax();
		if (!$res) return -1;
		$this->audit->appendChild( $this->master );
		//masterfiles end
		//SalesInvoices begin
		$res=$this->saft_salesinvoices();
		if (!$res) return -1;
		//SalesInvoices end
		$this->doc->appendChild( $this->audit );
		$this->doc->save($this->outputfile);
		return 1;
	}

	/**
	 *    build Header element
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	private function saft_header()
    {
		global $conf;
        $head = $this->doc->createElement( 'Header' );

		$ele = $this->doc->createElement( 'AuditFileVersion','1.03_01' );
		$head->appendChild( $ele );
		//conservatoria + nummero ou nif
		$ele = $this->doc->createElement( 'CompanyID',(! empty($conf->global->MAIN_INFO_RCS) ? $conf->global->MAIN_INFO_RCS : '') . ' '. (! empty($conf->global->MAIN_INFO_APE) ? $conf->global->MAIN_INFO_APE : (! empty($conf->global->MAIN_INFO_SIREN) ? $conf->global->MAIN_INFO_SIREN : '')) ); //conservatoria + num ou nif
		$head->appendChild( $ele );

		$ele = $this->doc->createElement( 'TaxRegistrationNumber',(! empty($conf->global->MAIN_INFO_SIREN) ? $conf->global->MAIN_INFO_SIREN : '') ); //nif sem prefixo do pais
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'TaxAccountingBasis','P' ); // considera sempre dados parciais
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'CompanyName',$conf->global->MAIN_INFO_SOCIETE_NOM ); //denominação comercial da empresa
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'BusinessName',$conf->global->MAIN_INFO_SOCIETE_NOM ); // designacao comercial
		$head->appendChild( $ele );

		$adre = $this->doc->createElement( 'CompanyAddress' );

		$ele = $this->doc->createElement( 'AddressDetail',($conf->global->MAIN_INFO_SOCIETE_ADDRESS?$conf->global->MAIN_INFO_SOCIETE_ADDRESS:'Omisso') );
		$adre->appendChild( $ele );
		$ele = $this->doc->createElement( 'City',($conf->global->MAIN_INFO_SOCIETE_TOWN?$conf->global->MAIN_INFO_SOCIETE_TOWN:'Omissao') );
		$adre->appendChild( $ele );
		$ele = $this->doc->createElement( 'PostalCode',$conf->global->MAIN_INFO_SOCIETE_ZIP );
		$adre->appendChild( $ele );

		$countrypt=explode(":", ($conf->global->MAIN_INFO_SOCIETE_COUNTRY?$conf->global->MAIN_INFO_SOCIETE_COUNTRY:'PT' ));

		$ele = $this->doc->createElement( 'Country',$countrypt[1]);
		$adre->appendChild( $ele );

		$head->appendChild( $adre );

		$ele = $this->doc->createElement( 'FiscalYear', dol_print_date($this->date_ini, '%Y') );
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'StartDate',dol_print_date($this->date_ini, '%Y-%m-%d') );
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'EndDate',dol_print_date($this->date_fim, '%Y-%m-%d') );
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'CurrencyCode','EUR' );
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'DateCreated',dol_print_date(dol_now(), '%Y-%m-%d') );
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'TaxEntity','Global' );
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'ProductCompanyTaxID','999999990' ); //TaxID of company that developed the Dolibarr (TaxID final consumer)
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'SoftwareCertificateNumber','0' ); //certification number or zero
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'ProductID','Dolibarr / Comunidade Dolibarr' );
		$head->appendChild( $ele );
		$ele = $this->doc->createElement( 'ProductVersion',$conf->global->MAIN_VERSION_LAST_INSTALL );
		$head->appendChild( $ele );
		if ($conf->global->MAIN_INFO_SOCIETE_TEL) {
			$ele = $this->doc->createElement( 'Telephone',$conf->global->MAIN_INFO_SOCIETE_TEL ); // not required
			$head->appendChild( $ele );
		}
		if ($conf->global->MAIN_INFO_SOCIETE_FAX) {
			$ele = $this->doc->createElement( 'Fax',$conf->global->MAIN_INFO_SOCIETE_FAX ); // not required
			$head->appendChild( $ele );
		}
		if ($conf->global->MAIN_INFO_SOCIETE_MAIL) {
			$ele = $this->doc->createElement( 'Email',$conf->global->MAIN_INFO_SOCIETE_MAIL ); // not required
			$head->appendChild( $ele );
		}
		$this->audit->appendChild( $head );
		return 1;
    }

	/**
	 *    build Customer element
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	private function saft_customer()
    {
        $sql = "SELECT s.code_client, s.nom, s.address, s.zip, s.town, s.phone, s.fax, s.email, s.siren, p.code as country ";
        $sql.= "FROM ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."c_pays as p WHERE s.fk_pays = p.rowid AND s.client>0 ";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);

				$cust = $this->doc->createElement( 'Customer' );

				$ele = $this->doc->createElement( 'CustomerID',$obj->code_client );
				$cust->appendChild( $ele );
				//no accounting accounts
				$ele = $this->doc->createElement( 'AccountID','Desconhecido' );
				$cust->appendChild( $ele );
				$ele = $this->doc->createElement( 'CustomerTaxID',(! empty($obj->siren) ? $obj->siren : '999999990' ) );
				$cust->appendChild( $ele );
				$ele = $this->doc->createElement( 'CompanyName');
				$ele->appendChild($this->doc->createCDATASection(  $obj->nom ));
				$cust->appendChild( $ele );

				$adre = $this->doc->createElement( 'BillingAddress' );

				$ele = $this->doc->createElement( 'AddressDetail',(! empty($obj->address) ? $obj->address : 'desconhecido' ) ); // no value = desconhecido
				$adre->appendChild( $ele );
				$ele = $this->doc->createElement( 'City',(! empty($obj->town) ? $obj->town : 'desconhecido' ) ); // no value = desconhecido
				$adre->appendChild( $ele );
				$ele = $this->doc->createElement( 'PostalCode',(! empty($obj->zip) ? $obj->zip : '0000-000' ) );
				$adre->appendChild( $ele );
				$ele = $this->doc->createElement( 'Country',$obj->country );
				$adre->appendChild( $ele );

				$cust->appendChild( $adre );
				// not required
				if (! empty($obj->phone)) {
					$ele = $this->doc->createElement( 'Telephone',$obj->phone );
					$cust->appendChild( $ele );
				}
				if (! empty($obj->fax)) {
					$ele = $this->doc->createElement( 'Fax',$obj->fax );
					$cust->appendChild( $ele );
				}
				if (! empty($obj->email)) {
					$ele = $this->doc->createElement( 'Email',$obj->email );
					$cust->appendChild( $ele );
				}

				$ele = $this->doc->createElement( 'SelfBillingIndicator','0' );
				$cust->appendChild( $ele );

				$this->master->appendChild( $cust );
				$i++;
			}
		}
		else {
			dol_print_error($this->db);
			return -1;
		}
		return 1;
    }
	/**
	 *    build Product element
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	private function saft_product()
    {
		$sql = "SELECT p.ref, p.label, p.barcode, p.tva_tx, p.fk_product_type ";
        $sql.= "FROM ".MAIN_DB_PREFIX."product as p ";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);
				$prod = $this->doc->createElement( 'Product' );

				$ele = $this->doc->createElement( 'ProductType',($obj->fk_product_type==1 ? 'S' : 'P') ); // P-product S-services
				$prod->appendChild( $ele );
				$ele = $this->doc->createElement( 'ProductCode',$obj->ref );
				$prod->appendChild( $ele );
				//category modulo not considered
				$ele = $this->doc->createElement( 'ProductGroup',($obj->fk_product_type==1 ? 'Serviços' : 'Produtos') ); //group of product
				$prod->appendChild( $ele );
				$ele = $this->doc->createElement( 'ProductDescription',$obj->label );
				$prod->appendChild( $ele );
				$ele = $this->doc->createElement( 'ProductNumberCode',(! empty($obj->barcode) ? $obj->barcode : $obj->ref ) ); // barcode code or ProductCode
				$prod->appendChild( $ele );

				$this->master->appendChild( $prod );
				$i++;
			}
		}
		else {
			dol_print_error($this->db);
			return -1;
		}
		return 1;
    }

	/**
	 *    build product element
	 *
	 *    @param  	taxrate		$taxrate		tax rate
	 *    @return	string						(ISE, NOR, RED, INT) if OK , ERR if KO
	 */
	private function tax_type($taxrate)
    {
        $out='ERR';
		if ($taxrate==0) {
			$out='ISE';
			return $out;
		}
		$sql = "SELECT t.label ";
        $sql.= "FROM ".MAIN_DB_PREFIX."c_taxtype as t WHERE t.code = ".$taxrate." ";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num>0)
			{
				$obj = $this->db->fetch_object($result);
				$out=$obj->label;
			}
		}
		else {
			dol_print_error($this->db);
		}
		return $out;
    }

	/**
	 *    build TaxTable element
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	private function saft_tax()
    {
        $tax = $this->doc->createElement( 'TaxTable' );
		$sql = "SELECT t.taux, t.note, t.fk_pays ";
        $sql.= "FROM ".MAIN_DB_PREFIX."c_tva as t WHERE t.fk_pays IN(SELECT rowid FROM ".MAIN_DB_PREFIX."c_pays WHERE code ='PT' ) ";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);

				$taxent = $this->doc->createElement( 'TaxTableEntry' );

				$ele = $this->doc->createElement( 'TaxType','IVA' );
				$taxent->appendChild( $ele );
				$ele = $this->doc->createElement( 'TaxCountryRegion','PT' ); //ISO 3166-1 alpha 1
				$taxent->appendChild( $ele );
				$ele = $this->doc->createElement( 'TaxCode',$this->tax_type($obj->taux) ); //RED INT NOR ISE OUT
				$taxent->appendChild( $ele );
				$ele = $this->doc->createElement( 'Description',$obj->note );
				$taxent->appendChild( $ele );
				$ele = $this->doc->createElement( 'TaxPercentage',$obj->taux );
				$taxent->appendChild( $ele );

				$tax->appendChild( $taxent );
				$i++;
			}
		}
		else {
			dol_print_error($this->db);
			return -1;
		}
		$this->master->appendChild( $tax );
		return 1;
    }

	/**
	 *    counter documents
	 *
	 *    @return	int						counter documents if OK, <0 if KO
	 */
	private function invoices_number()
    {
        $out=0;
		$sql = "SELECT COUNT(rowid) AS docsnumber ";
        $sql.= "FROM ".MAIN_DB_PREFIX."facture WHERE datef >= '".$this->db->idate($this->date_ini)."' AND datef <= '".$this->db->idate($this->date_fim)."' AND fk_statut>0";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num>0)
			{
				$obj = $this->db->fetch_object($result);
				$out=$obj->docsnumber;
			}
		}
		else {
			dol_print_error($this->db);
			$out=-1;
		}
		return $out;
    }

	/**
	 *    returm the net total of credits note
	 *
	 *     @return	int						credits notes net total if OK, <0 if KO
	 */
	private function invoices_debit()
    {
        $out=0;
		$sql = "SELECT SUM(total) AS sumval "; //net value
        $sql.= "FROM ".MAIN_DB_PREFIX."facture WHERE type=2 AND datef >= '".$this->db->idate($this->date_ini)."' AND datef <= '".$this->db->idate($this->date_fim)."' AND fk_statut>0";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num>0)
			{
				$obj = $this->db->fetch_object($result);
				$out=round(($obj->sumval * (-1)),2); //positive value
			}
		}
		else {
			dol_print_error($this->db);
			$out=-1;
		}
		return $out;
    }
	/**
	 *    returm the net total of invoice
	 *
	 *    @return	int						invoices net total if OK, <0 if KO
	 */
	private function invoices_credit()
    {
        $out=0;
		$sql = "SELECT SUM(total) AS sumval "; //net value
        $sql.= "FROM ".MAIN_DB_PREFIX."facture WHERE type!=2 AND datef >= '".$this->db->idate($this->date_ini)."' AND datef <= '".$this->db->idate($this->date_fim)."' AND fk_statut>0";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num>0)
			{
				$obj = $this->db->fetch_object($result);
				$out=round($obj->sumval,2); //positive value
			}
		}
		else {
			dol_print_error($this->db);
			$out=-1;
		}
		return $out;
    }
	/**
	 *    returm the user document
	 *
	 *    @param  	DoliDB		$iduser		User ID
	 *    @return	string					user login  if OK, '' if KO
	 */
	private function invoice_user($iduser)
    {
        $out='';
		$sql = "SELECT login ";
        $sql.= "FROM ".MAIN_DB_PREFIX."user WHERE rowid =".$iduser."";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num>0)
			{
				$obj = $this->db->fetch_object($result);
				$out=$obj->login;
			}
		}
		else {
			dol_print_error($this->db);
		}
		return $out;
    }

	/**
	 *    returm the customer number
	 *
	 *    @param  	DoliDB		$idcust		customer ID
	 *    @return	string					customer number  if OK, '' if KO
	 */
	private function invoice_cust($idcust)
    {
        $out='';

		$sql = "SELECT code_client ";
        $sql.= "FROM ".MAIN_DB_PREFIX."societe WHERE rowid =".$idcust."";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num>0)
			{
				$obj = $this->db->fetch_object($result);
				$out=$obj->code_client;
			}
		}
		else {
			dol_print_error($this->db);
		}
		return $out;
    }

	/**
	 *    returm the saf-t document type (FT FS FR ND NC)
	 *
	 *
	 *    @param  	DoliDB		$invtype		Dolibarr document type
	 *    @return	string					FT or NC
	 */
	private function invoice_type($invtype)
    {
        $out='FT';
		if ($invtype==2) $out='NC'; //nota de crédito

		return $out;
    }

	/**
	 *    returm the invoice reference used in credit note
	 *
	 *    @param  	DoliDB		$idfature		invoice ID
	 *    @return	string					invoice reference  if OK, '' if KO
	 */
	private function invoice_ref($idfature)
    {
        $out='';

		$sql = "SELECT facnumber ";
        $sql.= "FROM ".MAIN_DB_PREFIX."facture WHERE rowid =".$idfature."";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num>0)
			{
				$obj = $this->db->fetch_object($result);
				$out=$obj->facnumber;
			}
		}
		else {
			dol_print_error($this->db);
		}
		return $out;
    }

	/**
	 *    build SourceDocuments element
	 *
	 *    @return	int						>0 if OK, <0 if KO
	 */
	private function saft_salesinvoices()
    {
		$sdocs = $this->doc->createElement( 'SourceDocuments' );

        $sinvs = $this->doc->createElement( 'SalesInvoices' );

		$ele = $this->doc->createElement( 'NumberOfEntries',$this->invoices_number() );
		$sinvs->appendChild( $ele );
		//credit note
		$ele = $this->doc->createElement( 'TotalDebit',$this->invoices_debit() ); //sum total amount wirhout Invoicestatus=A or F
		$sinvs->appendChild( $ele );
		//invoices
		$ele = $this->doc->createElement( 'TotalCredit',$this->invoices_credit() ); //sum total amount wirhout Invoicestatus=A or F
		$sinvs->appendChild( $ele );

		//list of documents
		$sql = "SELECT c.rowid, c.facnumber, c.type, c.fk_soc, c.datec, c.datef, c.tva, c.total, c.total_ttc, c.fk_statut, c.fk_user_valid, c.fk_facture_source ";
        $sql.= "FROM ".MAIN_DB_PREFIX."facture as c WHERE c.datef BETWEEN '".$this->db->idate($this->date_ini)."' AND '".$this->db->idate($this->date_fim)."' AND c.fk_statut>0";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);

				$sinv = $this->doc->createElement( 'Invoice' );

				$ele = $this->doc->createElement( 'InvoiceNo',$obj->facnumber ); //document type+space+serie+/+number doc
				$sinv->appendChild( $ele );

				$sinvst = $this->doc->createElement( 'DocumentStatus' );
				$ele = $this->doc->createElement( 'InvoiceStatus','N' ); //N (normal) A (canceled)
				$sinvst->appendChild( $ele );
				$ele = $this->doc->createElement( 'InvoiceStatusDate', dol_print_date($obj->datec,'%Y-%m-%dT%H:%M:%S') ); //AAAA-MM-DDThh:mm:ss
				$sinvst->appendChild( $ele );
				$ele = $this->doc->createElement( 'SourceID',$this->invoice_user($obj->fk_user_valid) ); //user
				$sinvst->appendChild( $ele );
				$ele = $this->doc->createElement( 'SourceBilling','P' );
				$sinvst->appendChild( $ele );
				$sinv->appendChild( $sinvst ); //end DocumentStatus

				$ele = $this->doc->createElement( 'Hash','0' ); // 0 no software certification
				$sinv->appendChild( $ele );
				//$ele = $this->doc->createElement( 'HashControl','0' ); //no required
				//$sinv->appendChild( $ele );
				$ele = $this->doc->createElement( 'Period',dol_print_date($obj->datef,'%m') ); //month
				$sinv->appendChild( $ele );
				$ele = $this->doc->createElement( 'InvoiceDate', dol_print_date($obj->datef,'%Y-%m-%d') ); //AAAA-MM-DD
				$sinv->appendChild( $ele );
				$ele = $this->doc->createElement( 'InvoiceType',$this->invoice_type($obj->type) ); // FT FS FR ND NC
				$sinv->appendChild( $ele );

				$sinvsp = $this->doc->createElement( 'SpecialRegimes' );
				$ele = $this->doc->createElement( 'SelfBillingIndicator','0' );
				$sinvsp->appendChild( $ele );
				$ele = $this->doc->createElement( 'CashVATSchemeIndicator','0' );
				$sinvsp->appendChild( $ele );
				$ele = $this->doc->createElement( 'ThirdPartiesBillingIndicator','0' );
				$sinvsp->appendChild( $ele );
				$sinv->appendChild( $sinvsp ); //end SpecialRegimes

				$ele = $this->doc->createElement( 'SourceID',$this->invoice_user($obj->fk_user_valid) ); //user
				$sinv->appendChild( $ele );
				//$ele = $this->doc->createElement( 'EACCode','70450' ); //cae no required
				//$sinv->appendChild( $ele );
				$ele = $this->doc->createElement( 'SystemEntryDate',dol_print_date($obj->datec,'%Y-%m-%dT%H:%M:%S') ); //AAAA-MM-DDThh:mm:ss
				$sinv->appendChild( $ele );
				$ele = $this->doc->createElement( 'CustomerID',$this->invoice_cust($obj->fk_soc) ); //customer code
				$sinv->appendChild( $ele );

				// write invoice lines
				$sqll = "SELECT f.rowid, f.fk_product, f.qty, f.tva_tx, f.total_ht, f.total_tva, f.subprice, p.ref, p.label ";
				$sqll.= "FROM ".MAIN_DB_PREFIX."facturedet as f, ".MAIN_DB_PREFIX."product as p WHERE f.fk_product = p.rowid AND f.fk_facture = ".$obj->rowid." ";
				$resultl = $this->db->query($sqll);
				if ($resultl)
				{
					$il=0;
					$numl = $this->db->num_rows($resultl);
					while ($il < $numl)
					{
						$objl = $this->db->fetch_object($resultl);

						$sinvln = $this->doc->createElement( 'Line' );
						$ele = $this->doc->createElement( 'LineNumber',($il+1) ); //line number (counter)
						$sinvln->appendChild( $ele );
						$ele = $this->doc->createElement( 'ProductCode',$objl->ref ); //product table
						$sinvln->appendChild( $ele );
						$ele = $this->doc->createElement( 'ProductDescription',$objl->label );
						$sinvln->appendChild( $ele );
						$ele = $this->doc->createElement( 'Quantity',$objl->qty );
						$sinvln->appendChild( $ele );
						$ele = $this->doc->createElement( 'UnitOfMeasure','UNI' ); //this field not exists in product table
						$sinvln->appendChild( $ele );
						//total line with discounts / quantity (no rounding)
						$priceu=($objl->total_ht<0?$objl->total_ht*(-1):$objl->total_ht)/$objl->qty;
						$ele = $this->doc->createElement( 'UnitPrice',$priceu ); //price less discounts (without rounding)
						$sinvln->appendChild( $ele );
						$ele = $this->doc->createElement( 'TaxPointDate',dol_print_date($obj->datef,'%Y-%m-%d') ); //shipping date = invoice date
						$sinvln->appendChild( $ele );
						//reference of invoice in credit note
						if ($obj->type==2 && $obj->fk_facture_source) { //if credit note
							$refdoc = $this->doc->createElement( 'References' );
							$ele = $this->doc->createElement( 'Reference',$this->invoice_ref($obj->fk_facture_source) ); //invoice ref in credit note
							$refdoc->appendChild( $ele );
							$sinvln->appendChild( $refdoc );
						}
						$ele = $this->doc->createElement( 'Description',$objl->label ); //line description
						$sinvln->appendChild( $ele );
						if ($obj->type==2) { //if credit note
							$ele = $this->doc->createElement( 'DebitAmount',round(($objl->total_ht*(-1)),2) ); //line value without tax discount
							$sinvln->appendChild( $ele );
						} else {
							$ele = $this->doc->createElement( 'CreditAmount',round($objl->total_ht,2) ); //line value without tax
							$sinvln->appendChild( $ele );
						}
						$sinvtax = $this->doc->createElement( 'Tax' );
						$ele = $this->doc->createElement( 'TaxType','IVA' );
						$sinvtax->appendChild( $ele );
						$ele = $this->doc->createElement( 'TaxCountryRegion','PT' ); //standards ISO 3166-1 alpha 1
						$sinvtax->appendChild( $ele );
						$ele = $this->doc->createElement( 'TaxCode',$this->tax_type($objl->tva_tx) ); //RED INT NOR ISE OUT
						$sinvtax->appendChild( $ele );
						$ele = $this->doc->createElement( 'TaxPercentage',$objl->tva_tx ); //percentage tax
						$sinvtax->appendChild( $ele );
						if ($objl->tva_tx==0) {
							$ele = $this->doc->createElement( 'TaxExemptionReason',$this->taxexemption ); //VAT exemption code
							$sinvtax->appendChild( $ele );
						}
						$sinvln->appendChild( $sinvtax ); //end Tax
						//Unit price without discounts * quantity - net total with discounts
						$descln=(($objl->subprice<0?$objl->subprice*(-1):$objl->subprice)*$objl->qty)-($objl->total_ht<0?$objl->total_ht*(-1):$objl->total_ht);
						$ele = $this->doc->createElement( 'SettlementAmount', $descln ); //valor desc. da linha+cab da linha
						$sinvln->appendChild( $ele );

						$sinv->appendChild( $sinvln ); //end Line
						$il++;
					}
				}
				else {
					dol_print_error($this->db);
					return -1;
				}
				//totais do documento
				$sinvtot = $this->doc->createElement( 'DocumentTotals' );
				$ele = $this->doc->createElement( 'TaxPayable',round(($obj->tva<0?$obj->tva*(-1):$obj->tva),2) ); //vat amount
				$sinvtot->appendChild( $ele );
				$ele = $this->doc->createElement( 'NetTotal',round(($obj->total<0?$obj->total*(-1):$obj->total),2) ); //net amount
				$sinvtot->appendChild( $ele );
				$ele = $this->doc->createElement( 'GrossTotal',round(($obj->total_ttc<0?$obj->total_ttc*(-1):$obj->total_ttc),2) ); //total amount with tax
				$sinvtot->appendChild( $ele );
				$sinv->appendChild( $sinvtot ); //end DocumentTotals

				$sinvs->appendChild( $sinv ); //end invoice
				$i++;
			}
		}
		else {
			dol_print_error($this->db);
			return -1;
		}

		$sdocs->appendChild( $sinvs );
		$this->audit->appendChild( $sdocs );

		return 1;
    }




}

?>
