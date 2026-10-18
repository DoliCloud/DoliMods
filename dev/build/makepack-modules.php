#!/usr/bin/env php
<?php
/*
 * Copyright (C) 2025-2026  Mohamed Daoud           <mdaoud@dolicloud.com>
 * Copyright (C) 2025-2026  Laurent Destailleur     <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * This script searches through all module directories (into htdocs/) for files named 'index.yaml'
 * and combines their contents into a single 'index.yaml' file at the root of the repository.
 */

// Name of the directory, relative to the root of the repository, that contains the modules
define('MODULES_DIR_NAME', 'htdocs');

/**
 * Recursively searches directories for 'index.yaml' files.
 *
 * @param   string      $dir Directory to search.
 * @param   int         $level Max depth of directories to search.
 * @param   array       $results Array to store found file paths.
 * @param   int         $currentLevel Current depth level of the search.
 * @return  array       Paths of found 'index.yaml' files.
 */
function findIndexYamlFiles($dir, $level = 1, &$results = array(), $currentLevel = 1)
{
	if ($currentLevel > $level) {
		return $results;
	}

	$files = scandir($dir);

	foreach ($files as $key => $value) {
		$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
		if (!is_dir($path)) {
			if (basename($path) == 'index.yaml') {
				$results[] = $path;
			}
		} elseif ($value != "." && $value != "..") {
			findIndexYamlFiles($path, $level, $results, $currentLevel + 1);
		}
	}

	return $results;
}

/**
 * Combines the contents of multiple YAML files into a single file index.yaml by updating substitution keys.
 *
 * @param   array   $files Array of file paths to combine.
 * @param   string  $outputFile Path of the output file.
 * @return	void
 */
function combineYamlFiles($files, $outputFile)
{
	$combinedContent = '';
	foreach ($files as $file) {
		print "\n-- Process file ".$file."\n";
		$content = file_get_contents($file);

		if ($content) {
			// Remove any text before the first occurrence of 'packages:' for all files except the first one
			if ($file !== $files[0]) {
				// Remove any text before the first occurrence of 'packages:' for all files except the first one
				$content = preg_replace('/^.*?(?=packages:\s*)/s', '', $content);
			}

			// remove the first line of the file
			$content = preg_replace('/^.+\n/', '', $content);

			// Complete auto tags
			$content = completAutoTags($content, dirname($file));

			if ($content != '-1') {
				$combinedContent .= $content . "\n\n";
			} else {
				print "Failed to get value to replace into the yaml source file\n";
				$combinedContent .= "\n\n";
			}
		} else {
			print "Failed to get content of yaml source file\n";
		}
	}
	file_put_contents($outputFile, $combinedContent);
}

/**
 * Completes auto tags in the YAML content.
 *
 * @param   string  $content        YAML content.
 * @param   string  $modulePath     Path of the module directory.
 * @return  string  Modified YAML content.
 */
