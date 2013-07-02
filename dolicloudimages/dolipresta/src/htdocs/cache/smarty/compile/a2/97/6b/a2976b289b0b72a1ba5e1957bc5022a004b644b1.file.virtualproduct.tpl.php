<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:04
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/virtualproduct.tpl" */ ?>
<?php /*%%SmartyHeaderCode:56849507051c1c0e4056f77-54211268%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2976b289b0b72a1ba5e1957bc5022a004b644b1' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/products/virtualproduct.tpl',
      1 => 1371647792,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '56849507051c1c0e4056f77-54211268',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'product_downloaded' => 0,
    'download_product_file_missing' => 0,
    'download_dir_writable' => 0,
    'is_file' => 0,
    'upload_max_filesize' => 0,
    'up_filename' => 0,
    'currentIndex' => 0,
    'token' => 0,
    'error_product_download' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e422e745_13489108',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e422e745_13489108')) {function content_51c1c0e422e745_13489108($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<script type="text/javascript">
	var newLabel = '<?php echo smartyTranslate(array('s'=>'New label'),$_smarty_tpl);?>
';
	var choose_language = '<?php echo smartyTranslate(array('s'=>'Choose language:'),$_smarty_tpl);?>
';
	var required = '<?php echo smartyTranslate(array('s'=>'required'),$_smarty_tpl);?>
';
	var customizationUploadableFileNumber = '<?php echo $_smarty_tpl->tpl_vars['product']->value->uploadable_files;?>
';
	var customizationTextFieldNumber = '<?php echo $_smarty_tpl->tpl_vars['product']->value->text_fields;?>
';
	var uploadableFileLabel = 0;
	var textFieldLabel = 0;

	function uploadFile()
	{
		$.ajaxFileUpload (
			{
				url:'./uploadProductFile.php',
				secureuri:false,
				fileElementId:'virtual_product_file',
				dataType: 'xml',
				success: function (data, status)
				{
					data = data.getElementsByTagName('return')[0];
					var result = data.getAttribute("result");
					var msg = data.getAttribute("msg");
					var fileName = data.getAttribute("filename");
					if (result == "error")
					{
						$("#upload-confirmation").hide();
						$("#upload-error td").html('<div class="error"><?php echo smartyTranslate(array('s'=>'Error:'),$_smarty_tpl);?>
 ' + msg + '</div>');
						$("#upload-error").show();
					}
					else
					{
						$('#upload-error').hide();
						$('#file_missing').hide();
						$('#virtual_product_name').attr('value', fileName);
						$("#upload-confirmation .error").remove();
						$('#upload-confirmation div').prepend('<span><?php echo smartyTranslate(array('s'=>'The file'),$_smarty_tpl);?>
&nbsp;"<a class="link" href="get-file-admin.php?file='+msg+'&filename='+fileName+'">'+fileName+'</a>"&nbsp;<?php echo smartyTranslate(array('s'=>'has successfully been uploaded'),$_smarty_tpl);?>
' +
							'<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="' + msg + '" /></span>');
						$("#upload-confirmation").show();
					}
				}
			}
		);
	}

	function uploadFile2()
	{
			var link = '';
			$.ajaxFileUpload (
			{
				url:'./uploadProductFileAttribute.php',
				secureuri:false,
				fileElementId:'virtual_product_file_attribute',
				dataType: 'xml',
				success: function (data, status)
				{
					data = data.getElementsByTagName('return')[0];
					var result = data.getAttribute("result");
					var msg = data.getAttribute("msg");
					var fileName = data.getAttribute("filename");
					if(result == "error")
						$("#upload-confirmation2").html('<p>error: ' + msg + '</p>');
					else
					{
						$('#virtual_product_file_attribute').remove();
						$('#virtual_product_file_label').hide();
						$('#file_missing').hide();
						$('#delete_downloadable_product_attribute').show();
						$('#upload-confirmation2').html(
							'<a class="link" href="get-file-admin.php?file='+msg+'&filename='+fileName+'"><?php echo smartyTranslate(array('s'=>'The file'),$_smarty_tpl);?>
&nbsp;"' + fileName + '"&nbsp;<?php echo smartyTranslate(array('s'=>'has successfully been uploaded'),$_smarty_tpl);?>
</a>' +
							'<input type="hidden" id="virtual_product_filename_attribute" name="virtual_product_filename_attribute" value="' + msg + '" />');
						$('#virtual_product_name_attribute').attr('value', fileName);

						link = $("#delete_downloadable_product_attribute").attr('href');
						$("#delete_downloadable_product_attribute").attr('href', link+"&file="+msg);
					}
				}
			}
		);
	}

</script>

<input type="hidden" name="submitted_tabs[]" value="VirtualProduct" />
<h4><?php echo smartyTranslate(array('s'=>'Virtual Product (services, booking or downloadable products)'),$_smarty_tpl);?>
</h4>
<div class="separation"></div>
<div>
	<div class="is_virtual_good">
		<input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" <?php if ($_smarty_tpl->tpl_vars['product']->value->is_virtual&&$_smarty_tpl->tpl_vars['product']->value->productDownload->active){?>checked="checked"<?php }?> />
			<label for="is_virtual_good" class="t bold"><?php echo smartyTranslate(array('s'=>'Is this a virtual product?'),$_smarty_tpl);?>
</label>
	</div>
	
	<div id="virtual_good" <?php if (!$_smarty_tpl->tpl_vars['product']->value->productDownload->id||$_smarty_tpl->tpl_vars['product']->value->productDownload->active){?>style="display:none"<?php }?> >
		<div>
			<label><?php echo smartyTranslate(array('s'=>'Does this product have an associated file?'),$_smarty_tpl);?>
</label>
			<label style="width:50px"><input type="radio" value="1"  name="is_virtual_file" <?php if ($_smarty_tpl->tpl_vars['product_downloaded']->value){?>checked="checked"<?php }?> /><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</label>
			<label style="width:50px;"><input type="radio" value="0" name="is_virtual_file" <?php if (!$_smarty_tpl->tpl_vars['product_downloaded']->value){?>checked="checked"<?php }?> /><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</label>
		</div><br />
		<div class="separation"></div>
		<?php if ($_smarty_tpl->tpl_vars['download_product_file_missing']->value){?>
			<p class="alert" id="file_missing">
				<b><?php echo $_smarty_tpl->tpl_vars['download_product_file_missing']->value;?>
 :<br/>
				<?php echo @constant('_PS_DOWNLOAD_DIR_');?>
/<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->filename;?>
</b>
			</p>
		<?php }?>

		<div id="is_virtual_file_product" style="display:none;">
			<?php if (!$_smarty_tpl->tpl_vars['download_dir_writable']->value){?>
				<p class="alert">
					<?php echo smartyTranslate(array('s'=>'Your download repository is not writable.'),$_smarty_tpl);?>
<br/>
					<?php echo @constant('_PS_DOWNLOAD_DIR_');?>

				</p>
			<?php }?>
			
			<?php if (empty($_smarty_tpl->tpl_vars['product']->value->cache_default_attribute)){?>
				<?php if ($_smarty_tpl->tpl_vars['product']->value->productDownload->id){?>
					<input type="hidden" id="virtual_product_id" name="virtual_product_id" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->id;?>
" />
				<?php }?>
				<table cellpadding="5" style="float: left; margin-left: 10px;">
					<tr id="upload_input" <?php if ($_smarty_tpl->tpl_vars['is_file']->value){?>style="display:none"<?php }?>>
						<td class="col-left">
							<label id="virtual_product_file_label" for="virtual_product_file" class="t"><?php echo smartyTranslate(array('s'=>'Upload a file'),$_smarty_tpl);?>
</label>
						</td>
						<td class="col-right">
							<input type="file" id="virtual_product_file" name="virtual_product_file" onchange="uploadFile();" maxlength="<?php echo $_smarty_tpl->tpl_vars['upload_max_filesize']->value;?>
" />
							<p class="preference_description"><?php echo smartyTranslate(array('s'=>'Your server\'s maximum file-upload size is'),$_smarty_tpl);?>
:&nbsp;<?php echo $_smarty_tpl->tpl_vars['upload_max_filesize']->value;?>
 <?php echo smartyTranslate(array('s'=>'MB'),$_smarty_tpl);?>
</p>
						</td>
					</tr>
					<tr id="upload-error" style="display:none">
						<td colspan=2></td>
					</tr>
					<tr id="upload-confirmation" style="display:none">
						<td colspan=2>
							<?php if ($_smarty_tpl->tpl_vars['up_filename']->value){?>
								<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="<?php echo $_smarty_tpl->tpl_vars['up_filename']->value;?>
" />
							<?php }?>
							<div class="conf">
								<a class="delete_virtual_product" id="delete_downloadable_product" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Delete this file'),$_smarty_tpl);?>
')" href="<?php echo $_smarty_tpl->tpl_vars['currentIndex']->value;?>
&deleteVirtualProduct=true&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
" class="red">
									<img src="../img/admin/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Delete this file'),$_smarty_tpl);?>
"/>
								</a>
							</div>
						</td>
					</tr>
					<?php if ($_smarty_tpl->tpl_vars['is_file']->value){?>
						<tr>
							<td class="col-left">
								<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->filename;?>
" />
								<label class="t"><?php echo smartyTranslate(array('s'=>'Link to the file:'),$_smarty_tpl);?>
</label>
							</td>
							 <td class="col-right">
								<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->getHtmlLink(false,true);?>

								<a onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Delete this file'),$_smarty_tpl);?>
)')" href="<?php echo $_smarty_tpl->tpl_vars['currentIndex']->value;?>
&deleteVirtualProduct=true&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
" class="red delete_virtual_product">
									<img src="../img/admin/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Delete this file'),$_smarty_tpl);?>
"/>
								</a>
							</td>
						</tr>
					<?php }?>
					<tr>
						<td class="col-left">
							<label for="virtual_product_name" class="t"><?php echo smartyTranslate(array('s'=>'Filename'),$_smarty_tpl);?>
</label>
						</td>
						<td class="col-right">
							<input type="text" id="virtual_product_name" name="virtual_product_name" style="width:200px" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->productDownload->display_filename, 'htmlall', 'UTF-8');?>
" />
							<p class="preference_description" name="help_box"><?php echo smartyTranslate(array('s'=>'The full filename with its extension (e.g. Book.pdf)'),$_smarty_tpl);?>
</p>
						</td>
					</tr>
					<tr>
						<td class="col-left">
							<label for="virtual_product_nb_downloable" class="t"><?php echo smartyTranslate(array('s'=>'Number of allowed downloads'),$_smarty_tpl);?>
</label>
						</td>
						<td class="col-right">
							<input type="text" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value->productDownload->nb_downloadable);?>
" class="" size="6" />
							<p class="preference_description"><?php echo smartyTranslate(array('s'=>'Number of downloads allowed per customer. (Set to 0 for unlimited downloads)'),$_smarty_tpl);?>
</p>
						</td>
					</tr>
					<tr>
						<td class="col-left">
							<label for="virtual_product_expiration_date" class="t"><?php echo smartyTranslate(array('s'=>'Expiration date'),$_smarty_tpl);?>
</label>
						</td>
						<td class="col-right">
							<input class="datepicker" type="text" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->date_expiration;?>
" size="11" maxlength="10" autocomplete="off" /> <?php echo smartyTranslate(array('s'=>'Format: YYYY-MM-DD'),$_smarty_tpl);?>

							<p class="preference_description"><?php echo smartyTranslate(array('s'=>'If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.'),$_smarty_tpl);?>
</p>
						</td>
					</tr>
						<td class="col-left">
							<label for="virtual_product_nb_days" class="t"><?php echo smartyTranslate(array('s'=>'Number of days'),$_smarty_tpl);?>
</label>
						</td>
						<td class="col-right">
							<input type="text" id="virtual_product_nb_days" name="virtual_product_nb_days" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value->productDownload->nb_days_accessible);?>
" class="" size="4" /><sup> *</sup>
							<p class="preference_description"><?php echo smartyTranslate(array('s'=>'Number of days this file can be accessed by customers'),$_smarty_tpl);?>
 - <em>(<?php echo smartyTranslate(array('s'=>'Set to zero for unlimited access.'),$_smarty_tpl);?>
)</em></p>
						</td>
					</tr>
					
					
						
							
						
						
							
							
						
					
				<?php }else{ ?>
					<div class="hint clear" style="display: block;width: 70%;"><?php echo smartyTranslate(array('s'=>'You cannot edit your file here because you used combinations. Please edit this file in the Combinations tab.'),$_smarty_tpl);?>
</div>
					<br />
					<?php if (isset($_smarty_tpl->tpl_vars['error_product_download']->value)){?><?php echo $_smarty_tpl->tpl_vars['error_product_download']->value;?>
<?php }?>
				<?php }?>
			</table>
		</div>
	</div>
	<div style="clear:both"></div>
</div><?php }} ?>