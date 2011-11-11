<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *    \file       congespayes.class.php
 *    \ingroup    congespayes/class
 *    \brief      Class file of the module paid leave.
 *		\version    $Id: congespayes.class.php,v 1.00 2011/09/15 11:00:00 dmouillard Exp $
 *		\author		dmouillard@teclib.com <Dimitri Mouillard>
 *		\remarks	   Class file of the module paid leave.
 */


/**
 *    \class      Congespayes
 *    \brief      Class pour les Congés Payés
 *		\remarks	   Développé par Teclib ( http://www.teclib.com/ )
 */
class Congespayes // extends CommonObject
{
    var $db;
    var $error;
    var $errors=array();

    var $rowid;

    var $fk_user;
    var $date_create='';
    var $description;
    var $date_debut='';
    var $date_fin='';
    var $statut='';
    var $fk_validator;
    var $date_valid='';
    var $fk_user_valid;
    var $date_refuse='';
    var $fk_user_refuse;
    var $date_cancel='';
    var $fk_user_cancel;
    var $detail_refuse='';

    var $congespayes = array();
    var $events = array();
    var $logs = array();

    var $optName = '';
    var $optValue = '';
    var $optRowid = '';

    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Congespayes($DB)
    {
        $this->db = $DB;

        // Mets à jour les congés payés en début de mois
        $this->updateSoldeCP();

        // Vérifie le nombre d'utilisateur et mets à jour si besoin
        $this->verifNbUsers($this->countActiveUsers(),$this->getConfCP('nbUser'));
        return 1;
    }


