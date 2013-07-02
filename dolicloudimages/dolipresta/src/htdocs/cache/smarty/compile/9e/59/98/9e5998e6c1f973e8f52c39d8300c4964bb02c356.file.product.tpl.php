<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:12
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/product.tpl" */ ?>
<?php /*%%SmartyHeaderCode:91974501551c1c0ec92b3e6-23878504%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9e5998e6c1f973e8f52c39d8300c4964bb02c356' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/product.tpl',
      1 => 1371647179,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '91974501551c1c0ec92b3e6-23878504',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'confirmation' => 0,
    'packItems' => 0,
    'packItem' => 0,
    'link' => 0,
    'restricted_country_mode' => 0,
    'groups' => 0,
    'PS_CATALOG_MODE' => 0,
    'static_token' => 0,
    'allow_oosp' => 0,
    'virtual' => 0,
    'quantityBackup' => 0,
    'display_qties' => 0,
    'last_qties' => 0,
    'cart_btn_class' => 0,
    'cart_btn_theme' => 0,
    'cart_btn_icon' => 0,
    'HOOK_PRODUCT_FOOTER' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ecc72868_85493637',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ecc72868_85493637')) {function content_51c1c0ecc72868_85493637($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->name, 'htmlall', 'UTF-8');?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ('./product-js.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div data-role="content" id="content" class="product">
	
	<?php if (isset($_smarty_tpl->tpl_vars['confirmation']->value)&&$_smarty_tpl->tpl_vars['confirmation']->value){?>
	<p class="confirmation">
		<?php echo $_smarty_tpl->tpl_vars['confirmation']->value;?>

	</p>
	<?php }?>
	
	<?php echo $_smarty_tpl->getSubTemplate ("./product-images.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	
	<?php if ($_smarty_tpl->tpl_vars['product']->value->description_short||count($_smarty_tpl->tpl_vars['packItems']->value)>0){?>
		<?php if ($_smarty_tpl->tpl_vars['product']->value->description_short){?>
		<div><?php echo $_smarty_tpl->tpl_vars['product']->value->description_short;?>
</div>
		<?php }?>
	<?php }?>
	<?php if (count($_smarty_tpl->tpl_vars['packItems']->value)>0){?>
		<!-- pack description-->
		<div class="short_description_pack">
			<h3><?php echo smartyTranslate(array('s'=>'Pack content'),$_smarty_tpl);?>
</h3>
			<?php  $_smarty_tpl->tpl_vars['packItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['packItem']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['packItems']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['packItem']->key => $_smarty_tpl->tpl_vars['packItem']->value){
$_smarty_tpl->tpl_vars['packItem']->_loop = true;
?>
			<div class="pack_content">
				<?php echo $_smarty_tpl->tpl_vars['packItem']->value['pack_quantity'];?>
 x <a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['packItem']->value['id_product'],$_smarty_tpl->tpl_vars['packItem']->value['link_rewrite'],$_smarty_tpl->tpl_vars['packItem']->value['category']);?>
" data-ajax="false"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['packItem']->value['name'], 'htmlall', 'UTF-8');?>
</a>
				<p><?php echo $_smarty_tpl->tpl_vars['packItem']->value['description_short'];?>
</p>
			</div>
			<?php } ?>
		</div>
	<?php }?>
	
	<?php if (($_smarty_tpl->tpl_vars['product']->value->show_price&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value))||isset($_smarty_tpl->tpl_vars['groups']->value)||$_smarty_tpl->tpl_vars['product']->value->reference){?>
	<form id="buy_block" <?php if ($_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&!isset($_smarty_tpl->tpl_vars['groups']->value)&&$_smarty_tpl->tpl_vars['product']->value->quantity>0){?>class="hidden"<?php }?> action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart');?>
" method="post" data-ajax="false">

			<!-- hidden datas -->
			<p class="hidden">
				<input type="hidden" name="token" value="<?php echo $_smarty_tpl->tpl_vars['static_token']->value;?>
" />
				<input type="hidden" name="id_product" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value->id);?>
" id="product_page_product_id" />
				<input type="hidden" name="add" value="1" />
				<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
			</p>
		<div class="clearfix">
			
			<?php echo $_smarty_tpl->getSubTemplate ("./product-attributes.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			
			<div id="product_reference" <?php if (isset($_smarty_tpl->tpl_vars['groups']->value)||!$_smarty_tpl->tpl_vars['product']->value->reference){?>style="display: none;"<?php }?>>
				<br />
				<label><?php echo smartyTranslate(array('s'=>'Reference:'),$_smarty_tpl);?>
 </label>
				<span class="editable"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->reference, 'htmlall', 'UTF-8');?>
</span>
				<br />
			</div>
			
			<!-- quantity wanted -->
			<div id="quantity_wanted_p"<?php if ((!$_smarty_tpl->tpl_vars['allow_oosp']->value&&$_smarty_tpl->tpl_vars['product']->value->quantity<=0)||$_smarty_tpl->tpl_vars['virtual']->value||!$_smarty_tpl->tpl_vars['product']->value->available_for_order||$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?> style="display: none;"<?php }?>>
				<label for="qty" class=""><?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>
</label>
				<input type="text" name="qty" id="quantity_wanted" class="text" value="<?php if (isset($_smarty_tpl->tpl_vars['quantityBackup']->value)){?><?php echo intval($_smarty_tpl->tpl_vars['quantityBackup']->value);?>
<?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['product']->value->minimal_quantity>1){?><?php echo $_smarty_tpl->tpl_vars['product']->value->minimal_quantity;?>
<?php }else{ ?>1<?php }?><?php }?>" />
			</div><!-- #quantity_wanted_p -->
			
			<!-- minimal quantity wanted -->
			<div id="minimal_quantity_wanted_p"<?php if ($_smarty_tpl->tpl_vars['product']->value->minimal_quantity<=1||!$_smarty_tpl->tpl_vars['product']->value->available_for_order||$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?> style="display: none;"<?php }?>>
				<?php echo smartyTranslate(array('s'=>'This product is not sold individually. You must select at least'),$_smarty_tpl);?>
 <b id="minimal_quantity_label"><?php echo $_smarty_tpl->tpl_vars['product']->value->minimal_quantity;?>
</b> <?php echo smartyTranslate(array('s'=>'quantity for this product.'),$_smarty_tpl);?>

			</div><!-- #minimal_quantity_wanted_p -->
			
			
			
			<!-- availability -->
			<div id="availability_statut"<?php if (($_smarty_tpl->tpl_vars['product']->value->quantity<=0&&!$_smarty_tpl->tpl_vars['product']->value->available_later&&$_smarty_tpl->tpl_vars['allow_oosp']->value)||($_smarty_tpl->tpl_vars['product']->value->quantity>0&&!$_smarty_tpl->tpl_vars['product']->value->available_now)||!$_smarty_tpl->tpl_vars['product']->value->available_for_order||$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?> style="display: none;"<?php }?>>
				<label id="availability_label" class=""><?php echo smartyTranslate(array('s'=>'Availability:'),$_smarty_tpl);?>
</label>
				<div id="availability_value"<?php if ($_smarty_tpl->tpl_vars['product']->value->quantity<=0){?> class="warning_inline"<?php }?>>
				<?php if ($_smarty_tpl->tpl_vars['product']->value->quantity<=0){?><?php if ($_smarty_tpl->tpl_vars['allow_oosp']->value){?><?php echo $_smarty_tpl->tpl_vars['product']->value->available_later;?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'This product is no longer in stock'),$_smarty_tpl);?>
<?php }?><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['product']->value->available_now;?>
<?php }?>
				</div>
			</div><!-- #availability_statut -->
			
			<!-- number of item in stock -->
			<?php if (($_smarty_tpl->tpl_vars['display_qties']->value==1&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&$_smarty_tpl->tpl_vars['product']->value->available_for_order)){?>
			<p id="pQuantityAvailable"<?php if ($_smarty_tpl->tpl_vars['product']->value->quantity<=0){?> style="display: none;"<?php }?>>
				<span id="quantityAvailable"><?php echo intval($_smarty_tpl->tpl_vars['product']->value->quantity);?>
</span>
				<span <?php if ($_smarty_tpl->tpl_vars['product']->value->quantity>1){?> style="display: none;"<?php }?> id="quantityAvailableTxt"><?php echo smartyTranslate(array('s'=>'Item in stock'),$_smarty_tpl);?>
</span>
				<span <?php if ($_smarty_tpl->tpl_vars['product']->value->quantity==1){?> style="display: none;"<?php }?> id="quantityAvailableTxtMultiple"><?php echo smartyTranslate(array('s'=>'Items in stock'),$_smarty_tpl);?>
</span>
			</p>
			<?php }?>
			
			
			
			<p class="warning_inline" id="last_quantities"<?php if (($_smarty_tpl->tpl_vars['product']->value->quantity>$_smarty_tpl->tpl_vars['last_qties']->value||$_smarty_tpl->tpl_vars['product']->value->quantity<=0)||$_smarty_tpl->tpl_vars['allow_oosp']->value||!$_smarty_tpl->tpl_vars['product']->value->available_for_order||$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?> style="display: none"<?php }?> ><?php echo smartyTranslate(array('s'=>'Warning: Last items in stock!'),$_smarty_tpl);?>
</p>
			
			
		</div><!-- .clearfix -->
		
		<?php if ($_smarty_tpl->tpl_vars['product']->value->show_price&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
			<hr class="margin_less"/>
			<?php echo $_smarty_tpl->getSubTemplate ("./product-prices.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php }else{ ?>
			<hr class="margin_bottom"/>
		<?php }?>
		<div id="displayMobileAddToCartTop">
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileAddToCartTop"),$_smarty_tpl);?>

		</div>
		<div id="add_to_cart" class="btn-row">
			<?php $_smarty_tpl->tpl_vars['cart_btn_class'] = new Smarty_variable('btn-cart', null, 0);?>
			<?php $_smarty_tpl->tpl_vars['cart_btn_icon'] = new Smarty_variable('', null, 0);?>
			<?php $_smarty_tpl->tpl_vars['cart_btn_theme'] = new Smarty_variable('e', null, 0);?>
			<?php if ((!$_smarty_tpl->tpl_vars['allow_oosp']->value&&$_smarty_tpl->tpl_vars['product']->value->quantity<=0)||!$_smarty_tpl->tpl_vars['product']->value->available_for_order||(isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)&&$_smarty_tpl->tpl_vars['restricted_country_mode']->value)||$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
				<?php $_smarty_tpl->tpl_vars['cart_btn_class'] = new Smarty_variable(($_smarty_tpl->tpl_vars['cart_btn_class']->value).(' disabled'), null, 0);?>
				<?php $_smarty_tpl->tpl_vars['cart_btn_theme'] = new Smarty_variable('c', null, 0);?>
			<?php }else{ ?>
				<?php $_smarty_tpl->tpl_vars['cart_btn_icon'] = new Smarty_variable('data-icon="plus"', null, 0);?>
			<?php }?>
			<button type="submit" data-theme="<?php echo $_smarty_tpl->tpl_vars['cart_btn_theme']->value;?>
" name="Submit" class="<?php echo $_smarty_tpl->tpl_vars['cart_btn_class']->value;?>
" value="submit-value" id="Submit" <?php echo $_smarty_tpl->tpl_vars['cart_btn_icon']->value;?>
 ><?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
</button>
		</div><!-- .btn-row -->
	</form><!-- #buy_block -->
	<?php }?>
	
	
	
	<?php echo $_smarty_tpl->getSubTemplate ("./product-quantity-discount.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	
	
	<hr/>
	<!-- description and features -->
	<?php echo $_smarty_tpl->getSubTemplate ("./product-desc-features.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	
	<?php if (isset($_smarty_tpl->tpl_vars['packItems']->value)&&count($_smarty_tpl->tpl_vars['packItems']->value)>0){?>
	<!-- pack list -->
	<hr class="margin_less"/>
	<div id="blockpack">
		<h2><?php echo smartyTranslate(array('s'=>'Pack content'),$_smarty_tpl);?>
</h2>
		<?php echo $_smarty_tpl->getSubTemplate ("./category-product-list.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('products'=>$_smarty_tpl->tpl_vars['packItems']->value), 0);?>

	</div>
<?php }?>
</div><!-- #content -->
<?php if (isset($_smarty_tpl->tpl_vars['HOOK_PRODUCT_FOOTER']->value)&&$_smarty_tpl->tpl_vars['HOOK_PRODUCT_FOOTER']->value){?><?php echo $_smarty_tpl->tpl_vars['HOOK_PRODUCT_FOOTER']->value;?>
<?php }?>
<?php }} ?>