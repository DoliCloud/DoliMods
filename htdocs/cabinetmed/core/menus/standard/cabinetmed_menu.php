<?php
/* Copyright (C) 2005-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *		\file       htdocs/cabinetmed/core/menus/standard/cabinetmed_menu.php
 *		\brief      Gestionnaire nomme cabinetmed du menu du haut
 *
 *		\remarks    La construction d'un gestionnaire pour le menu du haut est simple:
 *		\remarks    Toutes les entrees de menu a faire apparaitre dans la barre du haut
 *		\remarks    doivent etre affichees par <a class="tmenu" href="...?mainmenu=...">...</a>
 *		\remarks    ou si menu selectionne <a class="tmenusel" href="...?mainmenu=...">...</a>
 */


/**
 *  Classe to manage menu Eldy
 */
class MenuManager
{
	var $db;
	var $type_user=0;								// Put 0 for internal users, 1 for external users
	var $atarget="";                                // Valeur du target a utiliser dans les liens

	var $menu_array;
	var $menu_array_after;


	/**
	 *  Constructor
	 *
	 *  @param	DoliDB		$db     	Database handler
     *  @param	int			$type_user	Type of user
     */
    function __construct($db, $type_user)
    {
    	$this->type_user=$type_user;
		$this->db=$db;
	}


	/**
	 * Load this->tabMenu
	 *
	 * @return	void
	 */
	function loadMenu()
	{
		// On sauve en session le menu principal choisi
		if (isset($_GET["mainmenu"])) $_SESSION["mainmenu"]=$_GET["mainmenu"];
		if (isset($_GET["idmenu"]))   $_SESSION["idmenu"]=$_GET["idmenu"];

		// Read mainmenu and leftmenu that define which menu to show
		if (isset($_GET["mainmenu"]))
		{
			// On sauve en session le menu principal choisi
			$mainmenu=$_GET["mainmenu"];
			$_SESSION["mainmenu"]=$mainmenu;
			$_SESSION["leftmenuopened"]="";
		}
		else
		{
			// On va le chercher en session si non defini par le lien
			$mainmenu=isset($_SESSION["mainmenu"])?$_SESSION["mainmenu"]:'';
		}

		if (isset($_GET["leftmenu"]))
		{
			// On sauve en session le menu principal choisi
			$leftmenu=$_GET["leftmenu"];
			$_SESSION["leftmenu"]=$leftmenu;

			if ($_SESSION["leftmenuopened"]==$leftmenu)	// To collapse
			{
				//$leftmenu="";
				$_SESSION["leftmenuopened"]="";
			}
			else
			{
				$_SESSION["leftmenuopened"]=$leftmenu;
			}
		} else {
			// On va le chercher en session si non defini par le lien
			$leftmenu=isset($_SESSION["leftmenu"])?$_SESSION["leftmenu"]:'';
		}

		require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
		$tabMenu=array();
		$menuArbo = new Menubase($this->db,'cabinetmed');
		$menuArbo->menuLoad($mainmenu, $leftmenu, $this->type_user, 'cabinetmed', $tabMenu);
		$this->tabMenu=$tabMenu;
	}

	/**
	 *  Show menu
	 *
	 *	@param	string	$mode		'top' or 'left'
	 *  @return	int     			Number of menu entries shown
	 */
	function showmenu($mode)
	{
		dol_include_once('/cabinetmed/core/menus/cabinetmed.lib.php');

		if ($this->type_user == 1)
		{
			$conf->global->MAIN_SEARCHFORM_SOCIETE=0;
			$conf->global->MAIN_SEARCHFORM_CONTACT=0;
		}

		$res='ErrorBadParameterForMode';

		require_once DOL_DOCUMENT_ROOT.'/core/class/menu.class.php';
		$this->menu=new Menu();

		if ($mode == 'top')  $res=print_cabinetmed_menu($this->db,$this->atarget,$this->type_user,$this->tabMenu,$this->menu);
		if ($mode == 'left') $res=print_left_cabinetmed_menu($this->db,$this->menu_array,$this->menu_array_after,$this->tabMenu,$this->menu);

		return $res;
	}

}

?>