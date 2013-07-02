<?php
/* Copyright (C) 2011 		Juanjo Menent 			<jmenent@2byte.es>
 * Copyright (C) 2012 		Ferran Marcet           <fmarcet@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU  *General Public License as published by
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

/**
 *  \file       htdocs/pos/class/place.class.php
 *  \ingroup    ticket
 *  \brief      Cash Class file
 *  \version    $Id: cash.class.php,v 1.5 2011-08-16 15:36:15 jmenent Exp $
 */

/**
 *  \class      Place
 *  \brief      Class to manage Places
 */

class Place extends CommonObject
{
    var $db;
    var $error;
    var $errors=array();
    var $element='pos_places';
    var $table_element='pos_places';
    var $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

    var $id;
    var $entity;
    var $name;
    var $ref;
    var $description;
    var $fk_ticket;
    var $status;
    
    var $fk_user_c;
    var $fk_user_m;
    var $datec;
    var $datea;
 

    /**
     *	\brief  Constructeur de la classe
     *	\param  DB         	handler acces base de donnees
     *	\param  name		id place ('' par defaut)
     */
    function Place($DB, $name='')
    {
        $this->db = $DB;

        $this->name = $name;
        $this->fk_ticket=0;
               
    }

    /**
     *	Create place in database
	 *	@param     	user       		Object user that create
     *	@return		int				<0 if KO, >0 if OK
     */
    function create($user)
    {
        global $langs,$conf,$mysoc;
        $error=0;

        // Clean parameters

        dol_syslog("Place::Create user=".$user->id);

        // Check parameters

		$now=dol_now();
        $this->db->begin();
        // Insert into database
        
        $name = $this->name;
        $description = trim($this->description);
                
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."pos_places (";
        $sql.= " name";
        $sql.= ", entity";
        $sql.= ", description";
        $sql.= ", status";
        $sql.= ", fk_ticket";
        $sql.= ", datec";
        $sql.= ", datea";
        $sql.= ", fk_user_c";
        $sql.= ")";
        $sql.= " VALUES (";
        $sql.= "'".$this->name."'";
        $sql.= ", ".$conf->entity;
        $sql.= ", '".$this->description."'";
        $sql.= ", 1";
        $sql.= ",".($this->fk_ticket?$this->fk_ticket:0);
        $sql.= ", ".$this->db->idate($now);
        $sql.= ", ".$this->db->idate($now);
        $sql.= ",".($user->id > 0 ? "'".$user->id."'":"null");
        $sql.= ")";

        dol_syslog("Place::Create sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
        	$this->db->commit();
            return 0;

        }
        else
        {
            $this->error=$this->db->error();
            dol_syslog("Place::create error ".$this->error." sql=".$sql, LOG_ERR);
            $this->db->rollback();
            return -1;
        }
    }

