<?php
/* Copyright (C) 2011 Laurent Destailleur         <eldy@users.sourceforge.net>
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
 *	\file			htdocs/cabinetmed/core/modules/substitutions/functions_cabinetmed.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains functions for plugin cabinetmed.
 */


/**
 * 		Function called to complete substitution array (before generating on ODT, or a personalized email)
 * 		functions xxx_completesubstitutionarray are called by make_substitutions() if file
 * 		is inside directory htdocs/core/substitutions
 *
 *		@param	array		&$substitutionarray	Array with substitution key=>val
 *		@param	Translate	$langs				Output langs
 *		@param	Object		$object				Object to use to get values
 * 		@return	void							The entry parameter $substitutionarray is modified
 */
function cabinetmed_completesubstitutionarray(&$substitutionarray,$langs,$object)
{
	global $conf,$db;

	dol_include_once('/cabinetmed/class/cabinetmedcons.class.php');
    dol_include_once('/cabinetmed/class/cabinetmedexambio.class.php');
    dol_include_once('/cabinetmed/class/cabinetmedexamother.class.php');

    $langs->load("cabinetmed@cabinetmed");

    $isbio=0;
    $isother=0;

    // Consultation + Exams
    if (GETPOST('idconsult') > 0)
    {
    	$outcome=new CabinetmedCons($db);
    	$result1=$outcome->fetch(GETPOST('idconsult'));
		$isconsult=1;
    }
    if (GETPOST('idbio') > 0)
    {
        $exambio=new CabinetmedExamBio($db);
        $result2=$exambio->fetch(GETPOST('idbio'));
        $isbio=1;
    }
    if (GETPOST('idradio') > 0)
    {
        $examother=new CabinetmedExamOther($db);
        $result3=$examother->fetch(GETPOST('idradio'));
        $isother=1;
    }

    if ($isother || $isbio) $substitutionarray['examshows']=$langs->transnoentitiesnoconv("ExamsShow");
    else $substitutionarray['examshows']='';

    if ($isother)	// An image exam was selected
    {
        $substitutionarray['examother_title']=$langs->transnoentitiesnoconv("BilanImage").':';
        $substitutionarray['examother_principal_and_conclusion']=$examother->examprinc.' : '.$examother->concprinc;
        $substitutionarray['examother_principal']=$examother->examprinc;
        $substitutionarray['examother_conclusion']=$examother->concprinc;
    }
    else
    {
        $substitutionarray['examother_title']='';
        $substitutionarray['examother_principal_and_conclusion']='';
        $substitutionarray['examother_principal']='';
        $substitutionarray['examother_conclusion']='';
    }
    if ($isbio)	// A bio exam was selected
    {
        if (! empty($exambio->conclusion)) $substitutionarray['exambio_title']=$langs->transnoentitiesnoconv("BilanBio").':';
        else $substitutionarray['exambio_title']='';
        $substitutionarray['exambio_conclusion']=$exambio->conclusion;
    }
    else
    {
        $substitutionarray['exambio_title']='';
        $substitutionarray['exambio_conclusion']='';
    }
	if ($isconsult)	// A consultation was selected
	{
	    $substitutionarray['outcome_date']=dol_print_date($outcome->datecons,'day');
	    $substitutionarray['outcome_reason']=$outcome->motifconsprinc;
	    $substitutionarray['outcome_diagnostic']=$outcome->diaglesprinc;
	    if (! empty($outcome->traitementprescrit))
	    {
	        $substitutionarray['treatment_title']=$langs->transnoentitiesnoconv("TreatmentSugested"); // old string
	        $substitutionarray['outcome_treatment_title']=$langs->transnoentitiesnoconv("TreatmentSugested");
	        $substitutionarray['outcome_treatment']=$outcome->traitementprescrit;
	    }
	    else
	    {
	        $substitutionarray['treatment_title']='';	// old string
	    	$substitutionarray['outcome_treatment_title']='';
	        $substitutionarray['outcome_treatment']='';
	    }
    	$substitutionarray['outcome_total_inctax_card']=$outcome->montant_carte;
    	$substitutionarray['outcome_total_inctax_cheque']=$outcome->montant_cheque;
    	$substitutionarray['outcome_total_inctax_cash']=$outcome->montant_espece;
    	$substitutionarray['outcome_total_inctax_other']=$outcome->montant_tiers;
    	$substitutionarray['outcome_total_ttc']=($outcome->montant_carte+$outcome->montant_cheque+$outcome->montant_espece+$outcome->montant_tiers);
	}
	else
	{
		$substitutionarray['treatment_title']='';	// old string
		$substitutionarray['outcome_treatment_title']='';	// old string
		$substitutionarray['outcome_treatment']='';
	}

    $substitutionarray['outcome_comment']=GETPOST('outcome_comment');


    // Patient
    //$patient=new Patient($db);
    //var_dump($object);
    //$patient->fetch($object->fk_soc);
    $substitutionarray['patient_name']=$object->name;
	$substitutionarray['patient_code']=$object->code_client;
	$substitutionarray['patient_barcode']=$object->barcode;
	$substitutionarray['patient_barcode_type']=$object->barcode_type_code;
	$substitutionarray['patient_country_code']=$object->country_code;
	$substitutionarray['patient_country']=$object->country;
	$substitutionarray['patient_email']=$object->email;
	$substitutionarray['patient_size']=$object->idprof1;
	$substitutionarray['patient_weight']=$object->idprof2;
    $substitutionarray['patient_birthdate']=$object->idprof3;
    $substitutionarray['patient_profession']=$object->idprof4;
    $substitutionarray['patient_gender']=$object->typent_code;
    $substitutionarray['patient_socialnum']=$object->tva_intra;
}

