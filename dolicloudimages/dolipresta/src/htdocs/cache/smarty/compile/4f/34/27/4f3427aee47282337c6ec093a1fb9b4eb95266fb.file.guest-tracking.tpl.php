<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/guest-tracking.tpl" */ ?>
<?php /*%%SmartyHeaderCode:170188436751c1c0df100109-15094046%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f3427aee47282337c6ec093a1fb9b4eb95266fb' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/guest-tracking.tpl',
      1 => 1371646827,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '170188436751c1c0df100109-15094046',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order_collection' => 0,
    'order' => 0,
    'transformSuccess' => 0,
    'link' => 0,
    'action' => 0,
    'show_login_link' => 0,
    'img_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0df3a21f8_98728793',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0df3a21f8_98728793')) {function content_51c1c0df3a21f8_98728793($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Guest tracking'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ("./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<h1><?php echo smartyTranslate(array('s'=>'Guest Tracking'),$_smarty_tpl);?>
</h1>

<?php if (isset($_smarty_tpl->tpl_vars['order_collection']->value)){?>
	<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value){
$_smarty_tpl->tpl_vars['order']->_loop = true;
?>
		<?php $_smarty_tpl->tpl_vars['order_state'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->getCurrentState(), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['invoice'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->invoice, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['order_history'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->order_history, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['carrier'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->carrier, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['address_invoice'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->address_invoice, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['address_delivery'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->address_delivery, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['inv_adr_fields'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->inv_adr_fields, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['dlv_adr_fields'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->dlv_adr_fields, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['invoiceAddressFormatedValues'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->invoiceAddressFormatedValues, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['deliveryAddressFormatedValues'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->deliveryAddressFormatedValues, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['currency'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->currency, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['discounts'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->discounts, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['invoiceState'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->invoiceState, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['deliveryState'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->deliveryState, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['products'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->products, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['customizedDatas'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->customizedDatas, null, 0);?>
		<?php $_smarty_tpl->tpl_vars['HOOK_ORDERDETAILDISPLAYED'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->hook_orderdetaildisplayed, null, 0);?>
		<?php if (isset($_smarty_tpl->tpl_vars['order']->value->total_old)){?>
			<?php $_smarty_tpl->tpl_vars['total_old'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_old, null, 0);?>
		<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['order']->value->followup)){?>
			<?php $_smarty_tpl->tpl_vars['followup'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->followup, null, 0);?>
		<?php }?>
		
		<div id="block-history">
			<div id="block-order-detail" class="std" style="zoom:1">
			<?php echo $_smarty_tpl->getSubTemplate ("./order-detail.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			</div>
		</div>
	<?php } ?>

	<h2 id="guestToCustomer"><?php echo smartyTranslate(array('s'=>'For more advantages...'),$_smarty_tpl);?>
</h2>

	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	
	<?php if (isset($_smarty_tpl->tpl_vars['transformSuccess']->value)){?>
		<p class="success"><?php echo smartyTranslate(array('s'=>'Your guest account has been successfully transformed into a customer account. You can now login as a registered shopper. '),$_smarty_tpl);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true);?>
"><?php echo smartyTranslate(array('s'=>'page.'),$_smarty_tpl);?>
</a></p>
	<?php }else{ ?>
		<form method="post" action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['action']->value, 'htmlall', 'UTF-8');?>
#guestToCustomer" class="std">
			<fieldset class="description_box">
				<p class="bold"><?php echo smartyTranslate(array('s'=>'Transform your guest account into a customer account and enjoy:'),$_smarty_tpl);?>
</p>
				<ul class="bullet">
					<li><?php echo smartyTranslate(array('s'=>'Personalized and secure access'),$_smarty_tpl);?>
</li>
					<li><?php echo smartyTranslate(array('s'=>'Fast and easy checkout'),$_smarty_tpl);?>
</li>
					<li><?php echo smartyTranslate(array('s'=>'Easier merchandise return'),$_smarty_tpl);?>
</li>
				</ul>
				<p class="text">
					<label><?php echo smartyTranslate(array('s'=>'Set your password:'),$_smarty_tpl);?>
</label>
					<input type="password" name="password" />
				</p>
				
				<input type="hidden" name="id_order" value="<?php if (isset($_smarty_tpl->tpl_vars['order']->value->id)){?><?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
<?php }else{ ?><?php if (isset($_GET['id_order'])){?><?php echo smarty_modifier_escape($_GET['id_order'], 'htmlall', 'UTF-8');?>
<?php }else{ ?><?php if (isset($_POST['id_order'])){?><?php echo smarty_modifier_escape($_POST['id_order'], 'htmlall', 'UTF-8');?>
<?php }?><?php }?><?php }?>" />
				<input type="hidden" name="order_reference" value="<?php if (isset($_GET['order_reference'])){?><?php echo smarty_modifier_escape($_GET['order_reference'], 'htmlall', 'UTF-8');?>
<?php }else{ ?><?php if (isset($_POST['order_reference'])){?><?php echo smarty_modifier_escape($_POST['order_reference'], 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				<input type="hidden" name="email" value="<?php if (isset($_GET['email'])){?><?php echo smarty_modifier_escape($_GET['email'], 'htmlall', 'UTF-8');?>
<?php }else{ ?><?php if (isset($_POST['email'])){?><?php echo smarty_modifier_escape($_POST['email'], 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				
				<p class="center"><input type="submit" class="exclusive_large" name="submitTransformGuestToCustomer" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
" /></p>
			</fieldset>
		</form>
	<?php }?>
<?php }else{ ?>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php if (isset($_smarty_tpl->tpl_vars['show_login_link']->value)&&$_smarty_tpl->tpl_vars['show_login_link']->value){?>
		<p><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/userinfo.gif" alt="<?php echo smartyTranslate(array('s'=>'Information'),$_smarty_tpl);?>
" class="icon" /><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><?php echo smartyTranslate(array('s'=>'Click here to login to your customer account.'),$_smarty_tpl);?>
</a><br /><br /></p>
	<?php }?>
	<form method="post" action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['action']->value, 'htmlall', 'UTF-8');?>
" class="std">
		<fieldset class="description_box">
			<p><?php echo smartyTranslate(array('s'=>'To track your order, please enter the following information:'),$_smarty_tpl);?>
</p>
			<p class="text">
				<label><?php echo smartyTranslate(array('s'=>'Order Reference:'),$_smarty_tpl);?>
 </label>
				<input type="text" name="order_reference" value="<?php if (isset($_GET['id_order'])){?><?php echo smarty_modifier_escape($_GET['id_order'], 'htmlall', 'UTF-8');?>
<?php }else{ ?><?php if (isset($_POST['id_order'])){?><?php echo smarty_modifier_escape($_POST['id_order'], 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" size="8" />
				<i><?php echo smartyTranslate(array('s'=>'For example: QIIXJXNUI or QIIXJXNUI#1'),$_smarty_tpl);?>
</i>
			</p>

			<p class="text">
				<label><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
</label>
				<input type="text" name="email" value="<?php if (isset($_GET['email'])){?><?php echo smarty_modifier_escape($_GET['email'], 'htmlall', 'UTF-8');?>
<?php }else{ ?><?php if (isset($_POST['email'])){?><?php echo smarty_modifier_escape($_POST['email'], 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
			</p>

			<p class="center"><input type="submit" class="button" name="submitGuestTracking" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
" /></p>
		</fieldset>
	</form>
<?php }?>
<?php }} ?>