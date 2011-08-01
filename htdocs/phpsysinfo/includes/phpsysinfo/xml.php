<?php 
/**
 * generate the xml
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: xml.php,v 1.1 2011/08/01 19:28:42 eldy Exp $
 * @link      http://phpsysinfo.sourceforge.net
 */
 
 /**
 * application root path
 *
 * @var string
 */
define('APP_ROOT', dirname(__FILE__));

/**
 * internal xml or external
 * external is needed when running in static mode
 *
 * @var boolean
 */
define('PSI_INTERNAL_XML', true);

require_once APP_ROOT.'/includes/autoloader.inc.php';

// check what xml part should be generated
if (isset($_GET['plugin'])) {
    $plugin = basename(htmlspecialchars($_GET['plugin']));
    if ($plugin == "complete") {
        $output = new WebpageXML(true, null);
        $output->run();
    } elseif ($plugin != "") {
        $output = new WebpageXML(false, $plugin);
        $output->run();
    }
} else {
    $output = new WebpageXML(false, null);
    $output->run();
}
?>
