<?php
/* Copyright (C) 2004      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2006-2007 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file       htdocs/numberingpack/core/modules/societe/mod_codeclient_lion.php
 *       \ingroup    societe
 *       \brief      File of class to manage third party codes with the Lion model
 *       \version    $Id: mod_codeclient_lion.php,v 1.5 2011/08/17 16:46:32 eldy Exp $
 */

require_once DOL_DOCUMENT_ROOT."/core/modules/societe/modules_societe.class.php";


/**
 *	Class to manage third party codes with the Lion model
 */
class mod_codeclient_lion extends ModeleThirdPartyCode
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	public $nom;							// Name of model
	public $code_modifiable;				// Code can be changed
	public $code_modifiable_invalide;		// Code can be changed if not valid
	public $code_modifiable_null;			// Code can be changed if not defined
	public $code_null;						// Code is optional
	public $version;		// 'development', 'experimental', 'dolibarr'
	public $code_auto; // Automatic numbering


	/**
	 * Constructor
	 *
	 * @param	DoliDB	$db	Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
		$this->nom = "Lion";
		$this->name = "Lion";
		$this->version = '$Revision: 1.5 $';
		$this->code_modifiable = 0;
		$this->code_modifiable_invalide = 1;
		$this->code_modifiable_null = 1;
		$this->code_null = 0;
		$this->code_auto = 1;
	}


	/**
	 * Return description of numbering module
	 *
	 * @param	Translate	$langs	Language object
	 * @return	string				Description
	 */
	public function info($langs)
	{
		return "Verifie si le code client/fournisseur est de la forme numerique 999 et sur au moins 3 chiffres. Verification mais pas de generation automatique.";
	}


	/**
	 * Return an example of code
	 *
	 * @param	?Translate		$langs	Language object
	 * @param	Societe|string	$objsoc	Third party object
	 * @param	int				$type	Type of third party (1:customer, 2:supplier, -1:autodetect)
	 * @return	string					Example
	 */
	public function getExample($langs = null, $objsoc = '', $type = -1)
	{
		return "001";
	}


	/**
	 * Return next value
	 *
	 * @param	Societe|int	$objsoc	Third party object
	 * @param	int	$type	Type of third party (1:customer, 2:supplier)
	 * @return	string|int	Next value if OK, -1 if KO
	 */
	public function getNextValue($objsoc = 0, $type = -1)
	{
		global $db, $conf;

		$return='001';

		$sql = "SELECT MAX(code_client) as maxval FROM ".MAIN_DB_PREFIX."societe";
		$resql=$db->query($sql);
		if ($resql) {
			$obj=$db->fetch_object($resql);
			if ($obj) {
				$newval=$obj->maxval+1;
				$return=sprintf('%03d', $newval);
				return $return;
			}
		} else {
			return -1;
		}
	}


	/**
	 * Check validity of code according to its rules
	 *
	 * @param	DoliDB	$db	Database handler
	 * @param	string	$code	Code to check, cleaned (trimmed and uppercased)
	 * @param	Societe	$soc	Third party object
	 * @param	int	$type	0 = customer/prospect, 1 = supplier
	 * @return	int	0 if OK, -1 ErrorBadCustomerCodeSyntax, -2 ErrorCustomerCodeRequired, -3 ErrorCustomerCodeAlreadyUsed
	 */
	public function verif($db, &$code, $soc, $type)
	{
		$result=0;
		$code = strtoupper(trim($code));

		if (! $code && $this->code_null) {
			$result=0;
		} else {
			if ($this->verifSyntax($code) >= 0) {
				$is_dispo = $this->verif_dispo($db, $code, $soc);
				if ($is_dispo <> 0) {
					$result=-3;
				} else {
					$result=0;
				}
			} else {
				if (strlen($code) == 0) {
					$result=-2;
				} else {
					$result=-1;
				}
			}
		}
		dol_syslog("mod_codeclient_lion::verif result=".$result);
		return $result;
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Return if a code is already used by another third party
	 *
	 * @param	DoliDB	$db	Database handler
	 * @param	string	$code	Code to check
	 * @param	Societe	$soc	Third party object
	 * @return	int	0 if available, -1 if already used, -2 if error
	 */
	public function verif_dispo($db, $code, $soc)
	{
		// phpcs:enable
		$sql = "SELECT code_client FROM ".MAIN_DB_PREFIX."societe";
		$sql.= " WHERE code_client = '".$db->escape($code)."'";
		$sql.= " AND entity IN (".getEntity('societe').")";
		if ($soc->id > 0) {
			$sql.= " AND rowid <> ".((int) $soc->id);
		}

		$resql=$db->query($sql);
		if ($resql) {
			if ($db->num_rows($resql) == 0) {
				return 0;
			} else {
				return -1;
			}
		} else {
			return -2;
		}
	}


	/**
	 * Return if a code respects the syntax
	 *
	 * @param	string	$code	Code to check
	 * @return	int	0 if OK, <0 if KO
	 */
	private function verifSyntax($code)
	{
		$res = 0;

		if (strlen($code) < 3) {
			$res = -1;
		} else {
			if (preg_match('/[0-9][0-9][0-9]+/', $code)) {
				$res = 0;
			} else {
				$res = -2;
			}
		}
		return $res;
	}
}
