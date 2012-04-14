<?php
/* Copyright (C) 2005-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007-2009 Regis Houssin        <regis@dolibarr.fr>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/core/menus/standard/cabinetmed_frontoffice.php
 *	\brief      Gestionnaire nomme cabinetmed du menu du haut
 *	\version    $Id: cabinetmed_frontoffice.php,v 1.3 2011/02/13 15:49:43 eldy Exp $
 *
 *	\remarks    La construction d'un gestionnaire pour le menu du haut est simple:
 *	\remarks    Toutes les entrees de menu a faire apparaitre dans la barre du haut
 *	\remarks    doivent etre affichees par <a class="tmenu" href="...?mainmenu=...">...</a>
 *	\remarks    ou si menu selectionne <a class="tmenusel" href="...?mainmenu=...">...</a>
 */


/**
 *      \class      MenuTop
 *	    \brief      Class to manage top menu cabinetmed (for external users)
 */
class MenuTop {

	var $require_left=array("cabinetmed_frontoffice");    // Si doit etre en phase avec un gestionnaire de menu gauche particulier
	var $hideifnotallowed=1;						// Put 0 for back office menu, 1 for front office menu
	var $atarget="";                                // Valeur du target a utiliser dans les liens


	/**
	 *  Constructor
	 *
	 *  @param	DoliDB	$db		Database handler
	 *  @return	void
	 */
	function MenuTop($db)
	{
		$this->db=$db;
	}


	/**
	 *  Show menu
	 *
	 *  @return	void
	 */
	function showmenu()
	{
		dol_include_once('/cabinetmed/core/menus/cabinetmed.lib.php');

		print_cabinetmed_menu($this->db,$this->atarget,$this->hideifnotallowed);
	}

}


/**
 *  \class      MenuLeft
 *  \brief      Classe permettant la gestion du menu du gauche cabinetmed
 */
class MenuLeft {

    var $require_top=array("cabinetmed_frontoffice");     // Si doit etre en phase avec un gestionnaire de menu du haut particulier

    var $db;
    var $menu_array;
    var $menu_array_after;


    /**
     *  Constructor
     *  @param      db                  Database handler
     *  @param      menu_array          Table of menu entries to show before entries of menu handler
     *  @param      menu_array_after    Table of menu entries to show after entries of menu handler
     */
    function MenuLeft($db,&$menu_array,&$menu_array_after)
    {
        $this->db=$db;
        $this->menu_array=$menu_array;
        $this->menu_array_after=$menu_array_after;
    }


    /**
     *      \brief      Show menu
     *      \return     int     Number of menu entries shown
     */
    function showmenu()
    {
        dol_include_once('/cabinetmed/core/menus/cabinetmed.lib.php');

        $res=print_left_cabinetmed_menu($this->db,$this->menu_array,$this->menu_array_after);

        $conf->global->MAIN_SEARCHFORM_SOCIETE=0;
        $conf->global->MAIN_SEARCHFORM_CONTACT=0;

        return $res;
    }

}

?>