function completAutoTags($content, $modulePath)
{
	// Look for missing auto tags in the module's core class file
	$DOLIBARRMAXBYDEFAULT = '24.0';

	$tagsToExtractFromDescriptor = array(
		'current_version'   => 'version',
		'dolibarrmin'       => 'need_dolibarr_version',
		'dolibarrmax'       => 'max_dolibarr_version',
		'phpmin'            => 'phpmin',
		'phpmax'            => 'phpmax',
	);

	$modulename = '';
	$reg = array();
	if (preg_match('/modulename:\s*[\'"]([^\'"]+)[\'"]/', $content, $reg)) {
		$modulename = $reg[1];
	}
	if (empty($modulename)) {
		print "Can't extract module name from yaml file\n";
		return -1;
	}

	// Set the name of the local descriptor module file
	$coreClassFile = $modulePath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'mod' . $modulename . '.class.php';

	/**
	 * Replaces only the double quotes that surround values in the given content.
	 *
	 * This function uses a regular expression to find patterns in the format of `: "value"`,
	 * and replaces the double quotes with single quotes. Additionally, it replaces any
	 * internal single quotes within the value with typographic apostrophes (’).
	 *
	 * @param string $content The content in which to perform the replacements.
	 * @return string The modified content with the replacements made.
	 */
	$content = preg_replace_callback('/:\s*"([^"]*)"/', function ($matches) {
		return ": '" . str_replace("'", "’", $matches[1]) . "'";
	}, $content);

	print "Process module path: ".$modulePath."\n";

	$git = '';
	$gitbranch = '';
	$gitsystem = '';

	// We extract data from the YAML file
	$reg = array();
	if (preg_match('/git:\s*[\'"]([^\'"]+)[\'"]/', $content, $reg)) {
		$git = $reg[1];
	}
	if (empty($git)) {
		print "Can't extract git url from yaml file\n";
		return -1;
	}
	if (preg_match('/git-branch:\s*[\'"]([^\'"]+)[\'"]/', $content, $reg)) {
		$gitbranch = $reg[1];
	}
	if (preg_match('/git-system:\s*[\'"]([^\'"]+)[\'"]/', $content, $reg)) {
		$gitsystem = $reg[1];
	}

	$coreClassContent = '';
	if (file_exists($coreClassFile)) {
		print "Try to get local content of descriptor file ".$coreClassFile."\n";
		$coreClassContent = file_get_contents($coreClassFile);
		//print "Core class file content:\n$coreClassContent\n";
	} else {
		// Try do get remote content.
		// Define the URL to get the descriptor file.
		// For github sources
		if (empty($gitsystem) || $gitsystem == 'github') {
			$urltoget = preg_replace('/https:\/\/github.com/', 'https://raw.githubusercontent.com', $git);
			$urltoget = preg_replace('/\/tree\//', '/refs/heads/', $urltoget);
			$urltoget = preg_replace('/\.git$/', '/refs/heads/main', $urltoget);
			$urltoget .= '/core/modules/mod'.$modulename.'.class.php';
		} elseif ($gitsystem == 'gitlab') {
			$urltoget = preg_replace('/\.git$/', '/-/raw/'.$gitbranch, $git);
			$urltoget .= '/core/modules/mod'.$modulename.'.class.php';
			$urltoget .= '?inline=false';
			// Example: 'https://mydomain.com/account/project/repo/-/blob/master/core/modules/modFacturx.class.php?ref_type=heads'
		}

		print "Try to get remote content of descriptor file ".$urltoget." (url guessed from ".$git.")\n";
		$coreClassContent = file_get_contents($urltoget);
		if (empty($coreClassContent)) {
			print "Failed to get remote content descriptor file.\n";
			return -1;
		} else {
			print "Success in getting remote content descriptor file.\n";
		}
	}

	$coreClassContent = preg_replace('/^\s*\/\/.*$/m', '', $coreClassContent);


	if ($coreClassContent) {
		$version = '';

		// Update tags with a corresponding value found into the descriptor file
		foreach ($tagsToExtractFromDescriptor as $tag => $property) {
			if (preg_match('/(' . preg_quote($tag) . ':\s*)["\']?auto["\']?/', $content)) {	// If the key: is 'auto'
				$value = '';

				$matches = array();
				if (preg_match('/\$this->' . preg_quote($property) . '\s*=\s*array\(([^)]+)\)/', $coreClassContent, $matches)) {
					// Case where the value is an array
					$value = trim($matches[1]);
					$value = preg_replace('/\s+/', '', $value); // Remove spaces
					$value = str_replace(',', '.', $value); // Replace commas with dots
					print "Found array value for '$property': $value\n";
				} elseif (preg_match('/\$this->' . preg_quote($property) . '\s*=\s*[\'"]([^\'"]+)[\'"]/', $coreClassContent, $matches)) {
					// Case where the value is a simple string
					$value = trim($matches[1]);
					print "Found string value for '$tag/$property': $value\n";

					if ($tag == 'current_version') {
						$version = $value;
					}
				}

				// Clean version x.y.z into x.y
				if (in_array($tag, array('dolibarrmin', 'dolibarrmax', 'phpmin', 'phpmax'))) {
					if (preg_match('/^(\d+\.\d+)\.[\-\d\*]+$/', $value, $reg)) {
						$value = $reg[1];
					}
					// Clean version x.-y into x.0
					if (preg_match('/^(\d+)\.\-\d+.*$/', $value, $reg)) {
						$value = $reg[1].'.0';
					}
				}

				// If we process tag version and it was not found inside the descriptor file, we try to get it from VERSION file
				if ($tag == 'current_version' && empty($version)) {
					$versionfile = $modulePath . DIRECTORY_SEPARATOR . "VERSION";
					print "Try to guess version from VERSION file ".$versionfile."\n";
					if (file_exists($versionfile)) {
						$version = file_get_contents($versionfile);
						$version = trim($version);
						$value = $version;
						print "Found version into VERSION file: ".$version."\n";
					}
				}

				if (!empty($value)) {
					// Replace "auto" with the found value
					$content = preg_replace('/(' . preg_quote($tag) . ':\s*)["\']?auto["\']?/', "$1\"$value\"", $content);
					print "Replaced auto for '$tag' with value: $value\n";
				} else {
					// Remove "auto" if no value is found
					if ($tag == 'dolibarrmax') {
						$content = preg_replace('/(' . preg_quote($tag) . ':\s*)["\']?auto["\']?/', "$1\"".$DOLIBARRMAXBYDEFAULT."\"", $content);
						print "No value found for '$tag', replaced auto with ".$DOLIBARRMAXBYDEFAULT."\n";
					} else {
						$content = preg_replace('/(' . preg_quote($tag) . ':\s*)["\']?auto["\']?/', "$1\"\"", $content);
						print "No value found for '$tag', replaced auto with empty string.\n";
					}
				}
			} else {
				// Nothing done, we keep value as in source file
			}
		}

		if (!empty($tagsToExtractFromDescriptor['current_version'])) {
			$directoryToSearch = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'); // We are in dev/build, we want to go to root of repository
			$outzip = $directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin/' . DIRECTORY_SEPARATOR . "module_" . strtolower($modulename) . "-" . $version . ".zip";
			print "We check if zip file for module ".$modulename.", with name ".$outzip." exists\n";
			if (file_exists($outzip)) {
				// File already exists, nothing is done.
			} else {
				// File does not exists, we can build it.
			}
		}


		// Now update the created_at

		// Now update the last_updated_at
		$tag = 'last_updated_at';
		if (preg_match('/(' . preg_quote($tag) . ':\s*)["\']?auto["\']?/', $content)) {	// If the key: is 'auto'
			$value = "";
			$urltoget = "";

			// The module sources are in this repository, so we take the date of the last local commit on the module directory
			$output = array();
			$returnCode = 0;
			exec('git -C ' . escapeshellarg($modulePath) . ' log -1 --format=%cs -- . 2>/dev/null', $output, $returnCode);
			if ($returnCode === 0 && !empty($output[0])) {
				$content = preg_replace('/(' . preg_quote($tag) . ':\s*)["\']?auto["\']?/', "$1\"".trim($output[0])."\"", $content);
				print "Replaced auto for '".$tag."' with value of last local commit: ".trim($output[0])."\n";
			} elseif (empty($gitsystem) || $gitsystem == 'github') {
				$urltoget = preg_replace('/https:\/\/github.com/', 'https://api.github.com/repos', $git);
				$urltoget = preg_replace('/\/tree\/.*$/', '/commits?per_page=1&sha='.$gitbranch, $urltoget);
				$urltoget = preg_replace('/\.git$/', '/commits?per_page=1&sha='.$gitbranch, $urltoget);
			} elseif ($gitsystem == 'gitlab') {
				$urltoget = preg_replace('/\.git$/', '/-/commits/'.$gitbranch.'?format=atom', $git);
				//$urltoget = ' https://inligit.fr/cap-rel/dolibarr/plugin-peppol/-/raw/master/core/modules/modPeppol.class.php?inline=false https://gitlab.com/api/v4/projects/cap-rel/repository/commits?per_page=1&ref_name=$branch";
				// Example: 'https://mydomain.com/account/project/repo/-/blob/master/core/modules/modFacturx.class.php?ref_type=heads'
			}

			$commitContent = '';
			if ($urltoget) {
				print "Try to get remote commit list from ".$urltoget." (url guessed from ".$git.")\n";

				$options = [
					"http" => [
						"header" => "User-Agent: Update-Repo script\r\n\r\n"
						]
					];
				$context = stream_context_create($options);

				$commitContent = file_get_contents($urltoget, false, $context);
				if (empty($commitContent)) {
					print "Failed to get remote commit list.\n";
					return -1;
				} else {
					print "Success in getting remote commit list.\n";

					if (empty($gitsystem) || $gitsystem == 'github') {
						$commitContentarray = json_decode($commitContent);
						$datestring = $commitContentarray[0]->commit->committer->date;
					} elseif ($gitsystem == 'gitlab') {
						$xml = simplexml_load_string($commitContent);
						if ($xml) {
							$datestring = $xml->entry[0]->updated;
						}
					}
					$datestring = preg_replace('/T.*$/', '', $datestring);
					print "Replaced auto for '".$tag."' with value: ".$datestring."\n";

					// Replace "auto" with the found value
					$content = preg_replace('/(' . preg_quote($tag) . ':\s*)["\']?auto["\']?/', "$1\"".$datestring."\"", $content);
				}
			}
		}
	} else {
		print "Core class file does not exist: $coreClassFile\n";
	}

	return $content;
}

