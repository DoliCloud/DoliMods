<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:05
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/tax_rules/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:848742051c1c0e513cdc1-46635929%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5261b985e338504454fda00d161b553f8e790a3' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/tax_rules/helpers/list/list_action_edit.tpl',
      1 => 1371647969,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '848742051c1c0e513cdc1-46635929',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'id' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e514ed85_43503271',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e514ed85_43503271')) {function content_51c1c0e514ed85_43503271($_smarty_tpl) {?>
<a onclick="loadTaxRule('<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
'); return false;" href="#"><img src="../img/admin/edit.gif" alt="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" /></a><?php }} ?>