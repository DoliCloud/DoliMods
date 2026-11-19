<?php
/* Copyright (C) 2023		Laurent Destailleur			<eldy@users.sourceforge.net>
 * Copyright (C) 2024		Alice Adminson				<myemail@mycompany.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    discussion/class/actions_discussion.class.php
 * \ingroup discussion
 * \brief   Example hook overload.
 *
 * TODO: Write detailed description here.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';

/**
 * Class ActionsDiscussion
 */
class ActionsDiscussion extends CommonHookActions
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var string[] Errors
	 */
	public $errors = array();


	/**
	 * @var mixed[] Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var ?string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority;


	/**
	 * Constructor
	 *
	 *  @param	DoliDB	$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Execute action
	 *
	 * @param	array<string,mixed>	$parameters	Array of parameters
	 * @param	CommonObject		$object		The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string				$action		'add', 'update', 'view'
	 * @return	int								Return integer <0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *											>0 if OK and we want to replace standard actions.
	 */
	public function getNomUrl($parameters, &$object, &$action)
	{
		global $db, $langs, $conf, $user;
		$this->resprints = '';
		return 0;
	}

	/**
	 * Overload the doActions function : replacing the parent's function with the one below
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	CommonObject		$object			The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	?string				$action			Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager	Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		// @phan-suppress-next-line PhanPluginEmptyStatementIf
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {	    // do something only for the context 'somecontext1' or 'somecontext2'
			// Do what you want here...
			// You can for example load and use call global vars like $fieldstosearchall to overwrite them, or update the database depending on $action and GETPOST values.
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overload the doMassActions function : replacing the parent's function with the one below
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	CommonObject		$object			The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	?string				$action			Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager	Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			// @phan-suppress-next-line PhanPluginEmptyStatementForeachLoop
			foreach ($parameters['toselect'] as $objectid) {
				// Do action on each object id
			}
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overload the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param	array<string,mixed>	$parameters     Hook metadata (context, etc...)
	 * @param	CommonObject		$object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	?string	$action						Current action (if set). Generally create or edit or null
	 * @param	HookManager	$hookmanager			Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("DiscussionMassAction").'</option>';
		}

		if (!$error) {
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}



	/**
	 * Execute action before PDF (document) creation
	 *
	 * @param	array<string,mixed>	$parameters	Array of parameters
	 * @param	CommonObject		$object		Object output on PDF
	 * @param	string				$action		'add', 'update', 'view'
	 * @return	int								Return integer <0 if KO,
	 *											=0 if OK but we want to process standard actions too,
	 *											>0 if OK and we want to replace standard actions.
	 */
	public function beforePDFCreation($parameters, &$object, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0;
		$deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		// @phan-suppress-next-line PhanPluginEmptyStatementIf
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}

	/**
	 * Execute action after PDF (document) creation
	 *
	 * @param	array<string,mixed>	$parameters	Array of parameters
	 * @param	CommonDocGenerator	$pdfhandler	PDF builder handler
	 * @param	string				$action		'add', 'update', 'view'
	 * @return	int								Return integer <0 if KO,
	 * 											=0 if OK but we want to process standard actions too,
	 *											>0 if OK and we want to replace standard actions.
	 */
	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0;
		$deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		// @phan-suppress-next-line PhanPluginEmptyStatementIf
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}



	/**
	 * Overload the loadDataForCustomReports function : returns data to complete the customreport tool
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	?string				$action 		Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager    Hook manager propagated to allow calling another hook
	 * @return	int									Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function loadDataForCustomReports($parameters, &$action, $hookmanager)
	{
		global $langs;

		$langs->load("discussion@discussion");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'discussion') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("Discussion");
			$this->results['picto'] = 'discussion@discussion';
		}

		$head[$h][0] = 'customreports.php?objecttype='.$parameters['objecttype'].(empty($parameters['tabfamily']) ? '' : '&tabfamily='.$parameters['tabfamily']);
		$head[$h][1] = $langs->trans("CustomReports");
		$head[$h][2] = 'customreports';

		$this->results['head'] = $head;

		$arrayoftypes = array();
		//$arrayoftypes['discussion_myobject'] = array('label' => 'MyObject', 'picto'=>'myobject@discussion', 'ObjectClassName' => 'MyObject', 'enabled' => isModEnabled('discussion'), 'ClassPath' => "/discussion/class/myobject.class.php", 'langs'=>'discussion@discussion')

		$this->results['arrayoftype'] = $arrayoftypes;

		return 0;
	}



	/**
	 * Overload the restrictedArea function : check permission on an object
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	string				$action			Current action (if set). Generally create or edit or null
	 * @param	HookManager			$hookmanager	Hook manager propagated to allow calling another hook
	 * @return	int									Return integer <0 if KO,
	 *												=0 if OK but we want to process standard actions too,
	 *												>0 if OK and we want to replace standard actions.
	 */
	public function restrictedArea($parameters, &$action, $hookmanager)
	{
		global $user;

		if ($parameters['features'] == 'myobject') {
			if ($user->hasRight('discussion', 'myobject', 'read')) {
				$this->results['result'] = 1;
				return 1;
			} else {
				$this->results['result'] = 0;
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Execute action completeTabsHead
	 *
	 * @param	array<string,mixed>	$parameters		Array of parameters
	 * @param	CommonObject		$object			The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string				$action			'add', 'update', 'view'
	 * @param	Hookmanager			$hookmanager	Hookmanager
	 * @return	int									Return integer <0 if KO,
	 *												=0 if OK but we want to process standard actions too,
	 *												>0 if OK and we want to replace standard actions.
	 */
	public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user;

		if (!isset($parameters['object']->element)) {
			return 0;
		}
		if ($parameters['mode'] == 'remove') {
			// used to make some tabs removed
			return 0;
		} elseif ($parameters['mode'] == 'add') {
			$langs->load('discussion@discussion');
			// used when we want to add some tabs
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/discussion/discussion_tab.php', 1) . '?id=' . $id . '&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('DiscussionTab');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'discussionemails';
				$counter++;
			}
			if ($counter > 0 && (int) DOL_VERSION < 14) {  // @phpstan-ignore-line
				$this->results = $parameters['head'];
				// return 1 to replace standard code
				return 1;
			} else {
				// From V14 onwards, $parameters['head'] is modifiable by reference
				return 0;
			}
		} else {
			// Bad value for $parameters['mode']
			return -1;
		}
	}

	/**
	 * Add a picto into the top right menu to open the messages of a channel into a popover
	 *
	 * @param	array<string,mixed>	$parameters		Hook metadata (context, etc...)
	 * @param	?CommonObject		$object			Not used
	 * @param	?string				$action			Not used
	 * @param	HookManager			$hookmanager	Hook manager
	 * @return	int									0 to add the picto to the standard top right menu
	 */
	public function printTopRightMenu($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $langs, $user;

		$channelid = getDolGlobalInt('DISCUSSION_TOPMENU_CHANNEL_ID', 1);
		if ($channelid <= 0 || !$user->hasRight('discussion', 'channel', 'read')) {
			return 0;
		}

		// Channels the user can open. The one of the setup is shown first, if the user is one of its contacts.
		dol_include_once('/discussion/class/channel.class.php');
		$channelstatic = new Channel($this->db);
		$channels = $channelstatic->getChannelsOfUser($user->id);
		if (!is_array($channels) || empty($channels)) {
			return 0;
		}
		if (!isset($channels[$channelid])) {
			reset($channels);
			$channelid = (int) key($channels);
		}
		$langs->load('discussion@discussion');

		$label = $langs->trans('ModuleDiscussionName');
		$urlpagebase = dol_buildpath('/discussion/channel_messages.php', 1).'?id=';
		$urlpage = $urlpagebase.$channelid;
		$picto = '<span class="fa fa-comment atoplogin valignmiddle"></span>';
		// Keyboard shortcut "d", like "c" for the quick add of Dolibarr: the click on the picto is done by the browser
		$labelwithshortcut = $label.' ('.$conf->browser->stringforfirstkey.' d)';

		// Without javascript or on smartphone, the picto is a link to the page of messages
		if (empty($conf->use_javascript_ajax) || !empty($conf->dol_optimize_smallscreen)) {
			$this->resprints = '<div class="login_block_elem"><a accesskey="d" href="'.$urlpage.'" title="'.dol_escape_htmltag($labelwithshortcut).'">'.$picto.'</a></div>';
			return 0;
		}

		// Popover anchored under the top menu, like the one of the AI assistant (CSS and JS of the module, versioned
		// with the date of the file so a change is not hidden by the browser cache)
		$urlcss = dol_buildpath('/discussion/css/discussion_popover.css', 1).'?v='.((int) @filemtime(dol_buildpath('/discussion/css/discussion_popover.css', 0)));
		$urljs = dol_buildpath('/discussion/js/discussion_popover.js', 1).'?v='.((int) @filemtime(dol_buildpath('/discussion/js/discussion_popover.js', 0)));

		$out = '<link rel="stylesheet" href="'.$urlcss.'">';
		// Same markup as the pictos of the group of tools (AI assistant, bookmarks...): the JS moves it into this group
		$out .= '<div id="topmenu-discussion-dropdown" class="atoplogin dropdown inline-block topmenu-any">';
		$out .= '<a accesskey="d" href="#" id="topmenu-discussion-toggle" class="login-dropdown-a nofocusvisible" title="'.dol_escape_htmltag($labelwithshortcut).'"><span class="fa fa-comment"></span></a>';
		$out .= '</div>';
		$out .= '<div id="topmenu-discussion-popover" class="discussion-popover" role="dialog" aria-modal="false" aria-label="'.dol_escape_htmltag($label).'">';
		$out .= '<div class="discussion-popover-header">';
		$out .= '<span class="discussion-popover-title"><span class="fa fa-comment"></span>';
		if (count($channels) > 1) {
			// Choice of the channel: the JS loads the selected one into the iframe
			require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
			$out .= Form::selectarray('topmenu-discussion-channel', $channels, $channelid, 0, 0, 0, 'aria-label="'.dol_escape_htmltag($langs->trans('Channel')).'"', 0, 0, 0, '', 'discussion-popover-channel', 0);
		} else {
			$out .= '<span>'.dol_escape_htmltag($label).'</span>';
		}
		$out .= '</span>';
		$out .= '<a href="'.$urlpage.'" id="topmenu-discussion-fullpage" data-urlbase="'.dol_escape_htmltag($urlpagebase).'" title="'.dol_escape_htmltag($langs->trans('DiscussionOpenFullPage')).'"><span class="fa fa-expand"></span></a>';
		$out .= '<a href="#" class="discussion-popover-close" title="'.dol_escape_htmltag($langs->trans('Close')).'"><span class="fa fa-times"></span></a>';
		$out .= '</div>';
		// The page of messages of the channel is loaded into an iframe by the JS on first open, without menus, tabs and banner
		$out .= '<div class="discussion-popover-body" data-channel="'.((int) $channelid).'" data-urlbase="'.dol_escape_htmltag(dol_buildpath('/discussion/channel_messages.php', 1).'?dol_hide_topmenu=1&dol_hide_leftmenu=1&dol_openinpopup=discussionchannel&id=').'"></div>';
		$out .= '</div>';
		$out .= '<script nonce="'.getNonce().'" src="'.$urljs.'"></script>';

		$this->resprints = $out;

		return 0;
	}
}
