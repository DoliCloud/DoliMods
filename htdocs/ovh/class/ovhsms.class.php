<?php
/* Copyright (C) 2007-2011 Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Jean-François FERRY <jfefe@aternatik.fr>
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
 *
 * https://www.ovh.com/fr/soapi-to-apiv6-migration/
 */

/**
 *      \file       ovh/class/ovhsms.class.php
 *      \ingroup    ovh
 *      \brief      This file allow to send sms with an OVH account
 */
require_once NUSOAP_PATH.'/nusoap.php';

require __DIR__ . '/../includes/autoload.php';
use \Ovh\Api;


/**
 *		Use an OVH account to send SMS with Dolibarr
 */
class OvhSms extends CommonObject
{
	public $db;							//!< To store db handler
	public $error;						//!< To return error code (or message)
	public $errors=array();				//!< To return several error codes (or messages)
	public $element='ovhsms';			//!< Id that identify managed object

	public $id;
	public $account;

	public $socid;
	public $contact_id;
	public $member_id;

	public $fk_project;

	public $nostop;
	public $expe;
	public $dest;
	public $message;
	public $validity;
	public $class;
	public $deferred;
	public $priority;
	public $deliveryreceipt;

	public $soap;         // Old API
	public $session;      // Old API
	public $conn;         // New API
	public $endpoint;


	/**
	 *	Constructor
	 *
	 * 	@param	DoliDB	$db		Database handler
	 */
	public function __construct($db)
	{
		global $conf;

		// CSMSFile calls the constructor with a null database handler
		if (!is_object($db)) {
			$db = $GLOBALS['db'];
		}
		$this->db = $db;

		// Réglages par défaut
		$this->validity = 24*60;  // 24 hours. the maximum time -in minute(s)- before the message is dropped, default is 48 hours
		$this->class = '2';       // the sms class: flash(0),phone display(1),SIM(2),toolkit(3)
		$this->deferred = '60';   // the time -in minute(s)- to wait before sending the message, default is 0
		$this->priority = '3';    // the priority of the message (0 to 3), default is 3
		// Set the WebService URL

		if (getDolGlobalString('OVH_OLDAPI')) {
			dol_syslog(get_class($this)."::OvhSms OVHSMS_SOAPURL=".getDolGlobalString('OVHSMS_SOAPURL'));

			if (getDolGlobalString('OVHSMS_SOAPURL')) {
				require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
				$params=getSoapParams();
				ini_set('default_socket_timeout', $params['response_timeout']);

				//if ($params['proxy_use']) print $langs->trans("TryToUseProxy").': '.$params['proxy_host'].':'.$params['proxy_port'].($params['proxy_login']?(' - '.$params['proxy_login'].':'.$params['proxy_password']):'').'<br>';
				//print 'URL: '.$WS_DOL_URL.'<br>';
				//print $langs->trans("ConnectionTimeout").': '.$params['connection_timeout'].'<br>';
				//print $langs->trans("ResponseTimeout").': '.$params['response_timeout'].'<br>';

				$err=error_reporting();
				error_reporting(E_ALL);     // Enable all errors

				try {
					$this->soap = new SoapClient(getDolGlobalString('OVHSMS_SOAPURL'), $params);

					$language = "en";
					$multisession = false;

					$this->session = $this->soap->login(getDolGlobalString('OVHSMS_NICK'), getDolGlobalString('OVHSMS_PASS'), $language, $multisession);
					//if ($this->session) print '<div class="ok">'.$langs->trans("OvhSmsLoginSuccessFull").'</div><br>';
					//else print '<div class="error">Error login did not return a session id</div><br>';
					$this->soapDebug();

					// We save known SMS account
					$this->account = (getDolGlobalString('OVHSMS_ACCOUNT') ? getDolGlobalString('OVHSMS_ACCOUNT') : 'ErrorNotDefined');

					return 1;
				} catch (SoapFault $se) {
					error_reporting($err);     // Restore default errors
					dol_syslog(get_class($this).'::SoapFault: '.$se, LOG_ERR);
					//var_dump('eeeeeeee');exit;
					return 0;
				} catch (Exception $ex) {
					error_reporting($err);     // Restore default errors
					dol_syslog(get_class($this).'::SoapFault: '.$ex, LOG_ERR);
					//var_dump('eeeeeeee');exit;
					return 0;
				} catch (Error $e) {
					error_reporting($err);     // Restore default errors
					dol_syslog(get_class($this).'::SoapFault: '.$e, LOG_ERR);
					//var_dump('eeeeeeee');exit;
					return 0;
				}
				error_reporting($err);     // Restore default errors

				return 1;
			} else return 0;
		} else {
			$endpoint = getDolGlobalString('OVH_ENDPOINT', 'ovh-eu');

			dol_syslog(get_class($this)."::OvhSms OVH_ENDPOINT=".$endpoint);

			$this->endpoint = $endpoint;

			require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
			$params=getSoapParams();
			ini_set('default_socket_timeout', $params['response_timeout']);

			try {
				// Get the factory to call API.
				// Array of endpoints is defined into ->$endpoints of Api. The $endpoint key will be used to find final URL.
				$this->conn = new Api(getDolGlobalString('OVHAPPKEY'), getDolGlobalString('OVHAPPSECRET'), $endpoint, getDolGlobalString('OVHCONSUMERKEY'));

				// We save known SMS account
				$this->account = (getDolGlobalString('OVHSMS_ACCOUNT') ? getDolGlobalString('OVHSMS_ACCOUNT') : 'ErrorNotDefined');
			} catch (Exception $e) {
				$this->error=$e->getMessage();
				setEventMessages($this->error, null, 'errors');
				return 0;
			}

			return 1;
		}
	}

