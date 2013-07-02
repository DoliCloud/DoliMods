<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:13
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/shopping-cart.tpl" */ ?>
<?php /*%%SmartyHeaderCode:143543283451c1c0ed4c1b46-18318585%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76902ac55c519666d73442cf415cfafde27054d8' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/shopping-cart.tpl',
      1 => 1371647181,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '143543283451c1c0ed4c1b46-18318585',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'empty' => 0,
    'PS_CATALOG_MODE' => 0,
    'products' => 0,
    'product' => 0,
    'link' => 0,
    'token_cart' => 0,
    'gift_products' => 0,
    'last_was_odd' => 0,
    'productId' => 0,
    'productAttributeId' => 0,
    'customizedDatas' => 0,
    'discounts' => 0,
    'discount' => 0,
    'priceDisplay' => 0,
    'opc' => 0,
    'discount_name' => 0,
    'use_taxes' => 0,
    'display_tax_label' => 0,
    'total_products' => 0,
    'total_products_wt' => 0,
    'total_discounts' => 0,
    'total_discounts_tax_exc' => 0,
    'total_discounts_negative' => 0,
    'total_wrapping' => 0,
    'total_wrapping_tax_exc' => 0,
    'total_shipping_tax_exc' => 0,
    'virtualCart' => 0,
    'total_shipping' => 0,
    'total_price_without_tax' => 0,
    'total_tax' => 0,
    'total_price' => 0,
    'isLogged' => 0,
    'isGuest' => 0,
    'back' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ed949828_29215042',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ed949828_29215042')) {function content_51c1c0ed949828_29215042($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['empty']->value)){?>
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


		<h2><?php echo smartyTranslate(array('s'=>'List of products'),$_smarty_tpl);?>
</h2>
		<?php if (isset($_smarty_tpl->tpl_vars['products']->value)){?>
		<ul data-role="listview" data-inset="true" data-split-theme="d" data-split-icon="delete">
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
				<li id="element_product_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
">
					<a>
						<input type="hidden" name="cart_product_id[]" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
"/>
						<input type="hidden" id="cart_product_attribute_id_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
"/>
						<input type="hidden" id="cart_product_address_delivery_id_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_address_delivery'];?>
"/>

						<div class="fl width-20">
							<img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['id_image'],'small_default');?>
" class="img_product_cart" />
						</div>
						<div class="fl width-60 padding-left-5px">
							<h3><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</h3>
							<?php if ($_smarty_tpl->tpl_vars['product']->value['reference']){?><p><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['reference'];?>
</p><?php }?>
							<p><?php echo $_smarty_tpl->tpl_vars['product']->value['description_short'];?>
</p>
						</div>
						<div class="clear"></div>

						<table class="width-100">
							<thead>
								<tr>
									<td class="width-40"><?php echo smartyTranslate(array('s'=>'Unit price'),$_smarty_tpl);?>
</td>
									<td><?php echo smartyTranslate(array('s'=>'Qty'),$_smarty_tpl);?>
</td>
									<td class="width-40"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price_wt']),$_smarty_tpl);?>
</td>
									<td>
										<input
											type="number"
											class="qty-field cart_quantity_input"
											name="product_cart_quantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
"
											value="<?php echo $_smarty_tpl->tpl_vars['product']->value['cart_quantity'];?>
"
											min="0"
											max="<?php echo $_smarty_tpl->tpl_vars['product']->value['quantity_available'];?>
"
											data-mini="true"
											data-initial-quantity="<?php echo $_smarty_tpl->tpl_vars['product']->value['cart_quantity'];?>
"
											data-id-product="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
"
											data-id-product-attribute="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
" />
									</td>
									<td class="right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_wt']),$_smarty_tpl);?>
</td>
								</tr>
							</tbody>
						</table>
					</a>
					<a rel="nofollow" class="cart_quantity_delete" id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_0_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp2=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php $_tmp3=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"delete=1&amp;id_product=".$_tmp1."&amp;ipa=".$_tmp2."&amp;id_address_delivery=".$_tmp3."&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</a>
				</li>
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
				<li id="element_product_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
">
					<a style="padding-right:10px">
						<?php $_smarty_tpl->tpl_vars['productId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['productAttributeId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product_attribute'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['quantityDisplayed'] = new Smarty_variable(0, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['odd'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->iteration+$_smarty_tpl->tpl_vars['last_was_odd']->value)%2, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['ignoreProductLast'] = new Smarty_variable(isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value]), null, 0);?>
						<?php $_smarty_tpl->tpl_vars['cannotModify'] = new Smarty_variable(1, null, 0);?>
						
						<?php echo $_smarty_tpl->getSubTemplate ("./shopping-cart-gift-line.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('productLast'=>$_smarty_tpl->tpl_vars['product']->last,'productFirst'=>$_smarty_tpl->tpl_vars['product']->first), 0);?>

					</a>
					<a rel="nofollow" class="cart_quantity_delete" id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_0_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp4=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp5=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php $_tmp6=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"delete=1&amp;id_product=".$_tmp4."&amp;ipa=".$_tmp5."&amp;id_address_delivery=".$_tmp6."&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
" data-ajax="false" style="display:none"><?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</a>
				</li>
			<?php } ?>
		</ul>
		<?php }?>
		<?php if (sizeof($_smarty_tpl->tpl_vars['discounts']->value)){?>
		<h2><?php echo smartyTranslate(array('s'=>'List of vouchers'),$_smarty_tpl);?>
</h2>
		<ul data-role="listview" data-inset="true" data-split-theme="d" data-split-icon="delete">
			<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value){
$_smarty_tpl->tpl_vars['discount']->_loop = true;
?>
			<li>
				<a>
					<table class="width-100">
						<tr>
							<td><?php echo $_smarty_tpl->tpl_vars['discount']->value['name'];?>
</td>
							<td class="right">
								<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value_real']*-1),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value_tax_exc']*-1),$_smarty_tpl);?>
<?php }?>
							</td>
						</tr>
					</table>
				</a>
				<?php if (strlen($_smarty_tpl->tpl_vars['discount']->value['code'])){?><a href="<?php if ($_smarty_tpl->tpl_vars['opc']->value){?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-opc',true);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true);?>
<?php }?>?deleteDiscount=<?php echo $_smarty_tpl->tpl_vars['discount']->value['id_discount'];?>
" class="price_discount_delete" title="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</a><?php }?>
			</li>
			<?php } ?>
		</ul>
		<?php }?>
		<br />
		<div class="ui-grid-a cart_total_bar same-height">
			<div class="ui-block-a">
				<div class="ui-bar ui-bar-c">
					<h3><?php echo smartyTranslate(array('s'=>'Voucher:'),$_smarty_tpl);?>
</h3>
					<form action="<?php if ($_smarty_tpl->tpl_vars['opc']->value){?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-opc',true);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true);?>
<?php }?>" method="post" data-ajax="false">
						<input type="text" name="discount_name" id="discount_name" value="<?php if (isset($_smarty_tpl->tpl_vars['discount_name']->value)&&$_smarty_tpl->tpl_vars['discount_name']->value){?><?php echo $_smarty_tpl->tpl_vars['discount_name']->value;?>
<?php }?>"  placeholder="<?php echo smartyTranslate(array('s'=>'Voucher code:'),$_smarty_tpl);?>
" />
						<div class='btn-row'>
							<input type="hidden" name="submitDiscount" />
							<button type="submit" data-theme="a" name="submitAddDiscount" value="submit-value"><?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
</button>
						</div><!-- .btn-row -->
					</form>
				</div>
			</div>
			<div class="ui-block-b total_price">
				<div class="ui-bar ui-bar-c">
					<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
						<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
							<h3><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total products (tax excl.)'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total products'),$_smarty_tpl);?>
<?php }?></h3>
							<p><span class="price" id="total_product"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_products']->value),$_smarty_tpl);?>
</span></p>
						<?php }else{ ?>
							<h3><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total products (tax incl.)'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total products'),$_smarty_tpl);?>
<?php }?></h3>
							<p><span class="price" id="total_product"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_products_wt']->value),$_smarty_tpl);?>
</span></p>
						<?php }?>
					<?php }else{ ?>
						<h3><?php echo smartyTranslate(array('s'=>'Total products'),$_smarty_tpl);?>
</h3>
						<p><span class="price" id="total_product"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_products']->value),$_smarty_tpl);?>
</span></p>
					<?php }?>

					<div <?php if ($_smarty_tpl->tpl_vars['total_discounts']->value==0){?>class="hide"<?php }?>>
						<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value&&$_smarty_tpl->tpl_vars['display_tax_label']->value){?>
							<h3><?php echo smartyTranslate(array('s'=>'Total vouchers (tax excl.)'),$_smarty_tpl);?>
</h3>
						<?php }else{ ?>
							<h3><?php echo smartyTranslate(array('s'=>'Total vouchers'),$_smarty_tpl);?>
</h3>
						<?php }?>

						<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value&&!$_smarty_tpl->tpl_vars['priceDisplay']->value){?>
							<?php $_smarty_tpl->tpl_vars['total_discounts_negative'] = new Smarty_variable($_smarty_tpl->tpl_vars['total_discounts']->value*-1, null, 0);?>
						<?php }else{ ?>
							<?php $_smarty_tpl->tpl_vars['total_discounts_negative'] = new Smarty_variable($_smarty_tpl->tpl_vars['total_discounts_tax_exc']->value*-1, null, 0);?>
						<?php }?>
							<p><span class="price" id="total_discount"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_discounts_negative']->value),$_smarty_tpl);?>
</span></p>
					</div>

					<div <?php if ($_smarty_tpl->tpl_vars['total_wrapping']->value==0){?>class="hide"<?php }?>>
						<h3>
						<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
							<?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total gift wrapping (tax incl.):'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total gift-wrapping cost:'),$_smarty_tpl);?>
<?php }?>
						<?php }else{ ?>
							<?php echo smartyTranslate(array('s'=>'Total gift-wrapping cost:'),$_smarty_tpl);?>

						<?php }?>
						</h3>
						<p><span class="price" id="total_wrapping">
						<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
							<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping_tax_exc']->value),$_smarty_tpl);?>

							<?php }else{ ?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping']->value),$_smarty_tpl);?>

							<?php }?>
						<?php }else{ ?>
							<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping_tax_exc']->value),$_smarty_tpl);?>

						<?php }?>
						</span></p>
					</div>

					<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
						<?php if ($_smarty_tpl->tpl_vars['total_shipping_tax_exc']->value<=0&&!isset($_smarty_tpl->tpl_vars['virtualCart']->value)){?>
							<h3><?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
</h3>
							<p><span class="price" id="total_shipping"><?php echo smartyTranslate(array('s'=>'Free Shipping!'),$_smarty_tpl);?>
</span></p>
						<?php }else{ ?>
							<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value){?>
								<div <?php if ($_smarty_tpl->tpl_vars['total_shipping_tax_exc']->value<=0){?>class="hide"<?php }?>>
									<h3><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total shipping (tax excl.)'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total shipping'),$_smarty_tpl);?>
<?php }?></h3>
									<p><span class="price" id="total_shipping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_shipping_tax_exc']->value),$_smarty_tpl);?>
</span></p>
								</div>
							<?php }else{ ?>
								<div <?php if ($_smarty_tpl->tpl_vars['total_shipping']->value<=0){?>class="hide"<?php }?>>
									<h3><?php if ($_smarty_tpl->tpl_vars['display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'Total shipping (tax incl.)'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Total shipping'),$_smarty_tpl);?>
<?php }?></h3>
									<p><span class="price" id="total_shipping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_shipping']->value),$_smarty_tpl);?>
</span></p>
								</div>
							<?php }?>
						<?php }?>
					<?php }else{ ?>
						<div <?php if ($_smarty_tpl->tpl_vars['total_shipping_tax_exc']->value<=0){?>class="hide"<?php }?>>
							<h3><?php echo smartyTranslate(array('s'=>'Total shipping'),$_smarty_tpl);?>
</h3>
							<p><span class="price" id="total_shipping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_shipping_tax_exc']->value),$_smarty_tpl);?>
</span></p>
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
						<h3><?php echo smartyTranslate(array('s'=>'Total (tax excl.)'),$_smarty_tpl);?>
</h3>
						<p><span class="price" id="total_price_without_tax"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_price_without_tax']->value),$_smarty_tpl);?>
</span></p>
	
						<h3><?php echo smartyTranslate(array('s'=>'Total tax'),$_smarty_tpl);?>
</h3>
						<p><span class="price" id="total_tax"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_tax']->value),$_smarty_tpl);?>
</span></p>
					<?php }?>
					<h3><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</h3>
					<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?>
						<p><span class="price" id="total_price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_price']->value),$_smarty_tpl);?>
</span></p>
					<?php }else{ ?>
						<p><span class="price" id="total_price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_price_without_tax']->value),$_smarty_tpl);?>
</span></p>
					<?php }?>
				</div>
			</div>
		</div><!-- /grid-a -->
		<br />
		<?php if ($_smarty_tpl->tpl_vars['opc']->value&&$_smarty_tpl->tpl_vars['isLogged']->value&&!$_smarty_tpl->tpl_vars['isGuest']->value){?>
			<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('index',true);?>
" data-role="button" data-theme="a" data-icon="back" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Continue shopping'),$_smarty_tpl);?>
</a>
		<?php }else{ ?>
			<ul data-role="listview" data-inset="true" id="list_myaccount">
				<li data-theme="a" data-icon="back">
					<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('index',true);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Continue shopping'),$_smarty_tpl);?>
</a>
				</li>
				<li data-theme="b" data-icon="check">
					<a href="<?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,'step=1&amp;back={$back}');?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,'step=1');?>
<?php }?>" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Confirm order'),$_smarty_tpl);?>
</a>
				</li>
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileShoppingCartButton"),$_smarty_tpl);?>

			</ul>
		<?php }?>
		<br />
	</div><!-- /content -->
	<div id="displayMobileShoppingCartBottom">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileShoppingCartBottom"),$_smarty_tpl);?>

	</div>
<?php }?><?php }} ?>