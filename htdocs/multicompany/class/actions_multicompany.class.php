<?php
/* Copyright (C) 2009-2014 Regis Houssin  <regis@dolibarr.fr>
 * Copyright (C) 2011      Herve Prot     <herve.prot@symeos.com>
 * Copyright (C) 2014      Philippe Grand <philippe.grand@atoo-net.com>
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
 *	\file       htdocs/multicompany/actions_multicompany.class.php
 *	\ingroup    multicompany
 *	\brief      File Class multicompany
 */

require 'dao_multicompany.class.php';

/**
 *	\class      ActionsMulticompany
 *	\brief      Class Actions of the module multicompany
 */
class ActionsMulticompany
{
	var $db;
	var $dao;

	var $mesg;
	var $error;
	var $errors=array();
	//! Numero de l'erreur
	var $errno = 0;

	var $template_dir;
	var $template;

	var $label;
	var $description;

	var $referent;

	var $sharings=array();
	var $options=array();
	var $entities=array();
	var $tpl=array();

	private $config=array();

	// For Hookmanager return
	var $resprints;
	var $results=array();


	/**
	 *	Constructor
	 *
	 *	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Instantiation of DAO class
	 *
	 * @return	void
	 */
	private function getInstanceDao()
	{
		if (! is_object($this->dao))
		{
			$this->dao = new DaoMulticompany($this->db);
		}
	}

