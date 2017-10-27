<?php
class ActionsTawkto
{

	/**
	 * 	Show entity info
	 */
	function printTopRightMenu()
	{
		global $conf,$user,$langs;

		$out='';

		/*$form=new Form($this->db);

		$text = img_picto('', 'object_multicompany@multicompany','id="switchentity" class="entity linkobject"');

		$htmltext ='aaa';

		$out.= $form->textwithtooltip('',$htmltext,2,1,$text,'login_block_elem',2);*/

		$out.= '<div class="inline-block"><div class="classfortooltip inline-block login_block_elem inline-block" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="xxx">';
		$out.= '<a href="'.$_SERVER['PHP_SELF'].'?tawktotoggle=1"><span class="fa fa-comments atoplogin"></span></a>';
		$out.= '</div></div>'."\n";

		if (GETPOST('tawktotoggle','int'))
		{
			if (empty($_SESSION['tawktoonoff'])) $_SESSION['tawktoonoff']='on';
			else unset($_SESSION['tawktoonoff']);
		}

		$this->resprints = $out;

		return 0;
	}



	/**
	 * @return number
	 */
	function printLeftBlock()
	{
	    global $user, $conf;

		if (empty($_SESSION['tawktoonoff'])) return 0;

		$userIdentity = $user->firstname.' '.$user->lastname;
		$userEmail = empty($user->email) ? $conf->global->MAIN_INFO_SOCIETE_MAIL : $user->email;

		$filewithchatcode = dol_buildpath('/tawkto/includes/chatscript.php');

		$htmlChatScript = file_get_contents($filewithchatcode);

		$idsitetawkto = $conf->global->TAWKTO_ID;
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

		 return 0;
	}
}