/**
 * Check that the module ChangeLog.md has an entry for the version being released.
 *
 * When the entry is missing, it is added with the titles of all pull requests merged
 * since the previous version tag (tag format is {project}_{version}). The format of
 * the new entry (with or without a "-- date" suffix) follows the one already used in
 * the file. Returns true when the changelog was modified, false otherwise.
 *
 * @param   string  $mod        Module name (lowercase, from detectModule).
 * @param   string  $version    Version being released.
 * @param   string  $project    Project name (lowercase directory name, used for the tag prefix).
 * @param   string  $repo       Full path to the module directory.
 * @return  bool    True if the changelog was modified, false otherwise.
 */
function checkAndCompleteChangelog($mod, $version, $project, $repo)
{
	$changelogFile = $repo . DIRECTORY_SEPARATOR . 'ChangeLog.md';
	if (!file_exists($changelogFile)) {
		print "No ChangeLog.md found at " . $changelogFile . ", skipping changelog check.\n";
		return false;
	}

	$content = file_get_contents($changelogFile);

	// Check if the changelog already has an entry for this version.
	// Entries look like "## 1.2.0" or "## 2.0.23 -- 2026-09-05".
	if (preg_match('/^##\s+' . preg_quote($version, '/') . '\b/m', $content)) {
		print "ChangeLog.md already has an entry for version " . $version . ".\n";
		return false;
	}

	print "ChangeLog.md does not have an entry for version " . $version . ".\n";

	// Find the previous version tag (tag format is {project}_{version}).
	$tagPrefix = $project . '_';
	$output = array();
	$returnCode = 0;
	$command ='git -C ' . escapeshellarg($repo) . ' tag --list ' . escapeshellarg($tagPrefix . '*') . ' --sort=-version:refname';
	print $command."\n";
	exec($command, $output, $returnCode);
	$previousTag = '';
	if (!empty($output)) {
		$previousTag = $output[0];
		print "Previous version tag found: " . $previousTag . "\n";
	} else {
		print "No previous version tag found, taking all merged pull requests.\n";
	}

	// Get the titles of all pull requests merged since the previous tag.
	// The repository is a monorepo: a merge window can contain pull requests for
	// other modules. Only keep the ones that touch at least one file whose path
	// starts with the module directory name.
	$logRange = $previousTag ? $previousTag . '..HEAD' : '';
	$output = array();
	$returnCode = 0;
	$command = sprintf(
		'git -C %s log %s --merges --grep="Merge pull request" --format="___CHANGELOG_SEP___%%H___TITLE___%%b"',
		escapeshellarg($repo),
		$logRange !== '' ? escapeshellarg($logRange) : ''
	);
	print $command."\n";
	exec($command, $output, $returnCode);

	$prTitles = array();
	$rawBody = implode("\n", $output);
	$entries = explode("___CHANGELOG_SEP___", $rawBody);
	$modulePrefix = MODULES_DIR_NAME . '/' . $project . '/';
	foreach ($entries as $entry) {
		$entry = trim($entry);
		if ($entry === '') {
			continue;
		}

		// Split the SHA and the title body.
		$parts = explode("___TITLE___", $entry, 2);
		$sha = trim($parts[0]);
		$title = trim(isset($parts[1]) ? $parts[1] : '');
		if ($sha === '' || $title === '') {
			continue;
		}

		// Keep only the first line of a multi-line title.
		$firstLine = preg_replace('/\n.*$/s', '', $title);

		// Get the files changed by this merge (diff against first parent).
		$filesOutput = array();
		$filesReturnCode = 0;
		$filesCommand = sprintf(
			'git -C %s diff --name-only %s^1 %s',
			escapeshellarg($repo),
			escapeshellarg($sha),
			escapeshellarg($sha)
		);
		exec($filesCommand, $filesOutput, $filesReturnCode);

		// Keep the PR only if it touches at least one file under the module directory.
		$touchesModule = false;
		foreach ($filesOutput as $file) {
			$file = trim($file);
			if ($file !== '' && strpos($file, $modulePrefix) === 0) {
				$touchesModule = true;
				break;
			}
		}

		if ($touchesModule) {
			// Normalize the prefix: keep only titles starting with a recognized
			// keyword (FIX, NEW, QUAL, PERF, DOC, CLOSE, SEC) followed by ":".
			// Discard entries that do not match.
			$prefixPattern = '/^(FIX|NEW|QUAL|PERF|DOC|CLOSE|SEC)\b\s*:?\s*(.*)$/i';
			if (preg_match($prefixPattern, $firstLine, $prefixMatch)) {
				$rest = ltrim($prefixMatch[2]);
				// Strip any leading colons to avoid "::" sequences.
				$rest = preg_replace('/^:+\s*/', '', $rest);
				$normalized = strtoupper($prefixMatch[1]) . ': ' . $rest;
				$prTitles[] = $normalized;
			} else {
				print "Skip PR (no recognized prefix): " . $firstLine . "\n";
			}
		} else {
			print "Skip PR (no file under " . $modulePrefix . "): " . $firstLine . "\n";
		}
	}

	// Detect whether the changelog uses the "-- date" format.
	$useDate = false;
	if (preg_match('/^##\s+\d+\.\d+[\.\d]*\s+--\s/m', $content)) {
		$useDate = true;
	}

	// Build the new entry.
	$header = '## ' . $version;
	if ($useDate) {
		$header .= ' -- ' . date('Y-m-d');
	}
	$newEntry = $header . "\n\n";
	if (!empty($prTitles)) {
		foreach ($prTitles as $title) {
			$newEntry .= '- ' . $title . "\n";
		}
	} else {
		$newEntry .= "No merged pull requests found since previous version.\n";
	}
	$newEntry .= "\n";

	// Insert the new entry after the header line(s) and before the first existing "## " entry.
	$newContent = '';
	$match = array();
	if (preg_match('/^(.*?)(?=^##\s)/ms', $content, $match, PREG_OFFSET_CAPTURE)) {
		$insertPos = $match[0][1] + strlen($match[0][0]);
		$newContent = substr($content, 0, $insertPos) . $newEntry . substr($content, $insertPos);
	} else {
		// No existing "## " entry found, append after a blank line.
		$newContent = rtrim($content) . "\n\n" . $newEntry;
	}

	file_put_contents($changelogFile, $newContent);
	print "Added new entry to ChangeLog.md for version " . $version . " with " . count($prTitles) . " pull request title(s).\n";
	return true;
}

