<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:06
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/help_access/button.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10603927851c1c0e691e185-24109683%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '61936a78f8ada513d050ec032e6dd50f42a55604' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/help_access/button.tpl',
      1 => 1371647813,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10603927851c1c0e691e185-24109683',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'label' => 0,
    'help_base_url' => 0,
    'iso_lang' => 0,
    'version' => 0,
    'doc_version' => 0,
    'country' => 0,
    'tooltip' => 0,
    'button_class' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e69879f3_41300078',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e69879f3_41300078')) {function content_51c1c0e69879f3_41300078($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><li class="help-context-<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['label']->value, 'htmlall', 'UTF-8');?>
" style="display:none">
    <a id="desc-<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['label']->value, 'htmlall', 'UTF-8');?>
-help"
       class="toolbar_btn"
       href="#"
       onclick="showHelp('<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['help_base_url']->value, 'htmlall', 'UTF-8');?>
',
                         '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['label']->value, 'htmlall', 'UTF-8');?>
',
                         '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['iso_lang']->value, 'htmlall', 'UTF-8');?>
',
                         '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['version']->value, 'htmlall', 'UTF-8');?>
',
                         '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['doc_version']->value, 'htmlall', 'UTF-8');?>
',
                         '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['country']->value, 'htmlall', 'UTF-8');?>
');"
        title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['tooltip']->value, 'htmlall', 'UTF-8');?>
">
        <span class="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['button_class']->value, 'htmlall', 'UTF-8');?>
"></span>
        <div><?php echo smartyTranslate(array('s'=>'Help'),$_smarty_tpl);?>
</div>
    </a>
</li>
<?php }} ?>