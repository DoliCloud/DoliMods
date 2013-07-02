<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:05
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/shopping-cart-product-line.tpl" */ ?>
<?php /*%%SmartyHeaderCode:91717465351c1c0e5bbf2a9-77497913%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '241fe7c994e88bdb9452126963c939eff584ed20' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/shopping-cart-product-line.tpl',
      1 => 1371646835,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '91717465351c1c0e5bbf2a9-77497913',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'quantityDisplayed' => 0,
    'productLast' => 0,
    'ignoreProductLast' => 0,
    'productFirst' => 0,
    'productId' => 0,
    'productAttributeId' => 0,
    'customizedDatas' => 0,
    'odd' => 0,
    'link' => 0,
    'smallSize' => 0,
    'priceDisplay' => 0,
    'cannotModify' => 0,
    'token_cart' => 0,
    'img_dir' => 0,
    'noDeleteButton' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e61b5c35_91210497',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e61b5c35_91210497')) {function content_51c1c0e61b5c35_91210497($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<tr id="product_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>nocustom<?php }else{ ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['gift'])){?>_gift<?php }?>" class="cart_item<?php if (isset($_smarty_tpl->tpl_vars['productLast']->value)&&$_smarty_tpl->tpl_vars['productLast']->value&&(!isset($_smarty_tpl->tpl_vars['ignoreProductLast']->value)||!$_smarty_tpl->tpl_vars['ignoreProductLast']->value)){?> last_item<?php }elseif(isset($_smarty_tpl->tpl_vars['productFirst']->value)&&$_smarty_tpl->tpl_vars['productFirst']->value){?> first_item<?php }?><?php if (isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])&&$_smarty_tpl->tpl_vars['quantityDisplayed']->value==0){?> alternate_item<?php }?> address_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
 <?php if ($_smarty_tpl->tpl_vars['odd']->value){?>odd<?php }else{ ?>even<?php }?>">
	<td class="cart_product">
		<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['product']->value['id_product'],$_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['category'],null,null,$_smarty_tpl->tpl_vars['product']->value['id_shop'],$_smarty_tpl->tpl_vars['product']->value['id_product_attribute']), 'htmlall', 'UTF-8');?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['id_image'],'small_default');?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'htmlall', 'UTF-8');?>
" <?php if (isset($_smarty_tpl->tpl_vars['smallSize']->value)){?>width="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['width'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['height'];?>
" <?php }?> /></a>
	</td>
	<td class="cart_description">
		<p class="s_title_block"><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['product']->value['id_product'],$_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['category'],null,null,$_smarty_tpl->tpl_vars['product']->value['id_shop'],$_smarty_tpl->tpl_vars['product']->value['id_product_attribute']), 'htmlall', 'UTF-8');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'htmlall', 'UTF-8');?>
</a></p>
		<?php if (isset($_smarty_tpl->tpl_vars['product']->value['attributes'])&&$_smarty_tpl->tpl_vars['product']->value['attributes']){?><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['product']->value['id_product'],$_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['category'],null,null,$_smarty_tpl->tpl_vars['product']->value['id_shop'],$_smarty_tpl->tpl_vars['product']->value['id_product_attribute']), 'htmlall', 'UTF-8');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['attributes'], 'htmlall', 'UTF-8');?>
</a><?php }?>
	</td>
	<td class="cart_ref"><?php if ($_smarty_tpl->tpl_vars['product']->value['reference']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['reference'], 'htmlall', 'UTF-8');?>
<?php }else{ ?>--<?php }?></td>
	<td class="cart_unit">
		<span class="price" id="product_price_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>_nocustom<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['gift'])){?>_gift<?php }?>">
			<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['gift'])){?>
				<span class="gift-icon"><?php echo smartyTranslate(array('s'=>'Gift!'),$_smarty_tpl);?>
</span>
			<?php }else{ ?>
				<?php if (isset($_smarty_tpl->tpl_vars['product']->value['is_discounted'])&&$_smarty_tpl->tpl_vars['product']->value['is_discounted']){?>
					<span style="text-decoration:line-through;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price_without_specific_price']),$_smarty_tpl);?>
</span><br />
				<?php }?>
				<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value){?>
					<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price_wt']),$_smarty_tpl);?>

				<?php }else{ ?>
					<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price']),$_smarty_tpl);?>

				<?php }?>
			<?php }?>
		</span>
	</td>
	<td class="cart_quantity"<?php if (isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])&&$_smarty_tpl->tpl_vars['quantityDisplayed']->value==0){?> style="text-align: center;"<?php }?>>
		<?php if (isset($_smarty_tpl->tpl_vars['cannotModify']->value)&&$_smarty_tpl->tpl_vars['cannotModify']->value==1){?>
			<span style="float:left">
				<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value==0&&isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])){?><?php echo count($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value]);?>

				<?php }else{ ?>
					<?php echo $_smarty_tpl->tpl_vars['product']->value['cart_quantity']-$_smarty_tpl->tpl_vars['quantityDisplayed']->value;?>

				<?php }?>
			</span>
		<?php }else{ ?>
			<?php if (isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])&&$_smarty_tpl->tpl_vars['quantityDisplayed']->value==0){?>
				<span id="cart_quantity_custom_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" ><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'];?>
</span>
			<?php }?>
			<?php if (!isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])||$_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>
				<div class="cart_quantity_button">
				<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>nocustom<?php }else{ ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp2=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php $_tmp3=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"add=1&amp;id_product=".$_tmp1."&amp;ipa=".$_tmp2."&amp;id_address_delivery=".$_tmp3."&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/quantity_up.gif" alt="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" width="14" height="9" /></a><br />
				<?php if ($_smarty_tpl->tpl_vars['product']->value['minimal_quantity']<($_smarty_tpl->tpl_vars['product']->value['cart_quantity']-$_smarty_tpl->tpl_vars['quantityDisplayed']->value)||$_smarty_tpl->tpl_vars['product']->value['minimal_quantity']<=1){?>
				<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>nocustom<?php }else{ ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp4=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp5=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php $_tmp6=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"add=1&amp;id_product=".$_tmp4."&amp;ipa=".$_tmp5."&amp;id_address_delivery=".$_tmp6."&amp;op=down&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
" title="<?php echo smartyTranslate(array('s'=>'Subtract'),$_smarty_tpl);?>
">
					<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/quantity_down.gif" alt="<?php echo smartyTranslate(array('s'=>'Subtract'),$_smarty_tpl);?>
" width="14" height="9" />
				</a>
				<?php }else{ ?>
				<a class="cart_quantity_down" style="opacity: 0.3;" href="#" id="cart_quantity_down_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>nocustom<?php }else{ ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" title="<?php echo smartyTranslate(array('s'=>'You must purchase a minimum of %d of this product.','sprintf'=>$_smarty_tpl->tpl_vars['product']->value['minimal_quantity']),$_smarty_tpl);?>
">
					<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/quantity_down.gif" width="14" height="9" alt="<?php echo smartyTranslate(array('s'=>'Subtract'),$_smarty_tpl);?>
" />
				</a>
				<?php }?>
				</div>
				<input type="hidden" value="<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value==0&&isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])){?><?php echo count($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value]);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['product']->value['cart_quantity']-$_smarty_tpl->tpl_vars['quantityDisplayed']->value;?>
<?php }?>" name="quantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>nocustom<?php }else{ ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
_hidden" />
				<input size="2" type="text" autocomplete="off" class="cart_quantity_input" value="<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value==0&&isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])){?><?php echo count($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value]);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['product']->value['cart_quantity']-$_smarty_tpl->tpl_vars['quantityDisplayed']->value;?>
<?php }?>"  name="quantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>nocustom<?php }else{ ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" />
				
			<?php }?>
		<?php }?>
	</td>
	<td class="cart_total">
		<span class="price" id="total_product_price_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>_nocustom<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['gift'])){?>_gift<?php }?>">
			<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['gift'])){?>
				<span class="gift-icon"><?php echo smartyTranslate(array('s'=>'Gift!'),$_smarty_tpl);?>
</span>
			<?php }else{ ?>
				<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value==0&&isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])){?>
					<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_customization_wt']),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_customization']),$_smarty_tpl);?>
<?php }?>
				<?php }else{ ?>
					<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_wt']),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total']),$_smarty_tpl);?>
<?php }?>
				<?php }?>
			<?php }?>
		</span>
	</td>
	<?php if (!isset($_smarty_tpl->tpl_vars['noDeleteButton']->value)||!$_smarty_tpl->tpl_vars['noDeleteButton']->value){?>
		<td class="cart_delete">
		<?php if ((!isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])||$_smarty_tpl->tpl_vars['quantityDisplayed']->value)>0&&empty($_smarty_tpl->tpl_vars['product']->value['gift'])){?>
			<div>
				<a rel="nofollow" class="cart_quantity_delete" id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0){?>nocustom<?php }else{ ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp7=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp8=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php $_tmp9=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"delete=1&amp;id_product=".$_tmp7."&amp;ipa=".$_tmp8."&amp;id_address_delivery=".$_tmp9."&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value));?>
"><?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</a>
			</div>
		<?php }?>
		</td>
	<?php }?>
</tr>
<?php }} ?>