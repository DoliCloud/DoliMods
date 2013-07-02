<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:11
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/page-title.tpl" */ ?>
<?php /*%%SmartyHeaderCode:62088892151c1c0eb85f408-82226633%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eb427f6db2f13192b60c0e46f1be483c8e4ae7bd' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/page-title.tpl',
      1 => 1371647175,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '62088892151c1c0eb85f408-82226633',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page_title' => 0,
    'meta_title' => 0,
    'shop_name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0eb8991f1_28564001',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0eb8991f1_28564001')) {function content_51c1c0eb8991f1_28564001($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><?php if (!isset($_smarty_tpl->tpl_vars['page_title']->value)&&isset($_smarty_tpl->tpl_vars['meta_title']->value)&&$_smarty_tpl->tpl_vars['meta_title']->value!=$_smarty_tpl->tpl_vars['shop_name']->value){?>
	<?php $_smarty_tpl->tpl_vars['page_title'] = new Smarty_variable(smarty_modifier_escape($_smarty_tpl->tpl_vars['meta_title']->value, 'htmlall', 'UTF-8'), null, 0);?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['page_title']->value)){?>
	<div data-role="header" class="clearfix navbartop" data-position="inline">
		<h1><?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
</h1>
	</div><!-- /navbartop -->
<?php }?><?php }} ?>