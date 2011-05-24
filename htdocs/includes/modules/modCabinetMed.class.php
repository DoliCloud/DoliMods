<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**     \defgroup   cabinetmed     Module CabinetMed
 *      \brief      Example of a module descriptor.
 *                  Such a file must be copied into htdocs/includes/module directory.
 */

/**
 *      \file       htdocs/includes/modules/modCabinetMed.class.php
 *      \ingroup    cabinetmed
 *      \brief      Description and activation file for module CabinetMed
 *      \version    $Id: modCabinetMed.class.php,v 1.30 2011/05/24 20:54:18 eldy Exp $
 */
include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**     \class      modCabinetMed
 *      \brief      Description and activation class for module CabinetMed
 */
class modCabinetMed extends DolibarrModules
{
    /**
     *   \brief      Constructor. Define names, constants, directories, boxes, permissions
     *   \param      DB      Database handler
     */
    function modCabinetMed($DB)
    {
        global $langs,$conf;

        $this->db = $DB;

        // Id for module (must be unique).
        // Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
        $this->numero = 100700;
        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'cabinetmed';

        // Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
        // It is used to group modules in module setup page
        $this->family = "other";
        // Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
        $this->name = preg_replace('/^mod/i','',get_class($this));
        // Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
        $this->description = "Module CabinetMed";
        // Possible values for version are: 'development', 'experimental', 'dolibarr' or version
        $this->version = '1.0';
        // Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        // Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
        $this->special = 3;
        // Name of image file used for this module.
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
        $this->picto='generic';

        // Data directories to create when module is enabled.
        // Example: this->dirs = array("/cabinetmed/temp");
        $this->dirs = array();
        $r=0;

        // Relative path to module style sheet if exists. Example: '/cabinetmed/mycss.css'.
        $this->style_sheet = '/cabinetmed/css/styles.css';

        // Config pages. Put here list of php page names stored in admmin directory used to setup module.
        $this->config_page_url = array();

        // Dependencies
        $this->depends = array('modSociete');       // List of modules id that must be enabled if this module is enabled
        $this->requiredby = array();    // List of modules id to disable if this one is disabled
        $this->phpmin = array(4,3);                 // Minimum version of PHP required by module
        $this->need_dolibarr_version = array(3,1,-3);   // Minimum version of Dolibarr required by module
        $this->langfiles = array('cabinetmed@cabinetmed','companies');

        // Constants
        // Example: $this->const=array(0=>array('MODULE_MY_NEW_CONST1','chaine','myvalue','This is a constant to add',1),
        //                             1=>array('MODULE_MY_NEW_CONST2','chaine','myvalue','This is another constant to add',1) );
        $this->const = array(0=>array('SOCIETE_DISABLE_PROSPECTS','chaine','1','Disable all prospects features',1,'current',1),
                             1=>array('SOCIETE_DISABLE_CONTACTS','chaine','1','Disable contacts features',1,'current',1),
                             2=>array('SOCIETE_DISABLE_CUSTOMERS','chaine','1','Disable all customers features',1,'current',1),
                             3=>array('SOCIETE_DISABLE_PROSPECTS_STATS','chaine','1','Disable all prospects features',1,'current',1),
                             4=>array('SOCIETE_DISABLE_STATE','chaine','1','Disable state features',1,'current',1),
                             5=>array('SOCIETE_DISABLE_BANKACCOUNT','chaine','1','Disable bank account information on third parties',1,'current',1),
                             6=>array('SOCIETE_DISABLE_PARENTCOMPANY','chaine','1','Disable parent company features',1,'current',1),
                             7=>array('MAIN_DISABLEPROFIDRULES','chaine','1','Disable info/check links near professional id fields',1,'current',1),
                             8=>array('MAIN_FORCELANGDIR','chaine','/cabinetmed','Language files are searched into this dir first',1,'current',1),
                             9=>array('MAIN_FORCETHEMEDIR','chaine','/cabinetmed','Skins files are searched into this dir first',1,'current',1),
//                             4=>array('MAIN_DIRECTEDITMODE','chaine','1','Notes are in edit mode directly',1,'current',1),
                            10=>array('MAIN_DISABLEVATCHECK','chaine','1','Disable link to VAT check',1,'current',1),
                            11=>array('MAIN_DISABLEDRAFTSTATUS','chaine','1','Disable draft status',1,'current',1),
                            12=>array('MAIN_MENU_STANDARD_FORCED','chaine','cabinetmed_backoffice.php','Force menu handler to this value',1,'current',1),
                            13=>array('MAIN_MENUFRONT_STANDARD_FORCED','chaine','cabinetmed_frontoffice.php','Force menu handler to this value',1,'current',1),
                            14=>array('MAIN_MENU_SMARTPHONE_FORCED','chaine','cabinetmed_backoffice.php','Force menu handler to this value',1,'current',1),
                            15=>array('MAIN_MENUFRONT_SMARTPHONE_FORCED','chaine','cabinetmed_frontoffice.php','Force menu handler to this value',1,'current',1),
                            16=>array('MAIN_SUPPORT_CONTACT_TYPE_FOR_THIRDPARTIES','chaine','1','Can add third party type of contact',1,'current',1),
                            17=>array('MAIN_APPLICATION_TITLE','chaine','DoliMed','Change software title',1,'current',1)
                            );

        // Array to add new pages in new tabs
        $this->tabs = array('thirdparty:+tabcontacts:Correspondants:@cabinetmed:/cabinetmed/contact.php?socid=__ID__',
                            'thirdparty:+tabantecedents:AntecedentsShort:@cabinetmed:/cabinetmed/antecedant.php?socid=__ID__',
                            'thirdparty:+tabtraitetallergies:TraitEtAllergies:@cabinetmed:/cabinetmed/traitetallergies.php?socid=__ID__',
                            'thirdparty:+tabconsultations:ConsultationsShort:@cabinetmed:/cabinetmed/consultations.php?socid=__ID__',
                            'thirdparty:+tabexambio:ResultExamBio:@cabinetmed:/cabinetmed/exambio.php?socid=__ID__',
                            'thirdparty:+tabexamautre:ResultExamAutre:@cabinetmed:/cabinetmed/examautre.php?socid=__ID__',
                            'thirdparty:+tabdocument:Courrier:@cabinetmed:/cabinetmed/documents.php?socid=__ID__',
                            'thirdparty:-customer');
        // where entity can be
        // 'thirdparty'       to add a tab in third party view
        // 'intervention'     to add a tab in intervention view
        // 'supplier_order'   to add a tab in supplier order view
        // 'supplier_invoice' to add a tab in supplier invoice view
        // 'invoice'          to add a tab in customer invoice view
        // 'order'            to add a tab in customer order view
        // 'product'          to add a tab in product view
        // 'propal'           to add a tab in propal view
        // 'member'           to add a tab in fundation member view

        // Dictionnaries
        $this->dictionnaries=array(
            'langs'=>'cabinetmed@cabinetmed',
            'tabname'=>array(MAIN_DB_PREFIX."cabinetmed_motifcons",
                             MAIN_DB_PREFIX."cabinetmed_diaglec",
                             MAIN_DB_PREFIX."cabinetmed_examenprescrit",
                             MAIN_DB_PREFIX."cabinetmed_c_examconclusion",
                             MAIN_DB_PREFIX."cabinetmed_c_banques"
                             ),
            'tablib'=>array("MotifConsultation",
                            "DiagnostiqueLesionnel",
                            "Examens",
                            "ExamenConclusion",
                            "BankNameList"
                             //,"ResultatExamBio","ResultatExamAutre"
                             ),
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_motifcons as f',
                            'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_diaglec as f',
                            'SELECT f.rowid as rowid, f.code, f.label, f.biorad, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_examenprescrit as f',
                            'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_c_examconclusion as f',
                            'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_c_banques as f'
                            ),
            'tabsqlsort'=>array("label ASC", "label ASC","biorad ASC, label ASC","label ASC","label ASC"),
            'tabfield'=>array("code,label","code,label","code,label,biorad","code,label","code,label"), // Nom des champs en resultat de select pour affichage du dictionnaire
            'tabfieldvalue'=>array("code,label","code,label","code,label,biorad","code,label","code,label"),  // Nom des champs d'edition pour modification d'un enregistrement
            'tabfieldinsert'=>array("code,label","code,label","code,label,biorad","code,label","code,label"),
            'tabrowid'=>array("rowid","rowid","rowid","rowid","rowid"),
            'tabcond'=>array($conf->cabinetmed->enabled,$conf->cabinetmed->enabled,$conf->cabinetmed->enabled,$conf->cabinetmed->enabled,$conf->cabinetmed->enabled)
        );

