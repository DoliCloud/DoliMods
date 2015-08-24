<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 *	\file       htdocs/cabinetmed/listconsult.php
 *	\ingroup    cabinetmed
 *	\brief      List of consultation
 */


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
require_once("./class/cabinetmedcons.class.php");
require_once("./lib/cabinetmed.lib.php");

$langs->load("companies");
$langs->load("customers");
$langs->load("suppliers");
$langs->load("commercial");

// Security check
$socid = GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user,'societe',$socid,'');

if (!$user->rights->cabinetmed->read) accessforbidden();

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$page=GETPOST("page");
if ($page == -1) { $page = 0 ; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="DESC";
if (! $sortfield) $sortfield="c.datecons,c.rowid";

$search_nom=GETPOST("search_nom");
$search_ville=GETPOST("search_ville");
$search_code=GETPOST("search_code");
$search_ref=GETPOST("search_ref");

// Load sale and categ filters
$search_sale = GETPOST("search_sale");
$search_categ = GETPOST("search_categ");
$search_motifprinc = GETPOST("search_motifprinc");
$search_diaglesprinc = GETPOST("search_diaglesprinc");
$search_contactid = GETPOST("search_contactid");


/*
 * view
 */

$form=new Form($db);
$htmlother=new FormOther($db);
$thirdpartystatic=new Societe($db);
$consultstatic = new CabinetmedCons($db);

$datecons=dol_mktime(0,0,0,GETPOST('consmonth'),GETPOST('consday'),GETPOST('consyear'));

llxHeader();

// Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x"))
{
    $search_categ='';
    $search_sale='';
    $socname="";
    $search_nom="";
    $search_ville="";
    $search_idprof1='';
    $search_idprof2='';
    $search_idprof3='';
    $search_idprof4='';
    $search_motifprinc='';
    $search_diaglesprinc='';
    $search_contactid='';
    $datecons='';
}

$sql = "SELECT s.rowid, s.nom as name, s.client, s.town, st.libelle as stcomm, s.prefix_comm, s.code_client,";
$sql.= " s.datec, s.canvas,";
$sql.= " c.rowid as cid, c.datecons, c.typepriseencharge, c.typevisit, c.motifconsprinc, c.diaglesprinc, c.examenprescrit, c.traitementprescrit";
// We'll need these fields in order to filter by categ
if ($search_categ) $sql .= ", cs.fk_categorie, cs.fk_societe";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
$sql.= ", ".MAIN_DB_PREFIX."cabinetmed_cons as c,";
$sql.= " ".MAIN_DB_PREFIX."c_stcomm as st";
// We'll need this table joined to the select in order to filter by categ
if ($search_categ) $sql.= ", ".MAIN_DB_PREFIX."categorie_societe as cs";
$sql.= " WHERE s.fk_stcomm = st.id AND c.fk_soc = s.rowid";
$sql.= " AND s.client IN (1, 3)";
$sql.= ' AND s.entity IN ('.getEntity('societe', 1).')';
if ($datecons > 0) $sql.=" AND c.datecons = '".$db->idate($datecons)."'";
if ($search_motifprinc)
{
	$label= dol_getIdFromCode($db,$search_motifprinc,'cabinetmed_motifcons','code','label');
	$sql.= " AND c.motifconsprinc LIKE '%".$db->escape($label)."%'";
}
if ($search_diaglesprinc)
{
	$label= dol_getIdFromCode($db,$search_diaglesprinc,'cabinetmed_diaglec','code','label');
	$sql.= " AND c.diaglesprinc LIKE '%".$db->escape($label)."%'";
}
if ($search_contactid)
{

}
if (!$user->rights->societe->client->voir && ! $socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
if ($socid) $sql.= " AND s.rowid = ".$socid;
if ($search_ref)   $sql.= " AND c.rowid = ".$db->escape($search_ref);
if ($search_categ) $sql.= " AND s.rowid = cs.fk_societe";	// Join for the needed table to filter by categ
if ($search_nom)   $sql.= " AND s.nom like '%".$db->escape(strtolower($search_nom))."%'";
if ($search_ville) $sql.= " AND s.town like '%".$db->escape(strtolower($search_ville))."%'";
if ($search_code)  $sql.= " AND s.code_client like '%".$db->escape(strtolower($search_code))."%'";
// Insert sale filter
if ($search_sale)
{
	$sql .= " AND c.fk_user = ".$search_sale;
}
// Insert categ filter
if ($search_categ)
{
	$sql .= " AND cs.fk_categorie = ".$search_categ;
}
if ($socname)
{
	$sql.= " AND s.nom like '%".$db->escape(strtolower($socname))."%'";
	$sortfield = "s.nom";
	$sortorder = "ASC";
}
//if ($search_contactid) $sql.=", ".MAIN_DB_PREFIX."element_contact as ec, ".MAIN_DB_PREFIX."c_type_contact as tc";
//if ($search_contactid) $sql.= " AND ec.element_id = s.rowid AND ec.fk_socpeople = ".$search_contactid." AND ec.fk_c_type_contact = tc.rowid AND tc.element='societe'";
if ($search_contactid)
{
	$sql .= " AND s.rowid IN (SELECT ec.element_id FROM ".MAIN_DB_PREFIX."element_contact as ec, ".MAIN_DB_PREFIX."c_type_contact as tc WHERE ec.fk_socpeople = ".$search_contactid." AND ec.fk_c_type_contact = tc.rowid AND tc.element='societe')";
}
// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($conf->liste_limit +1, $offset);
//print $sql;

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);

	if ($search_nom != '') $param = "&amp;search_nom=".$search_nom;
	if ($search_code != '') $param.= "&amp;search_code=".$search_code;
	if ($search_ville != '') $param.= "&amp;search_ville=".$search_ville;
 	if ($search_categ != '') $param.='&amp;search_categ='.$search_categ;
 	if ($search_sale != '')	$param.='&amp;search_sale='.$search_sale;
 	if ($search_motifprinc != '')	$param.='&amp;search_motifprinc='.urlencode($search_motifprinc);
 	if ($search_diaglesprinc != '')	$param.='&amp;search_diaglesprinc='.urlencode($search_diaglesprinc);
 	if ($search_contactid != '')	$param.='&amp;search_contactid='.$search_contactid;

	print_barre_liste($langs->trans("ListOfConsultations"), $page, $_SERVER["PHP_SELF"],$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);

	$i = 0;

	//print '<div class="error">PAGE EN DEVELOPPEMENT ...</div><br>';

	print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	print '<table class="liste" width="100%">'."\n";

	// Filter on categories
 	$moreforfilter='';
	if ($conf->categorie->enabled)
	{
	 	$moreforfilter.=$langs->trans('Categories'). ': ';
		$moreforfilter.=$htmlother->select_categories(2,$search_categ,'search_categ');
	 	$moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
	}
 	// If the user can view prospects other than his'
 	if ($user->rights->societe->client->voir || $socid)
 	{
	 	$moreforfilter.=$langs->trans('ConsultCreatedBy'). ': ';
		$moreforfilter.=$htmlother->select_salesrepresentatives($search_sale,'search_sale',$user);
	 	$moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
 	}
 	// To add filter on diagnostic
 	//$width="200";
 	//$moreforfilter.=$langs->trans('DiagnostiqueLesionnel'). ': ';
 	//$moreforfilter.=listdiagles(1,$width,'search_diagles',$search_diagles);
	//$moreforfilter.=' &nbsp; &nbsp; &nbsp; ';
 	// To add filter on contact
	$width="200";
 	$moreforfilter.=$langs->trans('Correspondants'). ': ';
	$moreforfilter.=$form->selectcontacts(0, $search_contactid, 'search_contactid', 1, '', '', 1);
 	// More filters
 	if ($moreforfilter)
	{
		print '<tr class="liste_titre">';
		print '<td class="liste_titre" colspan="9">';
	    print $moreforfilter;
	    print '</td></tr>';
	}

	print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans("IdConsultShort"),$_SERVER["PHP_SELF"],"c.rowid","",$param,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Patient"),$_SERVER["PHP_SELF"],"s.nom","",$param,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("CustomerCode"),$_SERVER["PHP_SELF"],"s.code_client","",$param,"",$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("DateConsultationShort"),$_SERVER["PHP_SELF"],"c.datecons,c.rowid","",$param,'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("MotifPrincipal"),$_SERVER["PHP_SELF"],"c.motifconsprinc","",$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("DiagLesPrincipal"),$_SERVER["PHP_SELF"],"c.diaglesprinc","",$param,'',$sortfield,$sortorder);
    if (! empty($conf->global->CABINETMED_FRENCH_PRISEENCHARGE))
    {
	    print_liste_field_titre($langs->trans('Prise en charge'),$_SERVER['PHP_SELF'],'c.typepriseencharge','',$param,'',$sortfield,$sortorder);
    }
	print_liste_field_titre($langs->trans('ConsultActe'),$_SERVER['PHP_SELF'],'c.typevisit','',$param,'align="right"',$sortfield,$sortorder);
	print "</tr>\n";

	print '<tr class="liste_titre">';
    print '<td class="liste_titre">';
	print '<input type="text" class="flat" size="6" name="search_ref" value="'.$search_ref.'">';
    print '</td>';
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" size="8" name="search_nom" value="'.$search_nom.'">';
	print '</td><td class="liste_titre">';
	print '<input type="text" class="flat" size="8" name="search_code" value="'.$search_code.'">';
	print '</td>';
	// Date
	print '<td class="liste_titre" align="center">';
	print $form->select_date($datecons, 'cons', 0, 0, 1, '',1,0,1);
	print '</td>';
    print '<td class="liste_titre">';
    $width='200';
    print listmotifcons(1,$width,'search_motifprinc',$search_motifprinc);
    print '</td>';
    print '<td class="liste_titre">';
    $width='200';
    print listdiagles(1,$width,'search_diaglesprinc',$search_diaglesprinc);
    print '</td>';
    if (! empty($conf->global->CABINETMED_FRENCH_PRISEENCHARGE))
    {
    	print '<td class="liste_titre">';
        print '&nbsp;';
        print '</td>';
    }
    print '<td class="liste_titre" align="right"><input class="liste_titre" type="image" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print '&nbsp; ';
    print '<input type="image" class="liste_titre" name="button_removefilter" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/searchclear.png" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
    print '</td>';
    print "</tr>\n";

	$var=True;

	while ($i < min($num,$conf->liste_limit))
	{
		$obj = $db->fetch_object($result);

		$var=!$var;

		print "<tr ".$bc[$var].">";
        print '<td>';
        $consultstatic->id=$obj->cid;
        $consultstatic->fk_soc=$obj->rowid;
        print $consultstatic->getNomUrl(1,'&amp;backtopage='.urlencode($_SERVER["PHP_SELF"]));
        print '</td>';
		print '<td>';
		$thirdpartystatic->id=$obj->rowid;
        $thirdpartystatic->name=$obj->name;
        $thirdpartystatic->client=$obj->client;
        $thirdpartystatic->canvas=$obj->canvas;
        print $thirdpartystatic->getNomUrl(1);
		print '</td>';
		print '<td>'.$obj->code_client.'</td>';
		print '<td align="center">'.dol_print_date($db->jdate($obj->datecons),'day').'</td>';
        print '<td>'.$obj->motifconsprinc.'</td>';
        print '<td>';
        print dol_trunc($obj->diaglesprinc,20);
        /*$val=dol_trunc($obj->examenprescrit,20);
        if ($val) $val.='<br>';
        $val=dol_trunc($obj->traitementprescrit,20);*/
        print '</td>';
	    if (! empty($conf->global->CABINETMED_FRENCH_PRISEENCHARGE))
        {
    		print '<td>';
            print $obj->typepriseencharge;
            print '</td>';
        }
        print '<td align="right">';
        print $langs->trans($obj->typevisit);
        print '</td>';
        print "</tr>\n";
		$i++;
	}
	//print_barre_liste($langs->trans("ListOfCustomers"), $page, $_SERVER["PHP_SELF"],'',$sortfield,$sortorder,'',$num);
	print "</table>\n";
	print "</form>\n";
	$db->free($result);
}
else
{
	dol_print_error($db);
}


llxFooter();

$db->close();
