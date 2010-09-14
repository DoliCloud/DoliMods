<?php
/* Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2003,2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
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
 *      \file       htdocs/newssubmitter/admin/newssubmittersetuppage.php
 *      \ingroup    newssubmitter
 *      \brief      Page to setup module NewsSubmitter
 *      \version    $Id: submiteverywheresetuppage.php,v 1.2 2010/09/14 20:42:43 eldy Exp $
 */

$res=false;
if (file_exists("../../main.inc.php") && ! $res) $res=@include("../../main.inc.php");
if (file_exists("../../../../dolibarr/htdocs/main.inc.php") && ! $res) $res=@include("../../../../dolibarr/htdocs/main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php");


$langs->load("admin");
$langs->load("submiteverywhere@submiteverywhere");

if (!$user->admin) accessforbidden();




/*
 * Action
 */

if ($_POST["action"] == 'add' || $_POST["modify"])
{
    $external_rss_urlrss = "external_rss_urlrss_" . $_POST["norss"];

    if(isset($_POST[$external_rss_urlrss]))
    {
        $boxlabel='(ExternalRSSInformations)';
        $external_rss_title = "external_rss_title_" . $_POST["norss"];
        //$external_rss_url = "external_rss_url_" . $_POST["norss"];

        $db->begin();

		if ($_POST["modify"])
		{
			// Supprime boite box_external_rss de definition des boites
/*	        $sql = "UPDATE ".MAIN_DB_PREFIX."boxes_def";
			$sql.= " SET name = '".$boxlabel."'";
	        $sql.= " WHERE file ='box_external_rss.php' AND note like '".$_POST["norss"]." %'";

			$resql=$db->query($sql);
			if (! $resql)
	        {
				dol_print_error($db,"sql=$sql");
				exit;
	        }
*/
		}
		else
		{
			// Ajoute boite box_external_rss dans definition des boites
	        $sql = "INSERT INTO ".MAIN_DB_PREFIX."boxes_def (file, note)";
			$sql.= " VALUES ('box_external_rss.php','".addslashes($_POST["norss"].' ('.$_POST[$external_rss_title]).")')";
	        if (! $db->query($sql))
	        {
	        	dol_print_error($db);
	            $err++;
	        }
		}

		$result1=dolibarr_set_const($db, "EXTERNAL_RSS_TITLE_" . $_POST["norss"],$_POST[$external_rss_title],'chaine',0,'',$conf->entity);
		if ($result1) $result2=dolibarr_set_const($db, "EXTERNAL_RSS_URLRSS_" . $_POST["norss"],$_POST[$external_rss_urlrss],'chaine',0,'',$conf->entity);

        if ($result1 && $result2)
        {
            $db->commit();
	  		//$mesg='<div class="ok">'.$langs->trans("Success").'</div>';
            header("Location: ".$_SERVER["PHP_SELF"]);
            exit;
        }
        else
        {
            $db->rollback();
            dol_print_error($db);
        }
    }
}

if ($_POST["delete"])
{
    if(isset($_POST["norss"]))
    {
        $db->begin();

		// Supprime boite box_external_rss de definition des boites
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."boxes_def";
        $sql.= " WHERE file ='box_external_rss.php' AND note like '".$_POST["norss"]." %'";

		$resql=$db->query($sql);
		if ($resql)
        {
			$num = $db->num_rows($resql);
			$i=0;
			while ($i < $num)
			{
				$obj=$db->fetch_object($resql);

		        $sql = "DELETE FROM ".MAIN_DB_PREFIX."boxes";
		        $sql.= " WHERE box_id = ".$obj->rowid;
				$resql=$db->query($sql);

		        $sql = "DELETE FROM ".MAIN_DB_PREFIX."boxes_def";
		        $sql.= " WHERE rowid = ".$obj->rowid;
				$resql=$db->query($sql);

				if (! $resql)
				{
					$db->rollback();
					dol_print_error($db,"sql=$sql");
					exit;
				}

				$i++;
			}

			$db->commit();
		}
		else
		{
			$db->rollback();
			dol_print_error($db,"sql=$sql");
			exit;
        }


		$result1=dolibarr_del_const($db,"EXTERNAL_RSS_TITLE_" . $_POST["norss"],$conf->entity);
		if ($result1) $result2=dolibarr_del_const($db,"EXTERNAL_RSS_URLRSS_" . $_POST["norss"],$conf->entity);

        if ($result1 && $result2)
        {
            $db->commit();
	  		//$mesg='<div class="ok">'.$langs->trans("Success").'</div>';
            header("Location: external_rss.php");
            exit;
        }
        else
        {
            $db->rollback();
            dol_print_error($db);
        }
    }
}


