<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:07
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/options/options.tpl" */ ?>
<?php /*%%SmartyHeaderCode:81650053151c1c0e76a4199-45841391%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '894f1854e32cd4021f636a90684c60742a1437d7' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/options/options.tpl',
      1 => 1371647817,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '81650053151c1c0e76a4199-45841391',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'show_toolbar' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'current_id_lang' => 0,
    'current' => 0,
    'token' => 0,
    'table' => 0,
    'categoryData' => 0,
    'option_list' => 0,
    'use_multishop' => 0,
    'field' => 0,
    'key' => 0,
    'option' => 0,
    'input' => 0,
    'k' => 0,
    'v' => 0,
    'currency_left_sign' => 0,
    'currency_right_sign' => 0,
    'id_lang' => 0,
    'value' => 0,
    'languages' => 0,
    'language' => 0,
    'custom_key' => 0,
    'name_controller' => 0,
    'hookName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e7e7f821_01973145',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e7e7f821_01973145')) {function content_51c1c0e7e7f821_01973145($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_replace')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.replace.php';
?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

	<div class="leadin"></div>
<?php }?>

<script type="text/javascript">
	id_language = Number(<?php echo $_smarty_tpl->tpl_vars['current_id_lang']->value;?>
);
</script>


<form action="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
"
	id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form"
	<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['name'])){?> name=<?php echo $_smarty_tpl->tpl_vars['categoryData']->value['name'];?>
<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['id'])){?> id=<?php echo $_smarty_tpl->tpl_vars['categoryData']->value['id'];?>
 <?php }?>
	method="post"
	enctype="multipart/form-data">
	<?php  $_smarty_tpl->tpl_vars['categoryData'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['categoryData']->_loop = false;
 $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['option_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['categoryData']->key => $_smarty_tpl->tpl_vars['categoryData']->value){
$_smarty_tpl->tpl_vars['categoryData']->_loop = true;
 $_smarty_tpl->tpl_vars['category']->value = $_smarty_tpl->tpl_vars['categoryData']->key;
?>
		<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['top'])){?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['top'];?>
<?php }?>
		<fieldset <?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['class'])){?>class="<?php echo $_smarty_tpl->tpl_vars['categoryData']->value['class'];?>
"<?php }?>>
		
		<legend>
			<img src="<?php echo $_smarty_tpl->tpl_vars['categoryData']->value['image'];?>
"/>
			<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['title'])){?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['title'];?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Options'),$_smarty_tpl);?>
<?php }?>
		</legend>

		
		<?php if ((isset($_smarty_tpl->tpl_vars['categoryData']->value['description'])&&$_smarty_tpl->tpl_vars['categoryData']->value['description'])){?>
			<div class="optionsDescription"><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['description'];?>
</div>
		<?php }?>
		
		<?php if ((isset($_smarty_tpl->tpl_vars['categoryData']->value['info'])&&$_smarty_tpl->tpl_vars['categoryData']->value['info'])){?>
			<p><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['info'];?>
</p>
		<?php }?>

		<?php if (!$_smarty_tpl->tpl_vars['categoryData']->value['hide_multishop_checkbox']&&$_smarty_tpl->tpl_vars['use_multishop']->value){?>
			<input type="checkbox" style="vertical-align: text-top" onclick="checkAllMultishopDefaultValue(this)" /> <b><?php echo smartyTranslate(array('s'=>'Check/uncheck all'),$_smarty_tpl);?>
</b> <?php echo smartyTranslate(array('s'=>'(Check boxes if you want to set a custom value for this shop or group shop context)'),$_smarty_tpl);?>

			<div class="separation"></div>
		<?php }?>

		<?php  $_smarty_tpl->tpl_vars['field'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categoryData']->value['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field']->key => $_smarty_tpl->tpl_vars['field']->value){
$_smarty_tpl->tpl_vars['field']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['field']->key;
?>
				<?php if ($_smarty_tpl->tpl_vars['field']->value['type']=='hidden'){?>
					<input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value['value'];?>
" />
				<?php }else{ ?>
					<div style="clear: both; padding-top:15px;" id="conf_id_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['field']->value['is_invisible']){?> class="isInvisible"<?php }?>>
					<?php if (!$_smarty_tpl->tpl_vars['categoryData']->value['hide_multishop_checkbox']&&$_smarty_tpl->tpl_vars['field']->value['multishop_default']&&empty($_smarty_tpl->tpl_vars['field']->value['no_multishop_checkbox'])){?>
						<div class="preference_default_multishop">
							<input type="checkbox" name="multishopOverrideOption[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" value="1" <?php if (!$_smarty_tpl->tpl_vars['field']->value['is_disabled']){?>checked="checked"<?php }?> onclick="checkMultishopDefaultValue(this, '<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
')" />
						</div>
					<?php }?>
					
						<?php if (isset($_smarty_tpl->tpl_vars['field']->value['title'])){?>
							<label class="conf_title">
							<?php echo $_smarty_tpl->tpl_vars['field']->value['title'];?>
</label>
						<?php }?>
					
					
						<div class="margin-form">
					
						<?php if ($_smarty_tpl->tpl_vars['field']->value['type']=='select'){?>
							<?php if ($_smarty_tpl->tpl_vars['field']->value['list']){?>
								<select name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"<?php if (isset($_smarty_tpl->tpl_vars['field']->value['js'])){?> onchange="<?php echo $_smarty_tpl->tpl_vars['field']->value['js'];?>
"<?php }?> id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])){?> size="<?php echo $_smarty_tpl->tpl_vars['field']->value['size'];?>
"<?php }?>>
									<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['option']->key;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['option']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']];?>
"<?php if ($_smarty_tpl->tpl_vars['field']->value['value']==$_smarty_tpl->tpl_vars['option']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']]){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['option']->value['name'];?>
</option>
									<?php } ?>
								</select>
							<?php }elseif(isset($_smarty_tpl->tpl_vars['input']->value['empty_message'])){?>
								<?php echo $_smarty_tpl->tpl_vars['input']->value['empty_message'];?>

							<?php }?>
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='bool'){?>
							<label class="t" for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_on"><img src="../img/admin/enabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
" /></label>
							<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_on" value="1" <?php if ($_smarty_tpl->tpl_vars['field']->value['value']){?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js']['on'])){?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js']['on'];?>
<?php }?>/>
							<label class="t" for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_on"> <?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</label>
							<label class="t" for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_off"><img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
" style="margin-left: 10px;" /></label>
							<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_off" value="0" <?php if (!$_smarty_tpl->tpl_vars['field']->value['value']){?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js']['off'])){?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js']['off'];?>
<?php }?>/>
							<label class="t" for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_off"> <?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</label>
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='radio'){?>
							<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['choices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
								<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['field']->value['value']){?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value])){?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value];?>
<?php }?>/>
								<label class="t" for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"> <?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</label><br />
							<?php } ?>
							<br />
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='checkbox'){?>
							<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['choices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
								<input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
_on" value="<?php echo intval($_smarty_tpl->tpl_vars['k']->value);?>
"<?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['field']->value['value']){?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value])){?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value];?>
<?php }?>/>
								<label class="t" for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
_on"> <?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</label><br />
							<?php } ?>
							<br />
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='text'){?>
							<input type="<?php echo $_smarty_tpl->tpl_vars['field']->value['type'];?>
"<?php if (isset($_smarty_tpl->tpl_vars['field']->value['id'])){?> id="<?php echo $_smarty_tpl->tpl_vars['field']->value['id'];?>
"<?php }?> size="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])){?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['size']);?>
<?php }else{ ?>5<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['field']->value['value'], 'htmlall', 'UTF-8');?>
" <?php if (isset($_smarty_tpl->tpl_vars['field']->value['autocomplete'])&&!$_smarty_tpl->tpl_vars['field']->value['autocomplete']){?>autocomplete="off"<?php }?>/>
							<?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])){?>&nbsp;<?php echo strval($_smarty_tpl->tpl_vars['field']->value['suffix']);?>
<?php }?>
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='password'){?>
							<input type="<?php echo $_smarty_tpl->tpl_vars['field']->value['type'];?>
"<?php if (isset($_smarty_tpl->tpl_vars['field']->value['id'])){?> id="<?php echo $_smarty_tpl->tpl_vars['field']->value['id'];?>
"<?php }?> size="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])){?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['size']);?>
<?php }else{ ?>5<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="" <?php if (isset($_smarty_tpl->tpl_vars['field']->value['autocomplete'])&&!$_smarty_tpl->tpl_vars['field']->value['autocomplete']){?>autocomplete="off"<?php }?> />
							<?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])){?>&nbsp;<?php echo strval($_smarty_tpl->tpl_vars['field']->value['suffix']);?>
<?php }?>
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='textarea'){?>
							<textarea name=<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
 cols="<?php echo $_smarty_tpl->tpl_vars['field']->value['cols'];?>
" rows="<?php echo $_smarty_tpl->tpl_vars['field']->value['rows'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['field']->value['value'], 'htmlall', 'UTF-8');?>
</textarea>
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='file'){?>
							<?php if (isset($_smarty_tpl->tpl_vars['field']->value['thumb'])&&$_smarty_tpl->tpl_vars['field']->value['thumb']){?>
								<img src="<?php echo $_smarty_tpl->tpl_vars['field']->value['thumb'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['field']->value['title'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['field']->value['title'];?>
" /><br />
							<?php }?>
							<input type="file" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" />
             <?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='color'){?>
              <input type="color"
                size="<?php echo $_smarty_tpl->tpl_vars['field']->value['size'];?>
"
                data-hex="true"
                <?php if (isset($_smarty_tpl->tpl_vars['input']->value['class'])){?>class="<?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
"
                <?php }else{ ?>class="color mColorPickerInput"<?php }?>
                name="<?php echo $_smarty_tpl->tpl_vars['field']->value['name'];?>
"
                class="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['class'])){?><?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
<?php }?>"
                value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['field']->value['value'], 'htmlall', 'UTF-8');?>
" />
						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='price'){?>
							<?php echo $_smarty_tpl->tpl_vars['currency_left_sign']->value;?>
<input type="text" size="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])){?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['size']);?>
<?php }else{ ?>5<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['field']->value['value'], 'htmlall', 'UTF-8');?>
" /><?php echo $_smarty_tpl->tpl_vars['currency_right_sign']->value;?>
 <?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>

						<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='textLang'||$_smarty_tpl->tpl_vars['field']->value['type']=='textareaLang'||$_smarty_tpl->tpl_vars['field']->value['type']=='selectLang'){?>
							<?php if ($_smarty_tpl->tpl_vars['field']->value['type']=='textLang'){?>
								<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_lang'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_lang']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
									<div id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
" style="margin-bottom:8px; display: <?php if ($_smarty_tpl->tpl_vars['id_lang']->value==$_smarty_tpl->tpl_vars['current_id_lang']->value){?>block<?php }else{ ?>none<?php }?>; float: left; vertical-align: top;">
										<input type="text" size="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])){?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['size']);?>
<?php }else{ ?>5<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['value']->value, 'htmlall', 'UTF-8');?>
" />
									</div>
								<?php } ?>
							<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='textareaLang'){?>
								<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_lang'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_lang']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
									<div id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
" style="display: <?php if ($_smarty_tpl->tpl_vars['id_lang']->value==$_smarty_tpl->tpl_vars['current_id_lang']->value){?>block<?php }else{ ?>none<?php }?>; float: left;">
										<textarea rows="<?php echo $_smarty_tpl->tpl_vars['field']->value['rows'];?>
" cols="<?php echo intval($_smarty_tpl->tpl_vars['field']->value['cols']);?>
"  name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
"><?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['value']->value,'\r\n',"\n");?>
</textarea>
									</div>
								<?php } ?>
							<?php }elseif($_smarty_tpl->tpl_vars['field']->value['type']=='selectLang'){?>
								<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
								<div id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" style="margin-bottom:8px; display: <?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang']==$_smarty_tpl->tpl_vars['current_id_lang']->value){?>block<?php }else{ ?>none<?php }?>; float: left; vertical-align: top;">
									<select name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['language']->value['iso_code'], 'UTF-8');?>
">
										<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
											<option value="<?php if (isset($_smarty_tpl->tpl_vars['v']->value['cast'])){?><?php echo $_smarty_tpl->tpl_vars['v']->value['cast'][$_smarty_tpl->tpl_vars['v']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']]];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['v']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']];?>
<?php }?>"
												<?php if ($_smarty_tpl->tpl_vars['field']->value['value'][$_smarty_tpl->tpl_vars['language']->value['id_lang']]==$_smarty_tpl->tpl_vars['v']->value['name']){?> selected="selected"<?php }?>>
												<?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>

											</option>
										<?php } ?>
									</select>
								</div>
								<?php } ?>
							<?php }?>
							<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1){?>
								<div class="displayed_flag">
									<img src="../img/l/<?php echo $_smarty_tpl->tpl_vars['current_id_lang']->value;?>
.jpg"
										class="pointer"
										id="language_current_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"
										onclick="toggleLanguageFlags(this);" />
								</div>
								<div id="languages_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="language_flags">
									<?php echo smartyTranslate(array('s'=>'Choose language:'),$_smarty_tpl);?>
<br /><br />
									<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
											<img src="../img/l/<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
.jpg"
												class="pointer"
												alt="<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
"
												title="<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
"
												onclick="changeLanguage('<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
', '<?php if (isset($_smarty_tpl->tpl_vars['custom_key']->value)){?><?php echo $_smarty_tpl->tpl_vars['custom_key']->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php }?>', <?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
, '<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
');" />
									<?php } ?>
								</div>
							<?php }?>
							<br style="clear:both">
						<?php }?>

						<?php if (isset($_smarty_tpl->tpl_vars['field']->value['required'])&&$_smarty_tpl->tpl_vars['field']->value['required']&&$_smarty_tpl->tpl_vars['field']->value['type']!='radio'){?>
							<sup>*</sup>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['field']->value['hint'])){?><span class="hint" name="help_box"><?php echo $_smarty_tpl->tpl_vars['field']->value['hint'];?>
<span class="hint-pointer">&nbsp;</span></span><?php }?>
					
					<?php if (isset($_smarty_tpl->tpl_vars['field']->value['desc'])){?><p class="preference_description"><?php echo $_smarty_tpl->tpl_vars['field']->value['desc'];?>
</p><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['field']->value['is_invisible']){?><p class="warn"><?php echo smartyTranslate(array('s'=>'You can\'t change the value of this configuration field in the context of this shop.'),$_smarty_tpl);?>
</p><?php }?>
					</div>
					</div>
					<div class="clear"></div>
				
			<?php }?>
		<?php } ?>
		<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit'])){?>
			<div class="margin-form">
				<input type="submit"
						value="<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit']['title'])){?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['title'];?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
<?php }?>"
						name="<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit']['name'])){?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['name'];?>
<?php }else{ ?>submitOptions<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
<?php }?>"
						class="<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit']['class'])){?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['class'];?>
<?php }else{ ?>button<?php }?>"
						id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form_submit_btn"
				/>
			</div>
		<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['required_fields'])&&$_smarty_tpl->tpl_vars['categoryData']->value['required_fields']){?>
			<div class="small"><sup>*</sup> <?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</div>
		<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['bottom'])){?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['bottom'];?>
<?php }?>
		</fieldset><br />
	<?php } ?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminOptions'),$_smarty_tpl);?>

	<?php if (isset($_smarty_tpl->tpl_vars['name_controller']->value)){?>
		<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo ucfirst($_smarty_tpl->tpl_vars['name_controller']->value);?>
Options<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

	<?php }elseif(isset($_GET['controller'])){?>
		<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo htmlentities(ucfirst($_GET['controller']));?>
Options<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

	<?php }?>
</form>


<?php }} ?>