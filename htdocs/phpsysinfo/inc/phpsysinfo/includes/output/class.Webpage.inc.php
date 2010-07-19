<?php 
/**
 * start page for webaccess
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Web
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Webpage.inc.php,v 1.1 2010/07/19 18:45:44 eldy Exp $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * generate the dynamic webpage
 *
 * @category  PHP
 * @package   PSI_Web
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Webpage extends Output implements PSI_Interface_Output
{
    /**
     * configured language
     *
     * @var String
     */
    private $_language;
    
    /**
     * configured template
     *
     * @var String
     */
    private $_template;
    
    /**
     * all available templates
     *
     * @var Array
     */
    private $_templates = array();
    
    /**
     * all available languages
     *
     * @var Array
     */
    private $_languages = array();
    
    /**
     * check for all extensions that are needed, initialize needed vars and read config.php
     */
    public function __construct()
    {
        parent::__construct();
        $this->_getTemplateList();
        $this->_getLanguageList();
    }
    
    /**
     * checking config.php setting for template, if not supportet set phpsysinfo.css as default
     * checking config.php setting for language, if not supported set en as default
     *
     * @return void
     */
    private function _checkTemplateLanguage()
    {
        $this->_template = trim(PSI_DEFAULT_TEMPLATE);
        if (!file_exists(APP_ROOT.'/templates/'.$this->_template.".css")) {
            $this->_template = 'phpsysinfo';
        }
        
        $this->_language = trim(PSI_DEFAULT_LANG);
        if (!file_exists(APP_ROOT.'/language/'.$this->_language.".xml")) {
            $this->_language = 'en';
        }
    }
    
    /**
     * get all available tamplates and store them in internal array
     *
     * @return void
     */
    private function _getTemplateList()
    {
        $dirlist = CommonFunctions::gdc(APP_ROOT.'/templates/');
        sort($dirlist);
        foreach ($dirlist as $file) {
            $tpl_ext = substr($file, strlen($file) - 4);
            $tpl_name = substr($file, 0, strlen($file) - 4);
            if ($tpl_ext === ".css") {
                array_push($this->_templates, $tpl_name);
            }
        }
    }
    
    /**
     * get all available translations and store them in internal array
     *
     * @return void
     */
    private function _getLanguageList()
    {
        $dirlist = CommonFunctions::gdc(APP_ROOT.'/language/');
        sort($dirlist);
        foreach ($dirlist as $file) {
            $lang_ext = substr($file, strlen($file) - 4);
            $lang_name = substr($file, 0, strlen($file) - 4);
            if ($lang_ext == ".xml") {
                array_push($this->_languages, $lang_name);
            }
        }
    }
    
    /**
     * render the page
     *
     * @return void
     */
    public function run()
    {
        $this->_checkTemplateLanguage();
        
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
        echo "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
        echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
        echo "  <head>\n";
        echo "    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
        echo "    <meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n";
        echo "    <meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\" />\n";
        echo "    <meta name=\"Description\" content=\"PHPSysInfo is a customizable PHP Script that parses /proc, and formats information nicely.";
        echo " It will display information about system facts like Uptime, CPU, Memory, PCI devices, SCSI devices, IDE devices, Network adapters, Disk usage, and more.\" />\n";
        echo "    <link type=\"text/css\" rel=\"stylesheet\" href=\"./templates/".$this->_template.".css\" title=\"PSI_Template\"/>\n";
        echo "    <link type=\"text/css\" rel=\"stylesheet\" href=\"./templates/plugin/nyroModal.full.css\" />\n";
        echo "    <link type=\"text/css\" rel=\"stylesheet\" href=\"./templates/plugin/jquery.jgrowl.css\" />\n";
        echo "    <link type=\"text/css\" rel=\"stylesheet\" href=\"./templates/plugin/jquery.dataTables.css\" />\n";
        echo "    <link type=\"text/css\" rel=\"stylesheet\" href=\"./templates/plugin/jquery.treeTable.css\" />\n";
        echo "    <script type=\"text/JavaScript\" src=\"./js.php?name=jquery\"></script>\n";
        echo "    <script type=\"text/JavaScript\" src=\"./js.php?name=jquery.dataTables\"></script>\n";
        echo "    <script type=\"text/JavaScript\" src=\"./js.php?name=jquery.nyroModal\"></script>\n";
        echo "    <script type=\"text/JavaScript\" src=\"./js.php?name=jquery.jgrowl\"></script>\n";
        echo "    <script type=\"text/JavaScript\" src=\"./js.php?name=jquery.timers\"></script>\n";
        echo "    <script type=\"text/JavaScript\" src=\"./js.php?name=jquery.treeTable\"></script>\n";
        echo "    <script type=\"text/JavaScript\" src=\"./js.php?name=phpsysinfo\"></script>\n";
        foreach (CommonFunctions::getPlugins() as $plugin) {
            echo "    <script type=\"text/JavaScript\" src=\"./js.php?plugin=".trim($plugin)."\"></script>\n";
        }
        echo "    <title>PhpSysInfo ".CommonFunctions::PSI_VERSION."</title>\n";
        echo "  </head>\n";
        echo "  <body>\n";
        echo "    <div id=\"loader\">\n";
        echo "      <h1>Loading... please wait!</h1>\n";
        echo "    </div>\n";
        echo "    <div id=\"errors\" style=\"display: none; width: 940px\">\n";
        echo "      <div id=\"errorlist\">\n";
        echo "        <h2>Oh, I'm sorry. Something seems to be wrong.</h2>\n";
        echo "      </div>\n";
        echo "    </div>\n";
        echo "    <div id=\"container\" style=\"display: none;\">\n";
        echo "      <h1>\n";
        echo "        <a href=\"#errors\" class=\"nyroModal\">\n";
        echo "          <img id=\"warn\" style=\"vertical-align: middle; display:none; border:0px;\" src=\"./gfx/attention.png\" alt=\"warning\" />\n";
        echo "        </a>\n";
        echo "        <span id=\"title\">\n";
        echo "          <span id=\"lang_001\">System information</span>\n";
        echo "          :&nbsp;<span id=\"s_hostname_title\"></span>\n";
        echo "          (<span id=\"s_ip_title\"></span>)\n";
        echo "        </span>\n";
        echo "      </h1>\n";
        echo "      <div id=\"select\">\n";
        echo "        <span id=\"lang_044\">Template</span>\n";
        echo "        <select id=\"template\" name=\"template\">\n";
        foreach ($this->_templates as $template) {
            $selected = "";
            if ($this->_template === $template) {
                $selected = " selected=\"selected\"";
            }
            echo "          <option value=\"".$template."\"".$selected.">".$template."</option>\n";
        }
        echo "        </select>\n";
        echo "        <span id=\"lang_045\">Language</span>\n";
        echo "        <select id=\"lang\" name=\"lang\">\n";
        foreach ($this->_languages as $language) {
            $selected = "";
            if ($this->_language === $language) {
                $selected = " selected=\"selected\"";
            }
            echo "          <option value=\"".$language."\"".$selected.">".$language."</option>\n";
        }
        echo "        </select>\n";
        echo "      </div>\n";
        echo "      <div id=\"vitals\">\n";
        echo "        <h2><span id=\"lang_002\">System vitals</span></h2>\n";
        echo "        <table class=\"stripeMe\" id=\"vitalsTable\" cellspacing=\"0\">\n";
        echo "          <tr>\n";
        echo "            <td style=\"width:160px;\"><span id=\"lang_003\">Hostname</span></td>\n";
        echo "            <td><span id=\"s_hostname\"></span></td>\n";
        echo "          </tr>\n";
        echo "          <tr>\n";
        echo "            <td style=\"width:160px;\"><span id=\"lang_004\">Listening IP</span></td>\n";
        echo "            <td><span id=\"s_ip\"></span></td>\n";
        echo "          </tr>\n";
        echo "          <tr>\n";
        echo "            <td style=\"width:160px;\"><span id=\"lang_005\">Kernel Version</span></td>\n";
        echo "            <td><span id=\"s_kernel\"></span></td>\n";
        echo "          </tr>\n";
        echo "          <tr>\n";
        echo "            <td style=\"width:160px;\"><span id=\"lang_006\">Distro Name</span></td>\n";
        echo "            <td><span id=\"s_distro\"></span></td>\n";
        echo "          </tr>\n";
        echo "          <tr>\n";
        echo "            <td style=\"width:160px;\"><span id=\"lang_007\">Uptime</span></td>\n";
        echo "            <td><span id=\"s_uptime\"></span></td>\n";
        echo "          </tr>\n";
        echo "          <tr>\n";
        echo "            <td style=\"width:160px;\"><span id=\"lang_008\">Current Users</span></td>\n";
        echo "            <td><span id=\"s_users\"></span></td>\n";
        echo "          </tr>\n";
        echo "          <tr>\n";
        echo "            <td style=\"width:160px;\"><span id=\"lang_009\">Load Averages</span></td>\n";
        echo "            <td id=\"s_loadavg\"></td>\n";
        echo "          </tr>\n";
        echo "        </table>\n";
        echo "      </div>\n";
        echo "      <div id=\"hardware\">\n";
        echo "      </div>\n";
        echo "      <div id=\"memory\">\n";
        echo "      </div>\n";
        echo "      <div id=\"filesystem\">\n";
        echo "      </div>\n";
        echo "      <div id=\"network\">\n";
        echo "        <h2><span id=\"lang_021\">Network Usage</span></h2>\n";
        echo "        <table class=\"stripeMe\" id=\"networkTable\" cellspacing=\"0\">\n";
        echo "          <thead>\n";
        echo "            <tr>\n";
        echo "              <th><span id=\"lang_022\">Interface</span></th>\n";
        echo "              <th class=\"right\" style=\"width:60px;\"><span id=\"lang_023\">Recieved</span></th>\n";
        echo "              <th class=\"right\" style=\"width:60px;\"><span id=\"lang_024\">Transfered</span></th>\n";
        echo "              <th class=\"right\" style=\"width:60px;\"><span id=\"lang_025\">Error/Drops</span></th>\n";
        echo "            </tr>\n";
        echo "          </thead>\n";
        echo "          <tbody id=\"tbody_network\">\n";
        echo "          </tbody>\n";
        echo "        </table>\n";
        echo "      </div>\n";
        echo "      <div id=\"voltage\" style=\"display: none;\">\n";
        echo "        <h2><span id=\"lang_052\">Voltage</span></h2>\n";
        echo "        <table class=\"stripeMe\" id=\"voltageTable\" cellspacing=\"0\">\n";
        echo "          <thead>\n";
        echo "            <tr>\n";
        echo "              <th><span id=\"lang_059\">Label</span></th>\n";
        echo "              <th class=\"right\"><span id=\"lang_052\">Voltage</span></th>\n";
        echo "              <th class=\"right\" style=\"width: 80px;\"><span id=\"lang_055\">Min</span></th>\n";
        echo "              <th class=\"right\" style=\"width: 80px;\"><span id=\"lang_056\">Max</span></th>\n";
        echo "            </tr>\n";
        echo "          </thead>\n";
        echo "          <tbody>\n";
        echo "          </tbody>\n";
        echo "        </table>\n";
        echo "      </div>\n";
        echo "      <div id=\"temp\" style=\"display: none;\">\n";
        echo "        <h2><span id=\"lang_051\">Temperature</span></h2>\n";
        echo "        <table class=\"stripeMe\" id=\"tempTable\" cellspacing=\"0\">\n";
        echo "          <thead>\n";
        echo "            <tr>\n";
        echo "              <th><span id=\"lang_059\">Label</span></th>\n";
        echo "              <th class=\"right\" style=\"width: 80px;\"><span id=\"lang_054\">Value</span></th>\n";
        echo "              <th class=\"right\" style=\"width: 80px;\"><span id=\"lang_058\">Limit</span></th>\n";
        echo "            </tr>\n";
        echo "          </thead>\n";
        echo "          <tbody>\n";
        echo "          </tbody>\n";
        echo "        </table>\n";
        echo "      </div>\n";
        echo "      <div id=\"fan\" style=\"display: none;\">\n";
        echo "        <h2><span id=\"lang_053\">Fan</span></h2>\n";
        echo "        <table class=\"stripeMe\" id=\"fanTable\" cellspacing=\"0\">\n";
        echo "          <thead>\n";
        echo "            <tr>\n";
        echo "              <th><span id=\"lang_059\">Label</span></th>\n";
        echo "              <th class=\"right\" style=\"width: 80px;\"><span id=\"lang_054\">Value</span></th>\n";
        echo "              <th class=\"right\" style=\"width: 80px;\"><span id=\"lang_055\">Min</span></th>\n";
        echo "            </tr>\n";
        echo "          </thead>\n";
        echo "          <tbody>\n";
        echo "          </tbody>\n";
        echo "        </table>\n";
        echo "      </div>\n";
        echo "      <div id=\"ups\" style=\"display: none;\">\n";
        echo "      </div>\n";
        echo "      <div id=\"footer\">\n";
        echo "        <span id=\"lang_047\">Generated by</span>&nbsp;<a href=\"http://phpsysinfo.sourceforge.net/\">phpSysInfo&nbsp;-&nbsp;<span id=\"version\"></span></a>\n";
        echo "      </div>\n";
        echo "    </div>\n";
        echo "  </body>\n";
        echo "</html>\n";
    }
}
?>
