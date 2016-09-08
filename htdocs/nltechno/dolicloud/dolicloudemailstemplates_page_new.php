<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       htdocs/nltechno/dolicloud/dolicloudemailstemplates_page.php
 *		\ingroup    nltechno
 *		\brief      This file is an example of a php page
 */

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
dol_include_once('/nltechno/class/dolicloudemailstemplates.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) {
	$page = 0;
}
$offset = $conf->liste_limit * $page;
if (! $sortorder) $sortorder='ASC';
if (! $sortfield) $sortfield='t.emailtype';
$limit = GETPOST('limit')?GETPOST('limit','int'):$conf->liste_limit;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add')
{
	$object=new Dolicloudemailstemplates($db);
	$object->prop1=$_POST["field1"];
	$object->prop2=$_POST["field2"];
	$result=$object->create($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$object->error;
	}
}

if ($action == 'update')
{
	$object=new Dolicloudemailstemplates($db);
	$object->prop1=$_POST["field1"];
	$object->prop2=$_POST["field2"];
	$result=$object->update($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$object->error;
	}
}





/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$form=new Form($db);
$object = new Dolicloudemailstemplates($db);

llxHeader('',$langs->transnoentitiesnoconv('EMailsTemplates'),'');


// Put here content of your page

// Example 1 : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_needroot();
	});
});
</script>';




// Edit
if ($action == 'edit' && $id)
{
	dol_fiche_head(array(), '', $langs->trans('EMailTemplate'), 0, 'generic');

	$object->fetch($id);

	print '<table class="border" width="100%">';
	print '<tr><td>'.$langs->trans("Ref").'</td><td>'.$object->id.'</td></tr>';
	print '<tr><td>'.$langs->trans("Lang").'</td><td>'.$object->lang.'</td></tr>';
	print '<tr><td>'.$langs->trans("Type").'</td><td>'.$object->emailtype.'</td></tr>';
	print '<tr><td>'.$langs->trans("Topic").'</td><td>'.$object->topic.'</td></tr>';
	print '<tr><td>'.$langs->trans("Content").'</td><td>'.$object->content.'</td></tr>';
	print '</table>';

	dol_fiche_end();
}

// View
if (($action == 'view' || empty($action)) && $id)
{
	dol_fiche_head(array(), '', $langs->trans('EMailTemplate'), 0, 'generic');

	$object->fetch($id);

	print '<table class="border" width="100%">';
	print '<tr><td>'.$langs->trans("Ref").'</td><td>'.$object->id.'</td></tr>';
	print '<tr><td>'.$langs->trans("Lang").'</td><td>'.$object->lang.'</td></tr>';
	print '<tr><td>'.$langs->trans("Type").'</td><td>'.$object->emailtype.'</td></tr>';
	print '<tr><td>'.$langs->trans("Topic").'</td><td>'.$object->topic.'</td></tr>';
	print '<tr><td>'.$langs->trans("Content").'</td><td>'.$object->content.'</td></tr>';
	print '</table>';

	dol_fiche_end();

	print '<div class="tabsAction">';

	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=edit">'.$langs->trans('Modify').'</a>';

	print '</div>';

}


// List of data
if ($action == 'list')
{
	print_fiche_titre($langs->trans("EMailsTemplates")).'<br>';

	$sql = "SELECT";
    $sql.= " t.rowid,";

	$sql.= " t.emailtype,";
	$sql.= " t.lang,";
	$sql.= " t.topic,";
	$sql.= " t.content";

    $sql.= " FROM ".MAIN_DB_PREFIX."dolicloud_emailstemplates as t";
    //$sql.= " WHERE field3 = 'xxx'";
    $sql.= $db->order($sortfield, $sortorder);

    $param='&action=list';

    print '<table class="noborder">'."\n";
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans('ID'),$_SERVER['PHP_SELF'],'t.rowid','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('lang'),$_SERVER['PHP_SELF'],'t.lang','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('emailtype'),$_SERVER['PHP_SELF'],'t.emailtype','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('topic'),$_SERVER['PHP_SELF'],'t.topic','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre('',$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
    print '</tr>';

    dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {
        	$var=true;
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    // You can use here results
                	$var=!$var;
                    print '<tr '.$bc[$var].'><td>';
                    print $obj->rowid;
                    print '</td><td>';
                    print $obj->lang;
                    print '</td><td>';
                    print $obj->emailtype;
                    print '</td><td>';
                    print $obj->topic;
                    print '</td><td align="right">';
                    print '<a href="'.$_SERVER["PHP_SELF"].'?action=view&id='.$obj->rowid.'">'.img_edit().'</a>';
                    print '</td></tr>';
                }
                $i++;
            }
        }
    }
    else
    {
        $error++;
        dol_print_error($db);
    }

    print '</table>'."\n";
}



// End of page
llxFooter();
$db->close();
