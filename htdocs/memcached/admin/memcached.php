<?php
/* Copyright (C) 2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *     \file       htdocs/memcached/admin/memcached.php
 *     \brief      Page administration de memcached
 *     \version    $Id: memcached.php,v 1.6 2010/05/25 23:24:21 eldy Exp $
 */

$res=@include("../main.inc.php");
if (! $res) $res=@include("../../main.inc.php");	// If pre.inc.php is called by jawstats
if (! $res) $res=@include("../../../dolibarr/htdocs/main.inc.php");		// Used on dev env only
if (! $res) $res=@include("../../../../dolibarr/htdocs/main.inc.php");	// Used on dev env only
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");

// Security check
if (!$user->admin)
accessforbidden();

$langs->load("admin");
$langs->load("errors");
$langs->load("install");
$langs->load("memcached@memcached");


/*
 * Actions
 */
if ($_POST["action"] == 'set')
{
	$error=0;
	if (! $error)
	{
		dolibarr_set_const($db,"MEMCACHED_SERVER",$_POST["MEMCACHED_SERVER"],'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"MEMCACHED_PORT",$_POST["MEMCACHED_PORT"],'chaine',0,'',$conf->entity);
	}
}




/*
 * View
 */

$help_url="EN:Module_Memcached_En|FR:Module_Memcached|ES:M&oacute;dulo_Memcached";
llxHeader("",$langs->trans("MemcachedSetup"),$help_url);

$html=new Form($db);
print_fiche_titre($langs->trans('MemcachedSetup'),'','setup');

print $langs->trans("MemcachedDesc")."<br>\n";
print "<br>\n";

$error=0;

// Check prerequisites
if (! class_exists("Memcache") && ! class_exists("Memcached"))
{
	print 'Your PHP must support Memcached client features (Nor the Memcached, nor the Memcache verion of client was found).';
	$error++;
}
else
{
	print $langs->trans("MemcachedClient","Memcache").': ';
	if (class_exists("Memcache")) print $langs->trans("Available");
	else print $langs->trans("NotAvailable");
	print '<br>';
	print $langs->trans("MemcachedClient","Memcached").': ';
	if (class_exists("Memcache")) print $langs->trans("Available");
	else print $langs->trans("NotAvailable");
	print '<br>';
	if (class_exists("Memcache") && class_exists("Memcached")) print $langs->trans("MemcachedClientBothAvailable",'Memcached').'<br>';
	else if (class_exists("Memcache")) print $langs->trans("OnlyClientAvailable",'Memcache').'<br>';
	else if (class_exists("Memcached")) print $langs->trans("OnlyClientAvailable",'Memcached').'<br>';
}
print '<br>';


// Param
$var=true;
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
print '<td align="right"><input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</td>';
print "</tr>\n";

$var=!$var;
print '<tr '.$bc[$var].'><td width=\"50%\">'.$langs->trans("Server").'</td>';
print '<td colspan="2">';
print '<input size="20" type="text" name="MEMCACHED_SERVER" value="'.$conf->global->MEMCACHED_SERVER.'">';
print ' (localhost)';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td width=\"50%\">'.$langs->trans("Port").'</td>';
print '<td colspan="2">';
print '<input size="20" type="text" name="MEMCACHED_PORT" value="'.$conf->global->MEMCACHED_PORT.'">';
print ' (11211)';
print '</td></tr>';

print '</table>';

print "</form>\n";

if (! $error)
{

	$m=new Memcached();
	$result=$m->addServer($conf->global->MEMCACHED_SERVER, $conf->global->MEMCACHED_PORT);
	//$m->setOption(Memcached::OPT_COMPRESSION, false);

	// This action must be set here and not in actions to be sure all lang files are already loaded
	if ($_GET["action"] == 'clear')
	{
		$error=0;
		if (! $error)
		{
			$m->flush();

			$mesg='<div class="ok">'.$langs->trans("Flushed").'</div>';
		}
	}

	if ($mesg) print '<br>'.$mesg;


	// Read cache
	$arraycache=$m->getStats();
	$resultcode=$m->getResultCode();
	//var_dump($arraycache);

	// Action
	print '<div class="tabsAction">';
	if ($resultcode == 0)
	{
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=clear">'.$langs->trans("FlushCache").'</a>';
	}
	else
	{
		print '<a class="butActionRefused" href="#">'.$langs->trans("FlushCache").'</a>';
	}
	print '</div>';
	print '<br>';


	// Statistics of cache server
	print '<table class="noborder">';
	print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("InformationsOnCacheServer").'</td></tr>';

	if (empty($conf->global->MEMCACHED_SERVER) || empty($conf->global->MEMCACHED_PORT))
	{
		print '<tr><td colspan="2">'.$langs->trans("ConfigureParametersFirst").'</td></tr>';
	}
	else if ($resultcode == 0)
	{
		foreach($arraycache as $key => $val)
		{
			print '<tr '.$bc[0].'><td>'.$langs->trans("MemcachedServer").'</td>';
			print '<td>'.$key.'</td></tr>';

			print '<tr '.$bc[1].'><td>'.$langs->trans("Version").'</td>';
			print '<td>'.$val['version'].'</td></tr>';

			print '<tr '.$bc[0].'><td>'.$langs->trans("ItemsInCache").'</td>';
			print '<td>'.$val['curr_items'].'</td></tr>';

			print '<tr '.$bc[1].'><td>'.$langs->trans("SizeOfCache").'</td>';
			print '<td>'.$val['bytes'].'</td></tr>';

			print '<tr '.$bc[0].'><td>'.$langs->trans("NumberOfCacheInsert").'</td>';
			print '<td>'.$val['cmd_set'].'</td></tr>';

			print '<tr '.$bc[1].'><td>'.$langs->trans("NumberOfCacheRead").'</td>';
			print '<td>'.$val['get_hits'].'/'.$val['cmd_get'].'</td></tr>';

	/*		print '<tr '.$bc[1].'><td>Content</td>';
			print '<td>';
			// Show list of items
			print '</td></tr>';
	*/	}
	}
	else
	{
		print '<tr><td colspan="2">'.$langs->trans("FailedToReadServer").' - Result code = '.$resultcode.'</td></tr>';
	}

	print '</table>';

}

llxfooter('$Date: 2010/05/25 23:24:21 $ - $Revision: 1.6 $');
?>