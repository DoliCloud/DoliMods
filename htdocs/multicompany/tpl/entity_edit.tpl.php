<?php
/* Copyright (C) 2009-2013 Regis Houssin <regis.houssin@capnetworks.com>
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
 *
 */
?>

<!-- BEGIN PHP TEMPLATE -->

<script type="text/javascript">
$(document).ready(function () {
	$("#selectcountry_id").change(function() {
		document.form_entity.action.value="edit";
		document.form_entity.submit();
	});
	$.extend($.ui.multiselect.locale, {
		addAll:'<?php echo $langs->transnoentities("AddAll"); ?>',
		removeAll:'<?php echo $langs->transnoentities("RemoveAll"); ?>',
		itemsCount:'<?php echo $langs->transnoentities("ItemsCount"); ?>'
	});
	$(function(){
	  $(".multiselect").multiselect({sortable: false, searchable: false});
	});
});
</script>

<form name="form_entity" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?php echo GETPOST('id'); ?>" />

<?php $var=true; ?>

<table class="noborder">
<tr class="liste_titre">
	<td width="35%"><?php echo $langs->trans("CompanyInfo"); ?></td>
	<td><?php echo $langs->trans("Value"); ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><span class="fieldrequired"><?php echo $langs->trans("Label"); ?></span></td>
	<td><input name="label" size="30" value="<?php echo $this->tpl['label']; ?>" /></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><span class="fieldrequired"><?php echo $langs->trans("CompanyName"); ?></span></td>
	<td><input name="name" size="30" value="<?php echo $this->tpl['name']; ?>" /></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><?php echo $langs->trans("CompanyAddress"); ?></td>
	<td><textarea name="address" cols="80" rows="<?php echo ROWS_3; ?>"><?php echo $this->tpl['address']; ?></textarea></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><?php echo $langs->trans("CompanyZip"); ?></td>
	<td><?php echo $this->tpl['select_zip']; ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><?php echo $langs->trans("CompanyTown"); ?></td>
	<td><?php echo $this->tpl['select_town']; ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><?php echo $langs->trans("Country"); ?></td>
	<td><?php echo $this->tpl['select_country'].$this->tpl['info_admin']; ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><?php echo $langs->trans("State"); ?></td>
	<td><?php echo $this->tpl['select_state']; ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><?php echo $langs->trans("CompanyCurrency"); ?></td>
	<td><?php echo $this->tpl['select_currency']; ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td><?php echo $langs->trans("DefaultLanguage"); ?></td>
	<td><?php echo $this->tpl['select_language']; ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("Description"); ?></td>
	<td><textarea class="flat" name="description" cols="80" rows="<?php echo ROWS_3; ?>"><?php echo $this->tpl['description']; ?></textarea></td>
</tr>

<?php if (! empty($conf->global->MULTICOMPANY_SHARINGS_ENABLED)) { ?>

<?php if (! empty($conf->global->MULTICOMPANY_SOCIETE_SHARING_ENABLED)) { ?>
<tr class="liste_titre">
<td colspan="2"><?php echo $langs->trans("CommonParameters"); ?></td>
</tr>
<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("ReferringEntity"); ?></td>
	<td><?php echo $this->tpl['select_entity']; ?></td>
</tr>
<?php } ?>

<?php if (! empty($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED)) { ?>
<tr class="liste_titre">
	<td colspan="2"><?php echo $langs->trans("ProductSharing"); ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("ProductSharingDescription"); ?></td>
	<td><?php echo $this->tpl['multiselect_shared_product']; ?></td>
</tr>
<?php } ?>

<?php if (! empty($conf->global->MULTICOMPANY_PRODUCTPRICE_SHARING_ENABLED)) { ?>
<tr class="liste_titre">
	<td colspan="2"><?php echo $langs->trans("ProductPriceSharing"); ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("ProductPriceSharingDescription"); ?></td>
	<td><?php echo $this->tpl['multiselect_shared_productprice']; ?></td>
</tr>
<?php } ?>

<?php if (! empty($conf->global->MULTICOMPANY_SOCIETE_SHARING_ENABLED)) { ?>
<tr class="liste_titre">
<td colspan="2"><?php echo $langs->trans("ThirdpartySharing"); ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("ThirdpartySharingDescription"); ?></td>
	<td><?php echo $this->tpl['multiselect_shared_thirdparty']; ?></td>
</tr>
<?php } ?>

<?php if (! empty($conf->global->MULTICOMPANY_CATEGORY_SHARING_ENABLED)) { ?>
<tr class="liste_titre">
	<td colspan="2"><?php echo $langs->trans("CategorySharing"); ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("CategorySharingDescription"); ?></td>
	<td><?php echo $this->tpl['multiselect_shared_category']; ?></td>
</tr>
<?php } ?>

<?php if (! empty($conf->global->MULTICOMPANY_AGENDA_SHARING_ENABLED)) { ?>
<tr class="liste_titre">
	<td colspan="2"><?php echo $langs->trans("AgendaSharing"); ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("AgendaSharingDescription"); ?></td>
	<td><?php echo $this->tpl['multiselect_shared_agenda']; ?></td>
</tr>
<?php } } ?>

<?php if (! empty($conf->global->MULTICOMPANY_BANK_ACCOUNT_SHARING_ENABLED)) { ?>
<tr class="liste_titre">
	<td colspan="2"><?php echo $langs->trans("BankSharing"); ?></td>
</tr>

<?php $var=!$var; ?>
<tr <?php echo $bc[$var]; ?>>
	<td valign="top"><?php echo $langs->trans("BankSharingDescription"); ?></td>
	<td><?php echo $this->tpl['multiselect_shared_bank_account']; ?></td>
</tr>
<?php } ?>

</table>
</div>

<div class="tabsAction">
<input type="submit" class="butAction linkobject" name="update" value="<?php echo $langs->trans('Update'); ?>" />
<input type="submit" class="butAction linkobject" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
</div>

</form>

<!-- END PHP TEMPLATE -->