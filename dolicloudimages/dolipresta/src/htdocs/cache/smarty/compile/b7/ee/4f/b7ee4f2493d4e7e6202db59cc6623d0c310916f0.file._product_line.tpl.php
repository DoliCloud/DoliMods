<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:01
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/_product_line.tpl" */ ?>
<?php /*%%SmartyHeaderCode:138107132851c1c0e1da6ab4-40016144%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b7ee4f2493d4e7e6202db59cc6623d0c310916f0' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/_product_line.tpl',
      1 => 1371647784,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '138107132851c1c0e1da6ab4-40016144',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order' => 0,
    'product' => 0,
    'product_price' => 0,
    'currency' => 0,
    'can_edit' => 0,
    'refund' => 0,
    'return' => 0,
    'stock_management' => 0,
    'productQuantity' => 0,
    'invoices_collection' => 0,
    'invoice' => 0,
    'current_id_lang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e2259993_27609234',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e2259993_27609234')) {function content_51c1c0e2259993_27609234($_smarty_tpl) {?>


<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))){?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl']+$_smarty_tpl->tpl_vars['product']->value['ecotax']), null, 0);?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'], null, 0);?>
<?php }?>

<?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity']>$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'])){?>
<tr<?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&$_smarty_tpl->tpl_vars['product']->value['image']->id&&isset($_smarty_tpl->tpl_vars['product']->value['image_size'])){?> height="<?php echo $_smarty_tpl->tpl_vars['product']->value['image_size'][1]+7;?>
"<?php }?>>
	<td align="center"><?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&$_smarty_tpl->tpl_vars['product']->value['image']->id){?><?php echo $_smarty_tpl->tpl_vars['product']->value['image_tag'];?>
<?php }?></td>
	<td><a href="index.php?controller=adminproducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
&updateproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
">
		<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
</span><br />
		<?php if ($_smarty_tpl->tpl_vars['product']->value['product_reference']){?><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_reference'];?>
<br /><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['product']->value['product_supplier_reference']){?><?php echo smartyTranslate(array('s'=>'Ref Supplier:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'];?>
<?php }?>
	</a></td>
	<td align="center">
		<span class="product_price_show"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</span>
		<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
		<span class="product_price_edit" style="display:none;">
			<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" />
			<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl'],2);?>
" size="5" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
<br />
			<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'],2);?>
" size="5" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>

		</span>
		<?php }?>
	</td>
	<td align="center" class="productQuantity">
		<span class="product_quantity_show<?php if ((int)$_smarty_tpl->tpl_vars['product']->value['product_quantity']>1){?> red bold<?php }?>"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity'];?>
</span>
		<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
		<span class="product_quantity_edit" style="display:none;">
			<input type="text" name="product_quantity" class="edit_product_quantity" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value['product_quantity']);?>
" size="2" />
		</span>
		<?php }?>
	</td>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?>
		<td align="center" class="productQuantity">
			<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>

			<?php if (count($_smarty_tpl->tpl_vars['product']->value['refund_history'])){?>
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<div class="tooltip_content">
					<span class="title"><?php echo smartyTranslate(array('s'=>'Refund history'),$_smarty_tpl);?>
</span>
					<?php  $_smarty_tpl->tpl_vars['refund'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['refund']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['refund_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['refund']->key => $_smarty_tpl->tpl_vars['refund']->value){
$_smarty_tpl->tpl_vars['refund']->_loop = true;
?>
						<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['refund']->value['date_add']),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['refund']->value['amount_tax_incl']),$_smarty_tpl);?>
<?php $_tmp2=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'%1s - %2s','sprintf'=>array($_tmp1,$_tmp2)),$_smarty_tpl);?>
<br />
					<?php } ?>
					</div>
				</span>
			<?php }?>
		</td>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasProductReturned()){?>
		<td align="center" class="productQuantity">
			<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>

			<?php if (count($_smarty_tpl->tpl_vars['product']->value['return_history'])){?>
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<div class="tooltip_content">
					<span class="title"><?php echo smartyTranslate(array('s'=>'Return history'),$_smarty_tpl);?>
</span>
					<?php  $_smarty_tpl->tpl_vars['return'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['return']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['return_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['return']->key => $_smarty_tpl->tpl_vars['return']->value){
$_smarty_tpl->tpl_vars['return']->_loop = true;
?>
						<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['return']->value['date_add']),$_smarty_tpl);?>
<?php $_tmp3=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'%1s - %2s - %3s','sprintf'=>array($_tmp3,$_smarty_tpl->tpl_vars['return']->value['product_quantity'],$_smarty_tpl->tpl_vars['return']->value['state'])),$_smarty_tpl);?>
<br />
					<?php } ?>
					</div>
				</span>
			<?php }?>
		</td>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['stock_management']->value){?><td align="center" class="productQuantity product_stock"><?php echo $_smarty_tpl->tpl_vars['product']->value['current_stock'];?>
</td><?php }?>
	<td align="center" class="total_product">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>(Tools::ps_round($_smarty_tpl->tpl_vars['product_price']->value,2)*($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'])),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

	</td>
	<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
	<td align="center" class="cancelCheck standard_refund_fields current-edit" style="display:none">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>
" />
		<input type="hidden" name="totalQty" id="totalQty" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity'];?>
" />
		<input type="hidden" name="productName" id="productName" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
" />
	<?php if (((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))&&(int)($_smarty_tpl->tpl_vars['product']->value['product_quantity_return'])<(int)($_smarty_tpl->tpl_vars['product']->value['product_quantity']))){?>
		<input type="checkbox" name="id_order_detail[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" id="id_order_detail[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" onchange="setCancelQuantity(this, <?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
, <?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']-$_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>
)" <?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])){?>disabled="disabled" <?php }?>/>
	<?php }else{ ?>
		--
	<?php }?>
	</td>
	<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
	<?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])){?>
		<input type="hidden" name="cancelQuantity[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" value="0" />
	<?php }elseif((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))){?>
		<input type="text" id="cancelQuantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" name="cancelQuantity[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" size="2" onclick="selectCheckbox(this);" value="" />
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']){?>
		<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']), null, 0);?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_quantity'], null, 0);?>
	<?php }?>

	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?>
		<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>
/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value-$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>

	<?php }elseif(($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?>
		<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>
/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value;?>

	<?php }else{ ?>
		0/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value;?>

	<?php }?>
	</td>
	<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
		<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><input onchange="checkPartialRefundProductQuantity(this)" type="text" size="3" name="partialRefundProductQuantity[<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
<?php $_tmp4=ob_get_clean();?><?php echo $_tmp4;?>
]" value="0" /> 0/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value-$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>
</div>
		<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Amount:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<input onchange="checkPartialRefundProductAmount(this)" type="text" size="3" name="partialRefundProduct[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" /> <?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</div> <?php if (!empty($_smarty_tpl->tpl_vars['product']->value['amount_refund'])&&$_smarty_tpl->tpl_vars['product']->value['amount_refund']>0){?>(<?php echo smartyTranslate(array('s'=>'%s refund','sprintf'=>$_smarty_tpl->tpl_vars['product']->value['amount_refund']),$_smarty_tpl);?>
)<?php }?>
		<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['quantity_refundable'];?>
" class="partialRefundProductQuantity" />
		<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['amount_refundable'];?>
" class="partialRefundProductAmount" />
	</td>
	<?php if (($_smarty_tpl->tpl_vars['can_edit']->value&&!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?>
	<td class="product_invoice" colspan="2" style="display: none;text-align:center;">
		<?php if (sizeof($_smarty_tpl->tpl_vars['invoices_collection']->value)){?>
		<select name="product_invoice" class="edit_product_invoice">
			<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value){
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
			<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['invoice']->value->id==$_smarty_tpl->tpl_vars['product']->value['id_order_invoice']){?>selected="selected"<?php }?>>#<?php echo Configuration::get('PS_INVOICE_PREFIX',$_smarty_tpl->tpl_vars['current_id_lang']->value,null,$_smarty_tpl->tpl_vars['order']->value->id_shop);?>
<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['invoice']->value->number);?>
</option>
			<?php } ?>
		</select>
		<?php }else{ ?>
		&nbsp;
		<?php }?>
	</td>
	<td class="product_action" style="text-align:right">
		<a href="#" class="edit_product_change_link"><img src="../img/admin/edit.gif" alt="<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
" /></a>
		<input type="submit" class="button" name="submitProductChange" value="<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
"  style="display: none;" />
		<a href="#" class="cancel_product_change_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
" /></a>
		<a href="#" class="delete_product_line"><img src="../img/admin/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" /></a>
	</td>
	<?php }?>
</tr>
<?php }?>
<?php }} ?>