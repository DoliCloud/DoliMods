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
	 * Allow the tiles of the tile server of the setup (OPENSTREETMAP_TILE_URL) into the Content Security Policy
	 * of the pages of the module
	 *
	 * @param	array<string,mixed>	$parameters		Hook parameters ('contentsecuritypolicy', 'mode')
	 * @param	?CommonObject		$object			Not used
	 * @param	?string				$action			Not used
	 * @param	HookManager			$hookmanager	Hook manager (->resPrint is the policy changed by the previous hooks)
	 * @return	int									0 to keep the policy, 1 to replace it with $this->resprints
	 */
	public function setContentSecurityPolicy($parameters, &$object, &$action, $hookmanager)
	{
		$csp = empty($parameters['contentsecuritypolicy']) ? '' : (string) $parameters['contentsecuritypolicy'];
		// Complete the policy of the previous hooks of this call, if any, so their changes are kept
		if ($hookmanager->resNbOfHooks > 1 && !empty($hookmanager->resPrint)) {
			$csp = (string) $hookmanager->resPrint;
		}
		// Give the policy to the next hooks, even if not changed, so they never read a value of another call
		$hookmanager->resPrint = $csp;
		// Only the pages of the module show maps
		if ($csp === '' || strpos((string) $_SERVER['PHP_SELF'], '/openstreetmap/') === false) {
			return 0;
		}

		$source = openstreetmap_get_tile_csp_source();
		if ($source === '') {
			dol_syslog("openstreetmap: tile URL not usable for the Content Security Policy: ".openstreetmap_get_tile_url(), LOG_WARNING);
			return 0;
		}
		$newcsp = openstreetmap_csp_add_img_source($csp, $source);
		if ($newcsp === $csp) {
			return 0;
		}
		$this->resprints = $newcsp;
		// Give the result to the next hooks (older Dolibarr versions do not do it)
		$hookmanager->resPrint = $newcsp;

		return 1;
	}
}
