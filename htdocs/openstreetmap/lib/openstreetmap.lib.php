<?php
/* Copyright (C) 2010-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file			htdocs/openstreetmap/lib/openstreetmap.lib.php
 *  \brief			Library of admin functions for openstreetmap module
 */


/**
 *  Define head array for tabs of openstreetmap tools setup pages
 *
 *  @return			Array of head
 */
function openstreetmapadmin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	/*  $head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap.php",1);
	$head[$h][1] = $langs->trans("AgendaView");
	$head[$h][2] = 'agenda';
	$h++;

	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_calsync.php",1);
	$head[$h][1] = $langs->trans("AgendaSync");
	$head[$h][2] = 'agendasync';
	$h++;
	*/
	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_maps.php", 1);
	$head[$h][1] = $langs->trans("Maps");
	$head[$h][2] = 'maps';
	$h++;
	/*
	$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_ad.php",1);
	$head[$h][1] = $langs->trans("Adsense");
	$head[$h][2] = 'adsense';
	$h++;

	include_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
	$dolibarrversionarray=preg_split('/[\.-]/',version_dolibarr());
	$dolibarrversionok=array(3,1,-2);
	if (versioncompare($dolibarrversionarray,$dolibarrversionok) >= 0)
	{
		$head[$h][0] = dol_buildpath("/openstreetmap/admin/openstreetmap_an.php",1);
		$head[$h][1] = $langs->trans("Analytics");
		$head[$h][2] = 'analytics';
		$h++;
	}
	*/

	$head[$h][0] = 'about.php';
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'tababout';
	$h++;

	return $head;
}

/**
 *  Add a source of images into a Content Security Policy, without changing the other rules.
 *  The source is added to img-src. When there is no img-src, images use default-src: an img-src is then
 *  created with the sources of default-src that apply to images, plus the new source.
 *
 *  @param	string	$csp		Content Security Policy (example: "default-src 'self'; frame-ancestors 'self'")
 *  @param	string	$source		Source to allow (example: '*.openstreetmap.org')
 *  @return	string				Content Security Policy with the source allowed for images
 */
function openstreetmap_csp_add_img_source($csp, $source)
{
	$directives = array();
	foreach (explode(';', (string) $csp) as $directive) {
		$tokens = preg_split('/\s+/', trim($directive), -1, PREG_SPLIT_NO_EMPTY);
		if (!empty($tokens)) {
			$directives[strtolower(array_shift($tokens))] = $tokens;
		}
	}

	// No img-src and no default-src: images are not restricted
	if (isset($directives['img-src'])) {
		$name = 'img-src';
		$sources = $directives['img-src'];
	} elseif (isset($directives['default-src'])) {
		$name = 'img-src';
		// Keywords of default-src that are only for scripts and styles are not copied
		$sources = preg_grep("/^'(unsafe-inline|unsafe-eval|unsafe-hashes|wasm-unsafe-eval|strict-dynamic|report-sample|nonce-.*|sha(256|384|512)-.*)'$/i", $directives['default-src'], PREG_GREP_INVERT);
	} else {
		return $csp;
	}
	if (in_array('*', $sources) || in_array($source, $sources)) {
		return $csp;
	}

	// 'none' must be the only source of a directive
	$sources = array_diff($sources, array("'none'"));
	$sources[] = $source;
	$directives[$name] = array_values($sources);

	$result = array();
	foreach ($directives as $directivename => $tokens) {
		$result[] = trim($directivename.' '.implode(' ', $tokens));
	}

	return implode('; ', $result).';';
}
