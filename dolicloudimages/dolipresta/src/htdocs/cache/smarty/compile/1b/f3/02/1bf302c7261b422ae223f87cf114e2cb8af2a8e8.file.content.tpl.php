<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:58
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/attribute_generator/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:99275969151c1c0de657e74-19648582%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1bf302c7261b422ae223f87cf114e2cb8af2a8e8' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/attribute_generator/content.tpl',
      1 => 1371647763,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '99275969151c1c0de657e74-19648582',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tax_rates' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'generate' => 0,
    'combinations_size' => 0,
    'url_generator' => 0,
    'attribute_groups' => 0,
    'attribute_group' => 0,
    'attribute_js' => 0,
    'k' => 0,
    'v' => 0,
    'product_name' => 0,
    'currency_sign' => 0,
    'weight_unit' => 0,
    'attributes' => 0,
    'attribute' => 0,
    'product_reference' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0de7dbb53_75911392',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0de7dbb53_75911392')) {function content_51c1c0de7dbb53_75911392($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<script type="text/javascript">
	i18n_tax_exc = '<?php echo smartyTranslate(array('s'=>'Tax Excluded'),$_smarty_tpl);?>
 ';
	i18n_tax_inc = '<?php echo smartyTranslate(array('s'=>'Tax Included'),$_smarty_tpl);?>
 ';

	var product_tax = '<?php echo $_smarty_tpl->tpl_vars['tax_rates']->value;?>
';
	function calcPrice(element, element_has_tax)
	{
			var element_price = element.val().replace(/,/g, '.');
			var other_element_price = 0;

			if (!isNaN(element_price) && element_price > 0)
			{
				if (element_has_tax)
					other_element_price = parseFloat(element_price / ((product_tax / 100) + 1)).toFixed(6);
				else
					other_element_price = ps_round(parseFloat(element_price * ((product_tax / 100) + 1)), 2).toFixed(2);
			}

			$('#related_to_'+element.attr('name')).val(other_element_price);
	}

	$(document).ready(function() { $('.price_impact').each(function() { calcPrice($(this), false); }); });
</script>

<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<div class="leadin"></div>

<?php if ($_smarty_tpl->tpl_vars['generate']->value){?><div class="module_confirmation conf confirm"><?php echo smartyTranslate(array('s'=>'%d product(s) successfully created.','sprintf'=>$_smarty_tpl->tpl_vars['combinations_size']->value),$_smarty_tpl);?>
</div><?php }?>
<script type="text/javascript" src="../js/attributesBack.js"></script>
<form enctype="multipart/form-data" method="post" id="generator" action="<?php echo $_smarty_tpl->tpl_vars['url_generator']->value;?>
">
	<fieldset style="margin-bottom: 35px;">
		<legend><img src="../img/admin/asterisk.gif" alt="" /><?php echo smartyTranslate(array('s'=>'Attributes generator'),$_smarty_tpl);?>
</legend>
		<div style="float: left; margin-right: 50px;">
			<select multiple name="attributes[]" id="attribute_group" style="height: 500px; margin-bottom: 10px;">
				<?php  $_smarty_tpl->tpl_vars['attribute_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute_group']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attribute_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute_group']->key => $_smarty_tpl->tpl_vars['attribute_group']->value){
$_smarty_tpl->tpl_vars['attribute_group']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['attribute_group']->key;
?>
					<?php if (isset($_smarty_tpl->tpl_vars['attribute_js']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']])){?>
						<optgroup name="<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
" label="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['attribute_group']->value['name'], 'htmlall', 'UTF-8');?>
">
							<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attribute_js']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
								<option name="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" id="attr_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['v']->value, 'htmlall', 'UTF-8');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['v']->value, 'htmlall', 'UTF-8');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['v']->value, 'htmlall', 'UTF-8');?>
</option>
							<?php } ?>
						</optgroup>
					<?php }?>
				<?php } ?>
			</select>
			<div style="text-align: center; margin-bottom: 10px;">
				<p>
					<input class="button" type="button" style="margin-right: 15px;" value="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" class="button" onclick="add_attr_multiple();" />
					<input class="button" type="button" value="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" class="button" onclick="del_attr_multiple();" />
				</p>
			</div>
		</div>
		<div style="float: left; width: 570px;">
			<div class="hint" style="width: 570px; padding-left: 45px; margin-bottom: 15px; display: block; position: inherit;"><?php echo smartyTranslate(array('s'=>'The Combinations Generator is a tool that allows you to easily create a series of combinations by selecting the related attributes. For example, if you\'re selling t-shirts in three different sizes and two different colors, the generator will create six combinations for you.'),$_smarty_tpl);?>
