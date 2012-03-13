<?php
/* Copyright (C) 2002-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2003      Brian Fraval         <brian@fraval.org>
 * Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2008      Patrick Raguin       <patrick.raguin@auguria.net>
 * Copyright (C) 2010      Juanjo Menent        <jmenent@2byte.es>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/cabinetmed/class/patient.class.php
 *	\ingroup    cabinetmed
 *	\brief      File for patient class
 */
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");


/**
 *	\class 		Patient
 *	\brief 		Class to manage third parties objects (customers, suppliers, prospects...)
 */
class Patient extends Societe
{
    var $db;
    var $error;
    var $errors=array();
    var $element='societe';
    var $table_element = 'societe';
    var $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

    var $id;
    var $nom;
    var $nom_particulier;
    var $prenom;
    var $particulier;
    var $address;
    var $zip;
    var $town;
    var $adresse; // deprecated
    var $cp;      // deprecated
    var $ville;   // deprecated

    var $departement_id;
    var $state_code;
    var $departement;

    var $country_id;
    var $country_code;
    var $country;

    var $tel;
    var $fax;
    var $email;
    var $url;
    var $barcode;

    // 4 identifiants professionnels (leur utilisation depend du pays)
    var $siren;		// IdProf1 - Deprecated
    var $siret;		// IdProf2 - Deprecated
    var $ape;		// IdProf3 - Deprecated
    var $idprof1;	// IdProf1
    var $idprof2;	// IdProf2
    var $idprof3;	// IdProf3
    var $idprof4;	// IdProf4

    var $prefix_comm;

    var $tva_assuj;
    var $tva_intra;

    // Local taxes
    var $localtax1_assuj;
    var $localtax2_assuj;

    var $capital;
    var $typent_id;
    var $typent_code;
    var $effectif_id;
    var $forme_juridique_code;
    var $forme_juridique;

    var $remise_percent;
    var $mode_reglement_id;
    var $cond_reglement_id;

    var $client;					// 0=no customer, 1=customer, 2=prospect
    var $prospect;					// 0=no prospect, 1=prospect
    var $fournisseur;				// =0no supplier, 1=supplier

    var $code_client;
    var $code_fournisseur;
    var $code_compta;
    var $code_compta_fournisseur;

    var $note;
    //! code statut prospect
    var $stcomm_id;
    var $statut_commercial;

    var $price_level;

    var $datec;
    var $date_update;

    var $commercial_id; //Id du commercial affecte
    var $default_lang;

    var $canvas;

    var $import_key;

    var $logo;
    var $logo_small;
    var $logo_mini;


    /**
	 *	Constructor
	 *
	 *  @param		DoliDB		$DB      Database handler
     */
    function Patient($DB)
    {
        global $conf;

        $this->db = $DB;

        $this->client = 0;
        $this->prospect = 0;
        $this->fournisseur = 0;
        $this->typent_id  = 0;
        $this->effectif_id  = 0;
        $this->forme_juridique_code  = 0;
        $this->tva_assuj = 1;

        return 1;
    }


