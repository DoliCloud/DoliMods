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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *  \file       htdocs/monitoring/lib/monitoring.lib.php
 *  \brief      Ensemble de fonctions de base pour le module Monitoring
 *  \ingroup    monitoring
 *  \version    $Id: monitoring.lib.php,v 1.10 2011/04/13 21:18:58 eldy Exp $
 */

$linktohelp='EN:Module_Monitoring_En|FR:Module_Monitoring|ES:Modulo_Monitoring';



/**
 *
 */
function monitoring_prepare_head($object)
{
    global $langs, $conf;
    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath('/monitoring/index.php',1).'?id='.$object->id;
    $head[$h][1] = $langs->trans('Card');
    $head[$h][2] = 'probe';
    $h++;

    return $head;
}

/**
 *  Return list of probes to scan
 *
 *  @param  int		$active     1 To get only activable probes
 *  @return	array				List of probes
 */
function getListOfProbes($active=1)
{
    global $db;

    $listofurls=array();

    $sql ="SELECT rowid, groupname, title, typeprot, url, url_params, useproxy, checkkey, frequency, maxval, active, status, lastreset,";
    $sql.=" oldesterrordate, oldesterrortext";
    $sql.=" FROM ".MAIN_DB_PREFIX."monitoring_probes";
    $sql.=" WHERE active = ".$active;
    $sql.=" ORDER BY rowid";

    dol_syslog("probes sql=".$sql,LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
        $num =$db->num_rows($resql);
        $i=0;

        while ($i < $num)
        {
            $obj = $db->fetch_object($resql);

            $listofurls[$i]=array(
            	'code'=>$obj->rowid,
            	'groupname'=>$obj->groupname,
            	'title'=>$obj->title,
            	'typeprot'=>$obj->typeprot,
            	'url'=>$obj->url,
            	'url_params'=>$obj->url_params,
            	'useproxy'=>$obj->useproxy,
                'checkkey'=>$obj->checkkey,
                'frequency'=>$obj->frequency,
                'active'=>$obj->active,
                'status'=>$obj->status,
                'max'=>$obj->maxval,
                'lastreset'=>$db->jdate($obj->lastreset),
                'oldesterrordate'=>$db->jdate($obj->oldesterrordate),
                'oldesterrortext'=>$obj->oldesterrortext
                );

            $i++;
        }
    }
    else
    {
        dol_print_error($db);
    }

    return $listofurls;
}




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
	 *
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
     *
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

		//print $outputfile;
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
     *
	 * @param unknown_type $fname
	 */
	function rrd_error($fname)
	{
		//print "dd".$fname;
		return file_get_contents($fname.'.out');
	}
}


?>
