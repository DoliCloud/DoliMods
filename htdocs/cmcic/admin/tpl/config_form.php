<?php
/* Copyright (C) 2012      Mikael Carlavan        <mcarlavan@qis-network.com>
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

/**	    \file       htdocs/cmcic/admin/tpl/cmcic_config.php
 *		\ingroup    cmcic
 *		\brief      Cmcic module setup form
 */

if (!$user->admin)
  accessforbidden();


if (empty($conf->cmcic->enabled))
    exit;


// Header
llxHeader('', $langs->trans("CMCIC_SETUP"));
//Titre
print_fiche_titre(' - '.$langs->trans("ModuleSetup"), $linkback, 'cmcic_logo@cmcic');
?>
<br />

<?php dol_fiche_head($head, $current_head, $langs->trans("ModuleSetup")); ?>

<script type="text/javascript">
<!--
$(document).ready(function () {
        $("#generate_token").click(function() {
        	$.get( "<?php echo DOL_URL_ROOT; ?>/core/ajax/security.php", {
        		action: 'getrandompassword',
        		generic: true
			},
			function(token) {
				$("#CMCIC_SECURITY_TOKEN").val(token);
			});
        });
});
-->
</script>
<br />
<?php echo $langs->trans("CMCIC_DESC"); ?>
<br />
<?php echo ($updated ? dol_htmloutput_mesg($langs->trans("SetupSaved"), '', 'ok', 0) : ''); ?>
<?php echo ($error ? dol_htmloutput_mesg($err, '', 'error', 0) : ''); ?>
<br />
<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

<table class="nobordernopadding" style="width:100%">
<tr class="liste_titre">
    <td><?php echo $langs->trans("CMCIC_ACCOUNT_PARAMETERS"); ?></td>
    <td><?php echo $langs->trans("Value"); ?></td>
    <td><?php echo $langs->trans("Infos"); ?></td>
</tr>

<tr class="impair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_API_TEST"); ?></td>
    <td><?php echo $form->selectyesno("CMCIC_API_TEST", $params->CMCIC_API_TEST, 1); ?></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_API_TEST'], 1, 0); ?></td>
</tr>

<tr class="pair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_BANK_SERVER"); ?></td>
    <td><?php echo $form->selectarray("CMCIC_BANK_SERVER", array('cic'=>'CIC','cm'=>'Crédit Mutuel','obc'=>'OBC'),$params->CMCIC_BANK_SERVER); ?></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_BANK_SERVER'], 1, 0); ?></td>
</tr>

<tr class="impair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_TPE_NUMBER"); ?></td>
    <td><input size="32" type="text" name="CMCIC_TPE_NUMBER" value="<?php echo $params->CMCIC_TPE_NUMBER; ?>" /></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_TPE_NUMBER'], 1, 0); ?></td>
</tr>

<tr class="pair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_SOCIETY_ID"); ?></td>
    <td><input size="32" type="text" name="CMCIC_SOCIETY_ID" value="<?php echo $params->CMCIC_SOCIETY_ID; ?>"/></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_SOCIETY_ID'], 1, 0); ?></td>
</tr>

<tr class="impair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_KEY"); ?></td>
    <td><input size="64" type="text" name="CMCIC_KEY" value="<?php echo $params->CMCIC_KEY; ?>" /></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_KEY'], 1, 0); ?></td>
</tr>

<tr class="liste_titre">
    <td><?php echo $langs->trans("CMCIC_USAGE_PARAMETERS"); ?></td>
    <td><?php echo $langs->trans("Value"); ?></td>
    <td><?php echo $langs->trans("Infos"); ?></td>
</tr>

<tr class="impair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_SECURITY_TOKEN"); ?></td>
    <td><input size="64" type="text" id="CMCIC_SECURITY_TOKEN" name="CMCIC_SECURITY_TOKEN" value="<?php echo $params->CMCIC_SECURITY_TOKEN; ?>" /> <?php echo img_picto($langs->trans('Generate'), 'refresh', 'id="generate_token" class="linkobject"'); ?></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_SECURITY_TOKEN'], 1, 0); ?></td>
</tr>

<tr class="pair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_DELIVERY_RECEIPT_EMAIL"); ?></td>
    <td><?php echo $form->selectyesno("CMCIC_DELIVERY_RECEIPT_EMAIL", $params->CMCIC_DELIVERY_RECEIPT_EMAIL, 1); ?></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_DELIVERY_RECEIPT_EMAIL'], 1, 0); ?></td>
</tr>

<tr class="impair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_CC_EMAIL"); ?></td>
    <td><?php echo $form->selectyesno("CMCIC_CC_EMAIL", $params->CMCIC_CC_EMAIL, 1); ?></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_CC_EMAIL'], 1, 0); ?></td>
</tr>

<tr class="pair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_CC_EMAILS"); ?></td>
    <td><input size="64" type="text" id="CMCIC_CC_EMAILS" name="CMCIC_CC_EMAILS" value="<?php echo $params->CMCIC_CC_EMAILS; ?>" /></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_CC_EMAILS'], 1, 0); ?></td>
</tr>

<tr class="liste_titre">
    <td><?php echo $langs->trans("CMCIC_INTEGRATION_PARAMETERS"); ?></td>
    <td><?php echo $langs->trans("Value"); ?></td>
    <td><?php echo $langs->trans("Infos"); ?></td>
</tr>

<tr class="impair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_UPDATE_INVOICE_STATUT"); ?></td>
    <td><?php echo $form->selectyesno("CMCIC_UPDATE_INVOICE_STATUT", $params->CMCIC_UPDATE_INVOICE_STATUT, 1); ?></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_UPDATE_INVOICE_STATUT'], 1, 0); ?></td>
</tr>

<tr class="pair">
    <td class="fieldrequired"><?php echo $langs->trans("CMCIC_BANK_ACCOUNT_ID"); ?></td>
    <td><?php $form->select_comptes($params->CMCIC_BANK_ACCOUNT_ID, 'CMCIC_BANK_ACCOUNT_ID', 0, '', 1); ?></td>
    <td><?php echo $form->textwithpicto('', $htmltooltips['CMCIC_BANK_ACCOUNT_ID'], 1, 0); ?></td>
</tr>


<tr>
    <td colspan="2" style="text-align:center;">
    <br />
    <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
    <input type="hidden" name="action" value="update" />
    <input type="submit" class="button" value="<?php echo $langs->trans("Modify"); ?>" />
    </td>
</tr>

</table>
</form>

<?php dol_fiche_end(); ?>

<br />

<?php llxFooter(); ?>


