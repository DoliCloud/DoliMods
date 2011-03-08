<?php
/* Copyright (C) 2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *  \file       htdocs/monitoring/lib/monitoring.lib.php
 *  \brief      Ensemble de fonctions de base pour le module Monitoring
 *  \ingroup    monitoring
 *  \version    $Id: monitoring.lib.php,v 1.5 2011/03/08 23:52:18 eldy Exp $
 */

$linktohelp='EN:Module_Monitoring_En|FR:Module_Monitoring|ES:Modulo_Monitoring';

if (! function_exists('rrd_create'))
{
	/**
	 * Create a RRD file
	 * @param 		$fname
	 * @param 		$opts
	 * @param 		$nbopts
	 * @return		int		0 if KO, >0 if OK
	 */
	function rrd_create ($fname, $opts, $nbopts)
	{
		global $conf;

		$outputfile=$fname.'.out';

		// Parameteres execution
		$command=$conf->global->MONITORING_COMMANDLINE_TOOL;
		if (preg_match("/\s/",$command)) $command=escapeshellarg($command);	// Use quotes on command

		//$param=escapeshellarg($dolibarr_main_db_name)." -h ".escapeshellarg($dolibarr_main_db_host)." -u ".escapeshellarg($dolibarr_main_db_user)." -p".escapeshellarg($dolibarr_main_db_pass);
		$param=' create "'.$fname.'" ';
		foreach ($opts as $val)
		{
			$param.=$val.' ';
		}

		$fullcommandclear=$command." ".$param." 2>&1";
		//print $fullcommandclear;

		$handle = fopen($outputfile, 'w');
		if ($handle)
		{
			dol_syslog("Run command ".$fullcommandclear);
			$handlein = popen($fullcommandclear, 'r');
			while (!feof($handlein))
			{
				$read = fgets($handlein);
				fwrite($handle,$read);
			}
			pclose($handlein);

			fclose($handle);

			if (! empty($conf->global->MAIN_UMASK))
			{
				@chmod($outputfile, octdec($conf->global->MAIN_UMASK));
				@chmod($fname, octdec($conf->global->MAIN_UMASK));
			}
			return 1;
		}
		else
		{
			$langs->load("errors");
			dol_syslog("Failed to open file ".$outputfile,LOG_ERR);
			$errormsg=$langs->trans("ErrorFailedToWriteInDir");
			return 0;
		}
	}

	/**
	 * Update a RRD file
	 * @param 		$fname
	 * @param 		$val
	 * @return		int		0 if KO, >0 if OK
	 */
	function rrd_update ($fname, $val)
	{
		global $conf;

		$outputfile=$fname.'.out';

		// Parameteres execution
		$command=$conf->global->MONITORING_COMMANDLINE_TOOL;
		if (preg_match("/\s/",$command)) $command=escapeshellarg($command);	// Use quotes on command

		//$param=escapeshellarg($dolibarr_main_db_name)." -h ".escapeshellarg($dolibarr_main_db_host)." -u ".escapeshellarg($dolibarr_main_db_user)." -p".escapeshellarg($dolibarr_main_db_pass);
		$param=' update "'.$fname.'" '.$val;

		$fullcommandclear=$command." ".$param." 2>&1";
		//print $fullcommandclear;

		$handle = fopen($outputfile, 'w');
		if ($handle)
		{
			dol_syslog("Run command ".$fullcommandclear);
			$handlein = popen($fullcommandclear, 'r');
			while (!feof($handlein))
			{
				$read = fgets($handlein);
				fwrite($handle,$read);
			}
			pclose($handlein);

			fclose($handle);

			if (! empty($conf->global->MAIN_UMASK))
			{
				@chmod($outputfile, octdec($conf->global->MAIN_UMASK));
			}
			return 1;
		}
		else
		{
			$langs->load("errors");
			dol_syslog("Failed to open file ".$outputfile,LOG_ERR);
			$errormsg=$langs->trans("ErrorFailedToWriteInDir");
			return 0;
		}
	}


	/**
	 * Create a RRD file
	 * @param 		$fname
	 * @param 		$opts
	 * @param 		$nbopts
	 * @return		int		0 if KO, array if OK
	 */
	function rrd_graph ($fileimage, $opts, $nbopts)
	{
		global $conf, $langs;

		$outputfile=$fileimage.'.out';

		// Parametres execution
		$command=$conf->global->MONITORING_COMMANDLINE_TOOL;
		if (preg_match("/\s/",$command)) $command=escapeshellarg($command);	// Use quotes on command

		//$param=escapeshellarg($dolibarr_main_db_name)." -h ".escapeshellarg($dolibarr_main_db_host)." -u ".escapeshellarg($dolibarr_main_db_user)." -p".escapeshellarg($dolibarr_main_db_pass);
		$param=' graph "'.$fileimage.'" ';
		foreach ($opts as $val)
		{
			$param.=$val.' ';
		}

		//var_dump($opts);
		$fullcommandclear=$command." ".$param." 2>&1";
		//print $fullcommandclear;

		$handle = fopen($outputfile, 'w');
		if ($handle)
		{
			dol_syslog("Run command ".$fullcommandclear);
			$handlein = popen($fullcommandclear, 'r');
			while (!feof($handlein))
			{
				$read = fgets($handlein);
				fwrite($handle,$read);
			}
			pclose($handlein);

			fclose($handle);

			if (! empty($conf->global->MAIN_UMASK))
			{
				@chmod($outputfile, octdec($conf->global->MAIN_UMASK));
				@chmod($fileimage, octdec($conf->global->MAIN_UMASK));
			}
			return array();
		}
		else
		{
			$langs->load("errors");
			dol_syslog("Failed to open file ".$outputfile,LOG_ERR);
			$errormsg=$langs->trans("ErrorFailedToWriteInDir");
			return 0;
		}
	}


	/**
	 * Show output content
	 * @param unknown_type $fname
	 */
	function rrd_error($fname)
	{
		//print "dd".$fname;
		return file_get_contents($fname.'.out');
	}
}


?>
