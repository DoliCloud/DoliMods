<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:58
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/conditions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:108058844651c1c0dea87eb2-43865975%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a07608348afba7739704ac96f6ac2d034c71f27a' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/conditions.tpl',
      1 => 1371647765,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '108058844651c1c0dea87eb2-43865975',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currentObject' => 0,
    'currentTab' => 0,
    'customerFilter' => 0,
    'defaultDateFrom' => 0,
    'defaultDateTo' => 0,
    'currencies' => 0,
    'currency' => 0,
    'defaultCurrency' => 0,
    'countries' => 0,
    'country' => 0,
    'carriers' => 0,
    'carrier' => 0,
    'groups' => 0,
    'group' => 0,
    'cart_rules' => 0,
    'cart_rule' => 0,
    'product_rule_groups' => 0,
    'product_rule_group' => 0,
    'shops' => 0,
    'shop' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0dee1f8c0_23732979',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0dee1f8c0_23732979')) {function content_51c1c0dee1f8c0_23732979($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><label><?php echo smartyTranslate(array('s'=>'Limit to a single customer'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	<input type="hidden" id="id_customer" name="id_customer" value="<?php echo intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'id_customer'));?>
" />
	<input type="text" id="customerFilter" name="customerFilter" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['customerFilter']->value, 'htmlall', 'UTF-8');?>
" style="width:400px" />
	<p class="preference_description"><?php echo smartyTranslate(array('s'=>'Optional: The cart rule will be available to everyone if you leave this field blank.'),$_smarty_tpl);?>
</p>
</div>
<label><?php echo smartyTranslate(array('s'=>'Valid'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	<strong><?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
</strong>
	<input type="text" class="datepicker" name="date_from"
		value="<?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_from')){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_from'), ENT_QUOTES, 'UTF-8', true);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['defaultDateFrom']->value;?>
<?php }?>" />
	<strong><?php echo smartyTranslate(array('s'=>'To'),$_smarty_tpl);?>
</strong>
	<input type="text" class="datepicker" name="date_to"
		value="<?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_to')){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_to'), ENT_QUOTES, 'UTF-8', true);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['defaultDateTo']->value;?>
<?php }?>" />
	<p class="preference_description"><?php echo smartyTranslate(array('s'=>'The default period is one month.'),$_smarty_tpl);?>
</p>
</div>
<label><?php echo smartyTranslate(array('s'=>'Minimum amount'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	<input type="text" name="minimum_amount" value="<?php echo floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount'));?>
" />
	<select name="minimum_amount_currency">
	<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value){
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
		<option value="<?php echo intval($_smarty_tpl->tpl_vars['currency']->value['id_currency']);?>
"
		<?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_currency')==$_smarty_tpl->tpl_vars['currency']->value['id_currency']||(!$_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_currency')&&$_smarty_tpl->tpl_vars['currency']->value['id_currency']==$_smarty_tpl->tpl_vars['defaultCurrency']->value)){?>
			selected="selected"
		<?php }?>
		>
			<?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>

		</option>
	<?php } ?>
	</select>
	<select name="minimum_amount_tax">
		<option value="0" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_tax')==0){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Tax excluded'),$_smarty_tpl);?>
</option>
		<option value="1" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_tax')==1){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Tax included'),$_smarty_tpl);?>
</option>
	</select>
	<select name="minimum_amount_shipping">
		<option value="0" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_shipping')==0){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Shipping excluded'),$_smarty_tpl);?>
</option>
		<option value="1" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_shipping')==1){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Shipping included'),$_smarty_tpl);?>
</option>
	</select>
	<p class="preference_description"><?php echo smartyTranslate(array('s'=>'You can choose a minimum amount for the cart either with or without the taxes and shipping.'),$_smarty_tpl);?>
