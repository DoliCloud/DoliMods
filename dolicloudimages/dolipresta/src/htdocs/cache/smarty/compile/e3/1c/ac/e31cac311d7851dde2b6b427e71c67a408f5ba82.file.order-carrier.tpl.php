<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:09
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-carrier.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21750894451c1c0e9a3fac7-15929395%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e31cac311d7851dde2b6b427e71c67a408f5ba82' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-carrier.tpl',
      1 => 1371647171,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21750894451c1c0e9a3fac7-15929395',
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
    'link' => 0,
    'virtual_cart' => 0,
    'giftAllowed' => 0,
    'cart' => 0,
    'empty' => 0,
    'PS_CATALOG_MODE' => 0,
    'multi_shipping' => 0,
    'carriers' => 0,
    'HOOK_BEFORECARRIER' => 0,
    'isVirtualCart' => 0,
    'recyclablePackAllowed' => 0,
    'recyclable' => 0,
    'delivery_option_list' => 0,
    'id_address' => 0,
    'address_collection' => 0,
    'option_list' => 0,
    'key' => 0,
    'delivery_option' => 0,
    'option' => 0,
    'carrier' => 0,
    'free_shipping' => 0,
    'use_taxes' => 0,
    'cookie' => 0,
    'product' => 0,
    'HOOK_EXTRACARRIER_ADDR' => 0,
    'address' => 0,
    'gift_wrapping_price' => 0,
    'priceDisplay' => 0,
    'total_wrapping_tax_exc_cost' => 0,
    'total_wrapping_cost' => 0,
    'conditions' => 0,
    'cms_id' => 0,
    'checkedTOS' => 0,
    'link_conditions' => 0,
    'back' => 0,
    'back_order_page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e9ec1d37_57380802',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e9ec1d37_57380802')) {function content_51c1c0e9ec1d37_57380802($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><?php if ($_smarty_tpl->tpl_vars['opc']->value){?>
	<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order-opc.php", null, 0);?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order.php", null, 0);?>
<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
	<script type="text/javascript">
		// <![CDATA[
		var orderProcess = 'order';
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
		var orderUrl = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink("order",true);?>
';
	
		var msg = "<?php echo smartyTranslate(array('s'=>'You must agree to the terms of service before continuing.','js'=>1),$_smarty_tpl);?>
";
		
		function acceptCGV()
		{
			if ($('#cgv').length && !$('input#cgv:checked').length)
			{
				alert(msg);
				return false;
			}
			else
				return true;
		}
		
		//]]>
	</script>
<?php }else{ ?>
	<script type="text/javascript">
		var txtFree = "<?php echo smartyTranslate(array('s'=>'Free'),$_smarty_tpl);?>
";
	</script>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['virtual_cart']->value)&&!$_smarty_tpl->tpl_vars['virtual_cart']->value&&$_smarty_tpl->tpl_vars['giftAllowed']->value&&$_smarty_tpl->tpl_vars['cart']->value->gift==1){?>
<script type="text/javascript">

// <![CDATA[
	$('document').ready( function(){
		if ($('input#gift').is(':checked'))
			$('p#gift_div').show();
	});
//]]>

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


		<h2><?php echo smartyTranslate(array('s'=>'Delivery methods'),$_smarty_tpl);?>
</h2>
		<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
			<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('shipping', null, 0);?>
			<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			
			<form id="form" action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,"multi-shipping=".((string)$_smarty_tpl->tpl_vars['multi_shipping']->value));?>
