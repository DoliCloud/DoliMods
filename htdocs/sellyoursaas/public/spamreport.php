<?php
/*
 * Page to report spam
 *
 * This page is a service called by SendGrid to report a SPAMMER.
 */

$tmpfile = '/home/admin/logs/spamreport.log';

fil_put_contents($tmpfile, "***** Spam report received *****\n");
file_put_contents($tmpfile, var_export($_SERVER, true), FILE_APPEND);
file_put_contents($tmpfile, var_export($_GET, true), FILE_APPEND);
file_put_contents($tmpfile, var_export($_POST, true), FILE_APPEND);

echo "Spam report received";

