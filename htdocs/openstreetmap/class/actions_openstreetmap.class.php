<?php
/* This program is free software; you can redistribute it and/or modify
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file		htdocs/openstreetmap/class/actions_openstreetmap.class.php
 *	\ingroup	openstreetmap
 *	\brief		Hook file of the module OpenStreetMap
 */

dol_include_once('/openstreetmap/lib/openstreetmap.lib.php');


/**
 *	Class of the hooks of the module OpenStreetMap
 */
class ActionsOpenstreetmap
{
	/**
	 * @var DoliDB Database handler
	 */
	public $db;

	/**
	 * @var string Error message
	 */
	public $error = '';

	/**
	 * @var string[] Error messages
	 */
	public $errors = array();

	/**
	 * @var array<string,mixed> Hook results
	 */
	public $results = array();

	/**
	 * @var string String returned by the hook
	 */
	public $resprints = '';


	/**
	 * Constructor
	 *
	 * @param	DoliDB	$db		Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Allow the tiles of OpenStreetMap into the Content Security Policy of the pages of the module
	 *
	 * @param	array<string,mixed>	$parameters		Hook parameters ('contentsecuritypolicy', 'mode')
	 * @param	?CommonObject		$object			Not used
	 * @param	?string				$action			Not used
	 * @param	HookManager			$hookmanager	Hook manager
	 * @return	int									0 to keep the policy, 1 to replace it with $this->resprints
	 */
	public function setContentSecurityPolicy($parameters, &$object, &$action, $hookmanager)
	{
		$csp = empty($parameters['contentsecuritypolicy']) ? '' : (string) $parameters['contentsecuritypolicy'];
		// Only the pages of the module show maps
		if ($csp === '' || strpos((string) $_SERVER['PHP_SELF'], '/openstreetmap/') === false) {
			return 0;
		}

		$newcsp = openstreetmap_csp_add_img_source($csp, '*.openstreetmap.org');
		if ($newcsp === $csp) {
			return 0;
		}
		$this->resprints = $newcsp;

		return 1;
	}
}
