<?php 
/**
 * compress js files and send them to the browser on the fly
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_JS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: js.php,v 1.1 2010/07/19 18:45:44 eldy Exp $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * application root path
 *
 * @var string
 */
define('APP_ROOT', dirname(__FILE__));

require_once APP_ROOT.'/includes/autoloader.inc.php';
require_once APP_ROOT.'/config.php';

$file = isset($_GET['name']) ? basename(htmlspecialchars($_GET['name'])) : null;
$plugin = isset($_GET['plugin']) ? basename(htmlspecialchars($_GET['plugin'])) : null;

if ($file != null && $plugin == null) {
    if (strtolower(substr($file, 0, 6)) == 'jquery') {
        $script = APP_ROOT.'/js/jQuery/'.$file.'.js';
    } else {
        $script = APP_ROOT.'/js/phpSysInfo/'.$file.'.js';
    }
}
if ($file == null && $plugin != null) {
    $script = APP_ROOT.'/plugins/'.$plugin.'/js/'.$plugin.'.js';
}
if ($file != null && $plugin != null) {
    $script = APP_ROOT.'/plugins/'.$plugin.'/js/'.$file.'.js';
}

if ($script != null && file_exists($script) && is_readable($script)) {
    header("content-type: application/x-javascript");
    $filecontent = file_get_contents($script);
    if (defined("PSI_DEBUG") && PSI_DEBUG === true) {
        echo $filecontent;
    } else {
        $packer = new JavaScriptPacker($filecontent);
        echo $packer->pack();
    }
}
?>
