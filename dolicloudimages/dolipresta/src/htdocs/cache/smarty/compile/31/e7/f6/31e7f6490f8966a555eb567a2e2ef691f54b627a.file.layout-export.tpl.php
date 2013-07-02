<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:57
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/layout-export.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28269662851c1c0dd9b22f1-86016401%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '31e7f6490f8966a555eb567a2e2ef691f54b627a' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/layout-export.tpl',
      1 => 1371647305,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28269662851c1c0dd9b22f1-86016401',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'export_precontent' => 0,
    'export_headers' => 0,
    'header' => 0,
    'export_content' => 0,
    'line' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0dd9d6640_47450684',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0dd9d6640_47450684')) {function content_51c1c0dd9d6640_47450684($_smarty_tpl) {?>
<?php echo $_smarty_tpl->tpl_vars['export_precontent']->value;?>
<?php  $_smarty_tpl->tpl_vars['header'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['header']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['export_headers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['header']->key => $_smarty_tpl->tpl_vars['header']->value){
$_smarty_tpl->tpl_vars['header']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['header']->value;?>
;<?php } ?>
<?php  $_smarty_tpl->tpl_vars['line'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['line']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['export_content']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['line']->key => $_smarty_tpl->tpl_vars['line']->value){
$_smarty_tpl->tpl_vars['line']->_loop = true;
?>

<?php  $_smarty_tpl->tpl_vars['content'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['content']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['line']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['content']->key => $_smarty_tpl->tpl_vars['content']->value){
$_smarty_tpl->tpl_vars['content']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
;<?php } ?>
<?php } ?><?php }} ?>