<?php
/*
 * Page to report spam
 *
 * This page is a service called by SendGrid or other services to report a SPAM.
 */

$tmpfile = '/tmp/spamreport.log';
$date = strftime("%Y-%m-%d %H:%M:%S" ,time());

echo "Spam report received at ".$date;

file_put_contents($tmpfile, "\n***** Spam report received ".$date."*****\n", FILE_APPEND);
file_put_contents($tmpfile, var_export($_SERVER, true), FILE_APPEND);
file_put_contents($tmpfile, var_export($_GET, true), FILE_APPEND);
file_put_contents($tmpfile, var_export($_POST, true), FILE_APPEND);
file_put_contents("\n", FILE_APPEND);

mail('supervision@nltechno.com', 'Spam report recevied from SendGrid', 'Spam was reported by sendgrid');

