<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:58
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/attachments/list_action_delete.tpl" */ ?>
<?php /*%%SmartyHeaderCode:155940153751c1c0de1e57a7-38149585%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '66a0a02c12bf64ed8fedf53c680b29644770888e' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/attachments/list_action_delete.tpl',
      1 => 1371647762,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '155940153751c1c0de1e57a7-38149585',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'confirm' => 0,
    'href' => 0,
    'id' => 0,
    'product_attachements' => 0,
    'product_list' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0de21f194_95609001',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0de21f194_95609001')) {function content_51c1c0de21f194_95609001($_smarty_tpl) {?>
<script>
	function confirmProductAttached(productList)
	{
		var confirm_text = "<?php echo smartyTranslate(array('s'=>'This attachment is associated with the following products:'),$_smarty_tpl);?>
";
		if (confirm('<?php echo $_smarty_tpl->tpl_vars['confirm']->value;?>
'))
			return confirm(confirm_text + product_list);
		return false;
	}
</script>

<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" onclick="<?php if (isset($_smarty_tpl->tpl_vars['product_attachements']->value[$_smarty_tpl->tpl_vars['id']->value])){?>return confirmProductAttached('<?php echo $_smarty_tpl->tpl_vars['product_list']->value[$_smarty_tpl->tpl_vars['id']->value];?>
')<?php }else{ ?>return confirm('<?php echo $_smarty_tpl->tpl_vars['confirm']->value;?>
')<?php }?>">
	<img src="../img/admin/delete.gif" alt="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" />
</a>

<?php }} ?>