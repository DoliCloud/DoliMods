<?php
/* Copyright (C) 2010      Regis Houssin    <regis@dolibarr.fr>
 * Copyright (C) 2011-2012 Philippe Grand	<philippe.grand@atoo-net.com>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
header('Cache-Control: Public, must-revalidate');
header("Content-type: text/html; charset=".$conf->file->character_set_client);

?>
<!DOCTYPE html>
<head>
<title><?php echo $title; ?></title>
<meta name="robots" content="noindex,nofollow">
<meta name="author" content="Dolibarr Development Team">
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $favicon; ?>">
<title><?php echo $langs->trans('Login').' '.$title; ?></title><?php echo "\n"; ?>

<link rel="stylesheet" href="<?php echo DOL_URL_ROOT.'/theme/bootstrap/css/bootstrap.css.php'; ?>">
<!--<link rel="stylesheet" href="<?php echo DOL_URL_ROOT.'/theme/bootstrap/css/DT_bootstrap.css.php'; ?>">
<link rel="stylesheet" href="<?php echo DOL_URL_ROOT.'/theme/bootstrap/css/demo_page.css'; ?>">
<link rel="stylesheet" href="<?php echo DOL_URL_ROOT.'/theme/bootstrap/css/demo_table.css'; ?>">-->

<script type="text/javascript" src="<?php echo DOL_URL_ROOT.'/includes/jquery/js/jquery-latest.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo DOL_URL_ROOT.'/custom/bootstrap/core/js/bootstrap.js'; ?>"></script>
<!--<script type="text/javascript" src="<?php echo DOL_URL_ROOT.'/theme/bootstrap/js/DT_bootstrap.js'; ?>"></script>
<script type="text/javascript" src="<?php echo DOL_URL_ROOT.'/theme/bootstrap/js/jquery.js'; ?>"></script>
<script type="text/javascript" src="<?php echo DOL_URL_ROOT.'/custom/bootstrap/core/js/jquery.dataTables.js'; ?>"></script>-->
<?php if (! empty($conf->global->MAIN_HTML_HEADER)) print $conf->global->MAIN_HTML_HEADER;
print '<!-- HTTP_USER_AGENT = '.$_SERVER['HTTP_USER_AGENT'].' -->
</head>';
?>