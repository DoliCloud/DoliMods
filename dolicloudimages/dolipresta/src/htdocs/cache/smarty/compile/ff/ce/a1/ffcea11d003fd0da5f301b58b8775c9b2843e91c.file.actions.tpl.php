<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:58
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/actions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:158154431051c1c0de84fa21-99984162%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ffcea11d003fd0da5f301b58b8775c9b2843e91c' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/actions.tpl',
      1 => 1371647765,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '158154431051c1c0de84fa21-99984162',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currentObject' => 0,
    'currentTab' => 0,
    'currencies' => 0,
    'currency' => 0,
    'defaultCurrency' => 0,
    'reductionProductFilter' => 0,
    'giftProductFilter' => 0,
    'gift_product_select' => 0,
    'hasAttribute' => 0,
    'gift_product_attribute_select' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0dea80d71_63243850',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0dea80d71_63243850')) {function content_51c1c0dea80d71_63243850($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><label><?php echo smartyTranslate(array('s'=>'Free shipping'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	&nbsp;&nbsp;
	<input type="radio" name="free_shipping" id="free_shipping_on" value="1" <?php if (intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'free_shipping'))){?>checked="checked"<?php }?> />
	<label class="t" for="free_shipping_on"> <img src="../img/admin/enabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" style="cursor:pointer" /></label>
	&nbsp;&nbsp;
	<input type="radio" name="free_shipping" id="free_shipping_off" value="0"  <?php if (!intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'free_shipping'))){?>checked="checked"<?php }?> />
	<label class="t" for="free_shipping_off"> <img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
" style="cursor:pointer" /></label>
</div>
<hr />
<label><?php echo smartyTranslate(array('s'=>'Apply a discount'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	&nbsp;&nbsp;
	<input type="radio" name="apply_discount" id="apply_discount_percent" value="percent" <?php if (floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_percent'))>0){?>checked="checked"<?php }?> />
	<label class="t" for="apply_discount_percent"> <img src="../img/admin/enabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" style="cursor:pointer" /> <?php echo smartyTranslate(array('s'=>'Percent (%)'),$_smarty_tpl);?>
</label>
	&nbsp;&nbsp;
	<input type="radio" name="apply_discount" id="apply_discount_amount" value="amount" <?php if (floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_amount'))>0){?>checked="checked"<?php }?> />
	<label class="t" for="apply_discount_amount"> <img src="../img/admin/enabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" style="cursor:pointer" /> <?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</label>
	&nbsp;&nbsp;
	<input type="radio" name="apply_discount" id="apply_discount_off" value="off" <?php if (!floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_amount'))>0&&!floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_percent'))>0){?>checked="checked"<?php }?> />
	<label class="t" for="apply_discount_off"> <img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
" style="cursor:pointer" /> <?php echo smartyTranslate(array('s'=>'None'),$_smarty_tpl);?>
</label>
</div>
<div id="apply_discount_percent_div">
	<label><?php echo smartyTranslate(array('s'=>'Value'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		<input type="text" id="reduction_percent" name="reduction_percent" value="<?php echo floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_percent'));?>
" style="width:40px" /> %
		<p><?php echo smartyTranslate(array('s'=>'Does not apply to the shipping costs'),$_smarty_tpl);?>
</p>
	</div>
</div>
<div id="apply_discount_amount_div">
	<label><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		<input type="text" id="reduction_amount" name="reduction_amount" value="<?php echo floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_amount'));?>
" onchange="this.value = this.value.replace(/,/g, '.');" />
		<select name="reduction_currency">
		<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value){
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
			<option value="<?php echo intval($_smarty_tpl->tpl_vars['currency']->value['id_currency']);?>
" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_currency')==$_smarty_tpl->tpl_vars['currency']->value['id_currency']||(!$_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_currency')&&$_smarty_tpl->tpl_vars['currency']->value['id_currency']==$_smarty_tpl->tpl_vars['defaultCurrency']->value)){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>
</option>
		<?php } ?>
		</select>
		<select name="reduction_tax">
			<option value="0" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_tax')==0){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Tax excluded'),$_smarty_tpl);?>
</option>
			<option value="1" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_tax')==1){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Tax included'),$_smarty_tpl);?>
