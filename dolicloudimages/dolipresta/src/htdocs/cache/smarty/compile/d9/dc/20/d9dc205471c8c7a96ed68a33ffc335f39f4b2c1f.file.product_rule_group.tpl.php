<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/product_rule_group.tpl" */ ?>
<?php /*%%SmartyHeaderCode:88282009751c1c0df25b536-77020651%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd9dc205471c8c7a96ed68a33ffc335f39f4b2c1f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/product_rule_group.tpl',
      1 => 1371647766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '88282009751c1c0df25b536-77020651',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product_rule_group_id' => 0,
    'product_rule_group_quantity' => 0,
    'product_rules' => 0,
    'product_rule' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0df2f1cb1_30770085',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0df2f1cb1_30770085')) {function content_51c1c0df2f1cb1_30770085($_smarty_tpl) {?><tr id="product_rule_group_<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
_tr">
	<td style="vertical-align:center;padding-right:10px">
		<a href="javascript:removeProductRuleGroup(<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
);">
			<img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
" />
		</a>
	</td>
	<td style="padding-bottom:15px">
		<input type="hidden" name="product_rule_group[]" value="<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
" />
		<?php echo smartyTranslate(array('s'=>'The cart must contain at least'),$_smarty_tpl);?>

		<input type="text" name="product_rule_group_<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
_quantity" value="<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_quantity']->value);?>
" style="width:30px" />
		<?php echo smartyTranslate(array('s'=>'Product(s) matching the following rules:'),$_smarty_tpl);?>

		<br />
		<a href="javascript:addProductRule(<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
);">
			<img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" />
			<?php echo smartyTranslate(array('s'=>'Add a rule concerning'),$_smarty_tpl);?>

		</a>
		<select id="product_rule_type_<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
">
			<option value=""><?php echo smartyTranslate(array('s'=>'-- Choose --'),$_smarty_tpl);?>
</option>
			<option value="products"><?php echo smartyTranslate(array('s'=>'Products:'),$_smarty_tpl);?>
</option>
			<option value="attributes"><?php echo smartyTranslate(array('s'=>'Attributes'),$_smarty_tpl);?>
</option>
			<option value="categories"><?php echo smartyTranslate(array('s'=>'Categories:'),$_smarty_tpl);?>
</option>
			<option value="manufacturers"><?php echo smartyTranslate(array('s'=>'Manufacturers:'),$_smarty_tpl);?>
</option>
			<option value="suppliers"><?php echo smartyTranslate(array('s'=>'Suppliers'),$_smarty_tpl);?>
</option>
		</select>
		<a href="javascript:addProductRule(<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
);">
			<input type="button" class="button" value="OK" />
		</a>
		<table id="product_rule_table_<?php echo intval($_smarty_tpl->tpl_vars['product_rule_group_id']->value);?>
" class="table" cellpadding="0" cellspacing="0">
			<?php if (isset($_smarty_tpl->tpl_vars['product_rules']->value)&&count($_smarty_tpl->tpl_vars['product_rules']->value)){?>
				<?php  $_smarty_tpl->tpl_vars['product_rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product_rule']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product_rules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product_rule']->key => $_smarty_tpl->tpl_vars['product_rule']->value){
$_smarty_tpl->tpl_vars['product_rule']->_loop = true;
?>
					<?php echo $_smarty_tpl->tpl_vars['product_rule']->value;?>

				<?php } ?>
			<?php }?>
		</table>
	</td>
</tr><?php }} ?>