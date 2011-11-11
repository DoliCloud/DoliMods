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
 *   	\file       congespayes.php
 *		\ingroup    congespayes
 *		\brief      Page module configuration paid leave.
 *		\version    $Id: congespayes.php,v 1.00 2011/09/15 11:00:00 dmouillard Exp $
 *		\author		dmouillard@teclib.com <Dimitri Mouillard>
 *		\remarks	   Page module configuration paid leave.
 */

require("../../main.inc.php");
dol_include_once("/congespayes/class/congespayes.class.php");
require_once(DOL_DOCUMENT_ROOT. "/core/class/html.form.class.php");
require_once(DOL_DOCUMENT_ROOT. "/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT. "/user/class/usergroup.class.php");

$langs->load("congespayes@congespayes");

// Protection if external user
if ($user->societe_id > 0)
{
    accessforbidden();
}

// Si pas administrateur
if(!$user->admin) {
    accessforbidden();
}



/*
 * View
*/

// Vérification si module activé
if(!in_array('congespayes', $conf->modules)) {
    $langs->load("congespayes@congespayes");
    llxHeader('','Congés Payés');
    print '<div class="tabBar">';
    print '<span style="color: #FF0000;">'.$langs->trans('NotActiveModCP').'</span>';
    print '</div>';
    llxFooter();
    exit();
}

llxheader('',$langs->trans('TitleAdminCP'));

print_fiche_titre($langs->trans('ConfCP'));

$cp = new Congespayes($db);

// Contrôle du formulaire

if(isset($_POST['action']) && $_POST['action'] == "add") {

    $message = '';
    $error = false;

    // Option du groupe de validation
    if(!$cp->updateConfCP('userGroup',$_POST['userGroup'])) {
        $error = true;
    }

    // Option du délai pour faire une demande de congés payés
    if(!$cp->updateConfCP('delayForRequest',$_POST['delayForRequest'])) {
        $error = true;
    }

    // Option du nombre de jours à ajouter chaque mois
    $nbCongesEveryMonth = price2num($_POST['nbCongesEveryMonth'],2);

    if(!$cp->updateConfCP('nbCongesEveryMonth',$nbCongesEveryMonth)) {
        $error = true;
    }

    // Option du nombre de jours pour un mariage
    $OptMariageCP = price2num($_POST['OptMariage'],2);

    if(!$cp->updateConfCP('OptMariage',$OptMariageCP)) {
        $error = true;
    }

    // Option du nombre de jours pour un décés d'un proche
    $OptDecesProcheCP = price2num($_POST['OptDecesProche'],2);

    if(!$cp->updateConfCP('OptDecesProche',$OptDecesProcheCP)) {
        $error = true;
    }

    // Option du nombre de jours pour un mariage d'un enfant
    $OptMariageProcheCP = price2num($_POST['OptMariageProche'],2);

    if(!$cp->updateConfCP('OptMariageProche',$OptMariageProcheCP)) {
        $error = true;
    }

    // Option du nombre de jours pour un décés d'un parent
    $OptDecesParentsCP = price2num($_POST['OptDecesParents'],2);

    if(!$cp->updateConfCP('OptDecesParents',$OptDecesParentsCP)) {
        $error = true;
    }

    // Option pour avertir le valideur si délai de demande incorrect
    if(isset($_POST['AlertValidatorDelay'])) {
        if(!$cp->updateConfCP('AlertValidatorDelay','1')) {
            $error = true;
        }
    } else {
        if(!$cp->updateConfCP('AlertValidatorDelay','0')) {
            $error = true;
        }
    }

    // Option pour avertir le valideur si solde des congés de l'utilisateur inccorect
    if(isset($_POST['AlertValidatorSolde'])) {
        if(!$cp->updateConfCP('AlertValidatorSolde','1')) {
            $error = true;
        }
    } else {
        if(!$cp->updateConfCP('AlertValidatorSolde','0')) {
            $error = true;
        }
    }

    // Option du nombre de jours à déduire pour 1 jour de congés
    $nbCongesDeducted = price2num($_POST['nbCongesDeducted'],2);

    if(!$cp->updateConfCP('nbCongesDeducted',$nbCongesDeducted)) {
        $error = true;
    }

    if($error) {
        $message = $langs->trans('ErrorUpdateConfCP');
    } else {
        $message = $langs->trans('UpdateConfCPOK');
    }

    // Si première mise à jour, prévenir l'utilisateur de mettre à jour le solde des congés payés
    $sql = "SELECT *";
    $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_users";

    $result = $db->query($sql);
    $num = $db->num_rows($sql);

    if($num < 1) {
        $cp->createCPusers();
        $message.= '<br /><span style="color: #FF0000;">'.$langs->trans('AddCPforUsers').'</span>';
    }


    dol_htmloutput_mesg($message);


    // Si il s'agit de créer un event
} elseif(isset($_POST['action']) && $_POST['action'] == 'create_event') {

    $error = false;

    if(!empty($_POST['optName'])) {
        $optName = trim($_POST['optName']);
    } else {
        $error = true;
    }

    if(!empty($_POST['optValue'])) {
        $optValue = price2num($_POST['optValue'],2);
    } else {
        $error = true;
    }

    $cp->optName = $optName;
    $cp->optValue = $optValue;

    if($error) {
        $message = 'ErrorCreateEventCP';
    } else {

        $result = $cp->createEventCP($user);

        if($result > 0) {
            $message = 'OkCreateEventCP';
        } else {
            $message = 'ErrorCreateEventCP';
        }
    }

    dol_htmloutput_mesg($message);

} elseif(isset($_POST['action']) && $_POST['action'] == 'event' && isset($_POST['update_event'])) {

    $error = false;

    $eventId = array_keys($_POST['update_event']);
    $eventId = $eventId[0];

    $eventName = $_POST['optName'];
    $eventName = $eventName[$eventId];

    $eventValue = $_POST['optValue'];
    $eventValue = $eventValue[$eventId];

    if(!empty($eventName)) {
        $eventName = trim($eventName);
    } else {
        $error = true;
    }

    if(!empty($eventValue)) {
        $eventValue = price2num($eventValue,2);
    } else {
        $error = true;
    }



    if(!$error) {

        // Mise à jour des congés de l'utilisateur
        $update = $cp->updateEventCP($eventId,$eventName,$eventValue);
        if(!$update) {
            $message='ErrorUpdateEventCP';
        } else {
            $message='UpdateEventOkCP';
        }
    } else {
        $message='ErrorUpdateEventCP';
    }

    dol_htmloutput_mesg($message);

} elseif(isset($_POST['action']) && $_POST['action'] == 'event' && isset($_POST['delete_event'])) {

    $eventId = array_keys($_POST['delete_event']);
    $eventId = $eventId[0];

    $result = $cp->deleteEventCP($eventId);

    if($result) {
        print '<div class="tabBar">';
        print $langs->trans('DeleteEventOkCP');
        print '</div>';
    } else {
        print '<div class="tabBar">';
        print $langs->trans('ErrorDeleteEventCP');
        print '</div>';
    }

}

// Affichage de la page de configuation

print '<div class="tabBar">';

print '<h3>'.$langs->trans('TitleOptionMainCP').'</h3>';

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?leftmenu=setup" name="config">'."\n";
print '<input type="hidden" name="action" value="add" />'."\n";

print '<table class="noborder" width="100%">';
print '<tbody>';
print '<tr class="liste_titre">';
print '<th class="liste_titre">'.$langs->trans('DescOptionCP').'</td>';
print '<th class="liste_titre">'.$langs->trans('ValueOptionCP').'</td>';
print '</tr>';

$var=true;

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px; width: 40%;">'.$langs->trans('GroupToValidateCP').'</td>'."\n";
print '<td style="padding:5px;">'.$cp->selectUserGroup('userGroup').'</td>'."\n";
print '</tr>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px;">'.$langs->trans('DelayForSubmitCP').'</td>'."\n";
print '<td style="padding:5px;"><input type="text" name="delayForRequest" value="'.$cp->getConfCP('delayForRequest').'" size="2" /> '.$langs->trans('Jours').'</td>'."\n";
print '</tr>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px;">'.$langs->trans('AlertValidatorDelayCP').'</td>'."\n";
print '<td style="padding:5px;"><input type="checkbox" name="AlertValidatorDelay" '.$cp->getCheckOption('AlertValidatorDelay').'/></td>'."\n";
print '</tr>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px;">'.$langs->trans('AlertValidorSoldeCP').'</td>'."\n";
print '<td style="padding:5px;"><input type="checkbox" name="AlertValidatorSolde" '.$cp->getCheckOption('AlertValidatorSolde').'/></td>'."\n";
print '</tr>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px;">'.$langs->trans('nbCongesEveryMonthCP').'</td>'."\n";
print '<td style="padding:5px;"><input type="text" name="nbCongesEveryMonth" value="'.$cp->getConfCP('nbCongesEveryMonth').'" size="2"/> '.$langs->trans('Jours').'</td>'."\n";
print '</tr>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px;">'.$langs->trans('nbCongesDeductedCP').'</td>'."\n";
print '<td style="padding:5px;"><input type="text" name="nbCongesDeducted" value="'.$cp->getConfCP('nbCongesDeducted').'" size="2"/> '.$langs->trans('Jours').'</td>'."\n";
print '</tr>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px;">'.$langs->trans('nbUserCP').'</td>'."\n";
print '<td style="padding:5px;"><input type="text" name="nbUser" value="'.$cp->getConfCP('nbUser').'" disabled="disabled" size="4"/></td>'."\n";
print '</tr>'."\n";

$var=!$var;
print '<tr '.$bc[$var].'>'."\n";
print '<td style="padding:5px;">'.$langs->trans('LastUpdateCP').'</td>'."\n";
print '<td style="padding:5px;"><input type="text" name="lastUpdate" value="'.date('d-m-Y à H:i:s',$cp->getConfCP('lastUpdate')).'" disabled="disabled"/></td>'."\n";
print '</tr>'."\n";

print '</tbody>'."\n";
print '</table>'."\n";

print '<br /><input type="submit" value="'.$langs->trans("ConfirmConfigCP").'" name="bouton" class="button"/>'."\n";
print '</form>'."\n\n";

print '<br /><h3>'.$langs->trans('TitleOptionEventCP').'</h3>'."\n\n";

$cp_events = $cp->fetchEventsCP();

if($cp_events == 1) {

    $var = false;
    $i = 0;

    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?leftmenu=setup" name="event_update">'."\n";
    print '<input type="hidden" name="action" value="event" />'."\n";

    print '<h5>'.$langs->trans('TitleUpdateEventCP').'</h5>'."\n";

    print '<table class="noborder" width="100%">'."\n";
    print '<tbody>'."\n";
    print '<tr class="liste_titre">'."\n";

    print '<td class="liste_titre" width="40%">'.$langs->trans('NameEventCP').'</td>'."\n";
    print '<td class="liste_titre">'.$langs->trans('ValueOptionCP').'</td>'."\n";
    print '<td class="liste_titre">'.$langs->trans('UpdateEventOptionCP').'</td>'."\n";
    print '<td class="liste_titre">'.$langs->trans('DeleteEventOptionCP').'</td>'."\n";

    print '</tr>'."\n";

    foreach($cp->events as $infos_event) {

        $var=!$var;

        print '<tr '.$bc[$var].'>'."\n";
        print '<td style="padding: 5px;"><input type="text" size="40" name="optName['.$infos_event['rowid'].']" value="'.$infos_event['name'].'" /></td>'."\n";
        print '<td width="10%"><input type="text" size="2" name="optValue['.$infos_event['rowid'].']" value="'.$infos_event['value'].'" /> '.$langs->trans('Jours').'</td>'."\n";
        print '<td width="10%"><input type="image" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/edit.png" name="update_event['.$infos_event['rowid'].']" style="border:0;"/></td>'."\n";
        print '<td width="10%"><input type="image" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/delete.png" name="delete_event['.$infos_event['rowid'].']" style="border:0;"/></td>'."\n";
        print '</tr>';

        $i++;
    }

    print '</tbody>'."\n";
    print '</table>'."\n";
    print '</form>'."\n";
    print '<br />'."\n\n";

}

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?leftmenu=setup" name="event_create">'."\n";

print '<h5>'.$langs->trans('TitleCreateEventCP').'</h5>';

print '<table class="noborder" width="100%">';
print '<tbody>';

print '<tr class="liste_titre">';

print '<td class="liste_titre" width="40%">'.$langs->trans('NameEventCP').'</td>';
print '<td class="liste_titre" width="20%">'.$langs->trans('ValueOptionCP').'</td>';
print '<td class="liste_titre">'.$langs->trans('CreateEventCP').'</td>';

print '</tr>';

print '<input type="hidden" name="action" value="create_event" />'."\n";

print '<tr class="pair">';
print '<td style="padding: 5px;"><input type="text" size="40" name="optName" value="" /></td>'."\n";
print '<td><input type="text" size="2" name="optValue" value="" /> '.$langs->trans('Jours').'</td>'."\n";
print '<td><input type="submit" class="button" name="button" value="'.$langs->trans('ValidEventCP').'" /></td>'."\n";
print '</tr>'."\n";

print '</tbody>';
print '</table>';

print '</form>';

print '</div>';


// Fin de page
llxFooter();

if (is_object($db)) $db->close();