<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:00
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules/favorites.tpl" */ ?>
<?php /*%%SmartyHeaderCode:87048130851c1c0e0067291-99632758%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '58f8181a20f6c925347a52b4a232832bcc062974' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules/favorites.tpl',
      1 => 1371647778,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '87048130851c1c0e0067291-99632758',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'modules' => 0,
    'km' => 0,
    'module' => 0,
    'tabs' => 0,
    't' => 0,
    'module_name' => 0,
    'tab_modules_preferences' => 0,
    't2' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e0254a25_81516524',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e0254a25_81516524')) {function content_51c1c0e0254a25_81516524($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><div id="productBox">
	<?php echo $_smarty_tpl->getSubTemplate ('controllers/modules/header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<ul class="view-modules">
		<li class="button normal-view"><a href="index.php?controller=<?php echo htmlentities($_GET['controller']);?>
&token=<?php echo htmlentities($_GET['token']);?>
"><img src="themes/default/img/modules_view_layout_sidebar.png" alt="<?php echo smartyTranslate(array('s'=>'Normal view'),$_smarty_tpl);?>
" border="0" /><span><?php echo smartyTranslate(array('s'=>'Normal view'),$_smarty_tpl);?>
</span></a></li>
		<li class="button favorites-view-disabled"><img src="themes/default/img/modules_view_table_select_row.png" alt="<?php echo smartyTranslate(array('s'=>'Favorites view'),$_smarty_tpl);?>
" border="0" /><span><?php echo smartyTranslate(array('s'=>'Favorites view'),$_smarty_tpl);?>
</span></li>
	</ul>

	<div id="container">

		<div id="moduleContainer" style="padding:0px;margin:0px;padding-top:15px">

			<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table" id="">
				<col width="30px">
				<col width="240px">
				<col width="">
				<col width="140px">
				<col width="250px">
				<col width="180px">
				<col width="70px">
				<col width="70px">
				<col width="130px">
				</colgroup>
				<thead>
					<tr class="nodrag nodrop">
						<th class="center"><?php echo smartyTranslate(array('s'=>'Logo'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Module Name'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Description'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Tab'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Categories'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Interest'),$_smarty_tpl);?>
</th>
						<th><?php echo smartyTranslate(array('s'=>'Favorite'),$_smarty_tpl);?>
</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_smarty_tpl->tpl_vars['km'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['km']->value = $_smarty_tpl->tpl_vars['module']->key;
?>
					<tr height="32" <?php if ($_smarty_tpl->tpl_vars['km']->value%2==0){?> class="alt_row"<?php }?>>
						<td><img src="<?php if (isset($_smarty_tpl->tpl_vars['module']->value->image)){?><?php echo $_smarty_tpl->tpl_vars['module']->value->image;?>
<?php }else{ ?>../modules/<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
/<?php echo $_smarty_tpl->tpl_vars['module']->value->logo;?>
<?php }?>" width="16" height="16" /></td>
						<td><span class="moduleName"><?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>
</span></td>
						<td><span class="moduleFavDesc"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['module']->value->description,80,'...');?>
</span></td>
						<td><?php if (isset($_smarty_tpl->tpl_vars['module']->value->id)&&$_smarty_tpl->tpl_vars['module']->value->id>0){?><span class="setup"><?php echo smartyTranslate(array('s'=>'Installed'),$_smarty_tpl);?>
</span><?php }else{ ?><span class="setup non-install"><?php echo smartyTranslate(array('s'=>'Not Installed'),$_smarty_tpl);?>
</span><?php }?></td>
						<td>
							<?php $_smarty_tpl->tpl_vars["module_name"] = new Smarty_variable($_smarty_tpl->tpl_vars['module']->value->name, null, 0);?>
							<select class="chosen moduleTabPreferencesChoise" name="t_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" multiple="multiple">
								<?php  $_smarty_tpl->tpl_vars['t'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['t']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['t']->key => $_smarty_tpl->tpl_vars['t']->value){
$_smarty_tpl->tpl_vars['t']->_loop = true;
?>
									<?php if ($_smarty_tpl->tpl_vars['t']->value['active']){?>
										<option <?php if (isset($_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])&&in_array($_smarty_tpl->tpl_vars['t']->value['id_tab'],$_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])){?> selected="selected" <?php }?> class="group" value="<?php echo $_smarty_tpl->tpl_vars['t']->value['id_tab'];?>
"><?php if ($_smarty_tpl->tpl_vars['t']->value['name']==''){?><?php echo $_smarty_tpl->tpl_vars['t']->value['class_name'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['t']->value['name'];?>
<?php }?></option>
										<?php  $_smarty_tpl->tpl_vars['t2'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['t2']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['t']->value['sub_tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['t2']->key => $_smarty_tpl->tpl_vars['t2']->value){
$_smarty_tpl->tpl_vars['t2']->_loop = true;
?>
											<?php if ($_smarty_tpl->tpl_vars['t2']->value['active']){?>
												<?php $_smarty_tpl->tpl_vars["id_tab"] = new Smarty_variable($_smarty_tpl->tpl_vars['t']->value['id_tab'], null, 0);?>
												<option <?php if (isset($_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])&&in_array($_smarty_tpl->tpl_vars['t2']->value['id_tab'],$_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])){?> selected="selected" <?php }?> value="<?php echo $_smarty_tpl->tpl_vars['t2']->value['id_tab'];?>
">&nbsp;&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['t2']->value['name']==''){?><?php echo $_smarty_tpl->tpl_vars['t2']->value['class_name'];?>
<?php }else{ ?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['t2']->value['name'], 'htmlall', 'UTF-8');?>
<?php }?></option>
											<?php }?>
										<?php } ?>
									<?php }?>
								<?php } ?>
							</select>
						</td>
						<td><?php echo $_smarty_tpl->tpl_vars['module']->value->categoryName;?>
</td>
						<td>
						<select name="i_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" class="moduleFavorite" style="width:50px">
							<option value="" selected="selected">---</option>
							<option value="1" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['interest'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['interest']=='1'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</option>
							<option value="0" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['interest'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['interest']=='0'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</option>
						</select>
						</td>
						<td>
						<select name="f_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" class="moduleFavorite" style="width:50px">
							<option value="" selected="selected">---</option>
							<option value="1" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['favorite'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['favorite']=='1'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</option>
							<option value="0" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['favorite'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['favorite']=='0'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</option>
						</select>
						</td>
						<td id="r_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
">&nbsp;</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

		</div>
	</div>
</div><?php }} ?>