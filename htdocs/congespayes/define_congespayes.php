<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2011      Dimitri Mouillard <dmouillard@teclib.com>
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
 *   	\file       define_congespayes.php
 *		\ingroup    congespayes
 *		\brief      File that defines the balance of paid leave of users.
 *		\version    $Id: define_congespayes.php,v 1.00 2011/09/15 11:00:00 dmouillard Exp $
 *		\author		dmouillard@teclib.com <Dimitri Mouillard>
 *		\remarks	   File that defines the balance of paid leave of users.
 */

require('pre.inc.php');
require_once(DOL_DOCUMENT_ROOT. "/user/class/user.class.php");

// Protection if external user
if ($user->societe_id > 0) accessforbidden();

// Si l'utilisateur n'a pas le droit de lire cette page
if(!$user->rights->congespayes->define_conges) accessforbidden();


/*
 * View
*/

llxHeader($langs->trans('CPTitreMenu'));

print_fiche_titre($langs->trans('MenuConfCP'));

$congespayes = new Congespayes($db);
$listUsers = $congespayes->fetchUsers(false,false);
$userstatic=new User($db);

// Si il y a une action de mise à jour
if(isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['update_cp'])) {

    $userID = array_keys($_POST['update_cp']);
    $userID = $userID[0];

    $userValue = $_POST['nb_conges'];
    $userValue = $userValue[$userID];

    if(!empty($userValue)) {
        $userValue = price2num($userValue,2);
    } else {
        $userValue = 0;
    }

    // On ajoute la modification dans le LOG
    $congespayes->addLogCP($user->id,$userID,'Event : Manual update',$userValue);

    // Mise à jour des congés de l'utilisateur
    $congespayes->updateSoldeCP($userID,$userValue);


    print '<div class="tabBar">';
    print $langs->trans('UpdateConfCPOK');
    print '</div>';


} elseif(isset($_POST['action']) && $_POST['action'] == 'add_event') {

    $error = false;

    if(!empty($_POST['list_event']) && $_POST['list_event'] > 0) {
        $event = $_POST['list_event'];
    } else { $error = true;
    }

    if(!empty($_POST['userCP']) && $_POST['userCP'] > 0) {
        $userCP = $_POST['userCP'];
    } else { $error = true;
    }

    if($error) {
        $message = $langs->trans('ErrorAddEventToUserCP');
    } else {

        $nb_conges = $congespayes->getCPforUser($userCP);
        $add_conges = $congespayes->getValueEventCp($event);
        $new_conges = $nb_conges + $add_conges;

        // On ajoute la modification dans le LOG
        $congespayes->addLogCP($user->id,$userCP,'Event : '.$congespayes->getNameEventCp($event),$new_conges);

        $congespayes->updateSoldeCP($userCP,$new_conges);

        $message = $langs->trans('AddEventToUserOkCP');

    }

    print '<div class="tabBar">';
    print $message;
    print '</div>';

}

$var=true;
$i = 0;

print '<div class="tabBar">';

print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input type="hidden" name="action" value="update" />';
print '<table class="noborder" width="100%;">';
print "<tr class=\"liste_titre\">";
print '<td width="5%">User ID</td>';
print '<td width="20%">'.$langs->trans('UserName').'</td>';
print '<td width="10%">'.$langs->trans('Available').'</td>';
print '<td>'.$langs->trans('UpdateButtonCP').'</td>';
print '</tr>';

foreach($listUsers as $users)
{

    $var=!$var;

    print '<tr '.$bc[$var].' style="height: 20px;">';
    print '<td>'.$users['rowid'].'</td>';
    print '<td>';
    $userstatic->id=$users['rowid'];
    $userstatic->nom=$users['name'];
    $userstatic->prenom=$users['firstname'];
    print $userstatic->getNomUrl(1);
    print '</td>';
    print '<td>';
    print '<input type="text" value="'.$congespayes->getCPforUser($users['rowid']).'" name="nb_conges['.$users['rowid'].']" size="5" style="text-align: center;"/>';
    print ' jours</td>'."\n";
    print '<td><input type="image" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/edit.png" name="update_cp['.$users['rowid'].']" style="border:0;"/></td>'."\n";
    print '</tr>';

    $i++;
}

print '</table>';
print '</form>';

$cp_events = $congespayes->fetchEventsCP();

if($cp_events == 1) {

    $html = new Form($db);

    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'."\n";
    print '<input type="hidden" name="action" value="add_event" />';

    print '<h3>'.$langs->trans('DefineEventUserCP').'</h3>';
    print $langs->trans('MotifCP').' : ';
    print $congespayes->selectEventCP();
    print ' '.$langs->trans('UserCP').' : ';
    print $html->select_users('',"userCP",1,"",0,'');
    print ' <input type="submit" value="'.$langs->trans("addEventToUserCP").'" name="bouton" class="button"/>';


    print '</form>';
}
print '</div>';
// Fin de page
$db->close();
llxFooter();
?>
