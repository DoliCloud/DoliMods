<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *  \file       htdocs/cabinetmed/reports.php
 *  \ingroup    cabinetmed
 *  \brief      List of consultation
 */


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
include_once("./class/cabinetmedcons.class.php");

$langs->load("companies");
$langs->load("customers");
$langs->load("suppliers");
$langs->load("commercial");

// Security check
$socid = GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user,'societe',$socid,'');

if (!$user->rights->cabinetmed->read) accessforbidden();

$sortfield = isset($_GET["sortfield"])?$_GET["sortfield"]:$_POST["sortfield"];
$sortorder = isset($_GET["sortorder"])?$_GET["sortorder"]:$_POST["sortorder"];
$page=isset($_GET["page"])?$_GET["page"]:$_POST["page"];
if ($page == -1) { $page = 0 ; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="DESC";
if (! $sortfield) $sortfield="c.datecons,c.rowid";

$search_nom=isset($_GET["search_nom"])?$_GET["search_nom"]:$_POST["search_nom"];
$search_ville=isset($_GET["search_ville"])?$_GET["search_ville"]:$_POST["search_ville"];
$search_code=isset($_GET["search_code"])?$_GET["search_code"]:$_POST["search_code"];

// Load sale and categ filters
$search_sale = GETPOST("search_sale");
$search_categ = GETPOST("search_categ");


/*
 * view
 */

$htmlother=new FormOther($db);
$thirdpartystatic=new Societe($db);
$consultstatic = new CabinetmedCons($db);


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
}

$sql = "SELECT s.rowid, s.nom as name, s.client, s.town, st.libelle as stcomm, s.prefix_comm, s.code_client,";
$sql.= " s.datec, s.canvas,";
$sql.= " c.rowid as cid, c.datecons, c.typepriseencharge, c.typevisit, c.motifconsprinc, c.diaglesprinc, c.examenprescrit, c.traitementprescrit";
// We'll need these fields in order to filter by sale (including the case where the user can only see his prospects)
if ($search_sale) $sql .= ", sc.fk_soc, sc.fk_user";
// We'll need these fields in order to filter by categ
if ($search_categ) $sql .= ", cs.fk_categorie, cs.fk_societe";
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."cabinetmed_cons as c,";
$sql.= " ".MAIN_DB_PREFIX."c_stcomm as st";
// We'll need this table joined to the select in order to filter by sale
if ($search_sale || !$user->rights->societe->client->voir) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
// We'll need this table joined to the select in order to filter by categ
if ($search_categ) $sql.= ", ".MAIN_DB_PREFIX."categorie_societe as cs";
$sql.= " WHERE s.fk_stcomm = st.id AND c.fk_soc = s.rowid";
$sql.= " AND s.client IN (1, 3)";
$sql.= " AND s.entity = ".$conf->entity;
if (!$user->rights->societe->client->voir && ! $socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
if ($socid) $sql.= " AND s.rowid = ".$socid;
if ($search_sale) $sql.= " AND s.rowid = sc.fk_soc";        // Join for the needed table to filter by sale
if ($search_categ) $sql.= " AND s.rowid = cs.fk_societe";   // Join for the needed table to filter by categ
if ($search_nom)   $sql.= " AND s.nom like '%".$db->escape(strtolower($search_nom))."%'";
if ($search_ville) $sql.= " AND s.town like '%".$db->escape(strtolower($search_ville))."%'";
if ($search_code)  $sql.= " AND s.code_client like '%".$db->escape(strtolower($search_code))."%'";
// Insert sale filter
if ($search_sale)
{
    $sql .= " AND sc.fk_user = ".$search_sale;
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

// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
    $result = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($conf->liste_limit +1, $offset);

$result = $db->query($sql);
if ($result)
{
    $num = $db->num_rows($result);

    $param = "&amp;search_nom=".$search_nom."&amp;search_code=".$search_code."&amp;search_ville=".$search_ville;
    if ($search_categ != '') $param.='&amp;search_categ='.$search_categ;
    if ($search_sale != '') $param.='&amp;search_sale='.$search_sale;

    print_barre_liste($langs->trans("ListOfConsultations"), $page, $_SERVER["PHP_SELF"],$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);

    $i = 0;

    //print '<div class="error">PAGE EN DEVELOPPEMENT ...</div><br>';

    print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
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
        $moreforfilter.=$langs->trans('SalesRepresentatives'). ': ';
        $moreforfilter.=$htmlother->select_salesrepresentatives($search_sale,'search_sale',$user);
    }
    if ($moreforfilter)
    {
        print '<tr class="liste_titre">';
        print '<td class="liste_titre" colspan="9">';
        print $moreforfilter;
        print '</td></tr>';
    }

    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans("Company"),$_SERVER["PHP_SELF"],"s.nom","",$param,"",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("CustomerCode"),$_SERVER["PHP_SELF"],"s.code_client","",$param,"",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("IdConsultShort"),$_SERVER["PHP_SELF"],"c.rowid","",$param,"",$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("DateConsultationShort"),$_SERVER["PHP_SELF"],"c.datecons,c.rowid","",$param,'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Prise en charge'),$_SERVER['PHP_SELF'],'c.typepriseencharge','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("MotifPrincipal"),$_SERVER["PHP_SELF"],"c.motifconsprinc","",$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("DiagLesPrincipal"),$_SERVER["PHP_SELF"],"","",$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('ConsultActe'),$_SERVER['PHP_SELF'],'c.typevisit','',$param,'align="right"',$sortfield,$sortorder);
    print "</tr>\n";

    print '<tr class="liste_titre">';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="8" name="search_nom" value="'.$search_nom.'">';
    print '</td><td class="liste_titre">';
    print '<input type="text" class="flat" size="8" name="search_code" value="'.$search_code.'">';
    print '</td>';
    print '<td class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td class="liste_titre">';
    print '&nbsp;';
    print '</td>';
    print '<td class="liste_titre">';
    print '&nbsp;';
    print '</td>';
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
        $thirdpartystatic->id=$obj->rowid;
        $thirdpartystatic->name=$obj->name;
        $thirdpartystatic->client=$obj->client;
        $thirdpartystatic->canvas=$obj->canvas;
        print $thirdpartystatic->getNomUrl(1);
        print '</td>';
        print '<td>'.$obj->code_client.'</td>';
        print '<td>';
        $consultstatic->id=$obj->cid;
        $consultstatic->fk_soc=$obj->rowid;
        print $consultstatic->getNomUrl(1,'&amp;backtopage='.urlencode($_SERVER["PHP_SELF"]));
        print '</td>';
        print '<td align="center">'.dol_print_date($obj->datecons,'day').'</td>';
        print '<td>';
        print $obj->typepriseencharge;
        print '</td>';
        print '<td>'.$obj->motifconsprinc.'</td>';
        print '<td>';
        print dol_trunc($obj->diaglesprinc,20);
        /*$val=dol_trunc($obj->examenprescrit,20);
        if ($val) $val.='<br>';
        $val=dol_trunc($obj->traitementprescrit,20);*/
        print '</td>';
        print '<td align="right">';
        print $obj->typevisit;
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
?>
