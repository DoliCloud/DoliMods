<?php
/* Copyright (C) 2026 Pierre Grasswill
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    .github/scripts/check-test-file-placement.php
 * \brief   Refuses a new PHPUnit file when the module source it tests already has one.
 * \remarks A test file costs a class, a bootstrap, a header and one more place to look; a method in
 *          an existing file costs a method. The rule is one test file per source file under test,
 *          and what a file tests is read from its own dol_include_once() lines. Only files ADDED by
 *          the pull request are checked, so the existing suite is never a reason to fail.
 *
 *          Usage: php .github/scripts/check-test-file-placement.php <base ref>
 */

if (PHP_SAPI !== 'cli') {
	echo "Error: this script must be run from the command line.\n";
	exit(1);
}

$base = (string) ($argv[1] ?? '');
if ($base === '') {
	fwrite(STDERR, "usage: check-test-file-placement.php <base ref>\n");
	exit(2);
}

/**
 * Sources of a module a test file loads, which is what it is a test of.
 *
 * @param	string	$content	Content of a test file
 * @return	string[]			Sorted module source paths, without duplicates
 */
function tested_sources($content)
{
	preg_match_all("/dol_include_once\('([^']+)'\)/", (string) $content, $matches);
	$sources = array_values(array_unique($matches[1]));
	sort($sources);

	return $sources;
}

/**
 * Content of a file as the base branch holds it.
 *
 * @param	string	$base	Base ref
 * @param	string	$path	Path of the file
 * @return	string			Content, empty when the ref does not carry it
 */
function content_at_base($base, $path)
{
	$out = array();
	$rc = 0;
	exec('git show ' . escapeshellarg($base . ':' . $path) . ' 2>/dev/null', $out, $rc);

	return ($rc === 0) ? implode("\n", $out) : '';
}

exec('git diff --name-only --diff-filter=A ' . escapeshellarg($base) . '...HEAD 2>&1', $added, $rc);
if ($rc !== 0) {
	// No usable history (a shallow clone, a branch that left no merge base): say so and let the
	// pull request through rather than failing on the checkout.
	echo "::notice::test file placement not checked, no diff against " . $base . "\n";
	exit(0);
}

// A pull request that regroups files adds one and removes several: what it removes tested the same
// sources, so such a file is a consolidation and not one more place to look.
$removed = array();
exec('git diff --name-only --diff-filter=D ' . escapeshellarg($base) . '...HEAD 2>/dev/null', $deleted);
foreach ($deleted as $file) {
	$file = trim($file);
	if (preg_match('#^htdocs/[^/]+/test/phpunit/\w+Test\.php$#', $file)) {
		$removed[] = tested_sources(content_at_base($base, $file));
	}
}

$errors = array();
foreach ($added as $file) {
	$file = trim($file);
	if (!preg_match('#^(htdocs/[^/]+)/test/phpunit/\w+Test\.php$#', $file, $m)) {
		continue;
	}
	$module = $m[1];
	$sources = tested_sources((string) @file_get_contents($file));
	if (!$sources || in_array($sources, $removed, true)) {
		continue;
	}

	foreach ((array) glob($module . '/test/phpunit/*Test.php') as $existing) {
		if (in_array($existing, array_map('trim', $added), true)) {
			continue;
		}
		if (tested_sources((string) @file_get_contents($existing)) === $sources) {
			$errors[] = $file . ' tests ' . implode(', ', $sources) . ', and so does ' . $existing
				. ': add a test method to ' . basename($existing) . ' instead of a new file.';
			break;
		}
	}
}

if ($errors) {
	foreach ($errors as $error) {
		echo '::error::' . $error . "\n";
	}
	echo "\nOne test file per source file under test: a new case belongs in the file of the class or\n";
	echo "library file it exercises. Create a file only when that source has no test file yet.\n";
	exit(1);
}

echo "Test file placement: nothing to say.\n";
