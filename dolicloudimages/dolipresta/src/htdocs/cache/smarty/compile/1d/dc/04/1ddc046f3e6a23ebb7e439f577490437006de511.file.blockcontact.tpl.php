<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:14
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockcontact/blockcontact.tpl" */ ?>
<?php /*%%SmartyHeaderCode:158958360751c1c0ee95f201-10262708%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1ddc046f3e6a23ebb7e439f577490437006de511' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockcontact/blockcontact.tpl',
      1 => 1371647521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '158958360751c1c0ee95f201-10262708',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'telnumber' => 0,
    'email' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ee99e444_33103979',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ee99e444_33103979')) {function content_51c1c0ee99e444_33103979($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<div id="contact_block" class="block">
	<p class="title_block"><?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcontact'),$_smarty_tpl);?>
</p>
	<div class="block_content clearfix">
			<p><?php echo smartyTranslate(array('s'=>'Our hotline is available 24/7','mod'=>'blockcontact'),$_smarty_tpl);?>
</p>
			<?php if ($_smarty_tpl->tpl_vars['telnumber']->value!=''){?><p class="tel"><span class="label"><?php echo smartyTranslate(array('s'=>'Phone:','mod'=>'blockcontact'),$_smarty_tpl);?>
</span><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['telnumber']->value, 'htmlall', 'UTF-8');?>
</p><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['email']->value!=''){?><a href="mailto:<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['email']->value, 'htmlall', 'UTF-8');?>
"><?php echo smartyTranslate(array('s'=>'Contact our hotline','mod'=>'blockcontact'),$_smarty_tpl);?>
</a><?php }?>
	</div>
</div>
<?php }} ?>