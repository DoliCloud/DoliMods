<?php

$list = [
    'admin',
    'ChangeLog.md',
    'class',
    'composer.json',
    'composer.lock',
    'COPYING',
    'core',
    'doc',
    'img',
    'importovhinvoice.php',
    'includes',
    'langs',
    'lib',
    'ovh_listinfoserver.php',
    'README.md',
    'README-update-ovh-lib-with-composer.txt',
    'scripts',
    'sms_member.php',
    'sms_thirdparty.php',
    'sql',
    'wrapper.php',
];

$tab = glob("../core/modules/mod*.class.php");
if (count($tab) == 1) {
	$file = $tab[0];
	$mod  = "";
	$pattern = "/.*mod(?<mod>.*)\.class\.php/";
	if (preg_match_all($pattern, $file, $matches)) {
		$mod = strtolower(reset($matches['mod']));
	}

	echo "file = $file\n";
	echo "mod = $mod\n";
	if (!file_exists($file) || $mod == "") {
		echo "Erreur de détection du fichier et/ou du code du module ...";
		exit -1;
	}
} else {
	echo "Erreur il semblerait qu'il y ait plusieurs fichiers mod* dans le répertoire ...";
	exit -1;
}

$contents = file_get_contents($file);
$pattern = "/^.*this->version\s*=\s*'(?<version>.*)'\s*;.*\$/m";

// search, and store all matching occurences in $matches
$version = '';
if (preg_match_all($pattern, $contents, $matches)) {
	$version = reset($matches['version']);
}

$zipfile = "module_" . $mod . "-" . $version . ".zip";

function delTree($dir)
{
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function rcopy($src, $dst)
{
	if (is_dir($src)) {
		// Make the destination directory if not exist
		@mkdir($dst);
		// open the source directory
		$dir = opendir($src);

		// Loop through the files in source directory
		while ($file = readdir($dir)) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					// Recursively calling custom copy function
					// for sub directory
					rcopy($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	} elseif (is_file($src)) {
		copy($src, $dst);
	} else {
		print "erreur pour *$src*\n";
	}
}

$tmpdir = tempnam('/tmp', $mod . "-module");
unlink($tmpdir);
mkdir($tmpdir);
$dst = $tmpdir . "/htdocs/";
mkdir($dst);
$dst .= $mod;
mkdir($dst);

echo "copy to $dst...\n";

foreach ($list as $l) {
	rcopy("../" . $l, $dst . '/' . $l);
}

chdir($tmpdir);
shell_exec("zip -r $zipfile htdocs");
if (file_exists($zipfile)) {
	rename($zipfile, "/tmp/" . $zipfile);
	delTree($tmpdir);
}

echo "fichier dispo /tmp/$zipfile ...\n";
