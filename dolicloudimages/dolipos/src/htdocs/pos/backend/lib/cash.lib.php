<?php
/* Copyright (C) 2011 Juanjo Menent		  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */


/**
	    \file       htdocs/lib/bank.lib.php
		\brief      Ensemble de fonctions de base pour le module pos
        \ingroup    banque

		Ensemble de fonctions de base de dolibarr sous forme d'include
*/

function cash_prepare_head($obj)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath('/pos/backend/terminal/fiche.php',1).'?id='.$obj->id;
	$head[$h][1] = $langs->trans("CashCard");
	$head[$h][2] = 'cashname';
	$h++;
	
    $head[$h][0] = dol_buildpath('/pos/backend/terminal/annuel.php',1).'?id='.$obj->id;
    $head[$h][1] = $langs->trans("MonthlyReporting");
    $head[$h][2] = 'annual';
    $h++;

    $head[$h][0] = dol_buildpath('/pos/backend/terminal/graph.php',1).'?id='.$obj->id;
    $head[$h][1] = $langs->trans("Graph");
    $head[$h][2] = 'graph';
    $h++;
    
	$head[$h][0] = dol_buildpath('/pos/backend/terminal/info.php',1).'?id='.$obj->id;
    $head[$h][1] = $langs->trans("Log");
    $head[$h][2] = 'info';
    $h++;

	return $head;
}

?>