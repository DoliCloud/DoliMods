<?php
/* Copyright (C) 2002-2003 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *
 */

/**
 *      \file       htdocs/mantis/class/mantis.class.php
 *      \ingroup    mantis
 *      \brief      Ensemble des fonctions permettant d'acceder a la database mantis.
 */


/**
 *	Classe permettant d'acceder a la database mantis
 */
class Mantis
{
	var $localdb;

	var $date;
	var $duree = 0;     // Secondes
	var $texte;
	var $desc;

	var $error;


	/**
	 *	Constructeur de la classe d'interface a mantis
	 */
	function Mantis()
	{
		global $conf;
		global $dolibarr_main_db_type,$dolibarr_main_db_host,$dolibarr_main_db_user;
		global $dolibarr_main_db_pass,$dolibarr_main_db_name;

		// Defini parametres mantis (avec substitution eventuelle)
		$mantistype=preg_replace('/__dolibarr_main_db_type__/i', $dolibarr_main_db_type, $conf->mantis->db->type);
		$mantishost=preg_replace('/__dolibarr_main_db_host__/i', $dolibarr_main_db_host, $conf->mantis->db->host);
		$mantisport=preg_replace('/__dolibarr_main_db_port__/i', $dolibarr_main_db_port, $conf->mantis->db->port);
		$mantisuser=preg_replace('/__dolibarr_main_db_user__/i', $dolibarr_main_db_user, $conf->mantis->db->user);
		$mantispass=preg_replace('/__dolibarr_main_db_pass__/i', $dolibarr_main_db_pass, $conf->mantis->db->pass);
		$mantisname=preg_replace('/__dolibarr_main_db_name__/i', $dolibarr_main_db_name, $conf->mantis->db->name);

		// On initie la connexion a la base mantisendar
		$this->localdb = getDoliDBInstance($mantistype, $mantishost, $mantisuser, $mantispass, $mantisname, $mantisport);
	}
}