" method="post" onsubmit="return acceptCGV();" data-ajax="false">
		<?php }else{ ?>
			<div id="opc_delivery_methods" class="opc-main-block">
			<div id="opc_delivery_methods-overlay" class="opc-overlay" style="display: none;"></div>
		<?php }?>


		<div class="order_carrier_content">
		<?php if (isset($_smarty_tpl->tpl_vars['virtual_cart']->value)&&$_smarty_tpl->tpl_vars['virtual_cart']->value){?>
			<input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
		<?php }else{ ?>
			
			<div id="HOOK_BEFORECARRIER">
				<?php if (isset($_smarty_tpl->tpl_vars['carriers']->value)&&isset($_smarty_tpl->tpl_vars['HOOK_BEFORECARRIER']->value)){?>
					<?php echo $_smarty_tpl->tpl_vars['HOOK_BEFORECARRIER']->value;?>

				<?php }?>
			</div>
			<?php if (isset($_smarty_tpl->tpl_vars['isVirtualCart']->value)&&$_smarty_tpl->tpl_vars['isVirtualCart']->value){?>
				<p class="warning"><?php echo smartyTranslate(array('s'=>'No carrier is needed for this order.'),$_smarty_tpl);?>
</p>
			<?php }else{ ?>
				<?php if ($_smarty_tpl->tpl_vars['recyclablePackAllowed']->value){?>
					<p class="checkbox">
						<input type="checkbox" name="recyclable" id="recyclable" value="1" <?php if ($_smarty_tpl->tpl_vars['recyclable']->value==1){?>checked="checked"<?php }?> />
						<label for="recyclable"><?php echo smartyTranslate(array('s'=>'I would like to receive my order in recycled packaging.'),$_smarty_tpl);?>
.</label>
					</p>
				<?php }?>
			<div class="delivery_options_address">
			<?php if (isset($_smarty_tpl->tpl_vars['delivery_option_list']->value)){?>
				<?php  $_smarty_tpl->tpl_vars['option_list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option_list']->_loop = false;
 $_smarty_tpl->tpl_vars['id_address'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['delivery_option_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option_list']->key => $_smarty_tpl->tpl_vars['option_list']->value){
$_smarty_tpl->tpl_vars['option_list']->_loop = true;
 $_smarty_tpl->tpl_vars['id_address']->value = $_smarty_tpl->tpl_vars['option_list']->key;
?>
					<label id="delivery_option">
						<?php if (isset($_smarty_tpl->tpl_vars['address_collection']->value[$_smarty_tpl->tpl_vars['id_address']->value])){?>
							<?php echo smartyTranslate(array('s'=>'Choose a shipping option for this address:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['address_collection']->value[$_smarty_tpl->tpl_vars['id_address']->value]->alias;?>

						<?php }else{ ?>
							<?php echo smartyTranslate(array('s'=>'Choose a shipping option'),$_smarty_tpl);?>

						<?php }?>
					</label>
					<div class="delivery_options">
					<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['option_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['option']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['option']->key;
 $_smarty_tpl->tpl_vars['option']->index++;
?>
						<div class="delivery_option <?php if (($_smarty_tpl->tpl_vars['option']->index%2)){?>alternate_<?php }?>item">
							<input class="delivery_option_radio" type="radio" name="delivery_option[<?php echo $_smarty_tpl->tpl_vars['id_address']->value;?>
]" onchange="<?php if ($_smarty_tpl->tpl_vars['opc']->value){?>updateCarrierSelectionAndGift();<?php }else{ ?>updateExtraCarrier('<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
', <?php echo $_smarty_tpl->tpl_vars['id_address']->value;?>
);<?php }?>" id="delivery_option_<?php echo $_smarty_tpl->tpl_vars['id_address']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['option']->index;?>
" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if (isset($_smarty_tpl->tpl_vars['delivery_option']->value[$_smarty_tpl->tpl_vars['id_address']->value])&&$_smarty_tpl->tpl_vars['delivery_option']->value[$_smarty_tpl->tpl_vars['id_address']->value]==$_smarty_tpl->tpl_vars['key']->value){?>checked="checked"<?php }?> />
							<label for="delivery_option_<?php echo $_smarty_tpl->tpl_vars['id_address']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['option']->index;?>
">
								<div class="ui-grid-a">
									<span class="resume ui-block-a">
										<div class="ui-grid-b">
											<p class="delivery_option_logo ui-block-a">
												<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['option']->value['carrier_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['carrier']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['carrier']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value){
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
 $_smarty_tpl->tpl_vars['carrier']->iteration++;
 $_smarty_tpl->tpl_vars['carrier']->last = $_smarty_tpl->tpl_vars['carrier']->iteration === $_smarty_tpl->tpl_vars['carrier']->total;
?>
													<?php if ($_smarty_tpl->tpl_vars['carrier']->value['logo']){?>
														<img src="<?php echo $_smarty_tpl->tpl_vars['carrier']->value['logo'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->name;?>
"/>
													<?php }elseif(!$_smarty_tpl->tpl_vars['option']->value['unique_carrier']){?>
														<?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->name;?>

														<?php if (!$_smarty_tpl->tpl_vars['carrier']->last){?> - <?php }?>
													<?php }?>
												<?php } ?>
											</p>
											<div class="ui-block-b" style="padding-left:4px;">
											<?php if ($_smarty_tpl->tpl_vars['option']->value['unique_carrier']){?>
												<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['option']->value['carrier_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['carrier']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['carrier']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value){
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
 $_smarty_tpl->tpl_vars['carrier']->iteration++;
 $_smarty_tpl->tpl_vars['carrier']->last = $_smarty_tpl->tpl_vars['carrier']->iteration === $_smarty_tpl->tpl_vars['carrier']->total;
?>
													<div class="delivery_option_title"><?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->name;?>
</div>
												<?php } ?>
											<?php }?>
											</div>
											<div class="ui-block-c">
												<div class="delivery_option_price">
													<?php if ($_smarty_tpl->tpl_vars['option']->value['total_price_with_tax']&&!$_smarty_tpl->tpl_vars['free_shipping']->value){?>
														<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value==1){?>
															<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['option']->value['total_price_with_tax']),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>

														<?php }else{ ?>
															<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['option']->value['total_price_without_tax']),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>

														<?php }?>
													<?php }else{ ?>
														<?php echo smartyTranslate(array('s'=>'Free'),$_smarty_tpl);?>

													<?php }?>
												</div>
											</div>
										</div>
									</span>
									<span class="delivery_option_carrier_desc ui-block-b <?php if (isset($_smarty_tpl->tpl_vars['delivery_option']->value[$_smarty_tpl->tpl_vars['id_address']->value])&&$_smarty_tpl->tpl_vars['delivery_option']->value[$_smarty_tpl->tpl_vars['id_address']->value]==$_smarty_tpl->tpl_vars['key']->value){?>selected<?php }?> <?php if ($_smarty_tpl->tpl_vars['option']->value['unique_carrier']){?>not-displayable<?php }?>">
										<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['option']->value['carrier_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['carrier']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['carrier']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value){
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
 $_smarty_tpl->tpl_vars['carrier']->iteration++;
 $_smarty_tpl->tpl_vars['carrier']->last = $_smarty_tpl->tpl_vars['carrier']->iteration === $_smarty_tpl->tpl_vars['carrier']->total;
?>
										<tr>
											<?php if (!$_smarty_tpl->tpl_vars['option']->value['unique_carrier']){?>
											<td class="first_item">
											<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->id;?>
" name="id_carrier" />
												<?php if ($_smarty_tpl->tpl_vars['carrier']->value['logo']){?>
													<img src="<?php echo $_smarty_tpl->tpl_vars['carrier']->value['logo'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->name;?>
"/>
												<?php }?>
											</td>
											<td>
												<?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->name;?>

											</td>
											<?php }?>
											<td <?php if ($_smarty_tpl->tpl_vars['option']->value['unique_carrier']){?>class="first_item" colspan="2"<?php }?>>
												<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->id;?>
" name="id_carrier" />
												<?php if (isset($_smarty_tpl->tpl_vars['carrier']->value['instance']->delay[$_smarty_tpl->tpl_vars['cookie']->value->id_lang])){?>
													<?php echo $_smarty_tpl->tpl_vars['carrier']->value['instance']->delay[$_smarty_tpl->tpl_vars['cookie']->value->id_lang];?>
<br />
													<?php if (count($_smarty_tpl->tpl_vars['carrier']->value['product_list'])<=1){?>
														(<?php echo smartyTranslate(array('s'=>'Product concerned:'),$_smarty_tpl);?>

													<?php }else{ ?>
														(<?php echo smartyTranslate(array('s'=>'Products concerned:'),$_smarty_tpl);?>

													<?php }?>
													
													<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carrier']->value['product_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['product']->iteration=0;
 $_smarty_tpl->tpl_vars['product']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['product']->iteration++;
 $_smarty_tpl->tpl_vars['product']->index++;
 $_smarty_tpl->tpl_vars['product']->last = $_smarty_tpl->tpl_vars['product']->iteration === $_smarty_tpl->tpl_vars['product']->total;
?>
													<?php if ($_smarty_tpl->tpl_vars['product']->index==4){?><acronym title="<?php }?><?php if ($_smarty_tpl->tpl_vars['product']->index>=4){?><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
<?php if (!$_smarty_tpl->tpl_vars['product']->last){?>, <?php }else{ ?>">...</acronym>)<?php }?><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
<?php if (!$_smarty_tpl->tpl_vars['product']->last){?>, <?php }else{ ?>)<?php }?><?php }?><?php } ?>
												<?php }?>
											</td>
										</tr>
									<?php } ?>
									</span>
								</div>
							</label>
						</div>
					<?php } ?>
					</div>
					<div class="hook_extracarrier" id="HOOK_EXTRACARRIER_<?php echo $_smarty_tpl->tpl_vars['id_address']->value;?>
"><?php if (isset($_smarty_tpl->tpl_vars['HOOK_EXTRACARRIER_ADDR']->value)&&isset($_smarty_tpl->tpl_vars['HOOK_EXTRACARRIER_ADDR']->value[$_smarty_tpl->tpl_vars['id_address']->value])){?><?php echo $_smarty_tpl->tpl_vars['HOOK_EXTRACARRIER_ADDR']->value[$_smarty_tpl->tpl_vars['id_address']->value];?>
<?php }?></div>
					<?php }
if (!$_smarty_tpl->tpl_vars['option_list']->_loop) {
?>
					<p class="warning" id="noCarrierWarning">
						<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cart']->value->getDeliveryAddressesWithoutCarriers(true); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['address']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['address']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
 $_smarty_tpl->tpl_vars['address']->iteration++;
 $_smarty_tpl->tpl_vars['address']->last = $_smarty_tpl->tpl_vars['address']->iteration === $_smarty_tpl->tpl_vars['address']->total;
?>
							<?php if (empty($_smarty_tpl->tpl_vars['address']->value->alias)){?>
								<?php echo smartyTranslate(array('s'=>'No carriers available.'),$_smarty_tpl);?>

							<?php }else{ ?>
								<?php echo smartyTranslate(array('s'=>'No carriers available for the address "%s".','sprintf'=>$_smarty_tpl->tpl_vars['address']->value->alias),$_smarty_tpl);?>

							<?php }?>
							<?php if (!$_smarty_tpl->tpl_vars['address']->last){?>
							<br />
							<?php }?>
						<?php } ?>
					</p>
				<?php } ?>
			<?php }?>
			
			</div>
			<div style="display: none;" id="extra_carrier"></div>
			
				<?php if ($_smarty_tpl->tpl_vars['giftAllowed']->value){?>
				<h3 class="gift_title"><?php echo smartyTranslate(array('s'=>'Gift'),$_smarty_tpl);?>
</h3>
				<p class="checkbox">
					<input type="checkbox" name="gift" id="gift" value="1" <?php if ($_smarty_tpl->tpl_vars['cart']->value->gift==1){?>checked="checked"<?php }?> />
					<label for="gift"><?php echo smartyTranslate(array('s'=>'I would like my order to be gift wrapped.'),$_smarty_tpl);?>
</label>
					<br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php if ($_smarty_tpl->tpl_vars['gift_wrapping_price']->value>0){?>
						(<?php echo smartyTranslate(array('s'=>'Additional cost of'),$_smarty_tpl);?>

						<span class="price" id="gift-price">
							<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value==1){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping_tax_exc_cost']->value),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping_cost']->value),$_smarty_tpl);?>
