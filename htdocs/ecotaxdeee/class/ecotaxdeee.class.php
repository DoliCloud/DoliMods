<?php
/* Copyright (C) 2011-2014	Laurent Destailleur	<eldy@users.sourceforge.net>
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
 *	\file       htdocs/ecotaxdeee/class/ecotaxdeee.class.php
 *	\ingroup    ecotaxdeee
 *	\brief      File for CRUD ecotaxdeee
 */
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";

class Ecotaxdeee extends CommonObject
{

    /**
	 * @var string ID to identify managed object
	 */
	public $element = 'ecotaxdeee';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'ecotaxdeee';

    /**
	 * @var DoliDB $db Database object
	 */
	public $db;

    /**
     * @var string  code name
     */
    public $code;

    /**
     * @var double  amount of ecotax
     */
    public $amount;

    /**
     * @var date  date creation
     */
    public $date_creation;
    /**
	 *  Constructor
	 *
	 *  @param	DoliDB	$db 	Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

   /**
	 * Create ecotax record into database
	 *
	 * @param	User	$user		User who created the ecotax
	 * @param	int		$notrigger	Disable triggers
	 * @return  int  		        Return integer <0 if KO, id of created ecotax if OK
	 */
    public function create($user, $notrigger = 0)
    {
        global $langs;


        if ($this->amount < 0) {
			$this->error = $langs->trans('FieldCannotBeNegative', $langs->transnoentitiesnoconv("Amount"));
			return -1;
		}
        $this->db->begin();

        // check if records already exists

        $check = "SELECT code,amount FROM ".MAIN_DB_PREFIX."ecotax";
        $check .= " WHERE code ='".$this->code."' AND amount=".$this->amount;
        $rslt = $this->db->query($check);
        $num = $this->db->num_rows($rslt);

        if ($num > 1) {
            $this->error = $langs->trans('RecordAlreadyExists');
            return -1;
        }else {

            $sql = "INSERT INTO ".MAIN_DB_PREFIX."ecotaxdeee (";
            $sql .= "code, ";
            $sql .= "amount, ";
            $sql .= "date_creation";
            $sql .= ")";
            $sql .= " VALUES (";
            $sql .= "'".$this->db->escape($this->code)."'";
            $sql .= ", ".(float)($this->amount);
            $sql .= ", '".$this->db->idate($this->date_creation ? $this->date_creation : dol_now())."'";
            $sql .= ")";
            $resql = $this->db->query($sql);
            if (!$resql) {
                $this->error = $this->db->lasterror();
                $this->db->rollback();
                return -1;
            }

            $this->db->commit();
            return 1;
        }
    }

        /**
	 *      Load ecotax records from database
	 *      @return     array|int     Return integer <0 if KO, >0 if OK
	 */
	public function fetchAll()
	{
        $sql = "SELECT rowid,code, amount, date_creation";
        $sql .= " FROM ".MAIN_DB_PREFIX."ecotaxdeee";

        dol_syslog(get_class($this)."::fetchAll", LOG_DEBUG);
        $resql = $this->db->query($sql);

        if ($resql) {
            $result = array();
            while ($obj = $this->db->fetch_object($resql)) {
                $result[] = $obj;
            }
            return $result;
        } else {
            dol_print_error($this->db);
            return -1;
        }
    }

    /**
	 *      Load ecotax records from database
     *      @param      int    $id    id of record
	 *      @return     object|int     Return integer <0 if KO, >0 if OK
	 */
	public function fetch($id)
	{
        $sql = "SELECT rowid,code, amount, date_creation";
        $sql .= " FROM ".MAIN_DB_PREFIX."ecotaxdeee";
        if (!empty($id)) {
            $sql .= " WHERE rowid=".((int) $id);
        }

        dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
        $resql = $this->db->query($sql);

        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            return $obj;
        } else {
            dol_print_error($this->db);
            return -1;
        }
    }

    /**
     * Update record for ecotax
     * @param    int   $id    id record to update
     * @return   int   1 if OK, -1 if KO
     */
    public function update($id)
    {
        global $langs;


        $code = $this->db->escape($this->code);
        $amount = ((float) $this->amount);

         // check if records already exists

         $check = "SELECT code,amount FROM ".MAIN_DB_PREFIX."ecotaxdeee";
         $check .= " WHERE code ='".$this->code."' AND amount=".$this->amount;
         $rslt = $this->db->query($check);
         $num = $this->db->num_rows($rslt);
         if ($num > 1) {
             $this->error = $langs->trans('RecordAlreadyExists');
             return -1;
            }else {
                $sql = "UPDATE ".MAIN_DB_PREFIX."ecotax SET ";
                $sql .= "code = '".$code."', ";
                $sql .= "amount = '".$amount."', ";
                $sql .= "date_modification = '".$this->db->idate(dol_now())."' ";
                $sql .= "WHERE rowid = ".((int) $id);

                $resql = $this->db->query($sql);
            if (!$resql) {
                $this->error = $this->db->lasterror();
                $this->db->rollback();
                return -1;
            }

            $this->db->commit();
            return 1;
        }
    }

    /**
     * Delete record in ecotax table
     *
     * @param   User   $user   	User making the deletion
     * @return  int   			1 if OK, -1 if KO
     */
    public function delete($user)
    {
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."ecotaxdeee";
        $sql .= " WHERE rowid = ".((int) $this->id);

        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            $this->db->rollback();
            return -1;
        }

        $this->db->commit();
        return 1;
    }
}
