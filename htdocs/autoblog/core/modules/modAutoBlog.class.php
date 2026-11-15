<?php
/* Copyright (C) 2026 Laurent Destailleur <eldy@users.sourceforge.net>
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

declare(strict_types=1);

/**
 * 	\defgroup   autoblog     Module AutoBlog
 *  \brief      AutoBlog module descriptor.
 *
 *  \file       htdocs/autoblog/core/modules/modAutoBlog.class.php
 *  \ingroup    autoblog
 *  \brief      Description and activation file for module AutoBlog
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module AutoBlog
 */
class modAutoBlog extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $conf;

		$this->db = $db;

		// Id for module (must be unique).
		$this->numero = 101100;

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'autoblog';

		// Family is the same than the Website module
		$this->family = "portal";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Module label (no space allowed), used if translation string 'ModuleAutoBlogName' not found
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModuleAutoBlogDesc' not found
		$this->description = "ModuleAutoBlogDesc";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "ModuleAutoBlogDesc";

		// Author
		$this->editor_name = 'DoliCloud';
		$this->editor_url = 'https://www.dolicloud.com?origin=dolimods';
		$this->editor_squarred_logo = '';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated', 'experimental_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';

		// Key used in llx_const table to save module status enabled/disabled
		$this->const_name = 'MAIN_MODULE_'.dol_strtoupper($this->name);

		// Name of image file used for this module (font awesome picto)
		$this->picto = 'fa-blog';

		// Define some features supported by module
		$this->module_parts = array(
			'triggers' => 0,
			'login' => 0,
			'substitutions' => 0,
			'menus' => 0,
			'tpl' => 0,
			'barcode' => 0,
			'models' => 0,
			'printing' => 0,
			'theme' => 0,
			'css' => array(),
			'js' => array(),
			'hooks' => array(),
			'moduleforexternal' => 0,
			'websitetemplates' => 0,
			'captcha' => 0
		);

		// Data directories to create when module is enabled.
		$this->dirs = array("/autoblog/temp");

		// Config pages. Put here list of php page, stored into autoblog/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@autoblog");

		// Dependencies
		$this->hidden = getDolGlobalInt('MODULE_AUTOBLOG_DISABLED');
		$this->depends = array('modWebsite', 'modAi');
		$this->requiredby = array();
		$this->conflictwith = array();

		// The language file dedicated to your module
		$this->langfiles = array("autoblog@autoblog");

		// Prerequisites
		$this->phpmin = array(8, 1);
		$this->need_dolibarr_version = array(25, 0, -3);
		$this->need_javascript_ajax = 0;

		// Messages at activation
		$this->warnings_activation = array();
		$this->warnings_activation_ext = array();

		// Constants
		$this->const = array(
			1 => array('AUTOBLOG_MAX_ATTEMPTS', 'chaine', '3', 'Max number of generation attempts by the scheduled job for a topic', 0, 'current', 0),
			2 => array('AUTOBLOG_AI_TIMEOUT', 'chaine', '120', 'Timeout in seconds of a request to the AI service', 0, 'current', 0),
			3 => array('AUTOBLOG_TARGET_WORDS', 'chaine', '800', 'Target number of words of a generated article', 0, 'current', 0),
		);

		if (!isModEnabled("autoblog")) {
			$conf->autoblog = new stdClass();
			$conf->autoblog->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array();

		// Dictionaries
		$this->dictionaries = array();

		// Boxes/Widgets
		$this->boxes = array();

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// The parameter is the max number of articles to generate at each run.
		$this->cronjobs = array(
			0 => array(
				'label' => 'AutoBlogGenerateArticles',
				'jobtype' => 'method',
				'class' => '/autoblog/class/blogtopic.class.php',
				'objectname' => 'BlogTopic',
				'method' => 'generateQueuedArticles',
				'parameters' => '1',
				'comment' => 'Generate with AI the content of the next blog topics in queue. Parameter is the max number of articles to generate at each run.',
				'frequency' => 1,
				'unitfrequency' => 86400,
				'status' => 0,
				'test' => 'isModEnabled("autoblog") && isModEnabled("website") && isModEnabled("ai")',
				'priority' => 50,
			),
		);

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;

		$o = 1;
		$this->rights[$r][0] = $this->numero.sprintf("%02d", ($o * 10) + 1);
		$this->rights[$r][1] = 'Read blog topics and generated articles';
		$this->rights[$r][4] = 'blogtopic';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero.sprintf("%02d", ($o * 10) + 2);
		$this->rights[$r][1] = 'Create/Update blog topics and generate articles';
		$this->rights[$r][4] = 'blogtopic';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero.sprintf("%02d", ($o * 10) + 3);
		$this->rights[$r][1] = 'Delete blog topics';
		$this->rights[$r][4] = 'blogtopic';
		$this->rights[$r][5] = 'delete';
		$r++;
		$this->rights[$r][0] = $this->numero.sprintf("%02d", ($o * 10) + 4);
		$this->rights[$r][1] = 'Publish generated articles on websites';
		$this->rights[$r][4] = 'blogtopic';
		$this->rights[$r][5] = 'publish';
		$r++;

		$o = 2;
		$this->rights[$r][0] = $this->numero.sprintf("%02d", ($o * 10) + 2);
		$this->rights[$r][1] = 'Edit the editorial setup of websites';
		$this->rights[$r][4] = 'websitesetup';
		$this->rights[$r][5] = 'write';
		$r++;

		// Main menu entries to add (left menus into the Tools top menu)
		$this->menu = array();
		$r = 0;
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=tools',
			'type' => 'left',
			'titre' => 'BlogTopics',
			'prefix' => img_picto('', $this->picto, 'class="pictofixedwidth valignmiddle paddingright"'),
			'mainmenu' => 'tools',
			'leftmenu' => 'autoblog_blogtopic',
			'url' => '/autoblog/blogtopic_list.php',
			'langs' => 'autoblog@autoblog',
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("autoblog")',
			'perms' => '$user->hasRight("autoblog", "blogtopic", "read")',
			'target' => '',
			'user' => 0,
			'object' => 'BlogTopic'
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=tools,fk_leftmenu=autoblog_blogtopic',
			'type' => 'left',
			'titre' => 'NewBlogTopic',
			'mainmenu' => 'tools',
			'leftmenu' => 'autoblog_blogtopic_new',
			'url' => '/autoblog/blogtopic_card.php?action=create',
			'langs' => 'autoblog@autoblog',
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("autoblog")',
			'perms' => '$user->hasRight("autoblog", "blogtopic", "write")',
			'target' => '',
			'user' => 0,
			'object' => 'BlogTopic'
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=tools,fk_leftmenu=autoblog_blogtopic',
			'type' => 'left',
			'titre' => 'List',
			'mainmenu' => 'tools',
			'leftmenu' => 'autoblog_blogtopic_list',
			'url' => '/autoblog/blogtopic_list.php',
			'langs' => 'autoblog@autoblog',
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("autoblog")',
			'perms' => '$user->hasRight("autoblog", "blogtopic", "read")',
			'target' => '',
			'user' => 0,
			'object' => 'BlogTopic'
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=tools,fk_leftmenu=autoblog_blogtopic',
			'type' => 'left',
			'titre' => 'WebsitesEditorialSetup',
			'mainmenu' => 'tools',
			'leftmenu' => 'autoblog_websitesetup',
			'url' => '/autoblog/autoblogwebsite_list.php',
			'langs' => 'autoblog@autoblog',
			'position' => 1000 + $r,
			'enabled' => 'isModEnabled("autoblog")',
			'perms' => '$user->hasRight("autoblog", "blogtopic", "read")',
			'target' => '',
			'user' => 0,
		);
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int<-1,1>          	1 if OK, <=0 if KO
	 */
	public function init($options = '')
	{
		$result = $this->_load_tables('/autoblog/sql/');
		if ($result < 0) {
			return -1;
		}

		// Permissions
		$this->remove($options);

		$sql = array();

		return $this->_init($sql, $options);
	}

	/**
	 *	Function called when module is disabled.
	 *	Remove from database constants, boxes and permissions from Dolibarr database.
	 *	Data directories are not deleted
	 *
	 *	@param	string		$options	Options when enabling module ('', 'noboxes')
	 *	@return	int<-1,1>				1 if OK, <=0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
