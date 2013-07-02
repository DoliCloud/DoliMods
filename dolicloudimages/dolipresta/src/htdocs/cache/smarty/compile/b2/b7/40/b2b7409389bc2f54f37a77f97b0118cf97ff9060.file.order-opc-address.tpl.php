<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:10
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-opc-address.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17334767151c1c0ea98f5a1-06389912%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b2b7409389bc2f54f37a77f97b0118cf97ff9060' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-opc-address.tpl',
      1 => 1371647172,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17334767151c1c0ea98f5a1-06389912',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'delivery' => 0,
    'delivery_state' => 0,
    'addresses' => 0,
    'address' => 0,
    'invoice' => 0,
    'invoice_state' => 0,
    'opc' => 0,
    'back_order_page' => 0,
    'back' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0eab66e27_17044910',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0eab66e27_17044910')) {function content_51c1c0eab66e27_17044910($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div data-role="content" id="address-section">
	<div class="ui-grid-a margin-bottom-10px">
		<div class="ui-block-a">
			<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Delivery address'),$_smarty_tpl);?>
</h3>
			<?php if (isset($_smarty_tpl->tpl_vars['delivery']->value)){?>
				<ul class="adress">
					<li class="address_name"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->firstname, 'htmlall', 'UTF-8');?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->lastname, 'htmlall', 'UTF-8');?>
</li>
					<?php if ($_smarty_tpl->tpl_vars['delivery']->value->company){?>
						<li class="address_company"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->company, 'htmlall', 'UTF-8');?>
</li>
					<?php }?>
					<li class="address_address1"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->address1, 'htmlall', 'UTF-8');?>
</li>
					<?php if ($_smarty_tpl->tpl_vars['delivery']->value->address2){?>
						<li class="address_address2"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->address2, 'htmlall', 'UTF-8');?>
</li>
					<?php }?>
					<li class="address_city"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->postcode, 'htmlall', 'UTF-8');?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->city, 'htmlall', 'UTF-8');?>
</li>
					<li class="address_country"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery']->value->country, 'htmlall', 'UTF-8');?>
 <?php if ($_smarty_tpl->tpl_vars['delivery_state']->value){?>(<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['delivery_state']->value, 'htmlall', 'UTF-8');?>
)<?php }?></li>
				</ul>
			<?php }?>
			<label for="delivery-address-choice" class="select"><?php echo smartyTranslate(array('s'=>'Change address:'),$_smarty_tpl);?>
</label>
			<select
				name="delivery-address-choice"
				id="delivery-address-choice"
				class="address-field"
				data-mini="true"
				data-address-type="delivery"
			>
				<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
"<?php if (($_smarty_tpl->tpl_vars['address']->value['id_address']==$_smarty_tpl->tpl_vars['delivery']->value->id)){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['address']->value['alias'];?>
</option>
				<?php } ?>
			</select>
		</div>
		<div class="ui-block-b">
			<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Invoice address'),$_smarty_tpl);?>
</h3>
			<?php if (isset($_smarty_tpl->tpl_vars['invoice']->value)){?>
				<ul class="adress">
					<li class="address_name"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->firstname, 'htmlall', 'UTF-8');?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->lastname, 'htmlall', 'UTF-8');?>
</li>
					<?php if ($_smarty_tpl->tpl_vars['invoice']->value->company){?>
						<li class="address_company"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->company, 'htmlall', 'UTF-8');?>
</li>
					<?php }?>
					<li class="address_address1"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->address1, 'htmlall', 'UTF-8');?>
</li>
					<?php if ($_smarty_tpl->tpl_vars['invoice']->value->address2){?>
						<li class="address_address2"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->address2, 'htmlall', 'UTF-8');?>
</li>
					<?php }?>
					<li class="address_city"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->postcode, 'htmlall', 'UTF-8');?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->city, 'htmlall', 'UTF-8');?>
</li>
					<li class="address_country"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice']->value->country, 'htmlall', 'UTF-8');?>
 <?php if ($_smarty_tpl->tpl_vars['invoice_state']->value){?>(<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoice_state']->value, 'htmlall', 'UTF-8');?>
)<?php }?></li>
				</ul>
			<?php }else{ ?>
				<p class="warning"><?php echo smartyTranslate(array('s'=>'You must specify your delivery and invoice address'),$_smarty_tpl);?>
</p>
			<?php }?>
			<label for="invoice-address-choice" class="select"><?php echo smartyTranslate(array('s'=>'Change address:'),$_smarty_tpl);?>
</label>
			<select
				name="invoice-address-choice"
				id="invoice-address-choice"
				class="address-field"
				data-mini="true"
				data-address-type="invoice"
			>
				<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
"<?php if (($_smarty_tpl->tpl_vars['address']->value['id_address']==$_smarty_tpl->tpl_vars['invoice']->value->id)){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['address']->value['alias'];?>
</option>
				<?php } ?>
			</select>
		</div>
	</div>

	<?php if ($_smarty_tpl->tpl_vars['opc']->value){?>
		<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order-opc.php", null, 0);?>
		<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order.php", null, 0);?>
	<?php }?>

	<p><a href="<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&mod=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back_order_page']->value)."?step=1".$_tmp1);?>
" title="<?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
" data-role="button" data-theme="e" data-icon="plus" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
</a><br /></p>

</div>
<?php }} ?>