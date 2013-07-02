<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:11
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:138059335451c1c0eb09cb83-18428540%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0ab81fd3aa9d8436f773675e16be3fe71ab4dec9' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-payment.tpl',
      1 => 1371647174,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '138059335451c1c0eb09cb83-18428540',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'opc' => 0,
    'currencySign' => 0,
    'currencyRate' => 0,
    'currencyFormat' => 0,
    'currencyBlank' => 0,
    'empty' => 0,
    'PS_CATALOG_MODE' => 0,
    'HOOK_TOP_PAYMENT' => 0,
    'HOOK_PAYMENT' => 0,
    'use_taxes' => 0,
    'priceDisplay' => 0,
    'display_tax_label' => 0,
    'total_products' => 0,
    'total_products_wt' => 0,
    'total_wrapping' => 0,
    'total_wrapping_tax_exc' => 0,
    'total_shipping_tax_exc' => 0,
    'virtualCart' => 0,
    'shippingCost' => 0,
    'shippingCostTaxExc' => 0,
    'total_discounts' => 0,
    'total_discounts_tax_exc' => 0,
    'total_tax' => 0,
    'voucherAllowed' => 0,
    'errors_discount' => 0,
    'error' => 0,
    'total_price' => 0,
    'link' => 0,
    'discount_name' => 0,
    'displayVouchers' => 0,
    'voucher' => 0,
    'total_price_without_tax' => 0,
    'products' => 0,
    'product' => 0,
    'productId' => 0,
    'productAttributeId' => 0,
    'customizedDatas' => 0,
    'id_customization' => 0,
    'customization' => 0,
    'type' => 0,
    'CUSTOMIZE_FILE' => 0,
    'datas' => 0,
    'pic_dir' => 0,
    'picture' => 0,
    'CUSTOMIZE_TEXTFIELD' => 0,
    'textField' => 0,
    'cannotModify' => 0,
    'quantityDisplayed' => 0,
    'token_cart' => 0,
    'img_dir' => 0,
    'gift_products' => 0,
    'last_was_odd' => 0,
    'discounts' => 0,
    'discount' => 0,
    'back' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0eb78e593_49316931',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0eb78e593_49316931')) {function content_51c1c0eb78e593_49316931($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
	<script type="text/javascript">
		// <![CDATA[
		var currencySign = '<?php echo html_entity_decode($_smarty_tpl->tpl_vars['currencySign']->value,2,"UTF-8");?>
';
		var currencyRate = '<?php echo floatval($_smarty_tpl->tpl_vars['currencyRate']->value);?>
';
		var currencyFormat = '<?php echo intval($_smarty_tpl->tpl_vars['currencyFormat']->value);?>
';
		var currencyBlank = '<?php echo intval($_smarty_tpl->tpl_vars['currencyBlank']->value);?>
';
		var txtProduct = "<?php echo smartyTranslate(array('s'=>'product','js'=>1),$_smarty_tpl);?>
";
		var txtProducts = "<?php echo smartyTranslate(array('s'=>'products','js'=>1),$_smarty_tpl);?>
";
		// ]]>
	</script>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['empty']->value)){?>
<p class="warning"><?php echo smartyTranslate(array('s'=>'Your shopping cart is empty.'),$_smarty_tpl);?>
</p>
<?php }elseif($_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
<p class="warning"><?php echo smartyTranslate(array('s'=>'This store has not accepted your new order.'),$_smarty_tpl);?>
</p>
<?php }else{ ?>
	<div id="displayMobileShoppingCartTop">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileShoppingCartTop"),$_smarty_tpl);?>

	</div>
	<div data-role="content" id="content" class="cart">
		<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


		<h2><?php echo smartyTranslate(array('s'=>'Your payment method'),$_smarty_tpl);?>
</h2>
		<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
			<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('payment', null, 0);?>
			<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php }else{ ?>
			<div id="opc_payment_methods" class="opc-main-block">
				<div id="opc_payment_methods-overlay" class="opc-overlay" style="display: none;"></div>
		<?php }?>


		<div class="paiement_block">
		
			<div id="HOOK_TOP_PAYMENT"><?php echo $_smarty_tpl->tpl_vars['HOOK_TOP_PAYMENT']->value;?>
