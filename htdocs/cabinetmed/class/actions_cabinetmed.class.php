<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *	\file       htdocs/cabinetmed/class/actions_cabinetmed.class.php
 *	\ingroup    societe
 *	\brief      File to control actions
 *	\version    $Id: actions_cabinetmed.class.php,v 1.3 2011/07/06 21:36:51 eldy Exp $
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	\class 		ActionsCabinetmed
 *	\brief 		Class to manage hooks for module Cabinetmed
 */
class ActionsCabinetmed
{
    var $db;
    var $error;
    var $errors=array();

    /**
     *    Constructor for class
     *    @param  DB     handler acces base de donnees
     */
    function ActionsCabinetmed($DB)
    {
        $this->db = $DB;
    }


    /**
     *    Execute action
     *    @param        Object      Deprecated. This field is nto used
     *    @param        action      'add', 'update', 'view'
     *    @param        id          Id of object (in output if create, in input if update of view)
     *    @return       int         <0 if KO,
     *                              =0 if OK but we want to process standard actions too,
     *                              >0 if OK and we want to replace standard actions.
     */
    function doActions(&$object,&$action,&$id)
    {
        global $langs,$conf;

        $ret=0;

        // Hook called when asking to add a new record
        if ($action == 'add')
        {
            $nametocheck=GETPOST('nom');
            $ape=GETPOST('idprof3');
            //$confirmduplicate=$_POST['confirmduplicate'];

            $sql = 'SELECT s.rowid, s.nom, s.entity, s.ape FROM '.MAIN_DB_PREFIX.'societe as s';
            $sql.= ' WHERE s.entity = '.$conf->entity;
            $sql.= " AND s.nom = '".trim($this->db->escape($nametocheck))."'";
            if (! empty($ape))
            {
                $sql.= " AND (s.ape IS NULL OR s.ape = '' OR s.ape = '".trim($this->db->escape($ape))."')";
            }
            $resql=$this->db->query($sql);
            if ($resql)
            {
                $obj=$this->db->fetch_object($resql);
                if ($obj)
                {
                    //if (empty($confirmduplicate) || $nametocheck != $_POST['confirmduplicate'])
                    if (empty($confirmduplicate))
                    {
                        // If already exists, we want to block creation
                        //$_POST['confirmduplicate']=$nametocheck;
                        $this->errors[]=$langs->trans("ErrorCompanyNameAlreadyExists",$nametocheck);
                        $ret=-1;
                    }
                }
                else
                {
                    // Create object, set $id to its id and return 1
                    // or
                    // Do something else and return 0 to use standard code to create;
                    // or
                    // Do nothing
                }
            }
            else dol_print_error($this->db);
        }

        // Hook called when asking to update a record
        if ($action == 'update')
        {

        }

        // Hook called when asking to view a record
        if ($action == 'view')
        {

        }

        return $ret;
    }

}

?>