/**
 * build modules zip file if module sources are available into the repository.
 *
 * @param	string	$action			Action code
 * @param	string 	$modulename		Force build of one given module.
 * @return 	void
 */
function buildModulePackages($action, $modulename)
{
	// list of files & dirs to include into the zip
	$listOfModuleContent = [
		'admin',
		'ajax',
		'backport',
		'class',
		'compat',
		'css',
		'core',
		'doctemplates',
		'icons',
		'images',
		'img',
		'includes',
		'js',
		'langs',
		'lib',
		'public',
		'scripts',
		'sql',
		'theme',
		'tools',
		'tpl',
		'user',
		'vendor',
		'*.md',
		'*.json',
		'*.php',
		'*.yaml',
		'COPYING',
		'COPYRIGHT',
		'VERSION',
		'modulebuilder.txt',
	];

	// Get path of module dir
	$directoryToSearch = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'); // We are in dev/build, we want to go to root of repository
	$modulesDir = $directoryToSearch . DIRECTORY_SEPARATOR . MODULES_DIR_NAME;
	$outputFile = $directoryToSearch . DIRECTORY_SEPARATOR . 'index.yaml';
	$yamlFiles = findIndexYamlFiles($modulesDir, 2);
	$yamlFiles = array_filter($yamlFiles, function ($file) use ($outputFile) {
		return $file != $outputFile; // Exclude the output file from the list of files to combine
	});

	// Get module lists from yaml files
	$projects = [];
	if ($modulename) {
		$projects[] = strtolower($modulename);
	} else {
		foreach ($yamlFiles as $yamlFile) {
			// Parse YAML file to get the name of the module
			$content = file_get_contents($yamlFile);
			$reg = array();
			if (preg_match('/modulename:\s*[\'"]([^\'"]+)[\'"]/', $content, $reg)) {
				$projects[] = strtolower($reg[1]);
			} else {
				print "Can't extract module name from yaml file: ".$yamlFile."\n";
				continue;
			}
		}
	}

	print "\n";

	$lastmessagetoshow = "";

	// For each module, we generate the zip file
	foreach ($projects as $project) {
		print "*** Build package for project: ".$project." in directory ".$modulesDir . DIRECTORY_SEPARATOR . $project."\n";

		// Change current dir to module dir, we will execute all next operations from this dir
		if (!is_dir($modulesDir . DIRECTORY_SEPARATOR . $project) || !chdir($modulesDir . DIRECTORY_SEPARATOR . $project)) {
			print "[fail] Directory ".$modulesDir . DIRECTORY_SEPARATOR . $project." not found, skipped.\n";
			print "\n";
			continue;
		}

		list($mod, $version) = detectModule();
		if ($mod == "" || $version == "") {
			print "[fail] This repository does not contain a valid module sources, skipped.\n";
			print "\n";
			continue;
			// TODO : Try to retrieve zip from Dolistore or make a git clone and then generate the build from sources.
		}

		// When creating a tag, verify the changelog has an entry for the version being released.
		// If the entry is missing, it is added from the merged PR titles and the script stops so
		// the change can be committed before relaunching.
		if ($action == 'makeziptag') {
			$repo = $modulesDir . DIRECTORY_SEPARATOR . $project;
			if (checkAndCompleteChangelog($mod, $version, $project, $repo)) {
				print "\n";
				print "The ChangeLog.md was updated with a new entry for version " . $version . ".\n";
				print "PLEASE REVIEW THE CHANGES AND VERSION, COMMIT THEM, AND RELAUCH THE SCRIPT...\n";
				print "\n";
				exit(1);
			}
		}

		//  Define the name of the output zip file and remove it if already exists
		if (!is_dir($directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin')) {
			mkdirAndCheck($directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin');
		}
		$outzip = $directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin/' . "module_" . $mod . "-" . $version . ".zip";
		$outzipothers = $directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin/' . DIRECTORY_SEPARATOR . "module_" . $mod . "-*.zip";
		if (file_exists($outzip)) {
			print "A zip file already exists with this name/version: $outzip\n";

			// Test if a tag exists with version $version. If yes, we cancel this
			$tag = $project.'_'.$version;

			$output = '';
			$returnCode = 0;
			$command = sprintf('git rev-parse -q --verify %s^{tag} 2>/dev/null', escapeshellarg($tag));
			print $command."\n";
			exec(
				$command,
				$output,
				$returnCode
			);
			$exists = ($returnCode === 0);
			if ($exists) {
				print "The tag ".$tag." exists, so we cancel action on this module. Change first the version if you want to regenerate the zip/tag of module.\n";
				continue;
			} else {
				print "The tag ".$tag." does not exists and action=$action.\n";
				if ($action == 'makeziptag') {
					$output = '';
					$returnCode = 0;
					$repo = $modulesDir . DIRECTORY_SEPARATOR . $project;
					$command = sprintf('git -C %s tag %s 2>&1', escapeshellarg($repo), escapeshellarg($tag));
					$command .= "; git tag push origin ".escapeshellarg($tag);
					$lastmessagetoshow .= "YOU MUST COMMIT ALL FILES (INCLUDING THE ZIP) AND CREATE A TAG WITH COMMAND:\n";
					$lastmessagetoshow .= $command."\n";
					/*exec(
						$command,
						$output,
						$returnCode
					);*/
				} else {
					print "No tag creation requested.\n";
				}
			}

			print "Delete all files like $outzipothers\n";
			//secureUnlink($outzipothers);	// We remove the existing zip, may be the new one will be the same.
			//dol_delete_file($outzipothers);
			foreach (glob($outzipothers) as $file) {
				unlink($file);
			}
		} else {
			print "Delete all files like $outzipothers\n";
			//secureUnlink($outzipothers);	// We remove the existing zip, may be the new one will be the same.
			//dol_delete_file($outzipothers);
			foreach (glob($outzipothers) as $file) {
				unlink($file);
			}
		}

		//copy all sources into system temp directory
		$tmpdir = tempnam(sys_get_temp_dir(), $mod . "-module");
		secureUnlink($tmpdir);
		mkdirAndCheck($tmpdir);
		$dst = $tmpdir . "/" . $mod;
		mkdirAndCheck($dst);

		foreach ($listOfModuleContent as $moduleContent) {
			foreach (glob($moduleContent) as $entry) {
				if (!rcopy($entry, $dst . '/' . $entry)) {
					print "[fail]  Error on copy " . $entry . " to " . $dst . "/" . $entry . " for project: ".$project."\n";
					print "Please take time to analyze the problem and fix the bug\n";
					print "\n";
					continue 3; // Skip to the next project if copy fails.
				}
			}
		}

		// TODO dir to exclude to store somewhere
		print "Exclude some directories not useful.\n";
		$dirsToExclude = array(
			'einvoicing/vendor/horstoeko/zugferd/tests',
			'einvoicing/vendor/horstoeko/zugferd/examples'
		);
		foreach ($dirsToExclude as $dirToExclude) {
			if (is_dir($tmpdir . '/' . $dirToExclude)) {
				print "Delete dir $tmpdir/$dirToExclude\n";
				delTree($tmpdir . '/' . $dirToExclude);
			}
		}


		// Stamp the package with the commit it was built from. A zip carries no repository, so
		// this is the only moment where that commit can be known; the einvoicing module reads
		// the file back to name its sources in the XML it generates (issue #686). The commit is
		// kept out of VERSION on purpose: that file is compared with version_compare() by the
		// core and names both the zip and the release tag here.
		$commitoutput = array();
		$returnCode = 0;
		exec('git rev-parse --short HEAD 2>/dev/null', $commitoutput, $returnCode);
		if ($returnCode === 0 && !empty($commitoutput[0])) {
			$commit = trim($commitoutput[0]);
			print "Stamp package with commit ".$commit."\n";
			file_put_contents($dst . '/COMMIT', $commit."\n");
		} else {
			print "Could not read the current commit, the package is not stamped\n";
		}


		$z = new ZipArchive();
		$z->open($outzip, ZIPARCHIVE::CREATE);
		zipDir($tmpdir, $z, $tmpdir . '/');
		$z->close();
		delTree($tmpdir);
		if (file_exists($outzip)) {
			print "[success] module archive is ready : $outzip ...\n";
			print "\n";
		} else {
			print "[fail] build zip error\n";
			continue;
			print "\n";
		}
	}

	print "\n";
	print $lastmessagetoshow."\n";
}

/**
 * auto detect module name and version from file name
 *
 * @return  (string|string)[] module name and module version
 */
function detectModule()
{
	$name  = $version = "";
	$tab = glob("core/modules/mod*.class.php");
	if (count($tab) == 0) {
		print "[fail] Error on auto detect data : there is no mod*.class.php file into core/modules dir\n";
		return ["", ""];
	}

	$matches = array();
	if (count($tab) == 1) {
		$file = $tab[0];
		$pattern = "/.*mod(?<mod>.*)\.class\.php/";
		if (preg_match_all($pattern, $file, $matches)) {
			$name = strtolower(reset($matches['mod']));
		}

		print "extract data from ".$file." in ".getcwd()."\n";
		if (!file_exists($file) || $name == "") {
			print "[fail] Error on auto detect data\n";
			return ["", ""];
		}
	} else {
		print "[fail] Error there is more than one mod*.class.php file into core/modules dir\n";
		return ["", ""];
	}

	//extract version from file
	$contents = file_get_contents($file);
	$pattern = "/^.*this->version\s*=\s*'(?<version>.*)'\s*;.*\$/m";

	// search, and store all matching occurrences in $matches
	if (preg_match_all($pattern, $contents, $matches)) {
		$version = reset($matches['version']);
	} elseif (file_exists('VERSION')) {
		// The descriptor may read its version from the VERSION file of the module instead of hardcoding it
		$version = trim((string) file_get_contents('VERSION'));
	}

	if (empty($version)) {
		// Try from VERSION file
		print "search version in VERSION file\n";
		$fileversion = getcwd().'/VERSION';
		$version = trim(file_get_contents($fileversion));
	}

	if (version_compare($version, '0.0.1', '>=') != 1) {
		print "[fail] Error auto extract version fail\n";
		return ["", ""];
	}

	print "module name = $name, version = $version\n";
	return [(string) $name, (string) $version];
}

/**
 * delete recursively a directory
 *
 * @param   string  $dir  dir path to delete
 *
 * @return bool true on success or false on failure.
 */
function delTree($dir)
{
	$files = array_diff(scandir($dir), array('.', '..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : secureUnlink("$dir/$file");
	}
	return rmdir($dir);
}


/**
 * do a secure delete file/dir with double check
 * (don't trust unlink return)
 *
 * @param   string  $path  full path to delete
 *
 * @return bool true on success ($path does not exists at the end of process), else exit
 */
function secureUnlink($path)
{
	if (file_exists($path)) {
		if (unlink($path)) {
			//then check if really deleted
			clearstatcache();
			if (file_exists($path)) {	// @phpstan-ignore-line
				print "[fail] unlink of $path fail !\n";
				exit(2);
			}
		} else {
			print "[fail] unlink of $path fail !\n";
			exit(2);
		}
	}
	return true;
}

/**
 * create a directory and check if dir exists
 *
 * @param   string  $path  path to make
 *
 * @return bool true on success ($path exists at the end of process), else exit
 */
function mkdirAndCheck($path)
{
	if (mkdir($path)) {
		clearstatcache();
		if (is_dir($path)) {
			return true;
		}
	}
	print "[fail] Error on $path (mkdir)\n";
	exit(3);
}

/**
 * check if that filename is concerned by exclude filter
 *
 * @param   string  $filename  file name to check
 *
 * @return bool true if file is in excluded list
 */
function is_excluded($filename)
{
	/**
	 * if you want to exclude some files from your zip
	 */
	$exclude_list = [
		'/^.git$/',
		'/.*js.map/',
		'/DEV.md/'
	];

	$count = 0;
	$notused = preg_filter($exclude_list, '1', $filename, -1, $count);
	if ($count > 0) {
		print " - exclude $filename\n";
		return true;
	}
	return false;
}

/**
 * recursive copy files & dirs
 *
 * @param   string  $src  source dir
 * @param   string  $dst  target dir
 *
 * @return bool true on success or false on failure.
 */
function rcopy($src, $dst)
{
	if (is_dir($src)) {
		// Make the destination directory if not exist
		mkdirAndCheck($dst);
		// open the source directory
		$dir = opendir($src);

		// Loop through the files in source directory
		while ($file = readdir($dir)) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					// Recursively calling custom copy function
					// for sub directory
					if (!rcopy($src . '/' . $file, $dst . '/' . $file)) {
						return false;
					}
				} else {
					if (!is_excluded($file)) {
						if (!copy($src . '/' . $file, $dst . '/' . $file)) {
							return false;
						}
					}
				}
			}
		}
		closedir($dir);
	} elseif (is_file($src)) {
		if (!is_excluded($src)) {
			if (!copy($src, $dst)) {
				return false;
			}
		}
	}
	return true;
}

