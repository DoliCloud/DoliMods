<?php
/* Copyright (C) 2023-2026  Frédéric France <frederic.france@free.fr>
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
 * \file    memcached/class/actions_memcached.class.php
 * \ingroup memcached
 * \brief   Memcached hook overload.
 *
 */

/**
 * Class ActionsMemcached
 */
class ActionsMemcached
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
	 * @var array Errors
	 */
	public $errors = [];


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = [];

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority = 99;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB $db Database handler.
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array        $parameters  Hook metadatas (context, etc...)
	 * @param   CommonObject $object      The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string       $action      Current action (if set). Generally create or edit or null
	 * @param   HookManager  $hookmanager Hook manager propagated to allow calling another hook
	 * @return  integer                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, $object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$contexts = explode(':', $parameters['context'] ?? []);

		// clear cache when adding or deleting a translation
		if (
			(in_array('admintranslation', $contexts) && in_array($action, ['add', 'delete']))
			|| (in_array('adminmodules', $contexts) && in_array($action, ["reload", "set", "reset"]))
		) {
			if (class_exists("Memcached")) {
				$m = new Memcached();
			} elseif (class_exists("Memcache")) {
				$m = new Memcache();
			} else {
				return 0;
			}
			$langs->load("memcached@memcached");
			$tmparray = explode(':', getDolGlobalString('MEMCACHED_SERVER'));
			$server = $tmparray[0];
			$port = (empty($tmparray[1]) ? 0 : $tmparray[1]);

			dol_syslog("Try to connect to server " . $server . " port " . $port . " with class " . get_class($m));
			$m->addServer($server, ($port || strpos($tmparray[0], '/') !== false) ? $port : 11211);

			// This action must be set here and not in actions to be sure all lang files are already loaded
			$m->flush();
			setEventMessage($langs->trans("Flushed"));
		}

		return 0;
	}
}
