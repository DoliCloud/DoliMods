<?php
/*
 * Page to report spam
 */

$tmpfile = '/tmp/spamreport.log';

file_put_contents($tmpfile, var_export($_SERVER, true));
file_put_contents($tmpfile, var_export($_GET, true), FILE_APPEND);
file_put_contents($tmpfile, var_export($_POST, true), FILE_APPEND);

echo "Spam report received";

