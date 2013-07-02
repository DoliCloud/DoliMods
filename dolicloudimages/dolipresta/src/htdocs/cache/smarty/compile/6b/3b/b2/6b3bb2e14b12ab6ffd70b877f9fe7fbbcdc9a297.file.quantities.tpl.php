<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/quantities.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6470770651c1c0e38b8d71-50986857%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b3bb2e14b12ab6ffd70b877f9fe7fbbcdc9a297' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/quantities.tpl',
      1 => 1371647790,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6470770651c1c0e38b8d71-50986857',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'ps_stock_management' => 0,
    'show_quantities' => 0,
    'stock_management_active' => 0,
    'pack_quantity' => 0,
    'attributes' => 0,
    'attribute' => 0,
    'available_quantity' => 0,
    'product_designation' => 0,
    'order_out_of_stock' => 0,
    'token_preferences' => 0,
    'has_attribute' => 0,
    'languages' => 0,
    'countAttributes' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e3b40128_11180568',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e3b40128_11180568')) {function content_51c1c0e3b40128_11180568($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)){?>
	<input type="hidden" name="submitted_tabs[]" value="Quantities" />
	<h4><?php echo smartyTranslate(array('s'=>'Available quantities for sale'),$_smarty_tpl);?>
</h4>
	<div class="separation"></div>
	<?php if (!$_smarty_tpl->tpl_vars['ps_stock_management']->value){?>
		<div class="hint" style="display:block; position:auto;"><?php echo smartyTranslate(array('s'=>'The stock management is disabled'),$_smarty_tpl);?>
</div>
	<?php }else{ ?>
		<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('product_tab'=>"Quantities"), 0);?>

		<div class="hint" style="display:block; position:'auto';">
			<p><?php echo smartyTranslate(array('s'=>'This interface allows you to manage available quantities for sale for products. It also allows you to manage product combinations in the current shop.'),$_smarty_tpl);?>
</p>
			<p><?php echo smartyTranslate(array('s'=>'You can choose whether or not to use the advanced stock management system for this product.'),$_smarty_tpl);?>
</p>
			<p><?php echo smartyTranslate(array('s'=>'You can manually specify the quantities for the product/each product combination, or you can choose to automatically determine these quantities based on your stock (if advanced stock management is activated).'),$_smarty_tpl);?>
</p>
			<p><?php echo smartyTranslate(array('s'=>'In this case, quantities correspond to the real-stock quantities in the warehouses connected with the current shop, or current group of shops.'),$_smarty_tpl);?>
</p>
			<br/>
			<p><?php echo smartyTranslate(array('s'=>'For packs: If it has products that use advanced stock management, you have to specify a common warehouse for these products in the pack.'),$_smarty_tpl);?>
</p>
			<p><?php echo smartyTranslate(array('s'=>'Also, please note that when a product has combinations, its default combination will be used in stock movements.'),$_smarty_tpl);?>
</p>
		</div>
		<br />
		<?php if ($_smarty_tpl->tpl_vars['show_quantities']->value==true){?>
			<div class="warn" id="available_quantity_ajax_msg" style="display: none;"></div>
			<div class="error" id="available_quantity_ajax_error_msg" style="display: none;"></div>
			<div class="conf" id="available_quantity_ajax_success_msg" style="display: none;"></div>

			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr <?php if ($_smarty_tpl->tpl_vars['product']->value->is_virtual||$_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>style="display:none;"<?php }?> class="stockForVirtualProduct">
						<td valign="top" style="vertical-align:top;">
							<input 
								<?php if ($_smarty_tpl->tpl_vars['product']->value->advanced_stock_management==1&&$_smarty_tpl->tpl_vars['stock_management_active']->value==1){?>
									value="1" checked="checked"
								<?php }else{ ?>
									value="0"
								<?php }?> 
								<?php if ($_smarty_tpl->tpl_vars['stock_management_active']->value==0||$_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>
									disabled="disabled" 
								<?php }?> 
								type="checkbox" name="advanced_stock_management" class="advanced_stock_management" id="advanced_stock_management" />
							<label style="float:none;font-weight:normal" for="advanced_stock_management">
								<?php echo smartyTranslate(array('s'=>'I want to use the advanced stock management system for this product.'),$_smarty_tpl);?>
 
								<?php if ($_smarty_tpl->tpl_vars['stock_management_active']->value==0&&!$_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>
								&nbsp;-&nbsp;<b><?php echo smartyTranslate(array('s'=>'This requires you to enable advanced stock management.'),$_smarty_tpl);?>
</b>
								<?php }elseif($_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>
								&nbsp;-&nbsp;<b><?php echo smartyTranslate(array('s'=>'This parameter depends on the product(s) in the pack.'),$_smarty_tpl);?>
</b>
								<?php }?>
							</label>
							<br /><br />
						</td>
					</tr>
					<tr <?php if ($_smarty_tpl->tpl_vars['product']->value->is_virtual||$_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>style="display:none;"<?php }?> class="stockForVirtualProduct">
						<td valign="top" style="vertical-align:top;">
							<input 
								<?php if ($_smarty_tpl->tpl_vars['product']->value->depends_on_stock==1&&$_smarty_tpl->tpl_vars['stock_management_active']->value==1){?>
									checked="checked" 
								<?php }?> 
								<?php if ($_smarty_tpl->tpl_vars['stock_management_active']->value==0||$_smarty_tpl->tpl_vars['product']->value->advanced_stock_management==0||$_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>
									disabled="disabled" 
								<?php }?> 
								type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_1" value="1"/>
							<label style="float:none;font-weight:normal" for="depends_on_stock_1">
								<?php echo smartyTranslate(array('s'=>'Available quantities for current product and its combinations are based on warehouse stock. '),$_smarty_tpl);?>
 
								<?php if (($_smarty_tpl->tpl_vars['stock_management_active']->value==0||$_smarty_tpl->tpl_vars['product']->value->advanced_stock_management==0)&&!$_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>
								&nbsp;-&nbsp;<b><?php echo smartyTranslate(array('s'=>'This requires you to enable advanced stock management globally or for this product.'),$_smarty_tpl);?>
</b>
								<?php }elseif($_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>
								&nbsp;-&nbsp;<b><?php echo smartyTranslate(array('s'=>'This parameter depends on the product(s) in the pack.'),$_smarty_tpl);?>
</b>
								<?php }?>
							</label>
							<br /><br />
						</td>
					</tr>
				
					<tr <?php if ($_smarty_tpl->tpl_vars['product']->value->is_virtual||$_smarty_tpl->tpl_vars['product']->value->cache_is_pack){?>style="display:none;"<?php }?> class="stockForVirtualProduct">
						<td valign="top" style="vertical-align:top;">
							<input 
								<?php if ($_smarty_tpl->tpl_vars['product']->value->depends_on_stock==0||$_smarty_tpl->tpl_vars['stock_management_active']->value==0){?>
									checked="checked" 
								<?php }?> 
								type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_0" value="0"/>
							<label style="float:none;font-weight:normal" for="depends_on_stock_0">
								<?php echo smartyTranslate(array('s'=>'I want to specify available quantities manually.'),$_smarty_tpl);?>

							</label>
							<br /><br />
						</td>
					</tr>
					<?php if (isset($_smarty_tpl->tpl_vars['pack_quantity']->value)){?>
					<tr>
						<td valign="top" style="text-align:left;vertical-align:top;">
							<p><b><?php echo smartyTranslate(array('s'=>'When a product has combinations, quantities will be based on the default combination.'),$_smarty_tpl);?>
</b></p>
							<p><b><?php echo smartyTranslate(array('s'=>'Given the quantities of the products in this pack, the maximum quantity should be:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['pack_quantity']->value;?>
</b></p>
						</td>
					</tr>
					<?php }?>
					<tr>
						<td valign="top" style="text-align:left;vertical-align:top;">
							<table class="table" cellpadding="0" cellspacing="0" style="width:100%;">
									<colgroup>
										<col width="50">
										<col>
									</colgroup>
								<thead>
									<tr>
										<th><?php echo smartyTranslate(array('s'=>'Quantity'),$_smarty_tpl);?>
</th>
										<th><?php echo smartyTranslate(array('s'=>'Designation'),$_smarty_tpl);?>
</th>
									</tr>
								</thead>
								<tbody>
								<?php  $_smarty_tpl->tpl_vars['attribute'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['attributes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute']->key => $_smarty_tpl->tpl_vars['attribute']->value){
$_smarty_tpl->tpl_vars['attribute']->_loop = true;
?>
									<tr>
										<td  class="available_quantity" id="qty_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
">
											<span><?php echo $_smarty_tpl->tpl_vars['available_quantity']->value[$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']];?>
</span>
											<input type="text" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['available_quantity']->value[$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']]);?>
"/>
										</td>
										<td><?php echo $_smarty_tpl->tpl_vars['product_designation']->value[$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']];?>
</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</td>
					</tr>
					<tr id="when_out_of_stock">
						<td>
							<table style="margin-top: 15px;">
								<tbody>
									<tr>
										<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'When out of stock:'),$_smarty_tpl);?>
</label></td>
										<td style="padding-bottom:5px;">
											<ul class="listForm">
												<li>
											<input <?php if ($_smarty_tpl->tpl_vars['product']->value->out_of_stock==0){?> checked="checked" <?php }?> id="out_of_stock_1" type="radio" checked="checked" value="0" class="out_of_stock" name="out_of_stock">
											<label id="label_out_of_stock_1" class="t" for="out_of_stock_1"><?php echo smartyTranslate(array('s'=>'Deny orders'),$_smarty_tpl);?>
</label>
												</li>
												<li>
											<input <?php if ($_smarty_tpl->tpl_vars['product']->value->out_of_stock==1){?> checked="checked" <?php }?> id="out_of_stock_2" type="radio" value="1" class="out_of_stock" name="out_of_stock">
											<label id="label_out_of_stock_2" class="t" for="out_of_stock_2"><?php echo smartyTranslate(array('s'=>'Allow orders'),$_smarty_tpl);?>
</label>
											</li>
											<li>
											<input <?php if ($_smarty_tpl->tpl_vars['product']->value->out_of_stock==2){?> checked="checked" <?php }?> id="out_of_stock_3" type="radio" value="2" class="out_of_stock" name="out_of_stock">
											<label id="label_out_of_stock_3" class="t" for="out_of_stock_3">
												<?php echo smartyTranslate(array('s'=>'Default'),$_smarty_tpl);?>
:
												<?php if ($_smarty_tpl->tpl_vars['order_out_of_stock']->value==1){?>
												<i><?php echo smartyTranslate(array('s'=>'Allow orders'),$_smarty_tpl);?>
</i>
												<?php }else{ ?>
												<i><?php echo smartyTranslate(array('s'=>'Deny orders'),$_smarty_tpl);?>
</i>
												<?php }?> 
												<a class="confirm_leave" href="index.php?tab=AdminPPreferences&token=<?php echo $_smarty_tpl->tpl_vars['token_preferences']->value;?>
">
													<?php echo smartyTranslate(array('s'=>'as set in Preferences'),$_smarty_tpl);?>

												</a>
											</label>
											</li>
											</ul>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		<?php }else{ ?>
			<div class="warn">
				<p><?php echo smartyTranslate(array('s'=>'It is not possible to manage quantities when:'),$_smarty_tpl);?>
</p>
				<ul>
					<li><?php echo smartyTranslate(array('s'=>'You are currently managing all of your shops.'),$_smarty_tpl);?>
</li>
					<li><?php echo smartyTranslate(array('s'=>'You are currently managing a group of shops where quantities are not shared between every shop in this group.'),$_smarty_tpl);?>
</li>
					<li><?php echo smartyTranslate(array('s'=>'You are currently managing a shop that is in a group where quantities are shared between every shop in this group.'),$_smarty_tpl);?>
</li>
				</ul>
			</div>
		<?php }?>
	<?php }?>
	<div class="separation"></div>
	<h4><?php echo smartyTranslate(array('s'=>'Availability settings'),$_smarty_tpl);?>
</h4>
	<table cellpadding="5">
			<?php if (!$_smarty_tpl->tpl_vars['has_attribute']->value){?>
			<tr>
				<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Minimum quantity:'),$_smarty_tpl);?>
</label></td>
				<td style="padding-bottom:5px;">
					<input size="3" maxlength="6" name="minimal_quantity" id="minimal_quantity" type="text" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['product']->value->minimal_quantity)===null||$tmp==='' ? 1 : $tmp);?>
" />
					<p class="preference_description"><?php echo smartyTranslate(array('s'=>'The minimum quantity to buy this product (set to 1 to disable this feature)'),$_smarty_tpl);?>
</p>
				</td>
			</tr>
			<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['ps_stock_management']->value){?>
			<tr>
				<td class="col-left">
				<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"available_now",'type'=>"default",'multilang'=>"true"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Displayed text when in-stock:'),$_smarty_tpl);?>
</label>
				</td>
				<td style="padding-bottom:5px;" class="col-right">
						<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/input_text_lang.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('languages'=>$_smarty_tpl->tpl_vars['languages']->value,'input_value'=>$_smarty_tpl->tpl_vars['product']->value->available_now,'input_name'=>'available_now'), 0);?>

					<span class="hint" name="help_box"><?php echo smartyTranslate(array('s'=>'Forbidden characters:'),$_smarty_tpl);?>
 <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
			</td>
			</tr>
			<tr>
				<td class="col-left">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"available_later",'type'=>"default",'multilang'=>"true"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Displayed text when back-ordereding is allowed:'),$_smarty_tpl);?>
</label>
				</td>
				<td style="padding-bottom:5px;" class="col-right">
						<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/input_text_lang.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('languages'=>$_smarty_tpl->tpl_vars['languages']->value,'input_value'=>$_smarty_tpl->tpl_vars['product']->value->available_later,'input_name'=>'available_later'), 0);?>

					<span class="hint" name="help_box"><?php echo smartyTranslate(array('s'=>'Forbidden characters:'),$_smarty_tpl);?>
 <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</td>
			</tr>
			<?php if (!$_smarty_tpl->tpl_vars['countAttributes']->value){?>
				<tr>
					<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Available date:'),$_smarty_tpl);?>
</label></td>
					<td style="padding-bottom:5px;">
						<input id="available_date" name="available_date" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->available_date;?>
" class="datepicker"
							style="text-align: center;" type="text" />
						<p><?php echo smartyTranslate(array('s'=>'The available date when this product is out of stock.'),$_smarty_tpl);?>
</p>
					</td>
				</tr>
			<?php }?>
		<?php }?>
	</table>

	<script type="text/javascript">
		var quantities_ajax_success = '<?php echo smartyTranslate(array('s'=>'Data saved'),$_smarty_tpl);?>
';
		var quantities_ajax_waiting = '<?php echo smartyTranslate(array('s'=>'Saving data...'),$_smarty_tpl);?>
';
	</script>
<?php }?><?php }} ?>