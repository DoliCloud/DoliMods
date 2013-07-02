<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:00
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/not_found/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9470354051c1c0e0e779b5-17203816%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ea425de1c3944b19dcac4d7f9501949dd62a80bf' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/not_found/content.tpl',
      1 => 1371647781,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9470354051c1c0e0e779b5-17203816',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'controller' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e0e8aec7_96624449',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e0e8aec7_96624449')) {function content_51c1c0e0e8aec7_96624449($_smarty_tpl) {?>
<h1><?php echo smartyTranslate(array('s'=>'The controller %s is missing or invalid.','sprintf'=>$_smarty_tpl->tpl_vars['controller']->value),$_smarty_tpl);?>
</h1>
<ul>
<li><a href="index.php"><?php echo smartyTranslate(array('s'=>'Go to the dashboard.'),$_smarty_tpl);?>
</a></li>
<li><a href="#" onclick="window.history.back();"><?php echo smartyTranslate(array('s'=>'Back to the previous page.'),$_smarty_tpl);?>
</a></li>
</ul>
<?php }} ?>