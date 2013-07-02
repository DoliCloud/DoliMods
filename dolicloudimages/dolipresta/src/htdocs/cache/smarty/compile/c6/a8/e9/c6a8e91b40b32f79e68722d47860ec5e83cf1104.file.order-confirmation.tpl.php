<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:01
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-confirmation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:84472979951c1c0e12426c6-72027118%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c6a8e91b40b32f79e68722d47860ec5e83cf1104' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-confirmation.tpl',
      1 => 1371646830,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '84472979951c1c0e12426c6-72027118',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'HOOK_ORDER_CONFIRMATION' => 0,
    'HOOK_PAYMENT_RETURN' => 0,
    'is_guest' => 0,
    'id_order_formatted' => 0,
    'reference_order' => 0,
    'email' => 0,
    'link' => 0,
    'img_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e12f81e5_75610523',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e12f81e5_75610523')) {function content_51c1c0e12f81e5_75610523($_smarty_tpl) {?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Order confirmation'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<h1><?php echo smartyTranslate(array('s'=>'Order confirmation'),$_smarty_tpl);?>
</h1>

<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('payment', null, 0);?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->tpl_vars['HOOK_ORDER_CONFIRMATION']->value;?>

<?php echo $_smarty_tpl->tpl_vars['HOOK_PAYMENT_RETURN']->value;?>


<br />
<?php if ($_smarty_tpl->tpl_vars['is_guest']->value){?>
	<p><?php echo smartyTranslate(array('s'=>'Your order ID is:'),$_smarty_tpl);?>
 <span class="bold"><?php echo $_smarty_tpl->tpl_vars['id_order_formatted']->value;?>
</span> . <?php echo smartyTranslate(array('s'=>'Your order ID has been sent via email.'),$_smarty_tpl);?>
</p>
	<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('guest-tracking',true,null,"id_order=".((string)$_smarty_tpl->tpl_vars['reference_order']->value)."&email=".((string)$_smarty_tpl->tpl_vars['email']->value));?>
" title="<?php echo smartyTranslate(array('s'=>'Follow my order'),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/order.gif" alt="<?php echo smartyTranslate(array('s'=>'Follow my order'),$_smarty_tpl);?>
" class="icon" /></a>
	<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('guest-tracking',true,null,"id_order=".((string)$_smarty_tpl->tpl_vars['reference_order']->value)."&email=".((string)$_smarty_tpl->tpl_vars['email']->value));?>
" title="<?php echo smartyTranslate(array('s'=>'Follow my order'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Follow my order'),$_smarty_tpl);?>
</a>
<?php }else{ ?>
	<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('history',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Back to orders'),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/order.gif" alt="<?php echo smartyTranslate(array('s'=>'Back to orders'),$_smarty_tpl);?>
" class="icon" /></a>
	<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('history',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Back to orders'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Back to orders'),$_smarty_tpl);?>
</a>
<?php }?>
<?php }} ?>