<?php
/* Copyright (C) 2004-2011      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * List reason for consultation
 *
 * @param 	nboflines
 * @param 	newwidth		Force width
 * @param	htmlname		Name of HTML select field
 * @param	selected		Preselected value
*/
function listmotifcons($nboflines,$newwidth=0,$htmlname='motifcons',$selected='')
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="list'.$htmlname.'" name="'.$htmlname.'" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_motifcons as s';
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            print '<option value="'.$obj->code.'"';
            if ($obj->code == $selected) print ' selected="selected"';
            print '>'.$obj->label.'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}

/**
 * List lesion diagnostic
 *
 * @param 	nboflines
 * @param 	newwidth		Force width
 * @param	htmlname		Name of HTML select field
 * @param	selected		Preselected value
*/
function listdiagles($nboflines,$newwidth=0,$htmlname='diagles',$selected='')
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    $out= '<select class="flat" id="list'.$htmlname.'" name="'.$htmlname.'" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    $out.= '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_diaglec as s';
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            $out.= '<option value="'.$obj->code.'"';
            if ($obj->code == $selected) $out.=' selected="selected"';
            $out.= '>'.$obj->label.'</option>';
            $i++;
        }
    }
    $out.= '</select>'."\n";
    return $out;
}

/**
 *  Show combo box with all exams
 *
 *  @param          nboflines       Max nb of lines
 *  @param          newwidth        Force width
 *  @param          type            To filter on a type
 *  @param          showtype        Show type
 *  @param          htmlname        Name of html select area
 */
function listexamen($nboflines,$newwidth=0,$type='',$showtype=0,$htmlname='examen')
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="list'.$htmlname.'" name="list'.$htmlname.'" '.($newwidth?'style="width: '.$newwidth.'px"':'').' size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label, s.biorad as type';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_examenprescrit as s';
    if ($type) $sql.=" WHERE s.biorad in ('".$type."')";
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            print '<option value="'.$obj->code.'">'.$obj->label.($showtype?' ('.strtolower($obj->type).')':'').'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}


/**
 *  Show combo box with all exam conclusions
 *
 *  @param          nboflines       Max nb of lines
 *  @param          newwidth        Force width
 *  @param          htmlname        Name of html select area
 */
function listexamconclusion($nboflines,$newwidth=0,$htmlname='examconc')
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="list'.$htmlname.'" name="list'.$htmlname.'" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_c_examconclusion as s';
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            print '<option value="'.$obj->code.'">'.$obj->label.'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}

/**
 * Show combo box with list of banks
 *
 * @param 	nboflines
 * @param 	newwidth		Force width
 * @param	defautlvalue	Preselected value
 * @param	htmlname		Name of HTML select field
 */
function listebanques($nboflines,$newwidth=0,$defaultvalue='',$htmlname='banque')
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value=""></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_c_banques as s';
    $sql.= ' ORDER BY label';
    $resql=$db->query($sql);
    dol_syslog("consutlations sql=".$sql);
    if ($resql)
    {
        $num=$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj=$db->fetch_object($resql);
            print '<option value="'.dol_escape_htmltag($obj->label).'"';
            if ($defaultvalue == $obj->label) print ' selected="selected"';
            print '>'.dol_escape_htmltag($obj->label).'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}


?>