	/**
	 * 	Enter description here ...
	 *
	 * 	@param	string	$action		Action type
	 */
	function doActions(&$action='')
	{
		global $conf,$user,$langs;

		$this->getInstanceDao();

		$id=GETPOST('id','int');
		$label=GETPOST('label','alpha');
		$name=GETPOST('name','alpha');
		$description=GETPOST('description','alpha');
		$value=GETPOST('value','int');
		$cancel=GETPOST('cancel');

		if ($action == 'add' && empty($cancel) && $user->admin && ! $user->entity)
		{
			$error=0;

			if (empty($label))
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Label")), 'errors');
				$action = 'create';
			}
			else if (empty($name))
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("CompanyName")), 'errors');
				$action = 'create';
			}

			// Verify if label already exist in database
			if (! $error)
			{
				$this->dao->getEntities();
				if (! empty($this->dao->entities))
				{
					foreach($this->dao->entities as $entity)
					{
						if (strtolower($entity->label) == strtolower($label)) $error++;
					}
					if ($error)
					{
						setEventMessage($langs->trans("ErrorEntityLabelAlreadyExist"), 'errors');
						$action = 'create';
					}
				}
			}

			if (! $error)
        	{
        		$this->db->begin();

        		$this->dao->label = $label;
        		$this->dao->description = $description;

        		$this->dao->options['referent']				= (GETPOST('referring_entity') ? GETPOST('referring_entity') : null);
        		$this->dao->options['sharings']['product']	= (GETPOST('product') ? GETPOST('product') : null);
        		$this->dao->options['sharings']['productprice']	= (GETPOST('productprice') ? GETPOST('productprice') : null);
        		$this->dao->options['sharings']['societe']	= (GETPOST('societe') ? GETPOST('societe') : null);
        		$this->dao->options['sharings']['category']	= (GETPOST('category') ? GETPOST('category') : null);
				$this->dao->options['sharings']['agenda']	= (GETPOST('agenda') ? GETPOST('agenda') : null);
				$this->dao->options['sharings']['bank_account']	= (GETPOST('bank_account') ? GETPOST('bank_account') : null);

        		$id = $this->dao->create($user);
        		if ($id <= 0)
        		{
        			$error++;
        			$errors=($this->dao->error ? array($this->dao->error) : $this->dao->errors);
        			setEventMessage($errors, 'errors');
        			$action = 'create';
        		}

        		if (! $error && $id > 0)
        		{
        			$country_id=GETPOST("country_id");
        			$country_code=getCountry($country_id,2);
        			$country_label=getCountry($country_id,0);

        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_COUNTRY", $country_id.':'.$country_code.':'.$country_label,'chaine',0,'',$id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_NOM",$name,'chaine',0,'',$id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_ADDRESS",GETPOST("address"),'chaine',0,'',$id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_TOWN",GETPOST("town"),'chaine',0,'',$id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_ZIP",GETPOST("zipcode"),'chaine',0,'',$id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_STATE",GETPOST("departement_id"),'chaine',0,'',$id);
        			dolibarr_set_const($this->db, "MAIN_MONNAIE",GETPOST("currency"),'chaine',0,'',$id);
        			dolibarr_set_const($this->db, "MAIN_LANG_DEFAULT",GETPOST("main_lang_default"),'chaine',0,'',$id);

        			// Load sql init_new_entity.sql file
        			$dir	= "/multicompany/sql/";
        			$file 	= 'init_new_entity_nocrypt.sql';
        			if (! empty($conf->db->dolibarr_main_db_encryption) && ! empty($conf->db->dolibarr_main_db_cryptkey))
        			{
        				$file = 'init_new_entity.sql';
        			}
        			$fullpath = dol_buildpath($dir.$file);

        			if (file_exists($fullpath))
        			{
        				$result=run_sql($fullpath,1,$id);
        			}

        			$this->db->commit();
        		}
        		else
        		{
        			$this->db->rollback();
        		}
        	}
		}

		if ($action == 'edit' && $user->admin && ! $user->entity)
		{
			$error=0;

			if ($this->dao->fetch($id) < 0)
			{
				$error++;
				setEventMessage($langs->trans("ErrorEntityIsNotValid"), 'errors');
				$action = '';
			}
		}

		if ($action == 'update' && empty($cancel) && $id > 0 && $user->admin && ! $user->entity)
		{
			$error=0;

			$ret = $this->dao->fetch($id);
			if ($ret < 0)
			{
				$error++;
				setEventMessage($langs->trans("ErrorEntityIsNotValid"), 'errors');
				$action = '';
			}
			else if (empty($label))
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Label")), 'errors');
				$action = 'edit';
			}
			else if (empty($name))
			{
				$error++;
				setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("CompanyName")), 'errors');
				$action = 'edit';
			}

			// Verify if label already exist in database
			if (! $error)
			{
				$this->dao->getEntities();
				if (! empty($this->dao->entities))
				{
					foreach($this->dao->entities as $entity)
					{
						if ($entity->id == $id) continue;
						if (strtolower($entity->label) == strtolower($label)) $error++;
					}
					if ($error)
					{
						setEventMessage($langs->trans("ErrorEntityLabelAlreadyExist"), 'errors');
						$action = 'edit';
					}
				}
			}

			if (! $error)
        	{
        		$this->db->begin();

        		$this->dao->label = $label;
        		$this->dao->description	= $description;

        		$this->dao->options['referent']				= (GETPOST('referring_entity') ? GETPOST('referring_entity') : null);
        		$this->dao->options['sharings']['product']	= (GETPOST('product') ? GETPOST('product') : null);
        		$this->dao->options['sharings']['productprice']	= (GETPOST('productprice') ? GETPOST('productprice') : null);
        		$this->dao->options['sharings']['societe']	= (GETPOST('societe') ? GETPOST('societe') : null);
        		$this->dao->options['sharings']['category']	= (GETPOST('category') ? GETPOST('category') : null);
				$this->dao->options['sharings']['agenda']	= (GETPOST('agenda') ? GETPOST('agenda') : null);
				$this->dao->options['sharings']['bank_account']	= (GETPOST('bank_account') ? GETPOST('bank_account') : null);

        		$ret = $this->dao->update($id,$user);
        		if ($ret <= 0)
        		{
        			$error++;
        			$errors=($this->dao->error ? array($this->dao->error) : $this->dao->errors);
        			setEventMessage($errors, 'errors');
        			$action = 'edit';
        		}

        		if (! $error && $ret > 0)
        		{
        			$country_id=GETPOST("country_id");
        			$country_code=getCountry($country_id,2);
        			$country_label=getCountry($country_id,0);

        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_COUNTRY", $country_id.':'.$country_code.':'.$country_label,'chaine',0,'',$this->dao->id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_NOM",$name,'chaine',0,'',$this->dao->id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_ADDRESS",GETPOST("address"),'chaine',0,'',$this->dao->id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_TOWN",GETPOST("town"),'chaine',0,'',$this->dao->id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_ZIP",GETPOST("zipcode"),'chaine',0,'',$this->dao->id);
        			dolibarr_set_const($this->db, "MAIN_INFO_SOCIETE_STATE",GETPOST("departement_id"),'chaine',0,'',$this->dao->id);
        			dolibarr_set_const($this->db, "MAIN_MONNAIE",GETPOST("currency"),'chaine',0,'',$this->dao->id);
        			dolibarr_set_const($this->db, "MAIN_LANG_DEFAULT",GETPOST("main_lang_default"),'chaine',0,'',$this->dao->id);

        			$this->db->commit();
        		}
        		else
        		{
        			$this->db->rollback();
        		}
        	}
		}

		if ($action == 'confirm_delete' && GETPOST('confirm') == 'yes' && $user->admin && ! $user->entity)
		{
			$error=0;

			if ($id == 1)
			{
				$error++;
				setEventMessage($langs->trans("ErrorNotDeleteMasterEntity"), 'errors');
				$action = '';
			}

			if (! $error && $id > 0)
			{
				if ($this->dao->fetch($id) > 0)
				{
					if ($this->dao->delete($id) > 0)
					{
						setEventMessage($langs->trans('ConfirmedEntityDeleted'));
					}
					else
					{
						setEventMessage($this->dao->error, 'errors');
						$action = '';
					}
				}
			}
		}

		if ($action == 'setactive' && $id > 0 && $user->admin && ! $user->entity)
		{
			$this->dao->setEntity($id,'active',$value);
			if ($value == 0) $this->dao->setEntity($id,'visible',$value);
		}

		if ($action == 'setvisible' && $id > 0 && $user->admin && ! $user->entity)
		{
			$this->dao->setEntity($id,'visible',$value);
		}
	}

	/**
	 *	Return combo list of entities.
	 *
	 *	@param	int		$selected	Preselected entity
	 *	@param	string	$option		Option
	 *	@param	int		$login		If use in login page or not
	 *	@return	void
	 */
	function select_entities($selected='', $htmlname='entity', $option='', $login=0)
	{
		global $user,$langs;

		$this->getInstanceDao();

		$this->dao->getEntities($login);

		$return = '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.$option.'>';
		if (is_array($this->dao->entities))
		{
			foreach ($this->dao->entities as $entity)
			{
				if ($entity->active == 1 && ($entity->visible == 1 || ($user->admin && ! $user->entity)))
				{
					if (! empty($user->login) && ! empty($conf->multicompany->transverse_mode) && ! empty($user->entity) && $this->checkRight($user->id, $entity->id) < 0) continue;

					$return.= '<option value="'.$entity->id.'" ';
					if ($selected == $entity->id)	$return.= 'selected="selected"';
					$return.= '>';
					$return.= $entity->label;
					if (empty($entity->visible)) $return.= ' ('.$langs->trans('Hidden').')';
					$return.= '</option>';
				}
			}
		}
		$return.= '</select>';

		return $return;
	}

	/**
	 *	Return multiselect list of entities.
	 *
	 *	@param	string	$htmlname	Name of select
	 *	@param	array	$current	Current entity to manage
	 *	@param	string	$option		Option
	 *	@return	void
	 */
	function multiselect_entities($htmlname, $current, $option='')
	{
		global $conf, $langs;

		$this->getInstanceDao();
		$this->dao->getEntities();

		$return = '<select id="'.$htmlname.'" class="multiselect" multiple="multiple" name="'.$htmlname.'[]" '.$option.'>';
		if (is_array($this->dao->entities))
		{
			foreach ($this->dao->entities as $entity)
			{
				if (is_object($current) && $current->id != $entity->id && $entity->active == 1)
				{
					$return.= '<option value="'.$entity->id.'" ';
					if (is_array($current->options['sharings'][$htmlname]) && in_array($entity->id, $current->options['sharings'][$htmlname]))
					{
						$return.= 'selected="selected"';
					}
					$return.= '>';
					$return.= $entity->label;
					if (empty($entity->visible))
					{
						$return.= ' ('.$langs->trans('Hidden').')';
					}
					$return.= '</option>';
				}
			}
		}
		$return.= '</select>';

		return $return;
	}

	/**
	 *    Switch to another entity.
	 *
	 *    @param	id		User id
	 *    @param	entity	Entity id
	 */
	function checkRight($id, $entity)
	{
		global $conf;

		$this->getInstanceDao();

		if ($this->dao->fetch($entity) > 0)
		{
			// Controle des droits sur le changement
			if ($this->dao->verifyRight($entity, $id) || $user->admin)
			{
				return 1;
			}
			else
			{
				return -2;
			}
		}
		else
		{
			return -1;
		}
	}

	/**
	 *    Switch to another entity.
	 *    @param	id		Id of the destination entity
	 */
	function switchEntity($id, $userid=null)
	{
		global $conf,$user;

		$this->getInstanceDao();

		if ($this->dao->fetch($id) > 0)
		{
			// Controle des droits sur le changement
			if (!empty($conf->global->MULTICOMPANY_HIDE_LOGIN_COMBOBOX)
			|| (!empty($conf->multicompany->transverse_mode) && $this->dao->verifyRight($id, $user->id))
			|| $user->admin)
			{
				$_SESSION['dol_entity'] = $id;
				$conf->entity = $id;
				return 1;
			}
			else
			{
				return -2;
			}
		}
		else
		{
			return -1;
		}
	}

	/**
	 * 	Get entity info
	 * 	@param	id	Object id
	 */
	function getInfo($id)
	{
		$this->getInstanceDao();
		$this->dao->fetch($id);

		$this->label		= $this->dao->label;
		$this->description	= $this->dao->description;
	}

	/**
	 * 	Get action title
	 * 	@param	action	Type of action
	 */
	function getTitle($action='')
	{
		global $langs;

		if ($action == 'create') return $langs->trans("AddEntity");
		else if ($action == 'edit') return $langs->trans("EditEntity");
		else return $langs->trans("EntitiesManagement");
	}


	/**
	 *    Assigne les valeurs pour les templates
	 *    @param      action     Type of action
	 */
	function assign_values($action='view')
	{
		global $conf,$langs,$user;
		global $form,$formcompany,$formadmin;

		$this->getInstanceDao();

		$this->template_dir = dol_buildpath('/multicompany/tpl/');

		if ($action == 'create')
		{
			$this->template = 'entity_create.tpl.php';
		}
		else if ($action == 'edit')
		{
			$this->template = 'entity_edit.tpl.php';

			if (GETPOST('id')) $ret = $this->dao->fetch(GETPOST('id'));
		}

		if ($action == 'create' || $action == 'edit')
		{
			// Label
			$this->tpl['label'] = (GETPOST('label')?GETPOST('label'):$this->dao->label);

			// Description
			$this->tpl['description'] = (GETPOST('description')?GETPOST('description'):$this->dao->description);

			// Company name
			$this->tpl['name'] = (GETPOST('name')?GETPOST('name'):$this->dao->MAIN_INFO_SOCIETE_NOM);

			// Address
			$this->tpl['address'] = (GETPOST('address')?GETPOST('address'):$this->dao->MAIN_INFO_SOCIETE_ADDRESS);

			// Zip
            $this->tpl['select_zip'] = $formcompany->select_ziptown((GETPOST('zipcode')?GETPOST('zipcode'):$this->dao->MAIN_INFO_SOCIETE_ZIP),'zipcode',array('town','selectcountry_id','departement_id'),6);

            // Town
            $this->tpl['select_town'] = $formcompany->select_ziptown((GETPOST('town')?GETPOST('town'):$this->dao->MAIN_INFO_SOCIETE_TOWN),'town',array('zipcode','selectcountry_id','departement_id'));

            if ($user->admin) $this->tpl['info_admin'] = info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);


			// We define country_id, country_code and country_label
			$country = (! empty($this->dao->MAIN_INFO_SOCIETE_COUNTRY)?$this->dao->MAIN_INFO_SOCIETE_COUNTRY:$conf->global->MAIN_INFO_SOCIETE_COUNTRY);
			if (GETPOST('country_id'))
			{
				$country_id=GETPOST('country_id');
			}
			else if (! empty($country) && ! GETPOST('country_id'))
			{
				$tmp=explode(':',$country);
				$country_id=$tmp[0];
				if (! empty($tmp[1]))   // If $conf->global->MAIN_INFO_SOCIETE_PAYS is "id:code:label"
				{
					$country_code=$tmp[1];
					$country_label=$tmp[2];
				}
				else
				{
					$country_code=getCountry($country_id,2);
					$country_label=getCountry($country_id,0);
				}
			}
			else
			{
				$country_id=0;
				$country_code='';
				$country_label='';
			}

			$this->tpl['select_country'] = $form->select_country($country_id,'country_id');
			$this->tpl['select_state'] = $formcompany->select_state((GETPOST('departement_id')?GETPOST('departement_id'):$this->dao->MAIN_INFO_SOCIETE_STATE),($country_code?$country_code:$country_id),'departement_id');
			$this->tpl['select_currency'] = $form->selectcurrency((GETPOST('currency')?GETPOST('currency'):($this->dao->MAIN_MONNAIE?$this->dao->MAIN_MONNAIE:$conf->currency)),"currency");
			$this->tpl['select_language'] = $formadmin->select_language((GETPOST('main_lang_default')?GETPOST('main_lang_default'):($this->dao->MAIN_LANG_DEFAULT?$this->dao->MAIN_LANG_DEFAULT:$conf->global->MAIN_LANG_DEFAULT)),'main_lang_default',1);

			$this->tpl['select_entity'] = $this->select_entities($this->dao->options['referent'], 'referring_entity');
			$this->tpl['multiselect_shared_product'] = $this->multiselect_entities('product', $this->dao);
			$this->tpl['multiselect_shared_productprice'] = $this->multiselect_entities('productprice', $this->dao);
			$this->tpl['multiselect_shared_thirdparty'] = $this->multiselect_entities('societe', $this->dao);
			$this->tpl['multiselect_shared_category'] = $this->multiselect_entities('category', $this->dao);
			$this->tpl['multiselect_shared_agenda'] = $this->multiselect_entities('agenda', $this->dao);
			$this->tpl['multiselect_shared_bank_account'] = $this->multiselect_entities('bank_account', $this->dao);
		}
		else
		{
			$this->dao->getEntities();

			$this->tpl['entities']		= $this->dao->entities;
			$this->tpl['img_on'] 		= img_picto($langs->trans("Activated"),'on');
			$this->tpl['img_off'] 		= img_picto($langs->trans("Disabled"),'off');
			$this->tpl['img_modify'] 	= img_edit();
			$this->tpl['img_delete'] 	= img_delete();

			// Confirm delete
			if ($_GET["action"] == 'delete')
			{
				$this->tpl['action_delete'] = $form->formconfirm($_SERVER["PHP_SELF"]."?id=".GETPOST('id'),$langs->trans("DeleteAnEntity"),$langs->trans("ConfirmDeleteEntity"),"confirm_delete",'',0,1);
			}

			$this->template = 'entity_view.tpl.php';
		}
	}

	/**
	 *    Display the template
	 */
	function display()
	{
		global $conf, $langs;
		global $bc;

		include($this->template_dir.$this->template);
	}

	/**
	 * 	Set values of global conf for multicompany
	 *
	 * 	@param	Conf	$conf	Object conf
	 * 	@return void
	 */
	function setValues(&$conf)
	{
		$this->getInstanceDao();

		$this->dao->fetch($conf->entity);

		if (! empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED))
		{
			$this->sharings = $this->dao->options['sharings'];
			$this->referent = $this->dao->options['referent'];

			// Load shared elements
			$this->loadSharedElements();

			// Define output dir for others entities
			$this->setMultiOutputDir($conf);
		}
	}

	/**
	 * 	Get entity to use
	 *
	 * 	@param	string	$element	Current element
	 * 	@param	int		$shared		1=Return shared entities
	 * 	@return	int					Entity id to use
	 */
	function getEntity($element=false, $shared=false)
	{
		global $conf;

		$addzero = array('user', 'usergroup');
		if (in_array($element, $addzero))
		{
			$out = '0,';
			if (!empty($conf->multicompany->transverse_mode)) $out.= '1,';

			return $out.$conf->entity;
		}

		if (! empty($element) && ! empty($this->entities[$element]))
		{
			if (! empty($shared))
			{
				return $this->entities[$element];
			}
			else if (! empty($this->sharings['referent']))
			{
				if ($element == 'societe') return $this->sharings['referent'];
			}
		}

		return $conf->entity;
	}

	/**
	 * 	Set object documents directory to use
	 *
	 *	@param	Conf	$conf		Object Conf
	 * 	@return	void
	 */
	function setMultiOutputDir(&$conf)
	{
		if (! empty($this->entities))
		{
			foreach($this->entities as $element => $shares)
			{
				if (!empty($conf->$element->enabled) && isset($conf->$element->multidir_output) && isset($conf->$element->multidir_temp))
				{
					$elementpath=$element;
					if ($element == 'product') $elementpath='produit';
					if ($element == 'category') $elementpath='categorie';

					$entities = explode(",", $shares);
					foreach($entities as $entity)
					{
						if ($entity != $conf->entity)
						{
							$path = ($entity > 1 ? "/".$entity : '');
							$dir_output	= array($entity => DOL_DATA_ROOT.$path."/".$elementpath);
							$dir_temp	= array($entity => DOL_DATA_ROOT.$path."/".$elementpath."/temp");
						}
					}

					$conf->$element->multidir_output += $dir_output;
					$conf->$element->multidir_temp += $dir_temp;
				}
			}
		}
	}

	/**
	 *
	 */
	function printTopRightMenu($parameters=false)
	{
		echo $this->getTopRightMenu();
	}

	/**
	 *
	 */
	function getLoginPageOptions($parameters=false)
	{
		global $conf, $langs;

		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}

		$out=array();
		$select_entity='';
		$lastuser='';
		$lastentity=(! empty($conf->multicompany->force_entity)?$conf->multicompany->force_entity:$entity);

		// Entity cookie
		if (! empty($conf->global->MULTICOMPANY_COOKIE_ENABLED))
		{
			$prefix=dol_getprefix();
			$entityCookieName = 'DOLENTITYID_'.$prefix;
			if (isset($_COOKIE[$entityCookieName]))
			{
				include_once(DOL_DOCUMENT_ROOT . "/core/class/cookie.class.php");

				$cryptkey = (! empty($conf->file->cookie_cryptkey) ? $conf->file->cookie_cryptkey : '' );

				$entityCookie = new DolCookie($cryptkey);
				$cookieValue = $entityCookie->_getCookie($entityCookieName);
				list($lastuser, $lastentity) = explode('|', $cookieValue);
				$out['username'] = $lastuser;
			}
		}

		// Entity combobox
		$select_entity='';
		if (empty($conf->global->MULTICOMPANY_HIDE_LOGIN_COMBOBOX))
		{
			$select_entity = $this->select_entities($lastentity, 'entity', ' tabindex="3"', 1);

			$divformat = '<div class="entityBox"><strong><label for="entity">'.$langs->trans('Entity').'</label></strong>';
			$divformat.= $select_entity;
			$divformat.= '</div>';

			$out['options']['div'] = $divformat;

			$tableformat = '<tr><td valign="middle" class="loginfield nowrap"><strong><label for="entity">'.$langs->trans('Entity').'</label></strong></td>';
			$tableformat.= '<td valign="middle" class="nowrap">';
			$tableformat.= $select_entity;
			$tableformat.= '</td></tr>';

			$out['options']['table'] = $tableformat;
		}

		$this->results = $out;

		return 1;
	}

	/**
	 *
	 */
	function getPasswordForgottenPageOptions($parameters=false)
	{
		return $this->getLoginPageOptions($parameters);
	}

	/**
	 *  Load shared elements
	 *
	 *  @return void
	 */
	private function loadSharedElements()
	{
		global $conf;

		$this->getInstanceDao();

		if (! empty($this->sharings))
		{
			foreach($this->sharings as $element => $ids)
			{
				$moduleSharingEnabled = 'MULTICOMPANY_'.strtoupper($element).'_SHARING_ENABLED';
				$module = $element;

				$module = ($element == 'bank_account' ? 'banque' : $module);
				if ($element == 'productprice') {
					$module = 'product';
				} else if ($element == 'bank_account') {
					$module = 'banque';
				} else if ($element == 'product' && empty($conf->product->enabled) && !empty($conf->service->enabled)) {
					$module = 'service';
				}

				if (! empty($conf->$module->enabled) && ! empty($conf->global->$moduleSharingEnabled))
				{
					$entities=array();

					if (! empty($this->referent))
					{
						// Load configuration of referent entity
						$this->config = $this->dao->getEntityConfig($this->referent);
						$this->setConstant($conf, $element);
					}

					if (! empty($ids))
					{
						foreach ($ids as $id)
						{
							$ret=$this->dao->fetch($id);
							if ($ret > 0 && $this->dao->active)
							{
								$entities[] = $id;
							}
						}

						$this->entities[$element] = (! empty($entities) ? implode(",", $entities) : 0);
						$this->entities[$element].= ','.$conf->entity;
					}
				}
			}
		}
		//var_dump($this->entities);
	}

	/**
	 * 	Show entity info
	 */
	private function getTopRightMenu()
	{
		global $conf,$user,$langs;

		$langs->load('multicompany@multicompany');

		$out='';

		if (!empty($conf->multicompany->transverse_mode) || !empty($user->admin))
		{
			$form=new Form($this->db);

			$this->getInfo($conf->entity);

			$text = img_picto('', 'object_multicompany@multicompany','id="switchentity" class="entity linkobject"');

			$htmltext ='<u>'.$langs->trans("Entity").'</u>'."\n";
			$htmltext.='<br><b>'.$langs->trans("Label").'</b>: '.$this->label."\n";
			$htmltext.='<br><b>'.$langs->trans("Description").'</b>: '.$this->description."\n";

			$out.= $form->textwithtooltip('',$htmltext,2,1,$text,'login_block_elem',2);

			$out.= '<script type="text/javascript">
			$( "#switchentity" ).click(function() {
				$( "#dialog-switchentity" ).dialog({
					modal: true,
					width: 400,
					buttons: {
						\''.$langs->trans('Ok').'\': function() {
							choice=\'ok\';
							$.get( "'.dol_buildpath('/multicompany/ajaxswitchentity.php',1).'", {
								action: \'switchentity\',
								entity: $( "#entity" ).val()
							},
							function(content) {
								$( "#dialog-switchentity" ).dialog( "close" );
							});
						},
						\''.$langs->trans('Cancel').'\': function() {
							choice=\'ko\';
							$(this).dialog( "close" );
						}
					},
					close: function(event, ui) {
						if (choice == \'ok\') {
							location.href=\''.DOL_URL_ROOT.'\';
						}
					}
				});
			});
			</script>';

			$out.= '<div id="dialog-switchentity" class="hideobject" title="'.$langs->trans('SwitchToAnotherEntity').'">'."\n";
			$out.= '<br>'.$langs->trans('SelectAnEntity').': ';
			$out.= ajax_combobox('entity');
			$out.= $this->select_entities($conf->entity)."\n";
			$out.= '</div>'."\n";
		}

		$this->resprints = $out;
	}

	/**
	 *	Set parameters with referent entity
	 */
	function setConstant(&$conf, $element)
	{
		if (! empty($this->config))
		{
			$constants=array();

			if ($element == 'societe')
			{
				$constants = array(
						'SOCIETE_CODECLIENT_ADDON',
						'COMPANY_ELEPHANT_MASK_CUSTOMER',
						'COMPANY_ELEPHANT_MASK_SUPPLIER',
						'SOCIETE_IDPROF1_UNIQUE',
						'SOCIETE_IDPROF2_UNIQUE',
						'SOCIETE_IDPROF3_UNIQUE',
						'SOCIETE_IDPROF4_UNIQUE'
				);
			}

			if (! empty($constants))
			{
				foreach($constants as $name)
				{
					if (! empty($this->config[$name])) $conf->global->$name = $this->config[$name];
				}
			}
		}
	}

}
?>
