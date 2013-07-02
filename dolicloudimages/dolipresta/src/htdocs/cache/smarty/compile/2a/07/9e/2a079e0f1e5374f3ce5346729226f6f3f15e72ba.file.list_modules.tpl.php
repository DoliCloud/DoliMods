<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:00
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules_positions/list_modules.tpl" */ ?>
<?php /*%%SmartyHeaderCode:134088794651c1c0e0b727f6-14731096%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a079e0f1e5374f3ce5346729226f6f3f15e72ba' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules_positions/list_modules.tpl',
      1 => 1371647780,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '134088794651c1c0e0b727f6-14731096',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'token' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'url_show_modules' => 0,
    'modules' => 0,
    'module' => 0,
    'display_key' => 0,
    'url_show_invisible' => 0,
    'hook_position' => 0,
    'live_edit' => 0,
    'url_live_edit' => 0,
    'url_submit' => 0,
    'can_move' => 0,
    'hooks' => 0,
    'hook' => 0,
    'current' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e0e6ccb8_60437115',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e0e6ccb8_60437115')) {function content_51c1c0e0e6ccb8_60437115($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/function.cycle.php';
?>

<script type="text/javascript">
	var token = '<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
';
	var come_from = 'AdminModulesPositions';
</script>
<script type="text/javascript" src="../js/admin-dnd.js"></script>

<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<div class="leadin"></div>

<div class="filter-module">
<form>
	<?php echo smartyTranslate(array('s'=>'Show'),$_smarty_tpl);?>
 :
	<select id="show_modules" onChange="autoUrl('show_modules', '<?php echo $_smarty_tpl->tpl_vars['url_show_modules']->value;?>
')">
		<option value="all"><?php echo smartyTranslate(array('s'=>'All modules'),$_smarty_tpl);?>
&nbsp;</option>
		<option>---------------</option>

		<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['module']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['module']->iteration++;
?>
			<option value="<?php echo intval($_smarty_tpl->tpl_vars['module']->value->id);?>
" <?php if ($_smarty_tpl->tpl_vars['display_key']->value==$_smarty_tpl->tpl_vars['module']->value->id){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>
</option>
		<?php } ?>
	</select>
	<br /><br />
	<input type="checkbox" id="hook_position" onclick="autoUrlNoList('hook_position', '<?php echo $_smarty_tpl->tpl_vars['url_show_invisible']->value;?>
')" <?php if ($_smarty_tpl->tpl_vars['hook_position']->value){?>checked="checked"<?php }?> />&nbsp;
	<label class="t" for="hook_position"><?php echo smartyTranslate(array('s'=>'Display non-positionable hooks'),$_smarty_tpl);?>
</label>
</form>
</div>
<br/>
<div>

<div id="modulePosition">
<div class="blocLiveEdit"><h2><?php echo smartyTranslate(array('s'=>'LiveEdit'),$_smarty_tpl);?>
</h2>
<?php if ($_smarty_tpl->tpl_vars['live_edit']->value){?>
	<p><?php echo smartyTranslate(array('s'=>'You have to select a shop to use LiveEdit'),$_smarty_tpl);?>
</p>
<?php }else{ ?>
	<p><?php echo smartyTranslate(array('s'=>'Click here to be redirected to the Front Office of your shop where you can move and delete modules directly.'),$_smarty_tpl);?>
</p>
		<a href="<?php echo $_smarty_tpl->tpl_vars['url_live_edit']->value;?>
" target="_blank" class="button"><?php echo smartyTranslate(array('s'=>'Run LiveEdit'),$_smarty_tpl);?>
</a>
<?php }?>
</div>
<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['url_submit']->value;?>
">
<div id="unhook_button_position_top">
	<input class="button floatr" type="submit" name="unhookform" value="<?php echo smartyTranslate(array('s'=>'Unhook the selection'),$_smarty_tpl);?>
"/></div>

<?php if (!$_smarty_tpl->tpl_vars['can_move']->value){?>
	<br /><div><b><?php echo smartyTranslate(array('s'=>'If you want to order/move the following data, please select a shop from the shop list.'),$_smarty_tpl);?>
</b></div>
<?php }?>
<?php  $_smarty_tpl->tpl_vars['hook'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hook']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hooks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hook']->key => $_smarty_tpl->tpl_vars['hook']->value){
$_smarty_tpl->tpl_vars['hook']->_loop = true;
?>
	<a name="<?php echo $_smarty_tpl->tpl_vars['hook']->value['name'];?>
"/>
	<table cellpadding="0" cellspacing="0" class="table widthfull space <?php if ($_smarty_tpl->tpl_vars['hook']->value['module_count']>=2){?> tableDnD<?php }?>" id="<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
">
		<colgroup>
			<col width="10">
			<col width="30">
			<col width="40">
			<col width="">
			<col width="50">
		</colgroup>
	<tr class="nodrag nodrop"><th colspan="5">	<?php if ($_smarty_tpl->tpl_vars['hook']->value['module_count']&&$_smarty_tpl->tpl_vars['can_move']->value){?>
		<input type="checkbox" id="Ghook<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
" style="margin-right: 2px;" onclick="hookCheckboxes(<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
, 0, this)"/>
	<?php }?><?php echo $_smarty_tpl->tpl_vars['hook']->value['title'];?>
 - <span style="color: red"><?php echo $_smarty_tpl->tpl_vars['hook']->value['module_count'];?>
</span> <?php if ($_smarty_tpl->tpl_vars['hook']->value['module_count']>1){?><?php echo smartyTranslate(array('s'=>'Modules'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Module'),$_smarty_tpl);?>
<?php }?>

	<?php if (!empty($_smarty_tpl->tpl_vars['hook']->value['description'])){?>
		&nbsp;<span style="font-size:0.8em; font-weight: normal">[<?php echo $_smarty_tpl->tpl_vars['hook']->value['description'];?>
]</span>
	<?php }?>
	<span style="color:grey;">(<?php echo smartyTranslate(array('s'=>'Technical name: '),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['hook']->value['name'];?>
)</span></th></tr>
	<?php if ($_smarty_tpl->tpl_vars['hook']->value['module_count']){?>
		<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_smarty_tpl->tpl_vars['position'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['hook']->value['modules']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['module']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['position']->value = $_smarty_tpl->tpl_vars['module']->key;
 $_smarty_tpl->tpl_vars['module']->iteration++;
?>
			<?php if (isset($_smarty_tpl->tpl_vars['module']->value['instance'])){?>
			<tr id="<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
" <?php echo smarty_function_cycle(array('values'=>'class="alt_row",'),$_smarty_tpl);?>
 style="height: 42px;">
			<td align=center ><input type="checkbox" id="mod<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
" class="hook<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
" onclick="hookCheckboxes(<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
, 1, this)" name="unhooks[]" value="<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
"/></td>
			<?php if (!$_smarty_tpl->tpl_vars['display_key']->value){?>
				<td align=center  class="positions"><?php echo $_smarty_tpl->tpl_vars['module']->iteration;?>
</td>
				<td <?php if ($_smarty_tpl->tpl_vars['can_move']->value&&$_smarty_tpl->tpl_vars['hook']->value['module_count']>=2){?> align=center class="dragHandle"<?php }?> id="td_<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
">
					<?php if ($_smarty_tpl->tpl_vars['can_move']->value){?>
						<a <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['module']->iteration;?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1==1){?> style="display: none;"<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&id_module=<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
&id_hook=<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
&direction=0&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&changePosition#<?php echo $_smarty_tpl->tpl_vars['hook']->value['name'];?>
">
							<img src="../img/admin/up.gif" alt="<?php echo smartyTranslate(array('s'=>'Up'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Up'),$_smarty_tpl);?>
" />
						</a><br />
						<a <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['module']->iteration;?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2==count($_smarty_tpl->tpl_vars['hook']->value['modules'])){?> style="display: none;"<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&id_module=<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
&id_hook=<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
&direction=1&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&changePosition#<?php echo $_smarty_tpl->tpl_vars['hook']->value['name'];?>
">
							<img src="../img/admin/down.gif" alt="<?php echo smartyTranslate(array('s'=>'Down'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Down'),$_smarty_tpl);?>
" />
						</a>
					<?php }?>
				</td>
				<td><div class="lab_modules_positions" for="mod<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
">
			<?php }else{ ?>
				<td colspan="3"><div class="lab_modules_positions" for="mod<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
">
			<?php }?>
			<img src="../modules/<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->name;?>
/logo.png" alt="<?php echo stripslashes($_smarty_tpl->tpl_vars['module']->value['instance']->name);?>
" /> <h3><?php echo stripslashes($_smarty_tpl->tpl_vars['module']->value['instance']->displayName);?>
</h3>
				<span><?php if ($_smarty_tpl->tpl_vars['module']->value['instance']->version){?>v<?php if (intval($_smarty_tpl->tpl_vars['module']->value['instance']->version)==$_smarty_tpl->tpl_vars['module']->value['instance']->version){?><?php echo sprintf('%.1f',$_smarty_tpl->tpl_vars['module']->value['instance']->version);?>
<?php }else{ ?><?php echo floatval($_smarty_tpl->tpl_vars['module']->value['instance']->version);?>
<?php }?><?php }?></span><p><?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->description;?>
</p>
			</div></td>
				<td>
					<a href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&id_module=<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
&id_hook=<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
&editGraft<?php if ($_smarty_tpl->tpl_vars['display_key']->value){?>&show_modules=<?php echo $_smarty_tpl->tpl_vars['display_key']->value;?>
<?php }?>&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
						<img src="../img/admin/edit.gif" border="0" alt="<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
" />
					</a>
					<a href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&id_module=<?php echo $_smarty_tpl->tpl_vars['module']->value['instance']->id;?>
&id_hook=<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
&deleteGraft<?php if ($_smarty_tpl->tpl_vars['display_key']->value){?>&show_modules=<?php echo $_smarty_tpl->tpl_vars['display_key']->value;?>
<?php }?>&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
						<img src="../img/admin/delete.gif" border="0" alt="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" />
					</a>
				</td>
			</tr>
			<?php }?>
		<?php } ?>
	<?php }else{ ?>
		<tr><td colspan="5"><?php echo smartyTranslate(array('s'=>'No module was found for this hook.'),$_smarty_tpl);?>
</td></tr>
	<?php }?>
	</table>
<?php } ?>
<div id="unhook_button_position_bottom"><input class="button floatr" type="submit" name="unhookform" value="<?php echo smartyTranslate(array('s'=>'Unhook the selection'),$_smarty_tpl);?>
"/></div>

</div>
</div>
</form><?php }} ?>