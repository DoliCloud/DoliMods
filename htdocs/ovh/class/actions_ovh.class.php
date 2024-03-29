<?php

/**
 * \file    ovh/class/actions_mymodule.class.php
 * \ingroup ovh
 *
 */

/**
 * Class ActionsOVH
 */
class ActionsOVH
{
	/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param array         $parameters     Hook metadatas (context, etc...)
	 * @param CommonObject    $object The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param string          $action Current action (if set). Generally create or edit or null
	 * @param HookManager $hookmanager Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		global $langs, $conf;
		$langs->load('ovh@ovh');
		$langs->load("sms");
		$massaction = GETPOST('massaction');

		if (in_array('contactlist', explode(':', $parameters['context'])) && getDolGlobalString('OVHSMS_ACCOUNT')) {
			$labelAction = img_picto('', 'fontawesome_sms', '') . $langs->trans("SendSMS");
			$this->resprints = '<option value="presendsms" data-html="' . dol_escape_htmltag($labelAction) . '"';

			if ($massaction == 'presendsms') {
				$this->resprints .= 'selected';
			}

			$this->resprints .= '>' . $labelAction . ' </option>';
		}

		if (!$error) {
			//$this->results = array('myreturn' => $myvalue);
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}

	/**
	 * Overloading the doPreMassActions function : replacing the parent's function with the one below
	 *
	 * @param array         $parameters     Hook metadatas (context, etc...)
	 * @param CommonObject    $object The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param string          $action Current action (if set). Generally create or edit or null
	 * @param HookManager $hookmanager Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doPreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		global $langs, $db, $user;
		$langs->load('ovh@ovh');
		$langs->load("sms");
		$massaction = GETPOST('massaction');

		if (in_array('contactlist', explode(':', $parameters['context']))) {
			if ($massaction == 'presendsms' && !GETPOST('cancel', 'aZ09')) {
				include_once DOL_DOCUMENT_ROOT . '/core/class/html.formsms.class.php';


				$receivers = $this->getReceivers($parameters['toselect']);

				$receivers_string ='';
				if (!empty($receivers) && !empty($receivers['phone_numbers'])) {
					$receivers_string = implode(', ', $receivers['phone_numbers']);
				}
				if (!empty($receivers_string)) {
					print dol_get_fiche_head();

                    $formsms = new FormSms($db);
                    $formsms->fromtype = 'user';
                    $formsms->fromid = $user->id;
                    $formsms->fromsms = $user->user_mobile;
                    $formsms->withfrom = 1;
                    $formsms->withfromreadonly = 0;
                    $formsms->withto = $receivers_string;
                    $formsms->withtoreadonly = 1;
                    $formsms->withbody = 1;
                    $formsms->withcancel = 0;
                    // Tableau des substitutions
                    $formsms->substit['__CONTACTREF__'] = $object->ref;
                    // Tableau des parametres complementaires du post
                    $formsms->param['action'] = 'send';
                    $formsms->param['models'] = '';
                    $formsms->param['id'] = $object->id;
                    $formsms->param['returnurl'] = $_SERVER["PHP_SELF"] . '?id=' . $object->id;

                    $formsms->show_form('', 0);

                    print '<br>';
                    print '<div class="center">';
                    print '<input class="button" type="submit" name="sendsms" value="' . dol_escape_htmltag($langs->trans("SendSms")) . '">';
                    print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    print '<input class="button" type="submit" name="cancel" value="' . dol_escape_htmltag($langs->trans("Cancel")) . '">';
                    print '</div>';

                    print dol_get_fiche_end();
				} else {
					setEventMessage($langs->trans('NoneValidPhoneNumbers'));
				}
			}
		}

		if (!$error) {
			//$this->results = array('myreturn' => $myvalue);
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}

	/**
	 * Overloading the doMassAction function : replacing the parent's function with the one below
	 *
	 * @param array         $parameters     Hook metadatas (context, etc...)
	 * @param CommonObject    $object The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param string          $action Current action (if set). Generally create or edit or null
	 * @param HookManager $hookmanager Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $user, $db, $langs;

		if (GETPOST('sendsms') && empty(GETPOST('cancel', 'aZ09'))) {
			$error = 0;
			$langs->load('ovh@ovh');
			$langs->load("sms");
			$smsfrom = '';

			if (!empty($_POST["fromsms"])) {
				$smsfrom = GETPOST("fromsms");
			}

			if (empty($smsfrom)) {
				$smsfrom = GETPOST("fromname");
			}

			$sendto = $this->getReceivers($parameters['toselect']);
			$receiver = 'contact';
			$body = GETPOST('message');
			$deliveryreceipt = GETPOST("deliveryreceipt");
			$deferred = GETPOST('deferred');
			$priority = GETPOST('priority');
			$class = GETPOST('class');
			$errors_to = GETPOST("errorstosms");

			// Test param
			if (empty($body)) {
				setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentities("Message")), 'errors');
				$action = 'test';
				$error++;
			}

			if (empty($smsfrom) || !str_replace('+', '', $smsfrom)) {
				setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentities("SmsFrom")), 'errors');
				$action = 'test';
				$error++;
			}

			if (empty($sendto) && empty($sendto['phone_numbers'])) {
				setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentities("SmsTo")), 'errors');
				$action = 'test';
				$error++;
			}

			$sendtonumber = implode(', ', $sendto['phone_numbers']);

			if (!$error) {
				// Make substitutions into message
				$substitutionarrayfortest = array();
				complete_substitutions_array($substitutionarrayfortest, $langs);
				$body = make_substitutions($body, $substitutionarrayfortest);

				require_once DOL_DOCUMENT_ROOT . "/core/class/CSMSFile.class.php";

				//if (empty($sendcontext)) $sendcontext = 'standard';
				$smsfile = new CSMSFile($sendtonumber, $smsfrom, $body, $deliveryreceipt, $deferred, $priority, $class);  // This define OvhSms->login, pass, session and account

				$smsfile->nostop = GETPOST('disablestop');
				$smsfile->socid = 0;
				$smsfile->contactid = 0;
				$smsfile->contact_id = 0;
				$smsfile->fk_project = 0;

				// Send the SMS
				$result = $smsfile->sendfile(); // This send SMS. It also includes run of triggers 'SENTBYSMS'.

				if ($result > 0) {
					setEventMessages($langs->trans("SmsSuccessfulySent", $smsfrom, $sendtonumber), null);

					//Create manually event because trigger cannot be run as we end 1 SMS to API instead of one SMS per contact
					foreach ($sendto['contacts'] as $contact) {
						require_once DOL_DOCUMENT_ROOT . '/comm/action/class/actioncomm.class.php';
						$now = dol_now();
						$actioncomm = new ActionComm($db);

						$actioncomm->elementtype = 'sms@ovh';

						$actioncomm->code = 'AC_SENTBYSMS';
						$actioncomm->type_code = 'AC_OTH_AUTO';
						$actioncomm->label = $langs->trans("SMSSentTo", $sendto['contact_phone_numbers'][$contact->id]);
						$actioncomm->datep = $now;
						$actioncomm->socpeopleassigned = array($contact->id=>null);
						$actioncomm->socid = $contact->socid;
						$actioncomm->userownerid = $user->id;
						$actioncomm->percentage = -1;
						$actioncomm->note_private = $body ;

						$result = $actioncomm->create($user);
						if ($result < 0) {
							setEventMessages($actioncomm->error, $actioncomm->errors, 'errors');
						}
					}
				} else {
					setEventMessages($langs->trans("ResultKo") . ' (sms from' . $smsfrom . ' to ' . $sendto . ')<br>' . $smsfile->error, null, 'errors');
				}

				$action = '';
			}
		}

		return 0;
	}

	/**
	 * Return Array of contact
	 * @param array $selected array of selected contacts ids
	 * @return array
	 */
	private function getSelectedContacts($selected)
	{
		global $db;
		$listofselectedcontacts = array();

		foreach ($selected as $toselectid) {
			$contact = new Contact($db);
			$result = $contact->fetch($toselectid);
			if ($result > 0) {
				$listofselectedcontacts[] = $contact;
			}
		}

		return $listofselectedcontacts;
	}

