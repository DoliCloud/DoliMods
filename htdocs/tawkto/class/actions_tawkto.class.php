<?php
class ActionsTawkto
{
	/**
	 * 	set CSP
	 *
	 * @param	array		$parameters		Array of parameters
	 * @param	Object		$object			Object
	 * @param	string		$action			Action string
	 * @param	HookManager	$hookmanager	Object HookManager
	 */
	function setContentSecurityPolicy($parameters, &$object, &$action, &$hookmanager)
	{
		global $conf,$user,$langs;

		$tmp = ($hookmanager->resPrint ? $hookmanager->resPrint : $parameters['contentsecuritypolicy']);

		$tmp = preg_replace('/script-src \'self\'/', 'script-src \'self\' *.tawk.to *.jsdelivr.net', $tmp);
		$tmp = preg_replace('/font-src \'self\'/', 'font-src \'self\' *.tawk.to', $tmp);
		$tmp = preg_replace('/connect-src \'self\'/', 'connect-src \'self\' *.tawk.to wss:', $tmp);
		$tmp = preg_replace('/frame-src \'self\'/', 'frame-src \'self\' *.tawk.to', $tmp);

		$hookmanager->resPrint = '';

		$this->resprints = $tmp;
		return 1;
	}

	/**
	 * 	Show entity info
	 */
	function printTopRightMenu()
	{
		global $conf,$user,$langs;

		$langs->load("tawkto@tawkto");

		$out='';

		if (GETPOST('tawktotoggle', 'int')) {
			if (empty($_SESSION['tawktoonoff'])) $_SESSION['tawktoonoff']='on';
			else unset($_SESSION['tawktoonoff']);
		}

		$fontas='fa-comment-o';
		$tooltiptext = $langs->trans("ClickToOpenChat");
		if (! empty($_SESSION['tawktoonoff'])) {
			$fontas='fa-commenting-o';
			$tooltiptext = $langs->trans("ClickToCloseChat");
		}

		$out.= '<div class="inline-block"><div class="classfortooltip inline-block login_block_elem inline-block" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="'.dol_escape_htmltag($tooltiptext).'">';
		$param = '';
		if (! empty($_SERVER["QUERY_STRING"])) $param = preg_replace('/&?tawktotoggle=\d/', '', $_SERVER["QUERY_STRING"]);
		$out.= '<a href="'.$_SERVER['PHP_SELF'].'?'.($param?$param.'&':'').'tawktotoggle=1"><span class="fa '.$fontas.' atoplogin"></span></a>';
		$out.= '</div></div>';	// Do not ouptu "\n" here, it create a space into toolbar

		$this->resprints = $out;

		return 0;
	}



	/**
	 * @return number
	 */
	function printLeftBlock()
	{
		global $user, $conf, $langs;

		// Get TawkTo ID
		$idsitetawkto = $conf->global->TAWKTO_ID;
		if (empty($idsitetawkto)) {
			if (! preg_match('/tawkto\/admin/', $_SERVER["PHP_SELF"])) {
				$langs->load("tawkto@tawkto");
				setEventMessages($langs->trans("TawkToModuleEnabledWithoutSetup"), null, 'warnings');
			}
		}

		// Return if chat not enabled
		if (empty($_SESSION['tawktoonoff'])) return 0;


		$userIdentity = $user->firstname.' '.$user->lastname;
		$userEmail = empty($user->email) ? $conf->global->MAIN_INFO_SOCIETE_MAIL : $user->email;

		$htmlChatScript = "
			<!-- Start of Tawk.to Script -->
			<!-- Even if this code is available into page, the widget may be visible only if service is online, depending on widget setup -->
			<script type='text/javascript'>
			var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
			Tawk_API.onLoad = function(){
			    Tawk_API.setAttributes({
			        'name'  : '{USER_NAME}',
			        'email' : '{USER_EMAIL}',
			        'hash'  : '{HASH}',

			        'User Phone 1' : '{USER_OFFICE_PHONE}',
			        'User Phone 2' : '{USER_MOBILE_PHONE}',

			        'Company' : '{COMPANY_NAME}',
			        'SIREN' : '{SIREN}',
			        'Company Phone' : '{COMPANY_PHONE}',
			        'Company Email' : '{COMPANY_EMAIL}',

			        'Dolibarr Version' : '{DOLIBARR_VERSION}',
			    }, function(error){});
			}

			Tawk_API.visitor = {

			};

			(function(){
			var s1=document.createElement('script'),s0=document.getElementsByTagName('script')[0];
			s1.async=true;
			s1.src='https://embed.tawk.to/{IDTAWKTO}/{IDWIDGET}';
			s1.charset='UTF-8';
			s1.setAttribute('crossorigin','*');
			s0.parentNode.insertBefore(s1,s0);
			})();
			</script>
			<!--End of Tawk.to Script-->
			";


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
			'{IDTAWKTO}' => $idsitetawkto,
			'{IDWIDGET}' => (empty($conf->global->TAWKTO_WIDGETID) ? 'default' : $conf->global->TAWKTO_WIDGETID)
		));

		return 0;
	}
}
