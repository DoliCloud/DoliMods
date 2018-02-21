<?php

if (! in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1','1.2.3.4')))
{
	print 'Forbidden by IP address';
	http_response_code(503);
	exit();
}

$param = preg_replace('/^\//', '', $_SERVER['REQUEST_URI']);
$tmparray=explode('?', $param, 2);
$paramarray = explode('&', urldecode($tmparray[1]));
$paramspace='';
foreach($paramarray as $val)
{
	$paramspace.=($val!='' ? $val : '-').' ';
}

/*
 * Actions
 */

$output='';
$return_var=0;

if (in_array($tmparray[0], array('deploy', 'undeploy', 'deployall', 'undeployall')))
{
	exec('../action_deploy_undeploy.sh '.$tmparray[0].' '.$paramspace, $output, $return_var);

	print 'return = '.$return_var;
	print join("\n",$output);

	http_response_code(200);
	exit();
}
if (in_array($tmparray[0], array('suspend', 'unsuspend')))
{
	exec('../action_suspend_unsuspend.sh '.$tmparray[0].' '.$paramspace, $output, $return_var);

	print 'return = '.$return_var;
	print join("\n",$output);

	http_response_code(200);
	exit();
}

http_response_code(404);
exit();