/**
 * build a zip file from a folder
 *
 * @param   string  	$folder  folder to use as zip root
 * @param   ZipArchive  $zip     zip object (ZipArchive)
 * @param   string  	$root    relative root path into the zip
 *
 * @return bool true on success or false on failure.
 */
function zipDir($folder, &$zip, $root = "")
{
	foreach (new \DirectoryIterator($folder) as $f) {
		if ($f->isDot()) {
			continue;
		} //skip . ..
		$src = $folder . '/' . $f;
		$dst = substr($f->getPathname(), strlen($root));
		if ($f->isDir()) {
			if ($zip->addEmptyDir($dst)) {
				if (zipDir($src, $zip, $root)) {
					continue;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		if ($f->isFile()) {
			if (! $zip->addFile($src, $dst)) {
				return false;
			}
		}
	}
	return true;
}


/**
 * Format a Dolibarr version for the Dolistore min/max extrafields: "24.0" or "24" gives "V24", "24.1" gives "V24.1".
 *
 * @param   string  $version    Version found in index.yaml.
 * @return  string              Version with syntax Vxx.
 */
function formatDolistoreVersion($version)
{
	$version = preg_replace('/^v/i', '', trim($version));
	$version = preg_replace('/(\.0)+$/', '', $version);
	return 'V' . $version;
}


/**
 * Push a module zip package to the Dolistore (remote Dolibarr instance) using the REST API.
 *
 * The product ID is read from the dolistore-download URL found in the root index.yaml file.
 * The product reference is then fetched from the API so the file can be attached to the right product.
 * After the upload, the product extrafields (module version, Dolibarr/PHP min/max, submission date)
 * are updated from the values found in the index.yaml.
 *
 * @param   string  $apikey             DOLISTORE_API_KEY for the remote Dolibarr API.
 * @param   string  $dolistoreApiUrl     Base URL of the Dolistore REST API.
 * @param   string  $modulename         Module name (lowercase).
 * @param   string  $directoryToSearch   Root directory of the repository.
 * @return  void
 */
function pushModuleToDolistore($apikey, $dolistoreApiUrl, $modulename, $directoryToSearch)
{

	// Read the root index.yaml to extract the module data
	$yamlFile = $directoryToSearch . DIRECTORY_SEPARATOR . 'index.yaml';
	if (!file_exists($yamlFile)) {
		print "[fail] No index.yaml file found at " . $yamlFile . "\n";
		return;
	}

	$content = file_get_contents($yamlFile);

	// Extract the block for this module (modulename in yaml may use different case than $modulename)
	$moduleBlock = '';
	$pattern = '/^  - modulename:\s*[\'"]' . preg_quote($modulename, '/') . '[\'"]/im';
	if (preg_match($pattern, $content, $reg, PREG_OFFSET_CAPTURE)) {
		$start = $reg[0][1];
		$rest = substr($content, $start + strlen($reg[0][0]));
		if (preg_match('/^  - modulename:/m', $rest, $reg2, PREG_OFFSET_CAPTURE)) {
			$moduleBlock = substr($content, $start, strlen($reg[0][0]) + $reg2[0][1]);
		} else {
			$moduleBlock = substr($content, $start);
		}
	}
	if (empty($moduleBlock)) {
		print "[fail] Can't find module " . $modulename . " in root index.yaml\n";
		return;
	}

	// Extract the Dolistore product ID and public URL from the dolistore-download URL
	$reg = array();
	if (!preg_match('/dolistore-download:\s*[\'"]?(https:\/\/www\.dolistore\.com\/product\.php\?id=(\d+))/', $moduleBlock, $reg)) {
		print "[fail] Can't extract Dolistore product ID from index.yaml for module " . $modulename . "\n";
		return;
	}
	$dolistoreDownloadUrl = $reg[1];
	$productId = $reg[2];
	print "Found Dolistore product ID: " . $productId . " for module " . $modulename . "\n";
	print "Dolistore download URL: " . $dolistoreDownloadUrl . "\n";

	// Extract module metadata from the index.yaml block
	$moduleVersion = '';
	$dolibarrMin = '';
	$dolibarrMax = '';
	$phpMin = '';
	$phpMax = '';
	$lastUpdatedAt = '';

	if (preg_match('/current_version:\s*["\']?([^"\'\n]+)["\']?/', $moduleBlock, $reg)) {
		$moduleVersion = trim($reg[1]);
	}
	if (preg_match('/dolibarrmin:\s*["\']?([^"\'\n]+)["\']?/', $moduleBlock, $reg)) {
		$dolibarrMin = trim($reg[1]);
	}
	if (preg_match('/dolibarrmax:\s*["\']?([^"\'\n]+)["\']?/', $moduleBlock, $reg)) {
		$dolibarrMax = trim($reg[1]);
	}
	if (preg_match('/phpmin:\s*["\']?([^"\'\n]+)["\']?/', $moduleBlock, $reg)) {
		$phpMin = trim($reg[1]);
	}
	if (preg_match('/phpmax:\s*["\']?([^"\'\n]+)["\']?/', $moduleBlock, $reg)) {
		$phpMax = trim($reg[1]);
	}
	if (preg_match('/last_updated_at:\s*["\']?([^"\'\n]+)["\']?/', $moduleBlock, $reg)) {
		$lastUpdatedAt = trim($reg[1]);
	}

	$newdate = time();

	print "Module version: " . $moduleVersion . "\n";
	print "Dolibarr min/max: " . $dolibarrMin . " / " . $dolibarrMax . "\n";
	print "PHP min/max: " . $phpMin . " / " . $phpMax . "\n";
	print "Last updated at: " . $lastUpdatedAt . " -> " . date('Y-m-d H:i:s', $newdate) . "\n";
	$lastUpdatedAt = date('Y-m-d H:i:s', $newdate);

	// Find the zip file for the module in dev/build/bin/
	$zipPattern = $directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin/' . "module_" . $modulename . "-*.zip";
	$zipFiles = glob($zipPattern);
	if (empty($zipFiles)) {
		print "[fail] No zip file found for module " . $modulename . " in dev/build/bin/ (pattern: " . $zipPattern . ")\n";
		return;
	}
	// Use the first (and normally only) zip file found
	$zipFile = $zipFiles[0];
	print "Using zip file: " . $zipFile . "\n";

	if (!extension_loaded('curl')) {
		print "[fail] The cURL extension is not loaded. Please install php-curl.\n";
		return;
	}

	$urltouse =$dolistoreApiUrl . "/products/" . $productId;
	print "URL to use: " . $urltouse . "\n";

	// Step 1: Fetch the product by ID to get its reference (the documents API uses ref, not id, for products)
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urltouse);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'DOLAPIKEY: ' . $apikey,
		'Accept: application/json'
	));

	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curlError = curl_error($ch);
	curl_close($ch);

	if ($curlError) {
		print "[fail] cURL error while fetching product: " . $curlError . "\n";
		return;
	}
	if ($httpCode != 200) {
		print "[fail] Failed to fetch product with ID " . $productId . " (HTTP " . $httpCode . "): " . $response . "\n";
		return;
	}

	$product = json_decode($response, true);
	if (empty($product) || empty($product['ref'])) {
		print "[fail] Product found but no ref returned for product ID " . $productId . "\n";
		return;
	}
	$productRef = $product['ref'];
	$productUrl = isset($product['url']) ? trim($product['url']) : '';
	print "Product reference: " . $productRef . "\n";
	print "Product current public URL: " . (empty($productUrl) ? '(empty)' : $productUrl) . "\n";

	// Step 2: Upload the zip file to the product via the documents/upload API
	$fileContent = file_get_contents($zipFile);
	if ($fileContent === false) {
		print "[fail] Failed to read zip file: " . $zipFile . "\n";
		return;
	}
	$base64Content = base64_encode($fileContent);
	$filename = basename($zipFile);

	$postData = array(
		'filename' => $filename,
		'modulepart' => 'product',
		'ref' => $productRef,
		'filecontent' => $base64Content,
		'fileencoding' => 'base64',
		'overwriteifexists' => 1,
		'createdirifnotexists' => 1,
		'share' =>1
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $dolistoreApiUrl . "/documents/upload");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'DOLAPIKEY: ' . $apikey,
		'Content-Type: application/json',
		'Accept: application/json'
	));

	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curlError = curl_error($ch);
	curl_close($ch);

	if ($curlError) {
		print "[fail] cURL error while uploading file: " . $curlError . "\n";
		return;
	}
	if ($httpCode != 200 && $httpCode != 201) {
		print "[fail] Failed to upload file to product (HTTP " . $httpCode . "): " . $response . "\n";
		return;
	}

	print "[success] File " . $filename . " uploaded to Dolistore product ID " . $productId . " (ref: " . $productRef . ")\n";

	// Step 3: Update the product extrafields with module metadata from index.yaml
	$arrayOptions = array();
	if (!empty($moduleVersion)) {
		$arrayOptions['options_marketplace_module_version'] = $moduleVersion;
	}
	if (!empty($dolibarrMin)) {
		$arrayOptions['options_marketplace_min_version'] = formatDolistoreVersion($dolibarrMin);
	}
	if (!empty($dolibarrMax)) {
		$arrayOptions['options_marketplace_max_version'] = formatDolistoreVersion($dolibarrMax);
	}
	if (!empty($phpMin)) {
		$arrayOptions['options_marketplace_php_min_version'] = $phpMin;
	}
	if (!empty($phpMax)) {
		$arrayOptions['options_marketplace_php_max_version'] = $phpMax;
	}
	if (!empty($lastUpdatedAt)) {
		$arrayOptions['options_marketplace_submitted'] = $lastUpdatedAt;
	}

	// Build the PUT data: extrafields + public URL if it is empty on the product
	$putData = array();
	if (!empty($arrayOptions)) {
		$putData['array_options'] = $arrayOptions;
	}
	if (empty($productUrl) && !empty($dolistoreDownloadUrl)) {
		$putData['url'] = $dolistoreDownloadUrl;
		print "Public URL is empty, will set it to: " . $dolistoreDownloadUrl . "\n";
	}

	if (!empty($putData)) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $dolistoreApiUrl . "/products/" . $productId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($putData));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'DOLAPIKEY: ' . $apikey,
			'Content-Type: application/json',
			'Accept: application/json'
		));

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);

		if ($curlError) {
			print "[fail] cURL error while updating product extrafields: " . $curlError . "\n";
			return;
		}
		if ($httpCode != 200 && $httpCode != 201) {
			print "[fail] Failed to update product extrafields (HTTP " . $httpCode . "): " . $response . "\n";
			return;
		}

		print "[success] Product updated for Dolistore product ID " . $productId . " - " . $productRef . " - " . $filename . " - " . $lastUpdatedAt . "\n";
	} else {
		print "No metadata to update in product.\n";
	}
}


