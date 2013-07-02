<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:04
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/multishop/check_fields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:200033436051c1c0e449cb43-32996478%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '15501ab7c7c0dfc611d7f6f3df4c3d2a40cbf6a5' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/multishop/check_fields.tpl',
      1 => 1371647890,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '200033436051c1c0e449cb43-32996478',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'display_multishop_checkboxes' => 0,
    'product_tab' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e44bc5f5_04509268',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e44bc5f5_04509268')) {function content_51c1c0e44bc5f5_04509268($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value)&&$_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value){?>
	<label style="float: none">
		<input type="checkbox" style="vertical-align: text-bottom" onclick="$('#product-tab-content-<?php echo $_smarty_tpl->tpl_vars['product_tab']->value;?>
 input[name^=\'multishop_check[\']').attr('checked', this.checked); ProductMultishop.checkAll<?php echo $_smarty_tpl->tpl_vars['product_tab']->value;?>
()" />
		<?php echo smartyTranslate(array('s'=>'Check/uncheck all. (If you are editing this page for several shops, some fields like "name" or "price" are may be disabled. You will need check these fields in order to edit them)'),$_smarty_tpl);?>

	</label>
<?php }?><?php }} ?>