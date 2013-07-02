<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/suppliers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:175477667151c1c0e3d33b00-99610274%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5d92e74b53b2c08f4862623d4a39a02066868ff' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/suppliers.tpl',
      1 => 1371647791,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175477667151c1c0e3d33b00-99610274',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'link' => 0,
    'suppliers' => 0,
    'supplier' => 0,
    'associated_suppliers' => 0,
    'attributes' => 0,
    'id_default_currency' => 0,
    'associated_suppliers_collection' => 0,
    'asc' => 0,
    'attribute' => 0,
    'index' => 0,
    'product_designation' => 0,
    'reference' => 0,
    'price_te' => 0,
    'currencies' => 0,
    'currency' => 0,
    'id_currency' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e3f123b5_34551851',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e3f123b5_34551851')) {function content_51c1c0e3f123b5_34551851($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<input type="hidden" name="supplier_loaded" value="1">
<?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)){?>
	<input type="hidden" name="submitted_tabs[]" value="Suppliers" />
	<h4><?php echo smartyTranslate(array('s'=>'Suppliers of the current product'),$_smarty_tpl);?>
</h4>
	<div class="separation"></div>
	<div class="hint" style="display:block; position:'auto';">
		<p><?php echo smartyTranslate(array('s'=>'This interface allows you to specify the suppliers of the current product and eventually its combinations.'),$_smarty_tpl);?>
</p>
		<p><?php echo smartyTranslate(array('s'=>'It is also possible to specify supplier references according to previously associated suppliers.'),$_smarty_tpl);?>
</p>
		<br />
		<p><?php echo smartyTranslate(array('s'=>'When using the advanced stock management tool (see Preferences/Products), the values you define (prices, references) will be used in supply orders.'),$_smarty_tpl);?>
</p>
	</div>
	<p><?php echo smartyTranslate(array('s'=>'Please choose the suppliers associated with this product. Please select a default supplier, as well.'),$_smarty_tpl);?>
</p>
	<a class="button bt-icon confirm_leave" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminSuppliers'), 'htmlall', 'UTF-8');?>
&addsupplier">
		<img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Create a new supplier'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Create a new supplier'),$_smarty_tpl);?>
" /><span><?php echo smartyTranslate(array('s'=>'Create a new supplier'),$_smarty_tpl);?>
</span>
	</a>
	<table cellpadding="5" style="width:100%">
		<tbody>
			<tr>
				<td valign="top" style="text-align:left;vertical-align:top;">
					<table class="table" cellpadding="0" cellspacing="0" style="width:50%;">
						<thead>
							<tr>
								<th><?php echo smartyTranslate(array('s'=>'Selected'),$_smarty_tpl);?>
</th>
								<th><?php echo smartyTranslate(array('s'=>'Supplier Name'),$_smarty_tpl);?>
</th>
								<th><?php echo smartyTranslate(array('s'=>'Default'),$_smarty_tpl);?>
