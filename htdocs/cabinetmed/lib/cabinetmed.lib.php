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


function listmotifcons($nboflines,$newwidth=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="listmotifcons" name="motifcons" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
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
            print '<option value="'.$obj->code.'">'.$obj->label.'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}

function listdiagles($nboflines,$newwidth=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="listdiagles" name="diagles" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
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
            print '<option value="'.$obj->code.'">'.$obj->label.'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}

/**
 *  Show combo box with all exams
 *  @param          nboflines       Max nb of lines
 *  @param          newwidth        Force width
 *  @param          type            To filter on a type
 *  @param          showtype        Show type
 */
function listexamenprescrit($nboflines,$newwidth=0,$type='',$showtype=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="listexamenprescrit" name="examenprescrit" '.($newwidth?'style="width: '.$newwidth.'px"':'').' size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label, s.biorad as type';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_examenprescrit as s';
    if ($type) $sql.=" WHERE s.biorad = '".$type."'";
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

function listebanques($nboflines,$newwidth=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="banque" name="banque" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
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
            print '<option value="'.dol_escape_htmltag($obj->label).'">'.dol_escape_htmltag($obj->label).'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}


function listexamconclusion($nboflines,$newwidth=0)
{
    global $db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="listexamconc" name="examconc" style="width: '.$newwidth.'px" size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
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


?>