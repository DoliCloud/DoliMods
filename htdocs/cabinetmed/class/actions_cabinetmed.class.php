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
    function __construct($db)
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
        global $db,$langs,$conf,$backtopage;

        $ret=0;
        dol_syslog(get_class($this).'::executeHooks action='.$action);

        $arraytmp=dol_getdate(dol_now());

        // Define cabinetmed context
        $cabinetmedcontext=0;
        if (isset($parameters['id']) && isset($parameters['currentcontext']) && in_array($parameters['currentcontext'],array('agendathirdparty','categorycard','infothirdparty','consumptionthirdparty')) && empty($action))
        {
        	$thirdparty=new Societe($db);
        	$thirdparty->fetch($parameters['id']);
        	if ($thirdparty->canvas == 'patient@cabinetmed') $cabinetmedcontext++;
        }

		if (GETPOST('canvas') == 'patient@cabinetmed') $cabinetmedcontext++;

        if ($cabinetmedcontext)
        {
       		$langs->tab_translate["ThirdParty"]=$langs->transnoentitiesnoconv("Patient");
       		$langs->tab_translate["ThirdPartyName"]=$langs->transnoentitiesnoconv("PatientName");
       		$langs->tab_translate["CustomerCode"]=$langs->transnoentitiesnoconv("PatientCode");
       		$langs->load("errors");
        	$langs->tab_translate["ErrorBadThirdPartyName"]=$langs->transnoentitiesnoconv("ErrorBadPatientName");
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
			$day=(int) $birthdatearray['tm_mday'];
            $month=((int) $birthdatearray['tm_month'] + 1);
            $year=((int) $birthdatearray['tm_year'] + 1900);
            $birthdate=dol_mktime(0,0,0,$month,$day,$year,true,true);
            if (GETPOST('idprof3') && (empty($birthdatearray['tm_year']) || (empty($birthdate) && $birthdate != '0') || ($day > 31) || ($month > 12) || ($year >( $arraytmp['year']+1))))
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
                            $this->errors[]=$langs->trans("ErrorPatientNameAlreadyExists",$nametocheck);
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

            if ($ret == 0 && $parameters['id'] > 0) $backtopage=$_SERVER["PHP_SELF"]."?socid=".$parameters['id'];
        }

        // Hook called when asking to update a record
        if ($action == 'update')
        {
            $nametocheck=GETPOST('nom');
            $date=GETPOST('idprof3');
            //$confirmduplicate=$_POST['confirmduplicate'];

            // Check on date
			$birthdatearray=dol_cm_strptime($date,$conf->format_date_short);
			$day=(int) $birthdatearray['tm_mday'];
            $month=((int) $birthdatearray['tm_month'] + 1);
            $year=((int) $birthdatearray['tm_year'] + 1900);
            //var_dump($birthdatearray);
            //var_dump($date."-".$birthdate."-".$day."-".$month."-".$year);exit;
            $birthdate=dol_mktime(0,0,0,$month,$day,$year,true,true);
            if (GETPOST('idprof3') && (empty($birthdatearray['tm_year']) || empty($birthdate) || ($day > 31) || ($month > 12) || ($year > ($arraytmp['year']+1))))
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
     * Complete search forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	string					HTML content to add by hook
     */
    function printSearchForm($parameters)
    {
        global $langs, $user, $conf;

        $searchform='';
        if (! empty($conf->cabinetmed->enabled) && ! empty($user->rights->cabinetmed->read))
        {
            $langs->load("companies");
            $langs->load("cabinetmed@cabinetmed");
            $searchform=printSearchForm(dol_buildpath('/cabinetmed/patients.php',1), dol_buildpath('/cabinetmed/patients.php',1), img_picto('','object_patient@cabinetmed').' '.$langs->trans("Patients"), '', 'search_nom');
        }
		$this->resprints = $searchform;

        return 0;
    }

    /**
     * Add fields into tr form of objects
     *
     * @param	array	$parameters		Array of parameters
     * @param   mixed	&$object      	Object
     * @param   string	&$action      	'add', 'update', 'view'
     * @param   string	&$hookmanager  	'add', 'update', 'view'
     * @return	string					HTML content to add by hook
     */
    function formObjectOptions($parameters, &$object, &$action, &$hookmanager)
    {
        global $langs, $user, $conf;


    }


    /**
     * Add more actions buttons
     *
     * @param	array	$parameters		Array of parameters
     * @param   mixed	&$object      	Object
     * @param   string	&$action      	'add', 'update', 'view'
     * @param   string	&$hookmanager  	'add', 'update', 'view'
     * @return	string					HTML content to add by hook
     */
    function addMoreActionsButtons($parameters, &$object, &$action, &$hookmanager)
    {
        global $langs, $user, $conf;

        if (! empty($object->societe->id) && $object->societe->id > 0 && ! empty($object->societe->canvas) && $object->societe->canvas == 'patient@cabinetmed')
        {
        	if ($action != 'edit')
        	{
	    		print '<div class="inline-block divButAction"><a class="butAction" href="'.dol_buildpath('/cabinetmed/consultations.php?socid='.$object->societe->id.'&action=create&fk_agenda='.$object->id, 1).'">';
	    		print $langs->trans("NewConsult");
	    		print '</a></div>';
        	}
        }
    }


    /**
     * Complete doc forms
     *
     * @param	array	$parameters		Array of parameters
     * @param	Object	$object			Object
     * @return	int						0 if KO, 1 to replace, -1 if KO
     */
    function formBuilddocOptions($parameters, $object)
    {
        global $langs, $user, $conf, $form;

        if (empty($parameters['modulepart']) || $parameters['modulepart'] != 'company') return 0;	// Add nothing

        include_once(DOL_DOCUMENT_ROOT.'/core/modules/societe/modules_societe.class.php');
        $modellist=ModeleThirdPartyDoc::liste_modeles($this->db);

		if ($object->canvas != 'patient@cabinetmed') return 0;

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
        $out.=$form->selectarray('idconsult',$array_consult,$firstid,1);
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
        $out.=$form->selectarray('idbio',$array_consult,GETPOST('idbio')?GETPOST('idbio'):'',1);
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
        $out.=$form->selectarray('idradio',$array_consult,GETPOST('idradio')?GETPOST('idradio'):'',1);

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

        $this->resprints = $out;

        return 0;
    }
}

