<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:04
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/stats/stats.tpl" */ ?>
<?php /*%%SmartyHeaderCode:101612689651c1c0e4ee4455-21454891%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a98ded40fc8a1b47e3169731fa0c97604471465f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/stats/stats.tpl',
      1 => 1371647801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '101612689651c1c0e4ee4455-21454891',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'module_name' => 0,
    'module_instance' => 0,
    'hook' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e4f123c0_89979708',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e4f123c0_89979708')) {function content_51c1c0e4f123c0_89979708($_smarty_tpl) {?>

<div>
	<?php if ($_smarty_tpl->tpl_vars['module_name']->value){?>
		<?php if ($_smarty_tpl->tpl_vars['module_instance']->value&&$_smarty_tpl->tpl_vars['module_instance']->value->active){?>
			<?php echo $_smarty_tpl->tpl_vars['hook']->value;?>

		<?php }else{ ?>
			<?php echo smartyTranslate(array('s'=>'Module not found'),$_smarty_tpl);?>

		<?php }?>
	<?php }else{ ?>
		<h3 class="space"><?php echo smartyTranslate(array('s'=>'Please select a module from the left column.'),$_smarty_tpl);?>
</h3>
	<?php }?>
</div>
</div>
</div>


<?php }} ?>