<?php
/* Copyright (C) 2011-2013	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Regis Houssin		<regis.houssin@capnetworks.com>
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
 *	\file       htdocs/autoattachfile/class/actions_autoattachfile.class.php
 *	\ingroup    autoattachfile
 *	\brief      File to control actions
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	Class to manage hooks for module autoattachfile
 */
class ActionsAutoattachfile
{
    var $db;
    var $error;
    var $errors=array();

    /**
     *	Constructor
     *
     *  @param		DoliDB		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
    }


    /**
     * doActions
     */
    function getFormMail($parameters, &$object, &$action, $hookmanager) 
    {
    	global $conf,$langs;
    	$langs->load('sendproductdoc@sendproductdoc');

    	$nbFiles=0;
    	
    	if (GETPOST('action') == 'presend' && GETPOST('mode') == 'init')
    	{
    		// Get current content of list of files
			$listofpaths = (! empty($_SESSION["listofpaths"])) ? explode(';',$_SESSION["listofpaths"]) : array();
			$listofnames = (! empty($_SESSION["listofnames"])) ? explode(';',$_SESSION["listofnames"]) : array();
			$listofmimes = (! empty($_SESSION["listofmimes"])) ? explode(';',$_SESSION["listofmimes"]) : array();
			if ($object->param['models'] == 'propal_send')
    		{
    			$nbFiles += $this->_addFiles($object, $listofpaths, $listofnames, $listofmimes, $conf->autoattachfile->dir_output.'/proposals');
    		}
    		
    	    if ($object->param['models'] == 'order_send')
    		{
    			$nbFiles += $this->_addFiles($object, $listofpaths, $listofnames, $listofmimes, $conf->autoattachfile->dir_output.'/orders');
    		}

    	    if ($object->param['models'] == 'facture_send')
    		{
    			$nbFiles += $this->_addFiles($object, $listofpaths, $listofnames, $listofmimes, $conf->autoattachfile->dir_output.'/invoices');
    		}

    		// Now we saved back content of files to have into attachment
    		$_SESSION["listofpaths"]=join(';',$listofpaths);
    		$_SESSION["listofnames"]=join(';',$listofnames);
    		$_SESSION["listofmimes"]=join(';',$listofmimes);
    	}
    
    }
    
	/**
	 * Add files from the list as e-mail attachments
	 */
	private function _addFiles($form, &$listofpaths, &$listofnames, &$listofmimes, $path) 
	{
		global $conf,$langs,$user;
		
		include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		$fileList = dol_dir_list($path,'files',0);
		$nbFiles = 0;

		$vardir=$conf->user->dir_output."/".$user->id;
		$upload_dir_tmp = $vardir.'/temp';
		
		foreach($fileList as $fileParams) {
			// Attachment in the e-mail
			$file = $fileParams['fullname'];
			$newfile = $upload_dir_tmp.'/'.basename($file);

			dol_move($file, $newfile, 0, 1);
			
			if (! in_array($newfile, $listofpaths)) 
			{
				$listofpaths[] = $newfile;
				$listofnames[] = basename($newfile);
				$listofmimes[] = dol_mimetype($newfile);
				$nbFiles++;
			}
		}
		
		return $nbFiles;
	}

}

?>
