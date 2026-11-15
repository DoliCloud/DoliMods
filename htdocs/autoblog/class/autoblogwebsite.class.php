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
 * \file        htdocs/autoblog/class/autoblogwebsite.class.php
 * \ingroup     autoblog
 * \brief       CRUD class for AutoBlogWebsite: editorial setup of a website used to generate its blog articles
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';


/**
 * Class for AutoBlogWebsite. There is one record per website of the Website module (once its setup was saved).
 */
class AutoBlogWebsite extends CommonObject
{
	/**
	 * @var string 		ID of module.
	 */
	public $module = 'autoblog';

	/**
	 * @var string 		ID to identify managed object.
	 */
	public $element = 'autoblogwebsite';

	/**
	 * @var string		Prefix to check for any trigger code of any business class to prevent bad value for trigger code.
	 */
	public $TRIGGER_PREFIX = 'AUTOBLOG_WEBSITE';

	/**
	 * @var string 		Name of table without prefix where object is stored.
	 */
	public $table_element = 'autoblog_website';

	/**
	 * @var string 		String with name of icon.
	 */
	public $picto = 'website';

	/**
	 * @var int<0,1>	Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 0;

	/**
	 * @var int<0,1>|string		Does this object support multicompany module ?
	 */
	public $ismultientitymanaged = 1;

	/**
	 * @inheritdoc
	 * Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields = array(
		'rowid' => array('type' => 'integer', 'label' => 'TechnicalID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'noteditable' => 1, 'index' => 1),
		'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => '1', 'enabled' => 1, 'visible' => 0, 'notnull' => 1, 'position' => 5, 'index' => 1),
		'fk_website' => array('type' => 'integer', 'label' => 'WebSite', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 0, 'index' => 1),
		'description' => array('type' => 'text', 'label' => 'AutoBlogWebsiteDescription', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
		'differentiators' => array('type' => 'text', 'label' => 'AutoBlogDifferentiators', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
		'target_audience' => array('type' => 'text', 'label' => 'AutoBlogTargetAudience', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
		'fk_country' => array('type' => 'integer', 'label' => 'AutoBlogTargetLocation', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
		'url_competitors' => array('type' => 'text', 'label' => 'AutoBlogUrlCompetitors', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
		'url_product_feed' => array('type' => 'varchar(255)', 'label' => 'AutoBlogUrlProductFeed', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
		'url_sitemap' => array('type' => 'varchar(255)', 'label' => 'AutoBlogUrlSitemap', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
		'url_style_example' => array('type' => 'varchar(255)', 'label' => 'AutoBlogUrlStyleExample', 'enabled' => 1, 'position' => 85, 'notnull' => 0, 'visible' => 1),
		'instructions' => array('type' => 'text', 'label' => 'AutoBlogGenericInstructions', 'enabled' => 1, 'position' => 15, 'notnull' => 0, 'visible' => 1),
		'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
		'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 0, 'visible' => -2),
		'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 1, 'visible' => -2, 'foreignkey' => '0'),
		'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => -1, 'visible' => -2),
	);

	/**
	 * @var int ID
	 */
	public $rowid;

	/**
	 * @var int ID of the website
	 */
	public $fk_website;

	/**
	 * @var ?string Description of the website
	 */
	public $description;

	/**
	 * @var ?string What sets the company apart from competitors
	 */
	public $differentiators;

	/**
	 * @var ?string Target audience
	 */
	public $target_audience;

	/**
	 * @var ?int ID of the target country (empty = all countries)
	 */
	public $fk_country;

	/**
	 * @var ?string URLs of competitors (one per line)
	 */
	public $url_competitors;

	/**
	 * @var ?string URL of the product catalog (Google shopping feed)
	 */
	public $url_product_feed;

	/**
	 * @var ?string URL of the sitemap
	 */
	public $url_sitemap;

	/**
	 * @var ?string URL of an article whose writing style is an example to follow
	 */
	public $url_style_example;

	/**
	 * @var ?string Generic instructions added to the generation of each article (one per line)
	 */
	public $instructions;

	/**
	 * @var int ID
	 */
	public $fk_user_creat;

	/**
	 * @var ?int ID
	 */
	public $fk_user_modif;


	/**
	 * Constructor
	 *
	 * @param	DoliDB $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;

		if (!isModEnabled('multicompany') && isset($this->fields['entity'])) {
			$this->fields['entity']['enabled'] = 0;
		}

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val) {
			if (isset($val['enabled']) && empty($val['enabled'])) {
				unset($this->fields[$key]);
			}
		}
	}

	/**
	 * Create object into database
	 *
	 * @param	User		$user		User that creates
	 * @param	int<0,1> 	$notrigger	0=launch triggers after, 1=disable triggers
	 * @return	int<-1,max>				Return integer <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = 0)
	{
		return $this->createCommon($user, $notrigger);
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param	int    		$id   			Id object
	 * @param	string 		$ref  			Ref (not used)
	 * @return	int<-1,1>					Return integer <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		return $this->fetchCommon($id, null, '', 1);
	}

	/**
	 * Load the editorial setup of a website
	 *
	 * @param	int		$fkWebsite		ID of the website
	 * @return	int						Return integer <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetchByWebsite(int $fkWebsite): int
	{
		$sql = "SELECT t.rowid FROM ".$this->db->prefix().$this->table_element." as t";
		$sql .= " WHERE t.fk_website = ".((int) $fkWebsite);
		$sql .= " AND t.entity IN (".getEntity($this->element).")";

		$resql = $this->db->query($sql);
		if (!$resql) {
			$this->error = $this->db->lasterror();
			return -1;
		}
		$obj = $this->db->fetch_object($resql);
		$this->db->free($resql);
		if (!$obj) {
			return 0;
		}

		return $this->fetch((int) $obj->rowid);
	}

	/**
	 * Update object into database
	 *
	 * @param	User		$user		User that modifies
	 * @param	int<0,1>	$notrigger	0=launch triggers after, 1=disable triggers
	 * @return	int<-1,1>				Return integer <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = 0)
	{
		return $this->updateCommon($user, $notrigger);
	}

	/**
	 * Delete object in database
	 *
	 * @param	User		$user		User that deletes
	 * @param	int<0,1> 	$notrigger	0=launch triggers, 1=disable triggers
	 * @return	int<-1,1>				Return integer <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = 0)
	{
		return $this->deleteCommon($user, $notrigger);
	}

	/**
	 * Return the non empty lines of a multi-line field (instructions, URLs of competitors, ...)
	 *
	 * @param	string		$field		Name of the field
	 * @return	string[]				Array of trimmed non empty lines
	 */
	public function getLines(string $field): array
	{
		$value = (isset($this->fields[$field]) ? (string) $this->$field : '');
		$lines = array_map('trim', (array) preg_split('/\r\n|\r|\n/', $value));

		return array_values(array_filter($lines, static function ($line) {
			return $line !== '';
		}));
	}

	/**
	 * Return if at least one section of the setup is filled
	 *
	 * @return	bool
	 */
	public function isFilled(): bool
	{
		foreach (array('description', 'differentiators', 'target_audience', 'url_competitors', 'url_product_feed', 'url_sitemap', 'url_style_example', 'instructions') as $field) {
			if (trim((string) $this->$field) !== '') {
				return true;
			}
		}

		return ((int) $this->fk_country > 0);
	}
}
