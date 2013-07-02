<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:10
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-opc-payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:62278884251c1c0eae5dd29-20313435%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bd6f8727465a7db8187bd1a0082d837474758a82' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-opc-payment.tpl',
      1 => 1371647172,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '62278884251c1c0eae5dd29-20313435',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'HOOK_PAYMENT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0eae737f2_27979867',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0eae737f2_27979867')) {function content_51c1c0eae737f2_27979867($_smarty_tpl) {?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Payment: '),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div data-role="content">
	<fieldset data-role="controlgroup">
		<?php echo $_smarty_tpl->tpl_vars['HOOK_PAYMENT']->value;?>

	</fieldset>
</div>
<?php }} ?>