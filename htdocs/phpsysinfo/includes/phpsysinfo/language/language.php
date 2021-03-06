<?php
/**
 * language reading
 * read the language wich is passed as a parameter in the url and if
 * it is not available read the default language
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Language
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: language.php 661 2012-08-27 11:26:39Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */

// Set the correct content-type header.
header("Content-Type: text/xml\n\n");

/**
 * default language
 *
 * @var String
 */
$lang = 'en';

/**
 * default pluginname
 *
 * @var String
 */
$plugin = '';

/**
 * application root path
 *
 * @var string
 */
define('APP_ROOT', realpath(dirname((__FILE__)).'/../'));

include_once APP_ROOT.'/read_config.php';

if (defined('PSI_DEFAULT_LANG')) {
	$lang = PSI_DEFAULT_LANG;
}

if (isset($_GET['lang']) && (trim($_GET['lang'])!=="")
   && !preg_match('/[^A-Za-z\-_]/', $_GET['lang'])
   && file_exists(APP_ROOT.'/language/'.$_GET['lang'].'.xml')) {
	$lang = $_GET['lang'];
}

if (isset($_GET['plugin'])) {
	if ((trim($_GET['plugin'])!=="") && !preg_match('/[^A-Za-z\-_]/', $_GET['plugin'])) {
		$plugin = $_GET['plugin'];
		if (file_exists(APP_ROOT.'/plugins/'.strtolower($plugin).'/lang/'.$lang.'.xml')) {
			echo file_get_contents(APP_ROOT.'/plugins/'.strtolower($plugin).'/lang/'.$lang.'.xml');
		} elseif (file_exists(APP_ROOT.'/plugins/'.strtolower($plugin).'/lang/en.xml')) {
			echo file_get_contents(APP_ROOT.'/plugins/'.strtolower($plugin).'/lang/en.xml');
		}
	}
} else {
	if (file_exists(APP_ROOT.'/language/'.$lang.'.xml')) {
		echo file_get_contents(APP_ROOT.'/language/'.$lang.'.xml');
	} else {
		echo file_get_contents(APP_ROOT.'/language/en.xml');
	}
}
