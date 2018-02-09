<?php
/* Copyright (C) 2018	Laurent Destailleur	<eldy@users.sourceforge.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    lib/sellyoursaas.lib.php
 * \ingroup sellyoursaas
 * \brief   Library files with common functions for SellYourSaas module
 */


/**
 * Return IP of server to deploy to
 */
function getRemoveServerDeploymentIp()
{
	global $conf;

	if (empty($conf->global->SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES)) $ip='localhost';
	else $ip = $conf->global->SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES;

	return $ip;
}

