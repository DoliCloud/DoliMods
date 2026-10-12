<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	Prepare head fot tabs
 *
 *	@param	Object	$object		Object probe
 *	@return	array				Array of tabs headers
 */
function monitoring_prepare_head($object)
{
	global $langs, $conf;
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath('/monitoring/reports.php', 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans('Card');
	$head[$h][2] = 'probe';
	$h++;

	return $head;
}

/**
 *  Return list of probes to scan
 *
 *  @param  int		$active     	1 To get only activable probes
 *  @param	int		$forceprobeid	Force probe id
 *  @return	array					List of probes
 */
function getListOfProbes($active = 1, $forceprobeid = 0)
{
	global $db;

	$listofurls=array();

	$sql ="SELECT rowid, groupname, title, typeprot, url, url_params, useproxy, checkkey, frequency, maxval, active, status, lastreset,";
	$sql.=" oldesterrordate, oldesterrortext";
	$sql.=" FROM ".MAIN_DB_PREFIX."monitoring_probes";
	$sql.=" WHERE active = ".$active;
	if ($forceprobeid) $sql.=" AND rowid = ".$forceprobeid;
	$sql.=" ORDER BY rowid";

	dol_syslog("probes", LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql) {
		$num =$db->num_rows($resql);
		$i=0;

		while ($i < $num) {
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
	} else {
		dol_print_error($db);
	}

	return $listofurls;
}


/**
 *  Execute a probe
 *
 *	@param	array	$object		Array with property of object probe
 *  @return	mixed	$result		Result
 */
function init_probe($object)
{
	global $conf;

	// Protocol GET, POST, SOAP (http or https)
	if (in_array($object['typeprot'], array('GET','POST','SOAP')) && preg_match('/^http/i', $object['url'])) {
		$ch = curl_init();
		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 0);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		if ($object['useproxy']) {
			curl_setopt($ch, CURLOPT_PROXY, getDolGlobalString('MAIN_PROXY_HOST') . ":" . getDolGlobalString('MAIN_PROXY_PORT'));
			if (! empty($conf->global->MAIN_PROXY_USER)) curl_setopt($ch, CURLOPT_PROXYUSERPWD, getDolGlobalString('MAIN_PROXY_USER') . ":" . getDolGlobalString('MAIN_PROXY_PASS'));
		}

		if ($object['typeprot'] == 'GET') {
			curl_setopt($ch, CURLOPT_POST, false);
		}
		if ($object['typeprot'] == 'POST' || $object['typeprot'] == 'SOAP') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $object['url_params']);
		}

		curl_setopt($ch, CURLOPT_URL, $object['url']);

		return array('ch'=>$ch,'client'=>null);
	}

	/*
	if ($object['typeprot'] == 'SOAP' && preg_match('/^http/i',$object['url']))
	{
		$arrayoption=array(
					'location' => $object['url'],
					'soap_version'=>SOAP_1_2,
					'exceptions'=>true,
					'trace'=>1
		);
		// $arrayoption['keep_alive']=1;  // PHP 5.4
		if (1 == 2)    // Mode WSDL
		{
			// TODO
		}
		else           // Mode non WSDL
		{
			$arrayoption['uri']  ="http://www.dolibarr.org/ns/";
			$arrayoption['style']=SOAP_DOCUMENT;
			$arrayoption['use']  =SOAP_LITERAL;
		}
		if ($object['useproxy'])
		{
			$arrayoption['proxy_host']    = $conf->global->MAIN_PROXY_HOST;
			$arrayoption['proxy_port']    = $conf->global->MAIN_PROXY_PORT;
			if (! empty($conf->global->MAIN_PROXY_USER))
			{
				$arrayoption['proxy_login']   = $conf->global->MAIN_PROXY_USER;
				$arrayoption['proxy_password']= $conf->global->MAIN_PROXY_PASS;
			}
		}
		//$arrayoption['authentication']=SOAP_AUTHENTICATION_BASIC;
		//$arrayoption['login']='';
		//$arrayoption['password']='';

		$client = new SoapClient(null, $arrayoption);

		return array('ch'=>null,'client'=>$client);
	}*/

	return array('ch'=>null, 'client'=>null);
}

/**
 *  Execute a probe
 *
 *	@param	array	$object		Array with property of object probe
 *	@param	ch		$ch			ch
 *	@param	client	$client		client
 *  @return	array	$result		Result
 */
