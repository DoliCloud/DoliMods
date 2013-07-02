<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/images/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:133030336651c1c0dfa8e5e8-31153508%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '64c74c7ba42c277aa3e795519191e3e1c9372a9a' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/images/content.tpl',
      1 => 1371647773,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '133030336651c1c0dfa8e5e8-31153508',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
    'display_regenerate' => 0,
    'current' => 0,
    'token' => 0,
    'types' => 0,
    'k' => 0,
    'type' => 0,
    'formats' => 0,
    'format' => 0,
    'table' => 0,
    'display_move' => 0,
    'safe_mode' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0dfb854d3_86081992',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0dfb854d3_86081992')) {function content_51c1c0dfb854d3_86081992($_smarty_tpl) {?>

<?php echo $_smarty_tpl->tpl_vars['content']->value;?>


<?php if (isset($_smarty_tpl->tpl_vars['display_regenerate']->value)){?>
	<h2 class="space"><?php echo smartyTranslate(array('s'=>'Regenerate thumbnails'),$_smarty_tpl);?>
</h2>
	<?php echo smartyTranslate(array('s'=>'Regenerates thumbnails for all existing images'),$_smarty_tpl);?>
<br /><br />
	<div  class="width4">
		<div class="warn">
			<?php echo smartyTranslate(array('s'=>'Please be patient. This can take several minutes.'),$_smarty_tpl);?>
<br />
			<?php echo smartyTranslate(array('s'=>'Be careful! Manually uploaded thumbnails will be erased and replaced by automatically generated thumbnails.'),$_smarty_tpl);?>

		</div>
	</div>
	<form action="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
" method="post">
		<fieldset class="width4">
			<legend><img src="../img/admin/picture.gif" /> <?php echo smartyTranslate(array('s'=>'Regenerate thumbnails'),$_smarty_tpl);?>
</legend><br />
			<label><?php echo smartyTranslate(array('s'=>'Select an image'),$_smarty_tpl);?>
</label>
			<div class="margin-form">
				<select name="type" onchange="changeFormat(this)">
					<option value="all"><?php echo smartyTranslate(array('s'=>'All'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value){
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['type']->key;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['type']->value;?>
</option>
					<?php } ?>
				</select>
			</div>

			<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value){
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['type']->key;
?>
				<label class="second-select format_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" style="display:none;"><?php echo smartyTranslate(array('s'=>'Select a format'),$_smarty_tpl);?>
</label>
				<div class="second-select margin-form format_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" style="display:none;">
					<select class="second-select format_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" name="format_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
">
						<option value="all"><?php echo smartyTranslate(array('s'=>'All'),$_smarty_tpl);?>
</option>
						<?php  $_smarty_tpl->tpl_vars['format'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['format']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['formats']->value[$_smarty_tpl->tpl_vars['k']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['format']->key => $_smarty_tpl->tpl_vars['format']->value){
$_smarty_tpl->tpl_vars['format']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['format']->value['id_image_type'];?>
"><?php echo $_smarty_tpl->tpl_vars['format']->value['name'];?>
</option>
						<?php } ?>
					</select>
				</div>
			<?php } ?>
			<script>
				function changeFormat(elt)
				{
					$('.second-select').hide();
					$('.format_' + $(elt).val()).show();
				}
			</script>
			<label><?php echo smartyTranslate(array('s'=>'Erase previous images'),$_smarty_tpl);?>
</label>
			<div class="margin-form">
				<input name="erase" type="checkbox" value="1" checked="checked" />
				<p><?php echo smartyTranslate(array('s'=>'Deselect this checkbox only if your server timed out and you need to resume the regeneration.'),$_smarty_tpl);?>
</p>
			</div>
			<div class="clear"></div>
			<center><input type="Submit" name="submitRegenerate<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="<?php echo smartyTranslate(array('s'=>'Regenerate thumbnails'),$_smarty_tpl);?>
" class="button space" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Are you sure?'),$_smarty_tpl);?>
');" /></center>
		</fieldset>
	</form>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['display_move']->value)&&$_smarty_tpl->tpl_vars['display_move']->value){?>
	<br /><h2 class="space"><?php echo smartyTranslate(array('s'=>'Move images'),$_smarty_tpl);?>
</h2>
	<?php echo smartyTranslate(array('s'=>'PrestaShop now uses a new storage system for product images. It offers better performance if your shop has a large number of products.'),$_smarty_tpl);?>
<br />
	<br />
	<?php if ($_smarty_tpl->tpl_vars['safe_mode']->value){?>
		<div class="warn">
			<?php echo smartyTranslate(array('s'=>'PrestaShop has detected that your server configuration is not compatible with the new storage system (directive "safe_mode" is activated). You should therefore continue to use the existing system.'),$_smarty_tpl);?>

		</div>
	<?php }else{ ?>
		<form action="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
" method="post">
			<fieldset class="width4">
				<legend><img src="../img/admin/picture.gif" /> <?php echo smartyTranslate(array('s'=>'Move images'),$_smarty_tpl);?>
</legend><br />
				<?php echo smartyTranslate(array('s'=>'You can choose to keep your images stored in the previous system. There\'s nothing wrong with that.'),$_smarty_tpl);?>
<br />
				<?php echo smartyTranslate(array('s'=>'You can also decide to move your images to the new storage system. In this case, click on the "Move images" button below. Please be patient. This can take several minutes.'),$_smarty_tpl);?>

				<br /><br />
				<div class="hint clear" style="display: block;">&nbsp;
					<?php echo smartyTranslate(array('s'=>'After moving all of your product images, set the "Use the legacy image filesystem" option above to "No" for best performance.'),$_smarty_tpl);?>

				</div>
				<center><input type="Submit" name="submitMoveImages<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="<?php echo smartyTranslate(array('s'=>'Move images'),$_smarty_tpl);?>
" class="button space" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Are you sure?'),$_smarty_tpl);?>
');" /></center>
			</fieldset>
		</form>
	<?php }?>
<?php }?><?php }} ?>