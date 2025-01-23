<?php
/* Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *	\file       htdocs/google/core/boxes/box_googlemaps.php
 *	\ingroup    google
 *	\brief      Module to show box of link to google maps
 */

include_once DOL_DOCUMENT_ROOT.'/core/boxes/modules_boxes.php';


/**
 * Class to manage the box to show links to maps
 */
class box_googlemaps extends ModeleBoxes
{
	var $boxcode="googlemaps";
	var $boximg="google@google";
	var $boxlabel="List of maps";
	var $depends = array("google@google");

	var $db;
	var $param;
	var $enabled = 1;

	var $info_box_head = array();
	var $info_box_contents = array();


	/**
	 *  Constructor
	 *
	 *  @param  DoliDB	$db      	Database handler
	 *  @param	string	$param		More parameters
	 */
	function __construct($db, $param = '')
	{
		global $user, $langs;

		$this->db = $db;

		$langs->load("google@google");
		$this->boxlabel=$langs->trans("ListOfMaps");

		// disable module for such cases
		$listofmodulesforexternal=explode(',', getDolGlobalString('MAIN_MODULES_FOR_EXTERNAL'));
		if (! in_array('adherent', $listofmodulesforexternal) && ! in_array('societe', $listofmodulesforexternal) && ! empty($user->societe_id)) $this->enabled=0;	// disabled for external users
	}

	/**
	 *  Load data into info_box_contents array to show array later.
	 *
	 *  @param	int		$max        Maximum number of records to load
	 *  @return	void
	 */
	function loadBox($max = 5)
	{
		global $user, $langs;
		$langs->load("boxes");
		$langs->load("google@google");

		$something = 0;

		$this->info_box_head = array('text' => $langs->trans("BoxMaps", $max));

		$i=0;
		if (isModEnabled('societe') && $user->hasRight('societe', 'lire') && getDolGlobalString('GOOGLE_ENABLE_GMAPS') && getDolGlobalString('CABINETMED_HIDETHIRPARTIESMENU')) {
			$something++;

			$url=dol_buildpath("/google/gmaps_all.php", 1)."?mode=thirdparty";
			$this->info_box_contents[$i][0] = array('td' => 'align="left" width="16"',
					'logo' => 'object_company',
					'url' => $url
			);
			$this->info_box_contents[$i][1] = array('td' => 'align="left"',
					'text' => '<a href="'.$url.'">'.$langs->trans("MapOfThirdparties").'</a>',
					'url' => $url
			);

			$i++;
		}
		if (isModEnabled('societe') && $user->hasRight('societe', 'lire') && getDolGlobalString('GOOGLE_ENABLE_GMAPS_CONTACTS')) {
			$something++;

			$url=dol_buildpath("/google/gmaps_all.php", 1)."?mode=contact";
			$this->info_box_contents[$i][0] = array('td' => 'align="left" width="16"',
					'logo' => 'object_contact',
					'url' => $url
			);
			$this->info_box_contents[$i][1] = array('td' => 'align="left"',
					'text' => '<a href="'.$url.'">'.$langs->trans("MapOfContactsAddresses").'</a>',
					'url' => $url
			);

			$i++;
		}
		if (isModEnabled('adherent') && $user->hasRight('adherent', 'lire') && getDolGlobalString('GOOGLE_ENABLE_GMAPS_MEMBERS')) {
			$something++;

			$url=dol_buildpath("/google/gmaps_all.php", 1)."?mode=member";
			$this->info_box_contents[$i][0] = array('td' => 'align="left" width="16"',
					'logo' => 'object_user',
					'url' => $url
			);
			$this->info_box_contents[$i][1] = array('td' => 'align="left"',
					'text' => '<a href="'.$url.'">'.$langs->trans("MapOfMembers").'</a>',
					'url' => $url
			);

			$i++;
		}
		if (isModEnabled('cabinetmed') && $user->hasRight('cabinetmed', 'read')) {
			$something++;

			$url=dol_buildpath("/google/gmaps_all.php", 1)."?mode=patient";
			$this->info_box_contents[$i][0] = array('td' => 'align="left" width="16"',
					'logo' => 'object_user',
					'url' => $url
			);
			$this->info_box_contents[$i][1] = array('td' => 'align="left"',
					'text' => '<a href="'.$url.'">'.$langs->trans("MapOfPatients").'</a>',
					'url' => $url
			);

			$i++;
		}

		if (! $something) {
			$this->info_box_contents[0][0] = array('align' => 'left',
			'text' => $langs->trans("No map available"));
		}
	}

	/**
	 *	Method to show box
	 *
	 *	@param	array	$head       Array with properties of box title
	 *	@param  array	$contents   Array with properties of box lines
	 *  @param	int		$nooutput	No print, only return string
	 *	@return	void
	 */
	function showBox($head = null, $contents = null, $nooutput = 0)
	{
		return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
	}
}