    /**
     *	Get object from database
     *	@param      rowid       Id of object to load
     * 	@param		ref 		Ref of place
     *	@return     int         >0 if OK, <0 if KO
     */
    function fetch($rowid,$ref='')
    {
        global $conf;

        if (empty($rowid) && empty($ref)) return -1;

        $sql = 'SELECT rowid';
        $sql.= ', name';
        $sql.= ', description';
        $sql.= ', status';
        $sql.= ', fk_ticket';
        $sql.= ', datec';
        $sql.= ', datea';
        $sql.= ', fk_user_c';
        $sql.= ', fk_user_m';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'pos_places';
        $sql.= ' WHERE entity = '.$conf->entity;
        if ($rowid)   $sql.= " AND rowid=".$rowid;
        else $sql.= " AND name='".$ref."'";
        

        dol_syslog("Places::Fetch sql=".$sql, LOG_DEBUG);
        $result = $this->db->query($sql);
        if ($result)
        {
            if ($this->db->num_rows($result))
            {
                $obj = $this->db->fetch_object($result);

                $this->id				= $obj->rowid;
                $this->name				= $obj->name;
                $this->ref				= $obj->name;
                $this->description		= $obj->description;
                $this->status			= $obj->status;
                $this->fk_ticket		= $obj->fk_ticket;
                $this->datec			= $obj->datec;
                $this->datea			= $obj->datea;
                $this->fk_user_c		= $obj->fk_user_c;
                $this->fk_user_m		= $obj->fk_user_m;
                
                return 1;
            }
            else
            {
                $this->error='Place with id '.$rowid.' not found sql='.$sql;
                dol_syslog('Place::Fetch Error '.$this->error, LOG_ERR);
                return -2;
            }
        }
        else
        {
            $this->error=$this->db->error();
            dol_syslog('Place::Fetch Error '.$this->error, LOG_ERR);
            return -1;
        }
    }
    
    
    /**
     *      \brief      Update database
     *      \param      user        	User that modify
     *      \return     int         	<0 if KO, >0 if OK
     */
    function update($user)
    {
        global $conf, $langs;
        $error=0;

        // Clean parameters
        
        if (isset($this->name)) $this->name=trim($this->name);
        if (isset($this->description)) $this->description=trim($this->description);
        
            
        // Check parameters
        $now=dol_now();
        // Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."pos_places SET";
        $sql.= " name='".$this->name."',";
        $sql.= " description='".$this->description."',";
        $sql.= " datea = ".$this->db->idate($now).",";
        $sql.= " fk_user_m=".($user->id > 0 ? "'".$user->id."'":"null");
        $sql.= " WHERE rowid=".$this->id;

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
     *	Delete Place
     *	@param     	rowid      	Id of place to delete
     *	@return		int			<0 if KO, >0 if OK
     */
    function delete($rowid=0)
    {
        global $user,$langs,$conf;

        if (! $rowid) $rowid=$this->id;

        dol_syslog(get_class($this)."::delete rowid=".$rowid, LOG_DEBUG);

    	// Check if cash can be deleted
		$nb=0;
		$sql = "SELECT COUNT(*) as nb from ".MAIN_DB_PREFIX."pos_ticket";
		$sql.= " WHERE fk_place = " . $rowid;
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$obj=$this->db->fetch_object($resql);
			if ($obj->nb > 0)
			{
				$this->error="ErrorRecordHasTickets";
            	return -2;
			}
		}
		else
		{
			$this->error .= $this->db->lasterror();
			dol_syslog(get_class($this)."::Delete erreur -1 ".$this->error, LOG_ERR);
			return -1;
		}

        // Remove third Cash
		$sql = "DELETE from ".MAIN_DB_PREFIX."pos_places";
		$sql.= " WHERE rowid = " . $rowid;
		
		dol_syslog("Places::Delete sql=".$sql, LOG_DEBUG);
		if ($this->db->query($sql))
		{
			return 1;
		}
		else
		{
 			$this->error = $this->db->lasterror();
			dol_syslog("Places::Delete erreur -3 ".$this->error, LOG_ERR);
			return -1;
		}
        
    }

    /**
     *      Tag the Place as active
     *      @param      user      	Objet utilisateur qui modifie
     *      @return     int         <0 si ok, >0 si ok
     */
    function set_active($user)
    {
        global $conf,$langs;
        $error=0;

        if ($this->status != 1 && $user->id > 0)
        {
            $this->db->begin();

            dol_syslog(get_class($this)."::set_used rowid=".$this->id, LOG_DEBUG);
            $sql = 'UPDATE '.MAIN_DB_PREFIX.'pos_places SET';
            $sql.= ' status = 1';
                     
            $sql.= ' WHERE rowid = '.$this->id;

            $resql = $this->db->query($sql);
            if (!$resql)
            {
                $error++;
                $this->error=$this->db->error();
                dol_print_error($this->db);
            }

            if (! $error)
            {
                $this->db->commit();
                return 1;
            }
            else
            {
                $this->db->rollback();
                return -1;
            }
        }
        else
        {
            return 0;
        }
    }


     /**
     *      Tag the Place with inactive
     *      @param      user      	Objet utilisateur qui modifie
     *      @return     int         <0 si ok, >0 si ok
     */
    function set_inactive($user)
    {
		global $conf,$langs;
        $error=0;

        if ($this->status != 0)
        {
            $this->db->begin();

            dol_syslog(get_class($this)."::set_inactive rowid=".$this->id, LOG_DEBUG);
            $sql = 'UPDATE '.MAIN_DB_PREFIX.'pos_places SET';
            $sql.= ' status = 0';
            $sql.= ' WHERE rowid = '.$this->id;

            $resql = $this->db->query($sql);
            if (!$resql)
            {
                $error++;
                $this->error=$this->db->error();
                dol_print_error($this->db);
            }

            if (! $error)
            {
                $this->db->commit();
                return 1;
            }
            else
            {
                $this->db->rollback();
                return -1;
            }
        }
        else
        {
            return 0;
        }
    }
    
