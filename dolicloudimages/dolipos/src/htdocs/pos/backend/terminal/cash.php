<?php
/* Copyright (C) 2011 		Juanjo Menent <jmenent@2byte.es>
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
 *	\file       htdocs/pos/backend/terminal/cash.php
 *	\ingroup    pos
 *	\brief      Page to show a terminal
 *	\version    $Id: cash.php,v 1.4 2011-08-19 07:54:24 jmenent Exp $
 */

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
dol_include_once('/pos/backend/class/cash.class.php');

$action=GETPOST('action');
// Security check
$cashid = GETPOST("cashid");

if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user,'societe',$socid,'');

$search_name=trim(GETPOST('search_name'));
$search_user=trim(GETPOST('search_user'));
$cashname=trim(GETPOST('cashname'));
$sortfield = GETPOST('sortfield');
$sortorder = GETPOST('sortorder');
$page=GETPOST('page');

if (! $sortorder) $sortorder="ASC";
if (! $sortfield) $sortfield="name";
if ($page == -1) { $page = 0 ; }

$offset = $conf->liste_limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

$langs->load("pos@pos");

/*
 * Actions
 */

//free terminal
if($action=='freeterminal')
{
	$cash= new Cash($db);
	$cash->fetch(GETPOST('id','int'));
	$cash->set_unused($user);
}

if($action=='blockterminal')
{
	$cash= new Cash($db);
	$cash->fetch(GETPOST('id','int'));
	$cash->set_used($user);
}

// Recherche
$mode=GETPOST('mode');
$modesearch=GETPOST('mode-search');

if ($mode == 'search')
{
	$_POST["search_name"]=$cashname;

	$sql = "SELECT rowid";
	$sql.= " FROM ".MAIN_DB_PREFIX."pos_cash";
	$sql.= " WHERE (";
	$sql.= " name like '%".$db->escape($cashname)."%'";
	$sql.= ")";
	$sql.= " AND entity = ".$conf->entity;
	if (!$user->rights->societe->client->voir && !$cash) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
	if ($cashid) $sql.= " AND s.rowid = ".$cashid;
  
	$result=$db->query($sql);
	if ($result)
	{
		if ($db->num_rows($result) == 1)
		{
			$obj = $db->fetch_object($result);
			$cashid = $obj->rowid;
			header("Location: ".DOL_URL_ROOT."/pos/backend/cash.php?cashid=".$cashid);
			exit;
		}
		$db->free($result);
	}
}

/*
 * View
 */

$form=new Form($db);
$htmlother=new FormOther($db);
$cashstatic=new Cash($db);
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('',$langs->trans("Cash"),$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

// Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x"))
{

    $cashname="";
	$search_name="";
}

if ($cashname)
{
	$search_name=$cashname;
}

/*
 * Mode Liste
 */

$title=$langs->trans("ListOfCash");

$sql = "SELECT rowid, name, tactil, is_used, fk_user_u";

$sql.= " FROM ".MAIN_DB_PREFIX."pos_cash";
$sql.= " WHERE entity = ".$conf->entity;

if ($cashid)	$sql.= " AND rowid = ".$cashid;

if ($search_name)
{
	$sql.= " AND (";
	$sql.= "name LIKE '%".$db->escape($search_name)."%'";
	$sql.= ")";
}


// Count total nb of records
$nbtotalofrecords = 0;

if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($conf->liste_limit+1, $offset);

$resql = $db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	$i = 0;

	$params = "&amp;cashname=".$socname."&amp;search_namem=".$search_name;

	print_barre_liste($title, $page, $_SERVER["PHP_SELF"],$params,$sortfield,$sortorder,'',$num,$nbtotalofrecords);

	$langs->load("other");
	$textprofid=array();
	

	print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" name="formfilter">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<table class="liste" width="100%">';

    
    // Lines of titles
    print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("CashS"),$_SERVER["PHP_SELF"],"name","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Device"),$_SERVER["PHP_SELF"],"tactil","",$params,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("IsUsed"),$_SERVER["PHP_SELF"],"is_used","",$params,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("User"),$_SERVER["PHP_SELF"],"fk_user_u","",$params,'',$sortfield,$sortorder);
	print '<td></td>';
	print '<td></td>';