</div>
			
			<?php if ($_smarty_tpl->tpl_vars['HOOK_PAYMENT']->value){?>
				<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
			<div id="order-detail-content" class="table_block">
				<table id="cart_summary" data-role="table" class="ui-body-d ui-shadow ui-responsive table-stroke" data-mode="reflow">
					<thead>
						<tr class="ui-bar-a">
							<th class="cart_product first_item"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</th>
							<th class="cart_description item"><?php echo smartyTranslate(array('s'=>'Description'),$_smarty_tpl);?>
</th>
							<th class="cart_availability item"><?php echo smartyTranslate(array('s'=>'Avail.'),$_smarty_tpl);?>
</th>
							<th class="cart_unit item" style="min-width:100px"><?php echo smartyTranslate(array('s'=>'Unit price'),$_smarty_tpl);?>
</th>
							<th class="cart_quantity item" style="min-width:30px"><?php echo smartyTranslate(array('s'=>'Qty'),$_smarty_tpl);?>
</th>
							<th class="cart_total last_item" style="min-width:100px"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</th>
						</tr>
					</thead>
					<tfoot>
						<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
							<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
								<tr class="cart_total_price">
									<td colspan="5" style="text-align:right;font-weight:bold"><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total products (tax excl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total products:'),$_smarty_tpl);?>
<?php }?></td>
									<td class="price" id="total_product"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_products']->value),$_smarty_tpl);?>
</td>
								</tr>
							<?php }else{ ?>
								<tr class="cart_total_price">
									<td colspan="5" style="text-align:right;font-weight:bold"><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total products (tax incl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total products:'),$_smarty_tpl);?>
<?php }?></td>
									<td class="price" id="total_product"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_products_wt']->value),$_smarty_tpl);?>
</td>
								</tr>
							<?php }?>
						<?php }else{ ?>
							<tr class="cart_total_price">
								<td colspan="5" style="text-align:right;font-weight:bold"><?php echo smartyTranslate(array('s'=>'Total products:'),$_smarty_tpl);?>
</td>
								<td class="price" id="total_product"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_products']->value),$_smarty_tpl);?>
</td>
							</tr>
						<?php }?>
						<tr class="cart_total_voucher" <?php if ($_smarty_tpl->tpl_vars['total_wrapping']->value==0){?>style="display:none"<?php }?>>
							<td colspan="5" style="text-align:right;font-weight:bold">
							<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
								<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
									<?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total gift wrapping (tax excl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total gift wrapping cost:'),$_smarty_tpl);?>
<?php }?>
								<?php }else{ ?>
									<?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total gift-wrapping (tax incl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total gift wrapping cost:'),$_smarty_tpl);?>
<?php }?>
								<?php }?>
							<?php }else{ ?>
								<?php echo smartyTranslate(array('s'=>'Total gift wrapping cost:'),$_smarty_tpl);?>

							<?php }?>
							</td>
							<td class="price-discount price" id="total_wrapping">
							<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
								<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping_tax_exc']->value),$_smarty_tpl);?>

								<?php }else{ ?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping']->value),$_smarty_tpl);?>

								<?php }?>
							<?php }else{ ?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping_tax_exc']->value),$_smarty_tpl);?>

							<?php }?>
							</td>
						</tr>
						<?php if ($_smarty_tpl->tpl_vars['total_shipping_tax_exc']->value<=0&&!isset($_smarty_tpl->tpl_vars['virtualCart']->value)){?>
							<tr class="cart_total_delivery">
								<td colspan="5" style="text-align:right;font-weight:bold"><?php echo smartyTranslate(array('s'=>'Shipping:'),$_smarty_tpl);?>
</td>
								<td class="price" id="total_shipping"><?php echo smartyTranslate(array('s'=>'Free Shipping!'),$_smarty_tpl);?>
</td>
							</tr>
						<?php }else{ ?>
							<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
								<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
									<tr class="cart_total_delivery" <?php if ($_smarty_tpl->tpl_vars['shippingCost']->value<=0){?> style="display:none"<?php }?>>
										<td colspan="5" style="text-align:right;font-weight:bold"><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total shipping (tax excl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total shipping:'),$_smarty_tpl);?>
<?php }?></td>
										<td class="price" id="total_shipping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['shippingCostTaxExc']->value),$_smarty_tpl);?>
</td>
									</tr>
								<?php }else{ ?>
									<tr class="cart_total_delivery"<?php if ($_smarty_tpl->tpl_vars['shippingCost']->value<=0){?> style="display:none"<?php }?>>
										<td colspan="5" style="text-align:right;font-weight:bold"><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total shipping (tax incl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total shipping:'),$_smarty_tpl);?>
<?php }?></td>
										<td class="price" id="total_shipping" ><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['shippingCost']->value),$_smarty_tpl);?>
