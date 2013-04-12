<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/cabinetmed/class/actions_cabinetmed.class.php
 *	\ingroup    cabinetmed
 *	\brief      File to control actions
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
dol_include_once("/cabinetmed/lib/cabinetmed.lib.php");


/**
 *	Class to manage hooks for module Cabinetmed
 */
class ActionsCabinetmed
{
    var $db;
    var $error;
    var $errors=array();

    /**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
     */
    function ActionsCabinetmed($db)
    {
        $this->db = $db;
    }


    /**
     *    Execute action
     *
     *    @param	array	$parameters		Array of parameters
     *    @param    mixed	&$object      	Deprecated. This field is not used
     *    @param    string	&$action      	'add', 'update', 'view'
     *    @return   int         			<0 if KO,
     *                              		=0 if OK but we want to process standard actions too,
     *                              		>0 if OK and we want to replace standard actions.
     */
    function doActions($parameters,&$object,&$action)
    {
        global $db,$langs,$conf,$backtourl;

        $ret=0;
        dol_syslog(get_class($this).'::executeHooks action='.$action);

        //print 'action='.$action;
        //var_dump($parameters);
        if (isset($parameters['id']) && isset($parameters['context']) && in_array($parameters['context'],array('agendathirdparty','categorycard','infothirdparty')) && empty($action))
        {
        	$thirdparty=new Societe($db);
        	$thirdparty->fetch($parameters['id']);
        	if ($thirdparty->canvas == 'patient@cabinetmed')
        	{
        		$langs->tab_translate["ThirdParty"]=$langs->trans("Patient");
        		$langs->tab_translate["ThirdPartyName"]=$langs->trans("PatientName");
        	}
        }

        require_once(DOL_DOCUMENT_ROOT ."/core/lib/date.lib.php");

        // Hook called when asking to add a new record
        if ($action == 'add')
        {
            $nametocheck=GETPOST('nom');
            $date=GETPOST('idprof3');
            //$confirmduplicate=$_POST['confirmduplicate'];

            // Check on date
            $birthdatearray=dol_cm_strptime($date,$conf->format_date_short);
            $birthdate=dol_mktime(0,0,0,$birthdatearray['tm_mon']+1,($birthdatearray['tm_mday']),($birthdatearray['tm_year']+1900),true);
            if (GETPOST('idprof3') && empty($birthdate))
            {
                $langs->load("errors");
                $this->errors[]=$langs->trans("ErrorBadDateFormat",$date);
                $ret=-1;
            }

            // Check duplicate
            if (! $ret)
            {
                $sql = 'SELECT s.rowid, s.nom, s.entity, s.ape FROM '.MAIN_DB_PREFIX.'societe as s';
                $sql.= ' WHERE s.entity = '.$conf->entity;
                $sql.= " AND s.nom = '".trim($this->db->escape($nametocheck))."'";
                if (! empty($date))
                {
                    $sql.= " AND (s.ape IS NULL OR s.ape = '' OR s.ape = '".trim($this->db->escape($date))."')";
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
                            $langs->load("errors");
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

            if ($ret == 0) $backtourl=$_SERVER["PHP_SELF"]."?socid=__ID__";
        }

        // Hook called when asking to update a record
        if ($action == 'update')
        {
            $nametocheck=GETPOST('nom');
            $date=GETPOST('idprof3');
            //$confirmduplicate=$_POST['confirmduplicate'];

            // Check on date
            $birthdatearray=dol_cm_strptime($date,$conf->format_date_short);
            $birthdate=dol_mktime(0,0,0,$birthdatearray['tm_mon']+1,($birthdatearray['tm_mday']),($birthdatearray['tm_year']+1900),true);
            if (GETPOST('idprof3') && empty($birthdate))
            {
                $langs->load("errors");
                $this->errors[]=$langs->trans("ErrorBadDateFormat",$date);
                $ret=-1;
            }
        }

        // Hook called when asking to view a record
        if ($action == 'view')
        {

        }

        return $ret;
    }



    /**
     * Complete doc forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	void
     */
    function addDemoProfile($parameters)
    {
    	global $conf;

    	if ($conf->cabinetmed->enabled)
    	{
    		if (! empty($conf->global->CABINETMED_DEMO_URL))
    		{
    			// $conf->global->CABINETMED_DEMO_URL = 'http://demodolimed.dolibarr.org'
    			$GLOBALS['demoprofiles'][]=array(
    				'default'=>'0',
    				'key'=>'profdemomed',
    				'lang'=>'cabinetmed@cabinetmed',
    				'label'=>'DemoCabinetMed',
    				'url'=>$conf->global->CABINETMED_DEMO_URL,
    		 		'disablemodules'=>'adherent,boutique,don,externalsite',
    		 		'icon'=>DOL_URL_ROOT.'/public/demo/dolibarr_screenshot9.png'
    			);
    		}
    	}
    }


    /**
     * Complete doc forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	string					HTML content to add by hook
     */
    function printSearchForm($parameters)
    {
        global $langs, $user, $conf;

        if ($conf->cabinetmed->enabled && $user->rights->cabinetmed->read)
        {
            $langs->load("companies");
            $langs->load("cabinetmed@cabinetmed");
            $searchform.=printSearchForm(dol_buildpath('/cabinetmed/patients.php',1), dol_buildpath('/cabinetmed/patients.php',1), img_picto('','object_patient').' '.$langs->trans("Patients"), '', 'search_nom');
        }

        return $searchform;
    }


    /**
     * Complete doc forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	string					HTML content to add by hook
     */
    function formBuilddocOptions($parameters)
    {
        global $langs, $user, $conf;

        if (empty($parameters['modulepart']) || $parameters['modulepart'] != 'company') return '';	// Add nothing

        $htmlform=new Form($this->db);

        include_once(DOL_DOCUMENT_ROOT.'/core/modules/societe/modules_societe.class.php');
        $modellist=ModeleThirdPartyDoc::liste_modeles($this->db);

        $out='';
        $out.='<tr>';
        $out.='<td align="left" colspan="4" valign="top" class="formdoc">';

        // Add javascript to disable/enabled button
        if (is_array($modellist) && count($modellist) > 0)
        {
            $out.="\n".'<script type="text/javascript" language="javascript">';
            $out.='jQuery(document).ready(function () {';
            $out.='    function initbutton(param) {';
            $out.='        if (param >= 0) { jQuery("#builddoc_generatebutton").removeAttr(\'disabled\'); }';
            $out.='        else { jQuery("#builddoc_generatebutton").attr(\'disabled\',true); }';
            $out.='    }';
            $out.='    initbutton(jQuery("#idconsult").val()); ';
            $out.='    jQuery("#idconsult").change(function() { initbutton(jQuery(this).val()); });';
            $out.='});';
            $out.='</script>'."\n";
        }
        else
        {
            $langs->load("errors");
            $out.=' &nbsp; '.img_warning($langs->transnoentitiesnoconv("ErrorModuleSetupNotComplete")).' &nbsp; ';
        }

        $firstid=0;
        $out.='<font class="fieldrequired">'.$langs->trans("Consultation").':</font> ';
        $array_consult=array();
        $sql='SELECT rowid, datecons as date FROM '.MAIN_DB_PREFIX.'cabinetmed_cons where fk_soc='.$parameters['socid'];
        $sql.=' ORDER BY datecons DESC, rowid DESC';
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num=$this->db->num_rows($resql);
            $i=0;
            while($i < $num)
            {
                $obj=$this->db->fetch_object($resql);
                $array_consult[$obj->rowid]=sprintf("%08d",$obj->rowid).' - '.dol_print_date($this->db->jdate($obj->date),'day');
                if (empty($firstid)) $firstid=$obj->rowid;
                $i++;
            }
        }
        else dol_print_error($this->db);
        $out.=$htmlform->selectarray('idconsult',$array_consult,$firstid,1);
        //print '</td>';
        //print '<td align="center">';

        $out.=' &nbsp; &nbsp; &nbsp; ';

        $out.=$langs->trans("ResultExamBio").': ';
        $array_consult=array();
        $sql='SELECT rowid, dateexam as date FROM '.MAIN_DB_PREFIX.'cabinetmed_exambio where fk_soc='.$parameters['socid'];
        $sql.=' ORDER BY dateexam DESC, rowid DESC';
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num=$this->db->num_rows($resql);
            $i=0;
            while($i < $num)
            {
                $obj=$this->db->fetch_object($resql);
                $array_consult[$obj->rowid]=dol_print_date($this->db->jdate($obj->date),'day');
                $i++;
            }
        }
        else dol_print_error($this->db);
        $out.=$htmlform->selectarray('idbio',$array_consult,GETPOST('idbio')?GETPOST('idbio'):'',1);
        //$out.= '</td>';
        //$out.= '<td align="center">';

        $out.=' &nbsp; &nbsp; &nbsp; ';

        $out.=$langs->trans("ResultExamAutre").': ';
        $array_consult=array();
        $sql='SELECT rowid, dateexam as date FROM '.MAIN_DB_PREFIX.'cabinetmed_examaut where fk_soc='.$parameters['socid'];
        $sql.=' ORDER BY dateexam DESC, rowid DESC';
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num=$this->db->num_rows($resql);
            $i=0;
            while($i < $num)
            {
                $obj=$this->db->fetch_object($resql);
                $array_consult[$obj->rowid]=dol_print_date($this->db->jdate($obj->date),'day');
                $i++;
            }
        }
        else dol_print_error($this->db);
        $out.=$htmlform->selectarray('idradio',$array_consult,GETPOST('idradio')?GETPOST('idradio'):'',1);

        if (! is_array($modellist) || count($modellist) == 0)
        {
            $langs->load("errors");
            $out.=' &nbsp; '.img_warning($langs->transnoentitiesnoconv("ErrorModuleSetupNotComplete")).' &nbsp; ';
        }

        $out.='</td>';
        $out.='</tr>';

        $out.='<tr><td colspan="4" valign="top" class="formdoc">';
        $out.=$langs->trans("Comment").': ';
        //$out.= '<textarea name="outcome_comment" cols="90" rows="'.ROWS_2.'">'.(GETPOST('outcome_comment')?GETPOST('outcome_comment'):'').'</textarea>';
        $out.='<input type="text" name="outcome_comment" size="90" value="'.(GETPOST('outcome_comment')?GETPOST('outcome_comment'):'').'">';
        $out.='</td></tr>';

        return $out;
    }
}

?>
