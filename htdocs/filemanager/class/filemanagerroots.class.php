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
 *      \file       dev/skeletons/filemanager_roots.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *      \class      Filemanager_roots
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2010-08-17 13:30
 */
class FilemanagerRoots // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='filemanager_roots';			//!< Id that identify managed objects
	//var $table_element='filemanager_roots';	//!< Name of table without prefix where object is stored

	var $id;

	var $datec='';
	var $rootlabel;
	var $rootpath;
	var $note;
	var $position;
	var $entity;




	/**
	 *	Constructor
	 *
	 * 	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}


	/**
	 *   Create in database
	 *
	 *   @param		User	$user        	User that create
	 *   @param     int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *   @return    int         			<0 if KO, Id of created object if OK
	 */
	function create($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->rootlabel)) $this->rootlabel=trim($this->rootlabel);
		if (isset($this->rootpath)) $this->rootpath=trim($this->rootpath);
		if (isset($this->note)) $this->note=trim($this->note);
		if (isset($this->position)) $this->position=trim($this->position);
		if (isset($this->entity)) $this->entity=trim($this->entity);



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."filemanager_roots(";

		$sql.= "datec,";
		$sql.= "rootlabel,";
		$sql.= "rootpath,";
		$sql.= "note,";
		$sql.= "position,";
		$sql.= "entity";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->datec) || strlen($this->datec)==0?'NULL':$this->db->idate($this->datec)).",";
		$sql.= " ".(! isset($this->rootlabel)?'NULL':"'".addslashes($this->rootlabel)."'").",";
		$sql.= " ".(! isset($this->rootpath)?'NULL':"'".addslashes($this->rootpath)."'").",";
		$sql.= " ".(! isset($this->note)?'NULL':"'".addslashes($this->note)."'").",";
		$sql.= " ".(! isset($this->position)?'NULL':"'".$this->position."'").",";
		$sql.= " ".(! isset($conf->entity)?'NULL':"'".$conf->entity."'")."";


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."filemanager_roots");
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		} else {
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

		$sql.= " t.datec,";
		$sql.= " t.rootlabel,";
		$sql.= " t.rootpath,";
		$sql.= " t.note,";
		$sql.= " t.position,";
		$sql.= " t.entity";


		$sql.= " FROM ".MAIN_DB_PREFIX."filemanager_roots as t";
		$sql.= " WHERE t.rowid = ".((int) $id);

		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->datec = $this->db->jdate($obj->datec);
				$this->rootlabel = $obj->rootlabel;
				$this->rootpath = $obj->rootpath;
				$this->note = $obj->note;
				$this->position = $obj->position;
				$this->entity = $obj->entity;
			}
			$this->db->free($resql);

			return 1;
		} else {
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}


	/**
	 *   Update database
	 *
	 *   @param		User	$user        	User that modify
	 *   @param     int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *   @return    int         			<0 if KO, >0 if OK
	 */
	function update($user = null, $notrigger = 0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->rootlabel)) $this->rootlabel=trim($this->rootlabel);
		if (isset($this->rootpath)) $this->rootpath=trim($this->rootpath);
		if (isset($this->note)) $this->note=trim($this->note);
		if (isset($this->position)) $this->position=trim($this->position);
		if (isset($this->entity)) $this->entity=trim($this->entity);



		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."filemanager_roots SET";

		$sql.= " datec=".(strlen($this->datec)!=0 ? "'".$this->db->idate($this->datec)."'" : 'null').",";
		$sql.= " rootlabel=".(isset($this->rootlabel)?"'".addslashes($this->rootlabel)."'":"null").",";
		$sql.= " rootpath=".(isset($this->rootpath)?"'".addslashes($this->rootpath)."'":"null").",";
		$sql.= " note=".(isset($this->note)?"'".addslashes($this->note)."'":"null").",";
		$sql.= " position=".(isset($this->position)?$this->position:"null").",";
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null")."";


		$sql.= " WHERE rowid=".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		} else {
			$this->db->commit();
			return 1;
		}
	}


	/**
	 *  Delete object in database
	 *
	 *	@param		User	$user        	User that delete
	 *  @param      int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *	@return		int						<0 if KO, >0 if OK
	 */
	function delete($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error=0;

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."filemanager_roots";
		$sql.= " WHERE rowid=".((int) $this->id);

		$this->db->begin();

		dol_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		} else {
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param		int		$fromid    		Id of object to clone
	 * 	@return		int						New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Filemanager_roots($this->db);

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
		if ($result < 0) {
			$this->error=$object->error;
			$error++;
		}

		if (! $error) {
		}

		// End
		if (! $error) {
			$this->db->commit();
			return $object->id;
		} else {
			$this->db->rollback();
			return -1;
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

		$this->datec='';
		$this->rootlabel='';
		$this->rootpath='';
		$this->note='';
		$this->position='';
		$this->entity='';
	}
}
