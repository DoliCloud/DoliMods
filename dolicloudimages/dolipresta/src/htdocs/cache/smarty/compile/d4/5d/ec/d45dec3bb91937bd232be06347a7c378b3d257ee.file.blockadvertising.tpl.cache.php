<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 17:29:33
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/modules/blockadvertising/blockadvertising.tpl" */ ?>
<?php /*%%SmartyHeaderCode:34034928251c1ce5d7f4865-27841793%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd45dec3bb91937bd232be06347a7c378b3d257ee' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/modules/blockadvertising/blockadvertising.tpl',
      1 => 1371646698,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '34034928251c1ce5d7f4865-27841793',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'adv_link' => 0,
    'adv_title' => 0,
    'image' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5d833fb2_50281884',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5d833fb2_50281884')) {function content_51c1ce5d833fb2_50281884($_smarty_tpl) {?>

<!-- MODULE Block advertising -->
<div class="advertising_block">
	<a href="<?php echo $_smarty_tpl->tpl_vars['adv_link']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['adv_title']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['image']->value;?>
" alt="<?php echo $_smarty_tpl->tpl_vars['adv_title']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['adv_title']->value;?>
" width="155"  height="163" /></a>
</div>
<!-- /MODULE Block advertising -->
<?php }} ?>