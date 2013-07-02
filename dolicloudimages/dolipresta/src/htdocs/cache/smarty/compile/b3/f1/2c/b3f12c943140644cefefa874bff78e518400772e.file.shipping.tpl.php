<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/shipping.tpl" */ ?>
<?php /*%%SmartyHeaderCode:159095791851c1c0e3c22fd4-76111804%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b3f12c943140644cefefa874bff78e518400772e' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/shipping.tpl',
      1 => 1371647791,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '159095791851c1c0e3c22fd4-76111804',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'display_common_field' => 0,
    'bullet_common_field' => 0,
    'product' => 0,
    'ps_dimension_unit' => 0,
    'ps_weight_unit' => 0,
    'currency' => 0,
    'country_display_tax_label' => 0,
    'carrier_list' => 0,
    'carrier' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e3cfe701_30999523',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e3cfe701_30999523')) {function content_51c1c0e3cfe701_30999523($_smarty_tpl) {?>

<input type="hidden" name="submitted_tabs[]" value="Shipping" />
<h4 class="tab">1. <?php echo smartyTranslate(array('s'=>'Info.'),$_smarty_tpl);?>
</h4>
<h4><?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
</h4>

<?php if (isset($_smarty_tpl->tpl_vars['display_common_field']->value)&&$_smarty_tpl->tpl_vars['display_common_field']->value){?>
	<div class="hint" style="display: block"><?php echo smartyTranslate(array('s'=>'Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product','sprintf'=>$_smarty_tpl->tpl_vars['bullet_common_field']->value),$_smarty_tpl);?>
</div>
<?php }?>

<div class="separation"></div>

<table>
	<tr>
		<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Width (package):'),$_smarty_tpl);?>
</label></td>
		<td style="padding-bottom:5px;">
			<input size="6" maxlength="6" name="width" type="text" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->width;?>
" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
  <?php echo $_smarty_tpl->tpl_vars['ps_dimension_unit']->value;?>

		</td>
	</tr>
	<tr>
		<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Height (package):'),$_smarty_tpl);?>
</label></td>
		<td style="padding-bottom:5px;">
			<input size="6" maxlength="6" name="height" type="text" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->height;?>
" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
  <?php echo $_smarty_tpl->tpl_vars['ps_dimension_unit']->value;?>

		</td>
	</tr>
	<tr>
	<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Depth (package):'),$_smarty_tpl);?>
</label></td>
	<td style="padding-bottom:5px;">
	<input size="6" maxlength="6" name="depth" type="text" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->depth;?>
" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
  <?php echo $_smarty_tpl->tpl_vars['ps_dimension_unit']->value;?>

	</td>
	</tr>
	<tr>
	<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Weight (package):'),$_smarty_tpl);?>
</label></td>
	<td style="padding-bottom:5px;">
	<input size="6" maxlength="6" name="weight" type="text" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->weight;?>
" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
  <?php echo $_smarty_tpl->tpl_vars['ps_weight_unit']->value;?>

	</td>
	</tr>
	<tr>
		<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Additional shipping cost (per quantity):'),$_smarty_tpl);?>
</label></td>
		<td style="padding-bottom:5px;"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<input type="text" name="additional_shipping_cost" onchange="this.value = this.value.replace(/,/g, '.');"
				value="<?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value->additional_shipping_cost);?>
" /><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

			<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value){?><?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
<?php }?>
			<p class="preference_description"><?php echo smartyTranslate(array('s'=>'A carrier tax will be applied.'),$_smarty_tpl);?>
</p>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			<label><?php echo smartyTranslate(array('s'=>'Carriers:'),$_smarty_tpl);?>
</label>
		</td>
		<td class="padding-bottom:5px;">
			<select name="carriers[]" id="carriers_restriction" multiple="multiple" size="4" style="height:100px;width:200px;">
				<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carrier_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value){
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['carrier']->value['id_reference'];?>
" <?php if (isset($_smarty_tpl->tpl_vars['carrier']->value['selected'])&&$_smarty_tpl->tpl_vars['carrier']->value['selected']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['carrier']->value['name'];?>
</option>
				<?php } ?>
			</select>
			<br>
			<button class="button" onclick="unselectAllCarriers(); return false;"><?php echo smartyTranslate(array('s'=>'Unselect all'),$_smarty_tpl);?>
</button>
			<p class="preference_description"><?php echo smartyTranslate(array('s'=>'If no carrier selected, all carriers can be used to ship this product.'),$_smarty_tpl);?>
</p>
		</td>
	</tr>
</table>
<script>
	function unselectAllCarriers()
	{
		$('#carriers_restriction option').each(function () { $(this).removeAttr('selected')});
		return false;
	}
</script><?php }} ?>