	/**
	 * Return Array (phone_numbers,contact_phone_numbers,contacts) of valid contacts for send SMS
	 * @param array $selected array of selected contacts ids
	 * @return array
	 */
	private function getReceivers($selected)
	{
		global $langs;
		$sendto = array();
		$sendto['phone_numbers'] = array();
		$sendto['contact_phone_numbers'] = array();
		$sendto['contacts'] = array();

		$listofselectedcontacts = $this->getSelectedContacts($selected);

		foreach ($listofselectedcontacts as $contact) {
			$mobile_phone = preg_replace("/[^0-9+]/", "", $contact->phone_mobile);

			if ($mobile_phone[0] == '+' && strlen($mobile_phone) == 12) {
				$international_mobile_phone = $mobile_phone;
			} elseif (strlen($mobile_phone) == 10) {
				$international_mobile_phone = substr($mobile_phone, 1);
				$international_mobile_phone = '+33' . $international_mobile_phone;
			} else {
				setEventMessage($langs->trans('InvalidMobilePhoneNumberForContact', $contact->getFullName($langs), $mobile_phone), 'warnings');
				continue;
			}

			$sendto['phone_numbers'][] = $international_mobile_phone;
			$sendto['contact_phone_numbers'][$contact->id] = $international_mobile_phone;
			$sendto['contacts'][$contact->id] = $contact;
		}

		return $sendto;
	}
}
