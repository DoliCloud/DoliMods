<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:04
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/slip/_print_pdf_icon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:212041601651c1c0e4bec219-77511550%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2283459a4f29913f3c005375c3bff9f6f3a4e428' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/slip/_print_pdf_icon.tpl',
      1 => 1371647799,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '212041601651c1c0e4bec219-77511550',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'order_slip' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e4c04910_87011899',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e4c04910_87011899')) {function content_51c1c0e4c04910_87011899($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>


<span style="width:20px; margin-right:5px;">
<a target="_blank" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), 'htmlall', 'UTF-8');?>
&submitAction=generateOrderSlipPDF&id_order_slip=<?php echo $_smarty_tpl->tpl_vars['order_slip']->value->id;?>
"><img src="../img/admin/tab-invoice.gif" alt="order_slip" /></a>
</span>
<?php }} ?>