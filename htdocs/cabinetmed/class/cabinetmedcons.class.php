<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *      \file       htdocs/cabinetmed/class/cabinetmedcons.class.php
 *      \ingroup    cabinetmed
 *      \brief      Class is CRUD class file for consultations (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 * Class of a consultation
 */
class CabinetmedCons extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='cabinetmed_cons';			//!< Id that identify managed objects
	var $table_element='cabinetmed_cons';	//!< Name of table without prefix where object is stored

	var $id;

	var $fk_soc;
	var $datecons='';
	var $typepriseencharge;
	var $motifconsprinc;
	var $diaglesprinc;
	var $motifconssec;
	var $diaglessec;
	var $examenclinique;
	var $examenprescrit;
	var $traitementprescrit;
	var $comment;
	var $typevisit='CS';
	var $infiltration;
	var $codageccam;
	var $montant_cheque;
	var $montant_espece;
	var $montant_carte;
	var $montant_tiers;
	var $banque;
	var $num_cheque;

	var $date_c;
	var $date_m;

	var $fk_agenda;

	var $bank;


    /**
     *	Constructor
     *
     *  @param	DoliDB	$db		Database handler
     */
	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}


	/**
	 *      Create object into database
	 *
	 *      @param	User	$user        	User that create
	 *      @param 	int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *      @return int         			<0 if KO, Id of created object if OK
	 */
	function create($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$now=dol_now();

		// Clean parameters
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->typepriseencharge)) $this->typepriseencharge=trim($this->typepriseencharge);
		if (isset($this->motifconsprinc)) $this->motifconsprinc=trim($this->motifconsprinc);
		if (isset($this->diaglesprinc)) $this->diagles=trim($this->diaglesprinc);
		if (isset($this->motifconssec)) $this->motifconssec=trim($this->motifconssec);
		if (isset($this->diaglessec)) $this->diaglessec=trim($this->diaglessec);
        if (isset($this->hdm)) $this->hdm=trim($this->hdm);
		if (isset($this->examenclinique)) $this->examenclinique=trim($this->examenclinique);
		if (isset($this->examenprescrit)) $this->examenprescrit=trim($this->examenprescrit);
		if (isset($this->traitementprescrit)) $this->traitementprescrit=trim($this->traitementprescrit);
		if (isset($this->comment)) $this->comment=trim($this->comment);
		if (isset($this->typevisit)) $this->typevisit=trim($this->typevisit);
		if (isset($this->infiltration)) $this->infiltration=trim($this->infiltration);
		if (isset($this->codageccam)) $this->codageccam=trim($this->codageccam);
		if (isset($this->montant_cheque)) $this->montant_cheque=trim($this->montant_cheque);
		if (isset($this->montant_espece)) $this->montant_espece=trim($this->montant_espece);
		if (isset($this->montant_carte)) $this->montant_carte=trim($this->montant_carte);
		if (isset($this->montant_tiers)) $this->montant_tiers=trim($this->montant_tiers);
		if (isset($this->banque)) $this->banque=trim($this->banque);


		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."cabinetmed_cons(";
		$sql.= "fk_soc,";
        $sql.= "fk_user,";
		$sql.= "datecons,";
		$sql.= "date_c,";
		$sql.= "typepriseencharge,";
		$sql.= "motifconsprinc,";
		$sql.= "diaglesprinc,";
		$sql.= "motifconssec,";
		$sql.= "diaglessec,";
		$sql.= "hdm,";
		$sql.= "examenclinique,";
		$sql.= "examenprescrit,";
		$sql.= "traitementprescrit,";
		$sql.= "comment,";
		$sql.= "typevisit,";
		$sql.= "infiltration,";
		$sql.= "codageccam,";
		$sql.= "montant_cheque,";
		$sql.= "montant_espece,";
		$sql.= "montant_carte,";
		$sql.= "montant_tiers,";
		$sql.= "banque,";
		$sql.= "fk_agenda";
		$sql.= ") VALUES (";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
        $sql.= " ".$user->id.",";
		$sql.= " ".(! isset($this->datecons) || dol_strlen($this->datecons)==0?'NULL':"'".$this->db->idate($this->datecons))."',";
		$sql.= " '".$this->db->idate($now)."',";
		$sql.= " ".(! isset($this->typepriseencharge)?'NULL':"'".addslashes($this->typepriseencharge)."'").",";
		$sql.= " ".(! isset($this->motifconsprinc)?'NULL':"'".addslashes($this->motifconsprinc)."'").",";
		$sql.= " ".(! isset($this->diaglesprinc)?'NULL':"'".addslashes($this->diaglesprinc)."'").",";
		$sql.= " ".(! isset($this->motifconssec)?'NULL':"'".addslashes($this->motifconssec)."'").",";
		$sql.= " ".(! isset($this->diaglessec)?'NULL':"'".addslashes($this->diaglessec)."'").",";
		$sql.= " ".(! isset($this->hdm)?'NULL':"'".addslashes($this->hdm)."'").",";
        $sql.= " ".(! isset($this->examenclinique)?'NULL':"'".addslashes($this->examenclinique)."'").",";
		$sql.= " ".(! isset($this->examenprescrit)?'NULL':"'".addslashes($this->examenprescrit)."'").",";
		$sql.= " ".(! isset($this->traitementprescrit)?'NULL':"'".addslashes($this->traitementprescrit)."'").",";
		$sql.= " ".(! isset($this->comment)?'NULL':"'".addslashes($this->comment)."'").",";
		$sql.= " ".(! isset($this->typevisit)?'NULL':"'".addslashes($this->typevisit)."'").",";
		$sql.= " ".(! isset($this->infiltration)?'NULL':"'".addslashes($this->infiltration)."'").",";
		$sql.= " ".(! isset($this->codageccam)?'NULL':"'".addslashes($this->codageccam)."'").",";
		$sql.= " ".(! isset($this->montant_cheque)?'NULL':"'".$this->montant_cheque."'").",";
		$sql.= " ".(! isset($this->montant_espece)?'NULL':"'".$this->montant_espece."'").",";
		$sql.= " ".(! isset($this->montant_carte)?'NULL':"'".$this->montant_carte."'").",";
		$sql.= " ".(! isset($this->montant_tiers)?'NULL':"'".$this->montant_tiers."'").",";
		$sql.= " ".(! isset($this->banque)?'NULL':"'".addslashes($this->banque)."'").",";
		$sql.= " ".(empty($this->fk_agenda)?'NULL':"'".addslashes($this->fk_agenda)."'")."";
		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."cabinetmed_cons");

			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action call a trigger.

				// Call triggers
				include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
				$interface=new Interfaces($this->db);
				$result=$interface->run_triggers('CABINETMED_OUTCOME_CREATE',$this,$user,$langs,$conf);
				if ($result < 0) { $error++; $this->errors=$interface->errors; }
				// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return $this->id;
		}
	}


	/**
	 *    Load object in memory from database
	 *
	 *    @param	int		$id         Id object
	 *    @return   int 				<0 if KO, >0 if OK
	 */
	function fetch($id)
	{
		global $langs;

		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_user,";
		$sql.= " t.datecons,";
		$sql.= " t.typepriseencharge,";
		$sql.= " t.motifconsprinc,";
		$sql.= " t.diaglesprinc,";
		$sql.= " t.motifconssec,";
		$sql.= " t.diaglessec,";
		$sql.= " t.hdm,";
		$sql.= " t.examenclinique,";
		$sql.= " t.examenprescrit,";
		$sql.= " t.traitementprescrit,";
		$sql.= " t.comment,";
		$sql.= " t.typevisit,";
		$sql.= " t.infiltration,";
		$sql.= " t.codageccam,";
		$sql.= " t.montant_cheque,";
		$sql.= " t.montant_espece,";
		$sql.= " t.montant_carte,";
		$sql.= " t.montant_tiers,";
		$sql.= " t.banque,";
		$sql.= " t.fk_agenda,";
		$sql.= " t.date_c,";
		$sql.= " t.tms as date_m,";
		$sql.= " b.num_chq";
		$sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_cons as t";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank_url as bu ON bu.url_id = t.rowid AND bu.type='consultation'";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."bank as b ON b.rowid = bu.fk_bank";
		$sql.= " WHERE t.rowid = ".$id;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->fk_soc = $obj->fk_soc;
				$this->fk_user = $obj->fk_user;
				$this->datecons = $this->db->jdate($obj->datecons);
				$this->typepriseencharge = $obj->typepriseencharge;
				$this->motifconsprinc = $obj->motifconsprinc;
				$this->diaglesprinc = $obj->diaglesprinc;
				$this->motifconssec = $obj->motifconssec;
				$this->diaglessec = $obj->diaglessec;
				$this->hdm = $obj->hdm;
				$this->examenclinique = $obj->examenclinique;
				$this->examenprescrit = $obj->examenprescrit;
				$this->traitementprescrit = $obj->traitementprescrit;
				$this->comment = $obj->comment;
				$this->typevisit = $obj->typevisit;
				$this->infiltration = $obj->infiltration;
				$this->codageccam = $obj->codageccam;
				$this->montant_cheque = $obj->montant_cheque;
				$this->montant_espece = $obj->montant_espece;
				$this->montant_carte = $obj->montant_carte;
				$this->montant_tiers = $obj->montant_tiers;
				$this->banque = $obj->banque;
				$this->fk_agenda = $obj->fk_agenda;
				$this->date_c = $this->db->jdate($obj->date_c);
				$this->date_m = $this->db->jdate($obj->date_m);
				$this->num_cheque = $obj->num_chq;
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

    /**
     *    Load bank informations of payments if exists for consult
     *
     *    @return     int         <0 if KO, >0 if OK
     */
    function fetch_bankid()
    {
        $this->bank=array();

        // Search if there is some bank lines
        $bid=0;
        $sql.= "SELECT b.rowid, b.rappro, b.fk_account, b.fk_type, b.num_chq FROM ".MAIN_DB_PREFIX."bank_url as bu, ".MAIN_DB_PREFIX."bank as b";
        $sql.= " WHERE bu.url_id = ".$this->id." AND bu.type = 'consultation'";
        $sql.= " AND bu.fk_bank = b.rowid";
        dol_syslog(get_class($this)."::fetch_bankid sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num=$this->db->num_rows($resql);
            $i=0;
            while ($i < $num)
            {
                $obj=$this->db->fetch_object($resql);
                if ($obj)
                {
                    $this->bank[$obj->fk_type]['bank_id']=$obj->rowid;
                    $this->bank[$obj->fk_type]['rappro']=$obj->rappro;
                    $this->bank[$obj->fk_type]['account_id']=$obj->fk_account;
                    if ($obj->fk_type == 'CHQ') $this->num_cheque=$obj->num_chq;
                }
                $i++;
            }
            return 1;
        }
        else
        {
            $error++;
            $this->error=$this->db->lasterror();
            return -1;
        }
    }


	/**
	 *      Update database
	 *
	 *      @param	User	$user        	User that modify
	 *      @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *      @return int         			<0 if KO, >0 if OK
	 */
	function update($user=0, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->typepriseencharge)) $this->typepriseencharge=trim($this->typepriseencharge);
		if (isset($this->motifconsprinc)) $this->motifconsprinc=trim($this->motifconsprinc);
		if (isset($this->diaglesprinc)) $this->diaglesprinc=trim($this->diaglesprinc);
		if (isset($this->motifconssec)) $this->motifconssec=trim($this->motifconssec);
		if (isset($this->diaglessec)) $this->diaglessec=trim($this->diaglessec);
		if (isset($this->hdm)) $this->hdm=trim($this->hdm);
		if (isset($this->examenclinique)) $this->examenclinique=trim($this->examenclinique);
		if (isset($this->examenprescrit)) $this->examenprescrit=trim($this->examenprescrit);
		if (isset($this->traitementprescrit)) $this->traitementprescrit=trim($this->traitementprescrit);
		if (isset($this->comment)) $this->comment=trim($this->comment);
		if (isset($this->typevisit)) $this->typevisit=trim($this->typevisit);
		if (isset($this->infiltration)) $this->infiltration=trim($this->infiltration);
		if (isset($this->codageccam)) $this->codageccam=trim($this->codageccam);
		if (isset($this->montant_cheque)) $this->montant_cheque=trim($this->montant_cheque);
		if (isset($this->montant_espece)) $this->montant_espece=trim($this->montant_espece);
		if (isset($this->montant_carte)) $this->montant_carte=trim($this->montant_carte);
		if (isset($this->montant_tiers)) $this->montant_tiers=trim($this->montant_tiers);
		if (isset($this->banque)) $this->banque=trim($this->banque);


		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."cabinetmed_cons SET";
		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " datecons=".(dol_strlen($this->datecons)!=0 ? "'".$this->db->idate($this->datecons)."'" : 'null').",";
		$sql.= " typepriseencharge=".(isset($this->typepriseencharge)?"'".addslashes($this->typepriseencharge)."'":"null").",";
		$sql.= " motifconsprinc=".(isset($this->motifconsprinc)?"'".addslashes($this->motifconsprinc)."'":"null").",";
		$sql.= " diaglesprinc=".(isset($this->diaglesprinc)?"'".addslashes($this->diaglesprinc)."'":"null").",";
		$sql.= " motifconssec=".(isset($this->motifconssec)?"'".addslashes($this->motifconssec)."'":"null").",";
		$sql.= " diaglessec=".(isset($this->diaglessec)?"'".addslashes($this->diaglessec)."'":"null").",";
		$sql.= " hdm=".(isset($this->hdm)?"'".addslashes($this->hdm)."'":"null").",";
		$sql.= " examenclinique=".(isset($this->examenclinique)?"'".addslashes($this->examenclinique)."'":"null").",";
		$sql.= " examenprescrit=".(isset($this->examenprescrit)?"'".addslashes($this->examenprescrit)."'":"null").",";
		$sql.= " traitementprescrit=".(isset($this->traitementprescrit)?"'".addslashes($this->traitementprescrit)."'":"null").",";
		$sql.= " comment=".(isset($this->comment)?"'".addslashes($this->comment)."'":"null").",";
		$sql.= " typevisit=".(isset($this->typevisit)?"'".addslashes($this->typevisit)."'":"null").",";
		$sql.= " infiltration=".(isset($this->infiltration)?"'".addslashes($this->infiltration)."'":"null").",";
		$sql.= " codageccam=".(isset($this->codageccam)?"'".addslashes($this->codageccam)."'":"null").",";
		$sql.= " montant_cheque=".(isset($this->montant_cheque)?$this->montant_cheque:"null").",";
		$sql.= " montant_espece=".(isset($this->montant_espece)?$this->montant_espece:"null").",";
		$sql.= " montant_carte=".(isset($this->montant_carte)?$this->montant_carte:"null").",";
		$sql.= " montant_tiers=".(isset($this->montant_tiers)?$this->montant_tiers:"null").",";
		$sql.= " banque=".(isset($this->banque)?"'".addslashes($this->banque)."'":"null").",";
		$sql.= " fk_agenda=".((! empty($this->fk_agenda))?"'".addslashes($this->fk_agenda)."'":"null")."";
		// date_c must not be edited by an update
		// tms is modified automatically

		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action call a trigger.

				//// Call triggers
				//include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}


	/**
	 *   Delete object in database
	 *
	 *	 @param      User	$user        	User that delete
	 *   @param      int	$notrigger	    0=launch triggers after, 1=disable triggers
	 *	 @return	 int					<0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		// Search if there is a bank line
		$bid=0;
		$sql.= "SELECT b.rowid FROM ".MAIN_DB_PREFIX."bank_url as bu, ".MAIN_DB_PREFIX."bank as b";
		$sql.= " WHERE bu.url_id = ".$this->id." AND type = 'consultation'";
		$sql.= " AND bu.fk_bank = b.rowid";
		dol_syslog($sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$obj=$this->db->fetch_object($resql);
			if ($obj)
			{
				$bid=$obj->rowid;
			}
		}
		else
		{
			$error++;
			$consult->error=$this->db->lasterror();
		}

		if (! $error)
		{
			// If bid
			if ($bid)
			{
				$bankaccountline=new AccountLine($this->db);
				$result=$bankaccountline->fetch($bid);
				$bankaccountline->delete($user);
			}
		}

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."cabinetmed_cons";
		$sql.= " WHERE rowid=".$this->id;

		dol_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action call a trigger.

				//// Call triggers
				//include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param		int		$fromid     	Id of object to clone
	 * 	@return		int						New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Cabinetmed_cons($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{



		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	id must be 0 if object instance is a specimen.
	 *
	 *	 @return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;

		$now=dol_now();

		$this->fk_soc='1';
		$this->datecons=$now;
		$this->typepriseencharge='CMU';
		$this->motifconsprinc='AAAPRINC';
		$this->diaglesprinc='AAAPRINC';
		$this->motifconssec='AAASEC';
		$this->diaglessec='AAASEC';
		$this->examenclinique='Examen clinique';
		$this->examenprescrit='Examen prescrit';
		$this->traitementprescrit='Traitement prescrit';
		$this->comment='Commentaire';
		$this->typevisit='CCAM';
		$this->infiltration='Genou';
		$this->codageccam='NZLB001';
		$this->montant_cheque='50';
		$this->montant_espece='';
		$this->montant_carte='';
		$this->montant_tiers='';
		$this->banque='Crédit agricol';
		$this->fk_agenda=0;
		$this->date_c=$now-3600*24;
		$this->date_m=$now;
	}


    /**
     *      Return a link on thirdparty (with picto)
     *
     *      @param	int		$withpicto      Inclut le picto dans le lien (0=No picto, 1=Inclut le picto dans le lien, 2=Picto seul)
     *      @param  string	$more           Add more param on url
     *      @return string          		String with URL
     */
    function getNomUrl($withpicto=0,$more='')
    {
        global $conf,$langs;

        $result='';
        $lien=$lienfin='';

        $lien = '<a href="'.dol_buildpath('/cabinetmed/consultations.php',1).'?socid='.$this->fk_soc.'&amp;id='.$this->id.'&amp;action=edit';
        if ($more) $lien.=$more;
        // Add type of canvas
        $lien.=(!empty($this->canvas)?'&amp;canvas='.$this->canvas:'').'">';
        $lienfin='</a>';

        if ($withpicto) $result.=($lien.img_object($langs->trans("ShowConsult").': '.sprintf('%08d',$this->id),'generic').$lienfin);
        if ($withpicto && $withpicto != 2) $result.=' ';
        $result.=$lien.sprintf('%08d',$this->id).$lienfin;

        return $result;
    }

}
?>
