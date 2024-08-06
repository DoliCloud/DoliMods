<?php
/* Copyright (C) 2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *     \file       htdocs/memcached/admin/memcached.php
 *     \brief      Page administration de memcached
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

$res=dol_include_once("/memcached/lib/memcached.lib.php");
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";

// Security check
if (!$user->admin)
accessforbidden();
if (! empty($dolibarr_memcached_setup_disable))	// Hidden variable to add to conf file to disable setup
accessforbidden();

$langs->load("admin");
$langs->load("errors");
$langs->load("install");
$langs->load("memcached@memcached");

$action=GETPOST('action');

//exit;

/*
 * Actions
 */

if ($action == 'set') {
	$error=0;

	if (GETPOST("MEMCACHED_SERVER") && !preg_match('/:/', GETPOST("MEMCACHED_SERVER")) && !preg_match('/\//', GETPOST("MEMCACHED_SERVER"))) {
		setEventMessages($langs->trans("ErrorBadParameters"), null, 'errors');
		$error++;
	}

	if (! $error) {
		dolibarr_set_const($db, "MEMCACHED_SERVER", GETPOST("MEMCACHED_SERVER"), 'chaine', 0, '', 0);
	}
}




/*
 * View
 */

$html=new Form($db);

$help_url="EN:Module_MemCached_En|FR:Module_MemCached|ES:M&oacute;dulo_MemCached";
llxHeader("", $langs->trans("MemcachedSetup"), $help_url);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans('MemcachedSetup'), $linkback, 'setup');

print '<br>';

$head=memcached_prepare_head();

dol_fiche_head($head, 'serversetup', '', -1);

print $langs->trans("MemcachedDesc")."<br>\n";
print "<br>\n";

$error=0;

// Check prerequisites
if (! class_exists("Memcache") && ! class_exists("Memcached")) {
	print '<div class="error">';
	//var_dump($langs->tab_translate['ClientNotFound']);
	//var_dump($langs->trans('ClientNotFound'));
	print $langs->trans("ClientNotFound");
	print '</div>';
	$error++;
} else {
	print $langs->trans("MemcachedClient", "Memcached").': ';
	if (class_exists("Memcached")) print $langs->trans("Available");
	else print $langs->trans("NotAvailable");
	print '<br>';
	print $langs->trans("MemcachedClient", "Memcache").': ';
	if (class_exists("Memcache")) print $langs->trans("Available");
	else print $langs->trans("NotAvailable");
	print '<br>';
	if (class_exists("Memcached") && class_exists("Memcache")) print $langs->trans("MemcachedClientBothAvailable", 'Memcached').'<br>';
	elseif (class_exists("Memcached")) print $langs->trans("OnlyClientAvailable", 'Memcached').'<br>';
	elseif (class_exists("Memcache")) print $langs->trans("OnlyClientAvailable", 'Memcache').'<br>';
}
print '<br>';


// Param
$var=true;
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="set">';

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
print '<td>'.$langs->trans("Examples").'</td>';
print '<td align="right"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
print "</tr>\n";

print '<tr class="oddeven"><td>'.$langs->trans("Server").':'.$langs->trans("Port");
print '<br><span class="opacitymedium">'.$langs->trans("or").'</span><br>';
print $langs->trans("SocketPath");
print '</td>';
print '<td>';
print '<input size="40" type="text" name="MEMCACHED_SERVER" value="' . getDolGlobalString('MEMCACHED_SERVER').'">';
print '</td>';
print '<td>127.0.0.1:11211<br>localhost:11211<br>';
print '/var/run/memcached/memcached.sock';
print '</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '</table>';
print '</div>';

print "</form>\n";

dol_fiche_end();

if (! $error) {
	if (class_exists("Memcached")) $m=new Memcached();
	elseif (class_exists("Memcache")) $m=new Memcache();
	else dol_print_error('', 'Should not happen');

	if (getDolGlobalString('MEMCACHED_SERVER')) {
		$tmparray=explode(':', getDolGlobalString('MEMCACHED_SERVER'));
		$server=$tmparray[0];
		$port = (empty($tmparray[1]) ? 0 : $tmparray[1]);

		dol_syslog("Try to connect to server ".$server." port ".$port." with class ".get_class($m));
		$result=$m->addServer($server, ($port || strpos($tmparray[0], '/') !== false) ? $port : 11211);
		//$m->setOption(Memcached::OPT_COMPRESSION, false);
		//print "xxx".$result;

		// This action must be set here and not in actions to be sure all lang files are already loaded
		if ($action == 'clear') {
			$error=0;
			if (! $error) {
				$m->flush();

				$mesg='<div class="ok">'.$langs->trans("Flushed").'</div>';
			}
		}


		dol_htmloutput_mesg($mesg);


		// Read cache
		$arraycache = $m->getStats();
	}

	// Action
	print '<div class="tabsAction">';
	if (is_array($arraycache)) {
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=clear">'.$langs->trans("FlushCache").'</a>';
	} else {
		print '<a class="butActionRefused" href="#">'.$langs->trans("FlushCache").'</a>';
	}
	print '</div>';
	print '<br>';


	// Statistics of cache server
	print '<table class="noborder" width="60%">';
	print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("Status").'</td></tr>';

	if (!getDolGlobalString('MEMCACHED_SERVER')) {
		print '<tr><td colspan="2">'.$langs->trans("ConfigureParametersFirst").'</td></tr>';
	} elseif (is_array($arraycache)) {
		$newarraycache = array();
		if (class_exists("Memcached")) $newarraycache = $arraycache;
		elseif (class_exists("Memcache")) $newarraycache[getDolGlobalString('MEMCACHED_SERVER')] = $arraycache;
		else dol_print_error('', 'Should not happen');

		foreach ($newarraycache as $key => $val) {
			print '<tr class="oddeven"><td>'.$langs->trans("MemcachedServer").'</td>';
			print '<td>'.$key.'</td></tr>';

			print '<tr class="oddeven"><td>'.$langs->trans("Version").'</td>';
			print '<td>'.$val['version'].'</td></tr>';

			print '<tr class="oddeven"><td>'.$langs->trans("Status").'</td>';
			print '<td>'.$langs->trans("On").'</td></tr>';
		}
	} else {
		print '<tr><td colspan="2">'.$langs->trans("FailedToReadServer").' - Result code = '.$resultcode.'</td></tr>';
	}

	print '</table>';
}

llxfooter();

$db->close();