</th>
							</tr>
						</thead>
						<tbody>
						<?php  $_smarty_tpl->tpl_vars['supplier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['supplier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['suppliers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['supplier']->key => $_smarty_tpl->tpl_vars['supplier']->value){
$_smarty_tpl->tpl_vars['supplier']->_loop = true;
?>
							<tr>
								<td><input type="checkbox" class="supplierCheckBox" name="check_supplier_<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" <?php if ($_smarty_tpl->tpl_vars['supplier']->value['is_selected']==true){?>checked="checked"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" /></td>
								<td><?php echo $_smarty_tpl->tpl_vars['supplier']->value['name'];?>
</td>
								<td><input type="radio" id="default_supplier_<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" name="default_supplier" value="<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" <?php if ($_smarty_tpl->tpl_vars['supplier']->value['is_selected']==false){?>disabled="disabled"<?php }?> <?php if ($_smarty_tpl->tpl_vars['supplier']->value['is_default']==true){?>checked="checked"<?php }?> /></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<p>&nbsp;</p>
					<h4><?php echo smartyTranslate(array('s'=>'Product reference(s)'),$_smarty_tpl);?>
</h4>
	<div class="separation"></div>
	<?php if (count($_smarty_tpl->tpl_vars['associated_suppliers']->value)==0){?>
		<p><?php echo smartyTranslate(array('s'=>'You must specify the suppliers associated with this product. You must also select the default product supplier before setting references.'),$_smarty_tpl);?>
</p>
	<?php }else{ ?>
		<p><?php echo smartyTranslate(array('s'=>'You can specify product reference(s) for each associated supplier.'),$_smarty_tpl);?>
</p>
	<?php }?>
	<p><?php echo smartyTranslate(array('s'=>'Click "Save and Stay" after changing selected suppliers to display the associated product references.'),$_smarty_tpl);?>
</p>
	<div id="suppliers_accordion" style="margin-top:10px; display:block;">
		<?php  $_smarty_tpl->tpl_vars['supplier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['supplier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['associated_suppliers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['supplier']->key => $_smarty_tpl->tpl_vars['supplier']->value){
$_smarty_tpl->tpl_vars['supplier']->_loop = true;
?>
		    <h3 style="margin-bottom:0;"><a href="#"><?php echo $_smarty_tpl->tpl_vars['supplier']->value->name;?>
</a></h3>
		    <div style="display:block;">

				<table cellpadding="10" cellspacing="0" class="table">

					<thead>
						<tr>
							<th><?php echo smartyTranslate(array('s'=>'Product name'),$_smarty_tpl);?>
</th>
							<th width="150"><?php echo smartyTranslate(array('s'=>'Supplier reference'),$_smarty_tpl);?>
</th>
							<th width="150"><?php echo smartyTranslate(array('s'=>'Unit price tax excluded'),$_smarty_tpl);?>
</th>
							<th width="150"><?php echo smartyTranslate(array('s'=>'Unit price currency'),$_smarty_tpl);?>
</th>
						</tr>
					</thead>
					<tbody>
					<?php  $_smarty_tpl->tpl_vars['attribute'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attributes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute']->key => $_smarty_tpl->tpl_vars['attribute']->value){
$_smarty_tpl->tpl_vars['attribute']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['attribute']->key;
?>
						<?php $_smarty_tpl->tpl_vars['reference'] = new Smarty_variable('', null, 0);?>
						<?php $_smarty_tpl->tpl_vars['price_te'] = new Smarty_variable('', null, 0);?>
						<?php $_smarty_tpl->tpl_vars['id_currency'] = new Smarty_variable($_smarty_tpl->tpl_vars['id_default_currency']->value, null, 0);?>
						<?php  $_smarty_tpl->tpl_vars['asc'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['asc']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['associated_suppliers_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['asc']->key => $_smarty_tpl->tpl_vars['asc']->value){
$_smarty_tpl->tpl_vars['asc']->_loop = true;
?>
							<?php if ($_smarty_tpl->tpl_vars['asc']->value->id_product==$_smarty_tpl->tpl_vars['attribute']->value['id_product']&&$_smarty_tpl->tpl_vars['asc']->value->id_product_attribute==$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']&&$_smarty_tpl->tpl_vars['asc']->value->id_supplier==$_smarty_tpl->tpl_vars['supplier']->value->id_supplier){?>
								<?php $_smarty_tpl->tpl_vars['reference'] = new Smarty_variable($_smarty_tpl->tpl_vars['asc']->value->product_supplier_reference, null, 0);?>
								<?php $_smarty_tpl->tpl_vars['price_te'] = new Smarty_variable(Tools::ps_round($_smarty_tpl->tpl_vars['asc']->value->product_supplier_price_te,2), null, 0);?>
								<?php if ($_smarty_tpl->tpl_vars['asc']->value->id_currency){?>
									<?php $_smarty_tpl->tpl_vars['id_currency'] = new Smarty_variable($_smarty_tpl->tpl_vars['asc']->value->id_currency, null, 0);?>
								<?php }?>
							<?php }?>
						<?php } ?>
						<tr <?php if ((1 & $_smarty_tpl->tpl_vars['index']->value)){?>class="alt_row"<?php }?>>
							<td><?php echo $_smarty_tpl->tpl_vars['product_designation']->value[$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']];?>
</td>
							<td>
								<input type="text" size="10" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['reference']->value, 'htmlall', 'UTF-8');?>
" name="supplier_reference_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id_supplier;?>
" />
							</td>
							<td>
								<input type="text" size="10" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['price_te']->value);?>
" name="product_price_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id_supplier;?>
" />
							</td>
							<td>
								<select name="product_price_currency_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id_supplier;?>
">
									<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value){
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['currency']->value['id_currency'];?>
"
											<?php if ($_smarty_tpl->tpl_vars['currency']->value['id_currency']==$_smarty_tpl->tpl_vars['id_currency']->value){?>selected="selected"<?php }?>
										><?php echo $_smarty_tpl->tpl_vars['currency']->value['name'];?>
</option>
									<?php } ?>
								</select>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</div>
<?php }?><?php }} ?>