</td>
									</tr>
								<?php }?>
							<?php }else{ ?>
								<tr class="cart_total_delivery"<?php if ($_smarty_tpl->tpl_vars['shippingCost']->value<=0){?> style="display:none"<?php }?>>
									<td colspan="5" style="text-align:right;font-weight:bold"><?php echo smartyTranslate(array('s'=>'Total shipping:'),$_smarty_tpl);?>
</td>
									<td class="price" id="total_shipping" ><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['shippingCostTaxExc']->value),$_smarty_tpl);?>
</td>
								</tr>
							<?php }?>
						<?php }?>
						<tr class="cart_total_voucher" <?php if ($_smarty_tpl->tpl_vars['total_discounts']->value==0){?>style="display:none"<?php }?>>
							<td colspan="5" style="text-align:right;font-weight:bold">
							<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
								<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
									<?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total vouchers (tax excl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total vouchers:'),$_smarty_tpl);?>
<?php }?>
								<?php }else{ ?>
									<?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total vouchers (tax incl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total vouchers:'),$_smarty_tpl);?>
<?php }?>
								<?php }?>
							<?php }else{ ?>
								<?php echo smartyTranslate(array('s'=>'Total vouchers:'),$_smarty_tpl);?>

							<?php }?>
							</td>
							<td class="price-discount price" id="total_discount">
							<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
								<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_discounts_tax_exc']->value*-1),$_smarty_tpl);?>

								<?php }else{ ?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_discounts']->value*-1),$_smarty_tpl);?>

								<?php }?>
							<?php }else{ ?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_discounts_tax_exc']->value*-1),$_smarty_tpl);?>

							<?php }?>
							</td>
						</tr>
						<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
							<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value&&$_smarty_tpl->tpl_vars['total_tax']->value!=0){?>
								<tr class="cart_total_tax">
									<td colspan="5"><?php echo smartyTranslate(array('s'=>'Total tax:'),$_smarty_tpl);?>
</td>
									<td class="price" id="total_tax" ><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_tax']->value),$_smarty_tpl);?>
</td>
								</tr>
							<?php }?>
						<tr class="cart_total_price">
							<td colspan="5" id="cart_voucher" class="cart_voucher" style="text-align:right;font-weight:bold">
							<?php echo smartyTranslate(array('s'=>'Total:'),$_smarty_tpl);?>

							<?php if ($_smarty_tpl->tpl_vars['voucherAllowed']->value){?>
								<?php if (isset($_smarty_tpl->tpl_vars['errors_discount']->value)&&$_smarty_tpl->tpl_vars['errors_discount']->value){?>
									<ul class="error">
									<?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['errors_discount']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value){
$_smarty_tpl->tpl_vars['error']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['error']->key;
?>
										<li><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['error']->value, 'htmlall', 'UTF-8');?>
</li>
									<?php } ?>
									</ul>
								<?php }?>
							<?php }?>
							</td>
							<td class="price total_price_container" id="total_price_container">
								<span><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_price']->value),$_smarty_tpl);?>
</span>
							</td>
						</tr>
						<?php }else{ ?>
						<tr class="cart_total_price">
							<td colspan="5" id="cart_voucher" class="cart_voucher" style="text-align:right;font-weight:bold">
							<?php echo smartyTranslate(array('s'=>'Total:'),$_smarty_tpl);?>

							<?php if ($_smarty_tpl->tpl_vars['voucherAllowed']->value){?>
							<div id="cart_voucher" class="table_block">
								<?php if (isset($_smarty_tpl->tpl_vars['errors_discount']->value)&&$_smarty_tpl->tpl_vars['errors_discount']->value){?>
									<ul class="error">
									<?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['errors_discount']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value){
$_smarty_tpl->tpl_vars['error']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['error']->key;
?>
										<li><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['error']->value, 'htmlall', 'UTF-8');?>
</li>
									<?php } ?>
									</ul>
								<?php }?>
								<?php if ($_smarty_tpl->tpl_vars['voucherAllowed']->value){?>
								<form action="<?php if ($_smarty_tpl->tpl_vars['opc']->value){?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-opc',true);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true);?>
<?php }?>" method="post" id="voucher">
									<fieldset>
										<p class="title_block"><label for="discount_name"><?php echo smartyTranslate(array('s'=>'Vouchers'),$_smarty_tpl);?>
