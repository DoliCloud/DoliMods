<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 17:29:32
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/modules/favoriteproducts/views/templates/hook/favoriteproducts-header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2182028451c1ce5cf18cf1-72450076%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76b9bf9636e46cd452958e4ab657d666ae73b073' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/modules/favoriteproducts/views/templates/hook/favoriteproducts-header.tpl',
      1 => 1371647736,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2182028451c1ce5cf18cf1-72450076',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5d095212_17544535',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5d095212_17544535')) {function content_51c1ce5d095212_17544535($_smarty_tpl) {?>
<script type="text/javascript">
	var favorite_products_url_add = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getModuleLink('favoriteproducts','actions',array('process'=>'add'),false);?>
';
	var favorite_products_url_remove = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getModuleLink('favoriteproducts','actions',array('process'=>'remove'),false);?>
';
<?php if (isset($_GET['id_product'])){?>
	var favorite_products_id_product = '<?php echo intval($_GET['id_product']);?>
';
<?php }?> 
</script>
<?php }} ?>