<?php
/* Copyright (C) 2011 Laurent Destailleur <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

global $db,$conf,$mysoc,$langs,$user;

$module=$conf->global->SOCIETE_CODECLIENT_ADDON;
if (! $module) dolibarr_error('',$langs->trans("ErrorModuleThirdPartyCodeInCompanyModuleNotDefined"));
if (substr($module, 0, 15) == 'mod_codeclient_' && substr($module, -3) == 'php')
{
    $module = substr($module, 0, dol_strlen($module)-4);
}
// Load object modCodeClient
$dirsociete=array_merge(array('/core/modules/societe/'),$conf->modules_parts['societe']);
foreach ($dirsociete as $dirroot)
{
	$res=dol_include_once($dirroot.$module.".php");
    if ($res) break;
}
require_once(DOL_DOCUMENT_ROOT ."/core/class/html.formcompany.class.php");
require_once(DOL_DOCUMENT_ROOT ."/core/class/html.formadmin.class.php");
$modCodeClient = new $module;

$form=new Form($GLOBALS['db']);
$formcompany=new FormCompany($GLOBALS['db']);
$formadmin=new FormAdmin($GLOBALS['db']);

$soc=$GLOBALS['object'];


$soc->client=1;

$soc->name=$_POST["nom"];
$soc->lastname=$_POST["nom"];
$soc->firstname=$_POST["firstname"];
$soc->particulier=0;
$soc->prefix_comm=$_POST["prefix_comm"];
$soc->client=$_POST["client"]?$_POST["client"]:$soc->client;
$soc->code_client=$_POST["code_client"];
$soc->fournisseur=$_POST["fournisseur"]?$_POST["fournisseur"]:$soc->fournisseur;
$soc->code_fournisseur=$_POST["code_fournisseur"];
$soc->adresse=$_POST["address"]; // TODO obsolete
$soc->address=$_POST["address"];
$soc->zip=$_POST["zipcode"];
$soc->town=$_POST["town"];
$soc->state_id=$_POST["departement_id"];
$soc->phone=$_POST["phone"];
$soc->fax=$_POST["fax"];
$soc->email=$_POST["email"];
$soc->url=$_POST["url"];
$soc->capital=$_POST["capital"];
$soc->barcode=$_POST["barcode"];
$soc->idprof1=$_POST["idprof1"];
$soc->idprof2=$_POST["idprof2"];
$soc->idprof3=$_POST["idprof3"];
$soc->idprof4=$_POST["idprof4"];
$soc->typent_id=$_POST["typent_id"];
$soc->effectif_id=$_POST["effectif_id"];

$soc->tva_assuj = $_POST["assujtva_value"];
$soc->status= $_POST["status"];

//Local Taxes
$soc->localtax1_assuj       = $_POST["localtax1assuj_value"];
$soc->localtax2_assuj       = $_POST["localtax2assuj_value"];

$soc->tva_intra=$_POST["tva_intra"];

$soc->commercial_id=$_POST["commercial_id"];
$soc->default_lang=$_POST["default_lang"];

// We set country_id, country_code and label for the selected country
$soc->country_id=$_POST["country_id"]?$_POST["country_id"]:$mysoc->country_id;
if ($soc->country_id)
{
    $sql = "SELECT code, libelle";
    $sql.= " FROM ".MAIN_DB_PREFIX."c_pays";
    $sql.= " WHERE rowid = ".$soc->country_id;
    $resql=$db->query($sql);
    if ($resql)
    {
        $obj = $db->fetch_object($resql);
    }
    else
    {
        dol_print_error($db);
    }
    $soc->country_code=$obj->code;
    $soc->country=$obj->libelle;
}
$soc->forme_juridique_code=$_POST['forme_juridique_code'];

?>

<!-- BEGIN PHP TEMPLATE -->
<?php
print_fiche_titre($langs->trans("NewPatient"));

dol_htmloutput_errors($GOBALS['error'],$GLOBALS['errors']);
?>

<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" name="formsoc">

<input type="hidden" name="canvas" value="<?php echo $GLOBALS['canvas'] ?>">
<input type="hidden" name="action" value="add">
<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>">
<input type="hidden" name="private" value="0">
<input type="hidden" name="status" value="1">
<input type="hidden" name="client" value="1">
<?php if ($modCodeClient->code_auto || $modCodeFournisseur->code_auto) print '<input type="hidden" name="code_auto" value="1">'; ?>

<table class="border" style="width: 100%;">

<tr>
	<td><span class="fieldrequired"><?php echo $langs->trans('PatientName'); ?></span></td>
	<td><input type="text" size="40" maxlength="60" name="nom" value="<?php echo $soc->name; ?>"></td>
    <td width="25%"><?php echo $langs->trans('PatientCode'); ?></td>
    <td width="25%">
<?php
        print '<table class="nobordernopadding"><tr><td>';
        $tmpcode=$soc->code_client;
        if ($modCodeClient->code_auto) $tmpcode=$modCodeClient->getNextValue($soc,0);
        print '<input type="text" name="code_client" size="16" value="'.$tmpcode.'" maxlength="15">';
        print '</td><td>';
        $s=$modCodeClient->getToolTip($langs,$soc,0);
        print $form->textwithpicto('',$s,1);
        print '</td></tr></table>';
?>
    </td>
</tr>

<tr>
	<td valign="top"><?php echo $langs->trans('Address'); ?></td>
	<td colspan="3"><textarea name="address" cols="40" rows="3"><?php echo $soc->address; ?></textarea></td>
</tr>

<?php
        // Zip / Town
        print '<tr><td>'.$langs->trans('Zip').'</td><td>';
        print $formcompany->select_ziptown($soc->zip,'zipcode',array('town','selectcountry_id','departement_id'),6);
        print '</td><td>'.$langs->trans('Town').'</td><td>';
        print $formcompany->select_ziptown($soc->town,'town',array('zipcode','selectcountry_id','departement_id'));
        print '</td></tr>';

        // Country
        print '<tr><td width="25%">'.$langs->trans('Country').'</td><td colspan="3">';
        print $form->select_country($soc->country_id,'country_id');
        if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
        print '</td></tr>';
?>

<tr>
	<td><?php echo $langs->trans('PhonePerso'); ?></td>
	<td><input type="text" name="phone" value="<?php echo $soc->phone; ?>"></td>
	<td><?php echo $langs->trans('PhoneMobile'); ?></td>
	<td><input type="text" name="fax" value="<?php echo $soc->fax; ?>"></td>
</tr>

<tr>
	<td><?php echo $langs->trans('EMail').($conf->global->SOCIETE_MAIL_REQUIRED?'*':''); ?></td>
	<td colspan="3"><input type="text" name="email" size="32" value="<?php echo $soc->email; ?>"></td>
</tr>

<?php
        print '<tr>';
        // Size
        $idprof=$langs->trans('Size');
        print '<td>'.$idprof.'</td><td>';
        print '<input type="text" name="idprof1" size="6" maxlength="6" value="'.$soc->idprof1.'">';
        print '</td>';
        // Weight
        $idprof=$langs->trans('Weight');
        print '<td>'.$idprof.'</td><td>';
        print '<input type="text" name="idprof2" size="6" maxlength="6" value="'.$soc->idprof2.'">';
        print '</td>';
        print '</tr>';
        print '<tr>';

        // Birthday
        $idprof=$langs->trans('DateToBirth');
        print '<td>'.$idprof.'</td><td colspan="3">';

        print '<input type="text" name="idprof3" size="18" maxlength="32" value="'.$soc->idprof3.'"> ('.$conf->format_date_short_java.')';
        //$conf->global->MAIN_POPUP_CALENDAR='none';
        //print $form->select_date(-1,'birthdate');
        print '</td>';
        print '</tr>';

        // Sexe
        print '<tr><td>'.$langs->trans("Gender").'</td><td colspan="3">'."\n";
        print $form->selectarray("typent_id",$formcompany->typent_array(0, "AND code in ('TE_UNKNOWN', 'TE_HOMME', 'TE_FEMME')"), $soc->typent_id);
        if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
        print '</td></tr>';

        // Legal Form
        print '<tr><td>'.$langs->trans('ActivityBranch').'</td>';
        print '<td>';
        if ($GLOBALS['mysoc']->country_id)
        {
            $formcompany->select_forme_juridique($soc->forme_juridique_code, $GLOBALS['mysoc']->country_code, "AND f.code > '100000'");
        }
        else
        {
            print $GLOBALS['countrynotdefined'];
        }
        print '</td>';
        print '<td>'.$langs->trans('Profession').'</td>';
        print '<td><input type="text" name="idprof4" size="32" value="'.$soc->idprof4.'"></td>';
        print '</tr>';

        print '<tr>';
        print '<td class="nowrap">'.$langs->trans('PatientVATIntra').'</td>';
        print '<td class="nowrap" colspan="3">';
        print '<input type="text" class="flat" name="tva_intra" size="16" maxlength="32" value="'.$soc->tva_intra.'">';
        print '</td></tr>';

        if ($conf->global->MAIN_MULTILANGS)
        {
            print '<tr><td>'.$langs->trans("DefaultLang").'</td><td colspan="3">'."\n";
            print $formadmin->select_language(($soc->default_lang?$soc->default_lang:$conf->global->MAIN_LANG_DEFAULT),'default_lang',0,0,1);
            print '</td>';
            print '</tr>';
        }

        if ($user->rights->societe->client->voir)
        {
            // Assign a Name
            print '<tr>';
            print '<td>'.$langs->trans("AllocateCommercial").'</td>';
            print '<td colspan="3">';
            $form->select_users(GETPOST('commercial_id')>0?GETPOST('commercial_id'):$user->id,'commercial_id',1);
            print '</td></tr>';
        }
?>
</table>
<br>

<div align="center">
	<input type="submit" class="button" value="<?php echo $langs->trans('AddPatient'); ?>">
</div>

</form>

<!-- END PHP TEMPLATE -->