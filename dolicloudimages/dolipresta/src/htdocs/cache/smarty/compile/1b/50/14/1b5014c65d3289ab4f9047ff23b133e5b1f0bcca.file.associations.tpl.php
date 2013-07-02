<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:02
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/associations.tpl" */ ?>
<?php /*%%SmartyHeaderCode:116918726951c1c0e25f1829-55003523%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1b5014c65d3289ab4f9047ff23b133e5b1f0bcca' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/associations.tpl',
      1 => 1371647788,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '116918726951c1c0e25f1829-55003523',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category_tree' => 0,
    'link' => 0,
    'selected_cat' => 0,
    'cat' => 0,
    'id_category_default' => 0,
    'accessories' => 0,
    'accessory' => 0,
    'product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e2702219_94796169',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e2702219_94796169')) {function content_51c1c0e2702219_94796169($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<input type="hidden" name="submitted_tabs[]" value="Associations" />
<div class="Associations">
	<h4><?php echo smartyTranslate(array('s'=>'Associations'),$_smarty_tpl);?>
</h4>

	<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('product_tab'=>"Associations"), 0);?>

	<div class="separation"></div>
		<div id="no_default_category" class="hint">
		<?php echo smartyTranslate(array('s'=>'Please select a default category.'),$_smarty_tpl);?>

	</div>
	<table>
		<tr>
			<td class="col-left">
				<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"category_box",'type'=>"category_box"), 0);?>

				<label for="category_block"><?php echo smartyTranslate(array('s'=>'Associated categories:'),$_smarty_tpl);?>
</label>
			</td>
			<td class="col-right">
				<div id="category_block">
					<?php echo $_smarty_tpl->tpl_vars['category_tree']->value;?>

				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td class="col-right">
					<a class="button bt-icon confirm_leave" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCategories'), 'htmlall', 'UTF-8');?>
&addcategory">
						<img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Create new category'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Create new category'),$_smarty_tpl);?>
" />
						<span><?php echo smartyTranslate(array('s'=>'Create new category'),$_smarty_tpl);?>
</span>
					</a>
			</td>
		</tr>
		<tr>
			<td class="col-left">
				<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"id_category_default",'type'=>"default"), 0);?>

				<label for="id_category_default"><?php echo smartyTranslate(array('s'=>'Default category:'),$_smarty_tpl);?>
</label>
			</td>
			<td class="col-right">
				<select id="id_category_default" name="id_category_default">
					<?php  $_smarty_tpl->tpl_vars['cat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['selected_cat']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cat']->key => $_smarty_tpl->tpl_vars['cat']->value){
$_smarty_tpl->tpl_vars['cat']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['cat']->value['id_category'];?>
" <?php if ($_smarty_tpl->tpl_vars['id_category_default']->value==$_smarty_tpl->tpl_vars['cat']->value['id_category']){?>selected="selected"<?php }?> ><?php echo $_smarty_tpl->tpl_vars['cat']->value['name'];?>
</option>
					<?php } ?>
				</select>
				<div class="hint" style="display:block;"><?php echo smartyTranslate(array('s'=>'The default category is the category displayed by default.'),$_smarty_tpl);?>
</div>
			</td>
		</tr>
	</table>
<div class="separation"></div>
	<table>
		<tr>
			<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Accessories:'),$_smarty_tpl);?>
</label></td>
			<td style="padding-bottom:5px;">
				<input type="hidden" name="inputAccessories" id="inputAccessories" value="<?php  $_smarty_tpl->tpl_vars['accessory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accessory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accessories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accessory']->key => $_smarty_tpl->tpl_vars['accessory']->value){
$_smarty_tpl->tpl_vars['accessory']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['accessory']->value['id_product'];?>
-<?php } ?>" />
				<input type="hidden" name="nameAccessories" id="nameAccessories" value="<?php  $_smarty_tpl->tpl_vars['accessory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accessory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accessories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accessory']->key => $_smarty_tpl->tpl_vars['accessory']->value){
$_smarty_tpl->tpl_vars['accessory']->_loop = true;
?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['accessory']->value['name'], 'htmlall', 'UTF-8');?>
¤<?php } ?>" />

				<div id="ajax_choose_product">
					<p style="clear:both;margin-top:0;">
						<input type="text" value="" id="product_autocomplete_input" />
						<?php echo smartyTranslate(array('s'=>'Begin typing the first letters of the product name, then select the product from the drop-down list.'),$_smarty_tpl);?>

					</p>
					<p class="preference_description"><?php echo smartyTranslate(array('s'=>'(Do not forget to save the product afterward)'),$_smarty_tpl);?>
</p>
					<!--<img onclick="$(this).prev().search();" style="cursor: pointer;" src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Add an accessory'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Add an accessory'),$_smarty_tpl);?>
" />-->
				</div>
				<div id="divAccessories">
					
					<?php  $_smarty_tpl->tpl_vars['accessory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accessory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accessories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accessory']->key => $_smarty_tpl->tpl_vars['accessory']->value){
$_smarty_tpl->tpl_vars['accessory']->_loop = true;
?>
						<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['accessory']->value['name'], 'htmlall', 'UTF-8');?>
<?php if (!empty($_smarty_tpl->tpl_vars['accessory']->value['reference'])){?><?php echo $_smarty_tpl->tpl_vars['accessory']->value['reference'];?>
<?php }?>
						<span class="delAccessory" name="<?php echo $_smarty_tpl->tpl_vars['accessory']->value['id_product'];?>
" style="cursor: pointer;">
							<img src="../img/admin/delete.gif" class="middle" alt="" />
						</span><br />
					<?php } ?>
				</div>
			</td>
		</tr>
		<tr>
		<br />
		<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Manufacturer:'),$_smarty_tpl);?>
</label></td>
		<td style="padding-bottom:5px;">
			<select name="id_manufacturer" id="id_manufacturer">
				<option value="0">-- <?php echo smartyTranslate(array('s'=>'Choose (optional)'),$_smarty_tpl);?>
 --</option>
				<?php if ($_smarty_tpl->tpl_vars['product']->value->id_manufacturer){?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['product']->value->id_manufacturer;?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['product']->value->manufacturer_name;?>
</option>
				<?php }?>
				<option disabled="disabled">----------</option>
			</select>&nbsp;&nbsp;&nbsp;
			<a class="button bt-icon confirm_leave" style="margin-bottom:0" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminManufacturers'), 'htmlall', 'UTF-8');?>
&addmanufacturer">
				<img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Create new manufacturer'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Create new manufacturer'),$_smarty_tpl);?>
" />
				<span><?php echo smartyTranslate(array('s'=>'Create new manufacturer'),$_smarty_tpl);?>
</span>
			</a>
		</td>
		</tr>
	</table>
</div><?php }} ?>