   	/**
     *    Returns if a cash can be deleted
     *    @return     boolean     true if yes, false if not
     */
    function can_be_deleted()
    {
        $can_be_deleted=false;

      	$sql = "SELECT COUNT(*) as nb from ".MAIN_DB_PREFIX."pos_ticket";
		$sql.= " WHERE fk_place = " . $this->id;

        $resql = $this->db->query($sql);
        if ($resql) 
        {
            $obj=$this->db->fetch_object($resql);
            if ($obj->nb <= 0) $can_be_deleted=true;
        }
        else 
        {
            dol_print_error($this->db);
        }
        return $can_be_deleted;
    }
    
     /**
     *    	Renturns clicable name
     *		@param		withpicto		Include picto in link
     *		@return		string			String avec URL
     */
    function getNomUrl($withpicto=0)
    {
        global $langs;

        $result='';

		$lien = '<a href='.dol_buildpath('/pos/backend/place/fiche.php',1).'?id='.$this->id.'>';
		$lienfin='</a>';
       
        if ($withpicto) $result.=($lien.img_object($langs->trans("ShowPlace").' '.$this->name,'barcode').$lienfin.' ');
        $result.=$lien.$this->name.$lienfin;
        return $result;
    }
    
    /**
     * 		Renturns clicable tickets name
     *		@param		withpicto		Include picto in link
     *		@return		string			String avec URL
     */
    function getTicketUrl($withpicto=0)
    {
    	global $langs,$conf;
    	$result='';
    	
    	$sql = 'SELECT rowid';
    	$sql.= ', ticketnumber';
    	$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket';
    	$sql.= ' WHERE entity = '.$conf->entity;
    	$sql.= " AND rowid=".$this->fk_ticket;
    	
    	$resql = $this->db->query($sql);
    	if ($resql)
    	{
    		$obj=$this->db->fetch_object($resql);
    		$lien = '<a href='.dol_buildpath('/pos/backend/ticket.php',1).'?id='.$this->fk_ticket.'>';
    		$lienfin='</a>';
    		if ($withpicto) $result.=($lien.img_object($langs->trans("ShowTicket").' '.$obj->ticketnumber,'barcode').$lienfin.' ');
    		$result.=$lien.$obj->ticketnumber.$lienfin;
    		return $result;
    	}
    	else
    	{
    		dol_print_error($this->db);
    	}
    }
    
