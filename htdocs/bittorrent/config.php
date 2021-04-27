<?php

$GLOBALS['scrape'] = false;
$GLOBALS['report_interval'] = 1800;
$GLOBALS['min_interval'] = 300;
$GLOBALS['maxpeers'] = 50;
$GLOBALS['NAT'] = false;
$GLOBALS['persist'] = false;
$GLOBALS['ip_override'] = false;
$GLOBALS['countbytes'] = true;
$GLOBALS['title'] = '';
$dbhost = $dolibarr_main_db_host;
$dbuser = $dolibarr_main_db_user;
$dbpass = $dolibarr_main_db_pass;
$database = $dolibarr_main_db_name;
$enablerss = true;
$rss_title = 'Dolibarr BitTorrent tracker';
$rss_link = '';
$rss_description = 'Files delivered by Dolibarr BitTorrent Tracker';
$GLOBALS['max_upload_rate'] = 100;
$GLOBALS['max_uploads'] = 5;
$timezone = '+0100';

$prefix = $conf->db->prefix.'bt_';

// Should work with DOL_URL_ROOT='' or DOL_URL_ROOT='/dolibarr'
$firstpart=$dolibarr_main_url_root;
$firstpart=preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/', '', $firstpart);
$website_url=$firstpart.DOL_URL_ROOT;
