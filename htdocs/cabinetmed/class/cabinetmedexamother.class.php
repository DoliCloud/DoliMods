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
 *      \file       cabinetmed/class/cabinetmedexamother.class.php
 *      \ingroup    cabinetmed
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Class to manage exam other
 */
class CabinetmedExamOther // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='cabinetmed_examaut';			//!< Id that identify managed objects
	//var $table_element='cabinetmed_examaut';	//!< Name of table without prefix where object is stored

    var $id;

	var $fk_soc;
	var $fk_user;
	var $dateexam='';
	var $examprinc;
	var $examsec;
	var $concprinc;
	var $concsec;
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
		if (isset($this->examprinc)) $this->examprinc=trim($this->examprinc);
		if (isset($this->examsec)) $this->examsec=trim($this->examsec);
		if (isset($this->concprinc)) $this->concprinc=trim($this->concprinc);
		if (isset($this->concsec)) $this->concsec=trim($this->concsec);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."cabinetmed_examaut(";
		$sql.= "fk_soc,";
		$sql.= "fk_user,";
		$sql.= "dateexam,";
		$sql.= "examprinc,";
		$sql.= "examsec,";
		$sql.= "concprinc,";
		$sql.= "concsec";
        $sql.= ") VALUES (";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".$user->id.",";
		$sql.= " ".(! isset($this->dateexam) || dol_strlen($this->dateexam)==0?'NULL':"'".$this->db->idate($this->dateexam))."',";
		$sql.= " ".(! isset($this->examprinc)?'NULL':"'".$this->db->escape($this->examprinc)."'").",";
		$sql.= " ".(! isset($this->examsec)?'NULL':"'".$this->db->escape($this->examsec)."'").",";
		$sql.= " ".(! isset($this->concprinc)?'NULL':"'".$this->db->escape($this->concprinc)."'").",";
		$sql.= " ".(! isset($this->concsec)?'NULL':"'".$this->db->escape($this->concsec)."'");
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."cabinetmed_examaut");

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
     *    @param	int		$id         id object
     *    @return   int         		<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_user,";
		$sql.= " t.dateexam,";
		$sql.= " t.examprinc,";
		$sql.= " t.examsec,";
		$sql.= " t.concprinc,";
		$sql.= " t.concsec,";
		$sql.= " t.tms";
        $sql.= " FROM ".MAIN_DB_PREFIX."cabinetmed_examaut as t";
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
				$this->examprinc = $obj->examprinc;
				$this->examsec = $obj->examsec;
				$this->concprinc = $obj->concprinc;
				$this->concsec = $obj->concsec;
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
		if (isset($this->examprinc)) $this->examprinc=trim($this->examprinc);
		if (isset($this->examsec)) $this->examsec=trim($this->examsec);
		if (isset($this->concprinc)) $this->concprinc=trim($this->concprinc);
		if (isset($this->concsec)) $this->concsec=trim($this->concsec);



		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."cabinetmed_examaut SET";

		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " dateexam=".(dol_strlen($this->dateexam)!=0 ? "'".$this->db->idate($this->dateexam)."'" : 'null').",";
		$sql.= " examprinc=".(isset($this->examprinc)?"'".$this->db->escape($this->examprinc)."'":"null").",";
		$sql.= " examsec=".(isset($this->examsec)?"'".$this->db->escape($this->examsec)."'":"null").",";
		$sql.= " concprinc=".(isset($this->concprinc)?"'".$this->db->escape($this->concprinc)."'":"null").",";
		$sql.= " concsec=".(isset($this->concsec)?"'".$this->db->escape($this->concsec)."'":"null").",";
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

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."cabinetmed_examaut";
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

		$object=new Cabinetmed_examaut($this->db);

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
		$this->examprinc='';
		$this->examsec='';
		$this->concprinc='';
		$this->concsec='';
		$this->tms='';
	}

}
?>
