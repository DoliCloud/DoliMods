<?php
/* Copyright (C) 2004-2013      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *   \file       htdocs/cabinetmed/lib/cabinetmed.lib.php
 *   \brief      List of functions for cabinetmed module
 *   \ingroup    cabinetmed
 */


/**
 * Add alert into database
 *
 * @param	DoliDB		$db			Database handler
 * @param	string		$type		Type of alert
 * @param	int			$id			Id of alert
 * @param	string		$value		Value
 * @return	string					'' if OK, error message if KO
 */
function addAlert($db, $type, $id, $value)
{
    $res='';

    $sql = "INSERT INTO ".MAIN_DB_PREFIX."cabinetmed_patient(rowid, ".$type.") VALUES (".$id.", ".$value.")";
    dol_syslog("sql=".$sql);
    $resql1 = $db->query($sql,1);

    $sql = "UPDATE ".MAIN_DB_PREFIX."cabinetmed_patient SET ".$type."=".$value." WHERE rowid=".$id;
    dol_syslog("sql=".$sql);
    $resql2 = $db->query($sql);

    if (! $resql2)    // resql1 can fails if key already exists
    {
        $res = $db->lasterror();
    }

    return $res;
}


/**
 * List reason for consultation
 *
 * @param 	int		$nboflines		Nb of lines
 * @param 	int		$newwidth		Force width
 * @param	string	$htmlname		Name of HTML select field
 * @param	string	$selected		Preselected value
 * @return	void
*/
function listmotifcons($nboflines,$newwidth=0,$htmlname='motifcons',$selected='')
{
    global $conf,$db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="list'.$htmlname.'" name="'.$htmlname.'" '.(empty($conf->dol_use_jmobile)?' style="width: '.$newwidth.'px" ':'').'size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_motifcons as s';
    $sql.= ' WHERE active = 1';
    $sql.= ' ORDER BY position, label';
    $resql=$db->query($sql);
    dol_syslog("listmotifcons sql=".$sql);
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
 * @param 	int		$nboflines		Nb of lines
 * @param 	int		$newwidth		Force width
 * @param	string	$htmlname		Name of HTML select field
 * @param	string	$selected		Preselected value
 * @return	void
*/
function listdiagles($nboflines,$newwidth=0,$htmlname='diagles',$selected='')
{
    global $conf,$db,$width;

    if (empty($newwidth)) $newwidth=$width;

    $out= '<select class="flat" id="list'.$htmlname.'" name="'.$htmlname.'" '.(empty($conf->dol_use_jmobile)?' style="width: '.$newwidth.'px" ':'').'size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    $out.= '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_diaglec as s';
    $sql.= ' WHERE active = 1';
    $sql.= ' ORDER BY position, label';
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
 *  @param	int		$nboflines       Max nb of lines
 *  @param  int     $newwidth        Force width
 *  @param  string  $type            To filter on a type (Ex: "RADIO" or "RADIO,OTHER")
 *  @param	string	$showtype        1=Show type of line after label
 *  @param  string	$htmlname        Name of html select area
 *  @return	void
 */
function listexamen($nboflines,$newwidth=0,$type='',$showtype=0,$htmlname='examen')
{
    global $conf,$db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="list'.$htmlname.'" name="list'.$htmlname.'" '.(empty($conf->dol_use_jmobile)?' style="width: '.$newwidth.'px" ':'').'size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label, s.biorad as type';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_examenprescrit as s';
    $sql.= ' WHERE active = 1';
    if ($type) $sql.=" AND s.biorad in ('".$type."')";
    $sql.= ' ORDER BY position, label';
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
 *  @param	int		$nboflines       Max nb of lines
 *  @param  int		$newwidth        Force width
 *  @param  string	$htmlname        Name of html select area
 *  @return	void
 */
function listexamconclusion($nboflines,$newwidth=0,$htmlname='examconc')
{
    global $conf,$db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="list'.$htmlname.'" name="list'.$htmlname.'" '.(empty($conf->dol_use_jmobile)?' style="width: '.$newwidth.'px" ':'').'size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value="0"></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_c_examconclusion as s';
    $sql.= ' WHERE active = 1';
    $sql.= ' ORDER BY position, label';
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
 * @param 	int		$nboflines		Nb of lines
 * @param 	int		$newwidth		Force width
 * @param	string	$defaultvalue	Preselected value
 * @param	string	$htmlname		Name of HTML select field
 * @return	void
 */
function listebanques($nboflines,$newwidth=0,$defaultvalue='',$htmlname='banque')
{
    global $conf,$db,$width;

    if (empty($newwidth)) $newwidth=$width;

    print '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'" '.(empty($conf->dol_use_jmobile)?' style="width: '.$newwidth.'px" ':'').'size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
    print '<option value=""></option>';
    $sql = 'SELECT s.rowid, s.code, s.label';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_c_banques as s';
    $sql.= ' WHERE active = 1';
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
            $labeltoshow = trim($obj->label);
            print '<option value="'.dol_escape_htmltag($labeltoshow).'"';
            if ($defaultvalue == $labeltoshow) print ' selected="selected"';
            print '>'.dol_escape_htmltag($labeltoshow).'</option>';
            $i++;
        }
    }
    print '</select>'."\n";
}


/**
 * List contacts
 *
 * @param 	int		$nboflines		Nb of lines
 * @param 	int		$newwidth		Force width
 * @param	string	$htmlname		Name of HTML select field
 * @param	string	$selected		Preselected value
 * @return	void
 */
function listcontacts($nboflines,$newwidth=0,$htmlname='diagles',$selected='')
{
	global $conf,$db,$width;

	if (empty($newwidth)) $newwidth=$width;

	$out= '<select class="flat" id="list'.$htmlname.'" name="'.$htmlname.'" '.(empty($conf->dol_use_jmobile)?' style="width: '.$newwidth.'px" ':'').'size="'.$nboflines.'"'.($nboflines > 1?' multiple':'').'>';
	$out.= '<option value="0"></option>';
	$sql = 'SELECT s.rowid, s.code, s.label';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'cabinetmed_diaglec as s';
    $sql.= ' WHERE active = 1';
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
 *  Return array head with list of tabs to view object stats informations
 *
 *  @param	Object	$object         Patient or null
 *  @return	array           		head
 */
function patient_stats_prepare_head($object)
{
    global $langs, $conf, $user;

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath('/cabinetmed/stats/index.php?userid='.$user->id,1);
    $head[$h][1] = $langs->trans("Month");
    $head[$h][2] = 'statsconsultations';
    $h++;

    $head[$h][0] = dol_buildpath('/cabinetmed/stats/geo.php?mode=cabinetmedbytown',1);
    $head[$h][1] = $langs->trans('Town');
    $head[$h][2] = 'statstown';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'cabinetmed_stats');

    return $head;
}


/**
*  Return array head with list of tabs to view object stats informations
*
*  @param	Object	$object         Contact or null
*  @return	array           		head
*/
function contact_patient_stats_prepare_head($object)
{
    global $langs, $conf, $user;

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath('/cabinetmed/stats/index_contacts.php?userid='.$user->id,1);
    $head[$h][1] = $langs->trans("Patients");
    $head[$h][2] = 'statscontacts';
    $h++;

    /*
    $head[$h][0] = dol_buildpath('/cabinetmed/stats/geo.php?mode=cabinetmedbytown',1);
    $head[$h][1] = $langs->trans('Town');
    $head[$h][2] = 'statstown';
    $h++;
	*/

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'cabinetmed_stats_contacts');

    return $head;
}



/**
 * dol_cm_strptime
 *
 * @param 	string 	$text 		Text to parse
 * @param 	string	$format		Format (%d, %m, %Y)
 * @return	array				Array of component of date
 */
function dol_cm_strptime($text, $format)
{
	$result=array();
	$posday=strpos($format,'%d');
	$posmonth=strpos($format,'%m');
	$posyear=strpos($format,'%Y');
	//print 'format='.$format.' posday='.$posday.' posmonth='.$posmonth.' posyear='.$posyear;
	if ($posday >= 0) $result['tm_mday']=(int) substr($text,$posday,2);
	if ($posmonth >= 0) $result['tm_month']=((int) substr($text,$posmonth,2)) - 1;
	if ($posyear >= 0) $result['tm_year']=((int) substr($text,$posyear,4)) - 1900;
	if ($result['tm_mday'] < 0) $result['tm_mday']='';
	if ($result['tm_month'] < 0) $result['tm_month']='';
	if ($result['tm_year'] < 0) $result['tm_year']='';
	//var_dump($result);
	return $result;
}

?>