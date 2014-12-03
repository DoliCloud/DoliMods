<?php
/* Copyright (C) 2009-2014 Regis Houssin <regis.houssin@capnetworks.com>
 * Copyright (C) 2011      Herve Prot    <herve.prot@symeos.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *		\file       htdocs/multicompany/dao_multicompany.class.php
 *		\ingroup    multicompany
 *		\brief      File Class multicompany
 */


/**
 *		\class      DaoMulticompany
 *		\brief      Class of the module multicompany
 */
class DaoMulticompany
{
	var $db;
	var $error;
	var $errors=array();
	//! Numero de l'erreur
	var $errno = 0;

	var $id;
	var $label;
	var $description;

	var $options=array();
	var $options_json;

	var $entity=array();
	var $entities=array();

	var $fk_tables=array();


	/**
	 *	Constructor
	 *
	 *	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;

		$this->fk_tables = array(
				'societe' => array(
						'key' => 'fk_soc',
						'childs' => array(
								'societe_address',
								'societe_commerciaux',
								'societe_log',
								'societe_prices',
								'societe_remise',
								'societe_remise_except',
								'societe_rib',
								'socpeople'
						)
				),
				'product' => array(
						'key' => 'fk_product',
						'childs' => array(
								'product_ca',
								'product_lang',
								'product_price',
								'product_stock',
								'product_fournisseur_price' => array(
										'key' => 'fk_product_fournisseur',
										'childs' => array('product_fournisseur_price_log')
								),
						)
				),
				'projet' => array(
						'key' => 'fk_projet',
						'childs' => array(
								'projet_task' => array(
										'key' => 'fk_task',
										'childs' => array('projet_task_time')
								)
						)
				)
		);
	}

	/**
	 *    Fetch entity
	 */
	function fetch($id)
	{
		global $conf,$langs,$user;

		$this->entity=array();

		$sql = "SELECT rowid, label, description, options, visible, active";
		$sql.= " FROM ".MAIN_DB_PREFIX."entity";
		$sql.= " WHERE rowid = ".$id;

		$result = $this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);

				$this->id			= $obj->rowid;
				$this->label		= $obj->label;
				$this->description 	= $obj->description;
				$this->options		= json_decode($obj->options, true);
				$this->visible 		= $obj->visible;
				$this->active		= $obj->active;

				// for backward compatibility
				if (isset($this->options['sharings']['referent']))
				{
					if (empty($this->options['referent']))
					{
						$this->options['referent'] = $this->options['sharings']['referent'];
					}
					unset($this->options['sharings']['referent']);
				}

				// constantes if connected
				if(! empty($user->login))
				{
					require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");

					$sql = "SELECT ";
					$sql.= $this->db->decrypt('name')." as name";
					$sql.= ", ".$this->db->decrypt('value')." as value";
					$sql.= " FROM ".MAIN_DB_PREFIX."const";
					$sql.= " WHERE ".$this->db->decrypt('name')." LIKE 'MAIN_%'";
					$sql.= " AND entity = ".$obj->rowid;

					$result = $this->db->query($sql);
					if ($result)
					{
						$num=$this->db->num_rows($result);
						$i=0;

						while ($i < $num)
						{
							$obj = $this->db->fetch_object($result);

							if (preg_match('/^MAIN_INFO_SOCIETE_COUNTRY$/i',$obj->name))
							{
								$tmp=explode(':',$obj->value);
								$country_id=$tmp[0];
								$this->country = getCountry($country_id);
							}
							else if (preg_match('/^MAIN_MONNAIE$/i',$obj->name))
							{
								$this->currency = currency_name($obj->value);
							}
							else if (preg_match('/^MAIN_LANG_DEFAULT$/i',$obj->name))
							{
								$s=picto_from_langcode($obj->value);
								$language=($s?$s.' ':'');
								$language.=($obj->value=='auto'?$langs->trans("AutoDetectLang"):$langs->trans("Language_".$obj->value));
								$this->language = $language;
							}

							$constname = $obj->name;
							$this->$constname = $obj->value;

							$i++;
						}
					}
				}

