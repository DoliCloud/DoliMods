<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *      \file       dev/skeletons/monitoring_probes.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *  Put here description of your class
 *	Initialy built by build_class_from_table on 2011-03-08 23:24
 */
class Monitoring_probes extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='monitoring_probes';		//!< Id that identify managed objects
	var $table_element='monitoring_probes';	//!< Name of table without prefix where object is stored

    var $id;

	var $title;
    var $groupname;
	var $url;
	var $url_params;
	var $useproxy;
	var $checkkey;
	var $maxvalue;
	var $frequency;
    var $active;
	var $status;
    var $lastreset;



    /**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
     */
    function Monitoring_probes($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *   Create in database
     *
     *   @param		User	$user        	User that create
     *   @param 	int		$notrigger	    0=launch triggers after, 1=disable triggers
     *   @return    int         			<0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		$now=dol_now();

		// Clean parameters
		if (isset($this->title)) $this->title=trim($this->title);
        if (isset($this->groupname)) $this->groupname=trim($this->groupname);
		if (isset($this->typeport)) $this->typeprot=trim($this->typeprot);
        if (isset($this->url)) $this->url=trim($this->url);
        if (isset($this->url_params)) $this->url_params=trim($this->url_params);
        if (isset($this->useproxy)) $this->useproxy=trim($this->useproxy);
		if (isset($this->checkkey)) $this->checkkey=trim($this->checkkey);
		if (isset($this->frequency)) $this->frequency=trim($this->frequency);
		if (isset($this->status)) $this->status=trim($this->status);

		// Check parameters
        if (empty($this->title)) { $this->error='ErrorFieldRequired'; return -1; }
		if (empty($this->url))   { $this->error='ErrorFieldRequired'; return -1; }

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."monitoring_probes(";
		$sql.= "title,";
		$sql.= "groupname,";
		$sql.= "typeprot,";
		$sql.= "url,";
		$sql.= "url_params,";
		$sql.= "useproxy,";
		$sql.= "checkkey,";
		$sql.= "maxval,";
		$sql.= "frequency,";
        $sql.= "active,";
		$sql.= "status,";
		$sql.= "lastreset";
        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->title)?'NULL':"'".$this->db->escape($this->title)."'").",";
        $sql.= " ".(! isset($this->groupname)?'NULL':"'".$this->db->escape($this->groupname)."'").",";
		$sql.= " ".(! isset($this->typeprot)?"'GET'":"'".$this->db->escape($this->typeprot)."'").",";
        $sql.= " ".(! isset($this->url)?'NULL':"'".$this->db->escape($this->url)."'").",";
		$sql.= " ".(! isset($this->url_params)?'NULL':"'".$this->db->escape($this->url_params)."'").",";
		$sql.= " ".(! isset($this->useproxy)?'NULL':"'".$this->db->escape($this->useproxy)."'").",";
		$sql.= " ".(! isset($this->checkkey)?'NULL':"'".$this->db->escape($this->checkkey)."'").",";
		$sql.= " ".(! isset($this->maxvalue)?'NULL':"'".$this->db->escape($this->maxvalue)."'").",";
		$sql.= " ".(! isset($this->frequency)?'NULL':"'".$this->frequency."'").",";
        $sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'").",";
		$sql.= " ".(! isset($this->status)?'NULL':"'".$this->status."'").",";
        $sql.= " '".$this->db->idate($now)."'";

		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."monitoring_probes");
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
     *  Load object in memory from database
     *
     *  @param	int		$id         id object
     *  @return int         		<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.title,";
        $sql.= " t.groupname,";
		$sql.= " t.typeprot,";
        $sql.= " t.url,";
		$sql.= " t.url_params,";
		$sql.= " t.useproxy,";
		$sql.= " t.checkkey,";
		$sql.= " t.maxval,";
		$sql.= " t.frequency,";
        $sql.= " t.active,";
		$sql.= " t.status,";
        $sql.= " t.lastreset,";
        $sql.= " t.oldesterrortext,";
        $sql.= " t.oldesterrordate";
        $sql.= " FROM ".MAIN_DB_PREFIX."monitoring_probes as t";
        $sql.= " WHERE t.rowid = ".((int) $id);

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                $this->ref   = $obj->rowid;
				$this->title = $obj->title;
                $this->groupname = $obj->groupname;
				$this->typeprot = $obj->typeprot;
                $this->url   = $obj->url;
				$this->url_params= $obj->url_params;
				$this->useproxy  = $obj->useproxy;
				$this->checkkey  = $obj->checkkey;
                $this->maxvalue  = $obj->maxval;
				$this->frequency = $obj->frequency;
				$this->active    = $obj->active;
                $this->status    = $obj->status;
                $this->lastreset = $this->db->jdate($obj->lastreset);
                $this->oldesterrortext = $obj->oldesterrortext;
                $this->oldesterrordate = $this->db->jdate($obj->oldesterrordate);
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
     *  Update database
     *
     *  @param	User	$user        	User that modify
     *  @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
     *  @return int         			<0 if KO, >0 if OK
     */
    function update($user=null, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->title)) $this->title=trim($this->title);
        if (isset($this->groupname)) $this->groupname=trim($this->groupname);
		if (isset($this->typeprot)) $this->typeprot=trim($this->typeprot);
        if (isset($this->url)) $this->url=trim($this->url);
		if (isset($this->url_params)) $this->url_params=trim($this->url_params);
		if (isset($this->useproxy)) $this->useproxy=trim($this->useproxy);
		if (isset($this->checkkey)) $this->checkkey=trim($this->checkkey);
		if (isset($this->frequency)) $this->frequency=trim($this->frequency);
        if (isset($this->maxvalue)) $this->maxvalue=trim($this->maxvalue);
		if (isset($this->active)) $this->active=trim($this->active);
		if (isset($this->status)) $this->status=trim($this->status);

		// Check parameters
        if (empty($this->title)) { $this->error=$langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv('Title')); return -1; }
        if (empty($this->url))   { $this->error=$langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv('Url')); return -1; }

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."monitoring_probes SET";
        $sql.= " title=".(isset($this->title)?"'".$this->db->escape($this->title)."'":"null").",";
        $sql.= " groupname=".(isset($this->groupname)?"'".$this->db->escape($this->groupname)."'":"null").",";
        $sql.= " typeprot=".(isset($this->typeprot)?"'".$this->db->escape($this->typeprot)."'":"'GET'").",";
        $sql.= " url=".(isset($this->url)?"'".$this->db->escape($this->url)."'":"null").",";
        $sql.= " url_params=".(isset($this->url_params)?"'".$this->db->escape($this->url_params)."'":"null").",";
        $sql.= " useproxy=".(isset($this->useproxy)?"'".$this->db->escape($this->useproxy)."'":"0").",";
        $sql.= " checkkey=".(isset($this->checkkey)?"'".$this->db->escape($this->checkkey)."'":"null").",";
        $sql.= " maxval=".(isset($this->maxvalue)?"'".$this->db->escape($this->maxvalue)."'":"null").",";
        $sql.= " frequency=".(isset($this->frequency)?$this->frequency:"null").",";
        $sql.= " active=".(isset($this->active)?$this->active:"null").",";
        $sql.= " status=".(isset($this->status)?$this->status:"null").",";
        $sql.= " lastreset=".($this->lastreset?"'".$this->db->idate($this->lastreset)."'":"null");
        $sql.= " WHERE rowid=".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

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
	 *  Delete object in database
	 *
     *	@param	User	$user        	User that delete
     *  @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."monitoring_probes";
		$sql.= " WHERE rowid=".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

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
     *  Update database when a status has changed
     *
     *  @param	int		$newstatus      New status to use. If 0, we also set value and date of first error to null.
     *  @param  date	$end_time       Date of detection
     *  @param  string	$errortext      To change also value and date of first error
     *  @return int             		<0 if KO, >0 if OK
     */
    function updateStatus($newstatus,$end_time,$errortext)
    {
        global $conf, $langs;
        $error=0;

        // Clean parameters
        if (isset($newstatus)) $newstatus=trim($newstatus);

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."monitoring_probes SET";
        $sql.= " status=".$newstatus.",";
        $sql.= " lastreset='".$this->db->idate($end_time)."'";
        if ($newstatus==0)
        {
            $sql.= ", oldesterrortext=null,";
            $sql.= " oldesterrordate=null";
        }
        else if ($errortext)
        {
            $sql.= ", oldesterrortext='".$this->db->escape($errortext)."',";
            $sql.= " oldesterrordate='".$this->db->idate($end_time)."'";
        }
        $sql.= " WHERE rowid=".((int) $this->id);

        $this->db->begin();

        dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

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
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param		int		$fromid     Id of object to clone
	 * 	@return		int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Monitoring_probes($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->status=0;

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
     *  Return label of object status
     *
     *  @param      int		$mode		0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=short label + picto
     *  @return     string   		   	Label
     */
    function getLibStatut($mode=0)
    {
        return $this->LibStatut($this->status,$mode,$this->active);
    }

    /**
     *  Renvoi le libelle d'un statut donne
     *
     *  @param		int		$status         Id statut
     *  @param      int		$mode           0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *  @param      int		$active         Active or not
     *  @return     string          		Libelle du statut
     */
    function LibStatut($status,$mode=0,$active=1)
    {
        global $langs;
        $langs->load('bills');

        //print "$paye,$status,$mode,$alreadypaid,$type";
        if ($mode == 0)
        {
           if ($status == 0) return $langs->trans('Unknown');
           if ($status == -1) return $langs->trans('Error');
           if ($status == 1) return $langs->trans('Success');
        }
        if ($mode == 1)
        {
           if ($status == 0) return $langs->trans('Unknown');
           if ($status == -1) return $langs->trans('Error');
           if ($status == 1) return $langs->trans('Success');
        }
        if ($mode == 2)
        {
           if ($status == 0) return img_picto($langs->trans('Unknown'),'statut0').' '.$langs->trans('Unknown');
           if ($status == -1) return img_picto($langs->trans('Error'),'statut8').' '.$langs->trans('Error');
           if ($status == 1) return img_picto($langs->trans('Success'),'statut4').' '.$langs->trans('Success');
        }
        if ($mode == 3)
        {
           if ($status == 0) return img_picto($langs->trans('Unknown'),'statut0');
           if ($status == -1) return img_picto($langs->trans('Error'),'statut8');
           if ($status == 1) return img_picto($langs->trans('Success'),'statut4');
        }
        if ($mode == 4)
        {
           if ($status == 0) return img_picto($langs->trans('Unknown'),'statut0').' '.$langs->trans('Unknown');
           if ($status == -1) return img_picto($langs->trans('Error'),'statut8').' '.$langs->trans('Error');
           if ($status == 1) return img_picto($langs->trans('Success'),'statut4').' '.$langs->trans('Success');
        }
        if ($mode == 5)
        {
           if ($status == 0) return $langs->trans('Unknown').' '.img_picto($langs->trans('Unknown'),'statut0');
           if ($status == -1) return $langs->trans('Error').' '.img_picto($langs->trans('Error'),'statut8');
           if ($status == 1) return $langs->trans('Success').' '.img_picto($langs->trans('Success'),'statut4');
        }
    }

	/**
	 *	Initialise object with example values
	 *	id must be 0 if object instance is a specimen.
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;

		$this->title='My probe';
		$this->title='My group';
		$this->typeprot='GET';
		$this->url='http://mywebsite.com';
		$this->url_params='';
		$this->useproxy=0;
		$this->checkkey='';
		$this->frequency='5';
        $this->active='1';
        $this->status='1';
	    $this->lastreset=dol_now()-3600;
	}

}