    /**
     *      \brief      Créer un congés payés dans la base de données
     *      \param      user        	User that create
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
        global $conf, $langs;
        $error=0;

        // Insert request
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."congespayes(";

        $sql.= "fk_user,";
        $sql.= "date_create,";
        $sql.= "description,";
        $sql.= "date_debut,";
        $sql.= "date_fin,";
        $sql.= "statut,";
        $sql.= "fk_validator";

        $sql.= ") VALUES (";

        // User
        if(!empty($this->fk_user)) {
            $sql.= "'".$this->fk_user."',";
        } else {
            $error++;
        }
        $sql.= " NOW(),";
        $sql.= " '".addslashes($this->description)."',";
        $sql.= " '".$this->date_debut."',";
        $sql.= " '".$this->date_fin."',";
        $sql.= " '1',";
        if(is_numeric($this->fk_validator)) {
            $sql.= " '".$this->fk_validator."'";
        }
        else {
            $error++;
        }

        $sql.= ")";

        $this->db->begin();

        dol_syslog("Congespayes::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if (! $resql) {
            $error++; $this->errors[]="Error ".$this->db->lasterror();
        }

        if (! $error)
        {
            $this->rowid = $this->db->last_insert_id(MAIN_DB_PREFIX."congespayes");

        }

        // Commit or rollback
        if ($error)
        {
            foreach($this->errors as $errmsg)
            {
                dol_syslog("Congespayes::create ".$errmsg, LOG_ERR);
                $this->error.=($this->error?', '.$errmsg:$errmsg);
            }
            $this->db->rollback();
            return -1*$error;
        }
        else
        {
            $this->db->commit();
            return $this->rowid;
        }
    }


    /**
     *    \brief      Load object in memory from database
     *    \param      id          id object
     *    \return     int         <0 if KO, >0 if OK
     */
    function fetch($id)
    {
        global $langs;

        $sql = "SELECT";
        $sql.= " cp.rowid,";

        $sql.= " cp.fk_user,";
        $sql.= " cp.date_create,";
        $sql.= " cp.description,";
        $sql.= " cp.date_debut,";
        $sql.= " cp.date_fin,";
        $sql.= " cp.statut,";
        $sql.= " cp.fk_validator,";
        $sql.= " cp.date_valid,";
        $sql.= " cp.fk_user_valid,";
        $sql.= " cp.date_refuse,";
        $sql.= " cp.fk_user_refuse,";
        $sql.= " cp.date_cancel,";
        $sql.= " cp.fk_user_cancel,";
        $sql.= " cp.detail_refuse";


        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes as cp";
        $sql.= " WHERE cp.rowid = ".$id;

        dol_syslog("Congespayes::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->rowid    = $obj->rowid;
                $this->fk_user = $obj->fk_user;
                $this->date_create = $obj->date_create;
                $this->description = $obj->description;
                $this->date_debut = $obj->date_debut;
                $this->date_fin = $obj->date_fin;
                $this->statut = $obj->statut;
                $this->fk_validator = $obj->fk_validator;
                $this->date_valid = $obj->date_valid;
                $this->fk_user_valid = $obj->fk_user_valid;
                $this->date_refuse = $obj->date_refuse;
                $this->fk_user_refuse = $obj->fk_user_refuse;
                $this->date_cancel = $obj->date_cancel;
                $this->fk_user_cancel = $obj->fk_user_cancel;
                $this->detail_refuse = $obj->detail_refuse;


            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            dol_syslog("Congespayes::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *    \brief      Liste les congés payés pour un utilisateur
     *    \param      user id     ID de l'utilisateur à lister
     *    \param      order       Filtrage par ordre
     *    \param      filter      Filtre de séléction
     *    \return     int         -1 si erreur, 1 si OK et 2 si pas de résultat
     */
    function fetchByUser($user_id,$order='',$filter='')
    {
        global $langs, $conf;

        $sql = "SELECT";
        $sql.= " cp.rowid,";

        $sql.= " cp.fk_user,";
        $sql.= " cp.date_create,";
        $sql.= " cp.description,";
        $sql.= " cp.date_debut,";
        $sql.= " cp.date_fin,";
        $sql.= " cp.statut,";
        $sql.= " cp.fk_validator,";
        $sql.= " cp.date_valid,";
        $sql.= " cp.fk_user_valid,";
        $sql.= " cp.date_refuse,";
        $sql.= " cp.fk_user_refuse,";
        $sql.= " cp.date_cancel,";
        $sql.= " cp.fk_user_cancel,";
        $sql.= " cp.detail_refuse";

        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes as cp";
        $sql.= " WHERE cp.fk_user = '".$user_id."'";

        // Filtre de séléction
        if(!empty($filter)) {
            $sql.= $filter;
        }

        // Ordre d'affichage du résultat
        if(!empty($order)) {
            $sql.= $order;
        }

        dol_syslog("Congespayes::fetchByUser sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);

        // Si pas d'erreur SQL
        if ($resql) {

            $i = 0;
            $tab_result = $this->congespayes;
            $num = $this->db->num_rows($resql);

            // Si pas d'enregistrement
            if(!$num) {
                return 2;
            }

            // Liste les enregistrements et les ajoutent au tableau
            while($i < $num) {

                $obj = $this->db->fetch_object($resql);

                $tab_result[$i]['rowid'] = $obj->rowid;
                $tab_result[$i]['fk_user'] = $obj->fk_user;
                $tab_result[$i]['date_create'] = $obj->date_create;
                $tab_result[$i]['description'] = $obj->description;
                $tab_result[$i]['date_debut'] = $obj->date_debut;
                $tab_result[$i]['date_fin'] = $obj->date_fin;
                $tab_result[$i]['statut'] = $obj->statut;
                $tab_result[$i]['fk_validator'] = $obj->fk_validator;
                $tab_result[$i]['date_valid'] = $obj->date_valid;
                $tab_result[$i]['fk_user_valid'] = $obj->fk_user_valid;
                $tab_result[$i]['date_refuse'] = $obj->date_refuse;
                $tab_result[$i]['fk_user_refuse'] = $obj->fk_user_refuse;
                $tab_result[$i]['date_cancel'] = $obj->date_cancel;
                $tab_result[$i]['fk_user_cancel'] = $obj->fk_user_cancel;
                $tab_result[$i]['detail_refuse'] = $obj->detail_refuse;

                $i++;
            }

            // Retourne 1 avec le tableau rempli
            $this->congespayes = $tab_result;
            return 1;
        }
        else
        {
            // Erreur SQL
            $this->error="Error ".$this->db->lasterror();
            dol_syslog("Congespayes::fetchByUser ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *    \brief      Liste les congés payés de tout les utilisateurs
     *    \param      order       Filtrage par ordre
     *    \param      filter      Filtre de séléction
     *    \return     int         -1 si erreur, 1 si OK et 2 si pas de résultat
     */
    function fetchAll($order,$filter)
    {
        global $langs;

        $sql = "SELECT";
        $sql.= " cp.rowid,";

        $sql.= " cp.fk_user,";
        $sql.= " cp.date_create,";
        $sql.= " cp.description,";
        $sql.= " cp.date_debut,";
        $sql.= " cp.date_fin,";
        $sql.= " cp.statut,";
        $sql.= " cp.fk_validator,";
        $sql.= " cp.date_valid,";
        $sql.= " cp.fk_user_valid,";
        $sql.= " cp.date_refuse,";
        $sql.= " cp.fk_user_refuse,";
        $sql.= " cp.date_cancel,";
        $sql.= " cp.fk_user_cancel,";
        $sql.= " cp.detail_refuse";

        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes as cp";
        $sql.= " WHERE cp.rowid > '0'"; // Hack pour la recherche sur le tableau

        // Filtrage de séléction
        if(!empty($filter)) {
            $sql.= $filter;
        }

        // Ordre d'affichage
        if(!empty($order)) {
            $sql.= $order;
        }

        dol_syslog("Congespayes::fetchAll sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);

        // Si pas d'erreur SQL
        if ($resql) {

            $i = 0;
            $tab_result = $this->congespayes;
            $num = $this->db->num_rows($resql);

            // Si pas d'enregistrement
            if(!$num) {
                return 2;
            }

            // On liste les résultats et on les ajoutent dans le tableau
            while($i < $num) {

                $obj = $this->db->fetch_object($resql);

                $tab_result[$i]['rowid'] = $obj->rowid;
                $tab_result[$i]['fk_user'] = $obj->fk_user;
                $tab_result[$i]['date_create'] = $obj->date_create;
                $tab_result[$i]['description'] = $obj->description;
                $tab_result[$i]['date_debut'] = $obj->date_debut;
                $tab_result[$i]['date_fin'] = $obj->date_fin;
                $tab_result[$i]['statut'] = $obj->statut;
                $tab_result[$i]['fk_validator'] = $obj->fk_validator;
                $tab_result[$i]['date_valid'] = $obj->date_valid;
                $tab_result[$i]['fk_user_valid'] = $obj->fk_user_valid;
                $tab_result[$i]['date_refuse'] = $obj->date_refuse;
                $tab_result[$i]['fk_user_refuse'] = $obj->fk_user_refuse;
                $tab_result[$i]['date_cancel'] = $obj->date_cancel;
                $tab_result[$i]['fk_user_cancel'] = $obj->fk_user_cancel;
                $tab_result[$i]['detail_refuse'] = $obj->detail_refuse;

                $i++;
            }
            // Retourne 1 et ajoute le tableau à la variable
            $this->congespayes = $tab_result;
            return 1;
        }
        else
        {
            // Erreur SQL
            $this->error="Error ".$this->db->lasterror();
            dol_syslog("Congespayes::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *      \brief      Update database
     *      \param      user        	User that modify
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
        global $conf, $langs;
        $error=0;

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."congespayes SET";

        $sql.= " description= '".addslashes($this->description)."',";

        if(!empty($this->date_debut)) {
            $sql.= " date_debut = '".$this->date_debut."',";
        } else {
            $error++;
        }
        if(!empty($this->date_fin)) {
            $sql.= " date_fin = '".$this->date_fin."',";
        } else {
            $error++;
        }
        if(!empty($this->statut) && is_numeric($this->statut)) {
            $sql.= " statut = '".$this->statut."',";
        } else {
            $error++;
        }
        if(!empty($this->fk_validator)) {
            $sql.= " fk_validator = '".$this->fk_validator."',";
        } else {
            $error++;
        }
        if(!empty($this->date_valid)) {
            $sql.= " date_valid = '".$this->date_valid."',";
        } else {
            $sql.= " date_valid = NULL,";
        }
        if(!empty($this->fk_user_valid)) {
            $sql.= " fk_user_valid = '".$this->fk_user_valid."',";
        } else {
            $sql.= " fk_user_valid = NULL,";
        }
        if(!empty($this->date_refuse)) {
            $sql.= " date_refuse = '".$this->date_refuse."',";
        } else {
            $sql.= " date_refuse = NULL,";
        }
        if(!empty($this->fk_user_refuse)) {
            $sql.= " fk_user_refuse = '".$this->fk_user_refuse."',";
        } else {
            $sql.= " fk_user_refuse = NULL,";
        }
        if(!empty($this->date_cancel)) {
            $sql.= " date_cancel = '".$this->date_cancel."',";
        } else {
            $sql.= " date_cancel = NULL,";
        }
        if(!empty($this->fk_user_cancel)) {
            $sql.= " fk_user_cancel = '".$this->fk_user_cancel."',";
        } else {
            $sql.= " fk_user_cancel = NULL,";
        }
        if(!empty($this->detail_refuse)) {
            $sql.= " detail_refuse = '".addslashes($this->detail_refuse)."'";
        } else {
            $sql.= " detail_refuse = NULL";
        }

        $sql.= " WHERE rowid= '".$this->rowid."'";

        $this->db->begin();

        dol_syslog("Congespayes::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (! $resql) {
            $error++; $this->errors[]="Error ".$this->db->lasterror();
        }

        if (! $error)
        {

        }

        // Commit or rollback
        if ($error)
        {
            foreach($this->errors as $errmsg)
            {
                dol_syslog("Congespayes::update ".$errmsg, LOG_ERR);
                $this->error.=($this->error?', '.$errmsg:$errmsg);
            }
            $this->db->rollback();
            return -1*$error;
        }
        else
        {
            $this->db->commit();
            return 1;
        }
    }


    /**
     *   \brief      Delete object in database
     *	\param      user        	User that delete
     *   \param      notrigger	    0=launch triggers after, 1=disable triggers
     *	\return		int				<0 if KO, >0 if OK
     */
    function delete($user, $notrigger=0)
    {
        global $conf, $langs;
        $error=0;

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."congespayes";
        $sql.= " WHERE rowid=".$this->rowid;

        $this->db->begin();

        dol_syslog("Congespayes::delete sql=".$sql);
        $resql = $this->db->query($sql);
        if (! $resql) {
            $error++; $this->errors[]="Error ".$this->db->lasterror();
        }

        if (! $error)
        {

        }

        // Commit or rollback
        if ($error)
        {
            foreach($this->errors as $errmsg)
            {
                dol_syslog("Congespayes::delete ".$errmsg, LOG_ERR);
                $this->error.=($this->error?', '.$errmsg:$errmsg);
            }
            $this->db->rollback();
            return -1*$error;
        }
        else
        {
            $this->db->commit();
            return 1;
        }
    }

    function verifDateCongesCP($fk_user,$dateDebut,$dateFin) {

        $this->fetchByUser($fk_user,'','');

        foreach($this->congespayes as $infos_CP) {

            if($dateDebut >= $infos_CP['date_debut'] && $dateDebut <= $infos_CP['date_fin'] || $dateFin <= $infos_CP['date_fin'] && $dateFin >= $infos_CP['date_debut']) {
                return false;
            }

        }

        return true;

    }

    /**
     *    \brief      Retourne la traduction du statut d'un congé payé
     *    \param      statut      int du statut du congé
     *    \return     string      retourne la traduction du statut
     */
    function getStatutCP($statut) {

        global $langs;

        if(is_numeric($statut)) {

            switch($statut) {
                case 1: // Brouillon
                    $statut = $langs->trans('DraftCP');
                    break;
                case 2: // En attente de validation
                    $statut = $langs->trans('ToValidateCP');
                    break;
                case 3: // Validée
                    $statut = $langs->trans('ValidateCP');
                    break;
                case 4: // Annulée
                    $statut = $langs->trans('CancelCP');
                    break;
                case 5: // Refusée
                    $statut = $langs->trans('RefuseCP');
            }

            return $statut;
        }
    }

    /**
     *    \brief      Affiche un select HTML des statuts de congés payés
     *    \param      selected    int du statut séléctionné par défaut
     *    \return     select      affiche le select des statuts
     */
    function selectStatutCP($selected='') {

        global $langs;

        // Liste des statuts
        $name = array('DraftCP','ToValidateCP','ValidateCP','CancelCP','RefuseCP');
        $nb = count($name)+1;

        // Select HTML
        $statut = '<select name="select_statut" class="flat">'."\n";
        $statut.= '<option value="-1">&nbsp;</option>'."\n";

        // Boucle des statuts
        for($i=1; $i < $nb; $i++) {
            if($i==$selected) {
                $statut.= '<option value="'.$i.'" selected="selected">'.$langs->trans($name[$i-1]).'</option>'."\n";
            }
            else {
                $statut.= '<option value="'.$i.'">'.$langs->trans($name[$i-1]).'</option>'."\n";
            }
        }

        $statut.= '</select>'."\n";
        print $statut;

    }

    /**
     *    \brief      Retourne un select HTML des groupes d'utilisateurs
     *    \param      prefix      nom du champ dans le formulaire
     *    \return     select      retourne le select des groupes
     */
    function selectUserGroup($prefix)
    {
        // On récupère le groupe déjà configuré
        $group.= "SELECT value";
        $group.= " FROM ".MAIN_DB_PREFIX."congespayes_config";
        $group.= " WHERE name = 'userGroup'";

        $resultat = $this->db->query($group);
        $objet = $this->db->fetch_object($resultat);
        $groupe = $objet->value;

        // On liste les groupes de Dolibarr
        $sql = "SELECT u.rowid, u.nom";
        $sql.= " FROM ".MAIN_DB_PREFIX."usergroup as u";
        $sql.= " ORDER BY u.rowid";

        dol_syslog("Congespayes::selectUserGroup sql=".$sql,LOG_DEBUG);
        $result = $this->db->query($sql);

        // Si pas d'erreur SQL
        if ($result)
        {
            // On créer le select HTML
            $selectGroup = '<select name="'.$prefix.'" class="flat">'."\n";
            $selectGroup.= '<option value="-1">&nbsp;</option>'."\n";

            // On liste les utilisateurs
            while ($obj = $this->db->fetch_object($result))
            {
                if($groupe==$obj->rowid) {
                    $selectGroup.= '<option value="'.$obj->rowid.'" selected="selected">'.$obj->nom.'</option>'."\n";
                } else {
                    $selectGroup.= '<option value="'.$obj->rowid.'">'.$obj->nom.'</option>'."\n";
                }
            }
            $selectGroup.= '</select>'."\n";
            $this->db->free($result);
        }
        else
        {
            // Erreur SQL
            $this->error=$this->db->lasterror();
            dol_syslog("Congespayes::selectUserGroup ".$this->error, LOG_ERR);
            return -1;
        }

        // Retourne le select HTML
        return $selectGroup;
    }

    /**
     *    \brief      Met à jour une option du module Conges Payés
     *    \param      name        nom du paramètre de configuration
     *    \return     value       vrai si mise à jour OK sinon faux
     */
    function updateConfCP($name,$value) {

        $sql = "UPDATE ".MAIN_DB_PREFIX."congespayes_config SET";
        $sql.= " value = '".$value."'";
        $sql.= " WHERE name = '".$name."'";

        $result = $this->db->query($sql);

        if($result) {
            return true;
        }

        return false;
    }

    /**
     *  Retourne la valeur d'un paramètre de configuration
     *
     *  @param      name        nom du paramètre de configuration
     *  @return     string      retourne la valeur du paramètre
     */
    function getConfCP($name)
    {
        $sql = "SELECT value";
        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_config";
        $sql.= " WHERE name = '".$name."'";

        $result = $this->db->query($sql);

        // Si pas d'erreur
        if($result) {

            $objet = $this->db->fetch_object($result);
            // Retourne la valeur
            return $objet->value;

        } else {

            // Erreur SQL
            $this->error=$this->db->lasterror();
            dol_syslog("Congespayes::getConfCP ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *    \brief      Met à jour le timestamp de la dernière mise à jour du solde des CP
     *    \return     nothing      ne retourne rien
     */
    function updateSoldeCP($userID='',$nbConges='') {

        global $user;

        if(empty($userID) && empty($nbConges)) {
            // Si mise à jour pour tous le monde en début de mois

            // Mois actuel
            $month = date('m',time());
            $lastUpdate = $this->getConfCP('lastUpdate');
            $monthLastUpdate = date('m', $lastUpdate);

            // Si la date du mois n'est pas la même que celle sauvegardé, on met à jour le timestamp
            if($month != $monthLastUpdate) {
                $sql = "UPDATE ".MAIN_DB_PREFIX."congespayes_config SET";
                $sql.= " value = '".time()."'";
                $sql.= " WHERE name = 'lastUpdate'";

                $result = $this->db->query($sql);

                // On ajoute x jours à chaque utilisateurs
                $nb_conges = $this->getConfCP('nbCongesEveryMonth');

                $users = $this->fetchUsers(false,false);
                $nbUser = count($users);

                $i = 0;

                while($i < $nbUser) {

                    $now_conges = $this->getCPforUser($users[$i]['rowid']);
                    $new_solde = $now_conges + $this->getConfCP('nbCongesEveryMonth');

                    // On ajoute la modification dans le LOG
                    $this->addLogCP($user->id,$users[$i]['rowid'],'Event : Mise à jour mensuelle',$new_solde);

                    $i++;
                }

                $sql2 = "UPDATE ".MAIN_DB_PREFIX."congespayes_users SET";
                $sql2.= " nb_conges = nb_conges + ".$nb_conges;

                $this->db->query($sql2);
            }
        } else {
            // Mise à jour pour un utilisateur
            $nbConges = number_format($nbConges,2,'.','');

            // Mise à jour pour un utilisateur
            $sql = "UPDATE ".MAIN_DB_PREFIX."congespayes_users SET";
            $sql.= " nb_conges = ".$nbConges;
            $sql.= " WHERE fk_user = '".$userID."'";

            $this->db->query($sql);

        }

    }

    /**
     *    \brief      Retourne un checked si vrai
     *    \param      name        nom du paramètre de configuration
     *    \return     string      retourne checked si > 0
     */
    function getCheckOption($name) {

        $sql = "SELECT *";
        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_config";
        $sql.= " WHERE name = '".$name."'";

        $result = $this->db->query($sql);

        if($result) {
            $obj = $this->db->fetch_object($result);

            // Si la valeur est 1 on retourne checked
            if($obj->value) {
                return 'checked="checked"';
            }
        }
    }


    /**
     *    \brief      Créer les entrées pour chaque utilisateur au moment de la configuration
     *    \return     nothing      ne retourne rien
     */
    function createCPusers($single=false,$userid='') {

        // Si c'est l'ensemble des utilisateurs à ajoutés
        if(!$single) {
            foreach($this->fetchUsers(false,true) as $users) {
                $sql = "INSERT INTO ".MAIN_DB_PREFIX."congespayes_users";
                $sql.= " (fk_user, nb_conges)";
                $sql.= " VALUES ('".$users['rowid']."','0')";

                $this->db->query($sql);
            }
        } else {
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."congespayes_users";
            $sql.= " (fk_user, nb_conges)";
            $sql.= " VALUES ('".$userid."','0')";

            $this->db->query($sql);
        }

    }

    /**
     *    \brief      Supprime un utilisateur du module Congés Payés
     *    \param      int          ID de l'utilisateur à supprimer
     *    \return     boolean      Vrai si pas d'erreur, faut si Erreur
     */
    function deleteCPuser($user_id) {

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."congespayes_users";
        $sql.= " WHERE fk_user = '".$user_id."'";

        $this->db->query($sql);

    }


    /**
     *    \brief      Retourne le solde de congés payés pour un utilisateur
     *    \param      user_id      ID de l'utilisateur
     *    \return     float        Retourne le solde de congés payés de l'utilisateur
     */
    function getCPforUser($user_id) {

        $sql = "SELECT *";
        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_users";
        $sql.= " WHERE fk_user = '".$user_id."'";

        $result = $this->db->query($sql);

        if($result) {
            $obj = $this->db->fetch_array($result);
            return number_format($obj['nb_conges'],2);
        } else {
            return '0';
        }

    }

    /**
     *    \brief      Liste la liste des utilisateurs du module congés payés
     *    \param      boolean     si vrai retourne une liste, si faux retourne un array
     *    \param      boolean     si vrai retourne pour Dolibarr si faux retourne pour CP
     *    \return     string      retourne un tableau de tout les utilisateurs actifs
     *    \remarks                uniquement pour vérifier si il existe de nouveau utilisateur
     */
    function fetchUsers($liste=true,$type=true)
    {
        // Si vrai donc pour user Dolibarr
        if($liste) {

            if($type) {
                // Si utilisateur de Dolibarr

                $sql = "SELECT u.rowid";
                $sql.= " FROM ".MAIN_DB_PREFIX."user as u";
                $sql.= " WHERE statut > '0'";

                dol_syslog("Congespayes::fetchUsers sql=".$sql, LOG_DEBUG);
                $resql=$this->db->query($sql);

                // Si pas d'erreur SQL
                if ($resql) {

                    $i = 0;
                    $num = $this->db->num_rows($resql);
                    $liste = '';

                    // Boucles du listage des utilisateurs
                    while($i < $num) {

                        $obj = $this->db->fetch_object($resql);

                        if($i == 0) {
                            $liste.= $obj->rowid;
                        } else {
                            $liste.= ', '.$obj->rowid;
                        }

                        $i++;
                    }
                    // Retoune le tableau des utilisateurs
                    return $liste;
                }
                else
                {
                    // Erreur SQL
                    $this->error="Error ".$this->db->lasterror();
                    dol_syslog("Congespayes::fetchUsers ".$this->error, LOG_ERR);
                    return -1;
                }

            } else { // Si utilisateur du module Congés Payés
                $sql = "SELECT u.fk_user";
                $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_users as u";

                dol_syslog("Congespayes::fetchUsers sql=".$sql, LOG_DEBUG);
                $resql=$this->db->query($sql);

                // Si pas d'erreur SQL
                if ($resql) {

                    $i = 0;
                    $num = $this->db->num_rows($resql);
                    $liste = '';

                    // Boucles du listage des utilisateurs
                    while($i < $num) {

                        $obj = $this->db->fetch_object($resql);

                        if($i == 0) {
                            $liste.= $obj->fk_user;
                        } else {
                            $liste.= ', '.$obj->fk_user;
                        }

                        $i++;
                    }
                    // Retoune le tableau des utilisateurs
                    return $liste;
                }
                else
                {
                    // Erreur SQL
                    $this->error="Error ".$this->db->lasterror();
                    dol_syslog("Congespayes::fetchUsers ".$this->error, LOG_ERR);
                    return -1;
                }
            }

        } else { // Si faux donc user Congés Payés

            // Si c'est pour les utilisateurs de Dolibarr
            if($type) {

                $sql = "SELECT u.rowid, u.name, u.firstname";
                $sql.= " FROM ".MAIN_DB_PREFIX."user as u";
                $sql.= " WHERE statut > '0'";

                dol_syslog("Congespayes::fetchUsers sql=".$sql, LOG_DEBUG);
                $resql=$this->db->query($sql);

                // Si pas d'erreur SQL
                if ($resql) {

                    $i = 0;
                    $tab_result = $this->congespayes;
                    $num = $this->db->num_rows($resql);

                    // Boucles du listage des utilisateurs
                    while($i < $num) {

                        $obj = $this->db->fetch_object($resql);

                        $tab_result[$i]['rowid'] = $obj->rowid;
                        $tab_result[$i]['name'] = $obj->name;
                        $tab_result[$i]['firstname'] = $obj->firstname;

                        $i++;
                    }
                    // Retoune le tableau des utilisateurs
                    return $tab_result;
                }
                else
                {
                    // Erreur SQL
                    $this->error="Error ".$this->db->lasterror();
                    dol_syslog("Congespayes::fetchUsers ".$this->error, LOG_ERR);
                    return -1;
                }

                // Si c'est pour les utilisateurs du module Congés Payés
            } else {

                $sql = "SELECT cpu.fk_user, u.name, u.firstname";
                $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_users as cpu,";
                $sql.= " ".MAIN_DB_PREFIX."user as u";
                $sql.= " WHERE cpu.fk_user = u.rowid";

                dol_syslog("Congespayes::fetchUsers sql=".$sql, LOG_DEBUG);
                $resql=$this->db->query($sql);

                // Si pas d'erreur SQL
                if ($resql) {

                    $i = 0;
                    $tab_result = $this->congespayes;
                    $num = $this->db->num_rows($resql);

                    // Boucles du listage des utilisateurs
                    while($i < $num) {

                        $obj = $this->db->fetch_object($resql);

                        $tab_result[$i]['rowid'] = $obj->fk_user;
                        $tab_result[$i]['name'] = $obj->name;
                        $tab_result[$i]['firstname'] = $obj->firstname;

                        $i++;
                    }
                    // Retoune le tableau des utilisateurs
                    return $tab_result;
                }
                else
                {
                    // Erreur SQL
                    $this->error="Error ".$this->db->lasterror();
                    dol_syslog("Congespayes::fetchUsers ".$this->error, LOG_ERR);
                    return -1;
                }
            }
        }
    }

    /**
     *    \brief      Compte le nombre d'utilisateur actifs dans Dolibarr
     *    \return     int      retourne le nombre d'utilisateur
     */
    function countActiveUsers() {

        $sql = "SELECT count(u.rowid) as compteur";
        $sql.= " FROM ".MAIN_DB_PREFIX."user as u";
        $sql.= " WHERE statut > '0'";

        $result = $this->db->query($sql);
        $objet = $this->db->fetch_object($result);
        return $objet->compteur;

    }

    /**
     *    \brief      Compare le nombre d'utilisateur actif de Dolibarr à celui des utilisateurs des congés payés
     *    \param      nbUsersDolibarr    nombre d'utilisateur actifs dans Dolibarr
     *    \param      nbUsersConges      nombre d'utilisateur actifs dans le module congés payés
     *    \return     nothing            ne retourne rien
     */
    function verifNbUsers($userDolibarr,$userCP) {

        // Si il y a plus d'utilisateur Dolibarr que dans le module CP
        if($userDolibarr > $userCP) {

            $this->updateConfCP('nbUser',$userDolibarr);

            $listUsersCP = $this->fetchUsers(true,false);

            // On séléctionne les utilisateurs qui ne sont pas déjà dans le module
            $sql = "SELECT u.rowid, u.name, u.firstname";
            $sql.= " FROM ".MAIN_DB_PREFIX."user as u";
            $sql.= " WHERE u.rowid NOT IN(".$listUsersCP.")";

            $result = $this->db->query($sql);

            // Si pas d'erreur SQL
            if($result) {

                $i = 0;
                $num = $this->db->num_rows($resql);

                while($i < $num) {

                    $obj = $this->db->fetch_object($resql);

                    // On ajoute l'utilisateur
                    $this->createCPusers(true,$obj->rowid);

                    $i++;
                }


            } else {
                // Erreur SQL
                $this->error="Error ".$this->db->lasterror();
                dol_syslog("Congespayes::verifNbUsers ".$this->error, LOG_ERR);
                return -1;
            }

        } else {
            // Si il y a moins d'utilisateur Dolibarr que dans le module CP

            $this->updateConfCP('nbUser',$userDolibarr);

            $listUsersDolibarr = $this->fetchUsers(true,true);

            // On séléctionne les utilisateurs qui ne sont pas déjà dans le module
            $sql = "SELECT u.fk_user";
            $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_users as u";
            $sql.= " WHERE u.fk_user NOT IN(".$listUsersDolibarr.")";

            $result = $this->db->query($sql);

            // Si pas d'erreur SQL
            if($result) {

                $i = 0;
                $num = $this->db->num_rows($resql);

                while($i < $num) {

                    $obj = $this->db->fetch_object($resql);

                    // On ajoute l'utilisateur
                    $this->deleteCPuser($obj->fk_user);

                    $i++;
                }


            } else {
                // Erreur SQL
                $this->error="Error ".$this->db->lasterror();
                dol_syslog("Congespayes::verifNbUsers ".$this->error, LOG_ERR);
                return -1;
            }
        }

    }


    /**
     *		\brief		Retourne le nombre de jours ouvrés entre deux dates
     *    \param      Date de début et date de fin au format TimeStamp
     *    \remarks    Prise en compte des jours fériés en France
     */

    function getOpenDays($date_start, $date_stop) {

        // Tableau des jours feriés
        $arr_bank_holidays = array();

        // On boucle dans le cas où l'année de départ serait différente de l'année d'arrivée
        $diff_year = date('Y', $date_stop) - date('Y', $date_start);

        for ($i = 0; $i <= $diff_year; $i++) {
            $year = (int)date('Y', $date_start) + $i;
            // Liste des jours feriés
            $arr_bank_holidays[] = '1_1_'.$year; // Jour de l'an
            $arr_bank_holidays[] = '1_5_'.$year; // Fete du travail
            $arr_bank_holidays[] = '8_5_'.$year; // Victoire 1945
            $arr_bank_holidays[] = '14_7_'.$year; // Fete nationale
            $arr_bank_holidays[] = '15_8_'.$year; // Assomption
            $arr_bank_holidays[] = '1_11_'.$year; // Toussaint
            $arr_bank_holidays[] = '11_11_'.$year; // Armistice 1918
            $arr_bank_holidays[] = '25_12_'.$year; // Noel
            // Récupération de paques. Permet ensuite d'obtenir le jour de l'ascension et celui de la pentecote
            $easter = easter_date($year);
            $arr_bank_holidays[] = date('j_n_'.$year, $easter + 86400); // Paques
            $arr_bank_holidays[] = date('j_n_'.$year, $easter + (86400*39)); // Ascension
            $arr_bank_holidays[] = date('j_n_'.$year, $easter + (86400*50)); // Pentecote
        }

        $nb_days_open = 0;

        while ($date_start <= $date_stop) {
            // Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour férié, on incrémente les jours ouvrés
            if (!in_array(date('w', $date_start), array(0, 6)) && !in_array(date('j_n_'.date('Y', $date_start), $date_start), $arr_bank_holidays)) {
                $nb_days_open++;
            }

            $date_start = mktime(date('H', $date_start), date('i', $date_start), date('s', $date_start), date('m', $date_start), date('d', $date_start) + 1, date('Y', $date_start));
        }

        // On retourne le nombre de jours ouvrés
        return $nb_days_open;
    }

    /**
     *    \brief      Liste les évènements de congés payés enregistré
     *    \return     int         -1 si erreur, 1 si OK et 2 si pas de résultat
     */
    function fetchEventsCP()
    {
        global $langs;

        $sql = "SELECT";
        $sql.= " cpe.rowid,";
        $sql.= " cpe.name,";
        $sql.= " cpe.value";

        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_events as cpe";

        dol_syslog("Congespayes::fetchEventsCP sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);

        // Si pas d'erreur SQL
        if ($resql) {

            $i = 0;
            $tab_result = $this->events;
            $num = $this->db->num_rows($resql);

            // Si pas d'enregistrement
            if(!$num) {
                return 2;
            }

            // On liste les résultats et on les ajoutent dans le tableau
            while($i < $num) {

                $obj = $this->db->fetch_object($resql);

                $tab_result[$i]['rowid'] = $obj->rowid;
                $tab_result[$i]['name'] = $obj->name;
                $tab_result[$i]['value'] = $obj->value;

                $i++;
            }
            // Retourne 1 et ajoute le tableau à la variable
            $this->events = $tab_result;
            return 1;
        }
        else
        {
            // Erreur SQL
            $this->error="Error ".$this->db->lasterror();
            dol_syslog("Congespayes::fetchEventsCP ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *    \brief      Créer un évènement de congés payés
     *    \return     int         -1 si erreur, id si OK
     */
    function createEventCP($user, $notrigger=0)
    {
        global $conf, $langs;
        $error=0;

        // Insert request
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."congespayes_events (";

        $sql.= "name,";
        $sql.= "value";

        $sql.= ") VALUES (";

        $sql.= " '".addslashes($this->optName)."',";
        $sql.= " '".$this->optValue."'";
        $sql.= ")";

        $this->db->begin();

        dol_syslog("Congespayes::createEventCP sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if (! $resql) {
            $error++; $this->errors[]="Error ".$this->db->lasterror();
        }

        if (! $error)
        {
            $this->optRowid = $this->db->last_insert_id(MAIN_DB_PREFIX."congespayes_events");

        }

        // Commit or rollback
        if ($error)
        {
            foreach($this->errors as $errmsg)
            {
                dol_syslog("Congespayes::createEventCP ".$errmsg, LOG_ERR);
                $this->error.=($this->error?', '.$errmsg:$errmsg);
            }
            $this->db->rollback();
            return -1*$error;
        }
        else
        {
            $this->db->commit();
            return $this->optRowid;
        }
    }

    /**
     *    \brief      Met à jour les évènements de congés payés
     *    \return     int         -1 si erreur, id si OK
     */
    function updateEventCP($rowid, $name, $value) {

        $sql = "UPDATE ".MAIN_DB_PREFIX."congespayes_events SET";
        $sql.= " name = '".addslashes($name)."', value = '".$value."'";
        $sql.= " WHERE rowid = '".$rowid."'";

        $result = $this->db->query($sql);

        if($result) {
            return true;
        }

        return false;
    }

    function selectEventCP() {

        $sql = "SELECT *";
        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_events";

        $result = $this->db->query($sql);

        if($result) {

            $num = $this->db->num_rows($result);
            $i = 0;
            $out = '<select name="list_event" class="flat" >';
            $out.= '<option value="-1">&nbsp;</option>';

            while($i < $num) {
                $obj = $this->db->fetch_object($result);

                $out.= '<option value="'.$obj->rowid.'">'.$obj->name.'</option>';
                $i++;
            }

            $out.= '</select>';

            return $out;

        } else {

            return false;

        }

    }

    function deleteEventCP($rowid) {

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."congespayes_events";
        $sql.= " WHERE rowid = '".$rowid."'";

        $result = $this->db->query($sql);

        if($result) {
            return true;
        } else {
            return false;
        }
    }

    function getValueEventCp($rowid) {

        $sql = "SELECT value";
        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_events";
        $sql.= " WHERE rowid = '".$rowid."'";

        $result = $this->db->query($sql);

        if($result) {
            $obj = $this->db->fetch_array($result);
            return number_format($obj['value'],2);
        } else {
            return false;
        }
    }

    function getNameEventCp($rowid) {

        $sql = "SELECT name";
        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_events";
        $sql.= " WHERE rowid = '".$rowid."'";

        $result = $this->db->query($sql);

        if($result) {
            $obj = $this->db->fetch_array($result);
            return $obj['name'];
        } else {
            return false;
        }
    }

    function addLogCP($fk_user_action,$fk_user_update,$type,$new_solde) {

        global $conf, $langs, $db;

        $error=0;

        $prev_solde = $this->getCPforUser($fk_user_update);
        $new_solde = number_format($new_solde,2,'.','');

        // Insert request
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."congespayes_logs (";

        $sql.= "date_action,";
        $sql.= "fk_user_action,";
        $sql.= "fk_user_update,";
        $sql.= "type_action,";
        $sql.= "prev_solde,";
        $sql.= "new_solde";

        $sql.= ") VALUES (";

        $sql.= " NOW(), ";
        $sql.= " '".$fk_user_action."',";
        $sql.= " '".$fk_user_update."',";
        $sql.= " '".addslashes($type)."',";
        $sql.= " '".$prev_solde."',";
        $sql.= " '".$new_solde."'";
        $sql.= ")";

        $this->db->begin();

   	   	dol_syslog("Congespayes::addLogCP sql=".$sql, LOG_DEBUG);
   	   	$resql=$this->db->query($sql);
       	if (! $resql) {
       	    $error++; $this->errors[]="Error ".$this->db->lasterror();
       	}

       	if (! $error)
       	{
       	    $this->optRowid = $this->db->last_insert_id(MAIN_DB_PREFIX."congespayes_logs");

       	}

       	// Commit or rollback
       	if ($error)
       	{
       	    foreach($this->errors as $errmsg)
       	    {
   	            dol_syslog("Congespayes::addLogCP ".$errmsg, LOG_ERR);
   	            $this->error.=($this->error?', '.$errmsg:$errmsg);
       	    }
       	    $this->db->rollback();
       	    return -1*$error;
       	}
       	else
       	{
       	    $this->db->commit();
            return $this->optRowid;
       	}
    }

    /**
     *    \brief      Liste le log des congés payés
     *    \param      order       Filtrage par ordre
     *    \param      filter      Filtre de séléction
     *    \return     int         -1 si erreur, 1 si OK et 2 si pas de résultat
     */
    function fetchLog($order,$filter)
    {
        global $langs;

        $sql = "SELECT";
        $sql.= " cpl.rowid,";
        $sql.= " cpl.date_action,";
        $sql.= " cpl.fk_user_action,";
        $sql.= " cpl.fk_user_update,";
        $sql.= " cpl.type_action,";
        $sql.= " cpl.prev_solde,";
        $sql.= " cpl.new_solde";

        $sql.= " FROM ".MAIN_DB_PREFIX."congespayes_logs as cpl";
        $sql.= " WHERE cpl.rowid > '0'"; // Hack pour la recherche sur le tableau

        // Filtrage de séléction
        if(!empty($filter)) {
            $sql.= $filter;
        }

        // Ordre d'affichage
        if(!empty($order)) {
            $sql.= $order;
        }

        dol_syslog("Congespayes::fetchLog sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);

        // Si pas d'erreur SQL
      		if ($resql) {

      		    $i = 0;
      		    $tab_result = $this->logs;
      		    $num = $this->db->num_rows($resql);

      		    // Si pas d'enregistrement
      		    if(!$num) {
                return 2;
      		    }

      		    // On liste les résultats et on les ajoutent dans le tableau
      		    while($i < $num) {

      		        $obj = $this->db->fetch_object($resql);

      		        $tab_result[$i]['rowid'] = $obj->rowid;
      		        $tab_result[$i]['date_action'] = $obj->date_action;
      		        $tab_result[$i]['fk_user_action'] = $obj->fk_user_action;
      		        $tab_result[$i]['fk_user_update'] = $obj->fk_user_update;
      		        $tab_result[$i]['type_action'] = $obj->type_action;
      		        $tab_result[$i]['prev_solde'] = $obj->prev_solde;
      		        $tab_result[$i]['new_solde'] = $obj->new_solde;

      		        $i++;
      		    }
      		    // Retourne 1 et ajoute le tableau à la variable
      		    $this->logs = $tab_result;
      		    return 1;
      		}
      		else
      		{
      		    // Erreur SQL
      		    $this->error="Error ".$this->db->lasterror();
      		    dol_syslog("Congespayes::fetchLog ".$this->error, LOG_ERR);
      		    return -1;
      		}
    }

}
?>
