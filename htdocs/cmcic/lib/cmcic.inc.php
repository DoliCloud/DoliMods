<?php
/* Copyright (C) 2012      Mikael Carlavan        <mcarlavan@qis-network.com>
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

/**	    \file       htdocs/cmcic/lib/cmcic.inc.php
 *		\ingroup    cmcic
 *		\brief      cmcic toolkit library
 */

if (empty($conf->cmcic->enabled))
    exit;

define("CMCIC_CTLHMAC","V1.04.sha1.php--[CtlHmac%s%s]-%s");
define("CMCIC_CTLHMACSTR", "CtlHmac%s%s");
define("CMCIC_CGI2_RECEIPT","version=2\ncdr=%s");
define("CMCIC_CGI2_MACOK","0");
define("CMCIC_CGI2_MACNOTOK","1\n");
define("CMCIC_CGI2_FIELDS", "%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*");
define("CMCIC_CGI1_FIELDS", "%s*%s*%s%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s");


/*****************************************************************************
*
* Classe / Class : CMCIC_Tpe
*
*****************************************************************************/

class CMCIC_Tpe
{

	public $sVersion;	// Version du TPE - TPE Version (Ex : 3.0)
	public $sNumero;	// Numero du TPE - TPE Number (Ex : 1234567)
	public $sCodeSociete;	// Code Societe - Company code (Ex : companyname)
	public $sLangue;	// Langue - Language (Ex : FR, DE, EN, ..)
	public $sUrlOK;		// Url de retour OK - Return URL OK
	public $sUrlKO;		// Url de retour KO - Return URL KO
	public $sUrlPaiement;	// Url du serveur de paiement - Payment Server URL (Ex : https://paiement.creditmutuel.fr/paiement.cgi)

	private $_sCle;		// La cl� - The Key


	// ----------------------------------------------------------------------------
	//
	// Constructeur / Constructor
	//
	// ----------------------------------------------------------------------------

	function __construct($sVersion, $sKey, $sTpe, $sServer, $sSocieteCode, $sURLOK, $sURLKO, $sLangue = "FR") {

		// contr�le de l'existence des constantes de param�trages.
		/*$aRequiredConstants = array('CMCIC_CLE', 'CMCIC_VERSION', 'CMCIC_TPE', 'CMCIC_CODESOCIETE');
		$this->_checkTpeParams($aRequiredConstants);*/

		$this->sVersion = $sVersion;
		$this->_sCle = $sKey;
		$this->sNumero = $sTpe;
		$this->sUrlPaiement = $sServer;

		$this->sCodeSociete = $sSocieteCode;
		$this->sLangue = $sLangue;

		$this->sUrlOK = $sURLOK;
		$this->sUrlKO = $sURLKO;

	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : getCle
	//
	// Renvoie la cl� du TPE / return the TPE Key
	//
	// ----------------------------------------------------------------------------

	public function getCle() {

		return $this->_sCle;
	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : _checkTpeParams
	//
	// Contr�le l'existence des constantes d'initialisation du TPE
	// Check for the initialising constants of the TPE
	//
	// ----------------------------------------------------------------------------

	private function _checkTpeParams($aConstants) {

		for ($i = 0; $i < count($aConstants); $i++)
			if (!defined($aConstants[$i]))
				die ("Erreur param�tre " . $aConstants[$i] . " ind�fini");
	}

}


/*****************************************************************************
*
* Classe / Class : CMCIC_Hmac
*
*****************************************************************************/

class CMCIC_Hmac
{

	private $_sUsableKey;	// La cl� du TPE en format op�rationnel / The usable TPE key

	// ----------------------------------------------------------------------------
	//
	// Constructeur / Constructor
	//
	// ----------------------------------------------------------------------------

	function __construct($oTpe) {

		$this->_sUsableKey = $this->_getUsableKey($oTpe);
	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : _getUsableKey
	//
	// Renvoie la cl� dans un format utilisable par la certification hmac
	// Return the key to be used in the hmac function
	//
	// ----------------------------------------------------------------------------

	private function _getUsableKey($oTpe){

		$hexStrKey  = substr($oTpe->getCle(), 0, 38);
		$hexFinal   = "" . substr($oTpe->getCle(), 38, 2) . "00";

		$cca0=ord($hexFinal);

		if ($cca0>70 && $cca0<97)
			$hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
		else {
			if (substr($hexFinal, 1, 1)=="M")
				$hexStrKey .= substr($hexFinal, 0, 1) . "0";
			else
				$hexStrKey .= substr($hexFinal, 0, 2);
		}


		return pack("H*", $hexStrKey);
	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : computeHmac
	//
	// Renvoie le sceau HMAC d'une chaine de donn�es
	// Return the HMAC for a data string
	//
	// ----------------------------------------------------------------------------

	public function computeHmac($sData) {

		return strtolower(hash_hmac("sha1", $sData, $this->_sUsableKey));

		// If you don't have PHP 5 >= 5.1.2 and PECL hash >= 1.1
		// you may use the hmac_sha1 function defined below
		//return strtolower($this->hmac_sha1($this->_sUsableKey, $sData));
	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : hmac_sha1
	//
	// RFC 2104 HMAC implementation for PHP >= 4.3.0 - Creates a SHA1 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Adjusted from the md5 version by Lance Rushing .
	//
	// Impl�mentation RFC 2104 HMAC pour PHP >= 4.3.0 - Cr�ation d'un SHA1 HMAC.
	// Elimine l'installation de mhash pour le calcul d'un HMAC
	// Adapt�e de la version MD5 de Lance Rushing.
	//
	// ----------------------------------------------------------------------------

	public function hmac_sha1 ($key, $data)
	{

		$length = 64; // block length for SHA1
		if (strlen($key) > $length) { $key = pack("H*",sha1($key)); }
		$key  = str_pad($key, $length, chr(0x00));
		$ipad = str_pad('', $length, chr(0x36));
		$opad = str_pad('', $length, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;

		return sha1($k_opad  . pack("H*",sha1($k_ipad . $data)));
	}

}

// ----------------------------------------------------------------------------
// function getMethode
//
// IN:
// OUT: Donn�es soumises par GET ou POST / Data sent by GET or POST
// description: Renvoie le tableau des donn�es / Send back the data array
// ----------------------------------------------------------------------------

function getMethode()
{
    if ($_SERVER["REQUEST_METHOD"] == "GET")
        return $_GET;

    if ($_SERVER["REQUEST_METHOD"] == "POST")
	return $_POST;

    die ('Invalid REQUEST_METHOD (not GET, not POST).');
}

// ----------------------------------------------------------------------------
// function HtmlEncode
//
// IN:  chaine a encoder / String to encode
// OUT: Chaine encod�e / Encoded string
//
// Description: Encode special characters under HTML format
//                           ********************
//              Encodage des caract�res sp�ciaux au format HTML
// ----------------------------------------------------------------------------
function HtmlEncode ($data)
{
    $SAFE_OUT_CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890._-";
    $encoded_data = "";
    $result = "";
    for ($i=0; $i<strlen($data); $i++)
    {
        if (strchr($SAFE_OUT_CHARS, $data{$i})) {
            $result .= $data{$i};
        }
        else if (($var = bin2hex(substr($data,$i,1))) <= "7F"){
            $result .= "&#x" . $var . ";";
        }
        else
            $result .= $data{$i};

    }
    return $result;
}

