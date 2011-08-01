<?php
/**
 * PSSTATUS Plugin Config File
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_Quotas
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: Quotas.config.php,v 1.1 2011/08/01 19:28:45 eldy Exp $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define how to access the repquota statistic data
 * - 'command' repquota command is run everytime the block gets refreshed or build
 * - 'data' (a file must be available in the data directory of the phpsysinfo installation with the filename "quotas.txt"; content is the output from "repquota -au")
 */
define('PSI_PLUGIN_QUOTAS_ACCESS', 'command');
?>
