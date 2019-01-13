#!/usr/bin/php
<?php
/**
  This script is a sendmail wrapper for php to log calls of the php mail() function.
  Author: Till Brehm, www.ispconfig.org
  (Hopefully) secured by David Goodwin <david @ _palepurple_.co.uk>

  Modify your php.ini file to add:
  sendmail_path = /usr/local/bin/phpsendmail.php
*/

//setlocale(LC_CTYPE, "en_US.UTF-8");

$sendmail_bin = '/usr/sbin/sendmail';
$logfile = '/var/log/phpsendmail.log';

//* Get the email content
$mail='';
$toline = ''; $ccline = ''; $bccline = '';
$nbto = 0; $nbcc = 0; $nbbcc = 0;
$fromline = '';
$referenceline = '';
$emailfrom = '';

$pointer = fopen('php://stdin', 'r');

while ($line = fgets($pointer)) {
        if(preg_match('/^to:/i', $line) ) {
		$toline .= trim($line)."\n";
		$linetmp = preg_replace('/^to:\s*/i','',trim($line));
		$tmpto=preg_split("/[\s,]+/", $linetmp);
		$nbto+=count($tmpto);
        }
	if(preg_match('/^cc:/i', $line) ) {
                $ccline .= trim($line)."\n";
                $linetmp = preg_replace('/^cc:\s*/i','',trim($line));
		$tmpcc=preg_split("/[\s,]+/", $linetmp);
                $nbcc+=count($tmpcc);
        }
	if(preg_match('/^bcc:/i', $line) ) {
                $bccline .= trim($line)."\n";
                $linetmp = preg_replace('/^bcc:\s*/i','',trim($line));
		$tmpbcc=preg_split("/[\s,]+/", $linetmp);
                $nbbcc+=count($tmpbcc);
        }
        if(preg_match('/^from:.*<(.*)>/i', $line, $reg) ) {
                $fromline .= trim($line)."\n";
		$emailfrom = $reg[1];
        }
        if(preg_match('/^references:/i', $line) ) {
                $referenceline .= trim($line)."\n";
        }
        $mail .= $line;
}

$tmpfile='/tmp/phpsendmail-'.posix_getuid().'-'.getmypid().'.tmp';
@unlink($tmpfile);
file_put_contents($tmpfile, $mail);
chmod ($tmpfile, 0660);

//* compose the sendmail command
#$command = 'echo ' . escapeshellarg($mail) . ' | '.$sendmail_bin.' -t -i ';
$command = 'cat '.$tmpfile.' | '.$sendmail_bin.' -t -i ';
$optionffound=0;
for ($i = 1; $i < $_SERVER['argc']; $i++) {
	if (preg_match('/-f/', $_SERVER['argv'][$i])) $optionffound++;
        $command .= escapeshellarg($_SERVER['argv'][$i]).' ';
}

if (! $optionffound)
{
	file_put_contents($logfile, date('Y-m-d H:i:s') . ' option -f not found. Args are '.join(' ',$_SERVER['argv']).'. We get if from the header'."\n", FILE_APPEND);
	$command .= "'-f".$emailfrom."'";
}

$ip=$_SERVER["REMOTE_ADDR"];
if (empty($ip))
{
        file_put_contents($logfile, date('Y-m-d H:i:s') . ' ip unknown. See tmp file '.$tmpfile."\n", FILE_APPEND);
#        exit(1);
}

// Rules
$MAXOK = 10;
$listofblacklistip=array('41.85.161.129','41.85.161.131','41.138.91.132','41.138.89.202','80.11.22.168','197.234.219.68','165.227.36.233','185.20.99.96','185.156.173.178','185.189.113.51');
$listofblacklistfrom=array('lcomanester@gmail.com','catherinepeladeau@gmail.com','peladeaucatherine@gmail.com','peladcath@gmail.com','catherinepeladeau9@ntymail.com','isabelleboel62@gmail.com','isabelleboel33@vivaldi.net','isa.boel@vivaldi.net','isabelleboel@vivaldi.net','ISABEL.BOEL1962@GMAIL.COM','catherinepeladeau@net-c.ca','kasorace@gmail.com','christianblandin63');
$listofblacklistcontent=array('thisisaspam','comanesterlucien','COMANESTER Lucien','isabelleboel','Isabelle BOEL','cabinetf.dako','dako@yahoo.com','dako@yahoo.com','dako@yahoo.fr','rinepelad','rine.pelad','brunof@netc.fr','peladcath','cathpelad','pealadoo','BARAKA qui m','Une berline de location et carburant à ma charge','chauffeur avec un Permis B pour mes déplacements','sur le site de www.pole-emploi.fr suite à la','christianblandi','location et carburant à ma charge sera','entreprise Français résident à Montreal','conduire étant handicapé');


