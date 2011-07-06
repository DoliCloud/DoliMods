<?php
/* Copyright (c) 2008-2011 Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2010-2011 Regis Houssin		<regis@dolibarr.fr>
 * Copyright (c) 2010      Juanjo Menent		<jmenent@2byte.es>
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
 */

/**
 *	\file       htdocs/cabinetmed/class/html.formfilecabinetmed.class.php
 *  \ingroup    core
 *	\brief      File of class to offer components to list and upload files
 *	\version	$Id: html.formfilecabinetmed.class.php,v 1.1 2011/07/06 18:23:18 eldy Exp $
 */


/**
 *	\class      FormFileCabinetmed
 *	\brief      Class to offer components to list and upload files
 */
class FormFileCabinetmed
{
	var $db;
	var $error;

	var $numoffiles;


	/**
	 *		Constructor
	 *		@param     DB      handler d'acces base de donnee
	 */
	function FormFileCabinetmed($DB)
	{
		$this->db = $DB;

		$this->numoffiles=0;

		return 1;
	}


    /**
     *      Show list of documents in a directory
     *      @param      filearray           Array of files loaded by dol_dir_list('files') function before calling this
     *      @param      object              Object on which document is linked to
     *      @param      modulepart          Value for modulepart used by download or viewimage wrapper
     *      @param      param               Parameters on sort links
     *      @param      forcedownload       Force to open dialog box "Save As" when clicking on file
     *      @param      relativepath        Relative path of docs (autodefined if not provided)
     *      @param      permtodelete        Permission to delete
     *      @param      useinecm            Change output for use in ecm module
     *      @param      textifempty         Text to show if filearray is empty
     *      @param      maxlength           Maximum length of file name shown
     *      @return     int                 <0 if KO, nb of files shown if OK
     */
    function list_of_documents($filearray,$object,$modulepart,$param,$forcedownload=0,$relativepath='',$permtodelete=1,$useinecm=0,$textifempty='',$maxlength=0)
    {
        global $user, $conf, $langs;
        global $bc;
        global $sortfield, $sortorder;

        // Show list of existing files
        if (empty($useinecm)) print_titre($langs->trans("AttachedFiles"));
        //else { $bc[true]=''; $bc[false]=''; };
        $url=$_SERVER["PHP_SELF"];
        print '<table width="100%" class="nobordernopadding">';
        print '<tr class="liste_titre">';
        print_liste_field_titre($langs->trans("Documents2"),$_SERVER["PHP_SELF"],"name","",$param,'align="left"',$sortfield,$sortorder);
        print_liste_field_titre($langs->trans("Size"),$_SERVER["PHP_SELF"],"size","",$param,'align="right"',$sortfield,$sortorder);
        print_liste_field_titre($langs->trans("Date"),$_SERVER["PHP_SELF"],"date","",$param,'align="center"',$sortfield,$sortorder);
        if (empty($useinecm)) print_liste_field_titre('',$_SERVER["PHP_SELF"],"","",$param,'align="center"');
        print_liste_field_titre('','','');
        print '</tr>';

        $nboffiles=sizeof($filearray);

        if ($nboffiles > 0) include_once(DOL_DOCUMENT_ROOT.'/lib/images.lib.php');

        $var=true;
        foreach($filearray as $key => $file)      // filearray must be only files here
        {
            if ($file['name'] != '.'
            && $file['name'] != '..'
            && $file['name'] != 'CVS'
            && ! preg_match('/\.meta$/i',$file['name']))
            {
                // Define relative path used to store the file
                if (! $relativepath) $relativepath=dol_sanitizeFileName($object->ref).'/';

                $var=!$var;
                print '<tr '.$bc[$var].'>';
                print '<td>';
                //print "XX".$file['name']; //$file['name'] must be utf8
                print '<a href="'.DOL_URL_ROOT.'/document.php?modulepart='.$modulepart;
                if ($forcedownload) print '&attachment=1';
                print '&file='.urlencode($relativepath.$file['name']).'">';
                print img_mime($file['name'],$file['name'].' ('.dol_print_size($file['size'],0,0).')').' ';
                print dol_trunc($file['name'],$maxlength,'middle');
                print '</a>';
                print "</td>\n";
                print '<td align="right">'.dol_print_size($file['size'],1,1).'</td>';
                print '<td align="center">'.dol_print_date($file['date'],"dayhour").'</td>';
                // Preview
                if (empty($useinecm))
                {
                    print '<td align="center">';
                    $tmp=explode('.',$file['name']);
                    $minifile=$tmp[0].'_mini.'.$tmp[1];
                    if (image_format_supported($file['name']) > 0) print '<img border="0" height="'.$maxheightmini.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&file='.urlencode($relativepath.'thumbs/'.$minifile).'" title="">';
                    else print '&nbsp;';
                    print '</td>';
                }
                // Delete or view link
                print '<td align="right">';
                if (! empty($useinecm)) print '<a href="'.DOL_URL_ROOT.'/ecm/docfile.php?section='.$_REQUEST["section"].'&urlfile='.urlencode($file['name']).'">'.img_view().'</a> &nbsp; ';
                if ($permtodelete) print '<a href="'.$url.'?socid='.$object->id.'&section='.$_REQUEST["section"].'&action=delete&urlfile='.urlencode($file['name']).'">'.img_delete().'</a>';
                else print '&nbsp;';
                print "</td>";
                print "</tr>\n";
            }
        }
        if ($nboffiles == 0)
        {
            print '<tr '.$bc[$var].'><td colspan="4">';
            if (empty($textifempty)) print $langs->trans("NoFileFound");
            else print $textifempty;
            print '</td></tr>';
        }
        print "</table>";
        // Fin de zone
    }


}

?>
