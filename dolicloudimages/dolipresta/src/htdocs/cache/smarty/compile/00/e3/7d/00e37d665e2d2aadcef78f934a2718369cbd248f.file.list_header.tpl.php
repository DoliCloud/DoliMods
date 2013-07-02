<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:05
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/tax_rules/helpers/list/list_header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15653611451c1c0e5508405-05237845%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '00e37d665e2d2aadcef78f934a2718369cbd248f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/tax_rules/helpers/list/list_header.tpl',
      1 => 1371647969,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15653611451c1c0e5508405-05237845',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'name_controller' => 0,
    'hookName' => 0,
    'currentIndex' => 0,
    'identifier' => 0,
    'token' => 0,
    'id_tax_rules_group' => 0,
    'table' => 0,
    'page' => 0,
    'total_pages' => 0,
    'pagination' => 0,
    'value' => 0,
    'selected_pagination' => 0,
    'list_total' => 0,
    'table_id' => 0,
    'table_dnd' => 0,
    'fields_display' => 0,
    'params' => 0,
    'shop_link_type' => 0,
    'has_actions' => 0,
    'has_bulk_actions' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e56d5426_03991160',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e56d5426_03991160')) {function content_51c1c0e56d5426_03991160($_smarty_tpl) {?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminListBefore'),$_smarty_tpl);?>

<?php if (isset($_smarty_tpl->tpl_vars['name_controller']->value)){?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo ucfirst($_smarty_tpl->tpl_vars['name_controller']->value);?>
ListBefore<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }elseif(isset($_GET['controller'])){?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo htmlentities(ucfirst($_GET['controller']));?>
ListBefore<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }?>

<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['currentIndex']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['identifier']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&id_tax_rules_group=<?php echo $_smarty_tpl->tpl_vars['id_tax_rules_group']->value;?>
&updatetax_rules_group#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" class="form">
	<input type="hidden" id="submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" name="submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="0"/>
	<table class="table_grid">
		<tr>
			<td style="vertical-align: bottom;">
				<span style="float: left;">
					<?php if ($_smarty_tpl->tpl_vars['page']->value>1){?>
						<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=1"/>&nbsp;
						<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=<?php echo $_smarty_tpl->tpl_vars['page']->value-1;?>
"/>
					<?php }?>
					<?php echo smartyTranslate(array('s'=>'Page'),$_smarty_tpl);?>
 <b><?php echo $_smarty_tpl->tpl_vars['page']->value;?>
</b> / <?php echo $_smarty_tpl->tpl_vars['total_pages']->value;?>

					<?php if ($_smarty_tpl->tpl_vars['page']->value<$_smarty_tpl->tpl_vars['total_pages']->value){?>
						<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=<?php echo $_smarty_tpl->tpl_vars['page']->value+1;?>
;"/>&nbsp;
						<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=<?php echo $_smarty_tpl->tpl_vars['total_pages']->value;?>
"/>
					<?php }?>
					| <?php echo smartyTranslate(array('s'=>'Display'),$_smarty_tpl);?>

					<select name="pagination" onchange="submit()">
						
						<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pagination']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
							<option value="<?php echo intval($_smarty_tpl->tpl_vars['value']->value);?>
"<?php if ($_smarty_tpl->tpl_vars['selected_pagination']->value==$_smarty_tpl->tpl_vars['value']->value){?> selected="selected" <?php }elseif($_smarty_tpl->tpl_vars['selected_pagination']->value==null&&$_smarty_tpl->tpl_vars['value']->value==$_smarty_tpl->tpl_vars['pagination']->value[1]){?> selected="selected2"<?php }?>><?php echo intval($_smarty_tpl->tpl_vars['value']->value);?>
</option>
						<?php } ?>
					</select>
					/ <?php echo $_smarty_tpl->tpl_vars['list_total']->value;?>
 <?php echo smartyTranslate(array('s'=>'result(s)'),$_smarty_tpl);?>

				</span>
				<span style="float: right;">
					<input type="submit" name="submitReset<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="<?php echo smartyTranslate(array('s'=>'Reset'),$_smarty_tpl);?>
" class="button" />
				</span>
				<span class="clear"></span>
			</td>
		</tr>
		<tr>
			<td>
				<table
				<?php if ($_smarty_tpl->tpl_vars['table_id']->value){?> id=<?php echo $_smarty_tpl->tpl_vars['table_id']->value;?>
<?php }?>
				class="table <?php if ($_smarty_tpl->tpl_vars['table_dnd']->value){?>tableDnD<?php }?> <?php echo $_smarty_tpl->tpl_vars['table']->value;?>
"
				cellpadding="0" cellspacing="0"
				style="width: 100%; margin-bottom:10px;"
				>
					<col width="10px" />
					<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value){
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
						<col <?php if (isset($_smarty_tpl->tpl_vars['params']->value['width'])&&$_smarty_tpl->tpl_vars['params']->value['width']!='auto'){?>width="<?php echo $_smarty_tpl->tpl_vars['params']->value['width'];?>
px"<?php }?>/>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value){?>
						<col width="80px" />
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['has_actions']->value){?>
						<col width="52px" />
					<?php }?>
					<thead>
						<tr class="nodrag nodrop">
							<th class="center">
								<?php if ($_smarty_tpl->tpl_vars['has_bulk_actions']->value){?>
									<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Box[]', this.checked)" />
								<?php }?>
							</th>
							<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value){
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
								<th <?php if (isset($_smarty_tpl->tpl_vars['params']->value['align'])){?> class="<?php echo $_smarty_tpl->tpl_vars['params']->value['align'];?>
"<?php }?>>
									<?php if (isset($_smarty_tpl->tpl_vars['params']->value['hint'])){?><span class="hint" name="help_box"><?php echo $_smarty_tpl->tpl_vars['params']->value['hint'];?>
<span class="hint-pointer">&nbsp;</span></span><?php }?>
									<span class="title_box">
										<?php echo $_smarty_tpl->tpl_vars['params']->value['title'];?>

									</span>
										<br />&nbsp;
								</th>
							<?php } ?>
							<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value){?>
								<th>
									<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value=='shop'){?>
										<?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>

									<?php }else{ ?>
										<?php echo smartyTranslate(array('s'=>'Group shop'),$_smarty_tpl);?>

									<?php }?>
									<br />&nbsp;
								</th>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['has_actions']->value){?>
								<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
<br />&nbsp;</th>
							<?php }?>
						</tr>
						</thead>
<?php }} ?>