<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:02
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/combinations.tpl" */ ?>
<?php /*%%SmartyHeaderCode:62617218751c1c0e2812ab6-12535790%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '823ff87502eca9b5c616582bba19647b5cbfe33f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/combinations.tpl',
      1 => 1371647789,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '62617218751c1c0e2812ab6-12535790',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'attributeJs' => 0,
    'idgrp' => 0,
    'group' => 0,
    'idattr' => 0,
    'attrname' => 0,
    'token_generator' => 0,
    'combination_exists' => 0,
    'display_multishop_checkboxes' => 0,
    'attributes_groups' => 0,
    'attribute_group' => 0,
    'currency' => 0,
    'country_display_tax_label' => 0,
    'tax_exclude_option' => 0,
    'ps_weight_unit' => 0,
    'field_value_unity' => 0,
    'ps_use_ecotax' => 0,
    'minimal_quantity' => 0,
    'available_date' => 0,
    'images' => 0,
    'imageWidth' => 0,
    'image' => 0,
    'list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e2b23721_50419330',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e2b23721_50419330')) {function content_51c1c0e2b23721_50419330($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<script type="text/javascript">
	var msg_combination_1 = '<?php echo smartyTranslate(array('s'=>'Please choose an attribute.'),$_smarty_tpl);?>
';
	var msg_combination_2 = '<?php echo smartyTranslate(array('s'=>'Please choose a value.'),$_smarty_tpl);?>
';
	var msg_combination_3 = '<?php echo smartyTranslate(array('s'=>'You can only add one combination per attribute type.'),$_smarty_tpl);?>
';
	var msg_new_combination = '<?php echo smartyTranslate(array('s'=>'New combination'),$_smarty_tpl);?>
';
</script>

<?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)&&!$_smarty_tpl->tpl_vars['product']->value->is_virtual){?>
	<input type="hidden" name="submitted_tabs[]" value="Combinations" />
	<script type="text/javascript">
		var attrs = new Array();
		var modifyattributegroup = "<?php echo smartyTranslate(array('s'=>'Modify this attribute combination.','js'=>1),$_smarty_tpl);?>
";
		attrs[0] = new Array(0, "---");
	<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_smarty_tpl->tpl_vars['idgrp'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attributeJs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value){
$_smarty_tpl->tpl_vars['group']->_loop = true;
 $_smarty_tpl->tpl_vars['idgrp']->value = $_smarty_tpl->tpl_vars['group']->key;
?>
		attrs[<?php echo $_smarty_tpl->tpl_vars['idgrp']->value;?>
] = new Array(0
		, '---'
		<?php  $_smarty_tpl->tpl_vars['attrname'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attrname']->_loop = false;
 $_smarty_tpl->tpl_vars['idattr'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['group']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attrname']->key => $_smarty_tpl->tpl_vars['attrname']->value){
$_smarty_tpl->tpl_vars['attrname']->_loop = true;
 $_smarty_tpl->tpl_vars['idattr']->value = $_smarty_tpl->tpl_vars['attrname']->key;
?>
			, "<?php echo $_smarty_tpl->tpl_vars['idattr']->value;?>
", "<?php echo addslashes($_smarty_tpl->tpl_vars['attrname']->value);?>
"
		<?php } ?>
		);
	<?php } ?>
	</script>
	<h4><?php echo smartyTranslate(array('s'=>'Add or modify combinations for this product.'),$_smarty_tpl);?>
</h4>
	<div class="separation"></div> <?php echo smartyTranslate(array('s'=>'Or use the'),$_smarty_tpl);?>

		&nbsp;<a class="button bt-icon confirm_leave" href="index.php?tab=AdminAttributeGenerator&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
&attributegenerator&token=<?php echo $_smarty_tpl->tpl_vars['token_generator']->value;?>
"><img src="../img/admin/appearance.gif" alt="combinations_generator" class="middle" title="<?php echo smartyTranslate(array('s'=>'Product combinations generator'),$_smarty_tpl);?>
" /><span><?php echo smartyTranslate(array('s'=>'Product combinations generator'),$_smarty_tpl);?>
</span></a> 
		<?php echo smartyTranslate(array('s'=>'in order to automatically create a set of combinations.'),$_smarty_tpl);?>

	<?php if ($_smarty_tpl->tpl_vars['combination_exists']->value){?>
	<div class="warn" style="display:block">
		<ul>
			<li><?php echo smartyTranslate(array('s'=>'Some combinations already exist. If you want to generate new combinations, the quantities for the existing combinations will be lost.'),$_smarty_tpl);?>
</li>
			<li><?php echo smartyTranslate(array('s'=>'You can add a combination by clicking the link "Add new combination" on the toolbar.'),$_smarty_tpl);?>
</li>
		</ul>
	</div>
	<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value)&&$_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value){?>
		<br />
		<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('product_tab'=>"Combinations"), 0);?>

	<?php }?>
	<div class="separation"></div>
	
	<div id="add_new_combination" style="display: none;">
		<table cellpadding="5" style="width:100%">
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" valign="top">
					<label><?php echo smartyTranslate(array('s'=>'Attribute:'),$_smarty_tpl);?>
</label>
				</td>
				<td style="padding-bottom:5px;">
					<select name="attribute_group" id="attribute_group" style="width: 200px;" onchange="populate_attrs();">
						<?php if (isset($_smarty_tpl->tpl_vars['attributes_groups']->value)){?>
							<?php  $_smarty_tpl->tpl_vars['attribute_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute_group']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attributes_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute_group']->key => $_smarty_tpl->tpl_vars['attribute_group']->value){
$_smarty_tpl->tpl_vars['attribute_group']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['attribute_group']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['attribute_group']->value['name'], 'htmlall', 'UTF-8');?>
&nbsp;&nbsp;</option>
							<?php } ?>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" valign="top">
					<label><?php echo smartyTranslate(array('s'=>'Value:'),$_smarty_tpl);?>
</label>
				</td>
				<td style="padding-bottom:5px;">
					<select name="attribute" id="attribute" style="width: 200px;">
						<option value="0">---</option>
					</select>
					<script type="text/javascript">
					$(document).ready(function(){
						populate_attrs();
					});
					</script>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" valign="top">
				<input style="width: 140px; margin-bottom: 10px;" type="button" value="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" class="button" onclick="add_attr();"/><br />
				<input style="width: 140px;" type="button" value="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" class="button" onclick="del_attr()"/></td>
				<td align="left">
					<select id="product_att_list" name="attribute_combination_list[]" multiple="multiple" size="4" style="width: 320px;"></select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="separation"></div>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;"><label><?php echo smartyTranslate(array('s'=>'Reference:'),$_smarty_tpl);?>
</label></td>
				<td style="padding-bottom:5px;">
					<input size="55" type="text" id="attribute_reference" name="attribute_reference" value="" style="width: 130px; margin-right: 44px;" />
					<?php echo smartyTranslate(array('s'=>'EAN13:'),$_smarty_tpl);?>
<input size="55" maxlength="13" type="text" id="attribute_ean13" name="attribute_ean13" value="" style="width: 110px; margin-left: 10px; margin-right: 44px;" />
					<?php echo smartyTranslate(array('s'=>'UPC:'),$_smarty_tpl);?>
<input size="55" maxlength="12" type="text" id="attribute_upc" name="attribute_upc" value="" style="width: 110px; margin-left: 10px; margin-right: 44px;" />
					<span class="hint" name="help_box"><?php echo smartyTranslate(array('s'=>'Special characters allowed:'),$_smarty_tpl);?>
 .-_#<span class="hint-pointer">&nbsp;</span></span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="separation"></div>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"attribute_wholesale_price",'type'=>"default"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Wholesale price:'),$_smarty_tpl);?>
</label>
				</td>
				<td style="padding-bottom:5px;">
					<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2!=0){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?>
					<input type="text" size="6"  name="attribute_wholesale_price" id="attribute_wholesale_price" value="" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
					<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2==0){?> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php }?><span id="attribute_wholesale_price_blank">(<?php echo smartyTranslate(array('s'=>'Leave blank if the price does not change'),$_smarty_tpl);?>
)</span>
					<span style="display:none" id="attribute_wholesale_price_full">(<?php echo smartyTranslate(array('s'=>'Overrides wholesale price on "Information" tab'),$_smarty_tpl);?>
)</span>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"attribute_price_impact",'type'=>"attribute_price_impact"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Impact on price:'),$_smarty_tpl);?>
</label>
				</td>
				<td colspan="2" style="padding-bottom:5px;">
					<select name="attribute_price_impact" id="attribute_price_impact" style="width: 140px;" onchange="check_impact(); calcImpactPriceTI();">
						<option value="0"><?php echo smartyTranslate(array('s'=>'None'),$_smarty_tpl);?>
</option>
						<option value="1"><?php echo smartyTranslate(array('s'=>'Increase'),$_smarty_tpl);?>
</option>
						<option value="-1"><?php echo smartyTranslate(array('s'=>'Reduction'),$_smarty_tpl);?>
</option>
					</select>
					<span id="span_impact">&nbsp;&nbsp;<?php echo smartyTranslate(array('s'=>'of'),$_smarty_tpl);?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2!=0){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php }?>
						<input type="hidden"  id="attribute_priceTEReal" name="attribute_price" value="0.00" />
						<input type="text" size="6" id="attribute_price" value="0.00" onkeyup="$('#attribute_priceTEReal').val(this.value.replace(/,/g, '.')); if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); calcImpactPriceTI();"/><?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2==0){?> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value){?>
							<?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>

							<span <?php if ($_smarty_tpl->tpl_vars['tax_exclude_option']->value){?>style="display:none"<?php }?>> <?php echo smartyTranslate(array('s'=>'or'),$_smarty_tpl);?>

							<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2!=0){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php }?>
							<input type="text" size="6" name="attribute_priceTI" id="attribute_priceTI" value="0.00" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); calcImpactPriceTE();"/>
							<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2==0){?> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>

							</span> <?php echo smartyTranslate(array('s'=>'final product price will be set to'),$_smarty_tpl);?>

							<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2!=0){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php }?>
							<span id="attribute_new_total_price">0.00</span>
							<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2==0){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php }?>
						<?php }?>
					</span>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"attribute_weight_impact",'type'=>"attribute_weight_impact"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Impact on weight:'),$_smarty_tpl);?>
</label>
				</td>
				<td colspan="2" style="padding-bottom:5px;">
					<select name="attribute_weight_impact" id="attribute_weight_impact" style="width: 140px;" onchange="check_weight_impact();">
						<option value="0"><?php echo smartyTranslate(array('s'=>'None'),$_smarty_tpl);?>
</option>
						<option value="1"><?php echo smartyTranslate(array('s'=>'Increase'),$_smarty_tpl);?>
</option>
						<option value="-1"><?php echo smartyTranslate(array('s'=>'Reduction'),$_smarty_tpl);?>
</option>
					</select>
					<span id="span_weight_impact">&nbsp;&nbsp;<?php echo smartyTranslate(array('s'=>'of'),$_smarty_tpl);?>
&nbsp;&nbsp;
						<input type="text" size="6" name="attribute_weight" id="attribute_weight" value="0.00" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
						<?php echo $_smarty_tpl->tpl_vars['ps_weight_unit']->value;?>

					</span>
				</td>
			</tr>
			<tr id="tr_unit_impact">
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"attribute_unit_impact",'type'=>"attribute_unit_impact"), 0);?>

					<label style="width: 100px; float: right"><?php echo smartyTranslate(array('s'=>'Impact on unit price :'),$_smarty_tpl);?>
</label>
				</td>
				<td colspan="2" style="padding-bottom:5px;">
					<select name="attribute_unit_impact" id="attribute_unit_impact" style="width: 140px;" onchange="check_unit_impact();">
						<option value="0"><?php echo smartyTranslate(array('s'=>'None'),$_smarty_tpl);?>
</option>
						<option value="1"><?php echo smartyTranslate(array('s'=>'Increase'),$_smarty_tpl);?>
</option>
						<option value="-1"><?php echo smartyTranslate(array('s'=>'Reduction'),$_smarty_tpl);?>
</option>
					</select>
					<span id="span_weight_impact">&nbsp;&nbsp;<?php echo smartyTranslate(array('s'=>'of'),$_smarty_tpl);?>
&nbsp;&nbsp;&nbsp;&nbsp;
						<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2!=0){?> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php }?>
						<input type="text" size="6" name="attribute_unity" id="attribute_unity" value="0.00" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /><?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2==0){?> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> / <span id="unity_third"><?php echo $_smarty_tpl->tpl_vars['field_value_unity']->value;?>
</span>
					</span>
				</td>
			</tr>
			<?php if ($_smarty_tpl->tpl_vars['ps_use_ecotax']->value){?>
				<tr>
					<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
						<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"attribute_ecotax",'type'=>"default"), 0);?>

						<label><?php echo smartyTranslate(array('s'=>'Eco-tax (tax excl.):'),$_smarty_tpl);?>
</label>
					</td>
					<td style="padding-bottom:5px;"><?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2!=0){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?>
						<input type="text" size="3" name="attribute_ecotax" id="attribute_ecotax" value="0.00" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
						<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2==0){?> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> 
						(<?php echo smartyTranslate(array('s'=>'overrides Eco-tax in the "Information" tab'),$_smarty_tpl);?>
)
					</td>
				</tr>
			<?php }?>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" class="col-left">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"attribute_minimal_quantity",'type'=>"default"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Minimum quantity:'),$_smarty_tpl);?>
</label>
				</td>
				<td style="padding-bottom:5px;">
					<input size="3" maxlength="6" name="attribute_minimal_quantity" id="attribute_minimal_quantity" type="text" value="<?php echo $_smarty_tpl->tpl_vars['minimal_quantity']->value;?>
" />
					<p><?php echo smartyTranslate(array('s'=>'The minimum quantity to buy this product (set to 1 to disable this feature)'),$_smarty_tpl);?>
</p>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" class="col-left">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"available_date_attribute",'type'=>"default"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Available date:'),$_smarty_tpl);?>
</label>
				</td>
				<td style="padding-bottom:5px;">
					<input class="datepicker" id="available_date_attribute" name="available_date_attribute" value="<?php echo $_smarty_tpl->tpl_vars['available_date']->value;?>
" style="text-align: center;" type="text" />
					<p><?php echo smartyTranslate(array('s'=>'The available date when this product is out of stock.'),$_smarty_tpl);?>
</p>
					<script type="text/javascript">
						$(document).ready(function(){
							$(".datepicker").datepicker({
								prevText: '',
								nextText: '',
								dateFormat: 'yy-mm-dd'
							});
						});
					</script>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="separation"></div>
				</td>
			</tr>
			<tr>
				<td style="width:150px"><label><?php echo smartyTranslate(array('s'=>'Image:'),$_smarty_tpl);?>
</label></td>
				<td style="padding-bottom:5px;">
					<ul id="id_image_attr">
						<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['images']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['image']->key;
?>
							<li style="float: left; width: <?php echo $_smarty_tpl->tpl_vars['imageWidth']->value;?>
px;">
								<input type="checkbox" name="id_image_attr[]" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id_image'];?>
" id="id_image_attr_<?php echo $_smarty_tpl->tpl_vars['image']->value['id_image'];?>
" />
								<label for="id_image_attr_<?php echo $_smarty_tpl->tpl_vars['image']->value['id_image'];?>
" style="float: none;">
									<img src="<?php echo @constant('_THEME_PROD_DIR_');?>
<?php echo $_smarty_tpl->tpl_vars['image']->value['obj']->getExistingImgPath();?>
-small_default.jpg" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['image']->value['legend'], 'htmlall', 'UTF-8');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['image']->value['legend'], 'htmlall', 'UTF-8');?>
" />
								</label>
							</li>
						<?php } ?>
					</ul>
					<img id="pic" alt="" title="" style="display: none; width: 100px; height: 100px; float: left; border: 1px dashed #BBB; margin-left: 20px;" />
				</td>
			</tr>
			<tr>
				<td style="width:150px">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"attribute_default",'type'=>"attribute_default"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Default:'),$_smarty_tpl);?>
</label><br /><br />
				</td>
				<td style="padding-bottom:5px;">
					<input type="checkbox" name="attribute_default" id="attribute_default" value="1" />
					&nbsp;<label for="attribute_default" style="float:none;"><?php echo smartyTranslate(array('s'=>'Make this combination the default combination for this product'),$_smarty_tpl);?>
</label><br /><br />
				</td>
			</tr>
			<tr>
				<td style="width:150px">&nbsp;</td>
				<td style="padding-bottom:5px;">
					<span id="ResetSpan" style="float:left;margin-left:8px;display:none;">
						<input type="reset" name="ResetBtn" id="ResetBtn" onclick="getE('id_product_attribute').value = 0;" class="button" value="<?php echo smartyTranslate(array('s'=>'Cancel modification'),$_smarty_tpl);?>
" />
					</span>
					<span class="clear"></span>
				</td>
			</tr>
		</table>
		<div class="separation"></div>
	</div>
	
	<?php echo $_smarty_tpl->tpl_vars['list']->value;?>

<?php }?>
<?php }} ?>