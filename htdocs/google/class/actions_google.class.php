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
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";


/**
 *	Class to manage hooks for module Google
 */
class ActionsGoogle
{
	public $db;
	public $error;
	public $errors=array();
	public $priority = 70;

	/**
	 * @var string	String of results.
	 */
	public $resprints;

	/**
	 * @var array 	Array of results.
	 */
	public $results = array();


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
	 * 	set CSP
	 *
	 * @param	array		$parameters		Array of parameters
	 * @param	Object		$object			Object
	 * @param	string		$action			Action string
	 * @param	HookManager	$hookmanager	Object HookManager
	 */
	function setContentSecurityPolicy($parameters, &$object, &$action, &$hookmanager)
	{
		$tmp = ($hookmanager->resPrint ? $hookmanager->resPrint : $parameters['contentsecuritypolicy']);

		// Add google to Content-Security-Policy
		$tmp = preg_replace('/script-src \'self\'/', 'script-src \'self\' *.googleapis.com *.google.com *.google-analytics.com', $tmp);
		$tmp = preg_replace('/font-src \'self\'/', 'font-src \'self\' *.google.com', $tmp);
		$tmp = preg_replace('/connect-src \'self\'/', 'connect-src \'self\' *.google.com', $tmp);
		$tmp = preg_replace('/frame-src \'self\'/', 'frame-src \'self\' *.google.com', $tmp);

		$hookmanager->resPrint = '';

		$this->resprints = $tmp;

		return 1;
	}

	/**
	 * addCalendarChoice
	 *
	 * @param	array		$parameters		Array of parameters
	 * @param	Object		$object			Object
	 * @param	string		$action			Action string
	 * @param	HookManager	$hookmanager	Object HookManager
	 * @return	int							0=OK
	 */
	function addCalendarChoice($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $langs, $user;
		global $form;

		$error = 0;

		if (isModEnabled('google')) {
			if (getDolGlobalString('GOOGLE_DUPLICATE_INTO_GCAL')) {
				// Define $max, $maxgoogle and $notolderforsync
				$max = getDolGlobalInt('GOOGLE_MAX_FOR_MASS_AGENDA_SYNC', 50);
				$maxgoogle=2500;
				$notolderforsync = getDolGlobalInt('GOOGLE_MAXOLDDAYS_FOR_MASS_AGENDA_SYNC', 20);   // nb days max
				$testoffset=3600;

				$fuser = $user;
				$now = dol_now();

				$userlogin = getDolGlobalString('GOOGLE_LOGIN');
				if (empty($userlogin)) {
					$userlogin = empty($fuser->conf->GOOGLE_LOGIN)?'':$fuser->conf->GOOGLE_LOGIN;
				}

				$keyparam = 'GOOGLE_AGENDA_LASTSYNC_'.$userlogin;
				$valparam = getDolGlobalString($keyparam);
				if ($valparam) $dateminsync=dol_stringtotime($valparam, 1);
				if (empty($dateminsync) || $dateminsync < ($now - ($notolderforsync * 24 * 3600))) $dateminsync=($now - ($notolderforsync * 24 * 3600));
				$dateminsync = strtotime('-1 day', $dateminsync);

				$actiongoogle = GETPOST('actiongoogle');

				$_SERVER['QUERY_STRING'] = preg_replace('/&*actiongoogle=refresh/', '', $_SERVER['QUERY_STRING']);


				// Action sync
				if ($actiongoogle == 'refresh') {
					dol_include_once("/google/lib/google.lib.php");
					dol_include_once('/google/lib/google_calendar.lib.php');

					if (! $error) {
						$resarray = syncEventsFromGoogleCalendar($userlogin, $user, $dateminsync, $max);

						$errors=$resarray['errors'];
						$nbinserted=$resarray['nbinserted'];
						$nbupdated=$resarray['nbupdated'];
						$nbdeleted=$resarray['nbdeleted'];
						$nbalreadydeleted=$resarray['nbalreadydeleted'];

						if (! empty($errors)) {
							setEventMessage($errors, 'errors');
						} else {
							$langs->load("google@google");
							setEventMessage($langs->trans("GetFromGoogleSucess", ($nbinserted ? $nbinserted : '0'), ($nbupdated ? $nbupdated : '0'), ($nbdeleted ? $nbdeleted : '0')), 'mesgs');
							if ($nbalreadydeleted) setEventMessage($langs->trans("GetFromGoogleAlreadyDeleted", $nbalreadydeleted), 'mesgs');

							include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
							dolibarr_set_const($this->db, $keyparam, dol_print_date(dol_now('gmt'), 'dayhourrfc', 'gmt'), 'chaine', 1, '', $conf->entity);
							$valparam=$conf->global->$keyparam;
							$dateminsync=dol_stringtotime($valparam, 1);
							//var_dump($keyparam);exit;
						}
					}
				}

				// HTML output to show into agenda views
				$langs->load("google@google");

				$this->resprints = ' &nbsp; <div class="googlerefreshcal inline-block">';
				$this->resprints.= '<a href="'.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'&actiongoogle=refresh">';
				$tooltip = $langs->trans("ClickToUpdateWithLastGoogleChanges", $userlogin);
				$tooltip .= ' '.dol_print_date($dateminsync, 'dayhour', 'tzserver', $langs);
				$tooltip .= '<br>'.$langs->trans("GoogleLimitBackTime", $notolderforsync);
				$this->resprints.= $form->textwithpicto($langs->trans("RefreshEventFromGoogle"), $tooltip);
				$this->resprints.= '</a></div>';
			}
		}

		return 0;
	}