</label></p>
										<p>
											<input type="text" id="discount_name" name="discount_name" value="<?php if (isset($_smarty_tpl->tpl_vars['discount_name']->value)&&$_smarty_tpl->tpl_vars['discount_name']->value){?><?php echo $_smarty_tpl->tpl_vars['discount_name']->value;?>
<?php }?>" />
										</p>
										<p class="submit"><input type="hidden" name="submitDiscount" /><input type="submit" name="submitAddDiscount" value="<?php echo smartyTranslate(array('s'=>'ok'),$_smarty_tpl);?>
" class="button" /></p>
									<?php if ($_smarty_tpl->tpl_vars['displayVouchers']->value){?>
										<p id="title" class="title_offers"><?php echo smartyTranslate(array('s'=>'Take advantage of our offers:'),$_smarty_tpl);?>
</p>
										<div id="display_cart_vouchers">
										<?php  $_smarty_tpl->tpl_vars['voucher'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['voucher']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['displayVouchers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['voucher']->key => $_smarty_tpl->tpl_vars['voucher']->value){
$_smarty_tpl->tpl_vars['voucher']->_loop = true;
?>
											<span onclick="$('#discount_name').val('<?php echo $_smarty_tpl->tpl_vars['voucher']->value['name'];?>
');return false;" class="voucher_name"><?php echo $_smarty_tpl->tpl_vars['voucher']->value['name'];?>
</span> - <?php echo $_smarty_tpl->tpl_vars['voucher']->value['description'];?>
 <br />
										<?php } ?>
										</div>
									<?php }?>
									</fieldset>
								</form>
								<?php }?>
							</div>
							<?php }?>
							</td>
							<td class="price total_price_container" id="total_price_container">
								<span id="total_price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_price_without_tax']->value),$_smarty_tpl);?>
</span>
							</td>
						</tr>
						<?php }?>
					</tfoot>
					<tbody>
					<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['product']->iteration=0;
 $_smarty_tpl->tpl_vars['product']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['product']->iteration++;
 $_smarty_tpl->tpl_vars['product']->index++;
 $_smarty_tpl->tpl_vars['product']->first = $_smarty_tpl->tpl_vars['product']->index === 0;
 $_smarty_tpl->tpl_vars['product']->last = $_smarty_tpl->tpl_vars['product']->iteration === $_smarty_tpl->tpl_vars['product']->total;
?>
						<?php $_smarty_tpl->tpl_vars['mobile_template_dir'] = new Smarty_variable(@constant('_PS_THEME_MOBILE_DIR_'), null, 0);?>
						<?php $_smarty_tpl->tpl_vars['productId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['productAttributeId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product_attribute'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['quantityDisplayed'] = new Smarty_variable(0, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['cannotModify'] = new Smarty_variable(1, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['odd'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->iteration%2, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['noDeleteButton'] = new Smarty_variable(1, null, 0);?>
						
						<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['mobile_template_dir']->value)."./shopping-cart-product-line.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

						
						<?php if (isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])){?>
							<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_smarty_tpl->tpl_vars['id_customization'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value][$_smarty_tpl->tpl_vars['product']->value['id_address_delivery']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value){
$_smarty_tpl->tpl_vars['customization']->_loop = true;
 $_smarty_tpl->tpl_vars['id_customization']->value = $_smarty_tpl->tpl_vars['customization']->key;
?>
								<tr id="product_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_customization']->value;?>
" class="alternate_item cart_item">
									<td colspan="4">
										<?php  $_smarty_tpl->tpl_vars['datas'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['datas']->_loop = false;
 $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customization']->value['datas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['datas']->key => $_smarty_tpl->tpl_vars['datas']->value){
$_smarty_tpl->tpl_vars['datas']->_loop = true;
 $_smarty_tpl->tpl_vars['type']->value = $_smarty_tpl->tpl_vars['datas']->key;
?>
											<?php if ($_smarty_tpl->tpl_vars['type']->value==$_smarty_tpl->tpl_vars['CUSTOMIZE_FILE']->value){?>
												<div class="customizationUploaded">
													<ul class="customizationUploaded">
														<?php  $_smarty_tpl->tpl_vars['picture'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['picture']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['picture']->key => $_smarty_tpl->tpl_vars['picture']->value){
$_smarty_tpl->tpl_vars['picture']->_loop = true;
?>
															<li>
																<img src="<?php echo $_smarty_tpl->tpl_vars['pic_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['picture']->value['value'];?>
_small" alt="" class="customizationUploaded" />
															</li>
														<?php } ?>
													</ul>
												</div>
											<?php }elseif($_smarty_tpl->tpl_vars['type']->value==$_smarty_tpl->tpl_vars['CUSTOMIZE_TEXTFIELD']->value){?>
												<ul class="typedText">
													<?php  $_smarty_tpl->tpl_vars['textField'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['textField']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['typedText']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['textField']->key => $_smarty_tpl->tpl_vars['textField']->value){
$_smarty_tpl->tpl_vars['textField']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['typedText']['index']++;
?>
														<li>
															<?php if ($_smarty_tpl->tpl_vars['textField']->value['name']){?>
																<?php echo smartyTranslate(array('s'=>'%s:','sprintf'=>$_smarty_tpl->tpl_vars['textField']->value['name']),$_smarty_tpl);?>

															<?php }else{ ?>
																<?php echo smartyTranslate(array('s'=>'Text #%s:','sprintf'=>$_smarty_tpl->getVariable('smarty')->value['foreach']['typedText']['index']+1),$_smarty_tpl);?>

															<?php }?>
															<?php echo $_smarty_tpl->tpl_vars['textField']->value['value'];?>

														</li>
													<?php } ?>
												</ul>
											<?php }?>
										<?php } ?>
									</td>
									<td class="cart_quantity">
										<?php if (isset($_smarty_tpl->tpl_vars['cannotModify']->value)&&$_smarty_tpl->tpl_vars['cannotModify']->value==1){?>
											<span style="float:left"><?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value==0&&isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])){?><?php echo count($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value]);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['product']->value['cart_quantity']-$_smarty_tpl->tpl_vars['quantityDisplayed']->value;?>
