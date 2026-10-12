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
 * \file    dev/tools/phpstan/bootstrap.php
 * \brief   Gives PHPStan the few Dolibarr constants a module resolves its includes against.
 * \remarks Read DOLIBARR_HTDOCS, the same variable the CI actions and the other tools of this
 *          repository use. This file deliberately does NOT load master.inc.php or main.inc.php:
 *          those open a database connection, start a session and read a conf.php that does not
 *          exist here, and PHPStan needs none of it. The classes and functions of the core reach
 *          the analysis through scanDirectories in phpstan.neon.dist, which indexes the symbols
 *          without running anything.
 */

$htdocs = getenv('DOLIBARR_HTDOCS');
if (!is_string($htdocs) || $htdocs === '' || !is_file($htdocs . '/filefunc.inc.php')) {
	fwrite(STDERR, "DOLIBARR_HTDOCS does not point at the htdocs directory of a Dolibarr checkout (got \"" . (string) $htdocs . "\").\n");
	fwrite(STDERR, "Run the analysis through dev/tools/phpstan/phpstan.sh, which resolves it.\n");
	exit(2);
}
$htdocs = rtrim(str_replace('\\', '/', $htdocs), '/');

// The constants every page and class of a module builds its require_once on. Without them PHPStan
// resolves nothing and reports the whole module as unknown symbols.
define('DOL_DOCUMENT_ROOT', $htdocs);
define('DOL_DOCUMENT_ROOT_ALT', $htdocs . '/custom');
define('DOL_DATA_ROOT', dirname($htdocs) . '/documents');
define('DOL_URL_ROOT', '');
define('DOL_MAIN_URL_ROOT', 'http://localhost');
define('MAIN_DB_PREFIX', 'llx_');

// DOL_VERSION is deliberately left undefined, and listed in dynamicConstantNames as well: a module
// that supports several cores guards its calls with version tests, and pinning the constant to the
// version of the checkout under analysis would let PHPStan declare each of those guards dead code.

// Parent classes a module extends that older cores do not provide yet. The modules using them require a
// newer core (need_dolibarr_version), but the analysis runs every module on every core of the matrix.
if (!is_file($htdocs . '/core/class/commonhookactions.class.php')) {
	eval('abstract class CommonHookActions {}');
}
if (!is_file($htdocs . '/core/class/commonnumrefgenerator.class.php')) {
	eval('abstract class CommonNumRefGenerator {}');
}
if (!is_file($htdocs . '/core/modules/security/captcha/modules_captcha.php')) {
	eval('abstract class ModeleCaptcha {}');
}
