<?php
/* Copyright (C) 2005-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2014 	   Philippe Grand       <philippe.grand@atoo-net.com>
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
 *      \file       pibarcode/core/modules/barcode/pibarcode.modules.php
 *		\ingroup    other
 *		\brief      File of class to generate barcode images using pibarcode generator
 */

require_once DOL_DOCUMENT_ROOT ."/core/modules/barcode/modules_barcode.class.php";


/**
 *	Class to generate barcode images using pibarcode generator
 */
class modPibarcode extends ModeleBarCode
{
	public $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	public $error='';

	/**
	 *	Return description of barcode generator
	 *
	 *	@param	Translate	$langs	Language object
	 *  @return     string      Text with description
	 */
	public function info($langs)
	{
		global $langs;

		return 'Pi-barcode';
	}

	/**
	 * Test if module can be activated
	 *
	 * @return     boolean     false if ko, true if ok
	 */
	public function canBeActivated()
	{
		global $langs;

		return true;
	}

	/**
	 * Return true if encoding is supported
	 *
	 * @param	string	$encoding	Encoding (EAN8, EAN13, UPC, C39, C128...)
	 * @return	int					>0 if supported, 0 if not
	 */
	public function encodingIsSupported($encoding)
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
	 *	Return an image file on the fly (no need to write on disk)
	 *
	 *	@param	string		$code				Value to encode
	 *	@param	string		$encoding			Mode of encoding
	 *	@param	string		$readable			Code can be read
	 *	@param	int			$scale				Scale (not used)
	 *	@param	int<0,1>	$nooutputiferror	No output if error (not used)
	 *	@return		int				Return integer <0 if KO, >0 if OK
	 */
	public function buildBarCode($code, $encoding, $readable = 'Y', $scale = 1, $nooutputiferror = 0)
	{
		global $conf,$_GET;
		//global $filebarcode;

		if (! $this->encodingIsSupported($encoding)) return -1;

		if ($encoding == 'EAN8' || $encoding == 'EAN13') $encoding = 'EAN';

		$_GET["code"]=$code;
		$_GET["type"]=$encoding;
		$_GET["height"]=50;
		$_GET["readable"]=$readable;

		// Load the encoding class
		$file=dol_include_once('/pibarcode/includes/barcode/pi_barcode/pi_barcode.php');

		$objCode = new pi_barcode();

		$objCode->setSize(50);
		$objCode->hideCodeType();
		$objCode->setColors('#000000');
		$objCode->setType($encoding);
		$objCode->setCode($code);

		//$objCode->writeBarcodeFile($filebarcode) ;
		dol_syslog("pibarcode::buildBarCode");
		$objCode->showBarcodeImage();

		return 1;
	}

	/**
	 *	Save an image file on disk (with no output)
	 *
	 *	@param	string		$code				Value to encode
	 *	@param	string		$encoding			Mode of encoding
	 *	@param	string		$readable			Code can be read
	 *	@param	int			$scale				Scale (not used)
	 *	@param	int<0,1>	$nooutputiferror	No output if error (not used)
	 *	@return		int				Return integer <0 if KO, >0 if OK
	 */
	public function writeBarCode($code, $encoding, $readable = 'Y', $scale = 1, $nooutputiferror = 0)
	{
		global $conf,$filebarcode;

		dol_mkdir($conf->barcode->dir_temp);
		$file=$conf->barcode->dir_temp.'/barcode_'.$code.'_'.$encoding.'.png';
		$filebarcode=$file;	// File to write, stored into a global var

		if (! $this->encodingIsSupported($encoding)) return -1;

		if ($encoding == 'EAN8' || $encoding == 'EAN13') $encoding = 'EAN';

		$_GET["code"]=$code;
		$_GET["type"]=$encoding;
		$_GET["height"]=50;
		$_GET["readable"]=$readable;

		// Load the encoding class
		$file=dol_include_once('/pibarcode/includes/barcode/pi_barcode/pi_barcode.php');

		$objCode = new pi_barcode();

		$objCode->setSize(50);
		$objCode->hideCodeType();
		$objCode->setColors('#000000');
		$objCode->setType($encoding);
		$objCode->setCode($code);

		$objCode->writeBarcodeFile($filebarcode);

		return 1;
	}
}
