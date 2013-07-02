<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cms_content/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:35516000951c1c0df41e560-93620298%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd5f36cff05fe325d0e4570e3ee8cb5250f13d908' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cms_content/content.tpl',
      1 => 1371647767,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '35516000951c1c0df41e560-93620298',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cms_breadcrumb' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0df439ae0_47935705',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0df439ae0_47935705')) {function content_51c1c0df439ae0_47935705($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['cms_breadcrumb']->value)){?>
	<div class="cat_bar">
		<span style="color: #3C8534;"><?php echo smartyTranslate(array('s'=>'Current category'),$_smarty_tpl);?>
 :</span>&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['cms_breadcrumb']->value;?>

	</div>
<?php }?>

<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<?php }} ?>