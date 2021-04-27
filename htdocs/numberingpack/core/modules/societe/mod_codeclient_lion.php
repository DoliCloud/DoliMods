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
 *       \file       htdocs/core/modules/societe/mod_codeclient_lion.class.php
 *       \ingroup    societe
 *       \brief      Fichier de la classe des gestion lion des codes clients
 *       \version    $Id: mod_codeclient_lion.php,v 1.5 2011/08/17 16:46:32 eldy Exp $
 */

require_once DOL_DOCUMENT_ROOT."/core/modules/societe/modules_societe.class.php";


/**
 *	Classe permettant la gestion lion des codes tiers
 */
class mod_codeclient_lion extends ModeleThirdPartyCode
{
	var $nom;							// Nom du modele
	var $code_modifiable;				// Code modifiable
	var $code_modifiable_invalide;		// Code modifiable si il est invalide
	var $code_modifiable_null;			// Code modifiables si il est null
	var $code_null;						// Code facultatif
	var $version;		// 'development', 'experimental', 'dolibarr'
	var $code_auto; // Numerotation automatique


	/**
	 * 	Constructeur classe
	 */
	function mod_codeclient_lion()
	{
		$this->nom = "Lion";
		$this->name = "Lion";
		$this->version = '$Revision: 1.5 $';
		$this->code_modifiable = 0;
		$this->code_modifiable_invalide = 1;
		$this->code_modifiable_null = 1;
		$this->code_null = 0;
		$this->code_auto = 1;
	}


	/**		\brief      Renvoi la description du module
	 *      	\return     string      Texte descripif
	 */
	function info($langs)
	{
		return "Verifie si le code client/fournisseur est de la forme numerique 999 et sur au moins 3 chiffres. Verification mais pas de generation automatique.";
	}


	/**		\brief      Renvoi la description du module
	 *      	\return     string      Texte descripif
	 */
	function getExample($langs)
	{
		return "001";
	}


	/**     \brief      Return next value
	 *      \param      objsoc      Object third party
	 *      \param      $type       Client ou fournisseur (1:client, 2:fournisseur)
	 *      \return     string      Value if OK, '' if module not configured, <0 if KO
	 */
	function getNextValue($objsoc = 0, $type = -1)
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
			if ($this->_verif_syntax($code) >= 0) {
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
		$sql.= " AND rowid != '".$soc->id."'";

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
	 *	Renvoi si un code respecte la syntaxe
	 *
	 *	@param		$code		Code a verifier
	 *	@return		int			0 si OK, <0 si KO
	 */
	private function _verif_syntax($code)
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