	/**
	 * getLoginPageExtraContent
	 *
	 * @param	array		$parameters		Array of parameters
	 * @param	Object		$object			Object
	 * @param	string		$action			Action string
	 * @param	HookManager	$hookmanager	Object HookManager
	 * @return	int							0=OK
	 */
	function getLoginPageExtraContent($parameters, &$object, &$action, $hookmanager)
	{
		return $this->getXXXPageExtraContent($parameters, $object, $action, $hookmanager);
	}

	/**
	 * getPasswordForgottenPageExtraContent
	 *
	 * @param	array		$parameters		Array of parameters
	 * @param	Object		$object			Object
	 * @param	string		$action			Action string
	 * @param	HookManager	$hookmanager	Object HookManager
	 * @return	int							0=OK
	 */
	function getPasswordForgottenPageExtraContent($parameters, &$object, &$action, $hookmanager)
	{
		return $this->getXXXPageExtraContent($parameters, $object, $action, $hookmanager);
	}

	/**
	 * getPasswordResetExtraContent
	 *
	 * @param	array		$parameters		Array of parameters
	 * @param	Object		$object			Object
	 * @param	string		$action			Action string
	 * @param	HookManager	$hookmanager	Object HookManager
	 * @return	int							0=OK
	 */
	function getPasswordResetExtraContent($parameters, &$object, &$action, $hookmanager)
	{
		return $this->getXXXPageExtraContent($parameters, $object, $action, $hookmanager);
	}

	/**
	 * getLoginPageExtraContent
	 *
	 * @param	array		$parameters		Array of parameters
	 * @param	Object		$object			Object
	 * @param	string		$action			Action string
	 * @param	HookManager	$hookmanager	Object HookManager
	 * @return	int							0=OK
	 */
	private function getXXXPageExtraContent($parameters, &$object, &$action, $hookmanager)
	{
		global $conf;

		$out = '';

		 // Google Analytics
		 if (isModEnabled('google') && getDolGlobalString('MAIN_GOOGLE_AN_ID')) {
			 $tmptagarray = explode(',', getDolGlobalString('MAIN_GOOGLE_AN_ID'));
			 foreach ($tmptagarray as $tmptag) {
		 		$out .= "\n";
		 		$out .= "<!-- JS CODE TO ENABLE for google analtics tag -->\n";
		 		$out .= "<!-- Global site tag (gtag.js) - Google Analytics -->
		 <script async src=\"https://www.googletagmanager.com/gtag/js?id=".dol_escape_htmltag(trim($tmptag))."\"></script>
		 <script>
		 window.dataLayer = window.dataLayer || [];
		 function gtag(){dataLayer.push(arguments);}
		 gtag('js', new Date());

		 gtag('config', '".dol_escape_js(trim($tmptag))."');
		 </script>";
				$out .= "\n";
		 	}
		 }
		 // Google Adsense (need Google module)
		 if (isModEnabled('google') && getDolGlobalString('MAIN_GOOGLE_AD_CLIENT') && getDolGlobalString('MAIN_GOOGLE_AD_SLOT')) {
		 	if (empty($conf->dol_use_jmobile)) {
		 		$out .= "<!-- Global Adsense -->
				 <div class=\"center\"><br>
				 <script><!--
				 google_ad_client = '".dol_escape_js(getDolGlobalString('MAIN_GOOGLE_AD_CLIENT'))."';
				 google_ad_slot = '".dol_escape_js(getDolGlobalString('MAIN_GOOGLE_AD_SLOT'))."';
				 google_ad_width = '".dol_escape_js(getDolGlobalString('MAIN_GOOGLE_AD_WIDTH'))."';
				 google_ad_height = '".dol_escape_js(getDolGlobalString('MAIN_GOOGLE_AD_HEIGHT'))."'
				 //-->
				 </script>
				 <script src=\"//pagead2.googlesyndication.com/pagead/show_ads.js\"></script>
				 </div>";
		 	}
		 }

		$this->resprints = $out;

		return 0;
	}
}
