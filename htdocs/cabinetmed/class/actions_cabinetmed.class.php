<?php
/* Copyright (C) 2002-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2003      Brian Fraval         <brian@fraval.org>
 * Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2008      Patrick Raguin       <patrick.raguin@auguria.net>
 * Copyright (C) 2010      Juanjo Menent        <jmenent@2byte.es>
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
 *	\file       htdocs/cabinetmed/class/actions_cabinetmed.class.php
 *	\ingroup    societe
 *	\brief      File for third party class
 *	\version    $Id: actions_cabinetmed.class.php,v 1.1 2011/07/01 23:09:20 eldy Exp $
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	\class 		ActionsCabinetmed
 *	\brief 		Class to manage hooks for module Cabinetmed
 */
class ActionsCabinetmed
{
    var $db;
    var $error;
    var $errors=array();

    /**
     *    Constructor for class
     *    @param  DB     handler acces base de donnees
     */
    function ActionsCabinetmed($DB)
    {
        global $conf;

        $this->db = $DB;

        return 1;
    }


    /**
     *    Execute action
     *    @param        Object
     *    @param        action      'add', 'update', 'view'
     *    @return       int         <0 if KO,
     *                              =0 if OK but we want to process standard actions too,
     *                              >0 if OK and we want to replace standard actions
     */
    function doActions($object,$action)
    {
        global $langs,$conf;

        /*print 'action='.$action;
        var_dump($object);
        exit;*/

        return 0;
    }

}

?>
