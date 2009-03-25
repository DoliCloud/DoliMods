<?php
/* Copyright (C) 2005-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Regis Houssin        <regis@dolibarr.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *      \file       htdocs/includes/modules/barcode/pibarcode.modules.php
 *		\ingroup    facture
 *		\brief      Fichier contenant la classe du modèle de generation code barre pibarcode
 *		\version    $Id: pibarcode.modules.php,v 1.3 2009/03/25 20:17:14 eldy Exp $
 */

require_once(DOL_DOCUMENT_ROOT ."/includes/modules/barcode/modules_barcode.php");

/**	    \class      modPibarcode
		\brief      Classe du modèle de generation code barre pibarcode
*/
class modPibarcode extends ModeleBarCode
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $error='';

    /**     \brief      Renvoi la description du modele de numérotation
     *      \return     string      Texte descripif
     */
    function info()
    {
	 	global $langs;

    	return 'Pi-barcode';
    }

    /**     \brief      Test si les numéros déjà en vigueur dans la base ne provoquent pas de
     *                  de conflits qui empechera cette numérotation de fonctionner.
     *      \return     boolean     false si conflit, true si ok
     */
    function canBeActivated()
    {
        global $langs;

        return true;
    }

	/**
	 *	\brief		Return true if encodinf is supported
	 *	\return		int		>0 if supported, 0 if not
	 */
    function encodingIsSupported($encoding)
	{
		$supported=0;
		if ($encoding == 'EAN8')  $supported=1;
		if ($encoding == 'EAN13') $supported=1;
		if ($encoding == 'UPC')   $supported=1;
		if ($encoding == 'C39')   $supported=1;
		if ($encoding == 'C128')  $supported=1;
		return $supported;
	}

    /**
	 *		\brief      Retourne fichier image
	 *		\param   	$code			Valeur numérique a coder
	 *		\param   	$encoding		Mode de codage
	 *		\param   	$readable		Code lisible
     */
    function buildBarCode($code,$encoding,$readable='Y')
    {
		global $_GET;

		if (! $this->encodingIsSupported($encoding)) return -1;

		if ($encoding == 'EAN8' || $encoding == 'EAN13') $encoding = 'EAN';

		$_GET["code"]=$code;
		$_GET["type"]=$encoding;
		$_GET["height"]=50;
		$_GET["readable"]=$readable;

		require_once(DOL_DOCUMENT_ROOT.'/includes/barcode/pi_barcode/pi_barcode.php');

		return 1;
    }

    /**
	 *		\brief      Save an image file on disk
	 *		\param   	$code			Valeur numérique a coder
	 *		\param   	$encoding		Mode de codage
	 *		\param   	$readable		Code lisible
     */
    function writeBarCode($code,$encoding,$readable='Y')
    {
    	global $conf,$filebarcode;

		create_exdir($conf->barcode->dir_temp);

		$file=$conf->barcode->dir_temp.'/barcode_'.$code.'_'.$encoding.'.png';

		$olderrlevel=error_reporting(0);

		ob_flush();
		ob_start();

		$filebarcode=$file;	// global var to be used in buildBarCode
    	$result=$this->buildBarCode($code,$encoding,$readable);

    	$content=ob_get_clean();
		//print "xx".$content."zz";

	    if (!$handle = fopen($file, 'w'))
	    {
    	    echo "Cannot open file ($file)";
    	}
    	else
    	{
			// Write $content to our opened file.
	    	if (fwrite($handle, $content) === FALSE) {
	        	echo "Cannot write to file ($file)";
	    	}
		    fclose($handle);
    	}

    	error_reporting($olderrlevel);

		return $result;
    }

}

?>