//	print_liste_field_titre($langs->trans("Status"),$_SERVER["PHP_SELF"],"s.status","",$params,'align="right"',$sortfield,$sortorder);
	print "</tr>\n";

	// Lignes des champs de filtre
	print '<tr class="liste_titre">';
	print '<td class="liste_titre">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	print '<input class="flat" type="text" name="search_name" value="'.$search_name.'">';
	print '</td>';
	print '<td></td>';
	print '<td></td>';
	print '<td></td>';
	print '<td></td>';
	print '<td colspan="2" class="liste_titre" align="right">';
	print '<input type="image" class="liste_titre" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '&nbsp; ';
	print '<input type="image" class="liste_titre" name="button_removefilter" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/searchclear.png" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
	print '</td>';
	print "</tr>\n";

	$var=True;

	while ($i < min($num,$conf->liste_limit))
	{

		
		$obj = $db->fetch_object($resql);
		
		$cashwil = new Cash($db);
		$cashwil->fetch($obj->rowid);
		
		$var=!$var;
		print "<tr $bc[$var]>";
		
		print '<td width="30%">'.$cashwil->getNomUrl(1).'</td>';
		print "<td>".$cashwil->tactiltype($obj->tactil)."</td>\n";
		
        print '<td align="left">'.$cashwil->getLibStatut(4).'</td>';
        
        $url = 'cash.php?id='.$obj->rowid;
        if(!$cashwil->is_closed)
        {
	        if($cashwil->fk_user_u)
	        {
	        	$userstatic=new User($db);
	        	$userstatic->fetch($cashwil->fk_user_u); 
	        	if($user->rights->pos->backend)
	        	{
					print "<td>".$userstatic->getNomUrl(1)."</td>\n";
					print '<td align="center">';
					print '<form action="'.$url.'" name="free" method="POST">';
					print '<input type="hidden" name="id" value="'.$cashwil->id.'">'	;
					print '<input type="hidden" name="action" value="freeterminal">';
					print '<input class="button" type="submit" value="'.$langs->trans("FreeIt").'">';
					print '</form>';	
					print '</td>';
	        	}
	        	else
	        	{
	        		print "<td></td>\n";
	        	}
	        }
	        elseif ($cashwil->is_used)
	        {
	        	print '<td></td>';
	        	print '<td align="center">';
	        	print '<form action="'.$url.'" name="free" method="POST">';
	        	print '<input type="hidden" name="id" value="'.$cashwil->id.'">'	;
	        	print '<input type="hidden" name="action" value="freeterminal">';
	        	print '<input class="button" type="submit" value="'.$langs->trans("FreeIt").'">';
	        	print '</form>';
	        	print '</td>';
	        }
	        else 
	        {
	        	if($user->rights->pos->backend)
	        	{
	        		print "<td></td>\n";
	        		print '<td align="center">';
					print '<form action="'.$url.'" name="free" method="POST">';
					print '<input type="hidden" name="id" value="'.$cashwil->id.'">'	;
					print '<input type="hidden" name="action" value="blockterminal">';
					print '<input class="button" type="submit" value="'.$langs->trans("BlockIt").'">';
					print '</form>';	
					print '</td>';
	        	}
	        	else
	        	{
	        		print "<td></td>\n";
	        		print "<td></td>\n";
	        	}
	        }
		}
        print '<td></td>';
		print '</tr>'."\n";
		$i++;
	}

	$db->free($resql);

	print "</table>";

	print '</form>';

}
else
{
	dol_print_error($db);
}


llxFooter();

$db->close();
?>