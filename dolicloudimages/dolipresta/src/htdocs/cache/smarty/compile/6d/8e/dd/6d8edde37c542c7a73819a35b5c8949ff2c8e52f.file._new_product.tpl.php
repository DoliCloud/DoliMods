<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:01
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/_new_product.tpl" */ ?>
<?php /*%%SmartyHeaderCode:90051865551c1c0e1c73d66-59628786%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6d8edde37c542c7a73819a35b5c8949ff2c8e52f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/_new_product.tpl',
      1 => 1371647783,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '90051865551c1c0e1c73d66-59628786',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currency' => 0,
    'order' => 0,
    'invoices_collection' => 0,
    'invoice' => 0,
    'current_id_lang' => 0,
    'carrier' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e1d44138_35098278',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e1d44138_35098278')) {function content_51c1c0e1d44138_35098278($_smarty_tpl) {?>
<tr id="new_product" height="52" style="display:none;background-color:#e9f1f6">
	<td style="display:none;" colspan="2">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />
		<?php echo smartyTranslate(array('s'=>'Product:'),$_smarty_tpl);?>
 <input type="text" id="add_product_product_name" value="" size="42" />
		<div id="add_product_product_attribute_area" style="margin-top: 5px;display: none;">
			<?php echo smartyTranslate(array('s'=>'Combinations'),$_smarty_tpl);?>
 <select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
		</div>
		<div id="add_product_product_warehouse_area" style="margin-top: 5px; display: none;">
			<?php echo smartyTranslate(array('s'=>'Warehouse'),$_smarty_tpl);?>
 <select  id="add_product_warehouse" name="add_product_warehouse">
			</select>
		</div>
	</td>
	<td style="display:none;">
		<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" size="4" disabled="disabled" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
<br />
		<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" size="4" disabled="disabled" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
<br />
	</td>
	<td style="display:none;" align="center" class="productQuantity"><input type="text" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" size="3" disabled="disabled" /></td>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?><td style="display:none;" align="center" class="productQuantity">&nbsp;</td><?php }?>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?><td style="display:none;" align="center" class="productQuantity">&nbsp;</td><?php }?>
	<td style="display:none;" align="center" class="productQuantity" id="add_product_product_stock">0</td>
	<td style="display:none;" align="center" id="add_product_product_total"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>0,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
	<td style="display:none;" align="center" colspan="2">
		<?php if (sizeof($_smarty_tpl->tpl_vars['invoices_collection']->value)){?>
		<select name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
			<optgroup class="existing" label="<?php echo smartyTranslate(array('s'=>'Existing'),$_smarty_tpl);?>
">
				<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value){
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>
</option>
				<?php } ?>
			</optgroup>
			<optgroup label="<?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
">
				<option value="0"><?php echo smartyTranslate(array('s'=>'Create a new invoice'),$_smarty_tpl);?>
</option>
			</optgroup>
		</select>
		<?php }?>
	</td>
	<td style="display:none;">
		<input type="button" class="button" id="submitAddProduct" value="<?php echo smartyTranslate(array('s'=>'Add product'),$_smarty_tpl);?>
" disabled="disabled" />
	</td>
</tr>
<tr id="new_invoice" style="display:none;background-color:#e9f1f6;">
	<td colspan="10">
		<h3><?php echo smartyTranslate(array('s'=>'New invoice information'),$_smarty_tpl);?>
</h3>
		<label><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<?php echo $_smarty_tpl->tpl_vars['carrier']->value->name;?>

		</div>
		<div class="margin-form">
			<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
			<label class="t"><?php echo smartyTranslate(array('s'=>'Free shipping'),$_smarty_tpl);?>
</label>
			<p><?php echo smartyTranslate(array('s'=>'If you don\'t select "Free shipping," the normal shipping cost will be applied.'),$_smarty_tpl);?>
</p>
		</div>
	</td>
</tr>
<?php }} ?>