<?php
/**
 * PS Plugin Config File
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_PS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: PS.config.php,v 1.1 2011/08/01 19:28:56 eldy Exp $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define how to access the ps statistic data
 * - 'command' ps command is run everytime the block gets refreshed or build / on WINNT information is retrieved everytime through WMI
 * - 'data' (a file must be available in the data directory of the phpsysinfo installation with the filename "ps.txt"; content is the output from "ps -axo pid,ppid,pmem,args")
 */
define('PSI_PLUGIN_PS_ACCESS', 'command');
?>