<?php }?>
						</span>
						<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value){?><?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value==1){?> <?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>
<?php }else{ ?> <?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>
<?php }?><?php }?>)
					<?php }?>
				</p>
				<p id="gift_div" class="textarea">
					<label for="gift_message"><?php echo smartyTranslate(array('s'=>'If you\'d like, you can add a note to the gift:'),$_smarty_tpl);?>
</label>
					<textarea rows="5" cols="35" id="gift_message" name="gift_message"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['cart']->value->gift_message, 'htmlall', 'UTF-8');?>
</textarea>
				</p>
				<?php }?>
			<?php }?>
		<?php }?>
		
		<?php if ($_smarty_tpl->tpl_vars['conditions']->value&&$_smarty_tpl->tpl_vars['cms_id']->value){?>
			<h3 class="condition_title"><?php echo smartyTranslate(array('s'=>'Terms of service'),$_smarty_tpl);?>
</h3>
			<p class="checkbox">
				<input type="checkbox" name="cgv" id="cgv" value="1" <?php if ($_smarty_tpl->tpl_vars['checkedTOS']->value){?>checked="checked"<?php }?> />
				<label for="cgv"><?php echo smartyTranslate(array('s'=>'I agree to the Terms of Service and will adhere to them unconditionally.'),$_smarty_tpl);?>
</label> <a href="<?php echo $_smarty_tpl->tpl_vars['link_conditions']->value;?>
" class="iframe"><?php echo smartyTranslate(array('s'=>'(Read the Terms of Service)'),$_smarty_tpl);?>
</a>
			</p>
			<script type="text/javascript">$('a.iframe').fancybox();</script>
		<?php }?>
		</div>


		<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
			<fieldset class="cart_navigation submit ui-grid-a">
				<input type="hidden" class="hidden" name="step" value="3" />
				<input type="hidden" name="back" value="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
" />
				<div class="ui-block-a"><a href="<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&back=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink($_smarty_tpl->tpl_vars['back_order_page']->value,true,null,"step=1".$_tmp1);?>
" title="<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
" data-role="button" data-icon="back" data-ajax="false">&laquo; <?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
</a></div>
				<div class="ui-block-b"><input type="submit" name="processCarrier" value="<?php echo smartyTranslate(array('s'=>'Next'),$_smarty_tpl);?>
" class="exclusive" data-icon="check" data-iconpos="right" data-theme="b" data-ajax="false" /></div>
			</fieldset>
		</form>
		<?php }else{ ?>
		</div>
		<?php }?>
	</div><!-- /content -->
	<div id="displayMobileShoppingCartBottom">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileShoppingCartBottom"),$_smarty_tpl);?>

	</div>
<?php }?><?php }} ?>