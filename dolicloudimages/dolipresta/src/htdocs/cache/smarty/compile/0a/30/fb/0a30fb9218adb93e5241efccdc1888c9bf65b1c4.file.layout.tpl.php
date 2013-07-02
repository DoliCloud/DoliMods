<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:09
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/layout.tpl" */ ?>
<?php /*%%SmartyHeaderCode:156373867751c1c0e9291cc8-03152874%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0a30fb9218adb93e5241efccdc1888c9bf65b1c4' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/layout.tpl',
      1 => 1371647170,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '156373867751c1c0e9291cc8-03152874',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'display_header' => 0,
    'header_file' => 0,
    'template' => 0,
    'display_footer' => 0,
    'footer_file' => 0,
    'live_edit' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e92e3650_50343140',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e92e3650_50343140')) {function content_51c1c0e92e3650_50343140($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars['header_file'] = new Smarty_variable('./header.tpl', null, 0);?>
<?php $_smarty_tpl->tpl_vars['footer_file'] = new Smarty_variable('./footer.tpl', null, 0);?>

<?php if (!empty($_smarty_tpl->tpl_vars['display_header']->value)){?>
	<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['header_file']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?>
<?php if (!empty($_smarty_tpl->tpl_vars['template']->value)){?>
	<?php echo $_smarty_tpl->tpl_vars['template']->value;?>

<?php }?>
<?php if (!empty($_smarty_tpl->tpl_vars['display_footer']->value)){?>
	<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['footer_file']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?>
<?php if (!empty($_smarty_tpl->tpl_vars['live_edit']->value)){?>
	<?php echo $_smarty_tpl->tpl_vars['live_edit']->value;?>

<?php }?>
<?php }} ?>