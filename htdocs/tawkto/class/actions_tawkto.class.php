<?php
class ActionsTawkto
{
	function printLeftBlock()
	{
	    global $user, $conf;

		$userIdentity = $user->firstname.' '.$user->lastname;
		$userEmail = empty($user->email) ? $conf->global->MAIN_INFO_SOCIETE_MAIL : $user->email;

		$filewithchatcode = dol_buildpath('/tawkto/includes/chatscript.php');

		$htmlChatScript = file_get_contents($filewithchatcode);

		// TODO
		$idsitetawkto = '59e0d01e4854b82732ff55e2';

		$this->resprints = strtr($htmlChatScript, array(
		       '{USER_NAME}' => $userIdentity,
		       '{USER_EMAIL}' => $userEmail,
		       '{HASH}' => hash_hmac("sha256", $userEmail, "dolibarr"),

		       '{USER_OFFICE_PHONE}' => $user->office_phone,
		       '{USER_MOBILE_PHONE}' => $user->user_mobile,

		       '{COMPANY_NAME}' => $conf->global->MAIN_INFO_SOCIETE_NOM,
		       '{COMPANY_EMAIL}' => $conf->global->MAIN_INFO_SOCIETE_MAIL,
		       '{COMPANY_PHONE}' => $conf->global->MAIN_INFO_SOCIETE_TEL,
		       '{SIREN}' => $conf->global->MAIN_INFO_SIREN,

		       '{DOLIBARR_VERSION}' => DOL_VERSION,
			   '{IDTAWKTO}' => $idsitetawkto
		       ));

		 return 1;
	}
}