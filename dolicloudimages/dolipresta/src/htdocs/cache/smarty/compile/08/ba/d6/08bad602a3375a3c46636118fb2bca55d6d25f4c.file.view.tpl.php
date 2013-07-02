<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:05
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/marketing/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4893099951c1c0e57cd8a1-49984011%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '08bad602a3375a3c46636118fb2bca55d6d25f4c' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/marketing/helpers/view/view.tpl',
      1 => 1371647935,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4893099951c1c0e57cd8a1-49984011',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'show_toolbar' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'modules_list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e57ebe85_01043482',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e57ebe85_01043482')) {function content_51c1c0e57ebe85_01043482($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['modules_list']->value;?>
<?php }} ?>