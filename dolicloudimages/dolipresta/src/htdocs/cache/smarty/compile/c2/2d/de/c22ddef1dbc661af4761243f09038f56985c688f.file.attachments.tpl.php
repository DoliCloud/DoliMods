<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:02
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/attachments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8720296651c1c0e2709697-01482305%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c22ddef1dbc661af4761243f09038f56985c688f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/attachments.tpl',
      1 => 1371647788,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8720296651c1c0e2709697-01482305',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'obj' => 0,
    'languages' => 0,
    'language' => 0,
    'default_form_language' => 0,
    'attachment_name' => 0,
    'attachment_description' => 0,
    'PS_ATTACHMENT_MAXIMUM_SIZE' => 0,
    'attach2' => 0,
    'attach' => 0,
    'attach1' => 0,
    'iso_tiny_mce' => 0,
    'ad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e280bd28_67848479',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e280bd28_67848479')) {function content_51c1c0e280bd28_67848479($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['obj']->value->id)){?>
	<input type="hidden" name="submitted_tabs[]" value="Attachments" />
	<h4><?php echo smartyTranslate(array('s'=>'Attachment'),$_smarty_tpl);?>
</h4>
	<div class="separation"></div>
	<fieldset style="border:none;">
		<label><?php echo smartyTranslate(array('s'=>'Filename:'),$_smarty_tpl);?>
 </label>
		<div class="margin-form translatable">
			<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
				<div class="lang_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" style="<?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang']!=$_smarty_tpl->tpl_vars['default_form_language']->value){?>display:none;<?php }?>float: left;">
					<input type="text" name="attachment_name_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['attachment_name']->value[$_smarty_tpl->tpl_vars['language']->value['id_lang']], 'htmlall', 'UTF-8');?>
" />
				</div>
			<?php } ?>
			<sup>&nbsp;*</sup>
		</div>
		<p class="margin-form preference_description"><?php echo smartyTranslate(array('s'=>'Maximum 32 characters.'),$_smarty_tpl);?>
</p>
		<div class="clear">&nbsp;</div>
		<label><?php echo smartyTranslate(array('s'=>'Description:'),$_smarty_tpl);?>
 </label>
		<div class="margin-form translatable">
			<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
				<div class="lang_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" style="display: <?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang']==$_smarty_tpl->tpl_vars['default_form_language']->value){?>block<?php }else{ ?>none<?php }?>; float: left;">
					<textarea name="attachment_description_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['attachment_description']->value[$_smarty_tpl->tpl_vars['language']->value['id_lang']], 'htmlall', 'UTF-8');?>
</textarea>
				</div>
			<?php } ?>
		</div>
		<div class="clear">&nbsp;</div>
		<label><?php echo smartyTranslate(array('s'=>'File:'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<p><input type="file" name="attachment_file" /></p>
			<p class="preference_description"><?php echo smartyTranslate(array('s'=>'Upload a file from your computer'),$_smarty_tpl);?>
 (<?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['PS_ATTACHMENT_MAXIMUM_SIZE']->value);?>
 <?php echo smartyTranslate(array('s'=>'MB max.'),$_smarty_tpl);?>
)</p>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="margin-form">
			<input type="submit" value="<?php echo smartyTranslate(array('s'=>'Upload attachment file'),$_smarty_tpl);?>
" name="submitAddAttachments" class="button" />
		</div>
		<div class="small"><sup>*</sup> <?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</div>
	</fieldset>
	<div class="separation"></div>
	<div class="clear">&nbsp;</div>
	<table>
		<tr>
			<td>
                <p><?php echo smartyTranslate(array('s'=>'Available attachments:'),$_smarty_tpl);?>
</p>
                <select multiple id="selectAttachment2" style="width:300px;height:160px;">
                    <?php  $_smarty_tpl->tpl_vars['attach'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attach']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['attach2']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attach']->key => $_smarty_tpl->tpl_vars['attach']->value){
$_smarty_tpl->tpl_vars['attach']->_loop = true;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['attach']->value['id_attachment'];?>
"><?php echo $_smarty_tpl->tpl_vars['attach']->value['name'];?>
</option>
                    <?php } ?>
                </select><br /><br />
                <a href="#" id="addAttachment" style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
                    <?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 &gt;&gt;
                </a>
            </td>
            <td style="padding-left:20px;">
                <p><?php echo smartyTranslate(array('s'=>'Attachments for this product:'),$_smarty_tpl);?>
</p>
                <select multiple id="selectAttachment1" name="attachments[]" style="width:300px;height:160px;">
                    <?php  $_smarty_tpl->tpl_vars['attach'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attach']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['attach1']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attach']->key => $_smarty_tpl->tpl_vars['attach']->value){
$_smarty_tpl->tpl_vars['attach']->_loop = true;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['attach']->value['id_attachment'];?>
"><?php echo $_smarty_tpl->tpl_vars['attach']->value['name'];?>
</option>
                    <?php } ?>
                </select><br /><br />
                <a href="#" id="removeAttachment" style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
                    &lt;&lt; <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>

                </a>
			</td>
		</tr>
	</table>
	<div class="clear">&nbsp;</div>
	<input type="hidden" name="arrayAttachments" id="arrayAttachments" value="<?php  $_smarty_tpl->tpl_vars['attach'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attach']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['attach1']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attach']->key => $_smarty_tpl->tpl_vars['attach']->value){
$_smarty_tpl->tpl_vars['attach']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['attach']->value['id_attachment'];?>
,<?php } ?>" />

	<script type="text/javascript">
		var iso = '<?php echo $_smarty_tpl->tpl_vars['iso_tiny_mce']->value;?>
';
		var pathCSS = '<?php echo @constant('_THEME_CSS_DIR_');?>
';
		var ad = '<?php echo $_smarty_tpl->tpl_vars['ad']->value;?>
';
	</script>
<?php }?>
<?php }} ?>