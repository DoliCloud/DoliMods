<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:06
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/list/list_action_default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:97062686051c1c0e69c3292-53679675%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '556786422dadc867c998f132a2c98a409d52c0ed' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/list/list_action_default.tpl',
      1 => 1371647814,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97062686051c1c0e69c3292-53679675',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e69e4600_57613420',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e69e4600_57613420')) {function content_51c1c0e69e4600_57613420($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" class="default" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" <?php if (isset($_smarty_tpl->tpl_vars['name']->value)){?>name="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
"<?php }?>>
	<img src="../img/admin/asterisk.gif" alt="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" />
</a><?php }} ?>