<?php
/* Copyright (C) 2001-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Brian Fraval         <brian@fraval.org>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2008	   Patrick Raguin       <patrick.raguin@auguria.net>
 * Copyright (C) 2010-2013 Juanjo Menent        <jmenent@2byte.es>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/societe/soc.php
 *  \ingroup    societe
 *  \brief      Third party card page
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
if (! empty($conf->adherent->enabled)) require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';

$langs->load("companies");
$langs->load("commercial");
$langs->load("bills");
$langs->load("banks");
$langs->load("users");
$langs->load("other");
if (! empty($conf->notification->enabled)) $langs->load("mails");

$mesg=''; $error=0; $errors=array();

$action		= (GETPOST('action') ? GETPOST('action') : 'view');
$confirm	= GETPOST('confirm');
$socid		= GETPOST('socid','int');
if ($user->societe_id) $socid=$user->societe_id;
if (empty($socid) && $action == 'view') $action='create';

$object = new Societe($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
$object->getCanvas($socid);
$canvas = $object->canvas?$object->canvas:GETPOST("canvas");
$objcanvas='';
if (! empty($canvas))
{
    require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
    $objcanvas = new Canvas($db, $action);
    $objcanvas->getCanvas('thirdparty', 'card', $canvas);
}

// Security check
$result = restrictedArea($user, 'societe', $socid, '&societe', '', 'fk_soc', 'rowid', $objcanvas);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('thirdpartycard'));


/*
 * Actions
 */

$parameters=array('id'=>$socid, 'objcanvas'=>$objcanvas);
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
$error=$hookmanager->error; $errors=array_merge($errors, (array) $hookmanager->errors);