				return 1;
			}
			else
			{
				return -2;
			}
		}
		else
		{
			return -3;
		}
	}

	/**
	 *    Create entity
	 */
	function create($user)
	{
		global $conf;

		// Clean parameters
		$this->label 		= trim($this->label);
		$this->description	= trim($this->description);
		$this->options_json = json_encode($this->options);

		dol_syslog(get_class($this)."::create ".$this->label);

		$this->db->begin();

		$now=dol_now();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."entity (";
		$sql.= "label";
		$sql.= ", description";
		$sql.= ", datec";
		$sql.= ", fk_user_creat";
		$sql.= ", options";
		$sql.= ", visible";
		$sql.= ", active";
		$sql.= ") VALUES (";
		$sql.= "'".$this->db->escape($this->label)."'";
		$sql.= ", '".$this->db->escape($this->description)."'";
		$sql.= ", '".$this->db->idate($now)."'";
		$sql.= ", ".$user->id;
		$sql.= ", '".$this->db->escape($this->options_json)."'";
		$sql.= ", 0";
		$sql.= ", 0";
		$sql.= ")";

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."entity");

			dol_syslog(get_class($this)."::Create success id=".$this->id);
			$this->db->commit();
            return $this->id;
		}
		else
		{
			dol_syslog(get_class($this)."::Create echec ".$this->error);
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *    Update entity
	 */
	function update($id, $user)
	{
		global $conf;

		// Clean parameters
		$this->label 		= trim($this->label);
		$this->description	= trim($this->description);
		$this->options_json = json_encode($this->options);

		dol_syslog(get_class($this)."::update id=".$id." label=".$this->label);

		$this->db->begin();

		$sql = "UPDATE ".MAIN_DB_PREFIX."entity SET";
		$sql.= " label = '" . $this->db->escape($this->label) ."'";
		$sql.= ", description = '" . $this->db->escape($this->description) ."'";
		$sql.= ", options = '" . $this->db->escape($this->options_json) ."'";
		$sql.= " WHERE rowid = " . $id;

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			dol_syslog(get_class($this)."::Update success id=".$id);
			$this->db->commit();
            return 1;
		}
		else
		{
			dol_syslog(get_class($this)."::Update echec ".$this->error, LOG_ERR);
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *    Delete entity
	 */
	function delete($id)
	{
		$error=0;

		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."const";
		$sql.= " WHERE entity = " . $id;
		dol_syslog(get_class($this)."::Delete sql=".$sql, LOG_DEBUG);
		if ($this->db->query($sql))
		{
			// TODO remove records of all tables
		}
		else
		{
			$error++;
			$this->error .= $this->db->lasterror();
			dol_syslog(get_class($this)."::Delete erreur -1 ".$this->error, LOG_ERR);
		}

		if (! $error)
		{
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."entity";
			$sql.= " WHERE rowid = " . $id;
			dol_syslog(get_class($this)."::Delete sql=".$sql, LOG_DEBUG);
			if (!$this->db->query($sql)) {
				$error++;
				$this->error .= $this->db->lasterror();
				dol_syslog(get_class($this)."::Delete erreur -1 ".$this->error, LOG_ERR);
			}
		}

		if (! $error)
		{
			dol_syslog(get_class($this)."::Delete success id=".$id);
			$this->db->commit();
            return 1;
		}
		else
		{
			dol_syslog(get_class($this)."::Delete echec ".$this->error);
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	Remove all records of an entity
	 *
	 *	@param	int		$id		Entity id
	 *	@return	void
	 */
	function deleteEntityRecords($id)
	{
		$error=1;

		$this->db->begin();

		$tables = $this->db->DDLListTables($this->db->database_name);
		if (is_array($tables) && !empty($tables))
		{
			foreach($tables as $table)
			{
				$fields = $this->db->DDLInfoTable($table);
				foreach ($fields as $field)
				{
					if (is_array($field) && in_array('entity', $field))
					{
						$tablewithoutprefix = str_replace(MAIN_DB_PREFIX, '', $table);
						$objIds = $this->getIdByForeignKey($tablewithoutprefix, $id);
						if (!empty($objIds))
						{
							if (array_key_exists($tablewithoutprefix, $this->fk_tables))
							{
								// Level 0
								$foreignKey = $this->fk_tables[$tablewithoutprefix]['key'];
								foreach($this->fk_tables[$tablewithoutprefix]['childs'] as $childTable => $child)
								{
									// Level 1
									if (!is_int($childTable) && is_array($child))
									{
										echo 'childTableLevel1='.$childTable.'<br>';
										$objLevel1Ids = array();
										foreach($objIds as $rowid)
										{
											$ret = $this->getIdByForeignKey($childTable, $rowid, $foreignKey);
											if (!empty($ret))
												$objLevel1Ids = array_merge($objLevel1Ids, $ret);
										}

										sort($objLevel1Ids);
										//var_dump($objLevel1Ids);

										// Level 2
										foreach($child['childs'] as $childLevel2)
										{
											echo 'childTableLevel2='.$childLevel2.'<br>';
											foreach($objLevel1Ids as $rowid)
											{
												$sql = "DELETE FROM " . MAIN_DB_PREFIX . $childLevel2;
												$sql.= " WHERE " . $child['key'] . " = " . $rowid;
												//echo $sql.'<br>';
												//dol_syslog(get_class($this)."::deleteEntityRecords sql=" . $sql, LOG_DEBUG);
												/*if (!$this->db->query($sql)) {
												 $error++;
												$this->error .= $this->db->lasterror();
												dol_syslog(get_class($this)."::deleteEntityRecords error -1 " . $this->error, LOG_ERR);
												}*/
											}
										}

										foreach($objIds as $rowid)
										{
											$sql = "DELETE FROM " . MAIN_DB_PREFIX . $childTable;
											$sql.= " WHERE " . $foreignKey . " = " . $rowid;
											//echo $sql.'<br>';
											//dol_syslog(get_class($this)."::deleteEntityRecords sql=" . $sql, LOG_DEBUG);
											/*if (!$this->db->query($sql)) {
											 $error++;
											$this->error .= $this->db->lasterror();
											dol_syslog(get_class($this)."::deleteEntityRecords error -1 " . $this->error, LOG_ERR);
											}*/
										}
									}
									else
									{
										foreach($objIds as $rowid)
										{
											$sql = "DELETE FROM " . MAIN_DB_PREFIX . $child;
											$sql.= " WHERE " . $foreignKey . " = " . $rowid;
											//echo $sql.'<br>';
											//dol_syslog(get_class($this)."::deleteEntityRecords sql=" . $sql, LOG_DEBUG);
											/*if (!$this->db->query($sql)) {
											 $error++;
											$this->error .= $this->db->lasterror();
											dol_syslog(get_class($this)."::deleteEntityRecords error -1 " . $this->error, LOG_ERR);
											}*/
										}
									}
								}
								echo 'with childs = '.$table.'<br>';
							}
							else
							{
								echo 'without childs = '.$table.'<br>';
							}
						}
					}
				}
			}

			if (! $error)
			{
				dol_syslog(get_class($this)."::deleteEntityRecords success entity=".$id);
				$this->db->commit();
				return 1;
			}
			else
			{
				dol_syslog(get_class($this)."::deleteEntityRecords echec ".$this->error);
				$this->db->rollback();
				return -1;
			}
		}
	}

	/**
	 * Get all rowid from a table by couple foreign key / id
	 */
	private function getIdByForeignKey($table, $id, $foreignkey = 'entity', $fieldname = 'rowid')
	{
		$objIds=array();

		$sql = "SELECT " . $fieldname . " FROM " . MAIN_DB_PREFIX .$table;
		$sql.= " WHERE " . $foreignkey . " = " . $id;
		//echo $sql.'<br>';
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);
				$objIds[] = $obj->rowid;
				$i++;
			}
		}

		return $objIds;
	}

    /**
	 *	Enable/disable entity
	 *
	 *	@param	id
	 *	@param	type
	 *	@param	value
	 */
	function setEntity($id, $type='active', $value)
	{
		global $conf;

		$this->db->begin();

		$sql = "UPDATE ".MAIN_DB_PREFIX."entity";
		$sql.= " SET ".$type." = ".$value;
		$sql.= " WHERE rowid = ".$id;

		dol_syslog(get_class($this)."::setEntity sql=".$sql, LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	List of entities
	 *
	 *	@param		int		$login		If use in login page or not
	 *	@return		void
	 */
	function getEntities($login=0)
	{
		global $conf, $user;

		if ($login || empty($conf->multicompany->transverse_mode) || $user->admin)
		{
			$sql = "SELECT rowid";
			$sql.= " FROM ".MAIN_DB_PREFIX."entity";
			$sql.= " ORDER by rowid";
		}
		else
		{
			$sql = "SELECT entity as rowid";
			$sql.= " FROM ".MAIN_DB_PREFIX."usergroup_user";
			$sql.= " WHERE fk_user=".$user->id;
			$sql.= " ORDER by entity";
		}

		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);

				$objectstatic = new self($this->db);
				$ret = $objectstatic->fetch($obj->rowid);

				$this->entities[$i] = $objectstatic;

				$i++;
			}
		}
	}

    /**
	 *    Verify right
	 */
	function verifyRight($id, $userid)
	{
		global $conf;

		$sql = "SELECT count(rowid) as nb";
		$sql.= " FROM ".MAIN_DB_PREFIX."usergroup_user";
		$sql.= " WHERE fk_user=".$userid;
		$sql.= " AND entity=".$id;

		dol_syslog(get_class($this)."::verifyRight sql=".$sql, LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$obj = $this->db->fetch_object($result);
			return $obj->nb;
		}
	}

	/**
	 * 	Get constants values of an entity
	 *
	 * 	@param	int		$entity		Entity id
	 * 	@return array				Array of constantes
	 */
	function getEntityConfig($entity)
	{
		$const=array();

		$sql = "SELECT ".$this->db->decrypt('value')." as value";
		$sql.= ", ".$this->db->decrypt('name')." as name";
		$sql.= " FROM ".MAIN_DB_PREFIX."const";
		$sql.= " WHERE entity = ".$entity;

		dol_syslog(get_class($this)."::getEntityConfig sql=".$sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);

				$const[$obj->name] = $obj->value;

				$i++;
			}

			return $const;
		}
	}

}
?>