function execute_probe($object, $ch, $client)
{
	$content = '';
	$result = '';
	$found=0;

	list($usec, $sec) = explode(" ", microtime());
	$micro_start_time=((float) $usec + (float) $sec);

	// Protocol GET or POST
	if (($object['typeprot'] == 'GET' || $object['typeprot'] == 'POST') && preg_match('/^http/i', $object['url'])) {
		$content = curl_exec($ch);
		if ($content === false) $result.='Failed to get response, reason is: '.curl_error($ch);
		elseif (! empty($object['checkkey'])) {
			if (preg_match('/'.preg_quote($object['checkkey']).'/', $content)) $found=1;
			else $result.='Failed to find string "'.$object['checkkey'].'" into reponse string.';
		}
	}
	// Protocol TCPIP
	if ($object['typeprot'] == 'SOCKET' && preg_match('/^tcp/i', $object['url'])) {
		$resultat=0;
		$tmparray=explode(':', $object['url']);
		$adresse=preg_replace('/\//', '', $tmparray[1]);
		$service_port=$tmparray[2];
		//print 'adress='.$adresse.' port='.$service_port."\n";
		try {
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if ($socket) {
				//socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $seconds, 'usec' => $milliseconds));
				$resultat = socket_connect($socket, $adresse, $service_port);
				socket_close($socket);
			} else {
				$result.='Failed to create client socket TCP, reason is: '.socket_strerror(socket_last_error())."\n";
			}
		} catch (Exception $e) {
			$result.='Failed to connect to address='.$adresse.' port='.$service_port.', reason is: '.$e->getMessage()."\n";
		}
		if ($resultat < 0) {
			$result.='Failed to connect using socket. Reason is: '.socket_strerror($resultat);
		}
		/*
		 $envoi = "HEAD / HTTP/1.0\r\n\r\n";
		$envoi .= "Host: www.siteduzero.com\r\n";
		$envoi .= "Connection: Close\r\n\r\n";
		$reception = '';

		echo "Envoi de la requête HTTP HEAD...";
		socket_write($socket, $envoi, strlen($envoi));
		echo "OK.<br>";

		echo "Lire la réponse : <br><br>";
		while ($reception = socket_read($socket, 2048))
		echo $reception;
		*/
		//socket_close($socket);
	}
	// Protocol SOAP
	if ($object['typeprot'] == 'SOAP' && preg_match('/^http/i', $object['url'])) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($object['url_params']), 'Expect:'));

		$content = curl_exec($ch);
		if ($content === false) $result.='Failed to get response, reason is: '.curl_error($ch);
		elseif (! empty($object['checkkey'])) {
			if (preg_match('/'.preg_quote($object['checkkey']).'/', $content)) $found=1;
			else $result.='Failed to find string "'.$object['checkkey'].'" into reponse string.';
		}

		/*
		if ($client)
		{
		try
		{
		$args=array();
		$ops=array('soapaction' => 'http://www.Nanonull.com/TimeService/getVersions');
		$content = $client->__soapCall('getVersions',$args,$ops);
		}
		catch(Exception $e)
		{
		$result.='Failed to create locally a client WS. Reason is '.$e->getMessage();
		}
		}
		else
		{
		$result.='Failed to create locally a client WS.';
		}*/
	}

	list($usec, $sec) = explode(" ", microtime());
	$micro_end_time=((float) $usec + (float) $sec);
	$end_time=((float) $sec);
	$delay=($micro_end_time-$micro_start_time);

	return array(
		'micro_start_time'=>$micro_start_time,
		'micro_end_time'=>$micro_end_time,
		'end_time'=>$end_time,
		'delay'=>$delay,
		'found'=>$found,
		'errorstr'=>$result,
		'content'=>$content
	);
}



