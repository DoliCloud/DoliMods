<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:58
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/category-count.tpl" */ ?>
<?php /*%%SmartyHeaderCode:152422548051c1c0de79aa01-28171109%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '127db7335e2e170de7e9f9846df3f752fe2e8bbe' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/category-count.tpl',
      1 => 1371646825,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '152422548051c1c0de79aa01-28171109',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
    'nb_products' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0de7cd8c5_83183842',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0de7cd8c5_83183842')) {function content_51c1c0de7cd8c5_83183842($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['category']->value->id==1||$_smarty_tpl->tpl_vars['nb_products']->value==0){?>
	<?php echo smartyTranslate(array('s'=>'There are no products in  this category'),$_smarty_tpl);?>

<?php }else{ ?>
	<?php if ($_smarty_tpl->tpl_vars['nb_products']->value==1){?>
		<?php echo smartyTranslate(array('s'=>'There is %d product.','sprintf'=>$_smarty_tpl->tpl_vars['nb_products']->value),$_smarty_tpl);?>

	<?php }else{ ?>
		<?php echo smartyTranslate(array('s'=>'There are %d products.','sprintf'=>$_smarty_tpl->tpl_vars['nb_products']->value),$_smarty_tpl);?>

	<?php }?>
<?php }?><?php }} ?>