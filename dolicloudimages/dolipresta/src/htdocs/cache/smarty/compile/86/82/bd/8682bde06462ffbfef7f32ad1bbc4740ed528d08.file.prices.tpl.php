<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/prices.tpl" */ ?>
<?php /*%%SmartyHeaderCode:136998777951c1c0e344db41-80939997%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8682bde06462ffbfef7f32ad1bbc4740ed528d08' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/prices.tpl',
      1 => 1371647791,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '136998777951c1c0e344db41-80939997',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'ecotax_tax_excl' => 0,
    'currency' => 0,
    'product' => 0,
    'tax_exclude_taxe_option' => 0,
    'tax_rules_groups' => 0,
    'tax_rules_group' => 0,
    'taxesRatesByGroup' => 0,
    'ecotaxTaxRate' => 0,
    'ps_use_ecotax' => 0,
    'country_display_tax_label' => 0,
    'unit_price' => 0,
    'ps_tax' => 0,
    'specificPriceModificationForm' => 0,
    'combinations' => 0,
    'combination' => 0,
    'multi_shop' => 0,
    'admin_one_shop' => 0,
    'shops' => 0,
    'shop' => 0,
    'currencies' => 0,
    'curr' => 0,
    'countries' => 0,
    'country' => 0,
    'groups' => 0,
    'group' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e38b13b9_58923737',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e38b13b9_58923737')) {function content_51c1c0e38b13b9_58923737($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<script type="text/javascript">
var Customer = new Object();
var product_url = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts',true);?>
';
var ecotax_tax_excl = parseFloat(<?php echo $_smarty_tpl->tpl_vars['ecotax_tax_excl']->value;?>
);
$(document).ready(function () {
	Customer = {
		"hiddenField": jQuery('#id_customer'),
		"field": jQuery('#customer'),
		"container": jQuery('#customers'),
		"loader": jQuery('#customerLoader'),
		"init": function() {
			jQuery(Customer.field).typeWatch({
				"captureLength": 1,
				"highlight": true,
				"wait": 50,
				"callback": Customer.search
			}).focus(Customer.placeholderIn).blur(Customer.placeholderOut);
		},
		"placeholderIn": function() {
			if (this.value == '<?php echo smartyTranslate(array('s'=>'All customers'),$_smarty_tpl);?>
') {
				this.value = '';
			}
		},
		"placeholderOut": function() {
			if (this.value == '') {
				this.value = '<?php echo smartyTranslate(array('s'=>'All customers'),$_smarty_tpl);?>
';
			}
		},
		"search": function()
		{
			Customer.showLoader();
			jQuery.ajax({
				"type": "POST",
				"url": "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers');?>
",
				"async": true,
				"dataType": "json",
				"data": {
					"ajax": "1",
					"token": "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
",
					"tab": "AdminCustomers",
					"action": "searchCustomers",
					"customer_search": Customer.field.val()
				},
				"success": Customer.success
			});
		},
		"success": function(result)
		{
			if(result.found) {
				var html = '<ul class="clearfix">';
				jQuery.each(result.customers, function() {
					html += '<li><a class="fancybox" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers');?>
&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1">'+this.firstname+' '+this.lastname+'</a>'+(this.birthday ? ' - '+this.birthday:'')+'<br/>';
					html += '<a href="mailto:'+this.email+'">'+this.email+'</a><br />';
					html += '<a onclick="Customer.select('+this.id_customer+', \''+this.firstname+' '+this.lastname+'\'); return false;" href="#" class="button"><?php echo smartyTranslate(array('s'=>'Choose'),$_smarty_tpl);?>
</a></li>';
				});
				html += '</ul>';
			}
			else
				html = '<div class="warn"><?php echo smartyTranslate(array('s'=>'No customers found'),$_smarty_tpl);?>
</div>';
			Customer.hideLoader();
			Customer.container.html(html);
			jQuery('.fancybox', Customer.container).fancybox();
		},
		"select": function(id_customer, fullname)
		{
			Customer.hiddenField.val(id_customer);
			Customer.field.val(fullname);
			Customer.container.empty();
			return false;
		},
		"showLoader": function() {
			Customer.loader.fadeIn();
		},
		"hideLoader": function() {
			Customer.loader.fadeOut();
		}
	};
	Customer.init();
});
</script>


<input type="hidden" name="submitted_tabs[]" value="Prices" />
<h4><?php echo smartyTranslate(array('s'=>'Product price'),$_smarty_tpl);?>
</h4>
<div class="hint" style="display:block;min-height:0;">
	<?php echo smartyTranslate(array('s'=>'You must enter either the pre-tax retail price, or the retail price with tax. The input field will be automatically calculated.'),$_smarty_tpl);?>

</div>

<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('product_tab'=>"Prices"), 0);?>


<div class="separation"></div>
<table>
	<tr>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"wholesale_price",'type'=>"default"), 0);?>

			<label><?php echo smartyTranslate(array('s'=>'Pre-tax wholesale price:'),$_smarty_tpl);?>
</label>
		</td>
		<td style="padding-bottom:5px;">
			<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<input size="11" maxlength="14" name="wholesale_price" id="wholesale_price" type="text" value="<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value->wholesale_price),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php echo sprintf('%.2f',$_tmp1);?>
" onchange="this.value = this.value.replace(/,/g, '.');" /><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

			<p class="preference_description"><?php echo smartyTranslate(array('s'=>'Wholesale price'),$_smarty_tpl);?>
</p>
		</td>
	</tr>

	<tr>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"price",'type'=>"price"), 0);?>

			<label><?php echo smartyTranslate(array('s'=>'Pre-tax retail price:'),$_smarty_tpl);?>
</label>
		</td>
		<td style="padding-bottom:5px;">
			<input type="hidden"  id="priceTEReal" name="price" value="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value->price),$_smarty_tpl);?>
" />
			<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<input size="11" maxlength="14" id="priceTE" name="price_displayed" type="text" value="<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value->price),$_smarty_tpl);?>
<?php $_tmp2=ob_get_clean();?><?php echo sprintf('%.2f',$_tmp2);?>
" onchange="noComma('priceTE'); $('#priceTEReal').val(this.value);" onkeyup="$('#priceType').val('TE'); $('#priceTEReal').val(this.value.replace(/,/g, '.')); if (isArrowKey(event)) return; calcPriceTI();" /><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

			<p class="preference_description"><?php echo smartyTranslate(array('s'=>'The pre-tax retail price to sell this product'),$_smarty_tpl);?>
</p>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"id_tax_rules_group",'type'=>"default"), 0);?>

			<label><?php echo smartyTranslate(array('s'=>'Tax rule:'),$_smarty_tpl);?>
</label>
		</td>
		<td style="padding-bottom:5px;">
			<script type="text/javascript">
				noTax = <?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value){?>true<?php }else{ ?>false<?php }?>;
				taxesArray = new Array ();
				taxesArray[0] = 0;
				<?php  $_smarty_tpl->tpl_vars['tax_rules_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tax_rules_group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tax_rules_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tax_rules_group']->key => $_smarty_tpl->tpl_vars['tax_rules_group']->value){
$_smarty_tpl->tpl_vars['tax_rules_group']->_loop = true;
?>
					<?php if (isset($_smarty_tpl->tpl_vars['taxesRatesByGroup']->value[$_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group']])){?>
					taxesArray[<?php echo $_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group'];?>
] = <?php echo $_smarty_tpl->tpl_vars['taxesRatesByGroup']->value[$_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group']];?>
;
						<?php }else{ ?>
					taxesArray[<?php echo $_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group'];?>
] = 0;
					<?php }?>
				<?php } ?>
				ecotaxTaxRate = <?php echo $_smarty_tpl->tpl_vars['ecotaxTaxRate']->value/100;?>
;
			</script>

			<span <?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value){?>style="display:none;"<?php }?> >
				 <select onChange="javascript:calcPrice(); unitPriceWithTax('unit');" name="id_tax_rules_group" id="id_tax_rules_group" <?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value){?>disabled="disabled"<?php }?> >
					<option value="0"><?php echo smartyTranslate(array('s'=>'No Tax'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['tax_rules_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tax_rules_group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tax_rules_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tax_rules_group']->key => $_smarty_tpl->tpl_vars['tax_rules_group']->value){
$_smarty_tpl->tpl_vars['tax_rules_group']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group'];?>
" <?php if ($_smarty_tpl->tpl_vars['product']->value->getIdTaxRulesGroup()==$_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group']){?>selected="selected"<?php }?> >
							<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['tax_rules_group']->value['name']);?>

						</option>
					<?php } ?>
				</select>
				<a class="button" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminTaxRulesGroup'), 'htmlall', 'UTF-8');?>
&addtax_rules_group&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
" class="confirm_leave">
				<img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Create'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Create'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Create'),$_smarty_tpl);?>

				</a>
			</span>
			<?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value){?>
				<span style="margin-left:10px; color:red;"><?php echo smartyTranslate(array('s'=>'Taxes are currently disabled'),$_smarty_tpl);?>
</span> (<b><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminTaxes'), 'htmlall', 'UTF-8');?>
"><?php echo smartyTranslate(array('s'=>'Tax options'),$_smarty_tpl);?>
</a></b>)
				<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->getIdTaxRulesGroup();?>
" name="id_tax_rules_group" />
			<?php }?>
		</td>
	</tr>
	<tr <?php if (!$_smarty_tpl->tpl_vars['ps_use_ecotax']->value){?> style="display:none;"<?php }?>>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"ecot",'type'=>"default"), 0);?>

			<label><?php echo smartyTranslate(array('s'=>'Eco-tax (tax incl.):'),$_smarty_tpl);?>
</label>
		</td>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<input size="11" maxlength="14" id="ecotax" name="ecotax" type="text" value="<?php echo sprintf('%.2f',$_smarty_tpl->tpl_vars['product']->value->ecotax);?>
" onkeyup="$('#priceType').val('TI');if (isArrowKey(event))return; calcPriceTE(); this.value = this.value.replace(/,/g, '.'); if (parseInt(this.value) > getE('priceTE').value) this.value = getE('priceTE').value; if (isNaN(this.value)) this.value = 0;" /><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

			<span style="margin-left:10px">(<?php echo smartyTranslate(array('s'=>'already included in price'),$_smarty_tpl);?>
)</span>
		</td>
	</tr>
	<tr <?php if (!$_smarty_tpl->tpl_vars['country_display_tax_label']->value||$_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value){?>style="display:none"<?php }?> >
		<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Retail price with tax:'),$_smarty_tpl);?>
</label></td>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<input size="11" maxlength="14" id="priceTI" type="text" value="" onchange="noComma('priceTI');" onkeyup="$('#priceType').val('TI');if (isArrowKey(event)) return;  calcPriceTE();" /><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

			<input id="priceType" name="priceType" type="hidden" value="TE" />
		</td>
	</tr>
	<tr id="tr_unit_price">
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"unit_price",'type'=>"unit_price"), 0);?>

			<label><?php echo smartyTranslate(array('s'=>'Unit price:'),$_smarty_tpl);?>
</label>
		</td>
		<td>
			<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
 <input size="11" maxlength="14" id="unit_price" name="unit_price" type="text" value="<?php echo sprintf('%.2f',$_smarty_tpl->tpl_vars['unit_price']->value);?>
"
				onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); unitPriceWithTax('unit');"/><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

			<?php echo smartyTranslate(array('s'=>'per'),$_smarty_tpl);?>
&nbsp;<input size="6" maxlength="10" id="unity" name="unity" type="text" value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->unity);?>
" onkeyup="if (isArrowKey(event)) return ;unitySecond();" onchange="unitySecond();"/>
			<?php if ($_smarty_tpl->tpl_vars['ps_tax']->value&&$_smarty_tpl->tpl_vars['country_display_tax_label']->value){?>
				<span style="margin-left:15px"><?php echo smartyTranslate(array('s'=>'or'),$_smarty_tpl);?>

					<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<span id="unit_price_with_tax">0.00</span><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

					<?php echo smartyTranslate(array('s'=>'per'),$_smarty_tpl);?>
 <span id="unity_second"><?php echo $_smarty_tpl->tpl_vars['product']->value->unity;?>
</span> <?php echo smartyTranslate(array('s'=>'with tax'),$_smarty_tpl);?>

				</span>
			<?php }?>
			<p><?php echo smartyTranslate(array('s'=>'e.g. per lb.'),$_smarty_tpl);?>
</p>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"on_sale",'type'=>"default"), 0);?>

			<label>&nbsp;</label>
		</td>
		<td>
			<input type="checkbox" name="on_sale" id="on_sale" style="padding-top: 5px;" <?php if ($_smarty_tpl->tpl_vars['product']->value->on_sale){?>checked="checked"<?php }?> value="1" />&nbsp;<label for="on_sale" class="t"><?php echo smartyTranslate(array('s'=>'Display the "on sale" icon on the product page, and in the text found within the product listing.'),$_smarty_tpl);?>
</label>
		</td>
	</tr>
	<tr>
		<td class="col-left"><label><b><?php echo smartyTranslate(array('s'=>'Final retail price:'),$_smarty_tpl);?>
</b></label></td>
		<td>
			<span <?php if (!$_smarty_tpl->tpl_vars['country_display_tax_label']->value){?>style="display:none"<?php }?> >
			<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<span id="finalPrice" style="font-weight: bold;">0.00</span><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
<span <?php if ($_smarty_tpl->tpl_vars['ps_tax']->value){?>style="display:none;"<?php }?>> (<?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
)</span>
			</span>
			<span <?php if ($_smarty_tpl->tpl_vars['ps_tax']->value){?>style="display:none;"<?php }?> >

			<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value){?>
				 /
			<?php }?>
			<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<span id="finalPriceWithoutTax" style="font-weight: bold;"></span><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
 <?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value){?>(<?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
)<?php }?></span>
		</td>
	</tr>
</table>
<div class="separation"></div>

<?php if (isset($_smarty_tpl->tpl_vars['specificPriceModificationForm']->value)){?>
	<h4><?php echo smartyTranslate(array('s'=>'Specific prices'),$_smarty_tpl);?>
</h4>
	<div class="hint" style="display:block;min-height:0;">
		<?php echo smartyTranslate(array('s'=>'You can set specific prices for clients belonging to different groups, different countries, etc...'),$_smarty_tpl);?>

	</div>
	<br />
	<a class="button bt-icon" href="#" id="show_specific_price"><img src="../img/admin/add.gif" alt="" /><span><?php echo smartyTranslate(array('s'=>'Add a new specific price'),$_smarty_tpl);?>
</span></a>
	<a class="button bt-icon" href="#" id="hide_specific_price" style="display:none"><img src="../img/admin/cross.png" alt=""/><span><?php echo smartyTranslate(array('s'=>'Cancel new specific price'),$_smarty_tpl);?>
</span></a>
	<br/>
	<script type="text/javascript">
	var product_prices = new Array();
	<?php  $_smarty_tpl->tpl_vars['combination'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['combination']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['combinations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['combination']->key => $_smarty_tpl->tpl_vars['combination']->value){
$_smarty_tpl->tpl_vars['combination']->_loop = true;
?>
		product_prices['<?php echo $_smarty_tpl->tpl_vars['combination']->value['id_product_attribute'];?>
'] = '<?php echo $_smarty_tpl->tpl_vars['combination']->value['price'];?>
';
	<?php } ?>
	</script>
	<div id="add_specific_price" style="display: none;">
		<label><?php echo smartyTranslate(array('s'=>'For:'),$_smarty_tpl);?>
</label>
		<?php if (!$_smarty_tpl->tpl_vars['multi_shop']->value){?>
			<div class="margin-form">
				<input type="hidden" name="sp_id_shop" value="0" />
		<?php }else{ ?>
			<div class="margin-form">
				<select name="sp_id_shop" id="sp_id_shop">
					<?php if (!$_smarty_tpl->tpl_vars['admin_one_shop']->value){?><option value="0"><?php echo smartyTranslate(array('s'=>'All shops'),$_smarty_tpl);?>
</option><?php }?>
					<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value){
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['shop']->value['id_shop'];?>
"><?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['shop']->value['name']);?>
</option>
					<?php } ?>
				</select>
							&gt;
		<?php }?>
			<select name="sp_id_currency" id="spm_currency_0" onchange="changeCurrencySpecificPrice(0);">
				<option value="0"><?php echo smartyTranslate(array('s'=>'All currencies'),$_smarty_tpl);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['curr'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['curr']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['curr']->key => $_smarty_tpl->tpl_vars['curr']->value){
$_smarty_tpl->tpl_vars['curr']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['curr']->value['id_currency'];?>
"><?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['curr']->value['name']);?>
</option>
				<?php } ?>
			</select>
						&gt;
			<select name="sp_id_country" id="sp_id_country">
				<option value="0"><?php echo smartyTranslate(array('s'=>'All countries'),$_smarty_tpl);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value){
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['country']->value['id_country'];?>
"><?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['country']->value['name']);?>
</option>
				<?php } ?>
			</select>
						&gt;
			<select name="sp_id_group" id="sp_id_group">
				<option value="0"><?php echo smartyTranslate(array('s'=>'All groups'),$_smarty_tpl);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value){
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['group']->value['id_group'];?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</option>
				<?php } ?>
			</select>
		</div>
		<label><?php echo smartyTranslate(array('s'=>'Customer:'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<input type="hidden" name="sp_id_customer" id="id_customer" value="0" />
			<input type="text" name="customer" value="<?php echo smartyTranslate(array('s'=>'All customers'),$_smarty_tpl);?>
" id="customer" autocomplete="off" />
			<img src="../img/admin/field-loader.gif" id="customerLoader" alt="<?php echo smartyTranslate(array('s'=>'Loading...'),$_smarty_tpl);?>
" style="display: none;" />
			<div id="customers"></div>
		</div>
		<?php if (count($_smarty_tpl->tpl_vars['combinations']->value)!=0){?>
			<label><?php echo smartyTranslate(array('s'=>'Combination:'),$_smarty_tpl);?>
</label>
			<div class="margin-form">
				<select id="sp_id_product_attribute" name="sp_id_product_attribute">
					<option value="0"><?php echo smartyTranslate(array('s'=>'Apply to all combinations'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['combination'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['combination']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['combinations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['combination']->key => $_smarty_tpl->tpl_vars['combination']->value){
$_smarty_tpl->tpl_vars['combination']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['combination']->value['id_product_attribute'];?>
"><?php echo $_smarty_tpl->tpl_vars['combination']->value['attributes'];?>
</option>
					<?php } ?>
				</select>
			</div>
		<?php }?>
		<label><?php echo smartyTranslate(array('s'=>'Available from:'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<input class="datepicker" type="text" name="sp_from" value="" style="text-align: center" id="sp_from" /><span style="font-weight:bold; color:#000000; font-size:12px"> <?php echo smartyTranslate(array('s'=>'to'),$_smarty_tpl);?>
</span>
			<input class="datepicker" type="text" name="sp_to" value="" style="text-align: center" id="sp_to" />
		</div>

		<label><?php echo smartyTranslate(array('s'=>'Starting at'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<input type="text" name="sp_from_quantity" value="1" size="3" /> <span style="font-weight:bold; color:#000000; font-size:12px"><?php echo smartyTranslate(array('s'=>'unit'),$_smarty_tpl);?>
</span>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				product_prices['0'] = $('#sp_current_ht_price').html();

				$('#id_product_attribute').change(function() {
					$('#sp_current_ht_price').html(product_prices[$('#id_product_attribute option:selected').val()]);
				});
				$('#leave_bprice').click(function() {
					if (this.checked)
						$('#sp_price').attr('disabled', 'disabled');
					else
						$('#sp_price').removeAttr('disabled');
				});

				$('.datepicker').datetimepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd',

					// Define a custom regional settings in order to use PrestaShop translation tools
					currentText: '<?php echo smartyTranslate(array('s'=>'Now'),$_smarty_tpl);?>
',
					closeText: '<?php echo smartyTranslate(array('s'=>'Done'),$_smarty_tpl);?>
',
					ampm: false,
					amNames: ['AM', 'A'],
					pmNames: ['PM', 'P'],
					timeFormat: 'hh:mm:ss tt',
					timeSuffix: '',
					timeOnlyTitle: '<?php echo smartyTranslate(array('s'=>'Choose Time'),$_smarty_tpl);?>
',
					timeText: '<?php echo smartyTranslate(array('s'=>'Time'),$_smarty_tpl);?>
',
					hourText: '<?php echo smartyTranslate(array('s'=>'Hour'),$_smarty_tpl);?>
',
					minuteText: '<?php echo smartyTranslate(array('s'=>'Minute'),$_smarty_tpl);?>
',
				});
			});
		</script>

		<label><?php echo smartyTranslate(array('s'=>'Product price'),$_smarty_tpl);?>

			<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value){?>
				<?php echo smartyTranslate(array('s'=>'(tax excl.):'),$_smarty_tpl);?>

			<?php }?>
		</label>
		<div class="margin-form">
			<span id="spm_currency_sign_pre_0" style="font-weight:bold; color:#000000; font-size:12px">
				<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>

			</span>
			<input type="text" disabled="disabled" name="sp_price" id="sp_price" value="<?php echo sprintf('%.2f',$_smarty_tpl->tpl_vars['product']->value->price);?>
" size="11" />
			<span id="spm_currency_sign_post_0" style="font-weight:bold; color:#000000; font-size:12px">
				<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

			</span>
		</div>
		<label>
			<?php echo smartyTranslate(array('s'=>'Leave base price:'),$_smarty_tpl);?>

		</label>
		<div class="margin-form">
			<input id="leave_bprice" type="checkbox" value="1" checked="checked" name="leave_bprice" />
		</div>
		<label><?php echo smartyTranslate(array('s'=>'Apply a discount of:'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<input type="text" name="sp_reduction" value="0.00" size="11" />
			<select name="sp_reduction_type">
				<option selected="selected">---</option>
				<option value="amount"><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</option>
				<option value="percentage"><?php echo smartyTranslate(array('s'=>'Percentage'),$_smarty_tpl);?>
</option>
			</select>
			<p class="preference_description"><?php echo smartyTranslate(array('s'=>'The discount is applied after the tax'),$_smarty_tpl);?>
</p>
		</div>
	</div>

	<table style="text-align: left;width:100%" class="table" cellpadding="0" cellspacing="0" id="specific_prices_list">
		<thead>
			<tr>
				<th class="cell border" style="width: 12%;"><?php echo smartyTranslate(array('s'=>'Rule'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 12%;"><?php echo smartyTranslate(array('s'=>'Combination'),$_smarty_tpl);?>
</th>
				<?php if ($_smarty_tpl->tpl_vars['multi_shop']->value){?><th class="cell border" style="width: 12%;"><?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>
</th><?php }?>
				<th class="cell border" style="width: 12%;"><?php echo smartyTranslate(array('s'=>'Currency'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 11%;"><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 13%;"><?php echo smartyTranslate(array('s'=>'Group'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 13%;"><?php echo smartyTranslate(array('s'=>'Customer'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 13%;"><?php echo smartyTranslate(array('s'=>'Fixed price'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 13%;"><?php echo smartyTranslate(array('s'=>'Impact'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 15%;"><?php echo smartyTranslate(array('s'=>'Period'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 13%;"><?php echo smartyTranslate(array('s'=>'From (quantity)'),$_smarty_tpl);?>
</th>
				<th class="cell border" style="width: 2%;"><?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>
</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $_smarty_tpl->tpl_vars['specificPriceModificationForm']->value;?>

				<script type="text/javascript">
					$(document).ready(function() {
						calcPriceTI();
						unitPriceWithTax('unit');
						});
				</script>
			<?php }?>

<?php }} ?>