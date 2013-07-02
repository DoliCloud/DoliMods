<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:00
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules_positions/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:22097013951c1c0e0a50df0-91150290%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'feff0620d677e060180cf90ef7652cc0f472fd26' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules_positions/form.tpl',
      1 => 1371647780,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22097013951c1c0e0a50df0-91150290',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'url_submit' => 0,
    'table' => 0,
    'display_key' => 0,
    'edit_graft' => 0,
    'modules' => 0,
    'module' => 0,
    'id_module' => 0,
    'show_modules' => 0,
    'hooks' => 0,
    'hook' => 0,
    'id_hook' => 0,
    'except_diff' => 0,
    'exception_list' => 0,
    'exception_list_diff' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e0b69f87_12693220',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e0b69f87_12693220')) {function content_51c1c0e0b69f87_12693220($_smarty_tpl) {?>

<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<div class="leadin"></div>

<form action="<?php echo $_smarty_tpl->tpl_vars['url_submit']->value;?>
" id=<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form method="post">
	<?php if ($_smarty_tpl->tpl_vars['display_key']->value){?>
		<input type="hidden" name="show_modules" value="<?php echo $_smarty_tpl->tpl_vars['display_key']->value;?>
" />
	<?php }?>
	<fieldset>
		<legend><img src="../img/t/AdminModulesPositions.gif" /><?php echo smartyTranslate(array('s'=>'Transplant a module'),$_smarty_tpl);?>
</legend>
		<label><?php echo smartyTranslate(array('s'=>'Module'),$_smarty_tpl);?>
 :</label>
		<div class="margin-form">
			<select name="id_module" <?php if ($_smarty_tpl->tpl_vars['edit_graft']->value){?> disabled="disabled"<?php }?>>
				<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['module']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['id_module']->value==$_smarty_tpl->tpl_vars['module']->value->id||(!$_smarty_tpl->tpl_vars['id_module']->value&&$_smarty_tpl->tpl_vars['show_modules']->value==$_smarty_tpl->tpl_vars['module']->value->id)){?>selected="selected"<?php }?>><?php echo stripslashes($_smarty_tpl->tpl_vars['module']->value->displayName);?>
</option>
				<?php } ?>
			</select><sup> *</sup>
		</div>
		<label><?php echo smartyTranslate(array('s'=>'Hook into'),$_smarty_tpl);?>
 :</label>
		<div class="margin-form">
			<select name="id_hook" <?php if ($_smarty_tpl->tpl_vars['edit_graft']->value){?> disabled="disabled"<?php }?>>
				<?php  $_smarty_tpl->tpl_vars['hook'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hook']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hooks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hook']->key => $_smarty_tpl->tpl_vars['hook']->value){
$_smarty_tpl->tpl_vars['hook']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
" <?php if ($_smarty_tpl->tpl_vars['id_hook']->value==$_smarty_tpl->tpl_vars['hook']->value['id_hook']){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['hook']->value['name'];?>
<?php if ($_smarty_tpl->tpl_vars['hook']->value['name']!=$_smarty_tpl->tpl_vars['hook']->value['title']){?> (<?php echo $_smarty_tpl->tpl_vars['hook']->value['title'];?>
)<?php }?></option>
				<?php } ?>
			</select><sup> *</sup>
		</div>
	
		<script type="text/javascript">
			//<![CDATA
			function position_exception_add(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (!r.test(inputValue))
					$('#em_text_'+shopID).val(inputValue + ((inputValue.trim()) ? ', ' : '') + listValue);
			}
		
			function position_exception_remove(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (r.test(inputValue))
				{
					var rep = '';
					if (new RegExp(', *'+listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ','));
					else if (new RegExp(listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
					else
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
				}
			}
			//]]>
		</script>
	
		<label><?php echo smartyTranslate(array('s'=>'Exceptions'),$_smarty_tpl);?>
 :</label>
		<div class="margin-form">
			<?php if (!$_smarty_tpl->tpl_vars['except_diff']->value){?>
				<?php echo $_smarty_tpl->tpl_vars['exception_list']->value;?>

			<?php }else{ ?>
				<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['exception_list_diff']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
					<?php echo $_smarty_tpl->tpl_vars['value']->value;?>

				<?php } ?>
			<?php }?>
			<?php echo smartyTranslate(array('s'=>'Please specify the files for which you do not want the module to be displayed.'),$_smarty_tpl);?>
.<br />
			<?php echo smartyTranslate(array('s'=>'Please input each filename, separated by a comma.'),$_smarty_tpl);?>
.
			<br /><br />
		</div>
	
		<div class="margin-form">
			<?php if ($_smarty_tpl->tpl_vars['edit_graft']->value){?>
				<input type="hidden" name="id_module" value="<?php echo $_smarty_tpl->tpl_vars['id_module']->value;?>
" />
				<input type="hidden" name="id_hook" value="<?php echo $_smarty_tpl->tpl_vars['id_hook']->value;?>
" />
			<?php }?>
			<input type="submit" value="<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
" name="<?php if ($_smarty_tpl->tpl_vars['edit_graft']->value){?>submitEditGraft<?php }else{ ?>submitAddToHook<?php }?>" id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form_submit_btn" class="button" />
		</div>
		<div class="small"><sup>*</sup> <?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</div>
	</fieldset>
</form><?php }} ?>