// Main
$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path = dirname(__FILE__).'/';

print "----- ".$script_file." -----\n";

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
	print "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
	exit(1);
}

// Test if zip extension is loaded
if (!extension_loaded('zip')) {
	print "Error: PHP extension 'zip' is not loaded. To execute ".$script_file." you must have this extension loaded.\n";
	exit(1);
}

if (empty($argv[1])) {
	print "Usage:   ".$script_file." index|makezip|makeziptag|pushdolistore\n";
	print "Example: ".$script_file." index                           to rebuild the index.yaml file (used by Dolibarr to retrieve list of community modules)\n";
	print "Example: ".$script_file." makezip|makeziptag [modulename] to regenerate zip of packages (and set Tag of version)\n";
	print "Example: ".$script_file." pushdolistore      [modulename] to publish zip of packages on dolistore (ask for API key)\n";
	print "\n";
	exit(1);
}


if ($argv[1] == 'index') {
	$directoryToSearch = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
	$outputFile = $directoryToSearch . DIRECTORY_SEPARATOR . 'index.yaml';

	$yamlFiles = findIndexYamlFiles($directoryToSearch . DIRECTORY_SEPARATOR . MODULES_DIR_NAME, 2);

	// Exclude the output file from the list of files to combine
	$yamlFiles = array_filter($yamlFiles, function ($file) use ($outputFile) {
		return $file != $outputFile;
	});

	print "Found ".count($yamlFiles)." yaml files to merge into the main index.yaml file.\n";

	combineYamlFiles($yamlFiles, $outputFile);

	// Remove last CR+LF lines
	$content = file_get_contents($outputFile);
	$content = preg_replace('/\n+$/', "\n", $content);
	file_put_contents($outputFile, $content);

	print "\n";
	print "The combined index.yaml file was created at: " . $outputFile;
	syslog(LOG_INFO, "The combined index.yaml file was created at: " . $outputFile);
	print "\n";
}