<?php }?></span>
										<?php }else{ ?>
											<div style="float:right">
												<a rel="nofollow" class="cart_quantity_delete" id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_customization']->value;?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"delete=1&amp;id_product=".$_tmp1."&amp;ipa=".$_tmp2."&amp;id_customization=".((string)$_smarty_tpl->tpl_vars['id_customization']->value)."&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Delete this customization'),$_smarty_tpl);?>
" width="11" height="13" class="icon" /></a>
											</div>
											<div id="cart_quantity_button" style="float:left">
											<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_customization']->value;?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp3=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp4=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"add=1&amp;id_product=".$_tmp3."&amp;ipa=".$_tmp4."&amp;id_customization=".((string)$_smarty_tpl->tpl_vars['id_customization']->value)."&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/quantity_up.gif" alt="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" width="14" height="9" /></a><br />
											<?php if ($_smarty_tpl->tpl_vars['product']->value['minimal_quantity']<($_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['quantityDisplayed']->value)||$_smarty_tpl->tpl_vars['product']->value['minimal_quantity']<=1){?>
											<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_customization']->value;?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp5=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp6=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"add=1&amp;id_product=".$_tmp5."&amp;ipa=".$_tmp6."&amp;id_customization=".((string)$_smarty_tpl->tpl_vars['id_customization']->value)."&amp;op=down&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
" title="<?php echo smartyTranslate(array('s'=>'Subtract'),$_smarty_tpl);?>
">
												<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/quantity_down.gif" alt="<?php echo smartyTranslate(array('s'=>'Subtract'),$_smarty_tpl);?>
" width="14" height="9" />
											</a>
											<?php }else{ ?>
											<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_customization']->value;?>
" href="#" title="<?php echo smartyTranslate(array('s'=>'Subtract'),$_smarty_tpl);?>
">
												<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/quantity_down.gif" alt="<?php echo smartyTranslate(array('s'=>'Subtract'),$_smarty_tpl);?>
" width="14" height="9" />
											</a>
											<?php }?>
											</div>
											<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
" name="quantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_customization']->value;?>
_hidden"/>
											<input size="2" type="text" value="<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
