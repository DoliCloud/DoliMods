<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       dev/skeletons/submitew_message.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2011-12-17 12:46
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *      \class      SubmitewMessage
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2011-12-17 12:46
 */
class SubmitewMessage // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='submitew_message';			//!< Id that identify managed objects
	//var $table_element='submitew_message';	//!< Name of table without prefix where object is stored

	var $id;

	var $statut;
	var $label;
	var $entity;
	var $title;
	var $body;
	var $bgcolor;
	var $bgimage;
	var $cible;
	var $nbemail;
	var $email_from;
	var $email_replyto;
	var $email_errorsto;
	var $tag;
	var $date_creat='';
	var $date_valid='';
	var $date_appro='';
	var $date_envoi='';
	var $fk_user_creat;
	var $fk_user_valid;
	var $fk_user_appro;
	var $joined_file1;
	var $joined_file2;
	var $joined_file3;
	var $joined_file4;




	/**
	 *  Constructor
	 *
	 *  @param      DoliDb		$db      Database handler
	 */
	function SubmitewMessage($db)
	{
		$this->db = $db;
		return 1;
	}


	/**
	 *  Create object into database
	 *
	 *  @param      User	$user        User that create
	 *  @param      int		$notrigger   0=launch triggers after, 1=disable triggers
	 *  @return     int      		   	 <0 if KO, Id of created object if OK
	 */
	function create($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->statut)) $this->statut=(int) $this->statut;
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->title)) $this->title=trim($this->title);
		if (isset($this->body)) $this->body=trim($this->body);
		if (isset($this->body_long)) $this->bgcolor=trim($this->body_long);
		if (isset($this->cible)) $this->cible=trim($this->cible);
		if (isset($this->nbemail)) $this->nbemail=trim($this->nbemail);
		if (isset($this->email_from)) $this->email_from=trim($this->email_from);
		if (isset($this->email_replyto)) $this->email_replyto=trim($this->email_replyto);
		if (isset($this->email_errorsto)) $this->email_errorsto=trim($this->email_errorsto);
		if (isset($this->tag)) $this->tag=trim($this->tag);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_valid)) $this->fk_user_valid=trim($this->fk_user_valid);
		if (isset($this->fk_user_appro)) $this->fk_user_appro=trim($this->fk_user_appro);
		if (isset($this->joined_file1)) $this->joined_file1=trim($this->joined_file1);
		if (isset($this->joined_file2)) $this->joined_file2=trim($this->joined_file2);
		if (isset($this->joined_file3)) $this->joined_file3=trim($this->joined_file3);
		if (isset($this->joined_file4)) $this->joined_file4=trim($this->joined_file4);



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."submitew_message(";

		$sql.= "statut,";
		$sql.= "label,";
		$sql.= "entity,";
		$sql.= "title,";
		$sql.= "body_short,";
		$sql.= "body_long,";
		$sql.= "cible,";
		$sql.= "nbemail,";
		$sql.= "email_from,";
		$sql.= "email_replyto,";
		$sql.= "email_errorsto,";
		$sql.= "tag,";
		$sql.= "date_creat,";
		$sql.= "date_valid,";
		$sql.= "date_appro,";
		$sql.= "date_envoi,";
		$sql.= "fk_user_creat,";
		$sql.= "fk_user_valid,";
		$sql.= "fk_user_appro,";
		$sql.= "joined_file1,";
		$sql.= "joined_file2,";
		$sql.= "joined_file3,";
		$sql.= "joined_file4";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->statut)?'NULL':"'".$this->statut."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " '".$conf->entity."',";
		$sql.= " ".(! isset($this->title)?'NULL':"'".$this->db->escape($this->title)."'").",";
		$sql.= " ".(! isset($this->body)?'NULL':"'".$this->db->escape($this->body)."'").",";
		$sql.= " ".(! isset($this->body_long)?'NULL':"'".$this->db->escape($this->body_long)."'").",";
		$sql.= " ".(! isset($this->cible)?'NULL':"'".$this->db->escape($this->cible)."'").",";
		$sql.= " ".(! isset($this->nbemail)?'NULL':"'".$this->nbemail."'").",";
		$sql.= " ".(! isset($this->email_from)?'NULL':"'".$this->db->escape($this->email_from)."'").",";
		$sql.= " ".(! isset($this->email_replyto)?'NULL':"'".$this->db->escape($this->email_replyto)."'").",";
		$sql.= " ".(! isset($this->email_errorsto)?'NULL':"'".$this->db->escape($this->email_errorsto)."'").",";
		$sql.= " ".(! isset($this->tag)?'NULL':"'".$this->db->escape($this->tag)."'").",";
		$sql.= " ".(! isset($this->date_creat) || dol_strlen($this->date_creat)==0?'NULL':$this->db->idate($this->date_creat)).",";
		$sql.= " ".(! isset($this->date_valid) || dol_strlen($this->date_valid)==0?'NULL':$this->db->idate($this->date_valid)).",";
		$sql.= " ".(! isset($this->date_appro) || dol_strlen($this->date_appro)==0?'NULL':$this->db->idate($this->date_appro)).",";
		$sql.= " ".(! isset($this->date_envoi) || dol_strlen($this->date_envoi)==0?'NULL':$this->db->idate($this->date_envoi)).",";
		$sql.= " ".(! isset($this->fk_user_creat)?'NULL':"'".$this->fk_user_creat."'").",";
		$sql.= " ".(! isset($this->fk_user_valid)?'NULL':"'".$this->fk_user_valid."'").",";
		$sql.= " ".(! isset($this->fk_user_appro)?'NULL':"'".$this->fk_user_appro."'").",";
		$sql.= " ".(! isset($this->joined_file1)?'NULL':"'".$this->db->escape($this->joined_file1)."'").",";
		$sql.= " ".(! isset($this->joined_file2)?'NULL':"'".$this->db->escape($this->joined_file2)."'").",";
		$sql.= " ".(! isset($this->joined_file3)?'NULL':"'".$this->db->escape($this->joined_file3)."'").",";
		$sql.= " ".(! isset($this->joined_file4)?'NULL':"'".$this->db->escape($this->joined_file4)."'")."";


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."submitew_message");

			if (! $notrigger) {
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
	 *  @param      int	$id    Id object
	 *  @return     int          <0 if KO, >0 if OK
	 */
	function fetch($id)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.statut,";
		$sql.= " t.label,";
		$sql.= " t.entity,";
		$sql.= " t.title,";
		$sql.= " t.body_short,";
		$sql.= " t.body_long,";
		$sql.= " t.cible,";
		$sql.= " t.nbemail,";
		$sql.= " t.email_from,";
		$sql.= " t.email_replyto,";
		$sql.= " t.email_errorsto,";
		$sql.= " t.tag,";
		$sql.= " t.date_creat,";
		$sql.= " t.date_valid,";
		$sql.= " t.date_appro,";
		$sql.= " t.date_envoi,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_valid,";
		$sql.= " t.fk_user_appro,";
		$sql.= " t.joined_file1,";
		$sql.= " t.joined_file2,";
		$sql.= " t.joined_file3,";
		$sql.= " t.joined_file4";


		$sql.= " FROM ".MAIN_DB_PREFIX."submitew_message as t";
		$sql.= " WHERE t.rowid = ".$id;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->statut = $obj->statut;
				$this->label = $obj->label;
				$this->entity = $obj->entity;
				$this->title = $obj->title;
				$this->body = $obj->body;
				$this->body_long = $obj->body_long;
				$this->cible = $obj->cible;
				$this->nbemail = $obj->nbemail;
				$this->email_from = $obj->email_from;
				$this->email_replyto = $obj->email_replyto;
				$this->email_errorsto = $obj->email_errorsto;
				$this->tag = $obj->tag;
				$this->date_creat = $this->db->jdate($obj->date_creat);
				$this->date_valid = $this->db->jdate($obj->date_valid);
				$this->date_appro = $this->db->jdate($obj->date_appro);
				$this->date_envoi = $this->db->jdate($obj->date_envoi);
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_valid = $obj->fk_user_valid;
				$this->fk_user_appro = $obj->fk_user_appro;
				$this->joined_file1 = $obj->joined_file1;
				$this->joined_file2 = $obj->joined_file2;
				$this->joined_file3 = $obj->joined_file3;
				$this->joined_file4 = $obj->joined_file4;
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
	 *  Update object into database
	 *
	 *  @param      User	$user        User that modify
	 *  @param      int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return     int     		   	 <0 if KO, >0 if OK
	 */
	function update($user = null, $notrigger = 0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters

		if (isset($this->statut)) $this->statut=(int) $this->statut;
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->title)) $this->title=trim($this->title);
		if (isset($this->body)) $this->body=trim($this->body);
		if (isset($this->body_long)) $this->bgcolor=trim($this->bgcolor);
		if (isset($this->cible)) $this->cible=trim($this->cible);
		if (isset($this->nbemail)) $this->nbemail=trim($this->nbemail);
		if (isset($this->email_from)) $this->email_from=trim($this->email_from);
		if (isset($this->email_replyto)) $this->email_replyto=trim($this->email_replyto);
		if (isset($this->email_errorsto)) $this->email_errorsto=trim($this->email_errorsto);
		if (isset($this->tag)) $this->tag=trim($this->tag);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_valid)) $this->fk_user_valid=trim($this->fk_user_valid);
		if (isset($this->fk_user_appro)) $this->fk_user_appro=trim($this->fk_user_appro);
		if (isset($this->joined_file1)) $this->joined_file1=trim($this->joined_file1);
		if (isset($this->joined_file2)) $this->joined_file2=trim($this->joined_file2);
		if (isset($this->joined_file3)) $this->joined_file3=trim($this->joined_file3);
		if (isset($this->joined_file4)) $this->joined_file4=trim($this->joined_file4);



		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."submitew_message SET";

		$sql.= " statut=".(isset($this->statut)?$this->statut:"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " entity=".(isset($this->entity)?$this->entity:"null").",";
		$sql.= " title=".(isset($this->title)?"'".$this->db->escape($this->title)."'":"null").",";
		$sql.= " body=".(isset($this->body)?"'".$this->db->escape($this->body)."'":"null").",";
		$sql.= " body_long=".(isset($this->body_long)?"'".$this->db->escape($this->body_long)."'":"null").",";
		$sql.= " cible=".(isset($this->cible)?"'".$this->db->escape($this->cible)."'":"null").",";
		$sql.= " nbemail=".(isset($this->nbemail)?$this->nbemail:"null").",";
		$sql.= " email_from=".(isset($this->email_from)?"'".$this->db->escape($this->email_from)."'":"null").",";
		$sql.= " email_replyto=".(isset($this->email_replyto)?"'".$this->db->escape($this->email_replyto)."'":"null").",";
		$sql.= " email_errorsto=".(isset($this->email_errorsto)?"'".$this->db->escape($this->email_errorsto)."'":"null").",";
		$sql.= " tag=".(isset($this->tag)?"'".$this->db->escape($this->tag)."'":"null").",";
		$sql.= " date_creat=".(dol_strlen($this->date_creat)!=0 ? "'".$this->db->idate($this->date_creat)."'" : 'null').",";
		$sql.= " date_valid=".(dol_strlen($this->date_valid)!=0 ? "'".$this->db->idate($this->date_valid)."'" : 'null').",";
		$sql.= " date_appro=".(dol_strlen($this->date_appro)!=0 ? "'".$this->db->idate($this->date_appro)."'" : 'null').",";
		$sql.= " date_envoi=".(dol_strlen($this->date_envoi)!=0 ? "'".$this->db->idate($this->date_envoi)."'" : 'null').",";
		$sql.= " fk_user_creat=".(isset($this->fk_user_creat)?$this->fk_user_creat:"null").",";
		$sql.= " fk_user_valid=".(isset($this->fk_user_valid)?$this->fk_user_valid:"null").",";
		$sql.= " fk_user_appro=".(isset($this->fk_user_appro)?$this->fk_user_appro:"null").",";
		$sql.= " joined_file1=".(isset($this->joined_file1)?"'".$this->db->escape($this->joined_file1)."'":"null").",";
		$sql.= " joined_file2=".(isset($this->joined_file2)?"'".$this->db->escape($this->joined_file2)."'":"null").",";
		$sql.= " joined_file3=".(isset($this->joined_file3)?"'".$this->db->escape($this->joined_file3)."'":"null").",";
		$sql.= " joined_file4=".(isset($this->joined_file4)?"'".$this->db->escape($this->joined_file4)."'":"null")."";


		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error) {
			if (! $notrigger) {
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
	 *	@param     User	$user        User that delete
	 *  @param     int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger = 0)
	{
		$error=0;

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."submitew_message";
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error) {
			if (! $notrigger) {
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
	 *	@param      int		$fromid     Id of object to clone
	 * 	@return		int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Submitew_message($this->db);

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
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;

		$this->statut=1;
		$this->label='Label';
		$this->entity='';
		$this->title='Title';
		$this->body='Short text';
		$this->body_long='Long text';
		$this->cible='';
		$this->nbemail='';
		$this->email_from='';
		$this->email_replyto='';
		$this->email_errorsto='';
		$this->tag='';
		$this->date_creat='';
		$this->date_valid='';
		$this->date_appro='';
		$this->date_envoi='';
		$this->fk_user_creat='';
		$this->fk_user_valid='';
		$this->fk_user_appro='';
		$this->joined_file1='';
		$this->joined_file2='';
		$this->joined_file3='';
		$this->joined_file4='';
	}
}