if (empty($reshook))
{
    if (GETPOST('getcustomercode'))
    {
        // We defined value code_client
        $_POST["code_client"]="Acompleter";
    }

    if (GETPOST('getsuppliercode'))
    {
        // We defined value code_fournisseur
        $_POST["code_fournisseur"]="Acompleter";
    }

    // Add new third party
    if ((! GETPOST('getcustomercode') && ! GETPOST('getsuppliercode'))
    && ($action == 'add' || $action == 'update') && $user->rights->societe->creer)
    {
        require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

        if ($action == 'update')
        {
        	$ret=$object->fetch($socid);
        	$object->oldcopy=dol_clone($object);
        }
		else $object->canvas=$canvas;

        if (GETPOST("private") == 1)
        {
            $object->particulier       = GETPOST("private");

            $object->name              = dolGetFirstLastname(GETPOST('firstname'),GETPOST('nom')?GETPOST('nom'):GETPOST('name'));
            $object->civilite_id       = GETPOST('civilite_id')?GETPOST('civilite_id'):GETPOST('civility_id');
            $object->civility_id       = GETPOST('civilite_id')?GETPOST('civilite_id'):GETPOST('civility_id');
            // Add non official properties
            $object->name_bis          = GETPOST('name')?GETPOST('name'):GETPOST('nom');
            $object->firstname         = GETPOST('firstname');
        }
        else
		{
            $object->name              = GETPOST('name')?GETPOST('name'):GETPOST('nom');
        }
        $object->address               = GETPOST('address');
        $object->zip                   = GETPOST('zipcode');
        $object->town                  = GETPOST('town');
        $object->country_id            = GETPOST('country_id');
        $object->state_id              = GETPOST('state_id');
        $object->skype                 = GETPOST('skype');
        $object->phone                 = GETPOST('phone');
        $object->fax                   = GETPOST('fax');
        $object->email                 = GETPOST('email');
        $object->url                   = GETPOST('url');
        $object->idprof1               = GETPOST('idprof1');
        $object->idprof2               = GETPOST('idprof2');
        $object->idprof3               = GETPOST('idprof3');
        $object->idprof4               = GETPOST('idprof4');
        $object->idprof5               = GETPOST('idprof5');
        $object->idprof6               = GETPOST('idprof6');
        $object->prefix_comm           = GETPOST('prefix_comm');
        $object->code_client           = GETPOST('code_client');
        $object->code_fournisseur      = GETPOST('code_fournisseur');
        $object->capital               = GETPOST('capital');
        $object->barcode               = GETPOST('barcode');

        $object->tva_intra             = GETPOST('tva_intra');
        $object->tva_assuj             = GETPOST('assujtva_value');
        $object->status                = GETPOST('status');

        // Local Taxes
        $object->localtax1_assuj       = GETPOST('localtax1assuj_value');
        $object->localtax2_assuj       = GETPOST('localtax2assuj_value');

        $object->forme_juridique_code  = GETPOST('forme_juridique_code');
        $object->effectif_id           = GETPOST('effectif_id');
        if (GETPOST("private") == 1)
        {
            $object->typent_id         = dol_getIdFromCode($db,'TE_PRIVATE','c_typent');
        }
        else
        {
            $object->typent_id         = GETPOST('typent_id');
        }

        $object->client                = GETPOST('client');
        $object->fournisseur           = GETPOST('fournisseur');

        $object->commercial_id         = GETPOST('commercial_id');
        $object->default_lang          = GETPOST('default_lang');

        // Fill array 'array_options' with data from add form
        $ret = $extrafields->setOptionalsFromPost($extralabels,$object);

        if (GETPOST('deletephoto')) $object->logo = '';
        else if (! empty($_FILES['photo']['name'])) $object->logo = dol_sanitizeFileName($_FILES['photo']['name']);

        // Check parameters
        if (empty($_POST["cancel"]))
        {
            if (! empty($object->email) && ! isValidEMail($object->email))
            {
                $langs->load("errors");
                $error++; $errors[] = $langs->trans("ErrorBadEMail",$object->email);
                $action = ($action=='add'?'create':'edit');
            }
            if (! empty($object->url) && ! isValidUrl($object->url))
            {
                $langs->load("errors");
                $error++; $errors[] = $langs->trans("ErrorBadUrl",$object->url);
                $action = ($action=='add'?'create':'edit');
            }
            if ($object->fournisseur && ! $conf->fournisseur->enabled)
            {
                $langs->load("errors");
                $error++; $errors[] = $langs->trans("ErrorSupplierModuleNotEnabled");
                $action = ($action=='add'?'create':'edit');
            }

            // We set country_id, country_code and country for the selected country
            $object->country_id=GETPOST('country_id')!=''?GETPOST('country_id'):$mysoc->country_id;
            if ($object->country_id)
            {
            	$tmparray=getCountry($object->country_id,'all');
            	$object->country_code=$tmparray['code'];
            	$object->country=$tmparray['label'];
            }

            // Check for duplicate or mandatory prof id
        	for ($i = 1; $i < 5; $i++)
        	{
        	    $slabel="idprof".$i;
    			$_POST[$slabel]=trim($_POST[$slabel]);
        	    $vallabel=$_POST[$slabel];
        		if ($vallabel && $object->id_prof_verifiable($i))
				{
					if($object->id_prof_exists($i,$vallabel,$object->id))
					{
						$langs->load("errors");
                		$error++; $errors[] = $langs->transcountry('ProfId'.$i, $object->country_code)." ".$langs->trans("ErrorProdIdAlreadyExist", $vallabel);
                		$action = (($action=='add'||$action=='create')?'create':'edit');
					}
				}

				$idprof_mandatory ='SOCIETE_IDPROF'.($i).'_MANDATORY';
				if (! $vallabel && ! empty($conf->global->$idprof_mandatory))
				{
					$langs->load("errors");
					$error++;
					$errors[] = $langs->trans("ErrorProdIdIsMandatory", $langs->transcountry('ProfId'.$i, $object->country_code));
					$action = (($action=='add'||$action=='create')?'create':'edit');
				}
			}
        }

        if (! $error)
        {
            if ($action == 'add')
            {
                $db->begin();

                if (empty($object->client))      $object->code_client='';
                if (empty($object->fournisseur)) $object->code_fournisseur='';

                $result = $object->create($user);
                if ($result >= 0)
                {
                    if ($object->particulier)
                    {
                        dol_syslog("This thirdparty is a personal people",LOG_DEBUG);
                        $contact=new Contact($db);

     					$contact->civilite_id		= empty($object->civilite_id)?$object->civility_id:$object->civilite_id;
     					$contact->civility_id		= empty($object->civilite_id)?$object->civility_id:$object->civilite_id;
     					$contact->name				= $object->name_bis;
                        $contact->firstname			= $object->firstname;
                        $contact->address			= $object->address;
                        $contact->zip				= $object->zip;
                        $contact->town				= $object->town;
                        $contact->state_id      	= $object->state_id;
                        $contact->country_id		= $object->country_id;

                        $contact->socid				= $object->id;
                        $contact->fk_soc			= $object->id;

                        $contact->status			= 1;
                        $contact->email				= $object->email;
						$contact->phone_pro			= $object->phone;
						$contact->fax				= $object->fax;
                        $contact->priv				= 0;

                        $result=$contact->create($user);
                        if (! $result >= 0)
                        {
                            $error=$contact->error; $errors=$contact->errors;
                        }
                    }

                    // Logo/Photo save
                    $dir     = $conf->societe->multidir_output[$conf->entity]."/".$object->id."/logos/";
                    $file_OK = is_uploaded_file($_FILES['photo']['tmp_name']);
                    if ($file_OK)
                    {
                        if (image_format_supported($_FILES['photo']['name']))
                        {
                            dol_mkdir($dir);

                            if (@is_dir($dir))
                            {
                                $newfile=$dir.'/'.dol_sanitizeFileName($_FILES['photo']['name']);
                                $result = dol_move_uploaded_file($_FILES['photo']['tmp_name'], $newfile, 1);

                                if (! $result > 0)
                                {
                                    $errors[] = "ErrorFailedToSaveFile";
                                }
                                else
                                {
                                    // Create small thumbs for company (Ratio is near 16/9)
                                    // Used on logon for example
                                    $imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);

                                    // Create mini thumbs for company (Ratio is near 16/9)
                                    // Used on menu or for setup page for example
                                    $imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
                                }
                            }
                        }
                    }
                    else
	              {
						switch($_FILES['photo']['error'])
						{
						    case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
						    case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
						      $errors[] = "ErrorFileSizeTooLarge";
						      break;
	      					case 3: //uploaded file was only partially uploaded
						      $errors[] = "ErrorFilePartiallyUploaded";
						      break;
						}
	                }
                    // Gestion du logo de la société
                }
                else
				{
                    $error=$object->error; $errors=$object->errors;
                }

                if ($result >= 0)
                {
                    $db->commit();

                    $url=$_SERVER["PHP_SELF"]."?socid=".$object->id;
                    /*if (($object->client == 1 || $object->client == 3) && empty($conf->global->SOCIETE_DISABLE_CUSTOMERS)) $url=DOL_URL_ROOT."/comm/fiche.php?socid=".$object->id;
                    else if ($object->fournisseur == 1) $url=DOL_URL_ROOT."/fourn/fiche.php?socid=".$object->id;*/
                    header("Location: ".$url);
                    exit;
                }
                else
                {
                    $db->rollback();
                    $action='create';
                }
            }

            if ($action == 'update')
            {
                if ($_POST["cancel"])
                {
                    header("Location: ".$_SERVER["PHP_SELF"]."?socid=".$socid);
                    exit;
                }

                // To not set code if third party is not concerned. But if it had values, we keep them.
                if (empty($object->client) && empty($object->oldcopy->code_client))          $object->code_client='';
                if (empty($object->fournisseur)&& empty($object->oldcopy->code_fournisseur)) $object->code_fournisseur='';
                //var_dump($object);exit;

                $result = $object->update($socid, $user, 1, $object->oldcopy->codeclient_modifiable(), $object->oldcopy->codefournisseur_modifiable(), 'update', 0);
                if ($result <=  0)
                {
                    $error = $object->error; $errors = $object->errors;
                }

                // Logo/Photo save
                $dir     = $conf->societe->multidir_output[$object->entity]."/".$object->id."/logos";
                $file_OK = is_uploaded_file($_FILES['photo']['tmp_name']);
                if ($file_OK)
                {
                    if (GETPOST('deletephoto'))
                    {
                        $fileimg=$dir.'/'.$object->logo;
                        $dirthumbs=$dir.'/thumbs';
                        dol_delete_file($fileimg);
                        dol_delete_dir_recursive($dirthumbs);
                    }

                    if (image_format_supported($_FILES['photo']['name']) > 0)
                    {
                        dol_mkdir($dir);

                        if (@is_dir($dir))
                        {
                            $newfile=$dir.'/'.dol_sanitizeFileName($_FILES['photo']['name']);
                            $result = dol_move_uploaded_file($_FILES['photo']['tmp_name'], $newfile, 1);

                            if (! $result > 0)
                            {
                                $errors[] = "ErrorFailedToSaveFile";
                            }
                            else
                            {
                                // Create small thumbs for company (Ratio is near 16/9)
                                // Used on logon for example
                                $imgThumbSmall = vignette($newfile, $maxwidthsmall, $maxheightsmall, '_small', $quality);

                                // Create mini thumbs for company (Ratio is near 16/9)
                                // Used on menu or for setup page for example
                                $imgThumbMini = vignette($newfile, $maxwidthmini, $maxheightmini, '_mini', $quality);
                            }
                        }
                    }
                    else
                    {
                        $errors[] = "ErrorBadImageFormat";
                    }
                }
                            else
              {
					switch($_FILES['photo']['error'])
					{
					    case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
					    case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					      $errors[] = "ErrorFileSizeTooLarge";
					      break;
      					case 3: //uploaded file was only partially uploaded
					      $errors[] = "ErrorFilePartiallyUploaded";
					      break;
					}
                }
                // Gestion du logo de la société


                // Update linked member
                if (! $error && $object->fk_soc > 0)
                {

                	$sql = "UPDATE ".MAIN_DB_PREFIX."adherent";
                	$sql.= " SET fk_soc = NULL WHERE fk_soc = " . $id;
                	dol_syslog(get_class($this)."::delete sql=".$sql, LOG_DEBUG);
                	if (! $this->db->query($sql))
                	{
                		$error++;
                		$this->error .= $this->db->lasterror();
                		dol_syslog(get_class($this)."::delete erreur -1 ".$this->error, LOG_ERR);
                	}
                }

                if (! $error && ! count($errors))
                {

                    header("Location: ".$_SERVER["PHP_SELF"]."?socid=".$socid);
                    exit;
                }
                else
                {
                    $object->id = $socid;
                    $action= "edit";
                }
            }
        }
    }

    // Delete third party
    if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->societe->supprimer)
    {
        $object->fetch($socid);
        $result = $object->delete($socid);

        if ($result > 0)
        {
            header("Location: ".DOL_URL_ROOT."/societe/societe.php?delsoc=".urlencode($object->name));
            exit;
        }
        else
        {
            $langs->load("errors");
            $error=$langs->trans($object->error); $errors = $object->errors;
            $action='';
        }
    }

    // Set parent company
    if ($action == 'set_thirdparty' && $user->rights->societe->creer)
    {
    	$result = $object->set_parent(GETPOST('editparentcompany','int'));
    }


    // Actions to send emails
    $id=$socid;
    $actiontypecode='AC_OTH_AUTO';
    $paramname='socid';
    include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';


	/*
     * Generate document
     */
    if ($action == 'builddoc')  // En get ou en post
    {
        if (is_numeric(GETPOST('model')))
        {
            $error=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Model"));
        }
        else
        {
            require_once DOL_DOCUMENT_ROOT.'/core/modules/societe/modules_societe.class.php';

            $object->fetch($socid);

            // Define output language
            $outputlangs = $langs;
            $newlang='';
            if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
            if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$fac->client->default_lang;
            if (! empty($newlang))
            {
                $outputlangs = new Translate("",$conf);
                $outputlangs->setDefaultLang($newlang);
            }
            $result=thirdparty_doc_create($db, $object, '', GETPOST('model','alpha'), $outputlangs);
            if ($result <= 0)
            {
                dol_print_error($db,$result);
                exit;
            }
        }
    }

    // Remove file in doc form
    else if ($action == 'remove_file')
    {
    	if ($object->fetch($socid))
    	{
    		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

    		$langs->load("other");
    		$upload_dir = $conf->societe->dir_output;
    		$file = $upload_dir . '/' . GETPOST('file');
    		$ret=dol_delete_file($file,0,0,0,$object);
    		if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
    		else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
    	}
    }
}



/*
 *  View
 */

$help_url='EN:Module_Third_Parties|FR:Module_Tiers|ES:Empresas';
llxHeader('',$langs->trans("ThirdParty"),$help_url);

$form = new Form($db);
$formfile = new FormFile($db);
$formadmin = new FormAdmin($db);
$formcompany = new FormCompany($db);

$countrynotdefined=$langs->trans("ErrorSetACountryFirst").' ('.$langs->trans("SeeAbove").')';


if (is_object($objcanvas) && $objcanvas->displayCanvasExists($action))
{
    // -----------------------------------------
    // When used with CANVAS
    // -----------------------------------------
    if (empty($object->error) && $socid)
 	{
	     $object = new Societe($db);
	     $result=$object->fetch($socid);
	     if ($result <= 0) dol_print_error('',$object->error);
 	}
   	$objcanvas->assign_values($action, $object->id, $object->ref);	// Set value for templates
    $objcanvas->display_canvas($action);		// Show template
}
else
{
	dol_print_error('','Error this page must be called for canvas pages only');
}


// End of page
llxFooter();
$db->close();
?>