" class="cart_quantity_input" name="quantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_customization']->value;?>
"/>
										<?php }?>
									</td>
									<td class="cart_total"></td>
								</tr>
								<?php $_smarty_tpl->tpl_vars['quantityDisplayed'] = new Smarty_variable($_smarty_tpl->tpl_vars['quantityDisplayed']->value+$_smarty_tpl->tpl_vars['customization']->value['quantity'], null, 0);?>
							<?php } ?>
							
							<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity']-$_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?><?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./shopping-cart-product-line.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }?>
						<?php }?>
					<?php } ?>
					<?php $_smarty_tpl->tpl_vars['last_was_odd'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->iteration%2, null, 0);?>
					<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['gift_products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['product']->iteration=0;
 $_smarty_tpl->tpl_vars['product']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['product']->iteration++;
 $_smarty_tpl->tpl_vars['product']->index++;
 $_smarty_tpl->tpl_vars['product']->first = $_smarty_tpl->tpl_vars['product']->index === 0;
 $_smarty_tpl->tpl_vars['product']->last = $_smarty_tpl->tpl_vars['product']->iteration === $_smarty_tpl->tpl_vars['product']->total;
?>
						<?php $_smarty_tpl->tpl_vars['productId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['productAttributeId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product_attribute'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['quantityDisplayed'] = new Smarty_variable(0, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['odd'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->iteration+$_smarty_tpl->tpl_vars['last_was_odd']->value)%2, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['ignoreProductLast'] = new Smarty_variable(isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value]), null, 0);?>
						<?php $_smarty_tpl->tpl_vars['cannotModify'] = new Smarty_variable(1, null, 0);?>
						
						<?php echo $_smarty_tpl->getSubTemplate ("./shopping-cart-product-line.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('productLast'=>$_smarty_tpl->tpl_vars['product']->last,'productFirst'=>$_smarty_tpl->tpl_vars['product']->first), 0);?>

					<?php } ?>
					</tbody>
				<?php if (count($_smarty_tpl->tpl_vars['discounts']->value)){?>
					<tbody>
					<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['discount']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['discount']->iteration=0;
 $_smarty_tpl->tpl_vars['discount']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value){
$_smarty_tpl->tpl_vars['discount']->_loop = true;
 $_smarty_tpl->tpl_vars['discount']->iteration++;
 $_smarty_tpl->tpl_vars['discount']->index++;
 $_smarty_tpl->tpl_vars['discount']->first = $_smarty_tpl->tpl_vars['discount']->index === 0;
 $_smarty_tpl->tpl_vars['discount']->last = $_smarty_tpl->tpl_vars['discount']->iteration === $_smarty_tpl->tpl_vars['discount']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['discountLoop']['first'] = $_smarty_tpl->tpl_vars['discount']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['discountLoop']['last'] = $_smarty_tpl->tpl_vars['discount']->last;
?>
						<tr class="cart_discount <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['discountLoop']['last']){?>last_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['discountLoop']['first']){?>first_item<?php }else{ ?>item<?php }?>" id="cart_discount_<?php echo $_smarty_tpl->tpl_vars['discount']->value['id_discount'];?>
">
							<td class="cart_discount_name" colspan="2"><?php echo $_smarty_tpl->tpl_vars['discount']->value['name'];?>
</td>
							<td class="cart_discount_description" colspan="3"><?php echo $_smarty_tpl->tpl_vars['discount']->value['description'];?>
</td>
							<td class="cart_discount_price">
								<span class="price-discount">
									<?php if ($_smarty_tpl->tpl_vars['discount']->value['value_real']>0){?>
										<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value){?>
											<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value_real']*-1),$_smarty_tpl);?>

										<?php }else{ ?>
											<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value_tax_exc']*-1),$_smarty_tpl);?>

										<?php }?>
									<?php }?>
								</span>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				<?php }?>
				</table>
			</div>
			<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['opc']->value){?><div id="opc_payment_methods-content"><?php }?>
					<div id="HOOK_PAYMENT"><?php echo $_smarty_tpl->tpl_vars['HOOK_PAYMENT']->value;?>
</div>
				<?php if ($_smarty_tpl->tpl_vars['opc']->value){?></div><?php }?>
			<?php }else{ ?>
				<p class="warning"><?php echo smartyTranslate(array('s'=>'No payment modules have been installed.'),$_smarty_tpl);?>
</p>
			<?php }?>
			
			<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
				<p class="cart_navigation"><a href="<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&back=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp7=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,"step=2".$_tmp7);?>
" title="<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
" data-role="button" data-icon="back" data-ajax="false">&laquo; <?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
</a></p>
			<?php }else{ ?>
				</div>
			<?php }?>
		</div>
	</div><!-- /content -->
	<div id="displayMobileShoppingCartBottom">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileShoppingCartBottom"),$_smarty_tpl);?>

	</div>
<?php }?><?php }} ?>