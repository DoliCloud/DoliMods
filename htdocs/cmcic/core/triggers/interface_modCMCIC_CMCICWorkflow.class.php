<?php
/* Copyright (C) 2012      Mikael Carlavan        <mcarlavan@qis-network.com>
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
 *      \file       /htdocs/includes/triggers/interface_modCMCIC_CMCICWorkflow.class.php
 *      \ingroup    cmcic
 *      \brief      Trigger file for cmcic workflow
 */


/**
 *      \class      InterfaceCMCICWorkflow
 *      \brief      Class of triggers for cmcic module
 */
class InterfaceCMCICWorkflow
{
    var $db;

	/**
     *	Constructor
     *
     * 	@param	DoliDB	$db		Database handler
     */
	function __construct($db)
    {
        $this->db = $db;

        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "cmcic";
        $this->description = "Triggers of this module allows to manage cmcic workflow";
        $this->version = 'dolibarr';            // 'development', 'experimental', 'dolibarr' or version
        $this->picto = 'cmcic@cmcic';
    }


    /**
     *  Renvoi nom du lot de triggers
     *
     *  @return     string      Nom du lot de triggers
     */
    function getName()
    {
        return $this->name;
    }

    /**
     *  Renvoi descriptif du lot de triggers
     *
     *  @return     string      Descriptif du lot de triggers
     */
    function getDesc()
    {
        return $this->description;
    }

    /**
     *  Renvoi version du lot de triggers
     *
     *  @return     string      Version du lot de triggers
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development') return $langs->trans("Development");
        elseif ($this->version == 'experimental') return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else return $langs->trans("Unknown");
    }

    /**
     *  Fonction appelee lors du declenchement d'un evenement Dolibarr.
     *  D'autres fonctions run_trigger peuvent etre presentes dans includes/triggers
     *
     *  @param	string		$action     Code de l'evenement
     *  @param  Object		$object     Objet concerne
     *  @param  User		$user       Objet user
     *  @param  Translate	$langs      Objet langs
     *  @param  Conf		$conf       Objet conf
     *  @return int         			<0 if fatal error, 0 si nothing done, >0 if ok
     */
	function run_trigger($action, $object, $user, $langs, $conf)
    {
        require_once(DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php');
        require_once(DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php');
        require_once(DOL_DOCUMENT_ROOT."/core/lib/security.lib.php");
        require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");



        if ($action == 'BILL_SENTBYMAIL')
        {
            $langs->load("cmcic@cmcic");
        	dol_syslog("CMCIC: Trigger '".$this->name."' for action '".$action."' launched by ".__FILE__." ref=".$object->ref);

            $refDolibarr = $object->ref;


            $item = new Facture($this->db);

            $result = $item->fetch('', $refDolibarr);
            if ($result < 0)
            {
                dol_syslog('CMCIC: Invoice with specified reference does not exist, email containing payment link has not been sent');
            	return $result;
            }
            else
            {
            	$result = $item->fetch_thirdparty($item->socid);
            }

			$alreadyPaid = $item->getSommePaiement();
			$creditnotes = $item->getSumCreditNotesUsed();
			$deposits = $item->getSumDepositsUsed();
			$totalInvoice = $item->total_ttc;

			$alreadyPaid = empty($alreadyPaid) ? 0 : $alreadyPaid;
			$creditnotes = empty($creditnotes) ? 0 : $creditnotes;
			$deposits = empty($deposits) ? 0 : $deposits;
			$totalInvoice = empty($totalInvoice) ? 0 : $totalInvoice;

			$amountTransaction =  $totalInvoice - ($alreadyPaid + $creditnotes + $deposits);

            $needPayment = ($item->statut == 1) ? true : false;

            // Do nothing if payment already completed
            if ($amountTransaction == 0 || !$needPayment){
                dol_syslog('CMCIC: Payment already done, email containing payment link has not been sent');
                return 0;
            }

            // Do nothing if payment is not CB
            // Get CB id
            $cbId = dol_getIdFromCode($this->db, 'CB','c_paiement');


            if ($item->mode_reglement_id != $cbId){
                dol_syslog('CMCIC: Invoice payment mode is not CB, can not send payment link email');
                return 0;
            }


            // Create URL
            $token = '';
            if (!empty($conf->global->CMCIC_SECURITY_TOKEN)){
              $token = '&token='.dol_hash($conf->global->CMCIC_SECURITY_TOKEN.$refDolibarr, 2);
            }

            $paymentLink = DOL_MAIN_URL_ROOT.'/public/cmcic/payment.php?ref='.$refDolibarr.$token;

            $substit = array(
                '__INVREF__' => $refDolibarr,
                '__PAYURL__' => $paymentLink,
                '__SOCNAM__' => $conf->global->MAIN_INFO_SOCIETE_NOM,
                '__SOCMAI__' => $conf->global->MAIN_INFO_SOCIETE_MAIL,
                '__CLINAM__' => $item->client->name,
                '__AMOINV__' => $amountTransaction
            );

            $sendtoid = $object->sendtoid;

            if ($sendtoid){
                $sendto = $item->client->contact_get_property($sendtoid, 'email');
            }else{
                $sendto = $item->client->email;
            }

            $from = $conf->global->MAIN_INFO_SOCIETE_MAIL;

            $subject = make_substitutions($langs->transnoentities('CMCIC_PAYMENT_EMAIL_SUBJECT_TEXT'), $substit);
            $message = make_substitutions($langs->transnoentities('CMCIC_PAYMENT_EMAIL_BODY_TEXT'), $substit);
            $message = str_replace('\n',"<br />", $message);

            $deliveryreceipt = $conf->global->CMCIC_DELIVERY_RECEIPT_EMAIL;
            $addr_cc = ($conf->global->CMCIC_CC_EMAIL ? $conf->global->MAIN_INFO_SOCIETE_MAIL: "");

            if (!empty($conf->global->CMCIC_CC_EMAILS)){
                $addr_cc.= (empty($addr_cc) ? $conf->global->CMCIC_CC_EMAILS : ','.$conf->global->CMCIC_CC_EMAILS);
            }

            $mail = new CMailFile($subject, $sendto, $from, $message, array(), array(), array(), $addr_cc, "", $deliveryreceipt, 1);
            $result = $mail->error;
            if (!$result)
            {
                $result = $mail->sendfile();
                if ($result){
                    dol_syslog('CMCIC: Email containing payment link has been correctly sent');
                }else{
                    dol_syslog('CMCIC: Error sending email containing payment link');
                }
                return $result;
            }
            else
            {
                dol_syslog('CMCIC: Error in creating email containing payment link');
                return $result;
            }

        }

		return 0;
    }

}
?>