if (! function_exists('rrd_create')) {
	/**
	 * Create a RRD file
	 *
	 * @param 	string	$fname		Fname
	 * @param 	array	$opts		Opts
	 * @param 	int		$nbopts		Nbopts
	 * @return	int					0 if KO, >0 if OK
	 */
	function rrd_create($fname, $opts, $nbopts)
	{
		global $conf;

		$outputfile=$fname.'.out';

		// Parameteres execution
		$command=$conf->global->MONITORING_COMMANDLINE_TOOL;
		if (preg_match("/\s/", $command)) $command=escapeshellarg($command);	// Use quotes on command

		//$param=escapeshellarg($dolibarr_main_db_name)." -h ".escapeshellarg($dolibarr_main_db_host)." -u ".escapeshellarg($dolibarr_main_db_user)." -p".escapeshellarg($dolibarr_main_db_pass);
		$param=' create "'.$fname.'" ';
		foreach ($opts as $val) {
			$param.=$val.' ';
		}

		$fullcommandclear=$command." ".$param." 2>&1";
		//print $fullcommandclear;

		$handle = fopen($outputfile, 'w');
		if ($handle) {
			dol_syslog("Run command ".$fullcommandclear);
			$handlein = popen($fullcommandclear, 'r');
			while (!feof($handlein)) {
				$read = fgets($handlein);
				fwrite($handle, $read);
			}
			pclose($handlein);

			fclose($handle);

			if (! empty($conf->global->MAIN_UMASK)) {
				@chmod($outputfile, octdec($conf->global->MAIN_UMASK));
				@chmod($fname, octdec($conf->global->MAIN_UMASK));
			}
			return 1;
		} else {
			$langs->load("errors");
			dol_syslog("Failed to open file ".$outputfile, LOG_ERR);
			$errormsg=$langs->trans("ErrorFailedToWriteInDir");
			return 0;
		}
	}

	/**
	 * Update a RRD file
	 *
	 * @param	string	$fname		Fname
	 * @param 	int		$val		Val
	 * @return	int					0 if KO, >0 if OK
	 */
	function rrd_update($fname, $val)
	{
		global $conf;

		$outputfile=$fname.'.out';

		// Parameteres execution
		$command=$conf->global->MONITORING_COMMANDLINE_TOOL;
		if (preg_match("/\s/", $command)) $command=escapeshellarg($command);	// Use quotes on command

		//$param=escapeshellarg($dolibarr_main_db_name)." -h ".escapeshellarg($dolibarr_main_db_host)." -u ".escapeshellarg($dolibarr_main_db_user)." -p".escapeshellarg($dolibarr_main_db_pass);
		$param=' update "'.$fname.'" '.$val;

		$fullcommandclear=$command." ".$param." 2>&1";
		//print $fullcommandclear;

		$handle = fopen($outputfile, 'w');
		if ($handle) {
			dol_syslog("Run command ".$fullcommandclear);
			$handlein = popen($fullcommandclear, 'r');
			while (!feof($handlein)) {
				$read = fgets($handlein);
				fwrite($handle, $read);
			}
			pclose($handlein);

			fclose($handle);

			if (! empty($conf->global->MAIN_UMASK)) {
				@chmod($outputfile, octdec($conf->global->MAIN_UMASK));
			}
			return 1;
		} else {
			$langs->load("errors");
			dol_syslog("Failed to open file ".$outputfile, LOG_ERR);
			$errormsg=$langs->trans("ErrorFailedToWriteInDir");
			return 0;
		}
	}


	/**
	 * Create a RRD file
	 *
	 * @param	string	$fileimage	Fname
	 * @param 	array	$opts		Opts
	 * @param 	int		$nbopts		Nb opts
	 * @return	int					0 if KO, array if OK
	 */
	function rrd_graph($fileimage, $opts, $nbopts)
	{
		global $conf, $langs;

		$outputfile=$fileimage.'.out';

		// Parametres execution
		$command=$conf->global->MONITORING_COMMANDLINE_TOOL;
		if (preg_match("/\s/", $command)) $command=escapeshellarg($command);	// Use quotes on command

		//$param=escapeshellarg($dolibarr_main_db_name)." -h ".escapeshellarg($dolibarr_main_db_host)." -u ".escapeshellarg($dolibarr_main_db_user)." -p".escapeshellarg($dolibarr_main_db_pass);
		$param=' graph "'.$fileimage.'" ';
		foreach ($opts as $val) {
			$param.=$val.' ';
		}

		//var_dump($opts);
		$fullcommandclear=$command." ".$param." 2>&1";
		//print $fullcommandclear;

		//print $outputfile;
		$handle = fopen($outputfile, 'w');
		if ($handle) {
			dol_syslog("Run command ".$fullcommandclear);
			$handlein = popen($fullcommandclear, 'r');
			while (!feof($handlein)) {
				$read = fgets($handlein);
				fwrite($handle, $read);
			}
			pclose($handlein);

			fclose($handle);

			if (! empty($conf->global->MAIN_UMASK)) {
				@chmod($outputfile, octdec($conf->global->MAIN_UMASK));
				@chmod($fileimage, octdec($conf->global->MAIN_UMASK));
			}
			return array();
		} else {
			$langs->load("errors");
			dol_syslog("Failed to open file ".$outputfile, LOG_ERR);
			$errormsg=$langs->trans("ErrorFailedToWriteInDir");
			return 0;
		}
	}


	/**
	 * Show output content
	 *
	 * @param 	string	$fname		Fname
	 * @return	void
	 */
	function rrd_error($fname)
	{
		//print "dd".$fname;
		return file_get_contents($fname.'.out');
	}
}
