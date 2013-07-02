<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:04
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/stats/engines.tpl" */ ?>
<?php /*%%SmartyHeaderCode:96706500651c1c0e4d13398-99448761%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0743d46cf9f7861b20cd90dcef7e6d99386c49d9' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/stats/engines.tpl',
      1 => 1371647801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '96706500651c1c0e4d13398-99448761',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'current' => 0,
    'token' => 0,
    'array_graph_engines' => 0,
    'k' => 0,
    'graph_engine' => 0,
    'value' => 0,
    'array_grid_engines' => 0,
    'grid_engine' => 0,
    'array_auto_clean' => 0,
    'auto_clean' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e4dcba69_88807010',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e4dcba69_88807010')) {function content_51c1c0e4dcba69_88807010($_smarty_tpl) {?>

<br/>
<div class="blocEngine">
	<form action="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
" method="post" id="settings_form" name="settings_form">
		<h3 class="icon-<?php echo smartyTranslate(array('s'=>'Settings'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Settings'),$_smarty_tpl);?>
</h3>

		<div class="rowForm">
			<label for="engine_stats_render"><?php echo smartyTranslate(array('s'=>'Graph engine'),$_smarty_tpl);?>
 </label>
			<?php if (count($_smarty_tpl->tpl_vars['array_graph_engines']->value)){?>
				<select name="PS_STATS_RENDER" id="engine_stats_render">
					<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['array_graph_engines']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['graph_engine']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value[0];?>
</option>
					<?php } ?>
				</select>

			<?php }else{ ?>
				<?php echo smartyTranslate(array('s'=>'No graph engine module has been installed.'),$_smarty_tpl);?>

			<?php }?>
		</div>

		<div class="rowForm">
			<label for="engine_grid_render"><?php echo smartyTranslate(array('s'=>'Grid engine'),$_smarty_tpl);?>
 </label>
			<?php if (count($_smarty_tpl->tpl_vars['array_grid_engines']->value)){?>
				<select name="PS_STATS_GRID_RENDER" id="engine_grid_render">
					<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['array_grid_engines']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['grid_engine']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value[0];?>
</option>
					<?php } ?>
				</select>
			<?php }else{ ?>
				<?php echo smartyTranslate(array('s'=>'No grid engine module has been installed.'),$_smarty_tpl);?>

			<?php }?>
		</div>

		<div class="rowForm">
			<label for="engine_auto_clean"><?php echo smartyTranslate(array('s'=>'Auto-clean period'),$_smarty_tpl);?>
</label>
			<select name="PS_STATS_OLD_CONNECT_AUTO_CLEAN" id="engine_auto_clean">
				<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['array_auto_clean']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['auto_clean']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</option>
				<?php } ?>
			</select>
		</div>
		<p><input type="submit" value="<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
" name="submitSettings" id="submitSettings" class="button" /></p>
</form>
</div>

</div><?php }} ?>