	/**
	 * Logout
	 *
	 * @return	void
	 */
	public function logout()
	{
		if (getDolGlobalString('OVH_OLDAPI')) {
			$this->soap->logout($this->session);
		}
		return 1;
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps,PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Send SMS
	 *
	 * @return	int     <=0 if error, >0 if OK
	 */
	public function SmsSend()
	{
		// phpcs:enable
		global $db, $conf, $langs, $user;

		try {
			if (getDolGlobalString('OVH_OLDAPI')) {
				// print "$this->session, $this->account, $this->expe, $this->dest, $this->message, $this->validity, $this->class, $this->deferred, $this->priority";
				$resultsend = $this->soap->telephonySmsSend($this->session, $this->account, $this->expe, $this->dest, $this->message, $this->validity, $this->class, $this->deferred, $this->priority, 2, 'Dolibarr');
				$this->soapDebug();
				return $resultsend;
			} else {
				// Values come as int or as string from forms. Compare them as int because, since PHP 8, '' == 0 is false.
				$prioritylabels = array(0 => 'high', 1 => 'medium', 2 => 'low', 3 => 'veryLow');
				$priority = '';
				if (in_array($this->priority, $prioritylabels, true)) {
					$priority = $this->priority;
				} elseif (is_numeric($this->priority) && isset($prioritylabels[(int) $this->priority])) {
					$priority = $prioritylabels[(int) $this->priority];
				}

				$classlabels = array(0 => 'flash', 1 => 'phoneDisplay', 2 => 'sim', 3 => 'toolkit');
				$smsclass = '';
				if (in_array($this->class, $classlabels, true)) {
					$smsclass = $this->class;
				} elseif (is_numeric($this->class) && isset($classlabels[(int) $this->class])) {
					$smsclass = $classlabels[(int) $this->class];
				}

				// Several receivers can be provided, separated with a comma or a semicolon
				$receivers = array();
				$rejectedreceivers = array();
				foreach (preg_split('/[,;]+/', (string) $this->dest) as $number) {
					$formattednumber = self::formatPhoneNumber($number);
					if ($formattednumber !== '') {
						$receivers[] = $formattednumber;
					} elseif (trim($number) !== '') {
						$rejectedreceivers[] = trim($number);
					}
				}
				if (empty($receivers)) {
					$langs->load("ovh@ovh");
					$this->error = $langs->trans("OvhSmsInvalidReceivers", $this->dest);
					return -1;
				}

				$content = array(
					"differedPeriod" => max(0, (int) $this->deferred),  // time in minutes
					"charset"=> "UTF-8",
					"coding"=> "7bit",
					"message"=> $this->message.($this->nostop?'':"\n"),
					"noStopClause"=> $this->nostop?true:false,
					"receivers"=> $receivers,   // [ "+3360000000" ]
					"sender"=> $this->expe,
					"senderForResponse"=> false,
					"validityPeriod"=> (int) $this->validity    // 28800
				);
				// When not provided, OVH uses its default class and priority
				if ($smsclass) {
					$content["class"] = $smsclass;
				}
				if ($priority) {
					$content["priority"] = $priority;
				}
				$content = (object) $content;
				//var_dump($content);exit;
				try {
					//var_dump($content);
					$relurl = '/sms/'. $this->account . '/jobs/';

					$resultPostJob = $this->conn->post($relurl, $content);
					/* Example of result:
					$resultPostJob = array(
						[totalCreditsRemoved] => 1
						[invalidReceivers] => array()
						[ids] => array(
							[0] => 26929925
							)
						[validReceivers] => array(
							[0] => +3366204XXXX
							)
					); */
					//var_dump($resultPostJob);
					$invalidreceivers = (isset($resultPostJob['invalidReceivers']) && is_array($resultPostJob['invalidReceivers'])) ? $resultPostJob['invalidReceivers'] : array();
					$invalidreceivers = array_merge($rejectedreceivers, $invalidreceivers);
					if (!empty($invalidreceivers)) {
						$langs->load("ovh@ovh");
						dol_syslog(get_class($this)."::SmsSend invalid receivers ".implode(', ', $invalidreceivers), LOG_WARNING);
					}
					if (isset($resultPostJob['totalCreditsRemoved']) && $resultPostJob['totalCreditsRemoved'] > 0) {
						if (!empty($invalidreceivers)) {	// Sent to part of the receivers only
							$this->errors[] = $langs->trans("OvhSmsInvalidReceivers", implode(', ', $invalidreceivers));
						}
						$validreceivers = (isset($resultPostJob['validReceivers']) && is_array($resultPostJob['validReceivers'])) ? $resultPostJob['validReceivers'] : $receivers;

						$object = new stdClass();
						$object->id = 0;
						$object->element = '';
						$triggersendname = 'SENTBYSMS';
						if ($this->member_id > 0) {
							$triggersendname = 'MEMBER_SENTBYSMS';
							//$conf->global->MAIN_AGENDA_ACTIONAUTO_MEMBER_SENTBYSMS should be set from agenda setup
							$object->id = $this->member_id;
							$object->element = 'member';
						} elseif ($this->socid > 0) {
							$triggersendname = 'COMPANY_SENTBYSMS';
							//$conf->global->MAIN_AGENDA_ACTIONAUTO_COMPANY_SENTBYSMS should be set from agenda setup
							$object->id = $this->socid;
							$object->element = 'societe';
						}

						// Force automatic event to ON for the generic trigger name
						if (!isset($conf->global->MAIN_AGENDA_ACTIONAUTO_SENTBYSMS)) {
							$conf->global->MAIN_AGENDA_ACTIONAUTO_SENTBYSMS = 1;	// Make trigger on
						}

						// Initialisation of datas of object to call trigger
						if (is_object($object)) {
							$langs->load("agenda");
							$langs->load("other");

							$actiontypecode='AC_OTH_AUTO'; // Event insert into agenda automatically

							$object->socid			= $this->socid;	   		// To link to a company
							$object->contact_id     = $this->contact_id;
							$object->fk_adherent    = $this->member_id;
							$object->fk_project     = $this->fk_project;
							$object->sendtoid		= ($this->contact_id > 0 ? array($this->contact_id) : array());	   // To link to contacts/addresses. This is an array.
							$object->context		= array();

							// The agenda trigger stores these texts into database, so they must not be HTML encoded
							$object->actiontypecode	= $actiontypecode; // Type of event ('AC_OTH', 'AC_OTH_AUTO', 'AC_XXX'...)
							$object->actionmsg2		= $langs->transnoentities("SMSSentTo", implode(', ', $validreceivers));
							$object->actionmsg		= $langs->transnoentities("SMSSentTo", implode(', ', $validreceivers))."\n".$this->message.($this->nostop?'':"\n");
							//$object->trackid        = $trackid;
							//$object->fk_element		= $object->id;
							//$object->elementtype	= $object->element;
							//$object->attachedfiles	= null;

							// Call of triggers
							if (! empty($triggersendname)) {
								include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
								$interface=new Interfaces($db);
								$result=$interface->run_triggers($triggersendname, $object, $user, $langs, $conf);
								if ($result < 0) {
									setEventMessages($interface->error, $interface->errors, 'errors');
								}
							}
						}

						return 1;
					} elseif (!empty($invalidreceivers)) {
						$this->error = $langs->trans("OvhSmsInvalidReceivers", implode(', ', $invalidreceivers));
						return -1;
					} else {
						$this->error = 'resultPostJob["totalCreditsRemoved"] not set. '.var_export($resultPostJob, true);
						return -1;
					}
				} catch (Exception $e) {
					$this->error = $e->getMessage();
					return -2;
				}
			}
		} catch (SoapFault $fault) {
			$errmsg="Error ".$fault->faultstring;
			dol_syslog(get_class($this)."::SmsSend ".$errmsg, LOG_ERR);
			$this->error .= ($this->error?', '.$errmsg:$errmsg);
			return -3;
		} catch (Exception $e) {
			$errmsg="Error ".$e->getMessage();
			dol_syslog(get_class($this)."::SmsSend ".$errmsg, LOG_ERR);
			$this->error .= ($this->error?', '.$errmsg:$errmsg);
			return -4;
		}

		return -5;
	}

	/**
	 * Clean a phone number and convert it into the international format expected by OVH.
	 * A national number is converted only for France (+33), other ones must already be international.
	 *
	 * @param	string	$phone			Phone number (example: '06 12 34 56 78', '0033612345678', '+33612345678')
	 * @param	string	$countrycode	Country code used for national numbers ('' = country of company)
	 * @return	string					Phone number (example: '+33612345678'), '' if empty
	 */
	public static function formatPhoneNumber($phone, $countrycode = '')
	{
		global $mysoc;

		if (empty($countrycode)) {
			$countrycode = (is_object($mysoc) && !empty($mysoc->country_code)) ? $mysoc->country_code : 'FR';
		}

		$number = preg_replace('/[^0-9+]/', '', (string) $phone);
		if (strpos($number, '00') === 0) {
			return '+'.substr($number, 2);
		}
		if (strpos($number, '+') === 0) {
			return $number;
		}
		if ($countrycode == 'FR' && preg_match('/^0([1-9]\d{8})$/', $number, $reg)) {
			return '+33'.$reg[1];
		}
		return $number;
	}

	/**
	 * Show HTML select box to select account
	 *
	 * @return	void
	 */
	public function printListAccount()
	{
		$resultaccount = $this->getSmsListAccount();
		print '<select name="ovh_account" id="ovh_account">';
		foreach ($resultaccount as $accountlisted) {
			print '<option value="'.$accountlisted.'">'.$accountlisted.'</option>';
		}
		print '</select>';
	}

	/**
	 * Return list of SMSAccounts
	 *
	 * @return	array
	 */
	public function getSmsListAccount()
	{
		try {
			if (getDolGlobalString('OVH_OLDAPI')) {
				$returnList = $this->soap->telephonySmsAccountList($this->session);
				$this->soapDebug();
				return $returnList;
			} else {
				$resultinfo = $this->conn->get('/sms');
				$resultinfo = json_decode(json_encode($resultinfo), true);
				return $resultinfo;
			}
		} catch (SoapFault $fault) {
			$errmsg="Error ".$fault->faultstring;
			dol_syslog(get_class($this)."::getSmsListAccount ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -1;
		} catch (Exception $e) {
			$errmsg="Error ".$e->getMessage();
			dol_syslog(get_class($this)."::getSmsListAccount ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -2;
		}
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps,PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Return Credit
	 *
	 * @return	array
	 */
	public function CreditLeft()
	{
		// phpcs:enable
		try {
			if (getDolGlobalString('OVH_OLDAPI')) {
				$returnList = $this->soap->telephonySmsCreditLeft($this->session, $this->account);
				$this->soapDebug();
				return $returnList;
			} else {
				//var_dump($this->conn);
				$resultinfo = $this->conn->get('/sms/'.$this->account);
				$resultinfo = json_decode(json_encode($resultinfo), false);
				return $resultinfo->creditsLeft;
			}
		} catch (SoapFault $fault) {
			$errmsg="Error ".$fault->faultstring;
			dol_syslog(get_class($this)."::CreditLeft ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -1;
		} catch (Exception $e) {
			$errmsg="Error ".$e->getMessage();
			dol_syslog(get_class($this)."::CreditLeft ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -1;
		}
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps,PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Return History
	 *
	 * @return	array
	 */
	public function SmsHistory()
	{
		// phpcs:enable
		try {
			if (getDolGlobalString('OVH_OLDAPI')) {
				$returnList = $this->soap->telephonySmsHistory($this->session, $this->account, "");
				$this->soapDebug();
				return $returnList;
			} else {
				$resultinfo = $this->conn->get('/sms/'.$this->account.'/outgoing');
				$resultinfo = json_decode(json_encode($resultinfo), true);
				return $resultinfo;
			}
		} catch (SoapFault $fault) {
			$errmsg="Error ".$fault->faultstring;
			dol_syslog(get_class($this)."::SmsHistory ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -1;
		} catch (Exception $e) {
			$errmsg="Error ".$e->getMessage();
			dol_syslog(get_class($this)."::SmsHistory ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -1;
		}
		return -1;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps,PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Return list of possible SMS senders
	 *
	 * @return array|int	                    <0 if KO, array with list of available senders if OK
	 */
	public function SmsSenderList()
	{
		// phpcs:enable
		try {
			if (getDolGlobalString('OVH_OLDAPI')) {
				$telephonySmsSenderList = $this->soap->telephonySmsSenderList($this->session, $this->account);
				$this->soapDebug();
				return $telephonySmsSenderList;
			} else {
				$resultinfo = $this->conn->get('/sms/'.$this->account.'/senders');
				//var_dump($resultinfo);
				$i=0;
				$senderlist=array();
				foreach ($resultinfo as $val) {
					$senderlist[$i] = new stdClass();
					$senderlist[$i]->number = $val;
					$i++;
				}
				return $senderlist;
			}
		} catch (SoapFault $fault) {
			$errmsg="Error ".$fault->faultstring;
			dol_syslog(get_class($this)."::SmsSenderList ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -1;
		} catch (Exception $e) {
			$errmsg="Error ".$e->getMessage();
			dol_syslog(get_class($this)."::SmsSenderList ".$errmsg, LOG_ERR);
			$this->error.=($this->error?', '.$errmsg:$errmsg);
			return -1;
		}
		return -1;
	}


	/**
	 * Call soapDebug method to output traces
	 *
	 * @return	void
	 */
	public function soapDebug()
	{
		if (method_exists($this->soap, '__getLastRequestHeaders')) dol_syslog(get_class($this).'::OvhSms REQUEST HEADER: ' . $this->soap->__getLastRequestHeaders(), LOG_DEBUG, 0, '_ovhsms');
		if (method_exists($this->soap, '__getLastRequest')) dol_syslog(get_class($this).'::OvhSms REQUEST: ' . $this->soap->__getLastRequest(), LOG_DEBUG, 0, '_ovhsms');

		if (method_exists($this->soap, '__getLastResponseHeaders')) dol_syslog(get_class($this).'::OvhSms RESPONSE HEADER: ' . $this->soap->__getLastResponseHeaders(), LOG_DEBUG, 0, '_ovhsms');
		if (method_exists($this->soap, '__getLastResponse')) dol_syslog(get_class($this).'::OvhSms RESPONSE: ' . $this->soap->__getLastResponse(), LOG_DEBUG, 0, '_ovhsms');
	}
}
