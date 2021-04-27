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
 *       \file       htdocs/core/modules/societe/mod_codeclient_zebre.class.php
 *       \ingroup    societe
 *       \brief      Fichier de la classe des gestion zebre des codes clients
 *       \version    $Id: mod_codeclient_zebre.php,v 1.5 2011/08/17 16:46:32 eldy Exp $
 */

require_once DOL_DOCUMENT_ROOT."/core/modules/societe/modules_societe.class.php";


/**
 *	Classe permettant la gestion zebre des codes tiers
 */
class mod_codeclient_zebre extends ModeleThirdPartyCode
{
	var $nom;							// Nom du modele
	var $code_modifiable;				// Can be changed if valid
	var $code_modifiable_invalide;		// Can be changed if not valid
	var $code_modifiable_null;			// Can be changed if not defined
	var $code_null;						// Can be undefined
	var $version;		// 'development', 'experimental', 'dolibarr'
	var $code_auto; 	// Numerotation automatique


	/**
	 * Constructor
	 */
	function mod_codeclient_zebre()
	{
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
	 *	Renvoie la description du module
	 *
	 *	@return     string      Texte descripif
	 */
	function info($langs)
	{
		return "Check if customer/supplier code is CCCC9999. Check only, no automatic generation.";
	}


	/**
	 * Renvoi la description du module
	 *
	 * @return     string      Texte descripif
	 */
	function getExample($langs)
	{
		return "ABCD7501";
	}


	/**
	 *  Return next value
	 *
	 *  @param      objsoc      Object third party
	 *  @param      $type       Client ou fournisseur (1:client, 2:fournisseur)
	 *  @return     string      Value if OK, '' if module not configured, <0 if KO
	 */
	function getNextValue($objsoc = 0, $type = -1)
	{
		global $db, $conf;

		$i = 1;

		$is_dispo = $this->verif_dispo($db, $code, $objsoc);

		while ( $is_dispo <> 0 && $i < 99) {
			$arr = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

			$code = substr($code, 0, 6) . substr("00".$i, -2);

			$is_dispo = $this->verif_dispo($db, $code, $objsoc);

			$i++;
		}

		return $is_dispo;
	}


	/**
	 *      \brief      Check validity of code according to its rules
	 *      \param      $db         Database handler
	 *      \param      $code       Code to check/correct
	 *      \param      $soc        Object third party
	 *      \param      $type       0 = customer/prospect , 1 = supplier
	 *      \return     int     0 if OK
	 *                          -1 ErrorBadCustomerCodeSyntax
	 *                          -2 ErrorCustomerCodeRequired
	 *                          -3 ErrorCustomerCodeAlreadyUsed
	 *                          -4 ErrorPrefixRequired
	 */
	function verif($db, &$code, $soc, $type)
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


	/**
	 *		\brief		Renvoi si un code est pris ou non (par autre tiers)
	 *		\param		$db			Handler acces base
	 *		\param		$code		Code a verifier
	 *		\param		$soc		Objet societe
	 *		\return		int			0 si dispo, <0 si erreur
	 */
	function verif_dispo($db, $code, $soc)
	{
		$sql = "SELECT code_client FROM ".MAIN_DB_PREFIX."societe";
		$sql.= " WHERE code_client = '".$code."'";
		if ($soc->id) $sql.= " AND rowid != '".$soc->id."'";

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
	 *	\brief		Renvoi si un code respecte la syntaxe
	 *	\param		$code		Code a verifier
	 *	\return		int			0 si OK, <0 si KO
	 */
	function verif_syntax(&$code)
	{
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


	function is_alpha($str)
	{
		$ok = 0;
		// Je n'ai pas trouve de fonction pour tester une chaine alpha sans les caract�re accentu�s
		// dommage
		$alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		for ($i = 0 ; $i < 4 ; $i++) {
			if (strpos($alpha, substr($str, $i, 1)) === false) {
				$ok++;
			}
		}

		return $ok;
	}


	function is_num($str)
	{
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