/*
 * View
 */

$htmladmin=new FormAdmin($db);

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SubmitEveryWhereSetup"), $linkback, 'setup');
print '<br>';

// Form to add entry
print '<form name="externalrssconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

print '<table class="nobordernopadding" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Label").'</td>';
print '<td align="left">'.$langs->trans("TargetType").'</td>';
print '<td align="left">'.$langs->trans("TargetLang").'</td>';
print '</tr>';

print '<tr class="liste_titre">';
// Label
print '<td>';
print '<input type="text" name="label" value="'.($_POST["label"]?$_POST["label"]:'').'">';
print '</td>';
// Type
print '<td align="left">';
print '<select class="flat" name="type">';
print '<option value="">&nbsp;</option>';
print '<option value="dig">'.$langs->trans("Dig").'</option>';
print '<option value="email">'.$langs->trans("Email").'</option>';
print '<option value="facebook">'.$langs->trans("Facebook").'</option>';
print '<option value="linkedin">'.$langs->trans("LinkedIn").'</option>';
print '<option value="twitter">'.$langs->trans("Twitter").'</option>';
print '<option value="web">'.$langs->trans("GenericWebSite").'</option>';
print '</select>';
print '</td>';
// Language
print '<td align="left">';
print $htmladmin->select_language($langs->defaultlang);
print '</td>';

print '</tr>';

print '<tr><td colspan="3" align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
print '<input type="hidden" name="action" value="add">';
print '</td>';
print '</tr>';

print '</table>';
print '</form>';


print '<br>';








print '<table class="nobordernopadding" width="100%">';

$sql ="SELECT rowid, label, targetcode, langcode, url, login, pass, comment, position";
$sql.=" FROM ".MAIN_DB_PREFIX."submiteverywhere_targets";
$sql.=" ORDER BY label";

dol_syslog("Get list of targets sql=".$sql,LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
	$num =$db->num_rows($resql);
	$i=0;

	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);

		print "<form name=\"externalrssconfig\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

		print "<tr class=\"liste_titre\">";
		print "<td colspan=\"2\">".$langs->trans("RSS")." ".($i+1)."</td>";
		print "</tr>";

		$var=!$var;
		print "<tr ".$bc[$var].">";
		print "<td width=\"100\">".$langs->trans("Title")."</td>";
		print "<td><input type=\"text\" class=\"flat\" name=\"external_rss_title_" . $idrss . "\" value=\"" . @constant("EXTERNAL_RSS_TITLE_" . $idrss) . "\" size=\"64\"></td>";
		print "</tr>";

		$var=!$var;
		print "<tr ".$bc[$var].">";
		print "<td>".$langs->trans("URL")."</td>";
		print "<td><input type=\"text\" class=\"flat\" name=\"external_rss_urlrss_" . $idrss . "\" value=\"" . @constant("EXTERNAL_RSS_URLRSS_" . $idrss) . "\" size=\"64\"></td>";
		print "</tr>";

		$var=!$var;
		print "<tr ".$bc[$var].">";
		print "<td>".$langs->trans("Status")."</td>";
		print "<td>";
	    if (! $rss->ERROR)
	    {
			print '<font class="ok">'.$langs->trans("Online").'</div>';
		}
		else
		{
			print '<font class="error">'.$langs->trans("Offline").'</div>';
		}
		print "</td>";
		print "</tr>";

		print "<tr>";
		print "<td colspan=\"2\" align=\"center\">";
		print "<input type=\"submit\" class=\"button\" name=\"modify\" value=\"".$langs->trans("Modify")."\">";
		print " &nbsp; ";
		print "<input type=\"submit\" class=\"button\" name=\"delete\" value=\"".$langs->trans("Delete")."\">";
		print "<input type=\"hidden\" name=\"norss\"  value=\"".$idrss."\">";
		print "</td>";
		print "</tr>";

		print "</form>";

		$i++;
	}
}
else
{
	dol_print_error($db);
}

print '</table>'."\n";


$db->close();

llxFooter('$Date: 2010/09/14 20:42:43 $ - $Revision: 1.2 $');
?>