</p>
</div>
<label><?php echo smartyTranslate(array('s'=>'Total available'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	<input type="text" name="quantity" value="<?php echo intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'quantity'));?>
" />
	<p class="preference_description"><?php echo smartyTranslate(array('s'=>'The cart rule will be applied to the first "X" customers only.'),$_smarty_tpl);?>
</p>
</div>
<label><?php echo smartyTranslate(array('s'=>'Total available for each user.'),$_smarty_tpl);?>
</label>
<div class="margin-form">
	<input type="text" name="quantity_per_user" value="<?php echo intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'quantity_per_user'));?>
" />
	<p class="preference_description"><?php echo smartyTranslate(array('s'=>'A customer will only be able to use the cart rule "X" time(s).'),$_smarty_tpl);?>
</p>
</div>
<?php if (count($_smarty_tpl->tpl_vars['countries']->value['unselected'])+count($_smarty_tpl->tpl_vars['countries']->value['selected'])>1){?>
<br />
<input type="checkbox" id="country_restriction" name="country_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['countries']->value['unselected'])){?>checked="checked"<?php }?> /> <strong><?php echo smartyTranslate(array('s'=>'Country selection'),$_smarty_tpl);?>
</strong>
<div id="country_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td style="padding-left:20px;">
				<p><strong><?php echo smartyTranslate(array('s'=>'Unselected countries'),$_smarty_tpl);?>
</strong></p>
				<select id="country_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value){
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['country']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="country_select_add"
					style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 &gt;&gt;
				</a>
			</td>
			<td>
				<p><strong><?php echo smartyTranslate(array('s'=>'Selected countries'),$_smarty_tpl);?>
</strong></p>
				<select name="country_select[]" id="country_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value){
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['country']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="country_select_remove"
					style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					&lt;&lt; <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>

				</a>
			</td>
		</tr>
	</table>
	<p><?php echo smartyTranslate(array('s'=>'This restriction applies to the country of delivery.'),$_smarty_tpl);?>
</p>
</div>
<?php }?>
<?php if (count($_smarty_tpl->tpl_vars['carriers']->value['unselected'])+count($_smarty_tpl->tpl_vars['carriers']->value['selected'])>1){?>
<br />
<input type="checkbox" id="carrier_restriction" name="carrier_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['carriers']->value['unselected'])){?>checked="checked"<?php }?> /> <strong><?php echo smartyTranslate(array('s'=>'Carrier selection'),$_smarty_tpl);?>
</strong>
<div id="carrier_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td style="padding-left:20px;">
				<p><strong><?php echo smartyTranslate(array('s'=>'Unselected carriers'),$_smarty_tpl);?>
</strong></p>
				<select id="carrier_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carriers']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value){
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['carrier']->value['id_reference']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['carrier']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="carrier_select_add"
					style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 &gt;&gt;
				</a>
			</td>
			<td>
				<p><strong><?php echo smartyTranslate(array('s'=>'Selected carriers'),$_smarty_tpl);?>
</strong></p>
				<select name="carrier_select[]" id="carrier_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carriers']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value){
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['carrier']->value['id_reference']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['carrier']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="carrier_select_remove"
					style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					&lt;&lt; <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>

				</a>
			</td>
		</tr>
	</table>
</div>
<?php }?>
<?php if (count($_smarty_tpl->tpl_vars['groups']->value['unselected'])+count($_smarty_tpl->tpl_vars['groups']->value['selected'])>1){?>
<br />
<input type="checkbox" id="group_restriction" name="group_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['groups']->value['unselected'])){?>checked="checked"<?php }?> />
<strong><?php echo smartyTranslate(array('s'=>'Customer group selection'),$_smarty_tpl);?>
</strong>
<div id="group_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td style="padding-left:20px;">
				<p><strong><?php echo smartyTranslate(array('s'=>'Unselected groups'),$_smarty_tpl);?>
</strong></p>
				<select id="group_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value){
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['group']->value['id_group']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['group']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="group_select_add"
					style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 &gt;&gt;
				</a>
			</td>
			<td>
				<p><strong><?php echo smartyTranslate(array('s'=>'Selected groups'),$_smarty_tpl);?>
</strong></p>
				<select name="group_select[]" id="group_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value){
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['group']->value['id_group']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['group']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="group_select_remove"
					style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					&lt;&lt; <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>

				</a>
			</td>
		</tr>
	</table>
</div>
<?php }?>
<?php if (count($_smarty_tpl->tpl_vars['cart_rules']->value['unselected'])+count($_smarty_tpl->tpl_vars['cart_rules']->value['selected'])>0){?>
<br />
<input type="checkbox" id="cart_rule_restriction" name="cart_rule_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['cart_rules']->value['unselected'])){?>checked="checked"<?php }?> />
<strong><?php echo smartyTranslate(array('s'=>'Compatibility with other cart rules'),$_smarty_tpl);?>
</strong>
<div id="cart_rule_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td style="padding-left:20px;">
				<p><strong><?php echo smartyTranslate(array('s'=>'Uncombinable cart rules'),$_smarty_tpl);?>
</strong></p>
				<select id="cart_rule_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple="">
					<?php  $_smarty_tpl->tpl_vars['cart_rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cart_rule']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cart_rules']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cart_rule']->key => $_smarty_tpl->tpl_vars['cart_rule']->value){
$_smarty_tpl->tpl_vars['cart_rule']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['cart_rule']->value['id_cart_rule']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cart_rule']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="cart_rule_select_add"
					style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 &gt;&gt;
				</a>
			</td>
			<td>
				<p><strong><?php echo smartyTranslate(array('s'=>'Combinable cart rules'),$_smarty_tpl);?>
</strong></p>
				<select name="cart_rule_select[]" id="cart_rule_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['cart_rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cart_rule']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cart_rules']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cart_rule']->key => $_smarty_tpl->tpl_vars['cart_rule']->value){
$_smarty_tpl->tpl_vars['cart_rule']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['cart_rule']->value['id_cart_rule']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cart_rule']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="cart_rule_select_remove"
					style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					&lt;&lt; <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>

				</a>
			</td>
		</tr>
	</table>
</div>
<?php }?>
<br />
<input type="checkbox" id="product_restriction" name="product_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['product_rule_groups']->value)){?>checked="checked"<?php }?> /> <strong><?php echo smartyTranslate(array('s'=>'Product selection'),$_smarty_tpl);?>
</strong>
<div id="product_restriction_div">
	<table id="product_rule_group_table" style="border:1px solid #AAAAAA;margin:10px 0 10px 0;padding:10px 10px 10px 10px;background-color:#FFF5D3;width:600px;display:none" cellpadding="0" cellspacing="0">
		<?php  $_smarty_tpl->tpl_vars['product_rule_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product_rule_group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product_rule_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product_rule_group']->key => $_smarty_tpl->tpl_vars['product_rule_group']->value){
$_smarty_tpl->tpl_vars['product_rule_group']->_loop = true;
?>
			<?php echo $_smarty_tpl->tpl_vars['product_rule_group']->value;?>

		<?php } ?>
	</table>
	<a href="javascript:addProductRuleGroup();" style="margin-top:5px;display:block">
		<img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Product selection'),$_smarty_tpl);?>

	</a>
</div>
<?php if (count($_smarty_tpl->tpl_vars['shops']->value['unselected'])+count($_smarty_tpl->tpl_vars['shops']->value['selected'])>1){?>
<br />
<input type="checkbox" id="shop_restriction" name="shop_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['shops']->value['unselected'])){?>checked="checked"<?php }?> /> <strong><?php echo smartyTranslate(array('s'=>'Shop selection'),$_smarty_tpl);?>
</strong>
<div id="shop_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td style="padding-left:20px;">
				<p><strong><?php echo smartyTranslate(array('s'=>'Unselected shops'),$_smarty_tpl);?>
</strong></p>
				<select id="shop_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value){
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['shop']->value['id_shop']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="shop_select_add"
					style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 &gt;&gt;
				</a>
			</td>
			<td>
				<p><strong><?php echo smartyTranslate(array('s'=>'Selected shops'),$_smarty_tpl);?>
</strong></p>
				<select name="shop_select[]" id="shop_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value){
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['shop']->value['id_shop']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select><br /><br />
				<a
					id="shop_select_remove"
					style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
				>
					&lt;&lt; <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>

				</a>
			</td>
		</tr>
	</table>
</div>
<?php }?><?php }} ?>