if ($argv[1] == 'pushdolistore') {
	$directoryToSearch = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');

	// Get the API key from environment variable DOLISTORE_API_KEY, or ask the user if not set
	$apikey = getenv('DOLISTORE_API_KEY');
	if (empty($apikey)) {
		print "Enter your Dolistore API key (DOLISTORE_API_KEY): ";
		$apikey = trim(fgets(STDIN));
		if (empty($apikey)) {
			print "Error: No API key provided.\n";
			exit(1);
		}
	} else {
		print "API key found in environment variable DOLISTORE_API_KEY: ".substr($apikey, 0, 4)."*****...\n";
	}

	// Get the Dolistore API URL from environment variable DOLISTORE_API_URL, or ask the user if not set
	$dolistoreApiUrl = getenv('DOLISTORE_API_URL');
	if (empty($dolistoreApiUrl)) {
		print "Enter the Dolistore API URL (leave empty for default https://www.dolistore.com/api/index.php): ";
		$dolistoreApiUrl = trim(fgets(STDIN));
		if (empty($dolistoreApiUrl)) {
			$dolistoreApiUrl = 'https://www.dolistore.com/api/index.php';
		}
	} else {
		print "Dolistore API URL found in environment variable DOLISTORE_API_URL: ".$dolistoreApiUrl."\n";
	}

	// Build the list of modules to push
	if (empty($argv[2])) {
		// Scan the directory dev/build/bin to get all module zip files
		$moduletopush = array();
		$zipFiles = glob($directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin/' . "module_*.zip");
		foreach ($zipFiles as $zipFile) {
			$reg = array();
			if (preg_match('/module_([a-z]+)-[\d.]+\.zip$/', basename($zipFile), $reg)) {
				$moduletopush[] = $reg[1];
			}
		}
		if (empty($moduletopush)) {
			print "No zip file found in dev/build/bin/. Run makezip first.\n";
			exit(1);
		}
		print "Found " . count($moduletopush) . " module(s) to push: " . implode(', ', $moduletopush) . "\n";
	} else {
		$moduletopush = array(strtolower($argv[2]));
		print "Module to push: " . strtolower($argv[2]);
		//print filesize($directoryToSearch . DIRECTORY_SEPARATOR . 'dev/build/bin/' . "module_" . strtolower($argv[2]) . ".zip")."ko";
		print "\n";
	}

	print "\n";
	print "Please type enter to confirm to push zip file on Dolistore (CTR+C to cancel): ";
	$confirmkey = trim(fgets(STDIN));
	if (!empty($confirmkey)) {
		print "Operation canceled.\n";
		exit(1);
	}

	// Push each module to the Dolistore
	foreach ($moduletopush as $modulename) {
		print "\n*** Push module " . $modulename . " to Dolistore ***\n";
		pushModuleToDolistore($apikey, $dolistoreApiUrl, $modulename, $directoryToSearch);
	}

	print "\nAll done.\n";
}

if ($argv[1] == 'makezip' || $argv[1] == 'makeziptag') {
	// For each module, we generate the zip file
	buildModulePackages($argv[1], $argv[2] ?? '');
	print "All done.\n";
}

print "\n";
