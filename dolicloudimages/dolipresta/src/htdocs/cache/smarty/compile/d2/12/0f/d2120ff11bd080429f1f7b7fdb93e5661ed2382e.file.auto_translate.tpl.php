<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:05
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/translations/auto_translate.tpl" */ ?>
<?php /*%%SmartyHeaderCode:194263796351c1c0e572ad58-18964588%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd2120ff11bd080429f1f7b7fdb93e5661ed2382e' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/translations/auto_translate.tpl',
      1 => 1371647810,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '194263796351c1c0e572ad58-18964588',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'language_code' => 0,
    'not_available' => 0,
    'tooltip_title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e5742698_30361460',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e5742698_30361460')) {function content_51c1c0e5742698_30361460($_smarty_tpl) {?>

<input type="button" class="button" id="translate_all" value="<?php echo smartyTranslate(array('s'=>'Translate with Google (experimental).'),$_smarty_tpl);?>
" />
<script type="text/javascript">
	var gg_translate = {
		language_code : '<?php echo $_smarty_tpl->tpl_vars['language_code']->value;?>
',
		not_available : '<?php echo $_smarty_tpl->tpl_vars['not_available']->value;?>
',
		tooltip_title : '<?php echo $_smarty_tpl->tpl_vars['tooltip_title']->value;?>
'
	};
</script><?php }} ?>