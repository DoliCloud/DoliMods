<?php
/*
 * Page to report spam
 *
 * This page is a service called by SendGrid to report a SPAMMER.
 */

$tmpfile = '/tmp/spamreport.log';

file_put_contents($tmpfile, var_export($_SERVER, true));
file_put_contents($tmpfile, var_export($_GET, true), FILE_APPEND);
file_put_contents($tmpfile, var_export($_POST, true), FILE_APPEND);

echo "Spam report received";