        // Boxes
        $this->boxes = array();         // List of boxes
        $r=0;

        // Add here list of php file(s) stored in includes/boxes that contains class to show a box.
        // Example:
        //$this->boxes[$r][1] = "myboxa.php";
        //$r++;
        //$this->boxes[$r][1] = "myboxb.php";
        //$r++;


        // Permissions
        $this->rights = array();        // Permission array used by this module
        $r=0;

        // Add here list of permission defined by an id, a label, a boolean and two constant strings.
        // Example:
        // $this->rights[$r][0] = 2000;                 // Permission id (must not be already used)
        // $this->rights[$r][1] = 'Permision label';    // Permission label
        // $this->rights[$r][3] = 1;                    // Permission by default for new user (0/1)
        // $this->rights[$r][4] = 'level1';             // In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
        // $this->rights[$r][5] = 'level2';             // In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
        // $r++;


        // Main menu entries
        $this->menus = array();         // List of menus to add
        $r=0;

        // Add here entries to declare new menus
        // Example to declare the Top Menu entry:
/*      $this->menu[$r]=array(  'fk_menu'=>0,           // Put 0 if this is a top menu
                                    'type'=>'top',          // This is a Top menu entry
                                    'titre'=>'CabinetMed',
                                    'mainmenu'=>'cabinetmed',
                                    'leftmenu'=>'0',        // Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
                                    'url'=>'/cabinetmed/index.php',
                                    'langs'=>'',    // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
                                    'position'=>100,
                                    'enabled'=>'1',         // Define condition to show or hide menu entry. Use '$conf->voyage->enabled' if entry must be visible if module is enabled.
                                    'perms'=>'1',           // Use 'perms'=>'$user->rights->voyage->level1->level2' if you want your menu with a permission rules
                                    'target'=>'',
                                    'user'=>2);             // 0=Menu for internal users, 1=external users, 2=both
        $r++;
*/
        // Example to declare a Left Menu entry:
        // $this->menu[$r]=array(   'fk_menu'=>'r=0',       // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
        //                          'type'=>'left',         // This is a Left menu entry
        //                          'titre'=>'Voyage left menu 1',
        //                          'mainmenu'=>'voyage',
        //                          'url'=>'/voyage/pagelevel1.php',
        //                          'langs'=>'mylangfile',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
        //                          'position'=>100,
        //                          'enabled'=>'1',         // Define condition to show or hide menu entry. Use '$conf->voyage->enabled' if entry must be visible if module is enabled.
        //                          'perms'=>'1',           // Use 'perms'=>'$user->rights->voyage->level1->level2' if you want your menu with a permission rules
        //                          'target'=>'',
        //                          'user'=>2);             // 0=Menu for internal users, 1=external users, 2=both
        // $r++;
        //
        // Example to declare another Left Menu entry:
        // $this->menu[$r]=array(   'fk_menu'=>'r=1',       // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
        //                          'type'=>'left',         // This is a Left menu entry
        //                          'titre'=>'Voyage left menu 2',
        //                          'mainmenu'=>'voyage',
        //                          'url'=>'/voyage/pagelevel2.php',
        //                          'langs'=>'mylangfile',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
        //                          'position'=>100,
        //                          'enabled'=>'1',         // Define condition to show or hide menu entry. Use '$conf->voyage->enabled' if entry must be visible if module is enabled.
        //                          'perms'=>'1',           // Use 'perms'=>'$user->rights->voyage->level1->level2' if you want your menu with a permission rules
        //                          'target'=>'',
        //                          'user'=>2);             // 0=Menu for internal users, 1=external users, 2=both
        // $r++;


