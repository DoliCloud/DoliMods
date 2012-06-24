<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/cabinetmed/class/actions_cabinetmed.class.php
 *	\ingroup    societe
 *	\brief      File to control actions
 *	\version    $Id: actions_cabinetmed.class.php,v 1.8 2011/09/11 18:41:48 eldy Exp $
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	Class to manage hooks for module DoliCloud
 */
class ActionsDoliCloud
{
    var $db;
    var $error;
    var $errors=array();

    /**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
     */
    function ActionsDoliCloud($db)
    {
        $this->db = $db;
    }

    /**
     * Complete top right menu
     *
     * @param	array	$parameters		Array of parameters
     * @return	string					HTML content to add by hook
     */
    function printTopRightMenu($parameters)
    {
        global $langs, $user, $conf;

        $out='<td><div class="login"><a href="https://www.on.dolicloud.com/signIn/index">Cloud</a></div></td>';

        return $out;
    }
}

?>