</div>
			<p><?php echo smartyTranslate(array('s'=>'You\'re currently generating combinations for the following product:'),$_smarty_tpl);?>
 <b><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product_name']->value, 'htmlall', 'UTF-8');?>
</b></p>
			<h4><?php echo smartyTranslate(array('s'=>'Step 1: On the left side, select the attributes you want to use (Hold down the "Ctrl" key on your keyboard and validate by clicking on "Add")'),$_smarty_tpl);?>
</h4>
			<div>
			<?php  $_smarty_tpl->tpl_vars['attribute_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute_group']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attribute_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute_group']->key => $_smarty_tpl->tpl_vars['attribute_group']->value){
$_smarty_tpl->tpl_vars['attribute_group']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['attribute_group']->key;
?>
				<?php if (isset($_smarty_tpl->tpl_vars['attribute_js']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']])){?>
					<table class="table clear" cellpadding="0" cellspacing="0" style="margin-bottom: 10px; display: none;">
						<thead>
							<tr>
								<th id="tab_h1" style="width: 150px"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['attribute_group']->value['name'], 'htmlall', 'UTF-8');?>
</th>
								<th id="tab_h2" style="width: 350px" colspan="2"><?php echo smartyTranslate(array('s'=>'Impact on the product price'),$_smarty_tpl);?>
 (<?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
)</th>
								<th style="width: 150px"><?php echo smartyTranslate(array('s'=>'Impact on the product weight'),$_smarty_tpl);?>
 (<?php echo $_smarty_tpl->tpl_vars['weight_unit']->value;?>
)</th>
							</tr>
						</thead>
						<tbody id="table_<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
" name="result_table">
						</tbody>
					</table>
					<?php if (isset($_smarty_tpl->tpl_vars['attributes']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']])){?>
						<?php  $_smarty_tpl->tpl_vars['attribute'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attributes']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute']->key => $_smarty_tpl->tpl_vars['attribute']->value){
$_smarty_tpl->tpl_vars['attribute']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['attribute']->key;
?>
							<script type="text/javascript">
								$('#table_<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
').append(create_attribute_row(<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
, '<?php echo addslashes($_smarty_tpl->tpl_vars['attribute']->value['attribute_name']);?>
', <?php echo $_smarty_tpl->tpl_vars['attribute']->value['price'];?>
, <?php echo $_smarty_tpl->tpl_vars['attribute']->value['weight'];?>
));
								toggle(getE('table_' + <?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
).parentNode, true);
							</script>
						<?php } ?>						
					<?php }?>
				<?php }?>
			<?php } ?>
            </div>
			<h4><?php echo smartyTranslate(array('s'=>'Select a default quantity, and reference, for each combination the generator will create for this product.'),$_smarty_tpl);?>
</h4>
			<table border="0" class="table" cellpadding="0" cellspacing="0">
				<tr>
					<td><?php echo smartyTranslate(array('s'=>'Default Quantity:'),$_smarty_tpl);?>
</td>
					<td><input type="text" size="20" name="quantity" value="0" style="width: 50px;" /></td>
				</tr>
				<tr>
					<td><?php echo smartyTranslate(array('s'=>'Default Reference:'),$_smarty_tpl);?>
</td>
					<td><input type="text" size="20" name="reference" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product_reference']->value, 'htmlall', 'UTF-8');?>
" /></td>
				</tr>
			</table>
			<h4><?php echo smartyTranslate(array('s'=>'Please click on "Generate these Combinations"'),$_smarty_tpl);?>
</h4>
			<p><input type="submit" class="button" style="margin-bottom:5px;" name="generate" value="<?php echo smartyTranslate(array('s'=>'Generate these Combinations'),$_smarty_tpl);?>
" /></p>
		</div>
	</fieldset>
</form><?php }} ?>