<?php
/* Copyright (C) 2011 Laurent Destailleur <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *
 * $Id: card_create.tpl.php,v 1.2 2011/05/29 17:36:59 eldy Exp $
 */

global $db,$conf,$mysoc,$langs,$user;

// Load object modCodeTiers
$module=$conf->global->SOCIETE_CODECLIENT_ADDON;
if (! $module) dolibarr_error('',$langs->trans("ErrorModuleThirdPartyCodeInCompanyModuleNotDefined"));
if (substr($module, 0, 15) == 'mod_codeclient_' && substr($module, -3) == 'php')
{
    $module = substr($module, 0, dol_strlen($module)-4);
}
require_once(DOL_DOCUMENT_ROOT ."/core/class/html.formcompany.class.php");
require_once(DOL_DOCUMENT_ROOT ."/core/class/html.formadmin.class.php");
require_once(DOL_DOCUMENT_ROOT ."/includes/modules/societe/".$module.".php");
$modCodeClient = new $module;

$form=new Form($GLOBALS['db']);
$formcompany=new FormCompany($GLOBALS['db']);
$formadmin=new FormAdmin($GLOBALS['db']);

$soc=$GLOBALS['soc'];


/*
 * Company Fact creation mode
 */
//if ($_GET["type"]=='cp') { $soc->client=3; }
if (GETPOST("type")!='f') $soc->client=3;
if (GETPOST("type")=='c')  { $soc->client=1; }
if (GETPOST("type")=='p')  { $soc->client=2; }
if ($conf->fournisseur->enabled && (GETPOST("type")=='f' || GETPOST("type")==''))  { $soc->fournisseur=1; }

$soc->nom=$_POST["nom"];
$soc->prenom=$_POST["prenom"];
$soc->particulier=0;
$soc->prefix_comm=$_POST["prefix_comm"];
$soc->client=$_POST["client"]?$_POST["client"]:$soc->client;
$soc->code_client=$_POST["code_client"];
$soc->fournisseur=$_POST["fournisseur"]?$_POST["fournisseur"]:$soc->fournisseur;
$soc->code_fournisseur=$_POST["code_fournisseur"];
$soc->adresse=$_POST["adresse"]; // TODO obsolete
$soc->address=$_POST["adresse"];
$soc->cp=$_POST["zipcode"];
$soc->ville=$_POST["town"];
$soc->departement_id=$_POST["departement_id"];
$soc->tel=$_POST["tel"];
$soc->fax=$_POST["fax"];
$soc->email=$_POST["email"];
$soc->url=$_POST["url"];
$soc->capital=$_POST["capital"];
$soc->gencod=$_POST["gencod"];
$soc->siren=$_POST["idprof1"];
$soc->siret=$_POST["idprof2"];
$soc->ape=$_POST["idprof3"];
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

// We set pays_id, pays_code and label for the selected country
$soc->pays_id=$_POST["pays_id"]?$_POST["pays_id"]:$mysoc->pays_id;
if ($soc->pays_id)
{
    $sql = "SELECT code, libelle";
    $sql.= " FROM ".MAIN_DB_PREFIX."c_pays";
    $sql.= " WHERE rowid = ".$soc->pays_id;
    $resql=$db->query($sql);
    if ($resql)
    {
        $obj = $db->fetch_object($resql);
    }
    else
    {
        dol_print_error($db);
    }
    $soc->pays_code=$obj->code;
    $soc->pays=$obj->libelle;
}
$soc->forme_juridique_code=$_POST['forme_juridique_code'];

?>

<!-- BEGIN PHP TEMPLATE -->
<?php
print_fiche_titre($langs->trans("NewCompany"));

dol_htmloutput_errors($soc->error,$soc->errors);
?>

<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" name="formsoc">

<input type="hidden" name="canvas" value="<?php echo $GLOBALS['canvas'] ?>">
<input type="hidden" name="action" value="add">
<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>">
<input type="hidden" name="private" value="0">
<input type="hidden" name="status" value="1">
<input type="hidden" name="client" value="1">
<?php if ($modCodeClient->code_auto || $modCodeFournisseur->code_auto) print '<input type="hidden" name="code_auto" value="1">'; ?>

<table class="border" width="100%">

<tr>
	<td><span class="fieldrequired"><?php echo $langs->trans('Name'); ?></span></td>
	<td><input type="text" size="30" maxlength="60" name="nom" value="<?php echo $soc->nom; ?>"></td>
    <td width="25%"><?php echo $langs->trans('CustomerCode'); ?></td>
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
	<td colspan="3"><textarea name="adresse" cols="40" rows="3"><?php echo $soc->address; ?></textarea></td>
</tr>

