<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       cabinetmed/class/cabinetmedexambio.class.php
 *      \ingroup    cabinetmed
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Class to manage bio exam
 */
class CabinetmedExamBio // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='cabinetmed_exambio';			//!< Id that identify managed objects
	//var $table_element='cabinetmed_exambio';	//!< Name of table without prefix where object is stored

    var $id;

	var $fk_soc;
	var $fk_user;
	var $dateexam='';
	var $resultat;
	var $conclusion;
	var $comment;
	var $suivipr_ad;
	var $suivipr_ag;
	var $suivipr_vs;
	var $suivipr_eva;
	var $suivipr_err;
	var $suivisa_fat;
	var $suivisa_dax;
	var $suivisa_dpe;
	var $suivisa_dpa;
	var $suivisa_rno;
	var $suivisa_dma;
	var $suivisa_basdai;
	var $tms='';


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
     *      @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
     *      @return int         			<0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->resultat)) $this->resultat=trim($this->resultat);
		if (isset($this->conclusion)) $this->conclusion=trim($this->conclusion);
		if (isset($this->comment)) $this->comment=trim($this->comment);
		if (isset($this->suivipr_ad)) $this->suivipr_ad=trim($this->suivipr_ad);
		if (isset($this->suivipr_ag)) $this->suivipr_ag=trim($this->suivipr_ag);
		if (isset($this->suivipr_vs)) $this->suivipr_vs=trim($this->suivipr_vs);
		if (isset($this->suivipr_eva)) $this->suivipr_eva=trim($this->suivipr_eva);
		if (isset($this->suivipr_err)) $this->suivipr_err=trim($this->suivipr_err);
        if (isset($this->suivipr_das28)) $this->suivipr_das28=trim($this->suivipr_das28);
        if (isset($this->suivisa_fat)) $this->suivisa_fat=trim($this->suivisa_fat);
		if (isset($this->suivisa_dax)) $this->suivisa_dax=trim($this->suivisa_dax);
		if (isset($this->suivisa_dpe)) $this->suivisa_dpe=trim($this->suivisa_dpe);
		if (isset($this->suivisa_dpa)) $this->suivisa_dpa=trim($this->suivisa_dpa);
		if (isset($this->suivisa_rno)) $this->suivisa_rno=trim($this->suivisa_rno);
		if (isset($this->suivisa_dma)) $this->suivisa_dma=trim($this->suivisa_dma);
		if (isset($this->suivisa_basdai)) $this->suivisa_basdai=trim($this->suivisa_basdai);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."cabinetmed_exambio(";
		$sql.= "fk_soc,";
		$sql.= "fk_user,";
		$sql.= "dateexam,";
		$sql.= "resultat,";
		$sql.= "conclusion,";
		$sql.= "comment,";
		$sql.= "suivipr_ad,";
		$sql.= "suivipr_ag,";
		$sql.= "suivipr_vs,";
		$sql.= "suivipr_eva,";
        $sql.= "suivipr_das28,";
		$sql.= "suivipr_err,";
        $sql.= "suivisa_fat,";
		$sql.= "suivisa_dax,";
		$sql.= "suivisa_dpe,";
		$sql.= "suivisa_dpa,";
		$sql.= "suivisa_rno,";
		$sql.= "suivisa_dma,";
		$sql.= "suivisa_basdai";
        $sql.= ") VALUES (";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".$user->id.",";
		$sql.= " ".(! isset($this->dateexam) || dol_strlen($this->dateexam)==0?'NULL':"'".$this->db->idate($this->dateexam))."',";
		$sql.= " ".(! isset($this->resultat)?'NULL':"'".$this->db->escape($this->resultat)."'").",";
		$sql.= " ".(! isset($this->conclusion)?'NULL':"'".$this->db->escape($this->conclusion)."'").",";
		$sql.= " ".(! isset($this->comment)?'NULL':"'".$this->db->escape($this->comment)."'").",";
		$sql.= " ".(! isset($this->suivipr_ad) || $this->suivipr_ad==''?'NULL':"'".$this->suivipr_ad."'").",";
		$sql.= " ".(! isset($this->suivipr_ag) || $this->suivipr_ag==''?'NULL':"'".$this->suivipr_ag."'").",";
		$sql.= " ".(! isset($this->suivipr_vs) || $this->suivipr_vs==''?'NULL':"'".$this->suivipr_vs."'").",";
		$sql.= " ".(! isset($this->suivipr_eva) || $this->suivipr_eva==''?'NULL':"'".$this->suivipr_eva."'").",";
        $sql.= " ".(empty($this->suivipr_das28)?'NULL':"'".$this->suivipr_das28."'").",";
		$sql.= " ".(! isset($this->suivipr_err) || $this->suivipr_err==''?'NULL':"'".$this->suivipr_err."'").",";
        $sql.= " ".(! isset($this->suivisa_fat) || $this->suivisa_fat==''?'NULL':"'".$this->suivisa_fat."'").",";
		$sql.= " ".(! isset($this->suivisa_dax) || $this->suivisa_dax==''?'NULL':"'".$this->suivisa_dax."'").",";
		$sql.= " ".(! isset($this->suivisa_dpe) || $this->suivisa_dpe==''?'NULL':"'".$this->suivisa_dpe."'").",";
		$sql.= " ".(! isset($this->suivisa_dpa) || $this->suivisa_dpa==''?'NULL':"'".$this->suivisa_dpa."'").",";
		$sql.= " ".(! isset($this->suivisa_rno) || $this->suivisa_rno==''?'NULL':"'".$this->suivisa_rno."'").",";
		$sql.= " ".(! isset($this->suivisa_dma) || $this->suivisa_dma==''?'NULL':"'".$this->suivisa_dma."'").",";
		$sql.= " ".(empty($this->suivisa_basdai)?'NULL':"'".$this->suivisa_basdai."'");
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."cabinetmed_exambio");

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.

	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
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
     *    @param	int		$id     id object
     *    @return   int        		<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_soc,";
		$sql.= " t.fk_user,";
		$sql.= " t.dateexam,";
		$sql.= " t.resultat,";
		$sql.= " t.conclusion,";
		$sql.= " t.comment,";
		$sql.= " t.suivipr_ad,";
		$sql.= " t.suivipr_ag,";
		$sql.= " t.suivipr_vs,";
		$sql.= " t.suivipr_eva,";
        $sql.= " t.suivipr_das28,";
		$sql.= " t.suivipr_err,";
		$sql.= " t.suivisa_fat,";
		$sql.= " t.suivisa_dax,";
		$sql.= " t.suivisa_dpe,";
		$sql.= " t.suivisa_dpa,";
		$sql.= " t.suivisa_rno,";
		$sql.= " t.suivisa_dma,";
		$sql.= " t.suivisa_basdai,";
		$sql.= " t.tms";


        $sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_exambio as t";
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
				$this->dateexam = $this->db->jdate($obj->dateexam);
				$this->resultat = $obj->resultat;
				$this->conclusion = $obj->conclusion;
				$this->comment = $obj->comment;
				$this->suivipr_ad = $obj->suivipr_ad;
				$this->suivipr_ag = $obj->suivipr_ag;
				$this->suivipr_vs = $obj->suivipr_vs;
				$this->suivipr_eva = $obj->suivipr_eva;
                $this->suivipr_das28 = $obj->suivipr_das28;
				$this->suivipr_err = $obj->suivipr_err;
				$this->suivisa_fat = $obj->suivisa_fat;
				$this->suivisa_dax = $obj->suivisa_dax;
				$this->suivisa_dpe = $obj->suivisa_dpe;
				$this->suivisa_dpa = $obj->suivisa_dpa;
				$this->suivisa_rno = $obj->suivisa_rno;
				$this->suivisa_dma = $obj->suivisa_dma;
				$this->suivisa_basdai = $obj->suivisa_basdai;
				$this->tms = $this->db->jdate($obj->tms);
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
     *      Update object into database
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
		if (isset($this->resultat)) $this->resultat=trim($this->resultat);
		if (isset($this->conclusion)) $this->conclusion=trim($this->conclusion);
		if (isset($this->comment)) $this->comment=trim($this->comment);
		if (isset($this->suivipr_ad)) $this->suivipr_ad=trim($this->suivipr_ad);
		if (isset($this->suivipr_ag)) $this->suivipr_ag=trim($this->suivipr_ag);
		if (isset($this->suivipr_vs)) $this->suivipr_vs=trim($this->suivipr_vs);
		if (isset($this->suivipr_eva)) $this->suivipr_eva=trim($this->suivipr_eva);
		if (isset($this->suivipr_das28)) $this->suivipr_das28=trim($this->suivipr_das28);
        if (isset($this->suivipr_err)) $this->suivipr_err=trim($this->suivipr_err);
		if (isset($this->suivisa_fat)) $this->suivisa_fat=trim($this->suivisa_fat);
		if (isset($this->suivisa_dax)) $this->suivisa_dax=trim($this->suivisa_dax);
		if (isset($this->suivisa_dpe)) $this->suivisa_dpe=trim($this->suivisa_dpe);
		if (isset($this->suivisa_dpa)) $this->suivisa_dpa=trim($this->suivisa_dpa);
		if (isset($this->suivisa_rno)) $this->suivisa_rno=trim($this->suivisa_rno);
		if (isset($this->suivisa_dma)) $this->suivisa_dma=trim($this->suivisa_dma);
		if (isset($this->suivisa_basdai)) $this->suivisa_basdai=trim($this->suivisa_basdai);


		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."cabinetmed_exambio SET";
		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " dateexam=".(dol_strlen($this->dateexam)!=0 ? "'".$this->db->idate($this->dateexam)."'" : 'null').",";
		$sql.= " resultat=".(isset($this->resultat)?"'".$this->db->escape($this->resultat)."'":"null").",";
		$sql.= " conclusion=".(isset($this->conclusion)?"'".$this->db->escape($this->conclusion)."'":"null").",";
		$sql.= " comment=".(isset($this->comment)?"'".$this->db->escape($this->comment)."'":"null").",";
		$sql.= " suivipr_ad=".(isset($this->suivipr_ad) && $this->suivipr_ad!=''?$this->suivipr_ad:"null").",";
		$sql.= " suivipr_ag=".(isset($this->suivipr_ag) && $this->suivipr_ag!=''?$this->suivipr_ag:"null").",";
		$sql.= " suivipr_vs=".(isset($this->suivipr_vs) && $this->suivipr_vs!=''?$this->suivipr_vs:"null").",";
		$sql.= " suivipr_eva=".(isset($this->suivipr_eva) && $this->suivipr_eva!=''?$this->suivipr_eva:"null").",";
        $sql.= " suivipr_das28=".(!empty($this->suivipr_das28) && $this->suivipr_das28!=''?$this->suivipr_das28:"null").",";
		$sql.= " suivipr_err=".(isset($this->suivipr_err) && $this->suivipr_err!=''?$this->suivipr_err:"null").",";
        $sql.= " suivisa_fat=".(isset($this->suivisa_fat) && $this->suivisa_fat!=''?$this->suivisa_fat:"null").",";
		$sql.= " suivisa_dax=".(isset($this->suivisa_dax) && $this->suivisa_dax!=''?$this->suivisa_dax:"null").",";
		$sql.= " suivisa_dpe=".(isset($this->suivisa_dpe) && $this->suivisa_dpe!=''?$this->suivisa_dpe:"null").",";
		$sql.= " suivisa_dpa=".(isset($this->suivisa_dpa) && $this->suivisa_dpa!=''?$this->suivisa_dpa:"null").",";
		$sql.= " suivisa_rno=".(isset($this->suivisa_rno) && $this->suivisa_rno!=''?$this->suivisa_rno:"null").",";
		$sql.= " suivisa_dma=".(isset($this->suivisa_dma) && $this->suivisa_dma!=''?$this->suivisa_dma:"null").",";
		$sql.= " suivisa_basdai=".(!empty($this->suivisa_basdai)?$this->suivisa_basdai:"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null')."";
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
     *	 @param		User	$user        	User that delete
     *   @param     int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *   @return	int						<0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."cabinetmed_exambio";
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

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
	 *		Load an object from its id and create a new one in database
	 *
	 *		@param	int		$fromid     	Id of object to clone
	 * 	 	@return	int						New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Cabinetmed_exambio($this->db);

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
	 *	Id must be 0 if object instance is a specimen.
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;

		$this->fk_soc='';
		$this->dateexam='';
		$this->resultat='';
		$this->conclusion='';
		$this->comment='';
		$this->suivipr_ad='';
		$this->suivipr_ag='';
		$this->suivipr_vs='';
		$this->suivipr_eva='';
		$this->suivipr_err='';
		$this->suivisa_fat='';
		$this->suivisa_dax='';
		$this->suivisa_dpe='';
		$this->suivisa_dpa='';
		$this->suivisa_rno='';
		$this->suivisa_dma='';
		$this->suivisa_basdai='';
		$this->tms='';


	}

}
?>
