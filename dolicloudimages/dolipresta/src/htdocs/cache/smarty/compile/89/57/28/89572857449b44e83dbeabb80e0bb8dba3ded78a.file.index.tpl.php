<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:09
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:102934506051c1c0e927fe53-11572181%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '89572857449b44e83dbeabb80e0bb8dba3ded78a' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/index.tpl',
      1 => 1371647170,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '102934506051c1c0e927fe53-11572181',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e928c195_25297525',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e928c195_25297525')) {function content_51c1c0e928c195_25297525($_smarty_tpl) {?>
	<div data-role="content" id="content">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"DisplayMobileIndex"),$_smarty_tpl);?>

		<?php echo $_smarty_tpl->getSubTemplate ('./sitemap.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div><!-- /content -->
<?php }} ?>