    /**
     *    \brief      Create third party in database
     *    \param      user        Object of user that ask creation
     *    \return     int         >= 0 if OK, < 0 if KO
     */
    function create($user='')
    {
        global $langs,$conf;

        // Clean parameters
        $this->name=trim($this->name);

        dol_syslog("Societe::create ".$this->name);

        // Check parameters
        if (! empty($conf->global->SOCIETE_MAIL_REQUIRED) && ! isValidEMail($this->email))
        {
            $langs->load("errors");
            $this->error = $langs->trans("ErrorBadEMail",$this->email);
            return -1;
        }
        if (empty($this->client)) $this->client=0;
        if (empty($this->fournisseur)) $this->fournisseur=0;

        $this->db->begin();

        // For automatic creation during create action (not used by Dolibarr GUI, can be used by scripts)
        if ($this->code_client == -1)      $this->get_codeclient($this->prefix_comm,0);
        if ($this->code_fournisseur == -1) $this->get_codefournisseur($this->prefix_comm,1);

        $now=dol_now();

        // Check more parameters
        $result = $this->verify();

        if ($result >= 0)
        {
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."societe (nom, entity, datec, datea, fk_user_creat, canvas)";
            $sql.= " VALUES ('".$this->db->escape($this->name)."', ".$conf->entity.", '".$this->db->idate($now)."', '".$this->db->idate($now)."'";
            $sql.= ", ".($user->id > 0 ? "'".$user->id."'":"null");
            $sql.= ", ".($this->canvas ? "'".$this->canvas."'":"null");
            $sql.= ")";

            dol_syslog("Societe::create sql=".$sql);
            $result=$this->db->query($sql);
            if ($result)
            {
                $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."societe");

                $ret = $this->update($this->id,$user,0,1,1);

                // si un commercial cree un client il lui est affecte automatiquement
                if (!$user->rights->societe->client->voir)
                {
                    $this->add_commercial($user, $user->id);
                }
                // Ajout du commercial affecte
                else if ($this->commercial_id != '' && $this->commercial_id != -1)
                {
                    $this->add_commercial($user, $this->commercial_id);
                }

                // si le fournisseur est classe on l'ajoute
                $this->AddFournisseurInCategory($this->fournisseur_categorie);

                if ($ret >= 0)
                {
                    $this->use_webcal=($conf->global->PHPWEBCALENDAR_COMPANYCREATE=='always'?1:0);

                    // Appel des triggers
                    include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                    $interface=new Interfaces($this->db);
                    $result=$interface->run_triggers('COMPANY_CREATE',$this,$user,$langs,$conf);
                    if ($result < 0) { $error++; $this->errors=$interface->errors; }
                    // Fin appel triggers

                    dol_syslog("Societe::Create success id=".$this->id);
                    $this->db->commit();
                    return $this->id;
                }
                else
                {
                    dol_syslog("Societe::Create echec update ".$this->error);
                    $this->db->rollback();
                    return -3;
                }
            }
            else
            {
                if ($this->db->errno() == 'DB_ERROR_RECORD_ALREADY_EXISTS')
                {

                    $this->error=$langs->trans("ErrorCompanyNameAlreadyExists",$this->name);
                }
                else
                {
                    $this->error=$this->db->lasterror();
                    dol_syslog("Societe::Create echec insert sql=".$sql);
                }
                $this->db->rollback();
                return -2;
            }

        }
        else
        {
            $this->db->rollback();
            dol_syslog("Societe::Create echec verify ".join(',',$this->errors));
            return -1;
        }
    }

    /**
     *    \brief      Check properties of third party are ok (like name, third party codes, ...)
     *    \return     int		0 if OK, <0 if KO
     */
    function verify()
    {
        $this->errors=array();

        $result = 0;
        $this->name=trim($this->name);

        if (! $this->name)
        {
            $this->errors[] = 'ErrorBadThirdPartyName';
            $result = -2;
        }

        if ($this->client && $this->codeclient_modifiable())
        {
            // On ne verifie le code client que si la societe est un client / prospect et que le code est modifiable
            // Si il n'est pas modifiable il n'est pas mis a jour lors de l'update
            $rescode = $this->check_codeclient();
            if ($rescode <> 0)
            {
                if ($rescode == -1)
                {
                    $this->errors[] = 'ErrorBadCustomerCodeSyntax';
                }
                if ($rescode == -2)
                {
                    $this->errors[] = 'ErrorCustomerCodeRequired';
                }
                if ($rescode == -3)
                {
                    $this->errors[] = 'ErrorCustomerCodeAlreadyUsed';
                }
                if ($rescode == -4)
                {
                    $this->errors[] = 'ErrorPrefixRequired';
                }
                $result = -3;
            }
        }

        if ($this->fournisseur && $this->codefournisseur_modifiable())
        {
            // On ne verifie le code fournisseur que si la societe est un fournisseur et que le code est modifiable
            // Si il n'est pas modifiable il n'est pas mis a jour lors de l'update
            $rescode = $this->check_codefournisseur();
            if ($rescode <> 0)
            {
                if ($rescode == -1)
                {
                    $this->errors[] = 'ErrorBadSupplierCodeSyntax';
                }
                if ($rescode == -2)
                {
                    $this->errors[] = 'ErrorSupplierCodeRequired';
                }
                if ($rescode == -3)
                {
                    $this->errors[] = 'ErrorSupplierCodeAlreadyUsed';
                }
                if ($rescode == -5)
                {
                    $this->errors[] = 'ErrorprefixRequired';
                }
                $result = -3;
            }
        }

        return $result;
    }

    /**
     *      \brief      Update parameters of third party
     *      \param      id              			id societe
     *      \param      user            			Utilisateur qui demande la mise a jour
     *      \param      call_trigger    			0=non, 1=oui
     *		\param		allowmodcodeclient			Inclut modif code client et code compta
     *		\param		allowmodcodefournisseur		Inclut modif code fournisseur et code compta fournisseur
     *      \return     int             			<0 si ko, >=0 si ok
     */
    function update($id, $user='', $call_trigger=1, $allowmodcodeclient=0, $allowmodcodefournisseur=0)
    {
        require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");

        global $langs,$conf;

        dol_syslog("Societe::Update id=".$id." call_trigger=".$call_trigger." allowmodcodeclient=".$allowmodcodeclient." allowmodcodefournisseur=".$allowmodcodefournisseur);

        // Clean parameters
        $this->id=$id;
        $this->nom=trim($this->nom);         // deprecated
        $this->name=trim($this->name?$this->name:$this->nom);
        $this->adresse=trim($this->adresse); // deprecated
        $this->address=trim($this->address);
        $this->cp=trim($this->cp);           // deprecated
        $this->zip=trim($this->zip);
        $this->ville=trim($this->ville);     // deprecated
        $this->town=trim($this->ville);
        $this->pays_id=trim($this->pays_id); // deprecated
        $this->country_id=trim($this->country_id);
        $this->state_id=trim($this->state_id);
        $this->tel=trim($this->tel);
        $this->fax=trim($this->fax);
        $this->tel = preg_replace("/\s/","",$this->tel);
        $this->tel = preg_replace("/\./","",$this->tel);
        $this->fax = preg_replace("/\s/","",$this->fax);
        $this->fax = preg_replace("/\./","",$this->fax);
        $this->email=trim($this->email);
        $this->url=$this->url?clean_url($this->url,0):'';
        $this->idprof1=trim($this->idprof1);
        $this->idprof2=trim($this->idprof2);
        $this->idprof3=trim($this->idprof3);
        $this->idprof4=trim($this->idprof4);
        $this->prefix_comm=trim($this->prefix_comm);

        $this->tva_assuj=trim($this->tva_assuj);
        $this->tva_intra=dol_sanitizeFileName($this->tva_intra,'');

        // Local taxes
        $this->localtax1_assuj=trim($this->localtax1_assuj);
        $this->localtax2_assuj=trim($this->localtax2_assuj);

        $this->capital=price2num(trim($this->capital),'MT');
        if (empty($this->capital)) $this->capital = 0;

        $this->effectif_id=trim($this->effectif_id);
        $this->forme_juridique_code=trim($this->forme_juridique_code);

        //barcode
        $this->barcode=trim($this->barcode);

        // For automatic creation
        if ($this->code_client == -1) $this->get_codeclient($this->prefix_comm,0);
        if ($this->code_fournisseur == -1) $this->get_codefournisseur($this->prefix_comm,1);

        $this->code_compta=trim($this->code_compta);
        $this->code_compta_fournisseur=trim($this->code_compta_fournisseur);

        // Check parameters
        if (! empty($conf->global->SOCIETE_MAIL_REQUIRED) && ! isValidEMail($this->email))
        {
            $langs->load("errors");
            $this->error = $langs->trans("ErrorBadEMail",$this->email);
            return -1;
        }

        // Check name is required and codes are ok or unique.
        // If error, this->errors[] is filled
        $result = $this->verify();

        if ($result >= 0)
        {
            dol_syslog("Societe::Update verify ok");

            $sql = "UPDATE ".MAIN_DB_PREFIX."societe";
            $sql.= " SET nom = '" . addslashes($this->name) ."'"; // Champ obligatoire
            $sql.= ",datea = '".$this->db->idate(mktime())."'";
            $sql.= ",address = '" . addslashes($this->address) ."'";

            $sql.= ",cp = ".($this->cp?"'".$this->cp."'":"null");
            $sql.= ",ville = ".($this->ville?"'".addslashes($this->ville)."'":"null");

            $sql .= ",fk_departement = '" . ($this->state_id?$this->state_id:'0') ."'";
            $sql .= ",fk_pays = '" . ($this->country_id?$this->country_id:'0') ."'";

            $sql .= ",tel = ".($this->tel?"'".addslashes($this->tel)."'":"null");
            $sql .= ",fax = ".($this->fax?"'".addslashes($this->fax)."'":"null");
            $sql .= ",email = ".($this->email?"'".addslashes($this->email)."'":"null");
            $sql .= ",url = ".($this->url?"'".addslashes($this->url)."'":"null");

            $sql .= ",siren   = '". addslashes($this->idprof1) ."'";
            $sql .= ",siret   = '". addslashes($this->idprof2) ."'";
            $sql .= ",ape     = '". addslashes($this->idprof3) ."'";
            $sql .= ",idprof4 = '". addslashes($this->idprof4) ."'";

            $sql .= ",tva_assuj = ".($this->tva_assuj!=''?"'".$this->tva_assuj."'":"null");
            $sql .= ",tva_intra = '" . addslashes($this->tva_intra) ."'";

            // Local taxes
            $sql .= ",localtax1_assuj = ".($this->localtax1_assuj!=''?"'".$this->localtax1_assuj."'":"null");
            $sql .= ",localtax2_assuj = ".($this->localtax2_assuj!=''?"'".$this->localtax2_assuj."'":"null");

            $sql .= ",capital = ".$this->capital;

            $sql .= ",prefix_comm = ".($this->prefix_comm?"'".addslashes($this->prefix_comm)."'":"null");

            $sql .= ",fk_effectif = ".($this->effectif_id?"'".$this->effectif_id."'":"null");

            $sql .= ",fk_typent = ".($this->typent_id?"'".$this->typent_id."'":"0");

            $sql .= ",fk_forme_juridique = ".($this->forme_juridique_code?"'".$this->forme_juridique_code."'":"null");

            $sql .= ",client = " . ($this->client?$this->client:0);
            $sql .= ",fournisseur = " . ($this->fournisseur?$this->fournisseur:0);
            $sql .= ",barcode = ".($this->barcode?"'".$this->barcode."'":"null");
            $sql .= ",default_lang = ".($this->default_lang?"'".$this->default_lang."'":"null");


            if ($allowmodcodeclient)
            {
                //$this->check_codeclient();

                $sql .= ", code_client = ".($this->code_client?"'".addslashes($this->code_client)."'":"null");

                // Attention get_codecompta peut modifier le code suivant le module utilise
                if (empty($this->code_compta)) $this->get_codecompta('customer');

                $sql .= ", code_compta = ".($this->code_compta?"'".addslashes($this->code_compta)."'":"null");
            }

            if ($allowmodcodefournisseur)
            {
                //$this->check_codefournisseur();

                $sql .= ", code_fournisseur = ".($this->code_fournisseur?"'".addslashes($this->code_fournisseur)."'":"null");

                // Attention get_codecompta peut modifier le code suivant le module utilise
                if (empty($this->code_compta_fournisseur)) $this->get_codecompta('supplier');

                $sql .= ", code_compta_fournisseur = ".($this->code_compta_fournisseur?"'".addslashes($this->code_compta_fournisseur)."'":"null");
            }
            $sql .= ", fk_user_modif = ".($user->id > 0 ? "'".$user->id."'":"null");
            $sql .= " WHERE rowid = '" . $id ."'";


            dol_syslog("Societe::update sql=".$sql);
            $resql=$this->db->query($sql);
            if ($resql)
            {
                // Si le fournisseur est classe on l'ajoute
                $this->AddFournisseurInCategory($this->fournisseur_categorie);

                if ($call_trigger)
                {
                    // Appel des triggers
                    include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                    $interface=new Interfaces($this->db);
                    $result=$interface->run_triggers('COMPANY_MODIFY',$this,$user,$langs,$conf);
                    if ($result < 0) { $error++; $this->errors=$interface->errors; }
                    // Fin appel triggers
                }

                $result = 1;
            }
            else
            {
                if ($this->db->errno() == 'DB_ERROR_RECORD_ALREADY_EXISTS')
                {
                    // Doublon
                    $this->error = $langs->trans("ErrorDuplicateField");
                    $result =  -1;
                }
                else
                {

                    $this->error = $langs->trans("Error sql=".$sql);
                    dol_syslog("Societe::Update echec sql=".$sql);
                    $result =  -2;
                }
            }
        }

        return $result;

    }

    /**
     *    Load a third party from database into memory
     *    @param      rowid			Id of third party to load
     *    @param      ref			Reference of third party, name (Warning, this can return several records)
     *    @param      ref_ext       External reference of third party (Warning, this information is a free field not provided by Dolibarr)
     *    @param      idprof1		Prof id 1 of third party (Warning, this can return several records)
     *    @param      idprof2		Prof id 2 of third party (Warning, this can return several records)
     *    @param      idprof3		Prof id 3 of third party (Warning, this can return several records)
     *    @param      idprof4		Prof id 4 of third party (Warning, this can return several records)
     *    @return     int			>0 if OK, <0 if KO or if two records found for same ref or idprof.
     */
    function fetch($rowid, $ref='', $ref_ext='', $idprof1='',$idprof2='',$idprof3='',$idprof4='')
    {
        global $langs;
        global $conf;

        if (empty($rowid) && empty($ref) && empty($ref_ext)) return -1;

        $sql = 'SELECT s.rowid, s.nom as name, s.entity, s.ref_ext, s.address, s.datec as dc, s.prefix_comm';
        $sql .= ', s.price_level';
        $sql .= ', s.tms as date_update';
        $sql .= ', s.tel, s.fax, s.email, s.url, s.cp as zip, s.ville as town, s.note, s.client, s.fournisseur';
        $sql .= ', s.siren, s.siret, s.ape, s.idprof4';
        $sql .= ', s.capital, s.tva_intra';
        $sql .= ', s.fk_typent as typent_id';
        $sql .= ', s.fk_effectif as effectif_id';
        $sql .= ', s.fk_forme_juridique as forme_juridique_code';
        $sql .= ', s.code_client, s.code_fournisseur, s.code_compta, s.code_compta_fournisseur, s.parent, s.barcode';
        $sql .= ', s.fk_departement, s.fk_pays, s.fk_stcomm, s.remise_client, s.mode_reglement, s.cond_reglement, s.tva_assuj';
        $sql .= ', s.localtax1_assuj, s.localtax2_assuj, s.fk_prospectlevel, s.default_lang';
        $sql .= ', s.import_key';
        $sql .= ', fj.libelle as forme_juridique';
        $sql .= ', e.libelle as effectif';
        $sql .= ', p.code as pays_code, p.libelle as pays';
        $sql .= ', d.code_departement as state_code, d.nom as state';
        $sql .= ', st.libelle as stcomm';
        $sql .= ', te.code as typent_code';
        $sql .= ', sa.note_antemed, sa.note_antechirgen, sa.note_antechirortho, sa.note_anterhum, sa.note_other';
        $sql .= ', sa.note_traitclass, sa.note_traitallergie, sa.note_traitintol, sa.note_traitspec';
        $sql .= ' FROM '.MAIN_DB_PREFIX.'societe as s';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'cabinetmed_patient as sa ON sa.rowid = s.rowid';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_effectif as e ON s.fk_effectif = e.id';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_pays as p ON s.fk_pays = p.rowid';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_stcomm as st ON s.fk_stcomm = st.id';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_forme_juridique as fj ON s.fk_forme_juridique = fj.code';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON s.fk_departement = d.rowid';
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_typent as te ON s.fk_typent = te.id';
        if ($rowid) $sql .= ' WHERE s.rowid = '.$rowid;
        if ($ref)   $sql .= " WHERE s.nom = '".$this->db->escape($ref)."' AND s.entity = ".$conf->entity;
        if ($ref_ext) $sql .= " WHERE s.ref_ext = '".$this->db->escape($ref_ext)."' AND s.entity = ".$conf->entity;
        if ($idprof1) $sql .= " WHERE s.siren = '".$this->db->escape($siren)."' AND s.entity = ".$conf->entity;
        if ($idprof2) $sql .= " WHERE s.siret = '".$this->db->escape($siret)."' AND s.entity = ".$conf->entity;
        if ($idprof3) $sql .= " WHERE s.ape = '".$this->db->escape($ape)."' AND s.entity = ".$conf->entity;
        if ($idprof4) $sql .= " WHERE s.idprof4 = '".$this->db->escape($idprof4)."' AND s.entity = ".$conf->entity;
        //print $sql;

        $resql=$this->db->query($sql);
        dol_syslog("Patient::fetch ".$sql);
        if ($resql)
        {
            $num=$this->db->num_rows($resql);
            if ($num > 1)
            {
                $this->error='Patient::Fetch several records found for ref='.$ref;
                dol_syslog($this->error, LOG_ERR);
                $result = -1;
            }
            if ($num)
            {
                $obj = $this->db->fetch_object($resql);

                $this->id           = $obj->rowid;
                $this->entity       = $obj->entity;

                $this->ref          = $obj->rowid;
                $this->nom 			= $obj->name; // deprecated
                $this->name 		= $obj->name;
                $this->ref_ext      = $obj->ref_ext;

                $this->datec = $this->db->jdate($obj->datec);
                $this->date_update = $this->db->jdate($obj->date_update);

                $this->adresse 		= $obj->address; // TODO obsolete
                $this->address 		= $obj->address;
                $this->cp 			= $obj->zip;	// TODO obsolete
                $this->zip 			= $obj->zip;
                $this->ville 		= $obj->town;// TODO obsolete
                $this->town 		= $obj->town;

                $this->pays_id 		= $obj->fk_pays;						// TODO obsolete
                $this->country_id   = $obj->fk_pays;
                $this->pays_code 	= $obj->fk_pays?$obj->pays_code:'';		// TODO obsolete
                $this->country_code = $obj->fk_pays?$obj->pays_code:'';
                $this->pays 		= $obj->fk_pays?($langs->trans('Country'.$obj->pays_code)!='Country'.$obj->pays_code?$langs->trans('Country'.$obj->pays_code):$obj->pays):''; // TODO obsolete
                $this->country 		= $obj->fk_pays?($langs->trans('Country'.$obj->pays_code)!='Country'.$obj->pays_code?$langs->trans('Country'.$obj->pays_code):$obj->pays):'';

                $this->state_id     = $obj->fk_departement;
                $this->state_code   = $obj->fk_departement?$obj->state_code:'';
                $this->state        = $obj->fk_departement?$obj->state:'';

                $transcode=$langs->trans('StatusProspect'.$obj->fk_stcomm);
                $libelle=($transcode!='StatusProspect'.$obj->fk_stcomm?$transcode:$obj->stcomm);
                $this->stcomm_id = $obj->fk_stcomm;     // id statut commercial
                $this->statut_commercial = $libelle;    // libelle statut commercial

                $this->email = $obj->email;
                $this->url = $obj->url;
                $this->tel = $obj->tel; // TODO obsolete
                $this->phone = $obj->tel;
                $this->fax = $obj->fax;

                $this->parent    = $obj->parent;

                $this->idprof1		= $obj->idprof1;
                $this->idprof2		= $obj->idprof2;
                $this->idprof3		= $obj->idprof3;
                $this->idprof4		= $obj->idprof4;

                $this->capital   = $obj->capital;

                $this->code_client = $obj->code_client;
                $this->code_fournisseur = $obj->code_fournisseur;

                $this->code_compta = $obj->code_compta;
                $this->code_compta_fournisseur = $obj->code_compta_fournisseur;

                $this->barcode = $obj->barcode;

                $this->tva_assuj      = $obj->tva_assuj;
                $this->tva_intra      = $obj->tva_intra;

                // Local Taxes
                $this->localtax1_assuj      = $obj->localtax1_assuj;
                $this->localtax2_assuj      = $obj->localtax2_assuj;


                $this->typent_id      = $obj->typent_id;
                $this->typent_code    = $obj->typent_code;

                $this->effectif_id    = $obj->effectif_id;
                $this->effectif       = $obj->effectif_id?$obj->effectif:'';

                $this->forme_juridique_code= $obj->forme_juridique_code;
                $this->forme_juridique     = $obj->forme_juridique_code?$obj->forme_juridique:'';

                $this->fk_prospectlevel = $obj->fk_prospectlevel;

                $this->prefix_comm = $obj->prefix_comm;

                $this->remise_percent		= $obj->remise_client;
                $this->mode_reglement_id 	= $obj->mode_reglement;
                $this->cond_reglement_id 	= $obj->cond_reglement;
                $this->remise_client  		= $obj->remise_client;  // TODO obsolete
                $this->mode_reglement 		= $obj->mode_reglement; // TODO obsolete
                $this->cond_reglement 		= $obj->cond_reglement; // TODO obsolete

                $this->client      = $obj->client;
                $this->fournisseur = $obj->fournisseur;

                $this->note = $obj->note;
                $this->default_lang = $obj->default_lang;

                // multiprix
                $this->price_level = $obj->price_level;

                $this->import_key = $obj->import_key;

                $this->note_antemed = $obj->note_antemed;
                $this->note_antechirgen = $obj->note_antechirgen;
                $this->note_antechirortho = $obj->note_antechirortho;
                $this->note_anterhum = $obj->note_anterhum;
                $this->note_other = $obj-> note_other;

                $this->note_traitclass = $obj->note_traitclass;
                $this->note_traitallergie = $obj->note_traitallergie;
                $this->note_traitintol = $obj->note_traitintol;
                $this->note_traitspec = $obj->note_traitspec;

                $result = 1;
            }
            else
            {
                $result = 0;
            }

            $this->db->free($resql);
        }
        else
        {
            dol_syslog('Erreur Patient::Fetch '.$this->db->error(), LOG_ERR);
            $this->error=$this->db->error();
            $result = -3;
        }

        // Use first price level if level not defined for third party
        if ($conf->global->PRODUIT_MULTIPRICES && empty($this->price_level)) $this->price_level=1;

        return $result;
    }


    /**
     *    Delete a third party from database and all its dependencies (contacts, rib...)
     *    @param      id      id of third party to delete
     */
    function delete($id)
    {
        global $user,$langs,$conf;

        dol_syslog("Societe::Delete", LOG_DEBUG);
        $sqr = 0;

        // Check if third party can be deleted
        $nbpropal=0;
        $sql = "SELECT COUNT(*) as nb from ".MAIN_DB_PREFIX."propal";
        $sql.= " WHERE fk_soc = " . $id;
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $obj=$this->db->fetch_object($resql);
            $nbpropal=$obj->nb;
            if ($nbpropal > 0)
            {
                $this->error="ErrorRecordHasChildren";
                return -1;
            }
        }
        else
        {
            $this->error .= $this->db->lasterror();
            dol_syslog("Societe::Delete erreur -1 ".$this->error, LOG_ERR);
            return -1;
        }



        if ($this->db->begin())
        {
            // Added by Matelli (see http://matelli.fr/showcases/patchs-dolibarr/fix-third-party-deleting.html)
            // Removing every "categorie" link with this company
            require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php");

            $static_cat = new Categorie($this->db);
            $toute_categs = array();

            // Fill $toute_categs array with an array of (type => array of ("Categorie" instance))
            if ($this->client || $this->prospect)
            {
                $toute_categs ['societe'] = $static_cat->containing($this->id,2);
            }
            if ($this->fournisseur)
            {
                $toute_categs ['fournisseur'] = $static_cat->containing($this->id,1);
            }

            // Remove each "Categorie"
            foreach ($toute_categs as $type => $categs_type)
            {
                foreach ($categs_type as $cat)
                {
                    $cat->del_type($this, $type);
                }
            }

            // Remove contacts
            $sql = "DELETE from ".MAIN_DB_PREFIX."socpeople";
            $sql.= " WHERE fk_soc = " . $id;
            dol_syslog("Societe::Delete sql=".$sql, LOG_DEBUG);
            if ($this->db->query($sql))
            {
                $sqr++;
            }
            else
            {
                $this->error .= $this->db->lasterror();
                dol_syslog("Societe::Delete erreur -1 ".$this->error, LOG_ERR);
            }

            // Update link in member table
            $sql = "UPDATE ".MAIN_DB_PREFIX."adherent";
            $sql.= " SET fk_soc = NULL where fk_soc = " . $id;
            dol_syslog("Societe::Delete sql=".$sql, LOG_DEBUG);
            if ($this->db->query($sql))
            {
                $sqr++;
            }
            else
            {
                $this->error .= $this->db->lasterror();
                dol_syslog("Societe::Delete erreur -1 ".$this->error, LOG_ERR);
            }

            // Remove ban
            $sql = "DELETE from ".MAIN_DB_PREFIX."societe_rib";
            $sql.= " WHERE fk_soc = " . $id;
            dol_syslog("Societe::Delete sql=".$sql, LOG_DEBUG);
            if ($this->db->query($sql))
            {
                $sqr++;
            }
            else
            {
                $this->error = $this->db->lasterror();
                dol_syslog("Societe::Delete erreur -2 ".$this->error, LOG_ERR);
            }

            // Remove third party
            $sql = "DELETE from ".MAIN_DB_PREFIX."societe";
            $sql.= " WHERE rowid = " . $id;
            dol_syslog("Societe::Delete sql=".$sql, LOG_DEBUG);
            if ($this->db->query($sql))
            {
                $sqr++;
            }
            else
            {
                $this->error = $this->db->lasterror();
                dol_syslog("Societe::Delete erreur -3 ".$this->error, LOG_ERR);
            }

            if ($sqr == 4)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface=new Interfaces($this->db);
                $result=$interface->run_triggers('COMPANY_DELETE',$this,$user,$langs,$conf);
                if ($result < 0) { $error++; $this->errors=$interface->errors; }
                // Fin appel triggers

                $this->db->commit();

                // Suppression du repertoire document
                $docdir = $conf->societe->dir_output . "/" . $id;
                if (file_exists ($docdir))
                {
                    dol_delete_dir_recursive($docdir);
                }

                return 1;
            }
            else
            {
                $this->db->rollback();
                return -1;
            }
        }

    }

    /**
     *    	\brief     	Define third party as a customer
     *		\return		int		<0 if KO, >0 if OK
     */
    function set_as_client()
    {
        if ($this->id)
        {
            $newclient=1;
            if ($this->client == 2 || $this->client == 3) $newclient=3;	//If prospect, we keep prospect tag
            $sql = "UPDATE ".MAIN_DB_PREFIX."societe";
            $sql.= " SET client = ".$newclient;
            $sql.= " WHERE rowid = " . $this->id;

            $resql=$this->db->query($sql);
            if ($resql)
            {
                $this->client = $newclient;
                return 1;
            }
            else return -1;
        }
        return 0;
    }

    /**
     *    	\brief      Definit la societe comme un client
     *    	\param      remise		Valeur en % de la remise
     *    	\param      note		Note/Motif de modification de la remise
     *    	\param      user		Utilisateur qui definie la remise
     *		\return		int			<0 si ko, >0 si ok
     */
    function set_remise_client($remise, $note, $user)
    {
        global $langs;

        // Nettoyage parametres
        $note=trim($note);
        if (! $note)
        {
            $this->error=$langs->trans("ErrorFieldRequired",$langs->trans("Note"));
            return -2;
        }

        dol_syslog("Societe::set_remise_client ".$remise.", ".$note.", ".$user->id);

        if ($this->id)
        {
            $this->db->begin();

            // Positionne remise courante
            $sql = "UPDATE ".MAIN_DB_PREFIX."societe ";
            $sql.= " SET remise_client = '".$remise."'";
            $sql.= " WHERE rowid = " . $this->id .";";
            $resql=$this->db->query($sql);
            if (! $resql)
            {
                $this->db->rollback();
                $this->error=$this->db->error();
                return -1;
            }

            // Ecrit trace dans historique des remises
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."societe_remise ";
            $sql.= " (datec, fk_soc, remise_client, note, fk_user_author)";
            $sql.= " VALUES (".$this->db->idate(mktime()).", ".$this->id.", '".$remise."',";
            $sql.= " '".addslashes($note)."',";
            $sql.= " ".$user->id;
            $sql.= ")";

            $resql=$this->db->query($sql);
            if (! $resql)
            {
                $this->db->rollback();
                $this->error=$this->db->error();
                return -1;
            }

            $this->db->commit();
            return 1;
        }
    }

    /**
     *    	Add a discount for third party
     *    	@param      remise      Montant de la remise
     *    	@param      user        Utilisateur qui accorde la remise
     *    	@param      desc		Motif de l'avoir
     *      @param      tva_tx      VAT rate
     *		@return		int			<0 if KO, id or record if OK
     */
    function set_remise_except($remise, $user, $desc, $tva_tx=0)
    {
        global $langs;

        // Nettoyage des parametres
        $remise = price2num($remise);
        $desc = trim($desc);

        // Check parameters
        if (! $remise > 0)
        {
            $this->error=$langs->trans("ErrorWrongValueForParameter","1");
            return -1;
        }
        if (! $desc)
        {
            $this->error=$langs->trans("ErrorWrongValueForParameter","3");
            return -2;
        }

        if ($this->id)
        {
            require_once(DOL_DOCUMENT_ROOT.'/core/class/discount.class.php');

            $discount = new DiscountAbsolute($this->db);
            $discount->fk_soc=$this->id;
            $discount->amount_ht=price2num($remise,'MT');
            $discount->amount_tva=price2num($remise*$tva_tx/100,'MT');
            $discount->amount_ttc=price2num($discount->amount_ht+$discount->amount_tva,'MT');
            $discount->tva_tx=price2num($tva_tx,'MT');
            $discount->description=$desc;
            $result=$discount->create($user);
            if ($result > 0)
            {
                return $result;
            }
            else
            {
                $this->error=$discount->error;
                return -3;
            }
        }
        else return 0;
    }

    /**
     *    	\brief      Renvoie montant TTC des reductions/avoirs en cours disponibles de la societe
     *		\param		user		Filtre sur un user auteur des remises
     * 		\param		filter		Filtre autre
     * 		\param		maxvalue	Filter on max value for discount
     *		\return		int			<0 if KO, Credit note amount otherwise
     */
    function getAvailableDiscounts($user='',$filter='',$maxvalue=0)
    {
        require_once(DOL_DOCUMENT_ROOT.'/core/class/discount.class.php');

        $discountstatic=new DiscountAbsolute($this->db);
        $result=$discountstatic->getAvailableDiscounts($this,$user,$filter,$maxvalue);
        if ($result >= 0)
        {
            return $result;
        }
        else
        {
            $this->error=$discountstatic->error;
            return -1;
        }
    }


    /**
     * Set the price level
     *
     * @param $price_level
     * @param $user
     */
    function set_price_level($price_level, $user)
    {
        if ($this->id)
        {
            $sql  = "UPDATE ".MAIN_DB_PREFIX."societe ";
            $sql .= " SET price_level = '".$price_level."'";
            $sql .= " WHERE rowid = " . $this->id .";";

            $this->db->query($sql);

            $sql  = "INSERT INTO ".MAIN_DB_PREFIX."societe_prices ";
            $sql .= " ( datec, fk_soc, price_level, fk_user_author )";
            $sql .= " VALUES (".$this->db->idate(mktime()).",".$this->id.",'".$price_level."',".$user->id.")";

            if (! $this->db->query($sql) )
            {
                dol_print_error($this->db);
                return -1;
            }
            return 1;
        }
        return -1;
    }

    /**
     *
     *
     */
    function add_commercial($user, $commid)
    {
        if ($this->id > 0 && $commid > 0)
        {
            $sql  = "DELETE FROM  ".MAIN_DB_PREFIX."societe_commerciaux ";
            $sql .= " WHERE fk_soc = ".$this->id." AND fk_user =".$commid;

            $this->db->query($sql);

            $sql  = "INSERT INTO ".MAIN_DB_PREFIX."societe_commerciaux ";
            $sql .= " ( fk_soc, fk_user )";
            $sql .= " VALUES (".$this->id.",".$commid.")";

            if (! $this->db->query($sql) )
            {
                dol_syslog("Societe::add_commercial Erreur");
            }

        }
    }

    /**
     *
     *
     *
     */
    function del_commercial($user, $commid)
    {
        if ($this->id > 0 && $commid > 0)
        {
            $sql  = "DELETE FROM  ".MAIN_DB_PREFIX."societe_commerciaux ";
            $sql .= " WHERE fk_soc = ".$this->id." AND fk_user =".$commid;

            if (! $this->db->query($sql) )
            {
                dol_syslog("Societe::del_commercial Erreur");
            }

        }
    }


    /**
     * 	\brief		Return full address of a third party (TODO in format of its country)
     *	\return		string		Full address string
     */
    function getFullAddress()
    {
        $ret='';
        $ret.=($this->address?$this->address."\n":'');
        $ret.=trim($this->zip.' '.$this->town);
        return $ret;
    }


    /**
     *    Renvoie la liste des contacts emails existant pour la societe
     *
     *    @return     array       tableau des contacts emails
     */
    function thirdparty_and_contact_email_array()
    {
        global $langs;

        $contact_email = $this->contact_email_array();
        if ($this->email)
        {
            // TODO: Tester si email non deja present dans tableau contact
            $contact_email['thirdparty']=$langs->trans("ThirdParty").': '.dol_trunc($this->name,16)." &lt;".$this->email."&gt;";
        }
        return $contact_email;
    }

    /**
     *    Renvoie la liste des contacts emails existant pour la societe
     *
     *    @return     array       tableau des contacts emails
     */
    function contact_email_array()
    {
        $contact_email = array();

        $sql = "SELECT rowid, email, name, firstname";
        $sql.= " FROM ".MAIN_DB_PREFIX."socpeople";
        $sql.= " WHERE fk_soc = '".$this->id."'";
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $nump = $this->db->num_rows($resql);
            if ($nump)
            {
                $i = 0;
                while ($i < $nump)
                {
                    $obj = $this->db->fetch_object($resql);
                    $contact_email[$obj->rowid] = trim($obj->firstname." ".$obj->name)." &lt;".$obj->email."&gt;";
                    $i++;
                }
            }
        }
        else
        {
            dol_print_error($this->db);
        }
        return $contact_email;
    }


    /**
     *    \brief      Renvoie la liste des contacts de cette societe
     *    \return     array      tableau des contacts
     */
    function contact_array()
    {
        $contacts = array();

        $sql = "SELECT rowid, name, firstname FROM ".MAIN_DB_PREFIX."socpeople WHERE fk_soc = '".$this->id."'";
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $nump = $this->db->num_rows($resql);
            if ($nump)
            {
                $i = 0;
                while ($i < $nump)
                {
                    $obj = $this->db->fetch_object($resql);
                    $contacts[$obj->rowid] = $obj->firstname." ".$obj->name;
                    $i++;
                }
            }
        }
        else
        {
            dol_print_error($this->db);
        }
        return $contacts;
    }

    /**
     *    Return email of contact from its id
     *    @param      rowid       id of contact
     *    @return     string      email of contact
     */
    function contact_get_email($rowid)
    {
        $sql = "SELECT rowid, email, name, firstname FROM ".MAIN_DB_PREFIX."socpeople WHERE rowid = '".$rowid."'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            $nump = $this->db->num_rows($resql);

            if ($nump)
            {

                $obj = $this->db->fetch_object($resql);

                $contact_email = "$obj->firstname $obj->name <$obj->email>";

            }
            return $contact_email;
        }
        else
        {
            dol_print_error($this->db);
        }

    }


    /**
     *    \brief      Affiche le rib
     */
    function display_rib()
    {
        global $langs;

        require_once DOL_DOCUMENT_ROOT . "/societe/class/companybankaccount.class.php";

        $bac = new CompanyBankAccount($this->db);
        $bac->fetch(0,$this->id);

        if ($bac->code_banque || $bac->code_guichet || $bac->number || $bac->cle_rib)
        {
            $rib = $bac->code_banque." ".$bac->code_guichet." ".$bac->number;
            $rib.=($bac->cle_rib?" (".$bac->cle_rib.")":"");
        }
        else
        {
            $rib=$langs->trans("NoRIB");
        }
        return $rib;
    }

    /**
     * Load this->bank_account attribut
     */
    function load_ban()
    {
        require_once DOL_DOCUMENT_ROOT . "/societe/class/companybankaccount.class.php";

        $bac = new CompanyBankAccount($this->db);
        $bac->fetch(0,$this->id);

        $this->bank_account = $bac;
        return 1;
    }


    function verif_rib()
    {
        $this->load_ban();
        return $this->bank_account->verif();
    }

    /**
     *    \brief      Defini la societe mere pour les filiales
     *    \param      id      id compagnie mere a positionner
     *    \return     int     <0 si ko, >0 si ok
     */
    function set_parent($id)
    {
        if ($this->id)
        {
            $sql  = "UPDATE ".MAIN_DB_PREFIX."societe ";
            $sql .= " SET parent = ".$id;
            $sql .= " WHERE rowid = " . $this->id .";";

            if ( $this->db->query($sql) )
            {
                return 1;
            }
            else
            {
                return -1;
            }
        }
    }

    /**
     *    \brief      Supprime la societe mere
     *    \param      id      id compagnie mere a effacer
     *    \return     int     <0 si ko, >0 si ok
     */
    function remove_parent($id)
    {
        if ($this->id)
        {
            $sql  = "UPDATE ".MAIN_DB_PREFIX."societe ";
            $sql .= " SET parent = null";
            $sql .= " WHERE rowid = " . $this->id .";";

            if ( $this->db->query($sql) )
            {
                return 1;
            }
            else
            {
                return -1;
            }
        }
    }

    /**
     *    Verifie la validite d'un identifiant professionnel en fonction du pays de la societe (siren, siret, ...)
     *    @param      idprof          1,2,3,4 (Exemple: 1=siren,2=siret,3=naf,4=rcs/rm)
     *    @param      soc             Objet societe
     *    @return     int             <0 si ko, >0 si ok
     *    TODO not in business class
     */
    function id_prof_check($idprof,$soc)
    {
        $ok=1;


        return $ok;
    }

    /**
     *   Renvoi url de verification d'un identifiant professionnal
     *   @param      idprof          1,2,3,4 (Exemple: 1=siren,2=siret,3=naf,4=rcs/rm)
     *   @param      soc             Objet societe
     *   @return     string          url ou chaine vide si aucune url connue
     *   TODO not in business class
     */
    function id_prof_url($idprof,$soc)
    {
        global $langs;

        return '';
    }

    /**
     *      \brief      Indique si la societe a des projets
     *      \return     bool	   true si la societe a des projets, false sinon
     */
    function has_projects()
    {
        $sql = 'SELECT COUNT(*) as numproj FROM '.MAIN_DB_PREFIX.'projet WHERE fk_soc = ' . $this->id;
        $resql = $this->db->query($sql);
        if ($resql)
        {
            $nump = $this->db->num_rows($resql);
            $obj = $this->db->fetch_object($resql);
            $count = $obj->numproj;
        }
        else
        {
            $count = 0;
            print $this->db->error();
        }
        $this->db->free($resql);
        return ($count > 0);
    }


    /**
     *  Charge les informations d'ordre info dans l'objet societe
     *
     *  @param	int		$id     id de la societe a charger
     *  @return	void
     */
    function info($id)
    {
        $sql = "SELECT s.rowid, s.nom as name, s.datec, s.datea,";
        $sql.= " fk_user_creat, fk_user_modif";
        $sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
        $sql.= " WHERE s.rowid = ".$id;

        $result=$this->db->query($sql);
        if ($result)
        {
            if ($this->db->num_rows($result))
            {
                $obj = $this->db->fetch_object($result);

                $this->id = $obj->rowid;

                if ($obj->fk_user_creat) {
                    $cuser = new User($this->db);
                    $cuser->fetch($obj->fk_user_creat);
                    $this->user_creation     = $cuser;
                }

                if ($obj->fk_user_modif) {
                    $muser = new User($this->db);
                    $muser->fetch($obj->fk_user_modif);
                    $this->user_modification = $muser;
                }
                $this->ref			     = $obj->name;
                $this->date_creation     = $this->db->jdate($obj->datec);
                $this->date_modification = $this->db->jdate($obj->datea);
            }

            $this->db->free($result);

        }
        else
        {
            dol_print_error($this->db);
        }
    }

    /**
     *       Return if third party is a company (Business) or an end user (Consumer)
     *       @return    boolean     true=is a company, false=a and user
     */
    function isACompany()
    {
        global $conf;

        // Define if third party is treated as company of not when nature is unknown
        $isacompany=empty($conf->global->MAIN_UNKNOWN_CUSTOMERS_ARE_COMPANIES)?0:1; // 0 by default
        if (! empty($this->tva_intra)) $isacompany=1;
        else if (! empty($this->typent_code) && in_array($this->typent_code,array('TE_PRIVATE'))) $isacompany=0;
        else if (! empty($this->typent_code) && in_array($this->typent_code,array('TE_SMALL','TE_MEDIUM','TE_LARGE'))) $isacompany=1;

        return $isacompany;
    }


    /**
     *       Return if a country is inside the EEC (European Economic Community)
     *       @param     boolean		true = pays inside EEC, false= pays outside EEC
     */
    function isInEEC()
    {
        // List of all country codes that are in europe for european vat rules
        // List found on http://ec.europa.eu/taxation_customs/vies/lang.do?fromWhichPage=vieshome
        $country_code_in_EEC=array(
			'AT',	// Austria
			'BE',	// Belgium
			'BG',	// Bulgaria
			'CY',	// Cyprus
			'CZ',	// Czech republic
			'DE',	// Germany
			'DK',	// Danemark
			'EE',	// Estonia
			'ES',	// Spain
			'FI',	// Finland
			'FR',	// France
			'GB',	// Royaume-uni
			'GR',	// Greece
			'NL',	// Holland
			'HU',	// Hungary
			'IE',	// Ireland
			'IT',	// Italy
			'LT',	// Lithuania
			'LU',	// Luxembourg
			'LV',	// Latvia
			'MC',	// Monaco 		Seems to use same IntraVAT than France (http://www.gouv.mc/devwww/wwwnew.nsf/c3241c4782f528bdc1256d52004f970b/9e370807042516a5c1256f81003f5bb3!OpenDocument)
			'MT',	// Malta
        	//'NO',	// Norway
			'PL',	// Poland
			'PT',	// Portugal
			'RO',	// Romania
			'SE',	// Sweden
			'SK',	// Slovakia
			'SI',	// Slovenia
        	//'CH',	// Switzerland - No. Swizerland in not in EEC
        );
        //print "dd".$this->pays_code;
        return in_array($this->pays_code,$country_code_in_EEC);
    }

    /**
     *  Charge la liste des categories fournisseurs
     *  @return    int      0 if success, <> 0 if error
     */
    function LoadSupplierCateg()
    {
        $this->SupplierCategories = array();
        $sql = "SELECT rowid, label";
        $sql.= " FROM ".MAIN_DB_PREFIX."categorie";
        $sql.= " WHERE type = 1";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            while ($obj = $this->db->fetch_object($resql) )
            {
                $this->SupplierCategories[$obj->rowid] = $obj->label;
            }
            return 0;
        }
        else
        {
            return -1;
        }
    }

    /**
     *  Charge la liste des categories fournisseurs
     *  @return    int      0 if success, <> 0 if error
     */
    function AddFournisseurInCategory($categorie_id)
    {
        if ($categorie_id > 0)
        {
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."categorie_fournisseur (fk_categorie, fk_societe) ";
            $sql.= " VALUES ('".$categorie_id."','".$this->id."');";

            if ($resql=$this->db->query($sql)) return 0;
        }
        else
        {
            return 0;
        }
        return -1;
    }


    /**
     * Add a line in log table to save status change.
     *
     * @param $id_status
     */
    function set_status($id_status)
    {
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."societe_log (datel, fk_soc, fk_statut, fk_user, author, label)";
        $sql.= " VALUES ('".$dateaction."', ".$socid.", ".$id_status.",";
        $sql.= "'".$user->id."',";
        $sql.= "'".addslashes($user->login)."',";
        $sql.= "'Change statut from ".$oldstcomm." to ".$stcommid."'";
        $sql.= ")";
        $result = $thi->db->query($sql);
        if ($result)
        {
            $sql = "UPDATE ".MAIN_DB_PREFIX."societe SET fk_stcomm = ".$stcommid." WHERE rowid=".$socid;
            $result = $this->db->query($sql);
        }
        else
        {
            $errmesg = $this->db->lasterror();
        }
    }


    /**
     *      Initialise an example of company with random values
     *      Used to build previews or test instances
     */
    function initAsSpecimen()
    {
        global $user,$langs,$conf,$mysoc;

        $now=dol_now();

        // Initialize parameters
        $this->id=0;
        $this->name = 'THIRDPARTY SPECIMEN '.dol_print_date($now,'dayhourlog');
        $this->specimen=1;
        $this->zip='99999';
        $this->town='MyTown';
        $this->country_id=1;
        $this->country_code='FR';

        $this->code_client='CC-'.dol_print_date($now,'dayhourlog');
        $this->code_fournisseur='SC-'.dol_print_date($now,'dayhourlog');
        $this->capital=10000;
        $this->client=1;
        $this->prospect=1;
        $this->fournisseur=1;
        $this->tva_assuj=1;
        $this->tva_intra='EU1234567';
        $this->note_public='This is a comment (public)';
        $this->note='This is a comment (private)';

        $this->idprof1='idprof1';
        $this->idprof2='idprof2';
        $this->idprof3='idprof3';
        $this->idprof4='idprof4';
    }

}

?>
