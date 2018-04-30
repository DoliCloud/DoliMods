<?php
/* Server agent for SellYourSaas */

$DEBUG = 1;

$fh = fopen('/var/log/remote_server.log','a+');
if (empty($fh))
{
	http_response_code(501);
	exit();
}

// Set array of allowed ips
$listofips = file_get_contents('./allowed_hosts.txt');
$tmparray=explode(',', trim($listofips));
$tmparray[]='127.0.0.1';

if (empty($listofips) || ! in_array($_SERVER['REMOTE_ADDR'], $tmparray))
{
	fwrite($fh, "\n".date('Y-m-d H:i:s').' >>>>>>>>>> Call done with bad ip '.$_SERVER['REMOTE_ADDR']." : Not into file ".realpath('.').'/allowed_hosts.txt'.".\n");
	fclose($fh);

	http_response_code(403);

	print 'IP address '.$_SERVER['REMOTE_ADDR'].' is not allowed to access this remote server agent. Check file ./allowed_hosts.txt'."\n";

	exit();
}

$param = preg_replace('/^\//', '', $_SERVER['REQUEST_URI']);
$tmparray=explode('?', $param, 2);

$paramspace='';
if (! empty($tmparray[1]))
{
	$paramarray = explode('&', urldecode($tmparray[1]));
	foreach($paramarray as $val)
	{
		$paramspace.=($val!='' ? $val : '-').' ';
	}
}


/*
 * Actions
 */

$output='';
$return_var=0;

if ($DEBUG) fwrite($fh, "\n".date('Y-m-d H:i:s').' >>>>>>>>>> Call for action '.$tmparray[0].' by '.$_SERVER['REMOTE_ADDR'].' URI='.$_SERVER['REQUEST_URI']."\n");
else fwrite($fh, "\n".date('Y-m-d H:i:s').' >>>>>>>>>> Call for action '.$tmparray[0]." by ".$_SERVER['REMOTE_ADDR']."\n");

if (in_array($tmparray[0], array('deploy', 'undeploy', 'deployall', 'undeployall')))
{
	if ($DEBUG) fwrite($fh, date('Y-m-d H:i:s').' ./action_deploy_undeploy.sh '.$tmparray[0].' '.$paramspace."\n");
	else fwrite($fh, date('Y-m-d H:i:s').' ./action_deploy_undeploy.sh '.$tmparray[0].' ...'."\n");

	exec('./action_deploy_undeploy.sh '.$tmparray[0].' '.$paramspace.' 2>&1', $output, $return_var);

	fwrite($fh, date('Y-m-d H:i:s').' return = '.$return_var."\n");
	fwrite($fh, date('Y-m-d H:i:s').' '.join("\n",$output));
	fclose($fh);

	$httpresponse = 500;
	if ($return_var == 0)
	{
		$httpresponse = 200;
	}
	http_response_code($httpresponse);

	print 'action_deploy_undeploy.sh for action '.$tmparray[0].' return '.$return_var.", \n";
	print "so remote agent returns http code ".$httpresponse."\n";

	exit();
}
if (in_array($tmparray[0], array('suspend', 'unsuspend')))
{
	if ($DEBUG) fwrite($fh, date('Y-m-d H:i:s').' ./action_deploy_undeploy.sh '.$tmparray[0].' '.$paramspace."\n");
	else fwrite($fh, date('Y-m-d H:i:s').' ./action_deploy_undeploy.sh '.$tmparray[0].' ...'."\n");

	exec('./action_suspend_unsuspend.sh '.$tmparray[0].' '.$paramspace.' 2>&1', $output, $return_var);

	fwrite($fh, date('Y-m-d H:i:s').' return = '.$return_var."\n");
	fwrite($fh, date('Y-m-d H:i:s').' '.join("\n",$output));
	fclose($fh);

	$httpresponse = 500;
	if ($return_var == 0)
	{
		$httpresponse = 200;
	}
	http_response_code($httpresponse);

	print 'action_deploy_undeploy.sh for action '.$tmparray[0].' return '.$return_var.", \n";
	print "so remote agent returns http code ".$httpresponse."\n";

	exit();
}

fwrite($fh, date('Y-m-d H:i:s').' action code "'.$tmparray[0].'" not supported'."\n");
fclose($fh);

http_response_code(404);

exit();


