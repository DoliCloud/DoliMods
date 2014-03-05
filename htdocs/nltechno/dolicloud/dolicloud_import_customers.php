<?php
/* Copyright (C) 2008-2013	Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	    \file       htdocs/nltechno/dolicloud/dolicloud_import_customers.php
 *      \ingroup    nltechno
 *      \brief      Page list payment
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');
dol_include_once('/nltechno/class/dolicloudcustomer.class.php');

if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("nltechno@nltechno");

$def = array();
$action=GETPOST('action', 'alpha');
$confirm=GETPOST('confirm', 'alpha');
$actionsave=GETPOST('save', 'alpha');
$file=GETPOST('file');
$line=GETPOST('line');

$modules = array();
$arraystatus=Dolicloudcustomer::$listOfStatus;
$upload_dir = $conf->nltechno->dir_temp.'/dolicloud';

/*
 * Actions
 */

if (preg_match('/set_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_set_const($db, $code, 1, 'chaine', 0, '', 0) > 0)
	{
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

if (preg_match('/del_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_del_const($db, $code, 0) > 0)
	{
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

// Send file
if (GETPOST('sendit') && ! empty($conf->global->MAIN_UPLOAD_DOC))
{
	$error=0;

	dol_mkdir($dir);

	if (dol_mkdir($upload_dir) >= 0)
	{
		$resupload=dol_move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_dir."/".$_FILES['userfile']['name'], 1, 0, $_FILES['userfile']['error']);
		if (is_numeric($resupload) && $resupload > 0)
		{
			setEventMessage($langs->trans("FileTransferComplete"),'mesgs');
			$showmessage=1;
		}
		else
		{
			$langs->load("errors");
			if ($resupload < 0)	// Unknown error
			{
				setEventMessage($langs->trans("ErrorFileNotUploaded"),'mesgs');
			}
			else if (preg_match('/ErrorFileIsInfectedWithAVirus/',$resupload))	// Files infected by a virus
			{
				setEventMessage($langs->trans("ErrorFileIsInfectedWithAVirus"),'mesgs');
			}
			else	// Known error
			{
				setEventMessage($langs->trans($resupload),'errors');
			}
		}
	}

	if ($error)
	{
		setEventMessage($langs->trans("ErrorFileNotUploaded"),'errors');
	}
}

// Delete file
if ($action == 'remove_file')
{
	$file = $conf->nltechno->dir_temp . "/" . $file;	// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).

	$ret=dol_delete_file($file);
	if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('file')));
	else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('file')), 'errors');
	header('Location: '.$_SERVER["PHP_SELF"]);
	exit;
}

if ($action == 'import' || $action == 'create')
{
	$importresult='';

	$handle=fopen($conf->nltechno->dir_temp.'/'.$file, 'r');
	if ($handle)
	{
		$importresult.='Import file '.$conf->nltechno->dir_temp.'/'.$file.'<br>';

		$listofid=array();
		$i=0; $j=0;
		$dolicloudcustomer=new Dolicloudcustomer($db);
		while(($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			// data[0] = organization
			// data[1] = email
			// data[2] = registered date
			// data[3] = acquired date
			// data[4] = plan
			// data[5] = total_invoiced
			// data[6] = total_payed
			// data[7] = customer status
			// data[8] = payment status

			$i++;
			$organization=$data[0];
			$email=$data[1];
			if (preg_match('/^([0-9][0-9])\/([0-9][0-9])\/([0-9][0-9])$/',$data[2],$reg))
			{
				$tmp='20'.$reg[3].'-'.$reg[2].'-'.$reg[1];
				$date_acquired=dol_stringtotime($tmp);
			}
			else $date_acquired=dol_stringtotime($data[2]);
			$plan=$data[4];
			$total_invoiced=$data[5];
			$total_payed=$data[6];
			$status=$data[7];
			$statuspayment=$data[8];
			if ($status == 'ACTIVE' && $statuspayment == 'FAILURE' && $total_payed < $total_invoiced) $status='ACTIVE_PAYMENT_ERROR';
			if ($status == 'CLOSURE_REQUESTED') $status='CLOSE_QUEUED';		// TODO Use CLOSURE_REQUESTED into database
			if ($status == 'CLOSED') $status='UNDEPLOYED';
			if ($organization == 'Organization') continue;	// Discard first line
			if (empty($total_invoiced)) continue;

			$j++;
			$importresult.=str_pad($j,4,'0',STR_PAD_LEFT).' - Line '.str_pad($i,4,'0',STR_PAD_LEFT).' - ';

			$result=$dolicloudcustomer->fetch('','',$organization);
			if ($result <= 0)
			{
				if ($status != 'UNDEPLOYED' && $status != 'SUSPENDED') $importresult.='<span style="color: red">';
				else $importresult.='<span style="color: #DAA520">';
				$importresult.='Organization "'.$organization.'" not found. ';
				$importresult.='</span>';
				$importresult.='We need to set it to status '.$status.'. ';
				//$importresult.='<a href="'.$_SERVER["PHP_SELF"].'?action=create&line='.$i.'&file='.urlencode($file).'">Click to create</a>.<br>';
				$importresult.='<a target="_blank" href="'.dol_buildpath('/nltechno/dolicloud/dolicloud_card.php',1).'?';
				$importresult.='action=create&plan='.urlencode($plan).'&organization='.urlencode($organization).'&email='.urlencode($email);
				$importresult.='&date_registrationmonth='.dol_print_date($date_acquired,'%m');
				$importresult.='&date_registrationday='.dol_print_date($date_acquired,'%d');
				$importresult.='&date_registrationyear='.dol_print_date($date_acquired,'%Y');
				$importresult.='">Click to create</a>.<br>';
			}
			else
			{
				$listofid[$dolicloudcustomer->id]=$dolicloudcustomer->organization;

				$importresult.='Organization "'.$organization.'" found'.($result>1 ? ' ('.$result.' instances)' : '').'.';

				$partner=(preg_match('/2Byte/i',$plan)?'2Byte':'');		// TODO Not complete

				//var_dump($organization.', '.$dolicloudcustomer->status.' - '.$status.', '.$dolicloudcustomer->plan.' - '.$plan.', '.$dolicloudcustomer->partner.' - '.$partner.', '.$dolicloudcustomer->date_registration.' - '.$date_acquired);
				$change=false;
				if ($dolicloudcustomer->plan!=$plan) $change=true;
				if ($dolicloudcustomer->partner!=$partner) $change=true;
				if ($dolicloudcustomer->date_registration!=$date_acquired) $change=true;
				if ($dolicloudcustomer->status!=$status) $change=true;
				if (! in_array($status,$arraystatus))
				{
					$importresult.=' <span style="color: red">Status '.$status.' is not recognized</span>.<br>';
				}
				else if ($change)
				{
					$dolicloudcustomer->plan=$plan;
					$dolicloudcustomer->partner=$partner;
					$dolicloudcustomer->date_registration=$date_acquired;
					$dolicloudcustomer->status=$status;

					$result=$dolicloudcustomer->update($user,1);
					$importresult.=' <span style="color: blue">We update record</span>. Status after is '.$dolicloudcustomer->status.'<br>';
				}
				else
				{
					$importresult.=' No need to update. Current status is '.($dolicloudcustomer->status=='ACTIVE_PAYMENT_ERROR'?'<span style="color: #DAA520">'.$dolicloudcustomer->status.'</span>':$dolicloudcustomer->status).'<br>';
				}
			}

			$importresult.="\n";
		}
		fclose($handle);

		// Test entries not into file
		if (count($listofid) > 0)
		{
			$sql=" SELECT c.organization, c.instance, c.status FROM ".MAIN_DB_PREFIX."dolicloud_customers as c";
			$sql.=" WHERE c.status = 'ACTIVE' AND c.rowid NOT IN (".join(',',array_keys($listofid)).")";
			$resql=$db->query($sql);
			if ($resql)
			{
				$num=$db->num_rows($resql);
				$i=0;
				while($i < $num)
				{
					$obj=$db->fetch_object($resql);
					$result=$dolicloudcustomer->fetch('','',$obj->organization);
					if ($result > 0)
					{
						if ($obj->status != $dolicloudcustomer->status)
						{
							$importresult.='<span style="color: red">Warning: Organization active into database and not into file: '.$obj->organization.' - '.$obj->instance;
							$importresult.='. There is a record for instance '.$dolicloudcustomer->instance.' that match same organization name but with a status '.$dolicloudcustomer->status.' different of '.$obj->status.'.';
							$importresult.=' May be there is also other instances for this customer (found '.$result.' instances into database for this organization)';
							$importresult.='</span><br>'."\n";
						}
						else
						{
							$importresult.='<span style="color: #DAA520">Warning: Organization active into database and not into file: '.$obj->organization.' - '.$obj->instance;
							$importresult.='. But found a record for instance '.$dolicloudcustomer->instance.' that match same organization name with same status.';
							$importresult.=' May be there is other instances for this customer (found '.$result.' instances into database for this organization)';
							$importresult.='</span><br>'."\n";
						}
					}
					$i++;
				}
			}
			else dol_print_error($db);
		}
	}
	else dol_print_error('','Failed to open file '.$conf->nltechno->dir_temp.'/'.$file);
}




/*
 * View
 */

$form=new Form($db);
$formfile=new FormFile($db);

llxHeader('','DoliCloud',$linktohelp);

print_fiche_titre($langs->trans("List customers"))."\n";
print '<br>';

$formfile->form_attach_new_file($_SERVER['PHP_SELF'], $langs->trans("ImportFileCustomers"), 0, 0, 1, 50, '', '', false, '', 0);

print_fiche_titre($langs->trans("Exclude customer"),'','')."<br>\n";
print '<form name="exclude" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<textarea name="excludelist" cols="120">'.$conf->global->NLTECHNO_DOLICLOUD_EXCLUDE_CUSTOMERS.'</textarea><br>';
print '<input type="submit" class="button" name="'.$langs->trans("Save").'">';
print '</form><br><br>'."\n";

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

$morehtml=' &nbsp; <a href="'.$_SERVER["PHP_SELF"].'?module=nltechno_temp&action=import&file=__FILENAMEURLENCODED__">'.$langs->trans("Import").'</a>';
print $formfile->showdocuments('nltechno_temp', 'dolicloud', $conf->nltechno->dir_temp.'/dolicloud', $_SERVER["PHP_SELF"], 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $morehtml);

if ($importresult)
{
	print '<br>'.$langs->trans("Result").':<br>'."\n";
	print $importresult;
}

// Footer
llxFooter();
// Close database handler
$db->close();
?>
