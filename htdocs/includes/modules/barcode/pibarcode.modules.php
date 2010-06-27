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
 *		\version    $Id: pibarcode.modules.php,v 1.7 2010/06/27 15:17:46 eldy Exp $
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
	 *	\brief		Return true if encoding is supported
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
	 *		\brief      Return an image file on the fly (no need to write on disk)
	 *		\param   	$code			Value to encode
	 *		\param   	$encoding		Mode of encoding
	 *		\param   	$readable		Code can be read
     */
    function buildBarCode($code,$encoding,$readable='Y')
    {
		global $conf,$_GET;
		//global $filebarcode;

		if (! $this->encodingIsSupported($encoding)) return -1;

		if ($encoding == 'EAN8' || $encoding == 'EAN13') $encoding = 'EAN';

		$_GET["code"]=$code;
		$_GET["type"]=$encoding;
		$_GET["height"]=50;
		$_GET["readable"]=$readable;

		// Chargement de la classe de codage
		foreach ($conf->dol_document_root as $dirroot)
		{
			$file=$dirroot . '/includes/barcode/pi_barcode/pi_barcode.php';
			$result=@include_once($file);
			if ($result) break;
		}
		//require_once(DOL_DOCUMENT_ROOT.'/includes/barcode/pi_barcode/pi_barcode.php');

		$objCode = new pi_barcode() ;

		$objCode->setSize(50);
		$objCode->hideCodeType();
		$objCode->setColors('#000000');
		$objCode->setType($encoding) ;
		$objCode->setCode($code) ;

		//$objCode->writeBarcodeFile($filebarcode) ;
		dol_syslog("pibarcode::buildBarCode");
		$objCode->showBarcodeImage();

		return 1;
    }

	/**
     *		\brief      Save an image file on disk (with no output)
	 *		\param   	$code			Value to encode
	 *		\param   	$encoding		Mode of encoding
	 *		\param   	$readable		Code can be read
     */
    function writeBarCode($code,$encoding,$readable='Y')
    {
    	global $conf,$filebarcode;

		create_exdir($conf->barcode->dir_temp);
		$file=$conf->barcode->dir_temp.'/barcode_'.$code.'_'.$encoding.'.png';
		$filebarcode=$file;	// global var to be used in buildBarCode

		if (! $this->encodingIsSupported($encoding)) return -1;

		if ($encoding == 'EAN8' || $encoding == 'EAN13') $encoding = 'EAN';

		$_GET["code"]=$code;
		$_GET["type"]=$encoding;
		$_GET["height"]=50;
		$_GET["readable"]=$readable;

		// Chargement de la classe de codage
		foreach ($conf->file->dol_document_root as $dirroot)
		{
			$file=$dirroot . '/includes/barcode/pi_barcode/pi_barcode.php';
			$result=@include_once($file);
			if ($result) break;
		}
		//require_once(DOL_DOCUMENT_ROOT.'/includes/barcode/pi_barcode/pi_barcode.php');

		$objCode = new pi_barcode() ;

		$objCode->setSize(50);
		$objCode->hideCodeType();
		$objCode->setColors('#000000');
		$objCode->setType($encoding) ;
		$objCode->setCode($code) ;

		$objCode->writeBarcodeFile($filebarcode) ;

		return 1;
    }

}

?>