        // Exports
        $r=0;

        // Export list of patient and attributes
        $r++;
        $this->export_code[$r]=$this->rights_class.'_'.$r;
        $this->export_label[$r]='ExportDataset_patient_1';
        $this->export_icon[$r]='company';
        $this->export_permission[$r]=array(array("societe","export"));
        $this->export_fields_array[$r]=array('s.rowid'=>"Id",'s.nom'=>"Name",'s.datec'=>"DateCreation",'s.tms'=>"DateLastModification",'s.code_client'=>"CustomerCode",'s.address'=>"Address",'s.cp'=>"Zip",'s.ville'=>"Town",'d.nom'=>'State','p.libelle'=>"Country",'p.code'=>"CountryCode",'s.tel'=>"Phone",'s.fax'=>"Mobile",'s.url'=>"Url",'s.email'=>"Email",'s.siret'=>"Taille",'s.siren'=>"Poids",'s.ape'=>"Date de naissance",'s.idprof4'=>"Profession",'s.tva_intra'=>"INSEE",'s.capital'=>"Tarif de base consultation",'s.note'=>"Note",'t.libelle'=>"ThirdPartyType",'ce.code'=>"Regime","cfj.libelle"=>"JuridicalStatus",
        'pa.note_antemed'=>'AntecedentsMed',
        'pa.note_antechirgen'=>'AntecedentsChirGene',
        'pa.note_antechirortho'=>'AntecedentsChirOrtho',
        'pa.note_anterhum'=>'AntecedentsRhumato',
        'pa.note_other'=>'Other',
        'pa.note_traitclass'=>'Classes',
        'pa.note_traitallergie'=>'Allergies',
        'pa.note_traitintol'=>'Intolerances',
        'pa.note_traitspec'=>'SpecPharma',
        'co.rowid'=>'IdConsult',
        'co.datecons'=>'DateConsultation',
        'co.typepriseencharge'=>'TypePriseEnCharge',
        'co.motifconsprinc'=>'MotifPrincipal',
        'co.motifconssec'=>'MotifSecondaires',
        'co.diaglesprinc'=>'DiagLesPrincipal',
        'co.diaglessec'=>'DiagLesSecondaires',
        'co.hdm'=>'HistoireDeLaMaladie',
        'co.examenclinique'=>'ExamensCliniques',
        'co.examenprescrit'=>'ExamensPrescrits',
        'co.traitementprescrit'=>'TraitementsPrescrits',
        'co.comment'=>'Comment',
        'co.typevisit'=>'TypeVisite',
        'co.infiltration'=>'Infiltration',
        'co.codageccam'=>'CCAM',
        'co.montant_cheque'=>'MontantCheque',
        'co.montant_espece'=>'MontantEspece',
        'co.montant_carte'=>'MontantCarte',
        'co.montant_tiers'=>'MontantTiers',
        'co.banque'=>'Banque'
        );
        $this->export_entities_array[$r]=array(
        'co.rowid'=>'generic:Consultation',
        'co.datecons'=>'generic:Consultation',
        'co.typepriseencharge'=>'generic:Consultation',
        'co.motifconsprinc'=>'generic:Consultation',
        'co.motifconssec'=>'generic:Consultation',
        'co.diaglesprinc'=>'generic:Consultation',
        'co.diaglessec'=>'generic:Consultation',
        'co.hdm'=>'generic:Consultation',
        'co.examenclinique'=>'generic:Consultation',
        'co.examenprescrit'=>'generic:Consultation',
        'co.traitementprescrit'=>'generic:Consultation',
        'co.comment'=>'generic:Consultation',
        'co.typevisit'=>'generic:Consultation',
        'co.infiltration'=>'generic:Consultation',
        'co.codageccam'=>'generic:Consultation',
        'co.montant_cheque'=>'generic:Consultation',
        'co.montant_espece'=>'generic:Consultation',
        'co.montant_carte'=>'generic:Consultation',
        'co.montant_tiers'=>'generic:Consultation',
        'co.banque'=>'generic:Consultation'
        );   // We define here only fields that use another picto