</option>
		</select>
	</div>
</div>
<div id="apply_discount_to_div">
	<label><?php echo smartyTranslate(array('s'=>'Apply a discount to'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_order" value="order" <?php if (intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_product'))==0){?>checked="checked"<?php }?> />
		<label class="t" for="apply_discount_to_order"> <?php echo smartyTranslate(array('s'=>'Order (without shipping)'),$_smarty_tpl);?>
</label>
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_product" value="specific"  <?php if (intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_product'))>0){?>checked="checked"<?php }?> />
		<label class="t" for="apply_discount_to_product"> <?php echo smartyTranslate(array('s'=>'Specific product'),$_smarty_tpl);?>
</label>
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_cheapest" value="cheapest"  <?php if (intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_product'))==-1){?>checked="checked"<?php }?> />
		<label class="t" for="apply_discount_to_cheapest"> <?php echo smartyTranslate(array('s'=>'Cheapest product'),$_smarty_tpl);?>
</label>
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_selection" value="selection"  <?php if (intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_product'))==-2){?>checked="checked"<?php }?> />
		<label class="t" for="apply_discount_to_selection"> <?php echo smartyTranslate(array('s'=>'Selected product(s)'),$_smarty_tpl);?>
</label>
	</div>
	<div id="apply_discount_to_product_div">
		<label><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<input type="hidden" id="reduction_product" name="reduction_product" value="<?php echo intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'reduction_product'));?>
" />
			<input type="text" id="reductionProductFilter" name="reductionProductFilter" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['reductionProductFilter']->value, 'htmlall', 'UTF-8');?>
" style="width:400px" />
		</div>
	</div>
</div>
<hr />
<label><?php echo smartyTranslate(array('s'=>'Send a free gift'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	&nbsp;&nbsp;
	<input type="radio" name="free_gift" id="free_gift_on" value="1" <?php if (intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'gift_product'))){?>checked="checked"<?php }?> />
	<label class="t" for="free_gift_on"> <img src="../img/admin/enabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
" style="cursor:pointer" /></label>
	&nbsp;&nbsp;
	<input type="radio" name="free_gift" id="free_gift_off" value="0" <?php if (!intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'gift_product'))){?>checked="checked"<?php }?> />
	<label class="t" for="free_gift_off"> <img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
" style="cursor:pointer" /></label>
</div>
<div id="free_gift_div">
	<label><?php echo smartyTranslate(array('s'=>'Search a product'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		<input type="text" id="giftProductFilter" value="<?php echo $_smarty_tpl->tpl_vars['giftProductFilter']->value;?>
" style="width:400px" />
	</div>
	<div id="gift_products_found" <?php if ($_smarty_tpl->tpl_vars['gift_product_select']->value==''){?>style="display:none"<?php }?>>
		<div id="gift_product_list">
			<label><?php echo smartyTranslate(array('s'=>'Matching products'),$_smarty_tpl);?>
</label>
			<select name="gift_product" id="gift_product" onclick="displayProductAttributes();">
				<?php echo $_smarty_tpl->tpl_vars['gift_product_select']->value;?>

			</select>
		</div>
		<div class="clear">&nbsp;</div>
		<div id="gift_attributes_list" <?php if (!$_smarty_tpl->tpl_vars['hasAttribute']->value){?>style="display:none"<?php }?>>
			<label><?php echo smartyTranslate(array('s'=>'Available combinations'),$_smarty_tpl);?>
</label>
			<div id="gift_attributes_list_select">
				<?php echo $_smarty_tpl->tpl_vars['gift_product_attribute_select']->value;?>

			</div>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
	<div id="gift_products_err" class="warn" style="display:none"></div>
</div>
<?php }} ?>