<?php
        // Zip / Town
        print '<tr><td>'.$langs->trans('Zip').'</td><td>';
        print $formcompany->select_ziptown($soc->cp,'zipcode',array('town','selectpays_id','departement_id'),6);
        print '</td><td>'.$langs->trans('Town').'</td><td>';
        print $formcompany->select_ziptown($soc->ville,'town',array('zipcode','selectpays_id','departement_id'));
        print '</td></tr>';

        // Country
        print '<tr><td width="25%">'.$langs->trans('Country').'</td><td colspan="3">';
        $form->select_pays($soc->pays_id,'pays_id');
        if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
        print '</td></tr>';
?>

<tr>
	<td><?php echo $langs->trans('PhonePerso'); ?></td>
	<td><input type="text" name="tel" value="<?php echo $soc->tel; ?>"></td>
	<td><?php echo $langs->trans('PhoneMobile'); ?></td>
	<td><input type="text" name="fax" value="<?php echo $soc->fax; ?>"></td>
</tr>

<tr>
	<td><?php echo $langs->trans('EMail').($conf->global->SOCIETE_MAIL_REQUIRED?'*':''); ?></td>
	<td colspan="3"><input type="text" name="email" size="32" value="<?php echo $soc->email; ?>"></td>
</tr>

<?php
        print '<tr>';
        // IdProf1 (SIREN for France)
        $idprof=$langs->transcountry('ProfId1',$soc->pays_code);
            print '<td>'.$idprof.'</td><td>';
            print '<input type="text" name="idprof1" size="6" maxlength="6" value="'.$soc->siren.'">';
            print '</td>';
        // IdProf2 (SIRET for France)
        $idprof=$langs->transcountry('ProfId2',$soc->pays_code);
            print '<td>'.$idprof.'</td><td>';
            print '<input type="text" name="idprof2" size="6" maxlength="6" value="'.$soc->siret.'">';
            print '</td>';
        print '</tr>';
        print '<tr>';
        // IdProf3 (APE for France)
        $idprof=$langs->transcountry('ProfId3',$soc->pays_code);
            print '<td>'.$idprof.'</td><td colspan="3">';
            print '<input type="text" name="idprof3" size="18" maxlength="32" value="'.$soc->ape.'">';
            print '</td>';
        print '</tr>';

        // Legal Form
        print '<tr><td>'.$langs->trans('JuridicalStatus').'</td>';
        print '<td>';
        if ($GLOBALS['mysoc']->pays_id)
        {
            $formcompany->select_forme_juridique($soc->forme_juridique_code,$GLOBALS['mysoc']->pays_code);
        }
        else
        {
            print $GLOBALS['countrynotdefined'];
        }
        print '</td>';
        print '<td>'.$langs->trans('ProfId4'.$GLOBALS['mysoc']->pays_code).'</td>';
        print '<td><input type="text" name="idprof4" size="32" value="'.$soc->idprof4.'"></td>';
        print '</tr>';

        // Type
        print '<tr><td>'.$langs->trans("ThirdPartyType").'</td><td colspan="3">'."\n";
        print $form->selectarray("typent_id",$formcompany->typent_array(0), $soc->typent_id);
        if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
        print '</td>';
        /*print '<td>'.$langs->trans("Staff").'</td><td>';
        print $form->selectarray("effectif_id",$formcompany->effectif_array(0), $soc->effectif_id);
        if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
        print '</td></tr>';*/

        if ($conf->global->MAIN_MULTILANGS)
        {
            print '<tr><td>'.$langs->trans("DefaultLang").'</td><td colspan="3">'."\n";
            print $formadmin->select_language(($soc->default_lang?$soc->default_lang:$conf->global->MAIN_LANG_DEFAULT),'default_lang',0,0,1);
            print '</td>';
            print '</tr>';
        }
?>
<tr>
	<td nowrap="nowrap"><?php echo $langs->trans('VATIntra'); ?></td>
	<td nowrap="nowrap" colspan="3">
    <input type="text" class="flat" name="tva_intra" size="12" maxlength="20" value="<?php echo $soc->tva_intra ?>">
	</td>
</tr>

<?php if(!empty($this->control->tpl['localtax'])) echo $this->control->tpl['localtax']; ?>

<?php
        if ($user->rights->societe->client->voir)
        {
            // Assign a Name
            print '<tr>';
            print '<td>'.$langs->trans("AllocateCommercial").'</td>';
            print '<td colspan="3">';
            $form->select_users($soc->commercial_id,'commercial_id',1);
            print '</td></tr>';
        }
?>

<tr>
	<td colspan="4" align="center"><input type="submit" class="button" value="<?php echo $langs->trans('AddThirdParty'); ?>"></td>
</tr>

</table>
</form>

<!-- END PHP TEMPLATE -->