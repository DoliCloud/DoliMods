<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *  \file       nltechno/class/dolicloudcustomer.class.php
 *  \ingroup    nltechno
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2012-06-26 21:03
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Class of DoliClou dcustomers
 */
class Dolicloudcustomer extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='dolicloudcustomers';			//!< Id that identify managed objects
	var $table_element='dolicloud_customers';	//!< Name of table without prefix where object is stored

    var $id;

	var $instance;
	var $organization;
	var $email;
	var $plan;
	var $date_registration='';
	var $date_endfreeperiod='';
	var $status;
	var $partner;
	var $total_invoiced;
	var $total_payed;
	var $tms='';
	var $hostname_web;
	var $username_web;
	var $password_web;
	var $hostname_db;
	var $database_db;
	var $port_db;
	var $username_db;
	var $password_db;
	var $lastcheck='';
	var $nbofusers;
	var $lastlogin='';
	var $lastpass='';
	var $date_lastlogin='';
	var $modulesenabled;
	var $version;

	var $firstname;
	var $lastname;
	var $address;
	var $zip;
	var $town;
	var $country_id;
	var $state_id;
	var $vat_number;
	var $phone;

	var $fileauthorizedkey;
	var $filelock;
	var $date_lastrsync='';

    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that create
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->instance)) $this->instance=trim($this->instance);
		if (isset($this->organization)) $this->organization=trim($this->organization);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->plan)) $this->plan=trim($this->plan);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->partner)) $this->partner=trim($this->partner);
		if (isset($this->total_invoiced)) $this->total_invoiced=trim($this->total_invoiced);
		if (isset($this->total_payed)) $this->total_payed=trim($this->total_payed);
		if (isset($this->hostname_web)) $this->hostname_web=trim($this->hostname_web);
		if (isset($this->username_web)) $this->username_web=trim($this->username_web);
		if (isset($this->password_web)) $this->password_web=trim($this->password_web);
		if (isset($this->hostname_db)) $this->hostname_db=trim($this->hostname_db);
		if (isset($this->database_db)) $this->database_db=trim($this->database_db);
		if (isset($this->port_db)) $this->port_db=trim($this->port_db);
		if (isset($this->username_db)) $this->username_db=trim($this->username_db);
		if (isset($this->password_db)) $this->password_db=trim($this->password_db);
		if (isset($this->nbofusers)) $this->nbofusers=trim($this->nbofusers);
		if (isset($this->modulesenabled)) $this->modulesenabled=trim($this->modulesenabled);
		if (isset($this->version)) $this->version=trim($this->version);
		if (isset($this->vat_number)) $this->vat_number=trim($this->vat_number);


		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."dolicloud_customers(";

		$sql.= "instance,";
		$sql.= "organization,";
		$sql.= "email,";
		$sql.= "plan,";
		$sql.= "date_registration,";
		$sql.= "date_endfreeperiod,";
		$sql.= "status,";
		$sql.= "partner,";
		$sql.= "total_invoiced,";
		$sql.= "total_payed,";
		$sql.= "hostname_web,";
		$sql.= "username_web,";
		$sql.= "password_web,";
		$sql.= "hostname_db,";
		$sql.= "database_db,";
		$sql.= "port_db,";
		$sql.= "username_db,";
		$sql.= "password_db,";
		$sql.= "lastcheck,";
		$sql.= "nbofusers,";
		$sql.= "lastlogin,";
		$sql.= "lastpass,";
		$sql.= "date_lastlogin,";
		$sql.= "modulesenabled,";
		$sql.= "firstname,";
		$sql.= "lastname,";
		$sql.= "address,";
		$sql.= "zip,";
		$sql.= "town,";
		$sql.= "country_id,";
		$sql.= "state_id,";
		$sql.= "vat_number,";
		$sql.= "phone,";
		$sql.= "fileauthorizedkey,";
		$sql.= "filelock,";
		$sql.= "version,";
		$sql.= "lastrsync";

        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->instance)?'NULL':"'".$this->db->escape($this->instance)."'").",";
		$sql.= " ".(! isset($this->organization)?'NULL':"'".$this->db->escape($this->organization)."'").",";
		$sql.= " ".(! isset($this->email)?'NULL':"'".$this->db->escape($this->email)."'").",";
		$sql.= " ".(! isset($this->plan)?'NULL':"'".$this->db->escape($this->plan)."'").",";
		$sql.= " ".(! isset($this->date_registration) || dol_strlen($this->date_registration)==0?'NULL':$this->db->idate($this->date_registration)).",";
		$sql.= " ".(! isset($this->date_endfreeperiod) || dol_strlen($this->date_endfreeperiod)==0?'NULL':$this->db->idate($this->date_endfreeperiod)).",";
		$sql.= " ".(! isset($this->status)?'NULL':"'".$this->status."'").",";
		$sql.= " ".(! isset($this->partner)?'NULL':"'".$this->db->escape($this->partner)."'").",";
		$sql.= " ".(! isset($this->total_invoiced)?'NULL':"'".$this->total_invoiced."'").",";
		$sql.= " ".(! isset($this->total_payed)?'NULL':"'".$this->total_payed."'").",";
		$sql.= " ".(! isset($this->hostname_web)?'NULL':"'".$this->db->escape($this->hostname_web)."'").",";
		$sql.= " ".(! isset($this->username_web)?'NULL':"'".$this->db->escape($this->username_web)."'").",";
		$sql.= " ".(! isset($this->password_web)?'NULL':"'".$this->db->escape($this->password_web)."'").",";
		$sql.= " ".(! isset($this->hostname_db)?'NULL':"'".$this->db->escape($this->hostname_db)."'").",";
		$sql.= " ".(! isset($this->database_db)?'NULL':"'".$this->db->escape($this->database_db)."'").",";
		$sql.= " ".(! isset($this->port_db)?'NULL':"'".$this->port_db."'").",";
		$sql.= " ".(! isset($this->username_db)?'NULL':"'".$this->db->escape($this->username_db)."'").",";
		$sql.= " ".(! isset($this->password_db)?'NULL':"'".$this->db->escape($this->password_db)."'").",";
		$sql.= " ".(! isset($this->lastcheck) || dol_strlen($this->lastcheck)==0?'NULL':$this->db->idate($this->lastcheck)).",";
		$sql.= " ".(! isset($this->nbofusers)?'NULL':"'".$this->nbofusers."'").",";
		$sql.= " ".(! isset($this->lastlogin) || dol_strlen($this->lastlogin)==0?'NULL':"'".$this->lastlogin."'").",";
		$sql.= " ".(! isset($this->lastpass) || dol_strlen($this->lastpass)==0?'NULL':"'".$this->lastpass."'").",";
		$sql.= " ".(! isset($this->date_lastlogin) || dol_strlen($this->date_lastlogin)==0?'NULL':$this->db->idate($this->date_lastlogin)).",";
		$sql.= " ".(! isset($this->modulesenabled)?'NULL':"'".$this->db->escape($this->modulesenabled)."'").",";

		$sql.= " ".(! isset($this->firstname)?'NULL':"'".$this->db->escape($this->firstname)."'").",";
		$sql.= " ".(! isset($this->lastname)?'NULL':"'".$this->db->escape($this->lastname)."'").",";
		$sql.= " ".(! isset($this->address)?'NULL':"'".$this->db->escape($this->address)."'").",";
		$sql.= " ".(! isset($this->zip)?'NULL':"'".$this->db->escape($this->zip)."'").",";
		$sql.= " ".(! isset($this->town)?'NULL':"'".$this->db->escape($this->town)."'").",";
		$sql.= " ".(! isset($this->country_id)?'NULL':"'".$this->db->escape($this->country_id)."'").",";
		$sql.= " ".(! isset($this->state_id)?'NULL':"'".$this->db->escape($this->state_id)."'").",";
		$sql.= " ".(! isset($this->vat_number)?'NULL':"'".$this->db->escape($this->vat_number)."'").",";
		$sql.= " ".(! isset($this->phone)?'NULL':"'".$this->db->escape($this->phone)."'").",";

		$sql.= " ".(! isset($this->fileauthorizedkey) || dol_strlen($this->fileauthorizedkey)==0?'NULL':$this->db->idate($this->fileauthorizedkey)).",";
		$sql.= " ".(! isset($this->filelock) || dol_strlen($this->filelock)==0?'NULL':$this->db->idate($this->filelock)).",";

		$sql.= " ".(! isset($this->date_lastrsync) || dol_strlen($this->date_lastrsync)==0?'NULL':$this->db->idate($this->date_lastrsync)).",";
		$sql.= " ".(! isset($this->version)?'NULL':"'".$this->db->escape($this->version)."'");

		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."dolicloud_customers");

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
     *  Load object in memory from database
     *
     *  @param	int		$id    	Id object
     *  @param	string	$ref   	Ref object
     *  @return int         	<0 if KO, >0 if OK
     */
    function fetch($id,$ref='')
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.instance,";
		$sql.= " t.organization,";
		$sql.= " t.email,";
		$sql.= " t.plan,";
		$sql.= " t.date_registration,";
		$sql.= " t.date_endfreeperiod,";
		$sql.= " t.status,";
		$sql.= " t.partner,";
		$sql.= " t.total_invoiced,";
		$sql.= " t.total_payed,";
		$sql.= " t.tms,";
		$sql.= " t.hostname_web,";
		$sql.= " t.username_web,";
		$sql.= " t.password_web,";
		$sql.= " t.hostname_db,";
		$sql.= " t.database_db,";
		$sql.= " t.port_db,";
		$sql.= " t.username_db,";
		$sql.= " t.password_db,";
		$sql.= " t.lastcheck,";
		$sql.= " t.nbofusers,";
		$sql.= " t.lastlogin,";
		$sql.= " t.lastpass,";
		$sql.= " t.date_lastlogin,";
		$sql.= " t.modulesenabled,";
		$sql.= " t.firstname,";
		$sql.= " t.lastname,";
		$sql.= " t.address,";
		$sql.= " t.zip,";
		$sql.= " t.town,";
		$sql.= " t.country_id,";
		$sql.= " t.state_id,";
		$sql.= " t.vat_number,";
		$sql.= " t.phone,";
		$sql.= " t.fileauthorizedkey,";
		$sql.= " t.filelock,";
		$sql.= " t.lastrsync,";
		$sql.= " t.version";

        $sql.= " FROM ".MAIN_DB_PREFIX."dolicloud_customers as t";
        if ($ref) $sql.= " WHERE t.instance = '".$ref."'";
        else $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;

				$this->instance = $obj->instance;
				$this->ref = $obj->instance;
				$this->organization = $obj->organization;
				$this->email = $obj->email;
				$this->plan = $obj->plan;
				$this->date_registration = $this->db->jdate($obj->date_registration);
				$this->date_endfreeperiod = $this->db->jdate($obj->date_endfreeperiod);
				$this->status = $obj->status;
				$this->partner = $obj->partner;
				$this->total_invoiced = $obj->total_invoiced;
				$this->total_payed = $obj->total_payed;
				$this->tms = $this->db->jdate($obj->tms);
				$this->hostname_web = $obj->hostname_web;
				$this->username_web = $obj->username_web;
				$this->password_web = $obj->password_web;
				$this->hostname_db = $obj->hostname_db;
				$this->database_db = $obj->database_db;
				$this->port_db = $obj->port_db;
				$this->username_db = $obj->username_db;
				$this->password_db = $obj->password_db;
				$this->lastcheck = $this->db->jdate($obj->lastcheck);
				$this->nbofusers = $obj->nbofusers;
				$this->lastlogin = $obj->lastlogin;
				$this->lastpass = $obj->lastpass;
				$this->date_lastlogin = $this->db->jdate($obj->date_lastlogin);
				$this->modulesenabled = $obj->modulesenabled;

                $this->firstname = $obj->firstname;
                $this->lastname = $obj->lastname;
                $this->address = $obj->address;
                $this->zip = $obj->zip;
                $this->town = $obj->town;
                $this->country_id = $obj->country_id;
                $this->state_id = $obj->state_id;
                $this->vat_number = $obj->vat_number;
                $this->phone = $obj->phone;

                $this->fileauthorizedkey = $this->db->jdate($obj->fileauthorizedkey);
                $this->filelock = $this->db->jdate($obj->filelock);

                $this->date_lastrsync = $this->db->jdate($obj->lastrsync);
                $this->version = $obj->version;

                include_once(DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php');
                if ($this->country_id > 0)
                {
                	$tmp=getCountry($this->country_id,'all');
                	$this->country_code=$tmp['code']; $this->country=$tmp['label'];
                }
                if ($this->state_id > 0)
                {
                	$tmp=getState($this->state_id,'all');
                	$this->state_code=$tmp['code']; $this->state=$tmp['label'];
                }
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
     *  Update object into database
     *
     *  @param	User	$user        User that modify
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->instance)) $this->instance=trim($this->instance);
		if (isset($this->organization)) $this->organization=trim($this->organization);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->plan)) $this->plan=trim($this->plan);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->partner)) $this->partner=trim($this->partner);
		if (isset($this->total_invoiced)) $this->total_invoiced=trim($this->total_invoiced);
		if (isset($this->total_payed)) $this->total_payed=trim($this->total_payed);
		if (isset($this->hostname_web)) $this->hostname_web=trim($this->hostname_web);
		if (isset($this->username_web)) $this->username_web=trim($this->username_web);
		if (isset($this->password_web)) $this->password_web=trim($this->password_web);
		if (isset($this->hostname_db)) $this->hostname_db=trim($this->hostname_db);
		if (isset($this->database_db)) $this->database_db=trim($this->database_db);
		if (isset($this->port_db)) $this->port_db=trim($this->port_db);
		if (isset($this->username_db)) $this->username_db=trim($this->username_db);
		if (isset($this->password_db)) $this->password_db=trim($this->password_db);
		if (isset($this->nbofusers)) $this->nbofusers=trim($this->nbofusers);
		if (isset($this->modulesenabled)) $this->modulesenabled=trim($this->modulesenabled);
		if (isset($this->version)) $this->version=trim($this->version);
		if (isset($this->vat_number)) $this->vat_number=trim($this->vat_number);


		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."dolicloud_customers SET";

		$sql.= " instance=".(isset($this->instance)?"'".$this->db->escape($this->instance)."'":"null").",";
		$sql.= " organization=".(isset($this->organization)?"'".$this->db->escape($this->organization)."'":"null").",";
		$sql.= " email=".(isset($this->email)?"'".$this->db->escape($this->email)."'":"null").",";
		$sql.= " plan=".(isset($this->plan)?"'".$this->db->escape($this->plan)."'":"null").",";
		$sql.= " date_registration=".(dol_strlen($this->date_registration)!=0 ? "'".$this->db->idate($this->date_registration)."'" : 'null').",";
		$sql.= " date_endfreeperiod=".(dol_strlen($this->date_endfreeperiod)!=0 ? "'".$this->db->idate($this->date_endfreeperiod)."'" : 'null').",";
		$sql.= " status=".(isset($this->status)?"'".$this->status."'":"null").",";
		$sql.= " partner=".(isset($this->partner)?"'".$this->db->escape($this->partner)."'":"null").",";
		$sql.= " total_invoiced=".(isset($this->total_invoiced)?$this->total_invoiced:"null").",";
		$sql.= " total_payed=".(isset($this->total_payed)?$this->total_payed:"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " hostname_web=".(isset($this->hostname_web)?"'".$this->db->escape($this->hostname_web)."'":"null").",";
		$sql.= " username_web=".(isset($this->username_web)?"'".$this->db->escape($this->username_web)."'":"null").",";
		$sql.= " password_web=".(isset($this->password_web)?"'".$this->db->escape($this->password_web)."'":"null").",";
		$sql.= " hostname_db=".(isset($this->hostname_db)?"'".$this->db->escape($this->hostname_db)."'":"null").",";
		$sql.= " database_db=".(isset($this->database_db)?"'".$this->db->escape($this->database_db)."'":"null").",";
		$sql.= " port_db=".(isset($this->port_db)?$this->port_db:"null").",";
		$sql.= " username_db=".(isset($this->username_db)?"'".$this->db->escape($this->username_db)."'":"null").",";
		$sql.= " password_db=".(isset($this->password_db)?"'".$this->db->escape($this->password_db)."'":"null").",";
		$sql.= " lastcheck=".(dol_strlen($this->lastcheck)!=0 ? "'".$this->db->idate($this->lastcheck)."'" : 'null').",";
		$sql.= " nbofusers=".(isset($this->nbofusers)?$this->nbofusers:"null").",";
		$sql.= " lastlogin=".(dol_strlen($this->lastlogin)!=0 ? "'".$this->lastlogin."'" : 'null').",";
		$sql.= " lastpass=".(dol_strlen($this->lastpass)!=0 ? "'".$this->lastpass."'" : 'null').",";
		$sql.= " date_lastlogin=".(dol_strlen($this->date_lastlogin)!=0 ? "'".$this->db->idate($this->date_lastlogin)."'" : 'null').",";
		$sql.= " modulesenabled=".(isset($this->modulesenabled)?"'".$this->db->escape($this->modulesenabled)."'":"null").",";
		$sql.= " firstname=".(isset($this->firstname)?"'".$this->db->escape($this->firstname)."'":"null").",";
		$sql.= " lastname=".(isset($this->lastname)?"'".$this->db->escape($this->lastname)."'":"null").",";
		$sql.= " address=".(isset($this->address)?"'".$this->db->escape($this->address)."'":"null").",";
		$sql.= " zip=".(isset($this->zip)?"'".$this->db->escape($this->zip)."'":"null").",";
		$sql.= " town=".(isset($this->town)?"'".$this->db->escape($this->town)."'":"null").",";
		$sql.= " country_id=".(isset($this->country_id)?"'".$this->db->escape($this->country_id)."'":"null").",";
		$sql.= " state_id=".(isset($this->state_id)?"'".$this->db->escape($this->state_id)."'":"null").",";
		$sql.= " phone=".(isset($this->phone)?"'".$this->db->escape($this->phone)."'":"null").",";
		$sql.= " fileauthorizedkey=".(dol_strlen($this->fileauthorizedkey)!=0 ? "'".$this->db->idate($this->fileauthorizedkey)."'" : 'null').",";
		$sql.= " filelock=".(dol_strlen($this->filelock)!=0 ? "'".$this->db->idate($this->filelock)."'" : 'null').",";
		$sql.= " lastrsync=".(dol_strlen($this->date_lastrsync)!=0 ? "'".$this->db->idate($this->date_lastrsync)."'" : 'null').",";
		$sql.= " version=".(isset($this->version)?"'".$this->db->escape($this->version)."'":"null").",";
		$sql.= " vat_number=".(isset($this->vat_number)?"'".$this->db->escape($this->vat_number)."'":"null");

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
	 *  Delete object in database
	 *
     *	@param  User	$user        User that delete
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

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

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."dolicloud_customers";
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(get_class($this)."::delete sql=".$sql);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
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
	 *  Return name of contact with link (and eventually picto)
	 *	Use $this->id, $this->name, $this->firstname, this->civilite_id
	 *
	 *	@param		int			$withpicto		Include picto with link
	 *	@param		string		$option			Where the link point to
	 *	@param		int			$maxlen			Max length of
	 *	@return		string						String with URL
	 */
	function getNomUrl($withpicto=0,$option='',$maxlen=0)
	{
	    global $langs;

	    $result='';

	    $lien = '<a href="'.dol_buildpath('/nltechno/dolicloud_card.php',1).'?id='.$this->id.'">';
	    $lienfin='</a>';

	    if ($withpicto) $result.=($lien.img_object($langs->trans("ShowCustomer").': '.$this->ref,'generic').$lienfin.' ');
	    $result.=$lien.($maxlen?dol_trunc($this->ref,$maxlen):$this->ref).$lienfin;
	    return $result;
	}


	/**
	 *    Return label of status (activity, closed)
	 *
	 *    @param	int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long
	 *    @return   string        		Libelle
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Renvoi le libelle d'un statut donne
	 *
	 *  @param	int		$statut         Id statut
	 *  @param	int		$mode           0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string          		Libelle du statut
	 */
	function LibStatut($statut,$mode=0)
	{
		global $langs;
		$langs->load('nltechno@nltechno');

		if ($this->status == 'ACTIVE') $picto=img_picto($langs->trans("Active"),'statut4');
		elseif ($this->status == 'CLOSED_QUEUED') $picto=img_picto($langs->trans("Disabled"),'statut6');
		elseif ($this->status == 'UNDEPLOYED') $picto=img_picto($langs->trans("Active"),'statut5');
		elseif ($this->status == 'TRIAL_EXPIRED') $picto=img_picto($langs->trans("Expired"),'statut1');
		elseif ($this->status == 'TRIAL') $picto=img_picto($langs->trans("Trial"),'statut0');
		else $picto=img_picto($langs->trans("Trial"),'statut0');

		if ($mode == 0)
		{
			return $this->status;
		}
		if ($mode == 1)
		{
			return $this->status;
		}
		if ($mode == 2)
		{
			return $picto.' '.$this->status;
		}
		if ($mode == 3)
		{
			return $picto;
		}
		if ($mode == 4)
		{
			return $picto.' '.$this->status;
		}
		if ($mode == 5)
		{
			return $this->status.' '.$picto;
		}
	}


	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Dolicloudcustomers($this->db);

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
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;

		$this->instance='specimeninstance';
		$this->organization='specimenorga';
		$this->email='test@test.com';
		$this->plan='';
		$this->date_registration='';
		$this->date_endfreeperiod='';
		$this->status='ACTIVE';
		$this->partner='';
		$this->total_invoiced='';
		$this->total_payed='';
		$this->tms='';
		$this->hostname_web='';
		$this->username_web='';
		$this->password_web='';
		$this->hostname_db='';
		$this->database_db='';
		$this->port_db='';
		$this->username_db='';
		$this->password_db='';
		$this->lastcheck='';
		$this->nbofusers='';
		$this->lastlogin='specimenlogin';
		$this->lastpass='';
		$this->date_lastlogin='2012-01-01';
		$this->modulesenabled='';
		$this->fileauthorizedkey='';
		$this->filelock='';
		$this->version='3.0.0';
		$this->date_lastrsync='2012-01-02';
		$this->vat_number='FR123456';
	}

}
?>
