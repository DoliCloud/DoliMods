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
 *       \file       htdocs/numberingpack/core/modules/societe/mod_codeclient_zebre.php
 *       \ingroup    societe
 *       \brief      File of class to manage third party codes with the Zebre model
 *       \version    $Id: mod_codeclient_zebre.php,v 1.5 2011/08/17 16:46:32 eldy Exp $
 */

require_once DOL_DOCUMENT_ROOT."/core/modules/societe/modules_societe.class.php";


/**
 *	Class to manage third party codes with the Zebre model
 */
class mod_codeclient_zebre extends ModeleThirdPartyCode
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	public $nom;							// Name of model
	public $code_modifiable;				// Can be changed if valid
	public $code_modifiable_invalide;		// Can be changed if not valid
	public $code_modifiable_null;			// Can be changed if not defined
	public $code_null;						// Can be undefined
	public $version;		// 'development', 'experimental', 'dolibarr'
	public $code_auto; 	// Automatic numbering


	/**
	 * Constructor
	 *
	 * @param	DoliDB	$db	Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
		$this->nom = "Zebre";
		$this->name = "Zebre";
		$this->version = '$Revision: 1.5 $';
		$this->code_modifiable = 0;				// Can be changed if valid
		$this->code_modifiable_invalide = 1;	// Can be changed if not valid
		$this->code_modifiable_null = 1;		// Can be changed if not defined
		$this->code_null = 0;					// Can be undefined
		$this->code_auto = 0;
	}


	/**
	 * Return description of numbering module
	 *
	 * @param	Translate	$langs	Language object
	 * @return	string				Description
	 */
	public function info($langs)
	{
		return "Check if customer/supplier code is CCCC9999. Check only, no automatic generation.";
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
		return "ABCD7501";
	}


	/**
	 * Return next value. This model does not generate codes (code_auto = 0).
	 *
	 * @param	Societe|int	$objsoc	Third party object
	 * @param	int	$type	Type of third party (1:customer, 2:supplier)
	 * @return	string	Empty string
	 */
	public function getNextValue($objsoc = 0, $type = -1)
	{
		return '';
	}


	/**
	 * Check validity of code according to its rules
	 *
	 * @param	DoliDB	$db	Database handler
	 * @param	string	$code	Code to check, cleaned (trimmed and uppercased), and changed to a free code if already used
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
			if ($this->verif_syntax($code) == 0) {
				$i = 1;

				$is_dispo = $this->verif_dispo($db, $code, $soc);
				while ($is_dispo <> 0 && $i < 99) {
					$arr = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

					$code = substr($code, 0, 6) . substr("00".$i, -2);

					$is_dispo = $this->verif_dispo($db, $code, $soc);

					$i++;
				}

				if ($is_dispo <> 0) {
					$result=-3;
				}
			} else {
				if (strlen(trim($code)) == 0) {
					$result=-2;
				} else {
					$result=-1;
				}
			}
		}
		dol_syslog("mod_codeclient_zebre::verif result=".$result);
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


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Return if a code respects the syntax (4 letters and 4 digits)
	 *
	 * @param	string	$code	Code to check
	 * @return	int	0 if OK, <0 if KO
	 */
	public function verif_syntax(&$code)
	{
		// phpcs:enable
		$res = 0;

		if (strlen($code) <> 8) {
			$res = -1;
		} else {
			if ($this->is_alpha(substr($code, 0, 4)) == 0 && $this->is_num(substr($code, 4, 4)) == 0 ) {
				$res = 0;
			} else {
				$res = -2;
			}
		}
		return $res;
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Count the characters that are not uppercase letters among the first 4 ones
	 *
	 * @param	string	$str	String to check
	 * @return	int	0 if the 4 characters are uppercase letters
	 */
	public function is_alpha($str)
	{
		// phpcs:enable
		$ok = 0;
		// No function found to test an alpha string without accented characters
		$alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		for ($i = 0 ; $i < 4 ; $i++) {
			if (strpos($alpha, substr($str, $i, 1)) === false) {
				$ok++;
			}
		}

		return $ok;
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Count the characters that are not digits among the first 4 ones
	 *
	 * @param	string	$str	String to check
	 * @return	int	0 if the 4 characters are digits
	 */
	public function is_num($str)
	{
		// phpcs:enable
		$ok = 0;

		$alpha = '0123456789';

		for ($i = 0 ; $i < 4 ; $i++) {
			if (strpos($alpha, substr($str, $i, 1)) === false) {
				$ok++;
			}
		}

		return $ok;
	}
}