//* Write the log
//file_put_contents($logfile, var_export($_SERVER, true)."\n", FILE_APPEND);
file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $toline, FILE_APPEND);
if ($ccline)  file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ccline, FILE_APPEND);
if ($bccline) file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $bccline, FILE_APPEND);
file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $fromline, FILE_APPEND);
file_put_contents($logfile, date('Y-m-d H:i:s') . ' Email detected into From: '. $emailfrom."\n", FILE_APPEND);
file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $referenceline, FILE_APPEND);
file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . (empty($_ENV['PWD'])?(empty($_SERVER["PWD"])?'':$_SERVER["PWD"]):$_ENV['PWD'])." - ".(empty($_SERVER["REQUEST_URI"])?'':$_SERVER["REQUEST_URI"])."\n", FILE_APPEND);


$blacklistofips = @file_get_contents('/tmp/blacklistip');
if (! empty($ip) && $blacklistofips)
{
        $blacklistofipsarray = explode("\n", $blacklistofips);
        if (is_array($blacklistofipsarray) && in_array($ip, $blacklistofipsarray))
        {
                file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ip . ' dolicloud rules ko blacklist - exit 2. Blacklisted ip '.$ip." found into file blacklistip\n", FILE_APPEND);
                exit(2);
        }
}



if (empty($fromline) && empty($emailfrom))
{
	file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ip . ' cant send email - exit 1. From not provided. See tmp file '.$tmpfile."\n", FILE_APPEND);
	exit(1);
}
elseif (! empty($ip) && in_array($ip, $listofblacklistip))
{
	file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ip . ' dolicloud rules ko blacklist - exit 2. Blacklisted ip '.$ip."\n", FILE_APPEND);
	file_put_contents('/tmp/blacklistip', $ip."\n", FILE_APPEND);
	chmod("/tmp/blacklistip", 0666);
        exit(2);
}
elseif (! empty($emailfrom) && in_array($emailfrom, $listofblacklistfrom))
{
        file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ip . ' dolicloud rules ko blacklist - exit 3. Blacklisted from '.$emailfrom."\n", FILE_APPEND);
		if (! empty($ip))
		{
			file_put_contents('/tmp/blacklistip', $ip."\n", FILE_APPEND);
			chmod("/tmp/blacklistip", 0666);
		}
        exit(3);
}
elseif (($nbto + $nbcc + $nbbcc) > $MAXOK)
{
        file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ip . ' dolicloud rules ko toomanyrecipient - exit 4 ( > '.$MAXOK.': ' . $nbto . ' ' . $nbcc . ' ' . $nbbcc . ') ' . (empty($_ENV['PWD'])?'':$_ENV['PWD'])."\n", FILE_APPEND);
        exit(4);
}
else
{
	foreach($listofblacklistcontent as $blackcontent)
	{
		if (preg_match('/'.preg_quote($blackcontent,'/').'/ims', $mail))
		{
			file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ip . ' dolicloud rules ko blacklist - exit 5. Blacklisted content '.$blackcontent."\n", FILE_APPEND);
			file_put_contents('/tmp/blacklistmail', $mail."\n", FILE_APPEND);
			chmod("/tmp/blacklistmail", 0666);
			if (! empty($ip))
			{
				file_put_contents('/tmp/blacklistip', $ip."\n", FILE_APPEND);
				chmod("/tmp/blacklistip", 0666);
			}
			exit(5);
		}
	}

        file_put_contents($logfile, date('Y-m-d H:i:s') . ' ' . $ip . ' dolicloud rules ok ( < '.$MAXOK.': ' . $nbto . ' ' . $nbcc . ' ' . $nbbcc . ') ' . (empty($_ENV['PWD'])?'':$_ENV['PWD'])."\n", FILE_APPEND);
}



file_put_contents($logfile, $command."\n", FILE_APPEND);

//* Execute the command
$resexec =  shell_exec($command);

if (empty($ip)) file_put_contents($logfile, "--- no ip detected ---", FILE_APPEND);
if (empty($ip)) file_put_contents($logfile, var_export($_SERVER, true), FILE_APPEND);
if (empty($ip)) file_put_contents($logfile, var_export($_ENV, true), FILE_APPEND);

return $resexec;