    /**
     * 		Returns if place is closed
     * 		@return 	boolean		true if is close, false if not
     */
    function is_closed()
    {
    	if($this->status)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    /**
     * 		Returns if place is in Use
     * 		@return 	boolean		true if is in use, false if not
     */
    function is_inUse()
    {
    	if($this->status && $this->fk_ticket > 0)
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}
    }
    /**
     * 		Returns if place is not used
     * 		@return 	boolean		true if isn't in use, false if is in use
     */
    function is_notInUse()
    {
    	if ($this->status && $this->fk_ticket == 0)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
 	/**
     *    Return label of status (activity, closed)
     *    @param      mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long
     *    @return     string        Libelle
     */
    function getLibStatut($mode=0)
    {
    	return $this->LibStatut($this->status,$mode);
    }

    /**
     *      Renvoi le libelle d'un statut donne
     *      @param      statut          Id statut
     *      @param      mode            0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *      @return     string          Libelle du statut
     */
    function LibStatut($statut,$mode=0)
    {
        global $langs;
        $langs->load('pos');

        if ($mode == 0)
        {
            if ($statut==0) 
            {
            	return $langs->trans("Closed");
            }
            if ($statut==1)
            { 
            	if($this->fk_ticket)
            	{
            		return $langs->trans("InUse");
            	}
            	else
            	{
            		return $langs->trans("NotInUse");
            	}
            }
            
        }
        if ($mode == 1)
        {
        	if ($statut==0) 
        	{
        		return $langs->trans("Closed");
        	}
            if ($statut==1)
            { 
            	if($this->fk_ticket)
            	{
            		return $langs->trans("InUse");
            	}
            	else
            	{
            		return $langs->trans("NotInUse");
            	}
            }
        }
        if ($mode == 2)
        {
            if ($statut==0) 
            {
            	return img_picto($langs->trans("Closed"),'statut6').' '.$langs->trans("Closed");
            }
            if ($statut==1)
            { 
            	if($this->fk_ticket)
            	{
            		return img_picto($langs->trans("InUse"),'statut8').' '.$langs->trans("InUse");
            	}
            	else
            	{
            		return img_picto($langs->trans("NotInUse"),'statut4').' '.$langs->trans("NotInUse");
            	}
            }
           
        }
        if ($mode == 3)
        {
            if ($statut==0) 
            {
            	return img_picto($langs->trans("Closed"),'statut6');
            }
            if ($statut==1) 
            {
            	if($this->fk_ticket)
            	{
            		return img_picto($langs->trans("InUse"),'statut8');
            	}
            	else 
            	{
            		return img_picto($langs->trans("NotInUse"),'statut4');
            	}
            }
           
        }
        if ($mode == 4)
        {
            if ($statut==0) 
            {
            	return img_picto($langs->trans("Closed"),'statut8').' '.$langs->trans("Closed");
            }
            if ($statut==1) 
            {
            	if($this->fk_ticket)
            	{
            		return img_picto($langs->trans("InUse"),'statut1').' '.$langs->trans("InUse");
            	}
            	else 
            	{
            		return img_picto($langs->trans("NotInUse"),'statut4').' '.$langs->trans("NotInUse");
            	}
            }
           
        }
        if ($mode == 5)
        {
            if ($statut==0) 
            {
            	return $langs->trans("Closed").' '.img_picto($langs->trans("statut6"),'Closed');
            }
            if ($statut==1) 
            {
            	if($this->fk_ticket)
            	{
            		return $langs->trans("InUse").' '.img_picto($langs->trans("InUse"),'statut8');
            	}
            	else 
            	{
            		return $langs->trans("NotInUse").' '.img_picto($langs->trans("NotInUse"),'statut4');
            	}
            }
           
        }
    }
    
   /**
     *       Charge les informations d'ordre info dans l'objet societe
     *       @param     id     Id de la societe a charger
     */
    function info($id)
    {
        $sql = "SELECT rowid, name, datec, datea,";
        $sql.= " fk_user_c, fk_user_m";
        $sql.= " FROM ".MAIN_DB_PREFIX."pos_places as s";
        $sql.= " WHERE rowid = ".$id;

        $result=$this->db->query($sql);
        if ($result)
        {
            if ($this->db->num_rows($result))
            {
                $obj = $this->db->fetch_object($result);

                $this->id = $obj->rowid;

                if ($obj->fk_user_c) 
                {
                    $cuser = new User($this->db);
                    $cuser->fetch($obj->fk_user_c);
                    $this->user_creation     = $cuser;
                }

                if ($obj->fk_user_m) 
                {
                    $muser = new User($this->db);
                    $muser->fetch($obj->fk_user_m);
                    $this->user_modification = $muser;
                }
                $this->name			     = $obj->name;
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
     * Set the place as free of tickets
     * 
     * @return 	number	> 0 OK, < 0 ko
     */
    
    function free_place()
    {
    	$sql = "UPDATE ".MAIN_DB_PREFIX."pos_places SET";
    	$sql.= " fk_ticket = " . 0 ;
    	$sql.= " WHERE rowid=".$this->id;
    	
    	$this->db->begin();
    	$result=$this->db->query($sql);
    	
    	if (! $result) 
    	{
    		$this->db->rollback();
    		return -1;
    	}
    	else
    	{
    		$this->db->commit();
    		return 1;
    	}
    }
    
    /**
     * Set the place as is in Use
     * 
     * @param 	$id_ticket	Id of ticket to associate to the place
     * @return 	number	> 0 OK, < 0 ko
     */
    function set_place($id_ticket)
    {
    	$sql = "UPDATE ".MAIN_DB_PREFIX."pos_places SET";
    	$sql.= " fk_ticket = " . $id_ticket ;
    	$sql.= " WHERE rowid=".$this->id;
    	
    	$this->db->begin();
    	$result=$this->db->query($sql);
    	 
    	if (! $result)
    	{
    		$this->db->rollback();
    		return -1;
    	}
    	else
    	{
    		$this->db->commit();
    		return 1;
    	}
    }

}