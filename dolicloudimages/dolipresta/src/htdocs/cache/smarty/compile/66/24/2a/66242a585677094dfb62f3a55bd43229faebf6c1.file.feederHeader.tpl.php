<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 17:29:33
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/modules/feeder/feederHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:175347508351c1ce5d0c8ab1-20749212%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '66242a585677094dfb62f3a55bd43229faebf6c1' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/modules/feeder/feederHeader.tpl',
      1 => 1371646755,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175347508351c1ce5d0c8ab1-20749212',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'meta_title' => 0,
    'feedUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5d1584f1_97745139',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5d1584f1_97745139')) {function content_51c1ce5d1584f1_97745139($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<link rel="alternate" type="application/rss+xml" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['meta_title']->value, 'html', 'UTF-8');?>
" href="<?php echo $_smarty_tpl->tpl_vars['feedUrl']->value;?>
" /><?php }} ?>