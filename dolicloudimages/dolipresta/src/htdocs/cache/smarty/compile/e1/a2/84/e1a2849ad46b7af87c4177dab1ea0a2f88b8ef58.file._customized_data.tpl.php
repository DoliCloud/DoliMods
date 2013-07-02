<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:01
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/_customized_data.tpl" */ ?>
<?php /*%%SmartyHeaderCode:67768145251c1c0e14c1b29-91287577%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e1a2849ad46b7af87c4177dab1ea0a2f88b8ef58' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/_customized_data.tpl',
      1 => 1371647782,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '67768145251c1c0e14c1b29-91287577',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'currency' => 0,
    'can_edit' => 0,
    'order' => 0,
    'stock_management' => 0,
    'customizationPerAddress' => 0,
    'customization' => 0,
    'type' => 0,
    'datas' => 0,
    'data' => 0,
    'customizationId' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e18d5a04_93121111',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e18d5a04_93121111')) {function content_51c1c0e18d5a04_93121111($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['product']->value['customizedDatas']){?>
	<tr class="customized customized-<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
		<td align="center">
			<input type="hidden" class="edit_product_id_order_detail" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" />
			<?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&intval($_smarty_tpl->tpl_vars['product']->value['image']->id)){?><?php echo $_smarty_tpl->tpl_vars['product']->value['image_tag'];?>
<?php }else{ ?>--<?php }?></td>
		<td>
			<a href="index.php?controller=adminproducts&id_product=<?php echo intval($_smarty_tpl->tpl_vars['product']->value['product_id']);?>
&updateproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
">
			<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
 - <?php echo smartyTranslate(array('s'=>'Customized'),$_smarty_tpl);?>
</span><br />
			<?php if (($_smarty_tpl->tpl_vars['product']->value['product_reference'])){?><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_reference'];?>
<br /><?php }?>
			<?php if (($_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'])){?><?php echo smartyTranslate(array('s'=>'Ref Supplier:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'];?>
<?php }?>
			</a>
		</td>
		<td align="center">
			<span class="product_price_show"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['product_price_wt'],'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>
</span>
			<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
			<span class="product_price_edit" style="display:none;">
				<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
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
		<td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'];?>
</td>
		<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?><td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityRefunded'];?>
</td><?php }?>
		<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasProductReturned())){?><td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityReturned'];?>
</td><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['stock_management']->value){?><td align="center" class=""> - </td><?php }?>
		<td align="center" class="total_product">
		<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))){?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price']*$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

		<?php }else{ ?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price_wt']*$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

		<?php }?>
		</td>
		<td class="cancelQuantity standard_refund_fields current-edit" style="display:none" colspan="2">
			&nbsp;
		</td>
		<td class="edit_product_fields" colspan="2" style="display:none">&nbsp;</td>
		<td class="partial_refund_fields current-edit" style="text-align:left;display:none"></td>
		<?php if (($_smarty_tpl->tpl_vars['can_edit']->value&&!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?>
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
	<?php  $_smarty_tpl->tpl_vars['customizationPerAddress'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['customizedDatas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customizationPerAddress']->key => $_smarty_tpl->tpl_vars['customizationPerAddress']->value){
$_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = true;
?>
		<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_smarty_tpl->tpl_vars['customizationId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customizationPerAddress']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value){
$_smarty_tpl->tpl_vars['customization']->_loop = true;
 $_smarty_tpl->tpl_vars['customizationId']->value = $_smarty_tpl->tpl_vars['customization']->key;
?>
			<tr class="customized customized-<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
				<td colspan="2">
				<input type="hidden" class="edit_product_id_order_detail" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" />
			<?php  $_smarty_tpl->tpl_vars['datas'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['datas']->_loop = false;
 $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customization']->value['datas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['datas']->key => $_smarty_tpl->tpl_vars['datas']->value){
$_smarty_tpl->tpl_vars['datas']->_loop = true;
 $_smarty_tpl->tpl_vars['type']->value = $_smarty_tpl->tpl_vars['datas']->key;
?>
				<?php if (($_smarty_tpl->tpl_vars['type']->value==Product::CUSTOMIZE_FILE)){?>
					<ul style="margin: 4px 0px 4px 0px; padding: 0px; list-style-type: none;">
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['data']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['data']->iteration++;
?>
						<li style="margin: 2px;">
							<span><?php if ($_smarty_tpl->tpl_vars['data']->value['name']){?><?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Picture #'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['data']->iteration;?>
<?php }?><?php echo smartyTranslate(array('s'=>':'),$_smarty_tpl);?>
</span>&nbsp;
								<a href="displayImage.php?img=<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
&name=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
-file<?php echo $_smarty_tpl->tpl_vars['data']->iteration;?>
" target="_blank"><img src="<?php echo @constant('_THEME_PROD_PIC_DIR_');?>
<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
_small" alt="" /></a>
						</li>
					<?php } ?>
					</ul>
				<?php }elseif(($_smarty_tpl->tpl_vars['type']->value==Product::CUSTOMIZE_TEXTFIELD)){?>
					<ul style="margin: 0px 0px 4px 0px; padding: 0px 0px 0px 6px; list-style-type: none;">
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['data']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['data']->iteration++;
?>
						<li><?php if ($_smarty_tpl->tpl_vars['data']->value['name']){?><?php echo smartyTranslate(array('s'=>'%s:','sprintf'=>$_smarty_tpl->tpl_vars['data']->value['name']),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Text #%s:','sprintf'=>$_smarty_tpl->tpl_vars['data']->iteration),$_smarty_tpl);?>
<?php }?> <?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
</li>
					<?php } ?>
					</ul>
				<?php }?>
			<?php } ?>
				</td>
				<td align="center">-
				</td>
				<td align="center" class="productQuantity">
					<span class="product_quantity_show<?php if ((int)$_smarty_tpl->tpl_vars['customization']->value['quantity']>1){?> red bold<?php }?>"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
					<span class="product_quantity_edit" style="display:none;">
						<input type="text" name="product_quantity[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" class="edit_product_quantity" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['customization']->value['quantity']);?>
" size="2" />
					</span>
					<?php }?>
				</td>
				<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?><td align="center"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity_refunded'];?>
</td><?php }?>
				<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?><td align="center"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity_returned'];?>
</td><?php }?>
				<td align="center">
				-
				</td>
				<td align="center" class="total_product">
					<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))){?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price']*$_smarty_tpl->tpl_vars['customization']->value['quantity'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

					<?php }else{ ?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price_wt']*$_smarty_tpl->tpl_vars['customization']->value['quantity'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

					<?php }?>
				</td>				
				<td align="center" class="cancelCheck standard_refund_fields current-edit" style="display:none">
					<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="<?php echo intval($_smarty_tpl->tpl_vars['customization']->value['quantity_returned']);?>
" />
					<input type="hidden" name="totalQty" id="totalQty" value="<?php echo intval($_smarty_tpl->tpl_vars['customization']->value['quantity']);?>
" />
					<input type="hidden" name="productName" id="productName" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
" />
					<?php if (((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))&&(int)($_smarty_tpl->tpl_vars['customization']->value['quantity_returned'])<(int)($_smarty_tpl->tpl_vars['customization']->value['quantity']))){?>
						<input type="checkbox" name="id_customization[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" id="id_customization[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" onchange="setCancelQuantity(this, <?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
, <?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']-$_smarty_tpl->tpl_vars['product']->value['product_quantity_reinjected'];?>
)" <?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])){?>disabled="disabled" <?php }?>/>
					<?php }else{ ?>
					--
				<?php }?>
				</td>
				<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
				<?php if (($_smarty_tpl->tpl_vars['customization']->value['quantity_returned']+$_smarty_tpl->tpl_vars['customization']->value['quantity_refunded']>=$_smarty_tpl->tpl_vars['customization']->value['quantity'])){?>
					<input type="hidden" name="cancelCustomizationQuantity[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" value="0" />
				<?php }elseif((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))){?>
					<input type="text" id="cancelQuantity_<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
" name="cancelCustomizationQuantity[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" size="2" onclick="selectCheckbox(this);" value="" />0/<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['customization']->value['quantity_refunded'];?>

				<?php }?>
				</td>
				<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
					<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><input type="text" size="3" name="partialRefundProductQuantity[<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
]" value="0" />		
					0/<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['customization']->value['quantity_refunded'];?>

					</div>
					<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Amount:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<input type="text" size="3" name="partialRefundProduct[<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
]" /><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</div>
				</td>
				<?php if (($_smarty_tpl->tpl_vars['can_edit']->value&&!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?>
					<td class="edit_product_fields" colspan="2" style="display:none"></td>
					<td class="product_action" style="text-align:right"></td>
				<?php }?>
			</tr>
		<?php } ?>
	<?php } ?>
<?php }?>
<?php }} ?>