        $this->export_sql_start[$r]='SELECT DISTINCT ';
        $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'societe as s';
        $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'cabinetmed_patient as pa ON s.rowid = pa.rowid';
        $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'cabinetmed_cons as co ON s.rowid = co.fk_soc';
        $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_typent as t ON s.fk_typent = t.id';
        $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_pays as p ON s.fk_pays = p.rowid';
        $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_effectif as ce ON s.fk_effectif = ce.id';
        $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_forme_juridique as cfj ON s.fk_forme_juridique = cfj.code';
        $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON s.fk_departement = d.rowid';
        $this->export_sql_end[$r] .=' WHERE s.entity = '.$conf->entity;


        // Example:
        // $this->export_code[$r]=$this->rights_class.'_'.$r;
        // $this->export_label[$r]='CustomersInvoicesAndInvoiceLines';  // Translation key (used only if key ExportDataset_xxx_z not found)
        // $this->export_permission[$r]=array(array("facture","facture","export"));
        // $this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.cp'=>'Zip','s.ville'=>'Town','s.fk_pays'=>'Country','s.tel'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4','s.code_compta'=>'CustomerAccountancyCode','s.code_compta_fournisseur'=>'SupplierAccountancyCode','f.rowid'=>"InvoiceId",'f.facnumber'=>"InvoiceRef",'f.datec'=>"InvoiceDateCreation",'f.datef'=>"DateInvoice",'f.total'=>"TotalHT",'f.total_ttc'=>"TotalTTC",'f.tva'=>"TotalVAT",'f.paye'=>"InvoicePaid",'f.fk_statut'=>'InvoiceStatus','f.note'=>"InvoiceNote",'fd.rowid'=>'LineId','fd.description'=>"LineDescription",'fd.price'=>"LineUnitPrice",'fd.tva_taux'=>"LineVATRate",'fd.qty'=>"LineQty",'fd.total_ht'=>"LineTotalHT",'fd.total_tva'=>"LineTotalTVA",'fd.total_ttc'=>"LineTotalTTC",'fd.date_start'=>"DateStart",'fd.date_end'=>"DateEnd",'fd.fk_product'=>'ProductId','p.ref'=>'ProductRef');
        // $this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.cp'=>'company','s.ville'=>'company','s.fk_pays'=>'company','s.tel'=>'company','s.siren'=>'company','s.siret'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.code_compta'=>'company','s.code_compta_fournisseur'=>'company','f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'fd.rowid'=>'invoice_line','fd.description'=>"invoice_line",'fd.price'=>"invoice_line",'fd.total_ht'=>"invoice_line",'fd.total_tva'=>"invoice_line",'fd.total_ttc'=>"invoice_line",'fd.tva_taux'=>"invoice_line",'fd.qty'=>"invoice_line",'fd.date_start'=>"invoice_line",'fd.date_end'=>"invoice_line",'fd.fk_product'=>'product','p.ref'=>'product');
        // $this->export_sql_start[$r]='SELECT DISTINCT ';
        // $this->export_sql_end[$r]  =' FROM ('.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'facturedet as fd, '.MAIN_DB_PREFIX.'societe as s)';
        // $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product as p on (fd.fk_product = p.rowid)';
        // $this->export_sql_end[$r] .=' WHERE f.fk_soc = s.rowid AND f.rowid = fd.fk_facture';
        // $r++;
    }

    /**
     *      \brief      Function called when module is enabled.
     *                  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     *                  It also creates data directories.
     *      \return     int             1 if OK, 0 if KO
     */
    function init()
    {
        $sql = array();

        $result=$this->load_tables();

        return $this->_init($sql);
    }

    /**
     *      \brief      Function called when module is disabled.
     *                  Remove from database constants, boxes and permissions from Dolibarr database.
     *                  Data directories are not deleted.
     *      \return     int             1 if OK, 0 if KO
     */
    function remove()
    {
        $sql = array();

        return $this->_remove($sql);
    }


    /**
     *      \brief      Create tables, keys and data required by module
     *                  Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     *                  and create data commands must be stored in directory /voyage/sql/
     *                  This function is called by this->init.
     *      \return     int     <=0 if KO, >0 if OK
     */
    function load_tables()
    {
        return $this->_load_tables('/cabinetmed/sql/');
    }
}

?>
