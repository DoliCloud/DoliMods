<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *  \file       dev/skeletons/appinstance.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2014-02-26 10:39
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Appinstance extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='appinstance';			//!< Id that identify managed objects
	var $table_element='appinstance';		//!< Name of table without prefix where object is stored

    var $id;

	var $version;
	var $app_package_id;
	var $created_date='';
	var $customer_account_id;
	var $db_name;
	var $db_password;
	var $db_port;
	var $db_server;
	var $db_username;
	var $default_password;
	var $deployed_date='';
	var $domain_id;
	var $fs_path;
	var $install_time;
	var $ip_address;
	var $last_login='';
	var $last_updated='';
	var $name;
	var $os_password;
	var $os_username;
	var $rm_install_url;
	var $rm_web_app_name;
	var $status;
	var $undeployed_date='';
	var $access_enabled;
	var $default_username;
	var $ssh_port;




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
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->version)) $this->version=trim($this->version);
		if (isset($this->app_package_id)) $this->app_package_id=trim($this->app_package_id);
		if (isset($this->customer_account_id)) $this->customer_account_id=trim($this->customer_account_id);
		if (isset($this->db_name)) $this->db_name=trim($this->db_name);
		if (isset($this->db_password)) $this->db_password=trim($this->db_password);
		if (isset($this->db_port)) $this->db_port=trim($this->db_port);
		if (isset($this->db_server)) $this->db_server=trim($this->db_server);
		if (isset($this->db_username)) $this->db_username=trim($this->db_username);
		if (isset($this->default_password)) $this->default_password=trim($this->default_password);
		if (isset($this->domain_id)) $this->domain_id=trim($this->domain_id);
		if (isset($this->fs_path)) $this->fs_path=trim($this->fs_path);
		if (isset($this->install_time)) $this->install_time=trim($this->install_time);
		if (isset($this->ip_address)) $this->ip_address=trim($this->ip_address);
		if (isset($this->name)) $this->name=trim($this->name);
		if (isset($this->os_password)) $this->os_password=trim($this->os_password);
		if (isset($this->os_username)) $this->os_username=trim($this->os_username);
		if (isset($this->rm_install_url)) $this->rm_install_url=trim($this->rm_install_url);
		if (isset($this->rm_web_app_name)) $this->rm_web_app_name=trim($this->rm_web_app_name);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->access_enabled)) $this->access_enabled=trim($this->access_enabled);
		if (isset($this->default_username)) $this->default_username=trim($this->default_username);
		if (isset($this->ssh_port)) $this->ssh_port=trim($this->ssh_port);



		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."app_instance(";

		$sql.= "version,";
		$sql.= "app_package_id,";
		$sql.= "created_date,";
		$sql.= "customer_account_id,";
		$sql.= "db_name,";
		$sql.= "db_password,";
		$sql.= "db_port,";
		$sql.= "db_server,";
		$sql.= "db_username,";
		$sql.= "default_password,";
		$sql.= "deployed_date,";
		$sql.= "domain_id,";
		$sql.= "fs_path,";
		$sql.= "install_time,";
		$sql.= "ip_address,";
		$sql.= "last_login,";
		$sql.= "last_updated,";
		$sql.= "name,";
		$sql.= "os_password,";
		$sql.= "os_username,";
		$sql.= "rm_install_url,";
		$sql.= "rm_web_app_name,";
		$sql.= "status,";
		$sql.= "undeployed_date,";
		$sql.= "access_enabled,";
		$sql.= "default_username,";
		$sql.= "ssh_port";


        $sql.= ") VALUES (";

		$sql.= " ".(! isset($this->version)?'NULL':"'".$this->version."'").",";
		$sql.= " ".(! isset($this->app_package_id)?'NULL':"'".$this->app_package_id."'").",";
		$sql.= " ".(! isset($this->created_date) || dol_strlen($this->created_date)==0?'NULL':$this->db->idate($this->created_date)).",";
		$sql.= " ".(! isset($this->customer_account_id)?'NULL':"'".$this->customer_account_id."'").",";
		$sql.= " ".(! isset($this->db_name)?'NULL':"'".$this->db->escape($this->db_name)."'").",";
		$sql.= " ".(! isset($this->db_password)?'NULL':"'".$this->db->escape($this->db_password)."'").",";
		$sql.= " ".(! isset($this->db_port)?'NULL':"'".$this->db->escape($this->db_port)."'").",";
		$sql.= " ".(! isset($this->db_server)?'NULL':"'".$this->db->escape($this->db_server)."'").",";
		$sql.= " ".(! isset($this->db_username)?'NULL':"'".$this->db->escape($this->db_username)."'").",";
		$sql.= " ".(! isset($this->default_password)?'NULL':"'".$this->db->escape($this->default_password)."'").",";
		$sql.= " ".(! isset($this->deployed_date) || dol_strlen($this->deployed_date)==0?'NULL':$this->db->idate($this->deployed_date)).",";
		$sql.= " ".(! isset($this->domain_id)?'NULL':"'".$this->domain_id."'").",";
		$sql.= " ".(! isset($this->fs_path)?'NULL':"'".$this->db->escape($this->fs_path)."'").",";
		$sql.= " ".(! isset($this->install_time)?'NULL':"'".$this->install_time."'").",";
		$sql.= " ".(! isset($this->ip_address)?'NULL':"'".$this->db->escape($this->ip_address)."'").",";
		$sql.= " ".(! isset($this->last_login) || dol_strlen($this->last_login)==0?'NULL':$this->db->idate($this->last_login)).",";
		$sql.= " ".(! isset($this->last_updated) || dol_strlen($this->last_updated)==0?'NULL':$this->db->idate($this->last_updated)).",";
		$sql.= " ".(! isset($this->name)?'NULL':"'".$this->db->escape($this->name)."'").",";
		$sql.= " ".(! isset($this->os_password)?'NULL':"'".$this->db->escape($this->os_password)."'").",";
		$sql.= " ".(! isset($this->os_username)?'NULL':"'".$this->db->escape($this->os_username)."'").",";
		$sql.= " ".(! isset($this->rm_install_url)?'NULL':"'".$this->db->escape($this->rm_install_url)."'").",";
		$sql.= " ".(! isset($this->rm_web_app_name)?'NULL':"'".$this->db->escape($this->rm_web_app_name)."'").",";
		$sql.= " ".(! isset($this->status)?'NULL':"'".$this->db->escape($this->status)."'").",";
		$sql.= " ".(! isset($this->undeployed_date) || dol_strlen($this->undeployed_date)==0?'NULL':$this->db->idate($this->undeployed_date)).",";
		$sql.= " ".(! isset($this->access_enabled)?'NULL':"'".$this->access_enabled."'").",";
		$sql.= " ".(! isset($this->default_username)?'NULL':"'".$this->db->escape($this->default_username)."'").",";
		$sql.= " ".(! isset($this->ssh_port)?'NULL':"'".$this->ssh_port."'")."";


		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."app_instance");

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
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
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";

		$sql.= " t.id,";
		$sql.= " t.version,";
		$sql.= " t.app_package_id,";
		$sql.= " t.created_date,";
		$sql.= " t.customer_account_id,";
		$sql.= " t.db_name,";
		$sql.= " t.db_password,";
		$sql.= " t.db_port,";
		$sql.= " t.db_server,";
		$sql.= " t.db_username,";
		$sql.= " t.default_password,";
		$sql.= " t.deployed_date,";
		$sql.= " t.domain_id,";
		$sql.= " t.fs_path,";
		$sql.= " t.install_time,";
		$sql.= " t.ip_address,";
		$sql.= " t.last_login,";
		$sql.= " t.last_updated,";
		$sql.= " t.name,";
		$sql.= " t.os_password,";
		$sql.= " t.os_username,";
		$sql.= " t.rm_install_url,";
		$sql.= " t.rm_web_app_name,";
		$sql.= " t.status,";
		$sql.= " t.undeployed_date,";
		$sql.= " t.access_enabled,";
		$sql.= " t.default_username,";
		$sql.= " t.ssh_port";


        $sql.= " FROM ".MAIN_DB_PREFIX."app_instance as t";
        $sql.= " WHERE t.id = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->id;

				$this->version = $obj->version;
				$this->app_package_id = $obj->app_package_id;
				$this->created_date = $this->db->jdate($obj->created_date);
				$this->customer_account_id = $obj->customer_account_id;
				$this->db_name = $obj->db_name;
				$this->db_password = $obj->db_password;
				$this->db_port = $obj->db_port;
				$this->db_server = $obj->db_server;
				$this->db_username = $obj->db_username;
				$this->default_password = $obj->default_password;
				$this->deployed_date = $this->db->jdate($obj->deployed_date);
				$this->domain_id = $obj->domain_id;
				$this->fs_path = $obj->fs_path;
				$this->install_time = $obj->install_time;
				$this->ip_address = $obj->ip_address;
				$this->last_login = $this->db->jdate($obj->last_login);
				$this->last_updated = $this->db->jdate($obj->last_updated);
				$this->name = $obj->name;
				$this->os_password = $obj->os_password;
				$this->os_username = $obj->os_username;
				$this->rm_install_url = $obj->rm_install_url;
				$this->rm_web_app_name = $obj->rm_web_app_name;
				$this->status = $obj->status;
				$this->undeployed_date = $this->db->jdate($obj->undeployed_date);
				$this->access_enabled = $obj->access_enabled;
				$this->default_username = $obj->default_username;
				$this->ssh_port = $obj->ssh_port;


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
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->version)) $this->version=trim($this->version);
		if (isset($this->app_package_id)) $this->app_package_id=trim($this->app_package_id);
		if (isset($this->customer_account_id)) $this->customer_account_id=trim($this->customer_account_id);
		if (isset($this->db_name)) $this->db_name=trim($this->db_name);
		if (isset($this->db_password)) $this->db_password=trim($this->db_password);
		if (isset($this->db_port)) $this->db_port=trim($this->db_port);
		if (isset($this->db_server)) $this->db_server=trim($this->db_server);
		if (isset($this->db_username)) $this->db_username=trim($this->db_username);
		if (isset($this->default_password)) $this->default_password=trim($this->default_password);
		if (isset($this->domain_id)) $this->domain_id=trim($this->domain_id);
		if (isset($this->fs_path)) $this->fs_path=trim($this->fs_path);
		if (isset($this->install_time)) $this->install_time=trim($this->install_time);
		if (isset($this->ip_address)) $this->ip_address=trim($this->ip_address);
		if (isset($this->name)) $this->name=trim($this->name);
		if (isset($this->os_password)) $this->os_password=trim($this->os_password);
		if (isset($this->os_username)) $this->os_username=trim($this->os_username);
		if (isset($this->rm_install_url)) $this->rm_install_url=trim($this->rm_install_url);
		if (isset($this->rm_web_app_name)) $this->rm_web_app_name=trim($this->rm_web_app_name);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->access_enabled)) $this->access_enabled=trim($this->access_enabled);
		if (isset($this->default_username)) $this->default_username=trim($this->default_username);
		if (isset($this->ssh_port)) $this->ssh_port=trim($this->ssh_port);



		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."app_instance SET";

		$sql.= " version=".(isset($this->version)?$this->version:"null").",";
		$sql.= " app_package_id=".(isset($this->app_package_id)?$this->app_package_id:"null").",";
		$sql.= " created_date=".(dol_strlen($this->created_date)!=0 ? "'".$this->db->idate($this->created_date)."'" : 'null').",";
		$sql.= " customer_account_id=".(isset($this->customer_account_id)?$this->customer_account_id:"null").",";
		$sql.= " db_name=".(isset($this->db_name)?"'".$this->db->escape($this->db_name)."'":"null").",";
		$sql.= " db_password=".(isset($this->db_password)?"'".$this->db->escape($this->db_password)."'":"null").",";
		$sql.= " db_port=".(isset($this->db_port)?"'".$this->db->escape($this->db_port)."'":"null").",";
		$sql.= " db_server=".(isset($this->db_server)?"'".$this->db->escape($this->db_server)."'":"null").",";
		$sql.= " db_username=".(isset($this->db_username)?"'".$this->db->escape($this->db_username)."'":"null").",";
		$sql.= " default_password=".(isset($this->default_password)?"'".$this->db->escape($this->default_password)."'":"null").",";
		$sql.= " deployed_date=".(dol_strlen($this->deployed_date)!=0 ? "'".$this->db->idate($this->deployed_date)."'" : 'null').",";
		$sql.= " domain_id=".(isset($this->domain_id)?$this->domain_id:"null").",";
		$sql.= " fs_path=".(isset($this->fs_path)?"'".$this->db->escape($this->fs_path)."'":"null").",";
		$sql.= " install_time=".(isset($this->install_time)?$this->install_time:"null").",";
		$sql.= " ip_address=".(isset($this->ip_address)?"'".$this->db->escape($this->ip_address)."'":"null").",";
		$sql.= " last_login=".(dol_strlen($this->last_login)!=0 ? "'".$this->db->idate($this->last_login)."'" : 'null').",";
		$sql.= " last_updated=".(dol_strlen($this->last_updated)!=0 ? "'".$this->db->idate($this->last_updated)."'" : 'null').",";
		$sql.= " name=".(isset($this->name)?"'".$this->db->escape($this->name)."'":"null").",";
		$sql.= " os_password=".(isset($this->os_password)?"'".$this->db->escape($this->os_password)."'":"null").",";
		$sql.= " os_username=".(isset($this->os_username)?"'".$this->db->escape($this->os_username)."'":"null").",";
		$sql.= " rm_install_url=".(isset($this->rm_install_url)?"'".$this->db->escape($this->rm_install_url)."'":"null").",";
		$sql.= " rm_web_app_name=".(isset($this->rm_web_app_name)?"'".$this->db->escape($this->rm_web_app_name)."'":"null").",";
		$sql.= " status=".(isset($this->status)?"'".$this->db->escape($this->status)."'":"null").",";
		$sql.= " undeployed_date=".(dol_strlen($this->undeployed_date)!=0 ? "'".$this->db->idate($this->undeployed_date)."'" : 'null').",";
		$sql.= " access_enabled=".(isset($this->access_enabled)?$this->access_enabled:"null").",";
		$sql.= " default_username=".(isset($this->default_username)?"'".$this->db->escape($this->default_username)."'":"null").",";
		$sql.= " ssh_port=".(isset($this->ssh_port)?$this->ssh_port:"null")."";


        $sql.= " WHERE id=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
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
     *	@param  User	$user        User that deletes
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
		        // want this action calls a trigger.

		        //// Call triggers
		        //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."app_instance";
    		$sql.= " WHERE id=".$this->id;

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
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Appinstance($this->db);

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

		$this->version='';
		$this->app_package_id='';
		$this->created_date='';
		$this->customer_account_id='';
		$this->db_name='';
		$this->db_password='';
		$this->db_port='';
		$this->db_server='';
		$this->db_username='';
		$this->default_password='';
		$this->deployed_date='';
		$this->domain_id='';
		$this->fs_path='';
		$this->install_time='';
		$this->ip_address='';
		$this->last_login='';
		$this->last_updated='';
		$this->name='';
		$this->os_password='';
		$this->os_username='';
		$this->rm_install_url='';
		$this->rm_web_app_name='';
		$this->status='';
		$this->undeployed_date='';
		$this->access_enabled='';
		$this->default_username='';
		$this->ssh_port='';


	}

}
?>
